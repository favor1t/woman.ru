<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустой датой создания"
 * Class HasCreatedAtNotZero
 */
trait HasCreatedAtNotZero
{

  /** @var string $createdAt дата создания */
	private $createdAt = null;


  /**
   * @param string $createdAt
   * @return $this
   * @throws \Exception
   */
	public function setCreatedAt(string $createdAt) : self
	{
    if($createdAt === null || $createdAt == '0000-00-00 00:00:00') throw new \Exception('created at invalid: '.$createdAt);

	  $this->createdAt = $createdAt;
	  return $this;
	}
  /**
   * @return string
   */
	public function getCreatedAt() : string
	{
	  return $this->createdAt;
	}


  /**
   * возвращает дату создания в виде метки времени unix
   * @return int
   */
	public function getCreatedAtAsTimestamp() : int
  {
    return strtotime($this->getCreatedAt());
  }


  /**
   * @return self
   * @throws \Exception
   */
  public function setCreatedAtAsNow() : self
  {
    return $this->setCreatedAt(date('Y-m-d H:i:s'));
  }


};

