<!-- Блок читайте также -->
<?php
 if($items = $model->getRelatedData($activeRecordsOnly = true, $cntItems = 64)): ?>
	<div class="readmore-carrier">
		<h3 class="readmore-title">Читайте также</h3>
		<div class="readmore-items">
			<?php foreach($items as $i => $link): ?>

                <?php /* if($i == 1):
                    Yii::app()->clientScript->registerScriptFile('https://relap.io/api/v6/head.js?token=76_Wt7052ifaCA0I', CClientScript::POS_HEAD, 100);
                ?>
                    <div class="readmore-item">
                        <script id="tEu0n1-fDjPJHvLy">if (window.relap) window.relap.ar('tEu0n1-fDjPJHvLy');</script>
                    </div>
                <?php else: */?>
                    <?php if($img = $link->getDescImage()): ?>

                        <a href="<?=$link->getUrl();?>?wic=suggested_links&wil=article_related" <?php if(PreviewUrlHelper::testPreviewUrl()): ?> target="_blank" <?php endif; ?> class="readmore-item">
                            <div class="readmore-image" style="background-image: url(<?= ImageHelper::getResizedImageUrl($img, '170x120') ?>);"></div>
                            <p><?=(empty($link->getNameShort()) ? $link->getName() : $link->getNameShort());?></p>
                        </a>

                    <?php else: ?>
                        <div class="readmore-item">
                            <a href="<?=$link->getUrl();?>" <?php if(PreviewUrlHelper::testPreviewUrl()): ?> target="_blank" <?php endif; ?>>
                                <p><?=$link->getName();?></p>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php // endif;?>

			<?php endforeach;?>
		</div>
	</div>
<?php endif; ?>
<!-- Блок читайте также -->