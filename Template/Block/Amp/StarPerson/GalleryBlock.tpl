<?php
 if(count($this->getStarPersonGallery())): ?>
<amp-carousel width="auto" height="392" layout="fixed-height" type="slides"
              autoplay loop delay="7000" class="person__carousel">
    <?php foreach ($this->getStarPersonGallery() as $slide):
    if (!isset($slide->url)) {
    continue;
    }
    $urlImage = null;
    if(isset($slide->url_image)) :
    $image = Image::createByUrl($slide->url_image);
    $urlImage = $image->getVariant('400x300',false, ImageHelper::RESIZE_CROP_ALIGN_TOP); ?>
    <figure class="person__carousel-item">
        <amp-img src="<?= $urlImage; ?>" width="400" height="300"
                 layout="intrinsic"
                 alt="<?= \mobile\helpers\StringHelper::cropByWord($slide->publicationTitle ?? '', 120, $lastSigns = '&#8230;'); ?>"></amp-img>
        <figcaption class="person__carousel-description">
            <?=\mobile\helpers\StringHelper::cropByWord(strip_tags($slide->description ?? ''), 120, $lastSigns = '&#8230;') ;?>
            <?php if ($slide->publicationUrl):?>
            <a href="<?= $slide->publicationUrl; ?>">Подробнее... &gt;</a>
            <?php endif; ?>
        </figcaption>
    </figure>
    <?php endif; ?>
    <?php endforeach; ?>
</amp-carousel>
<?php endif; ?>
<div class="person__outside">
    <amp-ad width=300 height=250
            type="doubleclick"
            data-slot="/81006599/hmiru-woman/amp/amp-inline"
            json='{"targeting":{"ru-woman-pubType":["tag"]}}'>
    </amp-ad>
</div>