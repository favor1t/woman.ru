<?php

declare(strict_types = 1);

namespace nt\User;

/**
 * мэппер для работы с юзерами
 * Class Mapper
 */
class Mapper
{
  use \nt\traits\MapperWithCache;

  /**
   * возвращает юзера на основании ID или null
   * @param int $userId
   * @return \nt\User | null
   */
  protected static function getByIdFromDbOrNull(int $userId) : ?\nt\User
  {
    $array = \Db::fetchAsArray('
      select id, name, userpic_id user_avatar_id, _extra
      from {{users}}
      where id = :id
      limit 1',
      [ ':id' => $userId, ]);
    if(! $array) return null;

    $arrExtra = json_decode($array['_extra'], $doArray = true);
    unset($array['_extra']);

    $url = null;
    if(isset($arrExtra['userpic_small']['bfsImage']))                              $url = $arrExtra['userpic_small']['bfsImage'];
    if(isset($arrExtra['userpic_small']) && is_string($arrExtra['userpic_small'])) $url = $arrExtra['userpic_small'];
    $array['url_user_image_small'] = (string) $url;

    // cast to need type
    $array['user_avatar_id'] = (int) $array['user_avatar_id'];

    return \nt\User::fromArray($array);
  }

};

