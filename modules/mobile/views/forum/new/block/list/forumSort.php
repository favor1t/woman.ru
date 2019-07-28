<?php
/**
 * @var int $pageNumber
 * @var \nt\Forum\Topic[] $topics
 */
$adInjected     = false;
$countShowTopic = 0;
$bannerTemplate = \BannerHelper::getNewBlockTopicListBanner();
$showBanners    = true;
if(ViewBlockHelper::isShowOnlyList()) $showBanners = false;

?>
<div class="list" data-page-index="<?= $pageNumber ?>" data-wid="<?= $pageNumber - 1 ?>">
    <ul class="list__container">
      <?php
      foreach ($topics as $topic):

        $this->renderPartial('/forum/new/block/list/item/forumSort', [
          'topic' => $topic,
        ]);
        $countShowTopic++;

        if ($showBanners && $countShowTopic % \mobile\helpers\AdHelper::FORUM_THREADS_LIST_AD_INJECT_RATE == 0) {
          $adInjected = true;
          $this->renderPartial('/forum/new/block/banner', ['template' => $bannerTemplate]);
        }

      endforeach;

      if($showBanners && !$adInjected) $this->renderPartial('/forum/new/block/banner', ['template' => $bannerTemplate]);
?>
    </ul>
</div>

