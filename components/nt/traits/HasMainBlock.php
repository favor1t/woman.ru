<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с главным блоком"
 */
trait HasMainBlock
{

  /** @var \BlockBase */
  private $mainBlock = null;


  /**
   * установка главного блока страницы
   * @param \BlockBase $block
   * @return $this
   */
  public function setMainBlock(\BlockBase $block) : self
  {
    $this->mainBlock = $block;
    return $this;
  }
  public function getMainBlock() : \BlockBase
  {
    return $this->mainBlock;
  }


  /**
   * отображение блока
   * @return self
   */
  public function displayMainBlock() : self
  {
    $this->getMainBlock()->display();
    return $this;
  }


};

