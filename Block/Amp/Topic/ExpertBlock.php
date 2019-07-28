<?php
declare(strict_types=1);

namespace Block\Amp\Topic;

use nt\Forum\Message;

/**
 * Class ExpertBlock
 * @package Block\Amp\Topic
 */
class ExpertBlock extends \BlockBase
{
  use
    \nt\traits\HasTopic;

  /**
   * @var Message|null
   */
  private $expertMessage = null;


  /**
   * @return Message|null
   */
  public function getExpertMessage() : ?Message
  {
    return $this->expertMessage;
  }


  /**
   * @param Message|null
   * @return ExpertBlock
   */
  public function setExpertMessage(?Message $expertMessage) : self
  {
    $this->expertMessage = $expertMessage;
    return $this;
  }


}