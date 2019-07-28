<?php
$startLeaflet = $block->getStartLeaflet();
$activePage = ($startLeaflet) ? $startLeaflet : 1;
$imagesWebpath = Yii::app()->params['imagesWebpath'];
$slide = reset($block->items);
$slideCount = count($block->items);

echo '<div class="article__gallery">';
$iterator = 0;
foreach ($block->items as $slide) :
  $iterator++;
  ?>
  <figure>
  <amp-img src="<?= $slide->image->getVariant('800x800', false, ImageHelper::RESIZE_CROP_ALIGN_TOP); ?>" width="400" height="400"
           layout="intrinsic" lightbox="gallery" alt="<?= $slide->title; ?>"><?php
    if ($iterator === 0) {
      $countSlide = count($arrSlide);
      echo "<span class=\"article__gallery-see-all\">Смотреть галерею, ${countSlide} фото</span>";
    }
    ?></amp-img>
  <figcaption class="image">
    <?= $slide->title; ?>
  </figcaption>
  </figure>
  <?php
  if ($iterator === 1) {
    echo '<div hidden>';
  }
  ?>
<?php endforeach;
echo '</div></div>';
