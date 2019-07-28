<?php

declare(strict_types = 1);

namespace nt;

/**
 * изображение
 * Class Image
 */
class Image extends \nt\Image\ImageBase
{

  /**
   * пытаеццо создать картинку, может вернуть null
   * @param array | null $array 
   * @return \nt\Image | null
   */
  public static function tryFromArray(array $array = null) : ?\nt\Image
  {
    if($array === null) return null;
    if(! isset($array['displayImage'])) return null;
    return (new static())->setUrl($array['displayImage']);
  }


  /**
   * возвращает URL ресайзенного изображения
   * @param int $width
   * @param int $height
   * @param int $resize
   * @return string
   */
  public function getUrlResized(int $width, int $height, $resize = \ImageHelper::RESIZE_FIT) : string
  {
    $url = preg_replace('/_\d+_\d+x\d+\.(gif|jpg|jpeg|png)/', '.$1', $this->getUrl());
    $url = preg_replace('/.(gif|jpg|jpeg|png)/', '_'.$resize.'_'.$width.'x'.$height.'.$1', $url);
    return $url;
  }


};

