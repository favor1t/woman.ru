<?php
/**
 * @var \nt\Forum\Topic[]   $topics
 * @var \nt\Forum\Message[] $messages
 */
$adInjected     = false;
$countShowTopic = 0;
$bannerTemplate = \BannerHelper::getNewBlockTopicBanner();
foreach ($messages as $message):

  if(!$message->isStatusVisible()) continue;
  $countShowTopic++;
  $this->renderPartial('/forum/new/block/list/item/forumAll',[
    'message' => $message,
    'topic'   => $topics[$message->getTopicId()],
  ]);


  if ($countShowTopic % \mobile\helpers\AdHelper::FORUM_THREADS_LIST_AD_INJECT_RATE == 0) {
    $adInjected = true;
    $this->renderPartial('/forum/new/block/banner', ['template' => $bannerTemplate]);
  }

endforeach;

if(!$adInjected) $this->renderPartial('/forum/new/block/banner', ['template' => $bannerTemplate]);
?>

