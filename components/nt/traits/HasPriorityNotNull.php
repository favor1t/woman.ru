<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустым приоритетом"
 * Class HasPriorityNotNull
 */
trait HasPriorityNotNull
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
    if($priority === null) throw new \Exception('priority invalid: '.$priority);

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

