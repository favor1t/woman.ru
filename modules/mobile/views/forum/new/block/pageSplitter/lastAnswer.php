<?php
/**
 * Блок последнего ответа в теме
 * @var string  $lastAnswerDate
 * @var string  $lastAnswerUrl
 * @var bool    $hasVisibleMessageOnPage
 */
?>
<?php if($lastAnswerDate): ?>
    <div class="page-splitter__last-answer">
        <div class="page-splitter__last-answer-text" data-date="<?=strtotime($lastAnswerDate)?>">Последний — <span class="page-splitter__last-answer-date"><?= DateHelper::format_full($lastAnswerDate) ?></span></div>
        <?php if ($hasVisibleMessageOnPage): ?>
            <a class="page-splitter__last-answer-btn btn-flat btn-flat_size_tiny btn-flat_notable"
              href="<?= $lastAnswerUrl ?>#message_last">Перейти</a>
        <?php endif; ?>
    </div>
<?php endif; ?>