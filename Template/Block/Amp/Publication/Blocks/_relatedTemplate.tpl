<br>
<div class="article__outside">
  <amp-ad width=300 height=250
          type="doubleclick"
          data-slot="/81006599/hmiru-woman/amp/amp-inline"
          json='{"targeting":{"ru-woman-pubType":["tag"]}}'>
  </amp-ad>
</div>
<!-- Блок читайте также -->
<div class="article__read-more">
  <h3 class="article__read-more-title">Сейчас читают</h3>
  <?php foreach ($items as $i => $link): ?>
    <?php if ($i === 1 || $i === 4) : ?>
      <div class="article__read-more-wrap">
        <a href="<?= $link->getUrl(); ?>?wic=suggested_links&wil=article_related" class="article__read-more-tile">
          <?php if ($img = $link->getDescImage()): ?>
            <span class="article__read-more-pic"
                  style="background-image: url(<?= ImageHelper::getResizedImageUrl($img, '670x502', ImageHelper::RESIZE_CROP_ALIGN_TOP) ?>);"></span>
          <?php else : ?>
            <span class="article__read-more-pic"></span>
          <?php endif; ?>
          <span class="article__read-more-text"><?= (empty($link->getNameShort()) ? $link->getName() : $link->getNameShort()); ?></span>
          <span class="clear"></span>
        </a>
      </div>
    <?php else : ?>
      <a href="<?= $link->getUrl(); ?>?wic=suggested_links&wil=article_related" class="article__read-more-link">
        <?php if ($img = $link->getDescImage()): ?>
          <span class="article__read-more-pic"
                style="background-image:url(<?= ImageHelper::getResizedImageUrl($img, '180x180', ImageHelper::RESIZE_CROP_ALIGN_TOP) ?>);"></span>
        <?php else : ?>
          <span class="article__read-more__no-image"></span>
        <?php endif; ?>
        <span class="article__read-more-text"><?= (empty($link->getNameShort()) ? $link->getName() : $link->getNameShort()); ?></span>
        <span class="clear"></span>
      </a>
    <?php endif; ?>
    <!--<a href="<? /*= $link->getUrl(); */ ?>?wic=suggested_links&wil=article_related" <?php /*if (PreviewUrlHelper::testPreviewUrl()): */ ?> target="_blank" <?php /*endif; */ ?>
       class="article__read-more__item">
      <?php /*if ($img = $link->getDescImage()): */ ?>
        <amp-img class="article__read-more__image" width="260" height="146" layout="fixed"
                 src="<? /*= ImageHelper::getResizedImageUrl($img, '260x146', ImageHelper::RESIZE_CROP_ALIGN_TOP) */ ?>"></amp-img>
      <?php /*else: */ ?>
        <div class="article__read-more__no-image"></div>
      <?php /*endif; */ ?>
      <p><? /*= (empty($link->getNameShort()) ? $link->getName() : $link->getNameShort()); */ ?></p>
    </a>-->
  <?php endforeach; ?>
</div>
<!-- /Блок читайте также -->