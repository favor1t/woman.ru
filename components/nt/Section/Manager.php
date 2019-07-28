<?php

declare(strict_types = 1);

namespace nt\Section;

/**
 * манагер для работы с секциями
 * Class Manager
 */
class Manager extends \nt\Section\Mapper
{

  /**
   * возвращает ID секций-детей
   * @return int[]
   */
  public static function getSectionIdByParent(\nt\Section $section) : array
  {
    $array = \Db::fetchAllAsArray('
      select id
      from {{sections}}
      where pid = :pid',
      [ ':pid' => $section->getId(), ]);
    return array_map(function(array $array) : int
    {
      return $array['id'];
    }, $array);
  }


};

