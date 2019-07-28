<?php
/**
 * @var ExpertBlock $block
 */
?>
<div class="expert-<?= $block->align; ?>">
	<?= Html::image($block->image->getVariant('132x180', false), $block->description, array(
		'title' => $block->description
	));
	echo Html::tag('div', array(), $block->description);
	?>
</div>