<?php
/**
 * Блок разделителя страниц
 * @var string  $allAnswersCountText
 * @var bool    $hasVisibleMessagesOnTopic
 *
 * @var array   $anchor
 * @var array   $lastAnswer
 * @var array   $pageSplitter
 */
?>
<div class="page-splitter">
    <div class="page-splitter__container">
        <?php
        /**
        * @var string  $id
        * @var string  $title
        * @var string  $className
        */
        $this->renderPartial('/forum/new/block/custom/anchor', $anchor);

        /**
         * Количество сообщений\тем
         * @var string $text
         */
        $this->renderPartial('/forum/new/block/pageSplitter/answersCount', ['text' => $allAnswersCountText]);

        /**
        * Блок последнего ответа в теме
        * @var string    $lastAnswerDate
        * @var string    $lastAnswerUrl
        * @var bool      $hasVisibleMessages
        */
        $this->renderPartial('/forum/new/block/pageSplitter/lastAnswer', $lastAnswer);

        /**
         * Блок последнего навигации. Упрощенная версия
         * @var int     $pageNumber
         * @var string  $prevUrl
         * @var string  $nextUrl
         */
        $this->renderPartial('/forum/new/block/pageSplitter/pageSplitterSmall', $pageSplitter);

        if (!$hasVisibleMessagesOnTopic)
            $this->renderPartial('/forum/new/block/pageSplitter/info');
        ?>
    </div>
</div>
