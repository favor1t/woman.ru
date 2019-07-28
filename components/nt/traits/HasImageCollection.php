<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с коллекцией картинок"
 * Class HasImageCollection
 */
trait HasImageCollection
{

  /** @var \nt\Image\Collection коллекция изображений из экстры */
  private $imageCollection = null;


  /**
   * @param \nt\Image\Collection $imageCollection
   * @return $this
   */
  public function setImageCollection(\nt\Image\Collection $imageCollection) : self
  {
    $this->imageCollection = $imageCollection;
    return $this;
  }
  /**
   * @return \nt\Image\Collection
   */
  public function getImageCollection() : \nt\Image\Collection
  {
    return $this->imageCollection;
  }

};

