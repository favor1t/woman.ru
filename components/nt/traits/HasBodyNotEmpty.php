<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустым телом"
 * Class HasBodyNotEmpty
 */
trait HasBodyNotEmpty
{

  /** @var string $body тело */
	private $body = null;


  /**
   * @param string $body
   * @return $this
   * @throws \Exception
   */
	public function setBody(string $body) : self
	{
    if($body == '') throw new \Exception('body is empty');

	  $this->body = $body;
	  return $this;
	}
  /**
   * @return string
   */
	public function getBody() : string
	{
	  return $this->body;
	}


};

