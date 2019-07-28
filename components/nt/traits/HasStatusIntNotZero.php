<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустым цифровым статусом"
 * Class HasStatusIntNotZero
 */
trait HasStatusIntNotZero
{

  /** @var int $status */
  private $status = null;


  /**
   * устанавливает $this->status
   * @param int $status
   * @return $this
   * @throws \Exception
   */
  public function setStatus(int $status) : self
  {
    if(! $status) throw new \Exception('status invalid: '.$status);

    $this->status = $status;
    return $this;
  }


  /**
   * возвращает $this->status
   * @return int
   */
  public function getStatus() : int
  {
    return $this->status;
  }


};

