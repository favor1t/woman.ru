<?php

declare(strict_types = 1);

namespace nt\User;

/**
 * аватарка юзера
 * Class Avatar
 */
class Avatar extends \nt\User\Avatar\AvatarBase
{
  use \nt\traits\EntityGetById;


  /**
   * возвращает URL аватарки по-умолчанию
   * @param bool $absolute
   * @return string
   */
  public static function getUrlDefaultSmall(bool $absolute) : string
  {
    return \StaticResourceHelper::staticUrl(($absolute ? \Yii::app()->params['baseUrl'] : '').'/i/userpic.gif');
  }


  /**
   * создает объект на основании массива свойств и значений
   * @param array $array
   * @return \nt\User\Avatar
   */
  public static function fromArray(array $array) : \nt\User\Avatar
  {
    return (new static())
      ->setId($array['id'])
      ->setUrlImage($array['url_image']);
  }


  /**
   * возвращает URL картинки
   * @TODO: на параметр $absolute положен болт
   * @param bool $absolute
   * @return string
   */
  public function getSiteUrl(bool $absolute) : string
  {
    return $this->getUrlImage();
  }


  /**
   * ID картинки совпадает с используемым по-умолчанию?
   * @param int | null $avatarId
   * @return bool
   */
  public static function isAvatarIdDefault(?int $avatarId) : bool
  {
    return in_array($avatarId, [ \Userpic::FEMALE_USERPIC, \Userpic::MALE_USERPIC, ]);
  }


};

