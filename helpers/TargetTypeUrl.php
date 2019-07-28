<?php
/**
 * Класс для получения к какому типу относится текущий url
 * Media | Forum | Main
 */

class TargetTypeUrl
{
  private static $main  = 0;
  private static $forum = 1;
  private static $media = 2;

  public static function isForum() : bool
  {
    return self::checkForum();
  }

  public static function isMedia() : bool
  {
    return self::checkMedia();
  }

  public static function isMain() : bool
  {
    return self::checkMain();
  }

  private static function getCode(): int
  {
    if(self::isMain())  return self::$main;
    if(self::isForum()) return self::$forum;
    return self::$media;
  }

  public static function getTargetTypeForLI() : string
  {
    $code = self::getCode();

    $array = [
      self::$main   => 'main',
      self::$forum  => 'forum',
      self::$media  => 'media',
    ];

    return $array[$code];
  }

  private static function getRequestUri() : string
  {
    return $_SERVER['REQUEST_URI'];
  }

  private static function checkForum() : bool
  {
    $uri = self::getRequestUri();
    return (
      strpos($uri, '/forum/')   !== false ||
      strpos($uri, '/thread/')  !== false ||
      strpos($uri, '/user/')    !== false ||
      strpos($uri, '/search/')  !== false
    );
  }

  private static function checkMedia() : bool
  {
      return ! self::checkMain() && ! self::checkForum();
  }

  private static function checkMain() : bool
  {
    $uri = static::getRequestUri();
    return $uri === '/';
  }

  /*
   * @todo переименовать или востановить условия
   */
  public static function isTopic() : bool
  {
    $uri = self::getRequestUri();
    return (
      //strpos($uri, '/forum/')      !== false ||
      strpos($uri, '/thread/')      !== false
      //strpos($uri, '/forum/?sort') !== false ||
      //strpos($uri, '/forum/all/')   !== false
    );
  }
}