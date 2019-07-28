<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с ID родителя"
 * ID родителя может быть пустым
 * Class HasParentId
 */
trait HasParentId
{

  /** @var int | null $parentId */
  private $parentId = null;


  /**
   * устанавливает $this->parentId
   * @param int | null $parentId
   * @return $this
   */
  public function setParentId(?int $parentId) : self
  {
    $this->parentId = $parentId;
    return $this;
  }


  /**
   * возвращает $this->parentId
   * @return int | null
   */
  public function getParentId() : ?int
  {
    return $this->parentId;
  }


  /**
   * родитель существует?
   * @return bool
   */
  public function hasParent() : bool
  {
    return (bool) $this->getParentId();
  }


  /**
   * возвращает родителя
   * @return \nt\Section
   * @throws \Exception
   */
  public function getParent()
  {
    $parentId = $this->getParentId();
    if(! $parentId) throw new \Exception(static::class.' has no parent');

    return static::getById((int) $parentId);
  }


};

