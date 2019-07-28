<?php

declare(strict_types = 1);

namespace Page;

/**
 * AMP страница
 */
class AmpPage extends \PageBase
{

  use
    \nt\traits\HasMainBlock,
    \nt\traits\HasUrlCanonicalNotEmpty;


  public function init()
  {
    $this->getMainBlock()->init();      

    $this
      ->addView('SidebarBlock', new \Block\Amp\Topic\SidebarBlock())
      ->addView('Header', new \Block\Amp\Header())
      ->addView('Footer', new \Block\Amp\Footer());

    return parent::init();
  }  


};

