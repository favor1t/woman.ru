<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с ID юзера"
 * Class HasUserId
 */
trait HasUserId
{

  /** @var int $userId ID юзера */
	private $userId = null;


  /**
   * @param int $userId
   * @return $this
   * @throws \Exception
   */
	public function setUserId(int $userId) : self
	{
	  $this->userId = $userId;
	  return $this;
	}
  /**
   * @return int
   */
	public function getUserId() : int
	{
	  return $this->userId;
	}


  /**
   * возвращает юзера-автора или null
   * @return \nt\User | null
   */
  public function getUserOrNull() : ?\nt\User
  {
    $userId = $this->getUserId();
    return $userId ? \nt\User::getByIdOrNull($userId) : null;
  }
  /**
   * возвращает юзера-автора
   * @return \nt\User
   * @throws \Exception
   */
  public function getUser() : \nt\User
  {
    $user = $this->getUserOrNull();
    if(! $user) throw new \Exception('can not get user by '.static::class);
    return $user;
  }

};

