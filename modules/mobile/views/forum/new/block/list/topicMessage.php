<?php
/**
 * @var \nt\Forum\Topic     $topic
 * @var \nt\Forum\Message[] $messages
 * @var int                 $page
 */

$adInjected     = false;
$countShowTopic = 0;
$bannerTemplate = \BannerHelper::getNewBlockMessageBanner();
$isTopicAuthor  = ForumHelper::isTopicAuthor($topic);
$userBanList    = \nt\Forum\Ban::getCookiesByTopic($topic);

foreach ($messages as $index => $message):
  if(!$message->isStatusVisible()) continue;

  $countShowTopic++;
  $isAuthor = \ForumHelper::isAuthor($topic, $message);

  /**
   * @var int $index
   * @var \nt\Forum\Message $message
   * @var array $userBanList
   * @var bool $isAuthor
   * @var bool $isTopicAuthor
   * @var bool $isLastMessage
   */
  $count    = \Yii::app()->params['limits']['messagesOnThread'];
  $indexMessage = $index + ($page * $count - $count) + 1;
  $this->renderPartial('/forum/new/block/list/item/topicMessage',[
    'message'       => $message,
    'isAuthor'      => $isAuthor,
    'isTopicAuthor' => $isTopicAuthor,
    'userBanList'   => $userBanList,
    'index'         => $indexMessage,
    'isLastMessage' => $topic->getAnswerCountAll() >= $indexMessage
  ]);

  if ($countShowTopic % \mobile\helpers\AdHelper::FORUM_THREADS_LIST_AD_INJECT_RATE == 0) {
    $adInjected = true;
    $this->renderPartial('/forum/new/block/banner', ['template' => $bannerTemplate]);
  }
endforeach;

if(!$adInjected) $this->renderPartial('/forum/new/block/banner', ['template' => $bannerTemplate]);
?>

