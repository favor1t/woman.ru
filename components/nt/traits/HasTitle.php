<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с титлом"
 * Class HasTitle
 */
trait HasTitle
{

  /** @var string | null $title титл */
	private $title = null;


  /**
   * @param string $title
   * @return $this
   * @throws \Exception
   */
	public function setTitle(?string $title) : self
	{
	  $this->title = $title;
	  return $this;
	}
  /**
   * @return string
   */
	public function getTitle() : ?string
	{
	  return $this->title;
	}


};

