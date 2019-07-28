<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "создание и иницилизация объекта на основании массива"
 * Trait EntityFromArray
 */
trait EntityFromArray
{

  /**
   * создает, инициализирует объект на основании массива
   * @param array $array
   * @return \nt\Forum\Message | \nt\Forum\Topic | \nt\Section | \nt\Tag | \nt\User
   */
  public static function fromArray(array $array)
  {
    static $arrMethod = [];

    $object = new static();
    foreach($array as $property => $value)
    {
      if(! isset($arrMethod[$property]))
      {
        $arrMethod[$property] = \TextHelper::doCamelCase('set_'.$property);
      }
      $object->{$arrMethod[$property]}($value);
    }
    return $object;
  }

};

