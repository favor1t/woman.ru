<?php
/**
 * @var $model Publication
 */

if (!($items = $model->getRelatedData($activeRecordsOnly = true, 7))) {
  return;
}

require "_relatedTemplate.tpl";
?>
