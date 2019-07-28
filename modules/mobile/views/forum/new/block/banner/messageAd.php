<?php
$placeholder = \BannerHelper::getPlaceholder($increment = true, $hasRelap = false);
$banner = '';
$banner = \Yii::app()->banners->registerPlaceholder($placeholder, true);
?>
<?= $banner ?>