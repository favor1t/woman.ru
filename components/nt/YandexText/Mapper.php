<?php

declare(strict_types = 1);

namespace nt\YandexText;

/**
 * мэппер для работы с текстами яндекса
 * Class Mapper
 */
class Mapper
{

  public static function save(\nt\YandexText $yandexText) : \nt\YandexText
  {
    $method = $yandexText->getId() ? 'update' : 'insert';
    [ '\nt\YandexText\Mapper', $method ]($yandexText);
    return $yandexText;
  }


  private static function insert(\nt\YandexText $yandexText)
  {
    assert(! $yandexText->getId());

    \Db::execute('
      insert into {{queue_yandex_text}} (created_at, priority, status, status_set_at, entity_type_id, entity_id, text, yandex_error_code, yandex_text_id)
      values (:created_at, :priority, :status, :status_set_at, :entity_type_id, :entity_id, :text, :yandex_error_code, :yandex_text_id)',
      [
        ':created_at'        => $yandexText->getCreatedAt(),
        ':priority'          => $yandexText->getPriority(),
        ':status'            => $yandexText->getStatus(),
        ':status_set_at'     => $yandexText->getStatusSetAt(),
        ':entity_type_id'    => $yandexText->getEntityTypeId(),
        ':entity_id'         => $yandexText->getEntityId(),
        ':text'              => $yandexText->getText(),
        ':yandex_error_code' => $yandexText->getYandexErrorCode(),
        ':yandex_text_id'    => $yandexText->getYandexTextId(),
      ]);
  }
  private static function update(\nt\YandexText $yandexText)
  {
    assert($yandexText->getId());

    \Db::execute('
      update {{queue_yandex_text}} set
        created_at        = :created_at, 
        priority          = :priority, 
        status            = :status, 
        status_set_at     = :status_set_at, 
        entity_type_id    = :entity_type_id, 
        entity_id         = :entity_id, 
        text              = :text,
        yandex_error_code = :yandex_error_code, 
        yandex_text_id    = :yandex_text_id 
      where id = :id',
      [
        ':created_at'        => $yandexText->getCreatedAt(),
        ':priority'          => $yandexText->getPriority(),
        ':status'            => $yandexText->getStatus(),
        ':status_set_at'     => $yandexText->getStatusSetAt(),
        ':entity_type_id'    => $yandexText->getEntityTypeId(),
        ':entity_id'         => $yandexText->getEntityId(),
        ':text'              => $yandexText->getText(),
        ':id'                => $yandexText->getId(),
        ':yandex_error_code' => $yandexText->getYandexErrorCode(),
        ':yandex_text_id'    => $yandexText->getYandexTextId(),
      ]);
  }

};

