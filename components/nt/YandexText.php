<?php

declare(strict_types = 1);

namespace nt;

/**
 * #523: тексты для яндекса
 * без понимания, как и куда оно будет развиваццо - сделано так, как сделано
 * по-хорошему нынешняя реализация должна быть разобрана на абстрактный YandexText и YandexTextByPublication
 * Class YandexText
 */
class YandexText extends YandexText\YandexTextBase
{
  use \nt\traits\EntitySave;

  const ENTITY_TYPE_PUBLICATION = 1;
  const ENTITY_TYPE_STAR_PERSON = 2;

  const STATUS_IDLE             = 1;    // для очереди в БД
  const STATUS_SENDING          = 2;
  const STATUS_ERROR            = 3;
  const STATUS_SUCCESS          = 4;

  const PRIORITY_DEFAULT        = 50;   // для очереди в БД

  const YANDEX_ERROR_CODE_QUOTA_EXCEEDED = 'QUOTA_EXCEEDED';

  /**
   * @param \Publication $publication
   * @return \nt\YandexText
   * @throws \Exception
   */
  public static function createByPublication(\Publication $publication) : self
  {
    if($publication->yandex_uid != '') throw new \Exception('yandex_uid already defined: '.$publication->yandex_uid);

    // если есть неотправленный текст - снесем
    // \nt\YandexText\Manager::deleteUnsendedForPublication($publication);

    // создадим новую
    return self::createEmpty()
      ->setEntityTypeId(self::ENTITY_TYPE_PUBLICATION)
      ->setEntityId((int) $publication->id)
      ->setText('');
      // moved to onBeforeSend
      //->setText($publication->getTextForYandex())
      //->save();
  }



  /**
   * @return \nt\YandexText
   */
  public static function createEmpty() : self
  {
    return (new static())
      ->setCreatedAtAsNow()
      ->setPriority(self::PRIORITY_DEFAULT)
      ->setStatus(self::STATUS_IDLE)
      ->setStatusSetAtAsNow()
      ->setYandexErrorCode('')
      ->setYandexTextId('');
  }


  /**
   * @param array $array
   * @return \nt\YandexText
   */
  public static function fromArray(array $array) : self
  {
    return (new static())
      ->setId($array['id'])
      ->setCreatedAt($array['created_at'])
      ->setPriority($array['priority'])
      ->setStatus($array['status'])
      ->setStatusSetAt($array['status_set_at'])
      ->setEntityTypeId($array['entity_type_id'])
      ->setEntityId($array['entity_id'])
      ->setText($array['text'])
      ->setYandexErrorCode($array['yandex_error_code'])
      ->setYandexTextId($array['yandex_text_id']);
  }


  /**
   * @param int $limit
   * @return \nt\YandexText[]
   */
  public static function getForSend(int $limit) : array
  {
    return \nt\YandexText\Manager::getForSend($limit);
  }


  public function setPriorityMinimal() : self
  {
    return $this->setPriority(0);
  }


  public function onBeforeSend() : self
  {
    // в случае серьезных изменений: см описание класса; надо расползаццо на несколько классов
    if($this->getEntityTypeId() == self::ENTITY_TYPE_PUBLICATION)
    {
      $publication = \Publication::model()->findByPk($this->getEntityId());
      $this->setText($publication->getTextForYandex());
    }

    if($this->getEntityTypeId() == self::ENTITY_TYPE_STAR_PERSON)
    {
      $tag = new \Tag();
      $tag->id = $this->getEntityId();

      $textArray = \StarPerson::getByTagOrNull($tag)->getHtmlFull();
      $this->setText(\StarPerson::getArrayAsString($textArray));
    }

    return $this
      ->setStatus(self::STATUS_SENDING)
      ->setStatusSetAtAsNow()
      ->save();
  }


  public function onYandexError(string $yandexErrorCode) : self
  {
    return $this
      ->setStatus(self::STATUS_ERROR)
      ->setStatusSetAtAsNow()
      ->setYandexErrorCode($yandexErrorCode)
      ->save();
  }


  public function onYandexSuccess(string $yandexTextId) : self
  {
    // в случае серьезных изменений: см описание класса; надо расползаццо на несколько классов
    if($this->getEntityTypeId() == self::ENTITY_TYPE_PUBLICATION)
    {
      \Publication::model()->findByPk($this->getEntityId())
        ->setYandexTextId($yandexTextId)
        ->save();
    }

    return $this
      ->setStatus(self::STATUS_SUCCESS)
      ->setStatusSetAtAsNow()
      ->setYandexTextId($yandexTextId)
      ->save();
  }

};



