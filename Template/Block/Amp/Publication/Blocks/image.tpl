<?php
/**
 * @var ImageBlock $block
 */

$image_link = $block->image->getVariant('400x400', false, ImageHelper::RESIZE_CROP_ALIGN_TOP)->getCdnUrl();

$alt = isset($block->alt) ? $block->alt : $block->comment;

// а коммент может содержать ссылку....
if (strpos($alt, 'href')) $alt = '';

?>
<div class="article__photo photo-<?= $block->align; ?>">
  <?php if (!empty($image_link)): ?>
    <?php if ($block->link): ?>
      <a href="<?= $block->link; ?>" target="<?= $block->target_link ?>">
        <amp-img src="<?= $image_link; ?>" class="main" alt="<?= $alt; ?>" width="400" height="400" layout="responsive"/>
      </a>
    <?php else: ?>
      <amp-img src="<?= $image_link; ?>" class="main" alt="<?= $alt; ?>" width="400" height="400" layout="responsive"/>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (trim($block->comment)): ?>
    <div class="description">
      <?= nl2br($block->comment); ?>
    </div>
  <?php endif; ?>
</div>