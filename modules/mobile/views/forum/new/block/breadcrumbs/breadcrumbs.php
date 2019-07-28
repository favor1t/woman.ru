<?php
/*
 * @section Nt\Section
 * @subSection Nt\Section
 *
 */
?>
<?php if (isset($section) && isset($section->name)): ?>
  <div class="breadcrumbs">
    <a href='<?= $section->getSiteUrl(); ?>forum/' class='breadcrumbs__link'><?= $section->name ?></a>
    <?php if (isset($subSection) && isset($subSection->name)): ?>
      <span>/</span>
      <a href='<?= $subSection->getSiteUrl(); ?>forum/' class='breadcrumbs__link'><?= $subSection->name ?></a>
    <?php endif; ?>
  </div>
<?php endif; ?>
