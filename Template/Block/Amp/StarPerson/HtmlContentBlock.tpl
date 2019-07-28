<section class="person__body">
  <?php
  $htmlFull = \PersonHelper::parseHtml($this->getStarPerson()->getHtmlFull());
  foreach ($htmlFull as $item):
  ?>
      <h3 class="person__body-title"><?=$item['title'] ?? '' ;?></h3>
      <?=$item['html'] ?? '';?>
  <?php endforeach;?>
</section>