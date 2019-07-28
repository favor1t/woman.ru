<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с датой создания"
 * Class HasCreatedAt
 */
trait HasCreatedAt
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


  public function getCreatedAtW3C() : string
  {
    $time = strtotime($this->getCreatedAt());
    return date('Y-m-d\TH:i:sP', $time);
  }
  public function getCreatedAtRfc822() : string
  {
    $time = strtotime($this->getCreatedAt());
    return date('D, d M Y H:i:s O', $time);
  }
  

  public function getCreatedAtHuman() : string
  {
    $time = strtotime($this->getCreatedAt());
    return date('d.m.Y H:i', $time);
  }


};

