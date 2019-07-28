<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то со списком ID секций"
 * копи-паста: HasSectionIdList, HasTagIdList
 * Trait HasSectionIdList
 */
trait HasSectionIdList
{

  /** @var int[] список ID секций */
  private $arrSectionId = [];


  /**
   * установка id секций
   * @param int[] | string
   * @return $this;
   */
  public function setSectionId($value) : self
  {
    if(is_string($value))
    {
      $value = trim($value, '{}');
      $value = $value == '' ? [] : explode(',', $value);
    }
    $this->arrSectionId = $value;
    return $this;
  }
  /**
   * возвращает массив ID секций
   * @return int[]
   */
  public function getSectionId() : array
  {
    return $this->arrSectionId;
  }


};

