<?php
/**
 * @var \nt\Forum\Topic $topic
 */
$url                = $topic->getSiteUrl();
$title              = $topic->getName();
$timeStamp          = $topic->getCreatedAtAsTimestamp();
$createdAt          = DateHelper::format_full($topic->getCreatedAt());
$count              = $topic->getCountAnswersBySort($_GET['sort'] ?? '');
$countAnswersString = $count . ' ' . TextHelper::declension($count, ['ответ', 'ответа', 'ответов']);
?>
<li class="list-item">
    <a class="list-item__link" href="<?= $url ?>">
        <div class="list-item__container">
            <div class="list-item__first-line">
                <div class="list-item__title"><?= $title ?></div>
            </div>
            <div class="list-item__second-line">
                <div class="list-item__date text text_regular text_gray"
                     data-date="<?= $timeStamp ?>"><?= $createdAt ?></div>
                <div class="list-item__other-text list-item__other-data_answers text_gray"><?= $countAnswersString ?></div>
            </div>
        </div>
    </a>
</li>
