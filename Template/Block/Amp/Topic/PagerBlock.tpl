<?php $topicUrlAmp = $this->getUrl(); ?>
<?php if($this->getMaxPage() > 1): ?>
<div class="page-pager <?php if($this->isSticky()):?>page-pager_sticky <?php endif;?>">
    <div class="page-pager__section page-pager__section--pager">
        <div class="page-pager__pager">

            <?php if($this->isFirstPage()): ?>
            <div class="page-pager__pager-item page-pager__pager-item--left f-arrow f-passive">
                <div class="page-pager__button page-pager__button--left">
              <span class="page-pager__arrow">
                <svg height="12" viewBox="0 0 7 12" width="7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path id="a" d="m298.52 544.53-4.11-4.08a.67.67 0 0 0 -.95 0 .66.66 0 0 0 0 .94l3.64 3.61-3.64 3.61a.66.66 0 0 0 0 .94c.26.26.69.26.95 0l4.11-4.08a.66.66 0 0 0 0-.94z" fill="#be007f" stroke="#be007f" stroke-miterlimit="50" stroke-width=".6" transform="translate(-292 -539)"></path></svg>
              </span>
                </div>
            </div>
            <?php else : ?>
            <div class="page-pager__pager-item page-pager__pager-item--left f-arrow">
                <div class="page-pager__button page-pager__button--left">
                    <?php if($this->isSecondPage()): ?>
                    <a class="page-pager__arrow" href="<?=$topicUrlAmp?>">
                        <svg height="12" viewBox="0 0 7 12" width="7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path id="a" d="m298.52 544.53-4.11-4.08a.67.67 0 0 0 -.95 0 .66.66 0 0 0 0 .94l3.64 3.61-3.64 3.61a.66.66 0 0 0 0 .94c.26.26.69.26.95 0l4.11-4.08a.66.66 0 0 0 0-.94z" fill="#be007f" stroke="#be007f" stroke-miterlimit="50" stroke-width=".6" transform="translate(-292 -539)"></path></svg>
                    </a>
                    <?php else : ?>
                    <a class="page-pager__arrow" href="<?=$topicUrlAmp.($this->getPage() - 1)?>">
                        <svg height="12" viewBox="0 0 7 12" width="7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path id="a" d="m298.52 544.53-4.11-4.08a.67.67 0 0 0 -.95 0 .66.66 0 0 0 0 .94l3.64 3.61-3.64 3.61a.66.66 0 0 0 0 .94c.26.26.69.26.95 0l4.11-4.08a.66.66 0 0 0 0-.94z" fill="#be007f" stroke="#be007f" stroke-miterlimit="50" stroke-width=".6" transform="translate(-292 -539)"></path></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!$this->isFirstPage()): ?>
            <div class="page-pager__pager-item page-pager__pager-item--first">
                <a href="<?=$topicUrlAmp?>" class="page-pager__num">1</a>
            </div>
            <?php if(!$this->isSecondPage()): ?>
            <div class="page-pager__pager-item page-pager__pager-item--dots">
                <span class="page-pager__dots">…</span>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php if($this->hasPreviewPage()): ?>
            <div class="page-pager__pager-item page-pager__pager-item--prev page-pager__pager-item--left">
                <a href="<?=$topicUrlAmp.($this->getPage() - 1)?>/" class="page-pager__num"><?php echo ($this->getPage() - 1);?></a>
            </div>
            <?php endif; ?>

            <div class="page-pager__pager-item page-pager__pager-item--current">
                <span class="page-pager__num"><?=$this->getPage()?></span>
            </div>
            <?php if($this->hasNextPage()): ?>
            <div class="page-pager__pager-item page-pager__pager-item--next page-pager__pager-item--right">
                <a href="<?=$topicUrlAmp.($this->getPage() + 1)?>/" class="page-pager__num"><?php echo ($this->getPage() + 1);?></a>
            </div>
            <?php endif; ?>

            <?php if(!$this->isLastPage()): ?>
            <?php if(!$this->isPenultPage()): ?>
            <div class="page-pager__pager-item page-pager__pager-item--dots">
                <span class="page-pager__dots">…</span>
            </div>
            <?php endif; ?>
            <div class="page-pager__pager-item page-pager__pager-item--last">
                <a href="<?=$topicUrlAmp.$this->getMaxPage()?>/" class="page-pager__num"><?=$this->getMaxPage()?></a>
            </div>
            <?php endif; ?>

            <?php if($this->isLastPage()): ?>
            <div class="page-pager__pager-item page-pager__pager-item--right f-arrow f-passive">
                <div class="page-pager__button page-pager__button--right">
              <span class="page-pager__arrow">
                <svg height="12" viewBox="0 0 7 12" width="7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path id="a" d="m298.52 544.53-4.11-4.08a.67.67 0 0 0 -.95 0 .66.66 0 0 0 0 .94l3.64 3.61-3.64 3.61a.66.66 0 0 0 0 .94c.26.26.69.26.95 0l4.11-4.08a.66.66 0 0 0 0-.94z" fill="#be007f" stroke="#be007f" stroke-miterlimit="50" stroke-width=".6" transform="translate(-292 -539)"></path></svg>
              </span>
                </div>
            </div>
            <?php else : ?>
            <div class="page-pager__pager-item page-pager__pager-item--right f-arrow">
                <div class="page-pager__button page-pager__button--right">
                    <a class="page-pager__arrow" href="<?=$topicUrlAmp.($this->getPage() + 1)?>/">
                        <svg height="12" viewBox="0 0 7 12" width="7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path id="a" d="m298.52 544.53-4.11-4.08a.67.67 0 0 0 -.95 0 .66.66 0 0 0 0 .94l3.64 3.61-3.64 3.61a.66.66 0 0 0 0 .94c.26.26.69.26.95 0l4.11-4.08a.66.66 0 0 0 0-.94z" fill="#be007f" stroke="#be007f" stroke-miterlimit="50" stroke-width=".6" transform="translate(-292 -539)"></path></svg>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
