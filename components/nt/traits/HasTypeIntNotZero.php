<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с типом" + проверка, что тип больше нуля
 * Class HasTypeIntNotZero
 */
trait HasTypeIntNotZero
{

  /** @var int $type */
  private $type = null;


  /**
   * устанавливает $this->type
   * @param int $type
   * @return $this
   * @throws \Exception
   */
  public function setType(int $type) : self
  {
    if($type < 1) throw new \Exception('type invalid: '.$type);

    $this->type = $type;
    return $this;
  }


  /**
   * возвращает $this->type
   * @return int
   */
  public function getType() : int
  {
    return $this->type;
  }


};

