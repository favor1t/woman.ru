<?php
$currentPage = isset($page) ? $page : 1;
$totalPages = isset($maxPage) ? $maxPage : 1;

if($totalPages <= 1) return '';
?>

<div class="page__pager pager pager_hidden" data-current-page="<?= $currentPage ?>" data-total-pages="<?= $totalPages ?>">
  <div class="pager__container">
    <a class="pager__link pager__arrow pager__arrow_left icon-before_right-arrow-18-crimson" href="" aria-label="Перейти на предыдущую страницу"></a>
    <div class="pager__item pager__item_first">
      <a class="pager__link" href="">1</a>
    </div>
    <div class="pager__item pager__item_dots">...</div>
    <div class="pager__item pager__item_prev">
      <a class="pager__link" href=""></a>
    </div>
    <div class="pager__item pager__item_current">
      <a class="pager__link" href=""></a>
    </div>
    <div class="pager__item pager__item_next">
      <a class="pager__link" href=""></a>
    </div>
    <div class="pager__item pager__item_dots">...</div>
    <div class="pager__item pager__item_last">
      <a class="pager__link" href=""><?=$totalPages?></a>
    </div>
    <a class="pager__link pager__arrow pager__arrow_right icon-before_right-arrow-18-crimson" href="" aria-label="Перейти на следующую страницу"></a>
  </div>
</div>
