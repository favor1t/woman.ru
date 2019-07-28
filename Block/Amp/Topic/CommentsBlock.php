<?php
declare(strict_types=1);

namespace Block\Amp\Topic;

/**
 * Class CommentsBlock
 * @package Block\Amp\Topic
 */
class CommentsBlock extends \BlockBase
{
  use
    \nt\traits\HasTopic,
    \nt\traits\HasPage;

  /**
   * @var array
   */
  private $comments = [];

  /**
   * @return $this
   */
  public function init(){
    if($this->forumTopic) $this->comments = $this->forumTopic->getMessageByPage($this->page);
    return parent::init();
  }

  /**
   * @return array
   */
  public function getComments() : array
  {
    return $this->comments;
  }

}