<?php
declare(strict_types = 1);

namespace nt\traits;

use nt\Forum\Topic;

/**
 * Trait HasTopic
 * @package nt\traits
 */
trait HasTopic
{
  /** @var nt\Forum\Topic | null $topic */
  private $forumTopic = null;

  /**
   * @return null|\nt\Forum\Topic
   */
  public function getForumTopic() : ?Topic
  {
    return $this->forumTopic;
  }

  /**
   * @param null|\nt\Forum\Topic $topic
   */
  public function setForumTopic(Topic $topic)
  {
    $this->forumTopic = $topic;
    return $this;
  }


}