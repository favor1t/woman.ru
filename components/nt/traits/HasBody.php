<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с телом"
 * Class HasBody
 */
trait HasBody
{

  /** @var string | null $body тело */
	private $body = null;


  /**
   * @param string $body
   * @return $this
   * @throws \Exception
   */
	public function setBody(?string $body) : self
	{
	  $this->body = $body;
	  return $this;
	}
  /**
   * @return string
   */
	public function getBody() : ?string
	{
	  return $this->body;
	}

  public function getBodyWithoutQuotes() : string
  {
    $body = $this->getBody();
    while(true)
    {
      if(! count($this->getQuotesFromBody()))
      {
        $body = html_entity_decode(str_replace('\u', '&#x', $this->getBody()), ENT_NOQUOTES, 'UTF-8');
        $body = str_replace('\n', '<br>', $body);
        break;
      }
      $body = \Html::restoreHtml($body);

      foreach($this->getQuotesFromBody() as $quote)
      {
        $body = str_replace($quote->complete, '', $body);
      }

      $body = preg_replace('/\[\/?quote[^\]]*\]/uUis', '', $body);
      break;
    }
    return (string)$body;
  }

  public function getQuotesFromBody() : array
  {
    return \Quote::parseQuotes(\Html::restoreHtml($this->getBody())) ?? [];
  }
};

