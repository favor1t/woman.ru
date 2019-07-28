<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустым титлом"
 * Class HasTitleNotEmpty
 */
trait HasTitleNotEmpty
{

  /** @var string $title титл */
	private $title = null;


  /**
   * @param string $title
   * @return $this
   * @throws \Exception
   */
	public function setTitle(string $title) : self
	{
    if($title == '') throw new \Exception('title is empty');

	  $this->title = $title;
	  return $this;
	}
  /**
   * @return string
   */
	public function getTitle() : string
	{
	  return $this->title;
	}


};

