<?php

declare(strict_types = 1);

namespace nt;

/**
 * секция
 * Class Section
 */
class Section extends \nt\Section\SectionBase
{
  use
    \nt\traits\EntityGetById,
    \nt\traits\EntityFromArray,
    \nt\traits\EntitySaveInCache;


  /**
   * это главная секция?
   * @return bool
   */
  public function isMain() : bool
  {
    return $this->getId() == \Section::SECTION_INDEX;
  }
  public function isHoroscopeOrChild() : bool
  {
    return $this->getId() == \Section::SECTION_ID_HOROSCOPE || $this->getParentId() == \Section::SECTION_ID_HOROSCOPE;
  } 


  /**
   * создает объект на основании массива свойств и значений
   * @param array $array
   * @return \nt\Section
   */
  /*
  public static function fromArray(array $array)
  {
    return (new static())
      ->setId($array['id'])
      ->setParentId($array['pid'])
      ->setWebName($array['webname'])
      ->setName($array['name'])
      ->setIsSpecialProject($array['is_sproject']);
  }
  */


  /**
   * сахар
   * @return string
   */
  public function getNameLowered() : string
  {
    return mb_strtolower($this->getName());
  }
  /**
   * сахар
   * @return string
   */
  public function getTitle() : string
  {
    return $this->getName();
  }
  /**
   * сахар
   * @return string
   */
  public function getTitleLowered() : string
  {
    return $this->getNameLowered();
  }


  /**
   * @return int
   */
  public function getForumTopicCountReadable() : int
  {
    return \nt\Forum\Topic::getCount(
    [
      'visible_only'      => true,
      'section'           => $this,
      'use_section_child' => true,
    ]);
  }


  /**
   * возвращает URL секции
   * example: $suffix = 'forum'
   * @param string $suffix
   * @return string
   */
  public function getUrl(string $suffix = null) : string
  {
    $array = array_filter(
    [
      $this->hasParent() ? $this->getParent()->getWebName() : false,
      $this->getWebName(),
      $suffix,
    ]);
    return '/'.implode('/', $array).'/';
  }


  /**
   * обновляет данные в кеше в случае измнения модели на вумане
   * @param \BaseSection $sectionWoman
   */
  public static function onWomanModelChanged(\BaseSection $sectionWoman)
  {
    $sectionNt = self::getByIdFromCache((int) $sectionWoman->id);
    if(! $sectionNt) return;

    $sectionNt
      ->setParentId($sectionWoman->pid)
      ->setWebName($sectionWoman->webname)
      ->setName($sectionWoman->name)
      ->setIsSpecialProject($sectionWoman->getIsSpecialProject())
      ->saveInCache();
  }


  /**
   * возвращает ID секций-детей
   * @return int[]
   */
  public function getChildrenId() : array
  {
    return \nt\Section\Manager::getSectionIdByParent($this);
  }
  /**
   * возвращает ID секций-детей
   * @return int[]
   */
  public function getChildId() : array
  {
    return $this->getChildrenId();
  }
  /**
   * возвращает массив секций-детей
   * @return \nt\Section[]
   */
  public function getChild() : array
  {
    $arrSection = [];
    foreach($this->getChildId() as $sectionId)
    {
      $section = \nt\Section::getById($sectionId);
      $arrSection[$section->getId()] = $section;
    }
    return $arrSection;
  }


};

