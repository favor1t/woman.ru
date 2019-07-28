<?php

declare(strict_types = 1);

namespace nt\YandexText;

/**
 * манагер для работы с текстами яндекса
 * Class Manager
 */
class Manager extends \nt\YandexText\Mapper
{

  /**
   * @param \Publication $publication
   * @return int rowCount
   */
  /*
  public static function deleteUnsendedForPublication(\Publication $publication) : int
  {
    return \Db::execute('
      delete from {{queue_yandex_text}}
      where status = :status and entity_type_id = :entity_type_id and entity_id = :entity_id',
      [
        ':status'         => \nt\YandexText::STATUS_IDLE,
        ':entity_type_id' => \nt\YandexText::ENTITY_TYPE_PUBLICATION,
        ':entity_id'      => $publication->id,
      ]);
  }
  */


  /**
   * @param int $limit
   * @return \nt\YandexText[]
   */
  public static function getForSend(int $limit) : array
  {
    $array = \Db::fetchAllAsArray('
      select id, created_at, priority, status, status_set_at, entity_type_id, entity_id, text, yandex_error_code, yandex_text_id
      from {{queue_yandex_text}}
      where status = :status_idle or (status = :status_error and yandex_error_code = :yandex_error_code)
      order by priority desc, created_at
      limit '.$limit,
      [ 
        ':status_idle'       => \nt\YandexText::STATUS_IDLE, 
        ':status_error'      => \nt\YandexText::STATUS_ERROR, 
        ':yandex_error_code' => \nt\YandexText::YANDEX_ERROR_CODE_QUOTA_EXCEEDED,
      ]);
    return array_map(function(array $array) : \nt\YandexText
    {
      return \nt\YandexText::fromArray($array);
    }, $array);
  }




};

