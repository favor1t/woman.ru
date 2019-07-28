<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с веб-именем"
 * Class HasWebNameNotEmpty
 */
trait HasWebNameNotEmpty
{

  /** @var string $webName */
  private $webName = null;


  /**
   * устанавливает $this->webName
   * @param string $webName
   * @return $this
   * @throws \Exception
   */
  public function setWebName(string $webName) : self
  {
    if($webName == '') throw new \Exception('WebName is empty');

    $this->webName = $webName;
    return $this;
  }


  /**
   * возвращает $this->webName
   * @return string
   */
  public function getWebName() : string
  {
    return $this->webName;
  }


};

