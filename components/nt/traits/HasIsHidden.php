<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с флагом скрытности"
 * Class HasIsHidden
 */
trait HasIsHidden
{

  /** @var bool $isHidden */
  private $isHidden = null;


  /**
   * устанавливает $this->isHidden
   * @param bool $isHidden
   * @return $this
   */
  public function setIsHidden(bool $isHidden) : self
  {
    $this->isHidden = $isHidden;
    return $this;
  }


  /**
   * возвращает $this->isHidden
   * @return bool
   */
  public function getIsHidden() : bool
  {
    return $this->isHidden;
  }


  /**
   * @param bool $isHidden
   * @return $this | bool
   */
  public function isHidden(bool $isHidden = null)
  {
    return count(func_get_args()) ? $this->setIsHidden($isHidden) : $this->getIsHidden();
  }


};

