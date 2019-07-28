<?php

declare(strict_types = 1);

namespace Block\Amp;

/**
 * AMP блок, отображающий информацию о публикации
 */
class PublicationBlock extends \BlockBase
{

  /** @var \Publication $publication */
  protected $publication = null;


  /**
   * инициализация блока
   * @return PublicationBlock
   */
  public function init()
  {
    $this->publication = \Publication::getById($_GET['id']);
    return parent::init();
  }


  public function getUrlCanonical() : string
  {
    $url = $this->publication->getSiteUrl($absolute = true);
    $url = str_replace('amp.', 'www.', $url);
    return $url;
  }


  /**
   * возвращает титл страницы
   * @return string
   */
  public function getPageTitle() : string
  {
    return $this->publication->title;
  }



};

