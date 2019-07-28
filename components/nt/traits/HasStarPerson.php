<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то со звездной персоной"
 * Class HasStarPerson
 */
trait HasStarPerson
{

  /** @var \StarPerson | null $starPerson титл */
	private $starPerson = null;


  /**
   * @param StarPerson $starPerson
   * @return $this
   * @throws \Exception
   */
	public function setStarPerson(\StarPerson $starPerson) : self
	{
	  $this->starPerson = $starPerson;
	  return $this;
	}
  /**
   * @return StarPerson | null
   */
	public function getStarPerson() : ?\StarPerson
	{
	  return $this->starPerson;
	}


};

