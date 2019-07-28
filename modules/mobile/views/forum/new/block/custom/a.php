<?php
/**
 * @var string  $link
 * @var string  $title
 * @var string  $className   | Optional     // "card__parent-link"
 * @var bool    $targetBlank | default: false
*/
$targetBlank = isset($targetBlank) ? $targetBlank : false;
$className   = isset($className) ? $className : '';
?>
<a class="<?=$className?>" href="<?=$link?>" <?php if ($targetBlank) echo 'target="_blank"'; ?>"><?=$title?></a>