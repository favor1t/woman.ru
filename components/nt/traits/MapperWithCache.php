<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт для реализации кеширования мэпперов
 * Trait MapperWithCache
 */
trait MapperWithCache
{

  /**
   * возвращает сущность по ID из кеша или false
   * применяется для изменения моделей в кеше при инвалидации соответствующих моделей вумана
   * @param int $entityId
   * @return \nt\Forum\Topic | \nt\Section | \nt\User | false
   */
  public static function getByIdFromCache(int $entityId)
  {
    return \nt\Cache::get(static::class, $entityId);
  }


  /**
   * возвращает сущность по ID или null
   * @param int $entityId
   * @return \nt\Forum\Topic | \nt\Section | \nt\User | null
   */
  public static function getByIdOrNull(int $entityId)
  {
    return \nt\Cache::get(static::class, $entityId, function(int $entityId)
    {
      return self::getByIdFromDbOrNull($entityId);
    });
  }


  /**
   * возвращает сущность по ID или выбрасывает исключение
   * @param int $entityId
   * @return \nt\Forum\Topic | \nt\Section | \nt\User
   * @throws \Exception
   */
  public static function getById($entityId)
  {
    $entity = self::getByIdOrNull($entityId);
    if($entity !== null) return $entity;
    \ErrorLogHelper::createByMessage('can not get entity '.static::class.' by id: '.print_r($entityId, true));
    throw new \Exception('can not get entity '.static::class.' by id: '.$entityId);
  }


  /**
   * запихивает сущность в кеш
   * @param $entity
   */
  public static function saveInCache($entity)
  {
    \nt\Cache::set(static::class, $entity->getId(), $entity);
    return $entity;
  }


  /**
   * сносит сущность из кеша по ID
   * @param int $entityId
   * @return bool
   */
  public static function flushCacheById(int $entityId)
  {
    return \nt\Cache::delete(static::class, $entityId);
  }

};

