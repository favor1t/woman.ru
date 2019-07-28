<?php
/**
 * @var $pageNumber int
 * @var $topic      \nt\Forum\Topic
 * @var $section    Section
 * @var $subSection Section
 * @var $pageNumber
 */
?>
<div class="card card_topic-start" id="m<?=$topic->getId()?>" data-id="<?=$topic->getId()?>" data-type="<?=\nt\Vote::getTopicType()?>">
    <div class="card__container card__container_topic-start">
<?php
/**
 * @var $section    Section
 * @var $subSection Section
 */
$params                   = [];
$params['section']        = $section;
$params['subSection']     = $subSection;
$this->renderPartial('/forum/new/block/custom/breadcrumbs', $params);


if($topic->isStatusClosed()){
/**
 * @var string  $text
 * @var string  $className
 */
$params                   = [];
$params['text']           = "Обсуждение закрыто";
$params['className']      = "card__status card__status_closed";
$this->renderPartial('/forum/new/block/custom/div', $params);

}
/**
 * @var string  $text
 * @var string  $className
 */
$params                   = [];
$params['text']           = $topic->getName();
$params['className']      = "card__topic-title";
$this->renderPartial('/forum/new/block/custom/div', $params);

/**
 * @var string   $body
 * @var Quote[]  $quotes
 */
$params                    = [];
$params['body']            = $topic->getBodyWithoutQuotes();
if($pageNumber > 1 && mb_strlen($params['body']) > 250) {
  $params['body'] = StringHelper::crop($params['body'], $length = 250, $appendText = ' ...');
  $params['fullMessageBtn'] = true;
}
$params['quotes']          = $topic->getQuotesFromBody() ?? [];
$this->renderPartial('/forum/new/block/card/text', $params);

/**
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
$params['imageUrl']         = \ForumTopicAction::getUserAvatarUrlByForumTopic($topic, $absolute = false);
$params['name']             = $topic->getUserNameDisplay();
$params['metaId']           = $topic->getUserHash();
$params['metaDate']         = DateHelper::format_full($topic->getCreatedAt());
$params['metaTimeStamp']    = $topic->getCreatedAtAsTimestamp();
$params['isAuthor']         = false;
$params['isAnonymous']      = $topic->isAnonymous();
$params['user']             = $topic->getUserOrNull();
$params['isBlocked']        = false;
$params['isExpert']         = false;
$this->renderPartial('/forum/new/block/card/user_block',$params);

/**
 * @var  array $arrVotes['count_like']
 * @var  array $arrVotes['count_dislike']
 */
$params                    = [];
$params['arrVote']         = $topic->getVote();
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
$params['b']['className']  = "card__answer-btn btn-flat btn-flat_size_content btn-flat_accent";
$params['b']['text']       = "Ответить";
$this->renderPartial('/forum/new/block/card/footer', $params);
?>
    </div>
</div>

