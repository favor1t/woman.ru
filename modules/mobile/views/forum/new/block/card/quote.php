<?php
/**
 * @var Quote[]   $quotes
 */
?>
<div class="card__quote">
    <?php
    foreach ($quotes as $quote){
        $multiple = $quote->subquotes ? true : false;
        $this->renderPartial('/forum/new/block/custom/div', ['className' => 'card__quote-title', 'text' => $quote->name]);
        $this->renderPartial('/forum/new/block/custom/div', ['className' => 'card__quote-text',  'text' => $quote->body]);
        if($multiple)
          foreach ($quote->subquotes as $subquote)
            $this->renderPartial('/forum/new/block/card/quote', ['quotes' => $subquote]);
    }
    ?>
</div>