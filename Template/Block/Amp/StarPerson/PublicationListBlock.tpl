
<section class="scope scope_tiles">
    <h4 class="scope__head scope__head_mb-middle scope__head_less-size">Публикации</h4>
    <div class="scope__head-underline"></div>
    <div class="scope__items">
        <?php
        foreach ($this->publicationList as $i => $publication):
            $variantName = '240x180'; // x2
            $width = '120';
            $crop = ImageHelper::RESIZE_CROP_ALIGN_TOP;

            $imgUrl = '';
            if(isset($publication->images) && isset($publication->getOwner()['desc_image'])) {
              $imgUrl = $publication->images->getOwner()['desc_image']->getVariant($variantName, false, $crop)->getCdnUrl();
            }

            if($imgUrl == ''){
                $imgUrl = \StaticResourceHelper::staticUrl("/i/thumb_og_204x300.jpg");
            }
        ?>
        <article class="announce announce_tile announce_tile_single">
            <a class="announce__link announce__body" href="<?=$publication->getSiteUrl(false);?>">
                <header>
                    <span class="announce__image announce__image_h <?=$i == 1 ? 'announce__image_2-6' : 'announce__image_1-6'?>">
                        <amp-img height="90" width="120" src="<?=$imgUrl;?>"
                                layout="responsive"></amp-img>
                    </span>
                    <h4 class="announce__title">
                        <?php echo empty($publication->name_short) ? $publication->name : $publication->name_short; ?></h4>
                </header>
            </a>
            <div class="clear"></div>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<footer class="person__footer">
    <a href="http://www.woman.ru<?=\Yii::app()->request->requestUri?>1/" class="person__more-btn">Все новости</a>
</footer>
