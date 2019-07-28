<?php

declare(strict_types = 1);

namespace nt;
use nt\Forum\Message;

/**
 * #1826: голосование
 * Class Vote
 * @package nt
 */
class Vote
{

  /**
   * преобразование класса в инт
   * @param $target
   * @return int
   * @throws \Exception
   */
  private static function getTargetTypeIdByTarget($target) : int
  {
    if($target instanceOf \nt\Forum\Message) return self::getMesageType($target);
    if($target instanceOf \nt\Forum\Topic)   return self::getTopicType();
    throw new \Exception('unknown target type: '.get_class($target));
  }


  /**
   * возвращает ключ кеша, где храним сумму голосов
   * @param $target
   * @return string
   */
  private static function getCacheKeyVoteSumByTarget($target) : string
  {
    return implode('~', [ __CLASS__, self::getTargetTypeIdByTarget($target), $target->getId(), ]);
  }


  /**
   * поддержка голосования
   * @param $target
   * @param \WebUser $webUser
   */
  public static function voteUp($target, \WebUser $webUser) : void
  {
    self::vote($target, $webUser, $vote = 1);
  }

  /**
   * @param $target
   * @param \WebUser $webUser
   */
  public static function removeVote($target, \WebUser $webUser) : void
  {
    \Db::execute('
      delete from {{vote_raw}}
      where  target_type_id = :target_type_id and target_id = :target_id and user_cookie = :user_cookie',
      [ ':target_type_id' => self::getTargetTypeIdByTarget($target), ':target_id' => $target->getId(), ':user_cookie' => $webUser->cookie->value, ]);
    self::recalcVoteSum($target);
  }

  /**
   * @param $target
   * @param \WebUser $webUser
   */
  public static function voteDown($target, \WebUser $webUser) : void
  {
    self::vote($target, $webUser, $vote = -1);
  }

  /**
   * @param $target
   * @param \WebUser $webUser
   * @param int $vote
   * @throws \Exception
   */
  public static function vote($target, \WebUser $webUser, int $vote) : void
  {
    if($vote != -1 && $vote != 1 && $vote != 0) throw new \Exception('vote invalid: '.$vote);

    \Db::execute('
      insert into {{vote_raw}} (target_type_id, target_id, created_at, user_cookie, user_ip, user_agent, vote, user_id)
      values (:target_type_id, :target_id, now(), :user_cookie, :user_ip, :user_agent, :vote, :user_id)',
      [
        ':target_type_id' => self::getTargetTypeIdByTarget($target),
        ':target_id'      => $target->getId(),
        ':user_cookie'    => $webUser->cookie->value,
        ':user_ip'        => $webUser->ip,
        ':user_agent'     => $webUser->userAgent,
        ':vote'           => $vote,
        ':user_id'        => $webUser->id > 0 ? (int) $webUser->id : null,
      ]);
    self::recalcVoteSum($target);
  }


  /**
   * пересчет голосов
   * @param $target
   */
  private static function recalcVoteSum($target) : void
  {
    \Db::begin();
    \Db::execute('
      delete from {{vote_sum}}
      where  target_type_id = :target_type_id and target_id = :target_id',
      [ ':target_type_id' => self::getTargetTypeIdByTarget($target), ':target_id' => $target->getId(), ]);
    $result = \Db::fetch('
      insert    into {{vote_sum}} (target_type_id, target_id, count_like, count_dislike)
      select    target_type_id, target_id, sum(if(vote > 0, 1, 0)), sum(if(vote < 0, 1, 0))
      from      {{vote_raw}}
      where     target_type_id = :target_type_id and target_id = :target_id
      group     by target_type_id, target_id
      returning count_like, count_dislike',
      [ ':target_type_id' => self::getTargetTypeIdByTarget($target), ':target_id' => $target->getId(), ]);
    \Db::commit();

    \nt\Cache::set(self::getCacheKeyVoteSumByTarget($target), $entityId = null, $value = self::createVoteBySqlResult($result));
  }


  /**
   * возвращает сумму голосов
   * @param $target
   * @return int
   */
  public static function getVoteSum($target) : int
  {
    $array = self::getVote($target);
    return $array['count_like'] - $array['count_dislike'];
  }
  /**
   * @param $target
   * @return int[]
   */
  public static function getVote($target) : array
  {
    return \nt\Cache::get(self::getCacheKeyVoteSumByTarget($target), $entityId = null, function() use ($target)
    {
      $result = \Db::fetch('
        select count_like, count_dislike
        from   {{vote_sum}}
        where  target_type_id = :target_type_id and target_id = :target_id
        limit  1',
        [ ':target_type_id' => self::getTargetTypeIdByTarget($target), ':target_id' => $target->getId(), ]);
      return self::createVoteBySqlResult($result);
    });
  }

  /**
   * @param $object | false
   * @return array
   */
  private static function createVoteBySqlResult($object) : array
  {
    return
    [
      'count_like'    => $object ? $object->count_like    : 0,
      'count_dislike' => $object ? $object->count_dislike : 0,
    ];
  }

  /**
   * @return int
   */
  public static function getMesageType(Message $message) : int
  {
    return $message->getVoteTargetType($message);

  }

  /**
   * @return int
   */
  public static function getVoteMessageExpertType(): int
  {
    return 1;
  }

  /**
   * @return int
   */
  public static function getVoteDefaultMessageType(): int
  {
    return 3;
  }

  /**
   * @return int
   */
  public static function getTopicType() : int
  {
    return 2;
  }
/*  public static function getTypes() : array
  {
    return [
      self::getMesageType() => self::getMesageType(),
      self::getTopicType()  => self::getTopicType(),
    ];
  }*/

  public static function getExpertIdsByRatingParams(array $params = []) : array
  {
    $array = [];
    $sql = '
      select count_like, count_dislike, target_id, target_type_id
      from {{vote_sum}}
      left join {{forum_messages}} on id = target_id
      where
        1=1 
        and target_type_id = 1 
        and (count_like + count_dislike) > 0 
      ' . self::getSqlBySorted($params) . ' , created_at desc
        
      limit 100';

    foreach (\Db::fetchAll($sql) as $obj){
      try{
      $message = \nt\Forum\Message::getById($obj->target_id);
      if($message) $array[] = ['topic_id' => $message->getTopicId(), 'message_id' => $obj->target_id];
      } catch (\Exception $e) {}
    }
    return $array;
  }

  private static function getSqlBySorted(array $params): string
  {
    $sql = ' order by count_like desc';
    if(isset($params['sort']) && $params['sort'] == 'count_dislike')
      $sql = ' order by count_dislike desc';
    return $sql;
  }

};




