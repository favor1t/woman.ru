<?php

declare(strict_types = 1);

namespace nt\Section;

/**
 * базовый класс секции
 * Class SectionBase
 */
class SectionBase
{
  use
    \nt\traits\HasIdNotZero,
    \nt\traits\HasParentId,
    \nt\traits\HasWebNameNotEmpty,
    \nt\traits\HasNameNotEmpty;


  /** @var bool эта секция - спецпроект? */
  private $isSpecialProject = null;


  /**
   * @param bool $isSpecialProject
   * @return $this
   */
  public function setIsSpecialProject(bool $isSpecialProject) : self
  {
    $this->isSpecialProject = $isSpecialProject;
    return $this;
  }
  /**
   * @return bool
   */
  public function getIsSpecialProject() : bool
  {
    return $this->isSpecialProject;
  }



};



