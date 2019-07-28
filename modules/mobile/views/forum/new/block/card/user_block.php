<?php
/**
 * @var string      $imageUrl | null
 * @var string      $name
 * @var \nt\User    $user | null
 * @var int         $metaId
 * @var string      $metaDate
 * @var string      $metaTimeStamp
 * @var bool        $isAuthor
 * @var bool        $isAnonymous
 * @var bool        $isBlocked
 * @var bool        $isExpert | default: false
 * @var string      $metaDescription | Optional
 * @var string      $expertUrl | Optional
 */
$addUserClass = '';
if($isAuthor) $addUserClass = 'card__user-pic_author';
$isBlockedClass = '';
if($isBlocked) $isBlockedClass = 'card__user-pic_ban';


$isExpert        = isset($isExpert) ? $isExpert : false;
$expertUrl       = isset($expertUrl) ? $expertUrl : '';
$metaDescription = isset($metaDescription) ? $metaDescription : '';

?>
<div class="card__user-block">
    <?php if(strpos($imageUrl, '/i/userpic.gif') !== false || $isAnonymous):?>
      <div class="card__user-pic <?=$isBlockedClass?> <?=$addUserClass?> "></div>
    <?php else: ?>
      <img class="card__user-pic <?=$isBlockedClass?> <?=$addUserClass?>" src="<?=$imageUrl?>" alt="<?=$name?>"/>
    <?php endif;?>
    <div class="card__user-info">
      <?php if($user && !$isAnonymous):?>
        <a class="card__user-name" href="<?php if (!$isExpert) {echo $user->getSiteUrl();} else {echo $expertUrl;}?>"><?=$name?></a>
      <?php else:?>
        <div class="card__user-name"><?=$name?></div>
      <?php endif;?>

        <div class="card__user-metadata">
          <?php if(!$isExpert): ?>
            <span class="card__user-metadata_id" data-id="<?=$metaId?>">[<?=$metaId?>] – </span>
            <span class="card__user-metadata_date" data-date="<?=$metaTimeStamp?>"><?=$metaDate?></span>
          <?php else: ?>
            <span class="card__user-metadata_id" data-id="<?=$metaId?>"></span>
            <span class="" ><?=$metaDescription?></span>
          <?php endif;?>
        </div>
    </div>
</div>
