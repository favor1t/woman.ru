<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с ID аватарки юзера"
 * Class HasUserAvatarId
 */
trait HasUserAvatarId
{

  /** @var int $userAvatarId ID аватарки юзера */
	private $userAvatarId = null;


  /**
   * @param int $userAvatarId
   * @return $this
   * @throws \Exception
   */
	public function setUserAvatarId(int $userAvatarId) : self
	{
	  $this->userAvatarId = $userAvatarId;
	  return $this;
	}
  /**
   * @return int
   */
	public function getUserAvatarId() : int
	{
	  return $this->userAvatarId;
	}


};

