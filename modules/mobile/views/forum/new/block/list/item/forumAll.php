<?php
/**
 * @var \nt\Forum\Topic $topic
 * @var \nt\Forum\Message $message
 */
?>
<div class="card card_answer">
  <div class="card__container">
<?php
/**
 * @var string $className
 * @var string $title
 *
 * @var string $params['a']['link']
 * @var string $params['a']['title']
 * @var string $params['a']['className']
 */

$params                     = [];
$params['className']        = "card__parent";
$params['title']            = "";
$params['a']['link']        = $topic->getSiteUrl();
$params['a']['title']       = $topic->getName();
$params['a']['className']   = "card__parent-link";
$this->renderPartial('/forum/new/block/custom/h2', $params);

/**
 * @var string $image | null
 * @var string  $name
 * @var int     $metaId
 * @var string  $metaDate
 * @var string  $metaTimeStamp
 * @var string  $isBlocked
 */
$userName                   = $message->getUserName();
$user                       = $message->getUserOrNull();
if($user)   $userName       = $message->getUserName();
$params                     = [];
$params['imageUrl']         = \ForumTopicAction::getUserAvatarUrlByForumMessage($message, $absolute = false);
$params['name']             = $message->getUserName();
$params['metaId']           = $message->getUserHash();
$params['metaDate']         = DateHelper::format_full($message->getCreatedAt());
$params['metaTimeStamp']    = $message->getCreatedAtAsTimestamp();
$params['isAuthor']         = false;
$params['user']             = $message->getUserOrNull();
$params['isAnonymous']      = $message->isAnonymous();
$params['isBlocked']        = false;
$this->renderPartial('/forum/new/block/card/user_block', $params);

/**
 * @var Quote[]   $quotes
 * @var string    $body
 */
$params                     = [];
$params['body']             = $message->getBodyWithoutQuotes();
$params['quotes']           = $message->getQuotesFromBody();
$this->renderPartial('/forum/new/block/card/text', $params);


/**
 * @var string  $otherData
 *
 * @var string  $params['a']['link']      ///kids/medley5/thread/5116723/
 * @var string  $params['a']['title']     //Перейти
 * @var string  $params['a']['className'] //btn-flat btn-flat_size_content btn-flat_accent
 */
$countAnswerAll             = $topic->getAnswerCountAll();
$params                     = [];
$params['otherData']       =
  $this->renderPartial(
    '/forum/new/block/custom/div',
    ['className' => 'card__other-data_answers', 'text' => $countAnswerAll .' '.TextHelper::declension($countAnswerAll, ['ответ', 'ответа', 'ответов'])],
    $return = true
  );

$pages                      = ceil($topic->getAnswerCountAll() / \Yii::app()->params['limits']['messagesOnThread']);
$topicLastUrl               = $topic->getSiteUrl().'#m'.$message->getId();
if($pages > 1)
  $topicLastUrl             = $topic->getSiteUrl().$pages.'/#m'.$message->getId();

$params['a']['link']        = $topicLastUrl;
$params['a']['title']       = "Перейти";
$params['a']['className']   = "btn-flat btn-flat_size_content btn-flat_accent";
$this->renderPartial('/forum/new/block/card/footer', $params);
?>
  </div>
</div>
