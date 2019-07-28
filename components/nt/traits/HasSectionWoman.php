<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт, релизующий стандартную вумановскую логику для работы с секцией сущности
 * Trait HasSectionWoman
 */
trait HasSectionWoman
{
  use \nt\traits\HasSectionIdList;

  /**
   * возвращает родителя основной секции сущности ($this->subSection->parent)
   * @return \nt\Section
   */
  public function getSectionMain() : \nt\Section
  {
    return $this->getSubSection()->getParent();
  }
  /**
   * возвращает основную секцию сущности ($this->subSection)
   * @return \nt\Section
   */
  public function getSubSection() : \nt\Section
  {
    $sectionId = $this->getSectionId()[0];
    if(! is_numeric($sectionId)) throw new \Exception('section id is not a numeric: '.$sectionId);
    if($sectionId < 1) throw new \Exception('section id invalid: '.$sectionId);

    return \nt\Section::getById((int) $sectionId);
  }

};


