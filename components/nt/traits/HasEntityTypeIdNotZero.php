<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустым цифровым типом сущности"
 * Class HasEntityTypeIdNotZero
 */
trait HasEntityTypeIdNotZero
{

  /** @var int $entityTypeId */
  private $entityTypeId = null;


  /**
   * устанавливает $this->entityTypeId
   * @param int $entityTypeId
   * @return $this
   * @throws \Exception
   */
  public function setEntityTypeId(int $entityTypeId) : self
  {
    if(! $entityTypeId) throw new \Exception('entity type id invalid: '.$entityTypeId);

    $this->entityTypeId = $entityTypeId;
    return $this;
  }


  /**
   * возвращает $this->entityTypeId
   * @return int
   */
  public function getEntityTypeId() : int
  {
    return $this->entityTypeId;
  }


};

