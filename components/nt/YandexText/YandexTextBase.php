<?php

declare(strict_types = 1);

namespace nt\YandexText;

/**
 * #523: тексты для яндекса
 * Class YandexText
 */
class YandexTextBase
{
  use
    \nt\traits\HasIdNotZeroNullable,
    \nt\traits\HasCreatedAtNotZero,
    \nt\traits\HasPriorityNotNull,
    \nt\traits\HasStatusIntNotZero,
    \nt\traits\HasStatusSetAtNotZero,
    \nt\traits\HasEntityTypeIdNotZero,
    \nt\traits\HasEntityId,
    \nt\traits\HasText;

  /** @var string | null */
  private $yandexErrorCode = null;
  /** @var string | null */
  private $yandexTextId    = null;  


  public function setYandexErrorCode(?string $yandexErrorCode) : self
  {
    $this->yandexErrorCode = $yandexErrorCode;
    return $this;
  }
  public function getYandexErrorCode() : ?string
  {
    return $this->yandexErrorCode;
  }

  
  public function setYandexTextId(?string $yandexTextId) : self
  {
    $this->yandexTextId = $yandexTextId;
    return $this;
  }
  public function getYandexTextId() : ?string
  {
    return $this->yandexTextId;
  }
  

};



