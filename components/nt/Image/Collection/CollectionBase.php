<?php

declare(strict_types = 1);

namespace nt\Image\Collection;

/**
 * базовая коллекция изображений
 * Class CollectionBase
 */
class CollectionBase
{

  /** @var \nt\Image[] */
  private $arrImage = [];


  /**
   * @param \nt\Image[] $arrImage
   * @return $this
   */
  public function setImage(array $arrImage) : self
  {
    $this->arrImage = $arrImage;
    return $this;
  }
  /**
   * @return \nt\Image[]
   */
  public function getImage() : array
  {
    return $this->arrImage;
  }


};

