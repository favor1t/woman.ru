<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с идентификатором браузера юзера"
 * Class HasUserAgent
 */
trait HasUserAgent
{

  /** @var string $userAgent ID юзера */
	private $userAgent = null;


  /**
   * @param string $userAgent
   * @return $this
   * @throws \Exception
   */
	public function setUserAgent(string $userAgent) : self
	{
	  $this->userAgent = $userAgent;
	  return $this;
	}
  /**
   * @return string
   */
	public function getUserAgent() : string
	{
	  return $this->userAgent;
	}


};

