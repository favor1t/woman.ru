<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с ID" + проверка, что ID больше нуля
 * Class HasIdNotZeroNullable
 */
trait HasIdNotZeroNullable
{

  /** @var int $id */
  private $id = null;


  /**
   * устанавливает $this->id
   * @param int $id
   * @return $this
   * @throws \Exception
   */
  public function setId(int $id) : self
  {
    if($id < 1) throw new \Exception('id invalid: '.$id);

    $this->id = $id;
    return $this;
  }


  /**
   * возвращает $this->id
   * @return int | null
   */
  public function getId() : ?int
  {
    return $this->id;
  }


};

