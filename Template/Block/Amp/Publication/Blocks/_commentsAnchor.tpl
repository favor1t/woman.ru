<?php

if ($SKIP_COMMENTS = true) return;

?>
<div class="comments-anchor">
    <a class="comments-anchor__btn" href="#comments">Перейти к обсуждению
      #commentsCountByArticle#
    </a>
    <div class="comments-anchor__shares"><?php $this->renderBodyBlock('_sharingButtons') ?></div>
    <div class="comments-anchor__shares-btn"></div>
</div>