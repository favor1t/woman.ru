<?php

declare(strict_types = 1);

namespace nt\Forum\Topic\Expert;

/**
 * генератор RSS задачи #1540
 * Class Rss1540Creator
 * @package nt\Forum\Topic\Expert
 */
class Rss1540Creator extends Mapper
{

  /**
   * возвращает содержимое RSS задачи #1540
   * @return string
   */
  public static function create() : string
  {
    return \nt\Cache::get($name = __CLASS__, $id = null, $miss = function()
    {
      return self::createRss(self::getTopicInfo());
    }, $expire = 60);
  }


  /**
   * на входе - массив информации о темах, на выходе - rss
   * @param array $array
   * @return string
   */
  private static function createRss(array $array) : string
  {
    $array = array_map(function(\StdClass $object) : string
    {
      $topic = \nt\Forum\Topic::getById($object->id);
      ob_start();
      ?>
<topic>
  <woman_topic_id><?=$topic->getId()?></woman_topic_id>
  <woman_topic_url><?=$topic->getUrl($absolute = true)?></woman_topic_url>
  <unixtime><?=$topic->getCreatedAtAsTimestamp()?></unixtime>
  <fio><?=self::getUserNameByTopic($topic)?></fio>
  <title><?=$topic->getTitle()?></title>
  <text><?=$topic->getBody()?></text>
  <hasAnswer><?=$object->has_answer ? 'true' : 'false'?></hasAnswer>
</topic>
      <?php
      return ob_get_clean();
    }, $array);

    ob_start();
    ?>
<?xml version="1.0" encoding="utf-8" ?>
<b17>
  <lastBuildDate><?=date('D, d M Y H:i:s +0300')?></lastBuildDate>
  <topic_list>
    <?=implode("\n", $array)?>
  </topic_list>
</b17>
    <?php
    return ob_get_clean();
  }


  /**
   * че-то там какая-то рехнутая логика
   * @param \nt\Forum\Topic $topic
   * @return string
   */
  private static function getUserNameByTopic(\nt\Forum\Topic $topic) : string
  {
    if($topic->getIsAnonymous()) return 'анонимно';

    $userName = $topic->getUserName();
    if($userName != '') return $userName;

    $userId = $topic->getUserId();
    if($userId)
    {
      $user = \nt\User::getByIdOrNull($userId);
      if($user) return $user->getName();
    }

    return \User::DEFAULT_NAME;
  }


  /**
   * возвращает информацию о темах из БД
   * @return array
   */
  private static function getTopicInfo() : array
  {
    return \Db::fetchAll("
      select z.id, 
      (
        select  1
        from    {{forum_messages}} m
        join    {{users}} u on u.id = m.user_id
        where   m.thread_id = z.id and u.role = 'site_expert'
        limit 1      
      ) has_answer
      from 
      (
        select t.id
        from   {{forum_threads}} t
        join   {{sections}} s on s.id = t.sections[1]
        where  1 = 1
          and t.status = ".(int) \ForumThread::STATUS_OPEN."
          and t.tags && array[".(int) \Yii::app()->params['siteExpertTagId']."]
          and
          (
            1 = 0 
            or s.pid = ".\Section::SECTION_ID_RELATIONS." /* Любовь - и все подсекции     */
            or s.pid = ".\Section::SECTION_ID_PSYCHO."    /* Психология - и все подсекции */
            or s.id in (279, 1156)                        /* Дети - только подсекции 'ДО 16 и старше' и 'Психология и развитие' */
            or s.id in (4278)                             /* Здоровье - только подсекция 'Диеты' */
          )
        order by t.created_at desc
        limit 100  
      ) z");
  }

};