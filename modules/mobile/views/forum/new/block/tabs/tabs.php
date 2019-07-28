<?php
$baseUrl    = isset($baseUrl) ? $baseUrl : '';

$day1       = "сутки"; //isset($_GET['testView']) && $_GET['testView'] == 'Y' ? 'сутки' : 'сегодня';
?>
<div class="tabs">
    <div class="tabs__container">
        <a class="tabs__tab
        <?= (isset($_GET['sort']) && $_GET['sort']==='1d') ? "tabs__tab_active" : ''?>"
           href="<?=$baseUrl?>?sort=1d"><?=$day1?></a>
        <a class="tabs__tab
        <?= (isset($_GET['sort']) && $_GET['sort']==='3d') ? "tabs__tab_active" : ''?>"
            href="<?=$baseUrl?>?sort=3d">3 дня</a>
        <a class="tabs__tab
        <?= (isset($_GET['sort']) && $_GET['sort']==='7d') ? "tabs__tab_active" : ''?>"
        href="<?=$baseUrl?>?sort=7d">неделя</a>
        <a class="tabs__tab
         <?= (isset($_GET['sort']) && $_GET['sort']==='30d') ? "tabs__tab_active" : ''?>"
           href="<?=$baseUrl?>?sort=30d">месяц</a>
        <a class="tabs__tab
        <?= (isset($_GET['sort']) && $_GET['sort']==='all') ? "tabs__tab_active" : ''?>"
           href="<?=$baseUrl?>?sort=all">все</a>
    </div>
</div>