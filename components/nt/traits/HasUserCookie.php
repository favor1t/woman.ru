<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с кукой юзера"
 * Class HasUserCookie
 */
trait HasUserCookie
{

  /** @var string $userCookie ID юзера */
	private $userCookie = null;


  /**
   * @param string $userCookie
   * @return $this
   * @throws \Exception
   */
	public function setUserCookie(string $userCookie) : self
	{
	  $this->userCookie = $userCookie;
	  return $this;
	}
  /**
   * @return string
   */
	public function getUserCookie() : string
	{
	  return $this->userCookie;
	}


};

