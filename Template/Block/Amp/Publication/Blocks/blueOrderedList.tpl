<?php
/**
 * @var BlueOrderedListBlock $block
 */
?>
<ul class="blueList">
	<?php 
	$i = $block->start;
	foreach ($block->items as $listItem): ?>
		<li>
			<span class="number<?= $i > 9 ? ' double' : '' ?>"><?=$i ?></span>
			<?= is_array($listItem) ? $listItem['body'] : $listItem ?>
		</li>
	<?php 
	$i++;
	endforeach?>
</ul>