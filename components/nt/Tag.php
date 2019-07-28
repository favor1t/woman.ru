<?php

declare(strict_types = 1);

namespace nt;

/**
 * тег
 * Class Tag
 * @package nt
 */
class Tag extends \nt\Tag\TagBase
{
  use
    \nt\traits\EntityGetById,
    \nt\traits\EntityFromArray,
    \nt\traits\EntitySaveInCache;


  /**
   * создает объект на основании массива свойств и значений
   * @param array $array
   * @return \nt\Tag
   */
  /*
  public static function fromArray(array $array)
  {
    return (new static())
      ->setId($array['id'])
      ->setParentId($array['pid'])
      ->setName($array['name'])
      ->setWebName($array['webname'])
      ->setIsHidden($array['is_hidden'])
      ->setStatus($array['status'])
      ->setIsHiddenFromList($array['is_hidden_from_list']);
  }
  */


  /**
   * обновляет данные в кеше в случае измнения модели на вумане
   * @param \Tag $tagWoman
   */
  public static function onWomanModelChanged(\Tag $tagWoman)
  {
    $cacheKey = self::getCacheKeySiteUrlDefaultByTagId((int) $tagWoman->id);
    \nt\Cache::set($cacheKey, $entityId = null, $tagWoman->getSiteUrl());

    $tagNt = self::getByIdFromCache((int) $tagWoman->id);
    if(! $tagNt) return;
    if(empty($tagWoman->pid) || $tagWoman->pid < 0) $tagWoman->pid = null;
    $tagNt
      ->setParentId($tagWoman->pid)
      ->setName($tagWoman->name)
      ->setWebName($tagWoman->webname)
      ->setIsHidden((bool)$tagWoman->is_hidden)
      ->setStatus((int)$tagWoman->status)
      ->setIsHiddenFromList((bool)$tagWoman->is_hidden_from_list)
      ->saveInCache();
  }


  /**
   * возвращает URL тега
   * @param bool $absolute
   * @param array $arrParam
   * @return string
   */
  public function getSiteUrl(bool $absolute = false, array $arrParam = []) : string
  {
    // кешируем дефолтное получение URL
    // @TODO: пертащить логику получения урла сюда
    $needCache = ! $absolute && ! count($arrParam);
    if($needCache)
    {
      $cacheKey = self::getCacheKeySiteUrlDefaultByTagId($this->getId());
      if($url = \nt\Cache::get($cacheKey)) return $url;
    }

    $url = \Tag::model()->findByPk($this->getId())->getSiteUrl($absolute, $arrParam);
    if($needCache) \nt\Cache::set($cacheKey, $entityId = null, $url);
    return $url;
  }


  /**
   * возвращает тег на основании web-названия
   * @param string $webName
   * @return \nt\Tag
   */
  public static function getByWebName(string $webName) : self
  {
    return \nt\Tag\Manager::getByWebName($webName);
  }


  /**
   * @param int $tagId
   * @return string
   */
  private static function getCacheKeySiteUrlDefaultByTagId(int $tagId) : string
  {
    return 'tag_site_url'.'~'.$tagId;
  }


  /**
   * сахар к статусу
   * @return bool
   */
  public function isStatusOff() : bool
  {
    return $this->getStatus() == \Tag::STATUS_OFF;
  }


};

