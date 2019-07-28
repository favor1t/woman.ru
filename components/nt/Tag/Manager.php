<?php

declare(strict_types = 1);

namespace nt\Tag;

/**
 * манагер для работы с тегами
 * Class Manager
 * @package nt\Tag
 */
class Manager extends \nt\Tag\Mapper
{

  /**
   * возвращает тег на основании web-названия
   * @param string $webName
   * @return \nt\Tag
   */
  public static function getByWebName(string $webName) : \nt\Tag
  {
    return \nt\Cache::get(__METHOD__, $webName, function() use ($webName)
    {
      $result = \Db::fetch('
        select id
        from {{tags}}
        where webname = :webname',
        [ ':webname' => $webName ]);
      if(! $result) throw new \Exception("can not get tag by webname: $webName");
      return self::getById($result->id);
    }, $expire = 60 * 60);
  }

};

