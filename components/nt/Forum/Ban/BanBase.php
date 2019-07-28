<?php
declare(strict_types=1);

namespace nt\Forum\Ban;

use nt\Cache;
use nt\Forum\Ban;
use nt\Forum\Message;
use nt\Forum\Topic;

/**
 * Class BanBase
 * @package nt\Forum\Ban
 */
class BanBase
{

  /**
   * @param Topic $topic
   * @return Ban[]
   */
  public static function getCookiesByTopic(Topic $topic): array
  {
    return Cache::get(self::getCacheKeyByTopic($topic), $topic->getId(),
      function () use ($topic) {
        return self::getRealCookiesByTopic($topic);
      }, self::getExpire());

  }

  public static function updateCookieCache(Topic $topic): bool
  {
    return Cache::set(self::getCacheKeyByTopic($topic), $topic->getId(), self::getRealCookiesByTopic($topic));
  }

  public static function getCacheByTopic(Topic $topic): ?array
  {
    return Cache::get(self::getCacheKeyByTopic($topic), $topic->getId());
  }

  /**
   * @param Topic $topic
   * @param array $params
   * @return array
   */
  private static function getRealCookiesByTopic(Topic $topic): array
  {
    $result = \Db::fetchAll('
        SELECT user_cookie, message_id
        FROM {{forum_user_blacklist}}
        WHERE thread_id = :thread_id',
      [':thread_id' => $topic->getId()]);

    if (empty($result)) return [];
    $cookies = [];
    foreach ($result as $ban)
      $cookies[$ban->user_cookie] = $ban->message_id;
    return $cookies;
  }

  /**
   * @param Topic $topic
   * @return string
   */
  private static function getCacheKeyByTopic(Topic $topic): string
  {
    return __CLASS__ . '~topicId~' . $topic->getId();
  }

  /**
   * @return int
   */
  private static function getExpire(): int
  {
    return 60*60;
  }


  /**
   * @param Topic $topic
   * @param Message $message
   * @return array
   */
  public static function add(Topic $topic, Message $message): bool
  {
    \Db::execute('
      INSERT INTO {{forum_user_blacklist}} (thread_id, user_cookie, created_at, message_id)
      VALUES (:thread_id, :user_cookie, now(), :message_id)',
      [
        ':thread_id'    => $topic->getId(),
        ':user_cookie'  => $message->getUserCookie(),
        ':message_id'   => $message->getId(),
      ]);

    return Cache::set(
      self::getCacheKeyByTopic($topic),
      $topic->getId(),
      self::getRealCookiesByTopic($topic),
      self::getExpire()
    );
  }

  /**
   * @param Topic $topic
   * @param Message $message
   * @return array
   */
  public static function delete(Topic $topic, Message $message): bool
  {
    \Db::execute('
      DELETE FROM {{forum_user_blacklist}}
      WHERE thread_id = :thread_id and user_cookie = :user_cookie',
      [
        ':thread_id'    => $topic->getId(),
        ':user_cookie'  => $message->getUserCookie(),
      ]);

    return Cache::set(
      self::getCacheKeyByTopic($topic),
      $topic->getId(),
      self::getRealCookiesByTopic($topic),
      self::getExpire()
    );
  }

};