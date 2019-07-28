</ul>
</div>
<?php
$placeholder = \BannerHelper::getPlaceholder($increment = true, $hasRelap = false);
$banner = '';
$banner = \Yii::app()->banners->registerPlaceholder($placeholder, true);
?>
<?= $banner ?>
<div class="list">
<ul class="list__container">
