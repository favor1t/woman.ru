<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<title><?= TitleHelper::getTitle() ?></title>
<link rel="canonical" href="<?=\UrlHelper::getCanonicalUrl()?>" />
<meta content="yii-head-top-place">
<?php
$meta = MetaTags::getAll();
foreach ($meta as $propName => $type):
  foreach ($type as $name => $value):
    if ($value != null):?>
      <meta <?= $propName; ?>="<?= $name ?>" content="<?= trim($value) ?>" />
    <?php endif;
  endforeach;
endforeach;
?>
<link href="<?= \StaticResourceHelper::staticUrl(\Yii::app()->params['fmc_mobile_resources_path'] . 'css/_main.css') ?>"
      rel="stylesheet" media="all">
<?php $this->renderPartial('/forum/new/counter/gtag');?>
<script src="<?= \StaticResourceHelper::staticUrl(\Yii::app()->params['fmc_mobile_resources_path'] . 'js/_db.js') ?>" defer></script>
<meta content="publisher-place">
<meta content="yii-head-meta-place">
