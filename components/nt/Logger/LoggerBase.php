<?php
declare(strict_types=1);

namespace nt\Logger;


use nt\User;

class LoggerBase
{

  private $id         = null;
  private $targetId   = null;
  private $targetType = null;
  private $level      = null;
  private $userId     = null;
  private $_extra     = null;
  private $createdAt  = null;

  /**
   * @return null
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param null $id
   */
  public function setId($id) : self
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return ?int
   */
  public function getTargetId()
  {
    return $this->targetId;
  }

  /**
   * @param null $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
    return $this;
  }

  /**
   * @return ?int
   */
  public function getTargetType()
  {
    return $this->targetType;
  }

  /**
   * @param int $targetType
   */
  public function setTargetType($targetType)
  {
    $this->targetType = $targetType;
    return $this;
  }

  /**
   * @return null
   */
  public function getLevel()
  {
    return $this->level;
  }

  /**
   * @param null $level
   */
  public function setLevel($level) : self
  {
    $this->level = $level;
    return $this;
  }

  /**
   * @return int
   */
  public function getUserId() : ?int
  {
    return $this->userId;
  }

  /**
   * @param int $userId
   */
  public function setUserId(int $userId) : self
  {
    $this->userId = $userId;
    return $this;
  }

  /**
   * @return string
   */
  public function getExtra(): ?string
  {
    return $this->_extra;
  }

  /**
   * @param string $text
   */
  public function setExtra(string $text) : self
  {
    $this->_extra = $text;
    return $this;
  }

  /**
   * @return null
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * @param string $createdAt
   */
  public function setCreatedAt(string $createdAt) : self
  {
    $this->createdAt = $createdAt;
    return $this;
  }
}