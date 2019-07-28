<div class="comments">
  <h3 class="comments__title">Комменатрии</h3>
<?php
// FOR REFACTOR
$comments = \PublicationHelper::getComments($this->publication, $offset = 0, $limit = 10);
foreach ($comments as $index => $comment) {
  $body = \Comment::getBodyAndQuote($comment->body);
  $author_avatar = $comment->getUserpicSmallUrl();
  $message_index = $index + 1;
  ?>
  <?php if ($index === 1) : ?>
    <div class="comments__row">
      <a href="<?=$this->publication->getSiteUrl($absolute = true, [ 'anchor' => 'add-comment-anchor' ])?>" class="comments__btn-add"><i class="icon icon__plus"></i>Добавить комментарий</a>
    </div>
  <?php endif; ?>
  <div class="comments__item" id="<?= $comment->id ?>">
    <article class="msg-card" id="m<?= $comment->id ?>">
      <div class="msg-card__body">
        <header class="msg-card__msg-head msg-card__msg-head_small-pb">
          <?php if ($comment->user_id): ?>
            <a href="<?=\Yii::app()->params['baseUrl'];?>/user/<?= $comment->user_id ?>/" target="_blank" class="msg-card__img-wrap">
          <?php endif; ?>
          <?php if ($author_avatar != '' && strpos('/i/userpic.gif', $author_avatar) === false): ?>
            <?php echo mobile\helpers\AvatarHelper::DEAFULT_AVATAR; /* AvatarHelper::getTag($author_avatar) */ ?>
          <?php else: ?>
            <div class="icon icon__account"></div>
          <?php endif ?>
          <?php if ($comment->user_id): ?></a><?php endif ?>
          <div class="msg-card__meta">
            <div class="msg-card__meta-row">
              <?php if ($comment->user_id): ?>
                <span class="msg-card__name"><?= ($message_index ? $message_index . '. ' : '') ?></span>
                <a href="/user/<?= $comment->user_id ?>/" target="_blank" class="msg-card__name"><?=
                  mobile\helpers\StringHelper::safeTags($comment->user_name) ?></a>
              <?php else: ?>
                <span class="msg-card__name"><?= ($message_index ? $message_index . '. ' : '') ?><?=
                  mobile\helpers\StringHelper::safeTags($comment->user_name) ?></span>
              <?php endif ?>
            </div>
            <div class="msg-card__meta-row">
          <span class="msg-card__info">
              <?= DateHelper::format_full($comment->created_at) ?></span>
            </div>
          </div>
        </header>
        <?php if (count($body['quote']) > 0) : ?>
          <div class="msg-card__quote">
            <div class="msg-card__quote-name"><?= Html::encode($body['quote']['nickname']) ?></div>
            <p class="msg-card__quote-text"><?= mobile\helpers\StringHelper::clearBreaks(mobile\helpers\StringHelper::safeTags($body['quote']['body'])) ?></p>
          </div>
        <?php endif; ?>
        <p class="msg-card__text">
          <?= mobile\helpers\StringHelper::clearBreaks(mobile\helpers\StringHelper::safeTags($body['body'])) ?>
        </p>
        <?php if (false) : // footer ?>
        <footer class="msg-card__footer">
          <div class="msg-card__footer-item">
            <span class="btn btn_warn"><i class="icon icon__abuse"></i></span>
          </div>
          <div class="msg-card__footer-item">
            <?php if (isset($isUser, $isUserBanned, $publication)): //если данные существуют?>
              <?php if (
                !$isUserBanned &&
                (
                  (
                    $publication->comments_mode == \mobile\helpers\PublicationHelper::COMMENTS_MODE_REGISTERED &&
                    $isUser
                  ) || $publication->comments_mode == \mobile\helpers\PublicationHelper::COMMENTS_MODE_ALL
                )
              ) : // Если пользователь забанен, если режим комментирования только для авторизованных пользователь, если комментарии отключены ?>
                <span class="btn btn_action">Ответить</span>
              <?php else: ?>
                <?php if ($publication->comments_mode == \mobile\helpers\PublicationHelper::COMMENTS_MODE_REGISTERED && !$isUser): ?>
                  <a href="#js-popup-login" class="btn btn_action disabled">Ответить</a>
                <?php else: ?>
                  <span class="btn btn_action disabled">Ответить</span>
                <?php endif; ?>
              <?php endif; ?>
            <?php else: ?>
              <span class="btn btn_action js-comments-answer">Ответить</span>
            <?php endif ?>
          </div>
        </footer>
        <?php endif; // footer ?>
      </div>
    </article>
  </div>
  <?php
}
?>

  <div class="comments__show-all">
    <a href="<?=$this->publication->getSiteUrl($absolute = true, [ 'anchor' => 'comments' ])?>" class="comments__more-btn">Посмотреть все комментарии</a>
  </div>
</div>