<?php
/**
 * @var string $text
 * @var string $inputProp
 * @var string $inputType
 */
?>
<div class="switch">
    <label class="switch__container">
        <span class="switch__text"><?=$text?></span>
        <input class="switch__input" type="<?=$inputType?>" <?=$inputProp?>/>
        <span class="switch__control"></span>
    </label>
</div>
