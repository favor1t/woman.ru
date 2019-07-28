<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с ID сущности"
 * Class HasEntityId
 */
trait HasEntityId
{

  /** @var int $entityId ID сущности */
	private $entityId = null;


  /**
   * @param int $entityId
   * @return $this
   * @throws \Exception
   */
	public function setEntityId(int $entityId) : self
	{
	  $this->entityId = $entityId;
	  return $this;
	}
  /**
   * @return int
   */
	public function getEntityId() : int
	{
	  return $this->entityId;
	}


};

