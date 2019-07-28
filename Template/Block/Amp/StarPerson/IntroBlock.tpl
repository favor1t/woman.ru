<?php
$name = $this->getStarPerson()->getNameRussian();
if ($this->getStarPerson()->getPseudonymPriority() !== null) {
  $name = $this->getStarPerson()->getPseudonym() . " ($name)";
}
?>
<article class=person>
  <header class="person__head">
    <div class="person__head-row">
      <h1 class="person__title person__title_sz-big">
        <?= $name; ?>
      </h1>
    </div>
    <div class="person__head-row">
      <h2 class="person__title person__title_sz-middle person__title_fw-regular person__title_c-meta person__title_c-meta_darker">
        <?= $this->getStarPerson()->getNameEnglish() ?>
      </h2>
    </div>
  </header>
  <section class="person__meta">
    <div class="person__meta-top">
      <div class="person__meta-row">
        <?php
        $imageSrc = PersonHelper::getImageCdn($this->getStarPerson()->getImageInfo());
        if ($imageSrc !== null):
          ?>
          <div class="person__meta-col">
            <amp-img
              src="<?= $imageSrc; ?>"
              alt="<?= $this->getStarPerson()->getNameRussian() ?? $this->getStarPerson()->getNameEnglish() ?? ''; ?>"
              layout="responsive"
              width="200"
              height="300"
            >
            </amp-img>
          </div>
        <?php endif; ?>
        <div class="person__meta-col">
          <dl class="person__info">
            <?php if ($this->getStarPerson()->getRole()): ?>
              <dt class="person__info-param" hidden>Профессия</dt>
              <dd class="person__info-value"><?= $profession = implode('', $this->getStarPerson()->getRole()) ?></dd>
            <?php endif; ?>
            <?php if ($this->getStarPerson()->getPseudonymPriority() == null &&
              $this->getStarPerson()->getPseudonym()): ?>
              <dt class="person__info-param">Псевдоним</dt>
              <dd class="person__info-value"><?= $this->getStarPerson()->getPseudonym() ?></dd>
            <?php endif; ?>
            <?php if ($this->getStarPerson()->getDateBirthday()): ?>
              <dt class="person__info-param">Дата рождения</dt>
              <dd class="person__info-value">
                <?php $dateArray = explode('-', $this->getStarPerson()->getDateBirthday()); ?>
                <?= (int)$dateArray[2] ?? '' ?> <?php if (isset($dateArray[1])) echo DateHelper::getMonthName((int)$dateArray[1], 1) ?>
                <?= $dateArray[0] ?>
              </dd>
              <dt class="person__info-param">Возраст</dt>
              <dd class="person__info-value"><?= PersonHelper::getDiffDate($this->
                getStarPerson()->getDateBirthday(), $this->getStarPerson()->getDateDeath() ?? 'now') ?></dd>
            <?php endif; ?>
            <?php if ($this->getStarPerson()->getPlaceBirthday()): ?>
              <dt class="person__info-param">Место
                рождения
              </dt>
              <dd class="person__info-value"><?= $this->getStarPerson()->getPlaceBirthday() ?></dd>
            <?php endif; ?>
            <?php if ($this->getStarPerson()->getDateDeath()): ?>
              <dt class="person__info-param">Дата смерти
              </dt>
              <dd class="person__info-value"><?= $this->getStarPerson()->getDateDeath() ?></dd>
            <?php endif; ?>
            <?php if ($this->getStarPerson()->getUserInstagramm()): ?>
              <?php
              $arr = explode('@', $this->getStarPerson()->getUserInstagramm());
              if (isset($arr[1]))
                $instagramUser = $arr[1];
              if (isset($instagramUser)):
                ?>
                <dt class="person__info-param">Instagram
                </dt>
                <dd class="person__info-value">
                        <span class="link"
                              href="https://www.instagram.com/ <?=$instagramUser ?>">
                        <?= $this->getStarPerson()->getUserInstagramm() ?>
                        </span>
                </dd>
              <?php endif; ?>
            <?php endif; ?>
          </dl>
        </div>
      </div>
    </div>
    <?php if ($this->getStarPerson()->getHtmlIntro()): ?>
      <p class="person__meta-about">
        <?= $this->getStarPerson()->getHtmlIntro() ?>
      </p>
    <?php endif; ?>
  </section>
</article>
<script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "Person",
    "mainEntityOfPage": "<?= \UrlHelper::getCanonicalUrl(); ?>",
    "name": "<?= $this->getStarPerson()->getNameRussian(); ?>",
    <?php if ($this->getStarPerson()->getPseudonym()): ?>
      "alternateName": "<?= $this->getStarPerson()->getPseudonym(); ?>",
    <?php endif;
    if ($this->getStarPerson()->getRole()): ?>
      "disambiguatingDescription": "<?= $profession; ?>",
    <?php endif;
    if ($this->getStarPerson()->getDateBirthday()): ?>
      "birthDate": "<?= $this->getStarPerson()->getDateBirthday(); ?>",
    <?php endif;
    if ($this->getStarPerson()->getDateDeath()): ?>
      "deathDate": "<?= $this->getStarPerson()->getDateDeath(); ?>",
    <?php endif;
    if ($this->getStarPerson()->getPlaceBirthday()): ?>
      "birthPlace": "<?= $this->getStarPerson()->getPlaceBirthday() ?>",
    <?php endif;
    if ($imageSrc): ?>
      "image": {
        "@type": "ImageObject",
        "url": "<?= $imageSrc; ?>",
        "height": 200,
        "width": 300
      }
    <?php endif; ?>
  }
</script>