<?php
/**
 * @var ImageListBlock $block
 */
?>
<ul class="block-with-listings">
	<?php foreach($block->items as $listItem):?>
		<li>
			<div class="image-in-list">
			<?= Html::image($listItem['image'], '', array(
				'class' => 'p',
				'width' => 150//$listItem['image']['width'],
				//'height' => $listItem['image']['height'],
			)); ?>
			</div>
			<div class="text-in-list">
				<h2 class="art"><?= nl2br(Html::encode(isset($listItem['subject'])?$listItem['subject']:'')); ?></h2>
				<span>
					<?= nl2br(Html::encode(isset($listItem['description'])?$listItem['description']:'')); ?>
				</span>
			</div>
			<br class="clear-fix"/>
		</li>
	<?php endforeach; ?>
</ul>