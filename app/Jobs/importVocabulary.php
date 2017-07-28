<?php

namespace App\Jobs;

use App\Models\Concept;
use App\Models\ConceptAttribute;
use App\Models\Element;
use App\Models\ElementAttribute;
use App\Models\Import;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportVocabulary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /** @var Import */
    private $import;
    /** @var Model */
    private $resource;
    /** @var Collection  */
    private $formResourceProps;
    /** @var Collection  */
    private $formLanguageProps;
    /** @var string  */
    private $resourceLang;
    /** @var array  */
    private $updatedStatements;

    /**
     * Create a new job instance.
     *
     * @param int $import
     */
    public function __construct(int $importId)
    {
        $this->import = Import::find($importId);
    }

    /**
     * Execute the job.
     *   step through the changeset and change/add the statements
     *   each row of the changeset is a resource
     *   if the row has an id then we're updating it
     *   if there's a statement id for a column, we're updating it
     *   if we delete a row, we have to delete all of the statements
     *   changing the statements should trigger an addition to the history table for each one
     *   update each resource base model with data from the statements
 *
     * @return void
     * @throws \Throwable
     * @throws \Exception
     */
    public function handle()
    {
        $timer           = new \DateTime();
        $this->setFormproperties();
        $this->setLanguage();
        $vocabId         = $this->isElementSet() ? $this->import->schema_id : $this->import->vocabulary_id;
        $changeset       = $this->import->instructions;
        $total_processed = 0;
        //each item in the main array is a row. Each item in the statements array is a statement
        foreach ($changeset['update'] as $reg_id => $row) {
            $this->updatedStatements = [];
            //start a transaction
            DB::transaction(function() use ($reg_id, $row) {
                $statements = $this->getStatements($reg_id);
                foreach ($row as $statement) {
                    $old = $statements->find($statement['statement_id']);
                    if ($old) {
                        if ($statement['new value']) {
                            $old->update([
                                'object'         => $statement['new value'],
                                'last_import_id' => $this->import->id,
                            ]);
                            $this->addUpdateStatement($statement);
                        } else {
                            $old->delete();
                            $this->addUpdateStatement($statement);
                        }
                    } else {
                        //make a new one
                        $newStatement = $this->addStatement($statement, $reg_id);
                        $statement['statement_id'] = $newStatement->id;
                        $this->addUpdateStatement($statement);
                    }
                }
                if (count($this->updatedStatements)) {
                    $this->resource->updateFromStatements($this->updatedStatements);
                }
            });
            $total_processed++;
        }
        foreach ($changeset['add'] as $row) {
            DB::transaction(function() use ($row, $vocabId) {
                $resource = $this->createResource($vocabId);
                foreach ($row as $statement) {
                    $newStatement = $this->addStatement($statement, $resource->id);
                    $statement['statement_id'] = $newStatement->id;
                    $this->addUpdateStatement($statement);
                }
                if (count($this->updatedStatements)) {
                    $this->resource->updateFromStatements($this->updatedStatements);
                }
            });
            $total_processed++;
        }
        foreach ($changeset['delete'] as $row) {
            //TODO implement this...
            //delete the resource
            //cascade delete the statements, which should cascade delete the reciprocals
            $total_processed++;
        }
        $this->import->results =
            'Your file has been imported. It took us: ' .
            $timer->diff(new \DateTime())->format('%h hours; %i minutes; %s seconds');
        $this->import->batch->increment('handled_count');
        $this->import->total_processed_count = $total_processed;
        $this->import->imported_at           = new \DateTime();
        $this->import->save();
    }

    public function failed(Exception $exception)
    {
        //report a failed import
    }

    private function addUpdateStatement($statement): void
    {
        if ($this->formLanguageProps->contains($statement['property_id']) &&
            $this->resourceLang === $statement['language']) {
            $this->updatedStatements[ $statement['property_id'] ] = $statement['new value'];
            //deprecated special handling for prefLabel
            if ($statement['property_id'] == 45) {
                $this->updatedStatements[ $statement['property_id'] . '-id' ] = $statement['statement_id'];
            }
        }
        if ( $this->formResourceProps->contains($statement['property_id']) ) {
            $this->updatedStatements[ $statement['property_id'] ] = $statement['new value'];
        }
    }
    private function getStatements(int $reg_id): ?Collection
    {
        if ($this->isElementSet()) {
            $this->resource = Element::find($reg_id)->load('statements');
        } else {
            $this->resource = Concept::find($reg_id)->load('statements');
        }

        return $this->resource->statements;
    }

    private function setFormproperties(): void
    {
        $this->formResourceProps =
            $this->isElementSet() ? $this->import->elementset->profile->profile_properties()->where([
                [ 'is_in_form', true ],
                [ 'has_language', false ],
            ])->get([ 'id' ])->pluck('id') : $this->import->vocabulary->profile->profile_properties()->where([
                [ 'is_in_form', true ],
                [ 'has_language', false ],
            ])->get([ 'id' ])->pluck('id');
        $this->formLanguageProps =
            $this->isElementSet() ? $this->import->elementset->profile->profile_properties()->where([
                [ 'is_in_form', true ],
                [ 'has_language', true ],
            ])->get([ 'id' ])->pluck('id') : $this->import->vocabulary->profile->profile_properties()->where([
                [ 'is_in_form', true ],
                [ 'has_language', true ],
            ])->get([ 'id' ])->pluck('id');
    }

    private function setLanguage(): void
    {
        $this->resourceLang =
            $this->isElementSet() ? $this->import->elementset->getOriginal('language') : $this->import->vocabulary->getOriginal('language');
    }

    private function createResource(int $vocabId): ?Model
    {
        if ($this->isElementSet()) {
            $this->resource = Element::create([ 'schema_id' => $vocabId ]);
        } else {
            $this->resource = Concept::create([ 'vocabulary_id' => $vocabId ]);
        }

        return $this->resource;
    }

    private function addStatement(array $statement, $reg_id)
    {
        if ($this->isElementSet()) {
            return ElementAttribute::create([
                'element_id'          => $this->resource->id,
                'object'              => $statement['new value'],
                'language'            => $statement['language'],
                'profile_property_id' => $statement['property_id'],
                'status_id'           => $this->import->elementset->status_id,
                'last_import_id'      => $this->import->id,
            ]);
        }

        return ConceptAttribute::create([
            'concept_id'          => $this->resource->id,
            'object'              => $statement['new value'],
            'language'            => $statement['language'],
            'profile_property_id' => $statement['property_id'],
            'status_id'           => $this->import->vocabulary->status_id,
            'last_import_id'      => $this->import->id,
        ]);
    }

    private function isElementSet(): bool
    {
        return (bool) $this->import->schema_id;
    }
}
