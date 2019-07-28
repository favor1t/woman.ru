<?php
if(!$this->getExpertMessage()) return;

$expertMessage  = $this->getExpertMessage();
$forumTopic     = $this->getForumTopic();

$expertImageUrl = str_replace('http://', 'https://', \StaticResourceHelper::staticUrl(\ForumTopicAction::getUserAvatarUrlByForumMessage($expertMessage)));
$expert         = (new Expert())->findByPk($expertMessage->getUserId());
$experUrl       = $expert ? $expert->getUrlB17OrNull() : null;
$userNameHtml   = $expertMessage->getUserName();
$dateCreate     = \DateHelper::format_full($expertMessage->getCreatedAt());
$topicUrl       = str_replace('www.', 'm.', $forumTopic->getSiteUrl($absolute = true));

$body           = $expertMessage->getBody();
if(preg_match('#\\\u04\d\d\\\#', $body)) $body = \JSON::decode($body);
$body           = Html::restoreHtml($body);
$body           = StopWordHelper::replaceAbuseWords($body);
$body           = Html::encode($body);
$body           = nl2br($body);
$body           = StringHelper::clearBreaks($body);
?>
<div class="topic-expert">
  <header class="topic-expert__heading">
    <svg width="24" height="15" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><path d="M25.013 595.08l8.249 2.962-8.25 2.963-8.257-2.963zm5.193 9.43a9.59 9.59 0 0 1-10.42 0v-3.747l4.958 1.781a.746.746 0 0 0 .505 0l4.957-1.781zm-16.453-.635a.761.761 0 0 0 .735.757.761.761 0 0 0 .756-.766v-4.738l3.022 1.092v4.698a.77.77 0 0 0 .324.613 11.086 11.086 0 0 0 12.795 0 .77.77 0 0 0 .323-.613v-4.698l4.045-1.454a.77.77 0 0 0 0-1.444l-10.498-3.778a.749.749 0 0 0-.505 0l-10.513 3.778h-.024l-.046.021-.078.043-.04.025a.758.758 0 0 0-.078.067l-.021.019a.764.764 0 0 0-.085.104l-.018.03a.767.767 0 0 0-.048.09l-.018.042a.77.77 0 0 0-.028.09v.042a.772.772 0 0 0 0 .138v5.842z" id="a"/></defs><g transform="translate(-13 -593)"><use xlink:href="#a" fill="#fcf5f8"/></g></svg>
    <span class="topic-expert__heading-title">Ответ эксперта</span>
    <a class="topic-expert__link" href="http://www.woman.ru/womanru/guestbook/article/215964/" target="_blank">Правила</a>
  </header>
  <div class="topic-expert__container">
    <header class="topic-expert__header">
      <div class="topic-expert__header-item">
        <a href="<?=$experUrl?>" class="topic-expert__avatar" rel="nofollow">
          <amp-img alt="EA" width="50" height="50" src="<?=$expertImageUrl?>"></a>
      </div>
      <div class="topic-expert__header-item">
        <div class="topic-expert__header-row">
          <a href="<?=$experUrl?>" class="topic-expert__name" rel="nofollow"><?=$userNameHtml?></a>
        </div>
        <div class="topic-expert__header-row">
          <p class="topic-expert__date"><?=$dateCreate?>
        </div>
        <div class="topic-expert__header-row">
          <p class="topic-expert__badge-txt">
            Психолог. Специалист с сайта b17.ru
        </div>
      </div>
    </header>
    <div class="topic-expert__body-wrap">
      <div class="topic-expert__body">
        <p class="topic-expert__text">
          <?=$body?>
      </div>
    </div>

    <footer class="topic-expert__footer">
      <div class="topic-expert__footer-item">
        <div class="topic-expert__blog-link"><a href="<?=$experUrl?>" target="_blank" rel="nofollow">Получить консультацию эксперта</a></div>
      </div>
      <div class="topic-expert__footer-item">
        <a href="<?=$topicUrl?>#show-d-message/answer-for=<?=$expertMessage->getId()?>" class="topic__answer">Ответить</a>
      </div>
    </footer>
  </div>
  <footer class="topic-expert__msg-footer">
    <a target="_blank" href="http://www.woman.ru/forum/expert/all-answers/?wic=forum_expert&&wil=answer_block">Все ответы экспертов</a>
  </footer>
</div>
