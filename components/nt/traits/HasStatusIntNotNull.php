<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то со статусом"
 * статус может быть 0, но не null
 * Class HasStatusIntNotNull
 */
trait HasStatusIntNotNull
{

  /** @var int $status статус */
	private $status = null;


  /**
   * @param int $status
   * @return $this
   * @throws \Exception
   */
	public function setStatus(int $status) : self
	{
	  $this->status = $status;
	  return $this;
	}
  /**
   * @return int
   */
	public function getStatus() : int
	{
	  return $this->status;
	}


};

