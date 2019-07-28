<?php
/**
 * @var PinkListBlock $block
 */
?>
<ul class="pinkList">
	<?php foreach ($block->items as $listItem): ?>
	<li>
		<?= Html::tag('span', array('class' => 'number'), isset($listItem['number']) ? $listItem['number'] : ''); ?>
		<span class="subject-container">
			<span>
				<?= Html::image(StaticResourceHelper::staticUrl('/i/p.gif')); ?>
			</span>
			<span class="subject"><?=isset($listItem['subject']) ? nl2br($listItem['subject']) : ''?></span>
			<span>
				<?= Html::image(StaticResourceHelper::staticUrl('/i/p-rev.gif')); ?>
			</span>
		</span>
		<span class="description"><?=isset($listItem['description']) ? nl2br($listItem['description']) : ''?></span>
	</li>
	<?php endforeach; ?>
</ul>