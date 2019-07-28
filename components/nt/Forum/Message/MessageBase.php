<?php

declare(strict_types = 1);

namespace nt\Forum\Message;

/**
 * базовый класс сообщения темы форума
 * Class MessageBase
 */
class MessageBase
{
  use
    \nt\traits\HasIdNotZero,
    \nt\traits\HasStatusIntNotNull,
    \nt\traits\HasIsAnonymous,
    \nt\traits\HasAnonymousId,
    \nt\traits\HasBody,
    \nt\traits\HasCreatedAt,
    \nt\traits\HasUserId,
    \nt\traits\HasUserAgent,
    \nt\traits\HasUserCookie,
    \nt\traits\HasUserIp,
    \nt\traits\HasUserName,
    \nt\traits\HasUserAvatarId,
    \nt\traits\HasImageCollection,
    \nt\traits\EntityGetUserHash;


  /** @var int ID темы */
  private $topicId = null;


  /**
   * @param int $topicId
   * @return $this
   * @throws \Exception
   */
  public function setTopicId(int $topicId) : self
  {
    if(! is_numeric($topicId)) throw new \Exception('not a numeric: '.$topicId);
    if($topicId < 1) throw new \Exception('topic id invalid: '.$topicId);

    $this->topicId = $topicId;
    return $this;
  }
  /**
   * @return int
   */
  public function getTopicId() : int
  {
    return $this->topicId;
  }


};

