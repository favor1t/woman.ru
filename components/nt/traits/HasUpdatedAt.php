<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с датой изменения"
 * Class HasUpdatedAt
 */
trait HasUpdatedAt
{

  /** @var string $updatedAt дата изменения */
	private $updatedAt = null;


  /**
   * @param string $updatedAt
   * @return $this
   * @throws \Exception
   */
  //public function setUpdatedAt(string $updatedAt) : self
	public function setUpdatedAt($updatedAt) : self
	{
	  $this->updatedAt = $updatedAt;
	  return $this;
	}
  /**
   * @return string
   */
  //public function getUpdatedAt() : string
	public function getUpdatedAt()
	{
	  return $this->updatedAt;
	}


  /**
   * возвращает дату изменения в виде метки времени unix
   * @return int
   */
	public function getUpdatedAtAsTimestamp() : int
  {
    return strtotime($this->getUpdatedAt());
  }


};

