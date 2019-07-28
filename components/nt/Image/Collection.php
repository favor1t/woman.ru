<?php

declare(strict_types = 1);

namespace nt\Image;

/**
 * коллекция изображений
 * Class Collection
 */
class Collection extends \nt\Image\Collection\CollectionBase
{

  /**
   * создает и возвращает коллекцию изображений
   * @param array $array
   * @return Collection
   */
  public static function fromArray(array $array) : \nt\Image\Collection
  {
    // преобразовали в картинки
    $arrImage = array_map(function($array)
    {
      // вот уроды...
      if($array === null) return null;
      if(is_string($array)) $array = [ 'displayImage' => $array, ];

      return \nt\Image::tryfromArray($array);
    }, $array);

    // снесли то, что не смогли преобразовать
    $arrImage = array_filter($arrImage, function($value)
    {
      return $value !== null;
    });

    return (new static())->setImage($arrImage);
  }


  /**
   * коллекция содержит картинки? (коллекция не пуста?)
   * @return bool
   */
  public function hasImage() : bool
  {
    return (bool) count($this->getImage());
  }


};

