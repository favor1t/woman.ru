<div class="list">
  <div class="list__container">
    <div class="list__subtitle">Похожие темы</div>
    <ul class="list__body">
      <?php foreach ($forumTopics as $topic): ?>
        <li class="list-item">
          <a class="list-item__link" href="<?= UrlHelper::addParametersToUrl($topic->getSiteUrl(), ['wic' => 'suggested_links', 'wil' => 'forum_related_b_s']) ?>">
            <div class="list-item__container">
              <div class="list-item__first-line">
                <div class="list-item__title"><?=$topic->getName()?></div>
                <?php if($topic->hasExpertAnswer() === ForumHelper::EXPERT_ANSWER_HAS):?>
                  <div class="icon-before_expert-24-crimson"></div>
                <?php endif;?>
              </div>
            </div>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>