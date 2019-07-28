<?php
/**
 * @var $block Gallery_newBlock
 */
// убедимся, что галерея не пустая
// получим слайды, пересоберем в индексный массив

try {
  $gallery = $block->getGallery();
  $arrSlide = $gallery->getSlide();
  $slideCount = count($arrSlide);
  if (!$slideCount) {
    if (PreviewUrlHelper::testPreviewUrl()) {
      echo ' <div class="vvodka" style="color: red"><b>В галерее отсутствуют картинки или галереи не существует</b></div>';
    }
    return;
  }
  $arrSlide = array_values($arrSlide);
} catch (Exception $e) {
  if (YII_DEBUG) {
    echo '<br><br><br>
      Не удалось получить слайды галереи.<br>
      Возможные причины:<br>
      - галерея не определена<br>
      - в галерее нет слайдов<br><br><br>';
    Yii::app()->end();
  }

  echo 'в галерее статьи изображений нет';
  return;
}
if (!isset($gallery, $arrSlide)) return;

echo '<div class="article__gallery">';
foreach ($arrSlide as $i => $slide) {
  if ($slide->isVideo()) {
    continue;
  }
  ?>
  <figure class="article__gallery-wrapper">
  <amp-img src="<?= $slide->getUrlResized('800x800', ImageHelper::RESIZE_CROP_ALIGN_TOP); ?>" width="400" height="400"
     layout="intrinsic" lightbox="gallery" alt="<?= $slide->getAlt(); ?>"><?php
    if ($i === 0) {
      $countSlide = count($arrSlide);
      echo "<span class=\"article__gallery-see-all\">Смотреть галерею, ${countSlide} фото</span>";
    }
    ?></amp-img>
    <figcaption class="article__gallery-text">
      <?= $slide->getDescription(); ?>
    </figcaption>
  </figure>
  <?php
  if ($i === 0) {
    echo '<div hidden>';
  }
}
echo '</div></div>';
?>
