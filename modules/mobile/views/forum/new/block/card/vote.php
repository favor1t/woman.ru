<?php
/**
 * @var  array $arrVote['count_like']
 * @var  array $arrVote['count_dislike']
 * @var  bool  $isExpert  | default: false
 */

$isExpert = isset($isExpert) ? $isExpert : false;
?>
<div class="card__voting">
    <div class="ajax-loader">
        <div class="card__like btn-flat btn-flat_size_tiny btn-flat_icon btn-flat_gray btn-flat_noactive icon-before_like-18-blue">
           <?=($isExpert) ? "Полезно" : "Нравится"; ?>
          <span class="card__like-count"><?=$arrVote['count_like']?></span>
        </div>
      <?php
        $this->renderPartial(
          '/forum/new/block/custom/div',
          ['className' => 'loader-spin loader-spin_bahamas-blue loader-spin_hidden', 'text' => '']
        );
      ?>
    </div>
    <div class="ajax-loader">
        <div class="card__dislike btn-flat btn-flat_size_tiny btn-flat_icon btn-flat_gray btn-flat_noactive icon-before_dislike-18-blue">
            <?=($isExpert) ? "Бесполезно" : "Не нравится"; ?>
          <span class="card__dislike-count"><?=$arrVote['count_dislike']?></span>
        </div>
      <?php
        $this->renderPartial(
          '/forum/new/block/custom/div',
          ['className' => 'loader-spin loader-spin_bahamas-blue loader-spin_hidden', 'text' => '']
        );
      ?>
    </div>
</div>
