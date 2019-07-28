<?php

declare(strict_types = 1);

namespace Block\Amp\StarPerson;

/**
 * AMP блок, отображающий информацию о персоне
 */
class IntroBlock extends \BlockBase
{

  use
    \nt\traits\HasStarPerson;

  /**
   * @var null|array
   */

  public function init()
  {
    $tag = \nt\Tag::getByWebName($_GET['tag']);
    $this->starPerson = \StarPerson::getByTag($tag);

    return parent::init();
  }


};
