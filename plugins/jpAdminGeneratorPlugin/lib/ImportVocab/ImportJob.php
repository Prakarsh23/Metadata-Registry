<?php
/**
 * Created by jonphipps, on 2015-05-22 at 5:48 PM
 * for the registry.dev project
 */

namespace ImportVocab;

class ImportJob
{
    public function setUp()
    {
        set_time_limit(0);
    }

    public function perform()
    {
        list($schemaId, $filePath, $importId, $environment, $type) = $this->args;
        // Set up environment for this job
        define('SF_ROOT_DIR', realpath(dirname(__file__) . '/../../../..'));
        define('SF_APP', 'frontend');
        define('SF_ENVIRONMENT', $environment);
        define('SF_DEBUG', false);

        //initialize composer
        require_once SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        // initialize symfony
        require_once SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        // initialize database manager
        $databaseManager = new \sfDatabaseManager();
        $databaseManager->initialize();

        $import = new ImportVocab($type, $filePath, $schemaId);
        try {
            $fileImportHistory = \FileImportHistoryPeer::retrieveByPK($importId);
        } catch (\PropelException $e) {
            //exit the job with an error
            throw $e;
        }

        try {
            if ('schema' == $type) {
                $schema = \SchemaPeer::retrieveByPK($schemaId);
            } else {
                $schema = \VocabularyPeer::retrieveByPK($schemaId);
            }
        } catch (\PropelException $e) {
            //exit the job with an error
            throw $e;
        }

        // Perform some job
        $import->importId = $importId;
        //todo update the prefixes table with prefixes
        //todo update the schema table with prefixes
        $schemaPrefixes = $schema->getPrefixes();
        $countSchemaPrefixes = count($schemaPrefixes);
        /** @var string[] $importPrefixes */
        $importPrefixes = $import->prolog['prefix'];
        foreach ($importPrefixes as $prefix => $url) {
            if (trim($prefix)) {
                if ( ! array_key_exists($prefix, $schemaPrefixes)) {
                    $schemaPrefixes[$prefix] = $url;
                }
            }
        }
        if (count($schemaPrefixes) != $countSchemaPrefixes)
        {
            $schema->setPrefixes($schemaPrefixes);
            $schema->save();
        }
        try {

        $prolog = $import->processProlog();
        $import->processData();
        $fileImportHistory->setResults($import->results);
        $fileImportHistory->setMap($import->mapping);
        $fileImportHistory->setTotalProcessedCount( $import->DataWorkflowResults->getTotalProcessedCount());
        $fileImportHistory->setErrorCount($import->DataWorkflowResults->getErrorCount());
        $fileImportHistory->setSuccessCount($import->DataWorkflowResults->getSuccessCount());
        $fileImportHistory->setResults('Your file has been imported. It took us: ' . $import->DataWorkflowResults->getElapsed()->format("%h hours; %i minutes; %s seconds"));
        $fileImportHistory->save();
        } catch (\Exception $e) {
            $fileImportHistory->setResults("There was an error processing the import. Message: " . $e->getMessage());
            $fileImportHistory->save();
            throw $e;
        }
        $agentId = ($fileImportHistory->getSchema()) ? $fileImportHistory->getSchema()->getAgentId() : $fileImportHistory->getVocabulary()->getAgentId();
        $newFilePath = \sfConfig::get( 'sf_repos_dir' ) . DIRECTORY_SEPARATOR .
                       'agents' . DIRECTORY_SEPARATOR .
                       $agentId . DIRECTORY_SEPARATOR .
                       $fileImportHistory->getSourceFileName();
        $request = new \myWebRequest();
        $result = $request->moveToRepo($filePath, $newFilePath);
        $schema = \SchemaPeer::retrieveByPK($schemaId);
        if ($schema) {
            $schema->setUpdatedAt(time());
            $schema->setUpdatedUserId($import->userId);
            $schema->save();
        }

        unset ($import);
        unset ($request);
        unset($databaseManager);

    }

    public function tearDown()
    {
        // Remove environment for this job
    }
}
