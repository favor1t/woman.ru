<?php
/**
 * @var string $title
 * @var array $iconMenuParams /icon/menu.php
 */
$title = $title ?? 'Форум Woman.ru';
$iconMenuParams = $iconMenuParams ?? [];
?>
<div class="menu">
    <div class="menu__container">
        <h1 class="menu__title"><?= $title ?></h1>
        <?php $this->renderPartial('/forum/new/block/breadcrumbs',['section' => $section, 'subSection' => $subSection]); ?>
      <?php if (!empty($iconMenuParams)): ?>
          <ul class="menu__list">
            <?php foreach ($iconMenuParams as $index => $iconParams)
              $this->renderPartial('/forum/new/block/icon/link', ['params' => $iconParams]);
            ?>
          </ul>
      <?php endif; ?>
    </div>
</div>
