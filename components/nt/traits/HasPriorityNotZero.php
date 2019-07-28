<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустым приоритетом"
 * Class HasPriorityNotZero
 */
trait HasPriorityNotZero
{

  /** @var int $priority */
  private $priority = null;


  /**
   * устанавливает $this->priority
   * @param int $priority
   * @return $this
   * @throws \Exception
   */
  public function setPriority(int $priority) : self
  {
    if(! $priority) throw new \Exception('priority invalid: '.$priority);

    $this->priority = $priority;
    return $this;
  }


  /**
   * возвращает $this->priority
   * @return int
   */
  public function getPriority() : int
  {
    return $this->priority;
  }


};

