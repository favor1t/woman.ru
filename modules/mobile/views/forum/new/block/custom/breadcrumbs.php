<?php
/**
 * @var $section    Section
 * @var $subSection Section
 */
?>
<div class="breadcrumbs">
    <a class="breadcrumbs__main" href="/forum/?wic=forum_navigation&wil=top_navbar&wid=mainpage">Форум Woman.ru</a>
    <div class="breadcrumbs__links">
      <a href="<?= $section->getSiteUrl(); ?>forum/" class="breadcrumbs__link"><?= $section->name ?></a>
      <?php if(isset($subSection) && is_object($subSection)): ?>
        <span>/</span>
        <a href="<?= $subSection->getSiteUrl(); ?>forum/" class="breadcrumbs__link"><?= $subSection->name ?></a>
      <?php endif; ?>
    </div>
</div>
