<?php

declare(strict_types = 1);

namespace nt;

/**
 * юзер
 * Class User
 */
class User extends \nt\User\UserBase
{
  use
    \nt\traits\EntityGetById,
    \nt\traits\EntityFromArray,
    \nt\traits\EntitySaveInCache;


  /**
   * создает объект на основании массива свойств и значений
   * @param array $array
   * @return \nt\User
   */
  /*
  public static function fromArray(array $array)
  {
    return (new static())
      ->setId($array['id'])
      ->setName($array['name'])
      ->setUserAvatarId($array['userpic_id'])
      ->setUrlUserImageSmall($array['url_user_image_small']);
  }
  */


  /**
   * возвращает URL страницы юзера
   * @param bool $absolute
   * @return string
   */
	public function getSiteUrl(bool $absolute = false) : string
  {
    return ($absolute ? \Yii::app()->params['baseUrl'] : '').'/user/'.$this->getId().'/';
  }


  /**
   * возвращает URL
   * @param bool $absolute
   * @return string
   */
  public function getAvatarUrlSmall(bool $absolute) : string
  {
    $userAvatarId = $this->getUserAvatarId();
    if($userAvatarId && $userAvatar = \nt\User\Avatar::getByIdOrNull($userAvatarId)) return $userAvatar->getUrlImage($absolute);

    $url = $this->getUrlUserImageSmall();
    if($url != '') return $url;

    return \nt\User\Avatar::getUrlDefaultSmall($absolute);
  }


  /**
   * обновляет данные в кеше в случае измнения модели на вумане
   * @param \BaseUser $userWoman
   */
  public static function onWomanModelChanged(\BaseUser $userWoman)
  {
    $userNt = self::getByIdFromCache((int) $userWoman->id);
    if(! $userNt) return;

    // @TODO: на картинку-аватарку положен болт
    $userNt
      ->setName($userWoman->name)
      ->setUserAvatarId((int) $userWoman->userpic_id)
      ->saveInCache();
  }


};

