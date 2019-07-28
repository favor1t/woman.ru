<?php

declare(strict_types = 1);

namespace nt\User;

/**
 * базовый класс юзера
 * Class UserBase
 */
class UserBase
{
  use
    \nt\traits\HasIdNotZero,
    \nt\traits\HasNameNotEmpty,
    \nt\traits\HasUserAvatarId;

  /** @var string маленькая картинка, загруженная юзером */
  private $urlUserImageSmall = null;


  /**
   * @param string $urlUserImageSmall
   * @return $this
   */
  public function setUrlUserImageSmall(string $urlUserImageSmall) : self
  {
    $this->urlUserImageSmall = $urlUserImageSmall;
    return $this;
  }
  /**
   * @return string | null
   */
  public function getUrlUserImageSmall() : string
  {
    return $this->urlUserImageSmall;
  }

};



