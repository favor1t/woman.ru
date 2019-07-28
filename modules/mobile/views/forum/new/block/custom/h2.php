<?php
/**
 * @var string $className   // "card__parent"
 * @var string $title
 *
 * @var string $a['link']
 * @var string $a['title']
 * @var string $a['className']
*/
?>
<h2 class="<?=$className?>"><?=$title?>
  <?php if(isset($a) && is_array($a)) $this->renderPartial('/forum/new/block/custom/a', $a) ?>
</h2>