<div class="list">
  <div class="list__container">
    <div class="list__subtitle"><?=($title ?? '')?></div>
    <ul class="list__body">
      <?php foreach ($arrTopic as $topic): ?>
      <li class="list-item">
        <a class="list-item__link" href="<?=$topic->getSiteUrl()?>?wic=suggested_links&wil=forum_related_b_s">
          <div class="list-item__container">
            <div class="list-item__first-line">
              <div class="list-item__title"><?=$topic->getName()?></div>
            </div>
          </div>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
