<?php

declare(strict_types = 1);

namespace Block\Amp;

/**
 * AMP блок, отображающий информацию о персоне
 */
class StarPersonBlock extends \BlockBase
{
  use
    \nt\traits\HasStarPerson;


  /**
   * инициализация блока
   * @return StarPersonBlock
   */
  public function init() : self
  {
    $tag = $_GET['tag'] ?? '';
    if($tag == '') $this->redirect();
    $tag = \nt\Tag::getByWebName($tag);

    $starPerson = \StarPerson::getByTagOrNull($tag);
    if(! $starPerson) $this->redirect();
    $this->setStarPerson($starPerson);

    // можно через массив
    $this->addView('IntroBlock',           (new \Block\Amp\StarPerson\IntroBlock())->setStarPerson($starPerson));
    $this->addView('GalleryBlock',         (new \Block\Amp\StarPerson\GalleryBlock())->setStarPerson($starPerson));
    $this->addView('HtmlContentBlock',     (new \Block\Amp\StarPerson\HtmlContentBlock())->setStarPerson($starPerson));
    $this->addView('PublicationListBlock', (new \Block\Amp\StarPerson\PublicationListBlock())->setStarPerson($starPerson));

    return parent::init();
  }


  private function redirect()
  {
    \Yii::app()->controller->redirect(\Yii::app()->params['baseUrl'].$_SERVER['REQUEST_URI'], $terminate = true);
  }


  /**
   * возвращает титл страницы
   * @return string
   */
  public function getPageTitle() : string
  {
    return $this->getStarPerson()->getPageTitle();
  }


};

