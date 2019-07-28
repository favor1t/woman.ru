<div class="expert">
    <div class="expert__container">
        <div class="expert__top">
            <div class="expert__title icon-before_expert-24-black">Ответ эксперта</div><a class="expert__rules" href="/womanru/guestbook/article/215964/"">Правила</a>
        </div>
<?php
/**
 * @var \nt\Forum\Message $message
 * @var $pageNumber
 */
$this->renderPartial('/forum/new/block/list/item/expertMessage', [
  'message'       => $expertMessage,
  'pageNumber'    => $pageNumber
])
?>
    </div>
</div>
