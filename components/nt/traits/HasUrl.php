<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с URL"
 * Class HasUrl
 */
trait HasUrl
{

  /** @var string $url URL */
	private $url = null;


  /**
   * @param string $url
   * @return $this
   * @throws \Exception
   */
	public function setUrl(string $url) : self
	{
	  $this->url = $url;
	  return $this;
	}
  /**
   * @return string
   */
	public function getUrl() : string
	{
	  return $this->url;
	}


};

