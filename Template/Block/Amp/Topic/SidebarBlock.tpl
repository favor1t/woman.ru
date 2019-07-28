<?php

/* мобильный url */
$mobileUrlBase = \Yii::app()->params['baseMobileUrl'];

?>
<amp-sidebar id="sidebar" class="sample-sidebar" layout="nodisplay" side="left">
  <section class="sidebar__topbar topbar">
    <div class="topbar__close" on="tap:sidebar.toggle" role="link" tabindex="0">
      <svg class="topbar__icon" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m29.41013 25.99551 6.2899-6.29628c.3908-.39053.3908-1.02509 0-1.41566-.39082-.39124-1.02371-.39124-1.41451 0l-6.2899 6.29592-6.29019-6.29592c-.39066-.39124-1.02354-.39124-1.41436 0-.3908.39057-.3908 1.02514 0 1.41566l6.29019 6.29628-6.29019 6.29592c-.3908.39093-.3908 1.02514 0 1.41606.19541.19529.45155.29342.70726.29342.2557 0 .51184-.09813.70709-.29342l6.29019-6.29628 6.2899 6.29628c.19541.19529.45155.29342.70724.29342.25572 0 .51186-.09813.70726-.29342.3908-.39093.3908-1.02514 0-1.41606z" fill="#c63491" transform="translate(-18 -16)"/></svg>
    </div>
    <a class="topbar__search" href="<?=$mobileUrlBase?>/search/?q=&control_charset=%D0%9A%D0%BE%D0%BD%D1%82%D1%80%D0%BE%D0%BB%D1%8C&category=&sort=relevance&yt0=%D0%9D%D0%B0%D0%B9%D1%82%D0%B8&where=forum">
      <svg class="topbar__icon" height="25" viewBox="0 0 25 25" width="25" xmlns="http://www.w3.org/2000/svg"><path d="m283.76 15.56a7.2 7.2 0 0 1 6.98 1.94 7.46 7.46 0 0 1 .06 10.4 7.08 7.08 0 0 1 -10.2-.06 7.46 7.46 0 0 1 -.05-10.4c.89-.9 2-1.55 3.21-1.88zm-7.57 7.06a9.81 9.81 0 0 0 2.81 6.85 9.47 9.47 0 0 0 9.19 2.55 9.27 9.27 0 0 0 3.38-1.7l7.02 7.14 1.6-1.63-7.02-7.15a9.81 9.81 0 0 0 -.83-12.81 9.31 9.31 0 0 0 -13.42-.08 9.6 9.6 0 0 0 -2.73 6.83z" fill="#be007f" transform="translate(-276 -13)"/></svg>
    </a>
  </section>
  <section class="sidebar__rubrics rubrics">
    <h3 class="rubrics__title">Все рубрики</h3>
    <amp-accordion disable-session-states>
      <?php
      $menuItems = $this->getMenuItems();
      foreach($menuItems as $item){
        if(isset($item['submenu']) && ! empty($item['submenu'])):
      ?>
      <section class="rubrics__rubric">
        <h2 class="rubrics__rubric-name">
          <a class="rubrics__rubric-link"href="<?=$mobileUrlBase.($item['href'] ?? '/')?>"><?=( $item['name'] ?? '')?></a>
        </h2>
        <ul class="rubrics__subrubrics-list">
          <?php
            foreach($item['submenu'] as $subItem):
          ?>
          <li class="rubrics__subrubric">
            <a href="<?=$mobileUrlBase.($subItem['href'] ?? '')?>" class="rubrics__subrubric-link"><?=($subItem['name'] ?? '')?></a>
          </li>
          <?php
            endforeach;
          ?>
        </ul>
      </section>
      <?php
        endif;
      }
      ?>
    </amp-accordion>
  </section>
</amp-sidebar>
