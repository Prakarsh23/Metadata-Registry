[?php
  /** @var sfContext $sf_context */
  /** @var sfParameterHolder $sf_flash */
  /** @var sfParameterHolder $sf_params */
  /** @var myWebRequest $sf_request */
  /** @var myUser $sf_user */
  /** @var sfPartialView $sf_view */
  /** @var <?php /** @var sfPropelAdminGenerator $this */
echo $this->getClassName() ?>  $<?php echo $this->getSingularName() ?> */
  ?]
<?php  $class = in_array($this->getModuleName(), [ 'schemahistory', 'history' ]) ? "sf_admin_list history" : "sf_admin_list"  ?>
<table class="<?php echo $class ?>">
<thead>
<tr>
[?php include_partial('list_th_<?php echo $this->getParameterValue('list.layout', 'tabular') ?>') ?]
<?php if ($this->getParameterValue('list.object_actions')): ?>
  <th id="sf_admin_list_th_sf_actions">[?php echo __s('Actions') ?]</th>
<?php endif; ?>
</tr>
</thead>
<tfoot>
<tr><th colspan="<?php echo $this->getParameterValue('list.object_actions') ? count($this->getColumns('list.display')) + 1 : count($this->getColumns('list.display')) ?>">
<div class="float-right pager">
[?php
/** @var sfPropelPager $pager */
/** @var sfParameterHolder $sf_params */
if ($pager->haveToPaginate()):
  $filterParam = '';
  $parent = '';
  $sortParam = $sf_params->has('sort') ? '&sort=' . $sf_params->get('sort', '') . '&type=' . $sf_params->get('type', '') : ''; ?]
<?php $parents = $this->getParameterValue('parents');
if ($parents): ?>
<?php foreach ($parents as $module => $param): ?>
  [?php if ($sf_params->has('<?php echo $param['requestid'] ?>')): ?]
    [?php $filterParam = '<?php echo $param['requestid'] ?>=' . $sf_params->get('<?php echo $param['requestid'] ?>'); $parent = '<?php echo $module ?>_';  ?]
  [?php endif; ?]
<?php endforeach; ?>
<?php endif; ?>
  [?php echo sf_link_to(image_tag(sfConfig::get('sf_admin_web_dir').'/images/first.png', array('alt' => __s('First'), 'title' => __s('First'))), '@' . $parent . '<?php echo $this->getModuleName() ?>_list?' .$filterParam, ['query_string' => '&page=1' . $sortParam]) ?]
  [?php echo sf_link_to(image_tag(sfConfig::get('sf_admin_web_dir').'/images/previous.png', array('alt' => __s('Previous'), 'title' => __s('Previous'))), '@' . $parent . '<?php echo $this->getModuleName() ?>_list?' .$filterParam, ['query_string' => '&page='.$pager->getPreviousPage() . $sortParam]) ?]

  [?php foreach ($pager->getLinks() as $page): ?]
    [?php echo link_to_unless($page == $pager->getPage(), $page, '@' . $parent . '<?php echo $this->getModuleName() ?>_list?' . $filterParam, ['query_string' => '&page=' . $page . $sortParam]) ?]
  [?php endforeach; ?]

  [?php echo sf_link_to(image_tag(sfConfig::get('sf_admin_web_dir').'/images/next.png', array('alt' => __s('Next'), 'title' => __s('Next'))), '@' . $parent . '<?php echo $this->getModuleName() ?>_list?' .$filterParam, ['query_string' => '&page='.$pager->getNextPage() . $sortParam]) ?]
  [?php echo sf_link_to(image_tag(sfConfig::get('sf_admin_web_dir').'/images/last.png', array('alt' => __s('Last'), 'title' => __s('Last'))), '@' . $parent . '<?php echo $this->getModuleName() ?>_list?' .$filterParam, ['query_string' => '&page='.$pager->getLastPage() . $sortParam]) ?]
[?php endif; ?]
</div>
[?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults()) ?]
</th></tr>
</tfoot>
<tbody>
[?php $i = 1; foreach ($pager->getResults() as $<?php echo $this->getSingularName() ?>): $odd = fmod(++$i, 2) ?]
<tr class="sf_admin_row_[?php echo $odd ?]">
[?php include_partial('list_td_<?php echo $this->getParameterValue('list.layout', 'tabular') ?>', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]
[?php include_partial('list_td_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]
</tr>
[?php endforeach; ?]
</tbody>
</table>
