<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с флагом анонимности"
 * Class HasIsAnonymous
 */
trait HasIsAnonymous
{

  /** @var bool $isAnonymous */
  private $isAnonymous = null;


  /**
   * устанавливает $this->isAnonymous
   * @param bool $isAnonymous
   * @return $this
   */
  public function setIsAnonymous(bool $isAnonymous) : self
  {
    $this->isAnonymous = $isAnonymous;
    return $this;
  }


  /**
   * возвращает $this->isAnonymous
   * @return bool
   */
  public function getIsAnonymous() : bool
  {
    return $this->isAnonymous;
  }


  /**
   * @param bool $isAnonymous
   * @return $this | bool
   */
  public function isAnonymous(bool $isAnonymous = null)
  {
    return count(func_get_args()) ? $this->setIsAnonymous($isAnonymous) : $this->getIsAnonymous();
  }


};

