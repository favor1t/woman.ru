<div class="favorite">
    <div class="favorite__container">
      <?php
      /**
       * @var string $text
       * @var string $inputProp
       * @var string $inputType
       */
      $arrParams['text'] = $text;
      $arrParams['inputProp'] = $inputProp;
      $arrParams['inputType'] = $inputType;
      $this->renderPartial('/forum/new/block/custom/switch', $arrParams)
      ?>
    </div>
</div>