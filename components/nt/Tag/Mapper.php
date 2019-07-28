<?php

declare(strict_types = 1);

namespace nt\Tag;

/**
 * мэппер для работы с тегами
 * Class Mapper
 */
class Mapper
{
  use \nt\traits\MapperWithCache;

  /**
   * возвращает тег на основании ID или null
   * @param int $tagId
   * @return \nt\Tag | null
   */
  protected static function getByIdFromDbOrNull($tagId) : ?\nt\Tag
  {
    $array = \Db::fetchAsArray('
      select id, pid parent_id, name, webname, _extra, is_hidden_from_list, status
      from {{tags}}
      where id = :id
      limit 1',
      [ ':id' => $tagId, ]);
    if(! $array) return null;

    $arrExtra = json_decode($array['_extra'], $doArray = true);
    $array['is_hidden'] = (bool) (isset($arrExtra['is_hidden']) ? $arrExtra['is_hidden'] : false);
    unset($array['_extra']);

    $array['is_hidden_from_list'] = (bool) $array['is_hidden_from_list'];    

    return \nt\Tag::fromArray($array);
  }

};

