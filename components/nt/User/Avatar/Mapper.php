<?php

declare(strict_types = 1);

namespace nt\User\Avatar;

/**
 * мэппер для работы с аватарками юзеров
 * Class Mapper
 */
class Mapper
{
  use \nt\traits\MapperWithCache;

  /**
   * возвращает аватарку юзера на основании ID или null
   * @param int $avatarId
   * @return \nt\User\Avatar | null
   */
  protected static function getByIdFromDbOrNull(int $avatarId) : ?\nt\User\Avatar
  {
    $array = \Db::fetchAsArray('
      select id, _extra
      from {{userpics}}
      where id = :id
      limit 1',
      [ ':id' => $avatarId, ]);
    if(! $array) return null;

    $arrExtra = json_decode($array['_extra'], $doArray = true);
    $array['url_image'] = isset($arrExtra['image']) ? $arrExtra['image'] : null;

    return \nt\User\Avatar::fromArray($array);
  }

};

