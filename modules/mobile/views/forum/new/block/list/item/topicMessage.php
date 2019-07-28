<?php
/**
 * @var int $index
 * @var \nt\Forum\Message $message
 * @var array $userBanList
 * @var bool $isAuthor
 * @var bool $isTopicAuthor
 * @var bool $isLastMessage
 */
$isBlocked = isset($userBanList[$message->getUserCookie()]);
?>
<div class="card card_answer" id="m<?=$message->getId()?>" data-id="<?=$message->getId()?>" data-type="<?= $message->getVoteTargetType()?>">
  <div class="card__container">
<?php

/**
 * @var string  $id
 * @var string  $className
 */
if ($isLastMessage) {
  $params               = PageHelper::getAnchorDefaultParams();
  $params['id']         = 'message_last';
  $params['className']  = 'card__anchor';
  $this->renderPartial('/forum/new/block/custom/anchor',$params);
}

/**
 * @var string  $isBlocked
 */
$params                     = [];
$params['isBlocked']        = $isBlocked;
$this->renderPartial('/forum/new/block/card/ban_info_block',$params);

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
 * @var bool        $isExpert | default: false
 * @var string      $metaDescription | Optional
 * @var string      $expertUrl | Optional
 */
$params                     = [];
$params['imageUrl']         = \ForumTopicAction::getUserAvatarUrlByForumMessage($message, $absolute = false);
$params['name']             = $index.'. '.$message->getUserName();
$params['metaId']           = $message->getUserHash();
$params['metaDate']         = DateHelper::format_full($message->getCreatedAt());
$params['metaTimeStamp']    = $message->getCreatedAtAsTimestamp();
$params['isAuthor']         = $isAuthor;
$params['isAnonymous']      = $message->isAnonymous();
$params['user']             = $message->getUserOrNull();
$params['isBlocked']        = $isBlocked;
$params['isExpert']         = false;
$this->renderPartial('/forum/new/block/card/user_block',$params);

/**
 * @var string   $body
 * @var Quote[]  $quotes
 */
$params                    = [];
$params['body']            = $message->getBodyWithoutQuotes();
$params['quotes']          = $message->getQuotesFromBody() ?? [];
$this->renderPartial('/forum/new/block/card/text', $params);

/**
 * @var  array $arrVotes['count_like']
 * @var  array $arrVotes['count_dislike']
 * @var  bool  $isExpert  | default: false
 */
$params                    = [];
//$params['arrVote']         = $topic->getVote();
$params['arrVote']['count_like']         = 0;
$params['arrVote']['count_dislike']      = 0;
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
    '/forum/new/block/custom/div',
    ['className' => 'card__exclaim icon-before_abuse-18-blue icon_18', 'text' => ''],
    $return = true
  );

if($isTopicAuthor && !$isAuthor){
    $classNames             = 'card__ban-user icon-before_block-user-18-blue-red';
    if($isBlocked) $classNames   = 'card__unban-user icon-before_add-user-18-blue-green';
    $params['otherData']       .=
      $this->renderPartial(
        '/forum/new/block/custom/div',
        ['className' => $classNames.' icon_18', 'text' => ''],
        $return = true
      );
}

$params['b']['className']  = "card__answer-btn btn-flat btn-flat_size_content btn-flat_accent";
$params['b']['text']       = "Ответить";
$this->renderPartial('/forum/new/block/card/footer', $params);
?>
  </div>
</div>
