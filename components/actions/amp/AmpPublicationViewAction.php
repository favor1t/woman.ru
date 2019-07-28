<?php

declare(strict_types = 1);

/**
 * экшен, отображающий AMP страницу публикации
 * Class AmpStarPersonPageAction
 */
class AmpPublicationViewAction extends ActionView
{

  public function execute()
  {
    return (new \Page\AmpPage())
      ->setMainBlock(new \Block\Amp\PublicationBlock());
  }


};


