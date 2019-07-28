<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустым именем"
 * Class HasNameNotEmpty
 */
trait HasNameNotEmpty
{

  /** @var string $name имя */
	private $name = null;


  /**
   * @param string $name
   * @return $this
   * @throws \Exception
   */
	public function setName(string $name) : self
	{
	  if($name == '') throw new \Exception('name is empty');

	  $this->name = $name;
	  return $this;
	}
  /**
   * @return string
   */
	public function getName() : string
	{
	  return $this->name;
	}


};

