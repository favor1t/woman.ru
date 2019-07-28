<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с текстом"
 * Class HasText
 */
trait HasText
{

  /** @var string $text текст */
	private $text = null;


  /**
   * @param string $text
   * @return $this
   * @throws \Exception
   */
	public function setText(string $text) : self
	{
	  $this->text = $text;
	  return $this;
	}
  /**
   * @return string
   */
	public function getText() : string
	{
	  return $this->text;
	}


};

