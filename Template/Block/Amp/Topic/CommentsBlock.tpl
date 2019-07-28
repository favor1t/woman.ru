<?php
use \mobile\helpers\StringHelper;

/* мобильный url */
$mobileUrlBase = \Yii::app()->params['baseMobileUrl'];

$forumTopic     = $this->getForumTopic();
?>
<header class="msg-heading">
  <div class="msg-heading__row">
    <span class="msg-heading__title">
      <?=number_format($forumTopic->getAnswerCountAll(),0,'.',' ')?>
      <?=\TextHelper::declension($forumTopic->getAnswerCountAll(), [ 'ответ', 'ответа', 'ответов' ])?>
      в теме</span>
  </div>
  <?php
  $date = $forumTopic->getDateLastComment();
  if($date || strtotime($date)) {
  ?>
  <div class="msg-heading__row">
    <a href="<?=str_replace('www.', 'm.', $forumTopic->getSiteUrl($absolute = true, [
     'page' => $forumTopic->getPageNumberMax(),
     'anchor' => 'message_last', ]))?>" class="msg-heading__meta">
      Последний ответ <?=DateHelper::format_full($date)?> <span class="msg-heading__meta-btn">Перейти</span></a>
  </div>
</header>
<?php } ?>

<?php
foreach($this->getComments() as $messageIndex => $comment):
if($comment->getStatus() !== ForumMessage::STATUS_ON) continue;
$bannerHtml = '';
if($messageIndex + 1 === 4) $bannerHtml = '<div class="amp-ad_placeholder">'.\BannerHelper::getAmpMobVideo().'</div>';
if($messageIndex > 4 && ($messageIndex+1)%4 === 0) {
  $index = ($messageIndex+1)/4-1;
if($index >= 1) $bannerHtml = '<div class="amp-ad_placeholder">'.\BannerHelper::getMyTargetAd((int)$index).'</div>';
}

$userUrl            = null;

$userFromMessage    = $comment->getUserOrNull();
if($userFromMessage && ! $comment->isAnonymous()) $userUrl = $userFromMessage->getSiteUrl();
$userName           = ! $comment->isAnonymous() && $userFromMessage ? $userFromMessage->getName() : $comment->getUserName();
$userName           = Html::encode($userName);

$topicUrl           = \Yii::app()->params['baseMobileUrl'].$forumTopic->getSiteUrl();
$userAvatarUrl      = str_replace('http://', 'https://',StaticResourceHelper::staticUrl(\ForumTopicAction::getUserAvatarUrlByForumMessage($comment, $absolute = false)));
$dateCreate         = \DateHelper::format_full($comment->getCreatedAt());

$body               = $comment->getBody();
if(preg_match('#\\\u04\d\d\\\#', $body)) $body = \JSON::decode($body);
$body               = Html::restoreHtml($body);
$body               = StopWordHelper::replaceAbuseWords($body);
$body               = Html::encode($body);
$body               = nl2br($body);
$body               = StringHelper::clearBreaks($body);
$userHashe          = $comment->getUserHash();

$arrQuote           = \Quote::parseQuotes($body);
if (!function_exists('getQuoteHtml')) {
function getQuoteHtml($quote){
$html = '';
$multiple = $quote->subquotes ? true : false;
$html .= '<div class="forum-quote ' . ($multiple ? 'forum-quote--multiple': '') .'">';
  $html .= '<p class="forum-quote__name">' . StringHelper::safeTags($quote->name) . '</p>';
  $html .= '<p class="forum-quote__text">';
    if ($multiple)
    foreach($quote->subquotes as $subQuote)
    $html .= getQuoteHtml($subQuote);
    $html .= StringHelper::safeTags($quote->body);
    $html .= '</p>';
  $html .= '</div><p class="js-comment__text msg-card__text">';
  return $html;
  }
}


  $html     = \Html::restoreHtml($comment->getBody());
  $arrQuote = \Quote::parseQuotes($html);

  $quoteHtml         = '';
  foreach($arrQuote ?? [] as $quote)
  $quoteHtml .= getQuoteHtml($quote);

  if(! is_array($arrQuote)) $arrQuote = [];

  $body = null;

  while(true){

  if(! count($arrQuote)){
    $body = html_entity_decode(str_replace('\u', '&#x', $comment->getBody()), ENT_NOQUOTES, 'UTF-8');
    $body = str_replace('\n', '<br>', $body);
    break;
  }

  $body = \Html::restoreHtml($comment->getBody());

  foreach($arrQuote as $quote)
    $body = str_replace($quote->complete, '', $body);

  $body = preg_replace('/\[\/?quote[^\]]*\]/uUis', '', $body);

  break;
  }

  $body = \StopWordHelper::replaceAbuseWords(\mobile\helpers\StringHelper::clearBreaks(\mobile\helpers\StringHelper::safeTags($body)));
  ?>
  <?=$bannerHtml?>
  <article class="msg-card-wrapper">
    <div class="msg-card">
      <div class="msg-card__body">
        <header class="msg-card__msg-head">
          <div class="msg-card__img-wrap">
            <amp-img alt="A" src="<?=$userAvatarUrl?>" width="36" height="36"></amp-img>
          </div>
          <div class="msg-card__meta">
            <div class="msg-card__meta-row">
              <?php if($userUrl):?>
              <a href="<?=$mobileUrlBase.$userUrl?>">
                <span class="msg-card__author-id"> <?=($messageIndex+1+($this->getPage()-1)*50)?>. </span>
                <span class="js-comment__author msg-card__author-name msg-card__author-name_auth"> <?=$userName?> </span>
              </a>
              <?php else : ?>
              <span class="msg-card__author-id"> <?=($messageIndex+1+($this->getPage()-1)*50)?>. </span>
              <span class="js-comment__author msg-card__author-name"> <?=$userName?> </span>
              <?php endif ;?>
            </div>
            <div class="msg-card__meta-row"><p class="msg-card__meta-text msg-card__meta-text_light">[<?=$userHashe?>] – <?=$dateCreate?></p></div>
</div>
</header>
<p class="js-comment__text msg-card__text"><?=$quoteHtml?><?=$body?>
  <footer class="topic__footer">
    <a href="<?=$topicUrl.$this->getPage()?>#show-d-message/answer-for=<?=$comment->getId()?>" class="topic__answer">Ответить</a>
    <?php /*
    <a href="<?=$topicUrl?>#show-d-exclaim/exclaim-for=<?=($messageIndex+1)?>" class="topic__exclaim */ ?>
  </footer>
  </div>
  </div>
  </article>
  <?php endforeach ?>
<div></div>
</section>