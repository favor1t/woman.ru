<?php

declare(strict_types = 1);

namespace nt\Section;

/**
 * мэппер для работы с секциями
 * Class Mapper
 */
class Mapper
{
  use \nt\traits\MapperWithCache;

  /**
   * возвращает секцию на основании ID или null
   * @param int $sectionId
   * @return \nt\Section | null
   */
  protected static function getByIdFromDbOrNull($sectionId) : ?\nt\Section
  {
    $array = \Db::fetchAsArray('
      select id, pid parent_id, name, webname, is_sproject is_special_project
      from {{sections}}
      where id = :id
      limit 1',
      [ ':id' => $sectionId, ]);

    // cast to need type
    $array['is_special_project'] = (bool) $array['is_special_project'];

    return $array ? \nt\Section::fromArray($array) : null;
  }

};

