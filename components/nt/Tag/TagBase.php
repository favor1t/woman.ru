<?php

declare(strict_types = 1);

namespace nt\Tag;

/**
 * базовый класс тега
 * Class TagBase
 * @package nt\Tag
 */
class TagBase
{
  use
    \nt\traits\HasIdNotZero,
    \nt\traits\HasParentId,
    \nt\traits\HasIsHidden,
    \nt\traits\HasNameNotEmpty,
    \nt\traits\HasStatusIntNotNull,
    \nt\traits\HasWebNameNotEmpty;

  /** @var bool */
  private $isHiddenFromList = null;


  /**
   * @param bool $isHiddenFromList
   * @return $this
   */
  public function setIsHiddenFromList(bool $isHiddenFromList) : self
  {
    $this->isHiddenFromList = $isHiddenFromList;
    return $this;
  }
  /**
   * @return bool
   */
  public function getIsHiddenFromList() : bool
  {
    return $this->isHiddenFromList;
  }


};



