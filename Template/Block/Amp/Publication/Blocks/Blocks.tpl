<?php
/**
 * @var $blocks BlockCollection
 * @var $model Publication
 */

$i = 0;
$readMoreBlockRendered = 0;
$readMoreBlockReturn = '';
if ($blocks->owner && isset($blocks->owner->relatedPublications) && $blocks->owner->relatedPublications &&
  !isset($blocks->byType[BaseBlock::TYPE_READ_ALSO])) {
  $readMoreBlockReturn = $this->renderBodyBlock('_related', array('model' => $blocks->owner), true);
}
$isInsertedReadability = false;
$mainWasClose = false;
$hasReadAlso = false;
echo '<main class="article__main"><!--#header#-->'; // do not remove '<!--header-->' it is replace pattern

foreach ($blocks as $i => $block) {
  if ($block->type == 'readAlso' && !$block->isEmpty()) {
    $hasReadAlso = true;
  }

  if (file_exists($this->getTemplatePath() . '/Blocks/' . $block->type . '.tpl')) {
    if ($block->type === 'readAlso') {   //WMN-1133
      $mainWasClose = true;
      echo '</main><div class="gtm-anchor" data-action="article_readability" data-label="article_content_end"></div>';
    }
    $html = $this->renderBodyBlock($block->type, array('blockIndex' => $i++, 'block' => $block, 'model' => $model), $return = true);

    if (
      $block->type == 'readAlso' &&
      $hasReadAlso &&
      $model->comments_mode !== \Publication::COMMENTS_MODE_NONE
    ) {
      $commentsCount = \Comment::getCountByArticle($model);
      $commentsAnchorHtml = $this->renderBodyBlock('_commentsAnchor', [], $return = true);

      $html = $commentsAnchorHtml . $html;
    }

    if ($readMoreBlockReturn != '' && strpos($return, '<!-- readmore -->') !== false) {
      $block->owner->isAlreadyRelatedPublications = true;
      $return = str_replace('<!-- readmore -->', $readMoreBlockReturn, $html);
    }

    if (!in_array($block->type, ['gallery', 'video',])) {
      $html = \Html::cleanup($html);
    }

    echo $html;
  }
}

if( !$hasReadAlso && ! $model->is_advertisement) {
  if ($mainWasClose === false) {
    echo "</main>";
    $mainWasClose = true;
  }
  $items = $model->getRelatedChartbeatPublication($idExept = [], $cntItems = 3, $currentId = $model->id);
  require "_relatedTemplate.tpl";
}

if ($mainWasClose === false) {
  echo "</main>";
}
if ($isInsertedReadability) { //WMN-1133
  echo '<div class="gtm-anchor" data-action="article_readability" data-label="article_content_end"></div>';
}
?>
<dl class="article__info">
  <?php if (isset($model->author) && ($author = trim($model->author))): ?>
    <dt>Текст:</dt>
    <dd><?= $author; ?></dd>
  <?php endif; ?>
  <?php if ($model->photo_source): ?>
    <dt>Фото:</dt>
    <dd><?= $model->photo_source; ?></dd>
  <?php endif; ?>
  <dt>Дата:</dt>
  <dd><?= $model->formatTimestamp('published_at', 'dd.MM.yyyy') ?></dd>
  <?php try {
    $tags = $model->getTags();
  } catch (Exception $err) {
    $tags = null;
  }
  if ($tags != null) :
    $_tags = [];
    foreach ($tags as $tag) {
      if ($tag->is_hidden || $tag->is_hidden_from_list || $tag->status == Tag::STATUS_OFF) {
        continue;
      }
      $_tags[] = Html::link($tag->name, $tag->getSiteUrl($absolute = true), array('title' => $tag->name . $tag->getPostfix()));
    } ?>
    <dt>Теги:</dt>
    <dd>
      <?= implode(', ', $_tags); ?>
    </dd>
  <?php endif; // $tags ?>
</dl>
