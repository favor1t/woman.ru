<?php

declare(strict_types = 1);

namespace nt\User\Avatar;

/**
 * базовый класс аватарки юзера
 * Class AvatarBase
 */
class AvatarBase
{
  use
    \nt\traits\HasIdNotZero;


  /** @var string URL изображения */
  private $urlImage = null;


  /**
   * @param string $urlImage
   * @return $this
   */
  public function setUrlImage(string $urlImage) : self
  {
    $this->urlImage = $urlImage;
    return $this;
  }
  /**
   * @return string
   */
  public function getUrlImage() : string
  {
    return $this->urlImage;
  }


};



