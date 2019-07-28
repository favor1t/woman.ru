<?php
/**
 * @var VideoBlock $block
 */
$block->defineSubType();
if ($block->subType === '') {
  return;
}
?>
<div class="embed">
	<?php if($block->subType === $block::SUBTYPE_INSTAGRAM) { ?>
    <amp-instagram
      data-shortcode="<?= $block->body; ?>"
      data-captioned
      width="400"
      height="400"
      layout="responsive">
    </amp-instagram>
  <?php } ?>
</div>