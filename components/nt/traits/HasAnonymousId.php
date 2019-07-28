<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с ID анонима"
 * Class HasAnonymousId
 */
trait HasAnonymousId
{

  /** @var string $anonymousId */
  private $anonymousId = null;


  /**
   * устанавливает $this->anonymousId
   * @param string $anonymousId
   * @return $this
   */
  public function setAnonymousId(string $anonymousId) : self
  {
    $this->anonymousId = $anonymousId;
    return $this;
  }


  /**
   * возвращает $this->anonymousId
   * @return string
   */
  public function getAnonymousId() : string
  {
    return $this->anonymousId;
  }


};

