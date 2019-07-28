<?php
declare(strict_types=1);

class QAPageHelper
{
  /*** @var int */
  private $pageNumber     = 0;
  /*** @var \nt\Forum\Topic */
  private $topic          = null;
  /*** @var \nt\Forum\Message[] */
  private $arrMessages    = [];
  /*** @var \nt\Forum\Message */
  private $expertMessage  = null;
  /*** @var self | null */
  static $instance        = null;

  /*** Возвращает экземпляр класса*/
  public static function getInstance(): self
  {
    if (!self::$instance) self::$instance = new static();
    return self::$instance;
  }

  /*** @return \nt\Forum\Topic */
  public function getTopic() : ?\nt\Forum\Topic
  {
    return $this->topic;
  }

  /*** @param \nt\Forum\Topic $topic */
  public function setTopic(\nt\Forum\Topic $topic): self
  {
    $this->topic = $topic;
    return $this;
  }

  /*** @return \nt\Forum\Message[] */
  public function getArrMessages(): array
  {
    return $this->arrMessages;
  }

  /*** @param \nt\Forum\Message[] $arrMessage */
  public function setArrMessages(array $arrMessages): self
  {
    $this->arrMessages = $arrMessages;
    return $this;
  }

  /*** @return \nt\Forum\Message[] */
  public function getExpertMessage(): ?\nt\Forum\Message
  {
    return $this->expertMessage;
  }

  /*** @param \nt\Forum\Message $expertMessage */
  public function setExpertMessage(\nt\Forum\Message $expertMessage): self
  {
    $this->expertMessage = $expertMessage;
    return $this;
  }

  /*** @return int */
  public function getPageNumber(): int
  {
    return $this->pageNumber;
  }

  /*** @param int $pageNumber */
  public function setPageNumber(int $pageNumber = 0): self
  {
    $this->pageNumber = $pageNumber;
    return $this;
  }


};