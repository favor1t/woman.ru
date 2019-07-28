<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустой датой установки статуса"
 * Class HasStatusSetAtNotZero
 */
trait HasStatusSetAtNotZero
{

  /** @var string $statusSetAt дата установки статуса */
	private $statusSetAt = null;


  /**
   * @param string $statusSetAt
   * @return $this
   * @throws \Exception
   */
	public function setStatusSetAt(string $statusSetAt) : self
	{
    if($statusSetAt === null || $statusSetAt == '0000-00-00 00:00:00') throw new \Exception('status set at invalid: '.$statusSetAt);

	  $this->statusSetAt = $statusSetAt;
	  return $this;
	}
  /**
   * @return string
   */
	public function getStatusSetAt() : string
	{
	  return $this->statusSetAt;
	}


  /**
   * возвращает дату создания в виде метки времени unix
   * @return int
   */
	public function getStatusSetAtAsTimestamp() : int
  {
    return strtotime($this->getStatusSetAt());
  }


  /**
   * @return self
   * @throws \Exception
   */
  public function setStatusSetAtAsNow() : self
  {
    return $this->setStatusSetAt(date('Y-m-d H:i:s'));
  }


};

