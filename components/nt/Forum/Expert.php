<?php

declare(strict_types=1);

namespace nt\Forum;

use nt\Forum\Topic\Expert\Manager;

/**
 * тема форума: вопрос эксперту
 * Class Expert
 * @package nt\Forum
 */
class Expert extends Topic
{

  public static function getThreadsByPage(int $page = 1) : array
  {
    return Manager::getMessageByPage($page);
  }

  public static function getCountMessage(array $array = [], bool $cache = true) : int
  {
    return Manager::getCountMessage($array, $cache);
  }

  /**
   * возвращает содержимое RSS задачи #1540
   * @return string
   */
  public static function getRss1540() : string
  {
    return \nt\Forum\Topic\Expert\Rss1540Creator::create();
  }
  

};