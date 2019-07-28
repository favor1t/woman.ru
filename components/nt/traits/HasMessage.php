<?php
declare(strict_types = 1);

namespace nt\traits;

use nt\Forum\Message;

/**
 * Trait HasMessage
 * @package nt\traits
 */
trait HasMessage
{
  /** @var nt\Forum\Message | null $forumMessage */
  private $message = null;

  /**
   * @return null|\nt\Forum\Message
   */
  public function getMessage() : ?Message
  {
    return $this->message;
  }

  /**
   * @param null|\nt\Forum\Message $message
   */
  public function setMessage(?Message $message)
  {
    $this->message = $message;
    return $this;
  }


}