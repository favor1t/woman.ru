<?php
/**
 * @var \nt\Forum\Message $message
 * @var $pageNumber
 */
$isAuthor = false;
?>
<div class="card card_expert<?=$pageNumber > 1 ? ' card_mini-version' : ''?>" id="m<?=$message->getId()?>" data-id="<?=$message->getId()?>" data-type="1">
  <div class="card__container">
<?php

$expert = (new Expert())->findByPk($message->getUserId());

/**
 *
 * @var string      $imageUrl | null
 * @var string      $name
 * @var \nt\User    $user | null
 * @var int         $metaId
 * @var string      $metaDate
 * @var string      $metaTimeStamp
 * @var bool        $isAuthor
 * @var bool        $isAnonymous
 * @var string      $isBlocked
 * @var bool        $isExpert | false
 * @var string      $metaDescription | null
 * @var string      $expertUrl | null
 */
$params                     = [];
$params['isExpert']          = true;
$params['imageUrl']         = \ForumTopicAction::getUserAvatarUrlByForumMessage($message, $absolute = false);
$params['name']             = $message->getUserName();
$params['metaDescription']  = $expert->title;
$params['metaId']           = $message->getUserHash();
$params['metaTimeStamp']    = '';
$params['metaDate']         = '';
$params['isAuthor']         = false;
$params['user']             = $message->getUserOrNull();
$params['isAnonymous']      = false;
$params['isBlocked']        = false;
$params['expertUrl']        = $expert->getUrlB17OrNull();
$this->renderPartial('/forum/new/block/card/user_block',$params);

/**
 * @var string   $body
 * @var Quote[]  $quotes
 */
$params                    = [];
$params['body']            = $message->getBodyWithoutQuotes();
if($pageNumber > 1 && mb_strlen($params['body']) > 250) {
  $params['body'] = StringHelper::crop($params['body'], $length = 250, $appendText = ' ...');
  $params['fullMessageBtn'] = true;
}
$params['quotes']          = $message->getQuotesFromBody() ?? [];
$this->renderPartial('/forum/new/block/card/text', $params);

/**
 * @var  array $arrVotes['count_like']
 * @var  array $arrVotes['count_dislike']
 * @var  bool  $isExpert  | default: false
 */
$params                    = [];
$params['isExpert']        = true;
$params['arrVote']         = $message->setVoteTargetType(\nt\Vote::getVoteMessageExpertType())->getVote();
$this->renderPartial('/forum/new/block/card/vote', $params);


/**
 * @var string  $otherData
 *
 * @var string  $a['link']      ///kids/medley5/thread/5116723/
 * @var string  $a['title']     //Перейти
 * @var string  $a['className'] //btn-flat btn-flat_size_content btn-flat_accent
 *
 * @var array   $b['className'] //className = card__answer-btn btn-flat btn-flat_size_content btn-flat_accent $text = ответить
 * @var array   $b['text']      //ответить
 */
$params                    = [];
$params['otherData']       =
  $this->renderPartial(
    '/forum/new/block/custom/a',
    ['link' => $expert->getUrlB17OrNull(), 'title' => 'Консультация эксперта', 'className' => 'btn-flat btn-flat_size_tiny btn-flat_secondary btn-flat_noactive', 'targetBlank' => true],
    $return = true
  );

$params['b']['className']  = "card__answer-btn btn-flat btn-flat_size_content btn-flat_accent";
$params['b']['text']       = "Ответить";
$this->renderPartial('/forum/new/block/card/footer', $params);
?>
  </div>
</div>
