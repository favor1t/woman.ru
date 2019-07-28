<?php
/**
 * @var string   $body
 * @var Quote[]  $quotes
 */
?>
<div class="card__text">
    <?php if(isset($quotes) && !empty($quotes)) $this->renderPartial('/forum/new/block/card/quote', ['quotes' => $quotes]) ?>
    <div class="card__comment"><?=nl2br($body)?></div>
    <?php if(isset($fullMessageBtn) && $fullMessageBtn === true): ?>
        <div class="card__text_other-data">
            <div class="ajax-loader">
                <?php
                    $this->renderPartial(
                        '/forum/new/block/custom/div',
                        ['className' => 'card__get-full-message-btn btn-flat btn-flat_size_content btn-flat_accent', 'text' => 'Показать полностью']
                    );
                    $this->renderPartial(
                        '/forum/new/block/custom/div',
                        ['className' => 'loader-spin loader-spin_hidden', 'text' => '']
                    );
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
