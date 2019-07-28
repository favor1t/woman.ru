<?php
/**
 * @var BlueListBlock $block
 */
?>
<ul class="bullets">
	<?php foreach ($block->items as $listItem): ?>
	<li>
		<?= is_array($listItem) && isset($listItem['body']) ? $listItem['body'] : ''?>
	</li>
	<?php endforeach; ?>
</ul>