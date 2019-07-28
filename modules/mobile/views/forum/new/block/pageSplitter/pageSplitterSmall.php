<?php
/**
 * Блок последнего навигации. Упрощенная версия
 * @var int     $pageNumber
 * @var string  $prevUrl
 * @var string  $nextUrl
 */
?>
<?php if ($pageNumber > 1 && ($prevUrl || $nextUrl)): ?>
    <div class="page-splitter__nav outdent-top_small">
      <?php if ($prevUrl): ?>
          <a class="page-splitter__prev icon-before_left-arrow-18-white" href="<?= $prevUrl ?>"></a>
      <?php endif; ?>
        Страница <?= $pageNumber; ?>
      <?php if ($nextUrl): ?>
          <a class="page-splitter__next icon-before_right-arrow-18-white" href="<?= $nextUrl ?>"></a>
      <?php endif; ?>
    </div>
<?php endif; ?>