<?php
declare(strict_types = 1);

class AmpTopicViewAction extends ActionView
{

  public function execute()
  {
    return (new \Page\AmpPage())
      ->setMainBlock(new \Block\Amp\TopicBlock())
      ->setUrlCanonical(UrlHelper::getUrlCanonical());
  }


};