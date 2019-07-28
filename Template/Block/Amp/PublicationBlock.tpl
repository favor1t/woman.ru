<?php
ob_start();
$commentsCount = $this->publication->comments_count;
$section = $this->publication->getIndexSection();
require 'Publication/Blocks/_commentCountByAnchor.tpl';
$htmlCommentCountAnchor = ob_get_clean();
ob_start();
?>
<header>
  <a href="<?= $section->getSiteUrl($absolute = true) ?>" class="article__section"><?= $section->name ?></a>
  <h1 class="article__title"><?= $this->publication->name; ?></h1>
</header>
<?php
$header = ob_get_clean();
?>
<section class="article">
  <section class="article__end">
  <?php
  $body = str_replace('#commentsCountByArticle#', $htmlCommentCountAnchor, $this->publication->body);;
  $body = str_replace('<!--#header#-->', $header, $body);
  echo $body;
  ?>
  <div class="clear"></div>
  </section>
  <?php if($commentsCount) {
    require 'Publication/Blocks/comments.tpl';
  } ?>
</section>