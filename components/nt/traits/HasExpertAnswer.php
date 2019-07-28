<?php
declare(strict_types = 1);

namespace nt\traits;


trait HasExpertAnswer
{
  private $expertAnswer = null;

  /**
   * @return int | null
   */
  public function hasExpertAnswer() : ?int
  {
    return $this->expertAnswer;
  }

  /**
   * @param null $expertMessage
   */
  public function setExpertAnswer(?int $expertAnswer = null)
  {
    $this->expertAnswer = $expertAnswer;
  }


}