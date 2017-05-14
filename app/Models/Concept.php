<?php

namespace App\Models;

use App\Helpers\Macros\Traits\Languages;
use Cache;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Concept
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $deleted_at
 * @property string|null $last_updated
 * @property int|null $created_user_id
 * @property int|null $updated_user_id
 * @property string $uri
 * @property string|null $pref_label
 * @property int|null $vocabulary_id
 * @property int|null $is_top_concept
 * @property int|null $pref_label_id
 * @property int $status_id
 * @property string $language
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ConceptAttribute[] $properties
 * @property-read \App\Models\Status $status
 * @property-read \App\Models\Vocabulary|null $vocabulary
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereCreatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereCreatedUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereDeletedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereIsTopConcept($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereLanguage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereLastUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept wherePrefLabel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept wherePrefLabelId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereStatusId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereUpdatedUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereUri($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Concept whereVocabularyId($value)
 * @mixin \Eloquent
 */
class Concept extends Model
{
    protected $table = self::TABLE;
    const TABLE = 'reg_concept';

    protected $primaryKey = 'id';

    use SoftDeletes;

    public function vocabulary()
    {
        return $this->belongsTo(\App\Models\Vocabulary::class, 'vocabulary_id', 'id');
    }


    public function properties()
    {
        return $this->hasMany(ConceptAttribute::class, 'concept_id');
    }

  public function status()
  {
    return $this->belongsTo(\App\Models\Status::class, 'status_id', 'id');
  }

  public function name()
  {
    return $this->pref_label;
  }

  public function getLanguageAttribute($value)
  {
    return Cache::get('language_' . $value,
        function () use ($value) {
          return Languages::list($value);
        });
  }

}
