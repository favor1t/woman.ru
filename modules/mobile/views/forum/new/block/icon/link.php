<?php
/**
 * Большие иконки
 * @var bool    $isActive
 * @var string  $activeClass
 * @var array   $params[]
 * Значения в массиве:
 * @var string  $iconClass
 * @var string  $text
 * @var string  $url
 */

$isActive       = $params['isActive'];
$activeClass    = $params['activeClass'];
$text           = $params['text'] ?? 'Мой форум';
$url            = $params['url'] ?? '#';
$iconClass      = $params['iconClass'] ?? 'menu__link-icon_my-forum';
?>
<li class="menu__item">
  <a class="menu__link <?php if($isActive) echo $activeClass ?>" href="<?=$url?>"><span class="menu__link-icon <?=$iconClass?>"></span><?=$text?></a>
</li>