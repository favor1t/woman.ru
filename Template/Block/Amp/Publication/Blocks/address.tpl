<?php
/**
 * @var AddressBlock $block
 */
?>
<div class="headerAdressBlock"><?= Html::encode($block->title); ?></div>
<?php foreach ($block->items as $addressData): ?>
	<ul class="adressBlock">
		<li><span><?= Html::encode($addressData['address']); ?></span>
		<?= Html::encode($addressData['contacts']); ?><br>
		<?php if (isset($addressData['links']) && !empty($addressData['links'])): ?>
			<?php foreach ($addressData['links'] as $link): ?>
				<?= Html::link(Html::encode($link['title']), $link['href'], array(
					'target' => '_blank'
				)); ?><br>
			<?php endforeach; ?>
		<?php endif; ?>
		</li>
	</ul>
<?php endforeach; ?>