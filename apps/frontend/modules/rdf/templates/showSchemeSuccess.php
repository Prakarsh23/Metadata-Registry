<?php include_partial('rdf/rdfHead', array('namespaces' => $namespaces));
  $statusArray = array(); ?>
<?php if ($timestamp): ?>
<!--
NOTICE: This is a TimeSlice of this Vocabulary as of:
  <?php echo date(DATE_W3C, $timestamp) ?>.

The most current complete Vocabulary may be retrieved from:
  <?php /** @var Vocabulary $vocabulary */
echo htmlspecialchars($vocabulary->getUri()) ?>

-->
<?php endif; ?>

<?php /** @var array $topConcepts */
include_partial('rdf/scheme', array(
      'vocabulary' => $vocabulary,
      'topConcepts' => $topConcepts,
      'timestamp' => $timestamp)); ?>

<?php /** @var Concept[] $concepts */
foreach ($concepts as $concept): ?>
<?php $properties = $concept->getConceptPropertysRelatedByConceptId();
      $status = $concept->getStatus();
      $statusId = $concept->getStatusId();
      $statusArray[$statusId] = $statusId;

      include_partial('rdf/concept', array(
      'concept' => $concept,
      'vocabulary' => $vocabulary,
      'properties' => $properties,
      'skosProps'  => $skosProps,
      'timestamp'  => $timestamp,
      'status'     => $status)); ?>
<?php endforeach; ?>

<?php include_partial('rdf/status', array(
      'statusArray' => $statusArray)); ?>

</rdf:RDF>
