<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с IP юзера"
 * Class HasUserIp
 */
trait HasUserIp
{

  /** @var string $userIp ID юзера */
	private $userIp = null;


  /**
   * @param string $userIp
   * @return $this
   * @throws \Exception
   */
	public function setUserIp(string $userIp) : self
	{
	  $this->userIp = $userIp;
	  return $this;
	}
  /**
   * @return string
   */
	public function getUserIp() : string
	{
	  return $this->userIp;
	}


};

