<?php
if (!$block->getContest())
	return;

$index = 1;

if ($block->showWithNums) {
    $class = '';
    $tag   = 'ul style="list-style-type: none;" s';
    $place = ' место:';
}
else {
    $tag   = 'ul';
    $class = 'bullets';
    $place = '';
}
/**
 * @var WinnersBlock $block
 */
?>

<?php if (($block->getContest() instanceof Photo) || ($block->getContest() instanceof Literary)):?>
<<?=$tag;?> class="<?=$class?>">
	<?php foreach ($block->getWinners() as $winner):?>
	<li> <?=($block->showWithNums ? $index : '' ) . ' ' . $place?>
		<a href="<?=$winner->getSiteUrl($absolute = true)?>" target="_blank"><?=Html::encode($winner->user ? $winner->user->name : $winner->name) ?><?=trim($winner->place) ? ' ('.Html::encode($winner->place).')' : '' ?></a>
	</li>
	<?php
        $index++;
    endforeach?>
</<?=$tag;?>>

<?php else:?>
<<?=$tag;?> class="<?=$class?>">
	<?php foreach ($block->getWinners() as $winner):?>
	<li> <?=($block->showWithNums ? $index : '' ) . ' ' . $place?>
		<?=Html::encode($winner->user ? $winner->user->name : $winner->name) ?><?=trim($winner->place) ? ' ('.Html::encode($winner->place).')' : '' ?>
	</li>
	<?php
        $index++;
    endforeach?>
</<?=$tag;?>>
<?php endif?>