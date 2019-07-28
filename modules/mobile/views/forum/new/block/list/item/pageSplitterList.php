<?php
/**
 * Блок разделителя страниц
 * @var array   $anchor
 * @var array   $pageSplitter
 */
?>
<div class="page-splitter">
  <div class="page-splitter__container">
    <?php
    /**
     * @var string  $id
     * @var string  $title
     * @var string  $className
     */
    $this->renderPartial('/forum/new/block/custom/anchor', $anchor);

    /**
     * Блок последнего навигации. Упрощенная версия
     * @var int     $pageNumber
     * @var string  $prevUrl
     * @var string  $nextUrl
     */
    $this->renderPartial('/forum/new/block/pageSplitter/pageSplitterSmall', $pageSplitter);
    ?>
  </div>
</div>
