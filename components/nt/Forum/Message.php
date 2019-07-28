<?php

declare(strict_types = 1);

namespace nt\Forum;

/**
 * сообщение темы форума
 * Class Message
 */
class Message extends \nt\Forum\Message\MessageBase
{
  use
    \nt\traits\EntityFromArray,
    \nt\traits\EntityVote;


  /**
   * возвращает массив сообщений указанной темы и страницы
   * @param \nt\Forum\Topic $forumTopic
   * @param int $page
   * @return \nt\Forum\Message[]
   */
  public static function getByTopicAndPage(\nt\Forum\Topic $forumTopic, int $page) : array
  {
    return \nt\Forum\Message\Manager::getByTopicAndPage($forumTopic, $page);
  }


  /**
   * возвращает сообщение по ID
   * @param int $forumMessageId
   * @return Message
   */
  public static function getById(int $forumMessageId) : self
  {
    return \nt\Forum\Message\Manager::getById($forumMessageId);
  }

  /**
   * возвращает сообщение по ID
   * @param int $forumMessageId
   * @return Message
   */
  public static function getByIdOrNull(int $forumMessageId) : ?self
  {
    return \nt\Cache::get(static::class, $forumMessageId, function(int $entityId)
    {
      return \nt\Forum\Message\Manager::getByIdOrNull($entityId);
    });
  }


  /**
   * поддержка голосования за сообщения
   * @TODO: вынести в трейт, т.к. будет использоваться и в темах форума
   * @param \WebUser $webUser
   * @return Message
   */
/*  public function voteUp(\WebUser $webUser) : self
  {
    \nt\Vote::voteUp($this, $webUser);
    return $this;
  }
  public function removeVote(\WebUser $webUser) : self
  {
    \nt\Vote::removeVote($this, $webUser);
    return $this;
  }
  public function voteDown(\WebUser $webUser) : self
  {
    \nt\Vote::voteDown($this, $webUser);
    return $this;
  }
  public function vote(\WebUser $webUser, int $vote) : self
  {
    \nt\Vote::vote($this, $webUser, $vote);
    return $this;
  }
  public function getVoteSum() : int
  {
    return \nt\Vote::getVoteSum($this);
  }
  public function getVote() : array
  {
    return \nt\Vote::getVote($this);
  }*/



  /**
   * сахар к статусу сообщения: сообщение видимо?
   * @return bool
   */
  public function isStatusVisible() : bool
  {
    return $this->getStatus() == \ForumMessage::STATUS_ON;
  }


  /**
   * сносит данные из кеша в случае измнения модели вумана
   * @param \ForumMessage | mobile\models\forum\Message $forumMessage
   */
  public static function onWomanModelChanged($forumMessage) : void
  {
    // снесем выборку сообщений из БД
    \nt\Forum\Message\Manager::onWomanForumMessageChanged($forumMessage);

    // информируем тему
    \nt\Forum\Topic::getById((int) $forumMessage->thread_id)->onForumMessageChanged($forumMessage);
  }

  /**
   * @return string
   */
  public function getBodyBrToP() : string
  {
    return \TextHelper::brToP($this->getBody());
  }

  /**
   * возвращает массив сообщений экспертов в указанной теме
   * @param \nt\Forum\Topic $forumTopic
   * @return \nt\Forum\Message[]
   */
  public static function getMessageExpertByTopic(\nt\Forum\Topic $forumTopic) : array
  {
    return \nt\Forum\Message\Manager::getMessageExpertByTopic($forumTopic);
  }

  /**
   * @return bool
   */
  public function isExpertMessage() : bool
  {
      return (bool)\Expert::getExpertById((int) $this->getUserId());
  }

  /**
   * @param array $arrParam
   * @return \nt\Forum\Topic[]
   */
  public static function getByParam(array $arrParam, bool $onlyTopics = true) : array
  {
    return \nt\Forum\Message\Manager::getByParam($arrParam, $onlyTopics);
  }


  /**
   * @param int $topicId
   * @param int $page
   * @return bool
   */
  public static function hasVisibleMessagesOnPage(int $topicId, int $page, int $lastMessageId): bool
  {
    $arrParam                     = [];
    $arrParam['topicId']          = $topicId;
    $arrParam['status']           = \ForumMessage::STATUS_ON;
    $arrParam['sql_where_native'] = 'id > ' . $lastMessageId;
    $arrParam['limit']            = 1;

    return \nt\Forum\Message\Manager::hasMessageByParams($arrParam);
  }

  /**
   * @param Message[] $messages
   * @return bool
   */
  public static function hasVisibleMessages(array $messages): bool
  {
    foreach ($messages as $message)
      if($message->isStatusVisible())
        return true;
    return false;
  }

  /**
   * @param Message[] $messages
   * @return int
   */
  public static function getLastVisibleIndex(array $messages): int
  {
    $result = 0;

    foreach ($messages as $index => $message)
      if($message->isStatusVisible())
        $result = $index;

    return $result;
  }

  /**
   * @param Message[] $messages
   * @return int
   */
  public static function getLastId(array $messages): int
  {
    $result = 0;

    foreach ($messages as $index => $message)
      if($message->isStatusVisible())
        $result = $message->getId() > $result ? $message->getId() : $result;

    return $result;
  }

};

