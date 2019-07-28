<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то со списком ID тегов"
 * копи-паста: HasSectionIdList, HasTagIdList
 * Trait HasTagIdList
 */
trait HasTagIdList
{

  /** @var int[] список ID тегов */
  private $arrTagId = [];


  /**
   * установка id тегов
   * @param int[] | string
   * @return $this
   */
  public function setTagId($value) : self
  {
    if($value === null)   $value = [];
    if(is_string($value))
    {
      $value = trim($value, '{}');
      $value = $value == '' ? [] : explode(',', $value);
    }
    $this->arrTagId = $value;
    return $this;
  }
  /**
   * возвращает массив ID секций
   * @return int[]
   */
  public function getTagId() : array
  {
    return $this->arrTagId;
  }


  /**
   * тег с таким ID существует?
   * @param int $tagId
   * @return bool
   */
  public function hasTagId(int $tagId) : bool
  {
    return in_array($tagId, $this->getTagId());
  }


  /**
   * возвращает теги
   * @param bool $skipUnExists
   * @return \nt\Tag[]
   */
  public function getTag(bool $skipUnExists = false) : array
  {
    $arrTag = array_map(function($tagId) use ($skipUnExists)
    {
      if(! is_numeric($tagId)) throw new Exception('tag id not a numeric: '.$tagId);
      if($tagId < 1) throw new Exception('tag id invalid: '.$tagId);

      $tag = \nt\Tag::getByIdOrNull((int) $tagId);
      if($tag || $skipUnExists) return $tag;
      throw new \Exception('can not get tag by id: '.$tagId);
    }, $this->getTagId());

    return array_filter($arrTag);
  }



};

