<?php

declare(strict_types = 1);

namespace nt;

/**
 * наш кеш
 * Class Cache
 */
class Cache
{

  /** @var \MemcacheWithLog null наш кеш */
  private static $cache = null;


  /**
   * возвращает кеш
   * @return \MemcacheWithLog
   */
  private static function getCache() : \MemcacheWithLog
  {
    if(self::$cache === null)
    {
      $array = \Yii::app()->params['cache_model_simple'];
      self::$cache = new \MemcacheWithLog($array['host'], $array['port']);
    }
    return self::$cache;
  }


  /**
   * @TODO: почему в $onCacheMiss передается только $entityId?
   * считывает и возвращает значение из кеша
   * @param string $entityName
   * @param string | int | null $entityId
   * @param callable $onCacheMiss | null
   * @param int | null $expire
   * @return bool | string | array | \nt\Forum\Topic | \nt\Section
   */
  public static function get(string $entityName, $entityId = null, callable $onCacheMiss = null, int $expire = null)
  {
    $cache    = self::getCache();
    $cacheKey = self::createCacheKey($entityName, $entityId);
    $result   = $cache->get($cacheKey);
    if($result === false && $onCacheMiss)
    {
      $result = $onCacheMiss($entityId);
      $cache->set($cacheKey, $result, $expire);
    }
    return $result;
  }


  /**
   * запись сущности в кеш
   * @param string $entityName
   * @param string | int | null $entityId
   * @param $entity
   * @return mixed
   */
  public static function set(string $entityName, $entityId = null, $entity, int $expire = null)
  {
    $cacheKey = self::createCacheKey($entityName, $entityId);
    return self::getCache()->set($cacheKey, $entity, $expire);
  }


  /**
   * возвращает ключ кеша
   * @param string $entityName
   * @param string | int $entityId
   * @return string
   */
  private static function createCacheKey(string $entityName, $entityId)
  {
    $cacheKey = $entityName.'~'.$entityId;
    return strlen($cacheKey) < 250 ? $cacheKey : md5($cacheKey);
  }


  /**
   * очистка кеша
   * @return bool
   */
  public static function flush()
  {
    return self::getCache()->flush();
  }


  /**
   * удаляет значение из кеша
   * @param string $entityName
   * @param string | int $entityId
   * @return bool
   */
  public static function delete(string $entityName, $entityId)
  {
    $cacheKey = self::createCacheKey($entityName, $entityId);
    return self::getCache()->delete($cacheKey);
  }


};


