<?php

/* мобильный url */
$mobileUrlBase = \Yii::app()->params['baseMobileUrl'];

$forumTopic         = $this->getForumTopic();
$section            = $this->getSection();
$subSection         = $this->getSubSection();

$topicUrl           = $mobileUrlBase.$forumTopic->getSiteUrl($absolute = false);

$sectionName        = $section->getName();
$sectionUrl         = $mobileUrlBase.$section->getSiteUrl();
$subSectionName     = $subSection->getName();
$subSectionUrl      = $mobileUrlBase.$subSection->getSiteUrl();

$userName           = $forumTopic->getUserName();
if(!$forumTopic->isAnonymous() && $forumTopic->getUserId() > 0)
{
    $userFromTopic  = $forumTopic->getUserOrNull();
    if($userFromTopic)
        $userName   = $userFromTopic->getName();
}
if($userName == '') $userName = 'Автор';
$userName           = \Html::encode($userName);

$userUrl            = \ForumTopicAction::getUserUrlByForumTopic($forumTopic);
$userAvatarUrl      = str_replace('http://', 'https://', StaticResourceHelper::staticUrl(\ForumTopicAction::getUserAvatarUrlByForumTopic($forumTopic, $absolute = false)));
$dateCreate         = \DateHelper::format_full($forumTopic->getCreatedAt());

$title              = $forumTopic->getName() ?? $forumTopic->getTitle();
$body               = $forumTopic->getBody();
if(preg_match('#\\\u04\d\d\\\#', $body)) $body = \JSON::decode($body);
$body               = Html::restoreHtml($body);
$body               = StopWordHelper::replaceAbuseWords($body);
$body               = Html::encode($body);
$body               = nl2br($body);
$body               = StringHelper::clearBreaks($body);
$userHashe          = $forumTopic->getUserHash();
?>
<section class="topic-wrapper">
<article class="topic">
    <a class="topic__bread-crunch" href="<?=$mobileUrlBase?>/forum/"><i class="icon icon__arrow-left"></i>Форум Woman.ru</a>
    <div class="topic__bread">
        <a href="<?=$sectionUrl?>forum/" class="topic__bread-link"><?=$sectionName?></a>
        <span>/</span>
        <a href="<?=$subSectionUrl?>forum/" class="topic__bread-link"><?=$subSectionName?></a>
    </div>
    <h1 class="article__title"><?=$title?></h1>
    <p><?=$body?>
    <div style="margin-bottom:30px;"></div>
    <div class="topic__author">
        <div class="msg-card__msg-head">
            <div class="msg-card__img-wrap">
                <amp-img alt="A" src="<?=$userAvatarUrl?>" width="36" height="36"></amp-img>
            </div>
            <div class="msg-card__meta">
                <div class="msg-card__meta-row">
                    <?php if($userUrl):?>
                        <a href="<?=$mobileUrlBase.$userUrl?>">
                            <span class="js-comment__author topic__author-name topic__author-name_auth"> <?=$userName?> </span>
                        </a>
                    <?php else : ?>
                        <span class="js-comment__author topic__author-name"> <?=$userName?> </span>
                    <?php endif ;?>
                </div>
                <div class="msg-card__meta-row"><p class="msg-card__meta-text msg-card__meta-text_light">
                    [<?=$userHashe?>] – <?=$dateCreate?></p>
                </div>
            </div>
        </div>
    </div>
    <footer class="topic__footer">
        <a href="<?=$topicUrl?>#show-d-message/answer-for=<?=$forumTopic->getId()?>" class="topic__answer">Ответить</a>
    </footer>
</article>
<div class="amp-ad_placeholder"><?php echo \BannerHelper::getAmpTopMob(); ?></div>