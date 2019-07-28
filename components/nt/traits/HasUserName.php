<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с именем юзера"
 * Class HasUserName
 */
trait HasUserName
{

  /** @var string $userName имя юзера */
	private $userName = null;


  /**
   * @param string $userName
   * @return $this
   * @throws \Exception
   */
	public function setUserName(string $userName) : self
	{
	  $this->userName = $userName;
	  return $this;
	}
  /**
   * @return string
   */
	public function getUserName() : string
	{
	  return $this->userName;
	}


};

