<?php
$result = Expert::getAllByCarousel();
?>
<div class="expert-carousel" data-url="/forum/expert/all-answers/?wic=forum_expert&wil=carousel">
    <div class="expert-carousel__container">
        <div class="expert-carousel__title">Эксперты Woman.ru</div>
        <div class="expert-carousel__text">Узнай мнение эксперта по твоей теме</div>
        <div class="expert-carousel__list">
            <ul class="expert-carousel__list-container">
              <?php foreach ($result as $expert): ?>
                  <li class="expert-carousel__expert">
                      <img class="expert-carousel__expert-img" src="<?=ImageHelper::getDefaultImageAsBase64()?>" data-src="<?=$expert['avatarUrl']?>">
                      <div class="expert-carousel__expert-block">
                          <div class="expert-carousel__expert-name"><?=$expert['name']?></div>
                          <div class="expert-carousel__expert-info"><?=$expert['description']?>
                          </div>
                      </div>
                  </li>
              <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
