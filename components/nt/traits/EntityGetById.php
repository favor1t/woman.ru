<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * сахар для вызовов методов манагера сущности
 * Trait EntityGetById
 */
trait EntityGetById
{

  /**
   * возвращает сущность по ID
   * @param int $entityId
   * @return \nt\Forum\Topic | \nt\Section | \nt\User | \nt\User\Avatar
   */
  public static function getById(int $entityId)
  {
    // @TODO: интересно, насколько быстро это работает
    return call_user_func([ get_called_class().'\Manager', 'getById', ], $entityId);
  }


  /**
   * возвращает сущность по ID
   * @param int $entityId
   * @return \nt\Forum\Topic | \nt\Section | \nt\User | \nt\User\Avatar | null
   */
  public static function getByIdOrNull(int $entityId)
  {
    // @TODO: интересно, насколько быстро это работает
    return call_user_func([ get_called_class().'\Manager', 'getByIdOrNull', ], $entityId);
  }



  /**
   * возвращает сущность по ID из кеша
   * @param int $entityId
   * @return \nt\Forum\Topic | \nt\Section | \nt\User | \nt\User\Avatar | false
   */
  public static function getByIdFromCache(int $entityId)
  {
    // @TODO: интересно, насколько быстро это работает
    return call_user_func([ get_called_class().'\Manager', 'getByIdFromCache', ], $entityId);
  }


  /**
   * сносит сущность из кеша по ID
   * @param int $entityId
   * @return bool
   */
  public static function flushCacheById(int $entityId)
  {
    // @TODO: интересно, насколько быстро это работает
    return call_user_func([ get_called_class().'\Manager', 'flushCacheById', ], $entityId);
  }

};


