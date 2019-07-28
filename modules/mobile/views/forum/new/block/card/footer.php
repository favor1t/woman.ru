<?php
/**
 * @var string  $otherData
 *
 * @var string  $a['link']      ///kids/medley5/thread/5116723/
 * @var string  $a['title']     //Перейти
 * @var string  $a['className'] //btn-flat btn-flat_size_content btn-flat_accent
 *
 * @var array   $b['className'] //className = card__answer-btn btn-flat btn-flat_size_content btn-flat_accent $text = ответить
 * @var array   $b['text']      //ответить
 */
?>
<footer class="card__footer">
  <div class="card__other-data card__other-data_secondary"><?=$otherData?></div>
  <?php
    /**
     * Вставка ссылки a href
    * @var string  $link
    * @var string  $title
    * @var string  $className
    */
  if(isset($a) && is_array($a)) $this->renderPartial('/forum/new/block/custom/a', $a);
    /**
     * Вставка области div
     * @var string  $text
     * @var string  $className
     */
  if(isset($b) && is_array($b)) $this->renderPartial('/forum/new/block/custom/div', $b);
  ?>
</footer>
