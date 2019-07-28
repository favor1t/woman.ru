<?php

/**
 * хелпер для работы с архивом тем форума
 * Class ForumThreadArchiveHelper
 */
class ForumThreadArchiveHelper
{

  /**
   * возвращает название $_GET параметра, переключающего форум в "работа с архивом тем"
   * @return string
   */
  public static function getParamName()
  {
    return 'forum_archive';
  }
  /**
   * возвращает булевое: параметр "работа с архивом тем" определен?
   * @return bool
   */
  public static function hasParam()
  {
    return isset($_GET[self::getParamName()]) && $_GET[self::getParamName()];
  }
  /**
   * возвращает значение параметра (год, он же - шард)
   * @throws Exception
   */
  public static function getParam()
  {
    $value = self::getParamOrNull();
    if($value === null) throw new Exception('archive parameter not defined');
    return $value;
  }
  /**
   * возвращает значение параметра (год, он же - шард)
   * @throws Exception
   */
  public static function getParamOrNull()
  {
    return self::hasParam() ? $_GET[self::getParamName()] : null;
  }


  /**
   * возвращает название таблицы-шарда, откуда следует читать информацию о темах
   * @return string
   * @throws Exception
   */
  public static function getSqlTableNameByParam()
  {
    // костылим нужные экшены
    $app = Yii::app();
    $controller = $app->controller;
    $action     = $controller &&  $controller->action ? $controller->action : null;

    $threadId = null;
    // просмотр темы с www
    if($controller instanceOf ForumController && $action instanceOf ForumThreadAction && isset($_GET['id']) && $_GET['id']) $threadId = $_GET['id'];
    // создание сообщения с www
    if($controller instanceOf ForumController && $action instanceOf ForumAddMessageAction && isset($_POST['thread_id']) && $_POST['thread_id']) $threadId = $_POST['thread_id'];
    // постоянная ссылка на www-сообщение
    if($controller instanceOf ForumController && $action instanceOf ForumMessagePermalink && isset($_GET['id']) && $_GET['id'])
    {
      // @TODO: add cache
      $message = ForumMessage::model()->resetScope()->findByPkOr404($_GET['id']);
      return self::getSqlTableNameByThreadId($message->thread_id);
    }
    // постоянная ссылка на www-тему
    if($controller instanceOf ForumController && $action instanceOf ForumThreadPermalink && isset($_GET['id']) && $_GET['id'])
    {
      return self::getSqlTableNameByThreadId($_GET['id']);
    }
    // просмотр темы с мобилы
    if($controller instanceof \mobile\controllers\ForumController && $action instanceOf CInlineAction && isset($_GET['id']) && $_GET['id']) $threadId = $_GET['id'];
    // создание сообщения с мобилы
    if($controller instanceOf \mobile\controllers\ForumController && $action instanceOf CInlineAction && isset($_POST['ForumMessage']['thread_id']) && $_POST['ForumMessage']['thread_id']) $threadId = $_POST['ForumMessage']['thread_id'];
    // поиск тем с mobile
    if($controller instanceof \mobile\controllers\ForumController && $action instanceOf CInlineAction)
    {
      $year = self::getYearByMobileParam();
      if($year) return self::getSqlTableNameByYear($year);
    }
    // просмотр темы из админки
    if($controller instanceOf ThreadController && $action instanceOf CInlineAction && isset($_GET['id']) && $_GET['id']) $threadId = $_GET['id'];


    if($controller instanceof  ManageController && isset($_GET['shardId']) && in_array($_GET['shardId'], self::getYearList()) ){
        return self::getSqlTableNameByYear($_GET['shardId']);
    }

    if($threadId !== null) return self::getSqlTableNameByThreadId($threadId);

    // поиск тем с www
    if(self::hasParam()) return self::getSqlTableNameByYear(self::getParam());

    // по-умолчанию
    return self::getSqlTableNameDefault();
  }


  // вызовут из MoveActiveForumThreadFromShardCommand после перетаскивания темы в основную таблицу
  public static function onForumThreadRestored(array $arrThreadId)
  {
    $cache = Yii::app()->cache;
    foreach($arrThreadId as $threadId)
    {
      $cacheKey = self::getCacheKeyByThreadId($threadId);
      $cache->set($cacheKey, $tableName = null);  // это именно "дефолтная таблица"
    }
  }


  // возвращает год из аякс-запроса или null
  public static function getYearByMobileParam()
  {
    if(! isset($_GET['fromPathname'])) return null;
    if(! preg_match('#/forum/archive_(\d{4})/#iU', $_GET['fromPathname'], $arrMatch)) return null;
    return $arrMatch[1];
  }


  /**
   * возвращает имя таблицы по-умолчанию
   * @return string
   */
  private static function getSqlTableNameDefault()
  {
    return '{{forum_threads}}';
  }


  /**
   * тема находится в архиве?
   * @param ForumThread | \nt\Forum\Topic $topic
   * @return bool
   */
  public static function isForumThreadArchived($topic)
  {
    return self::getSqlTableNameByThreadId($topic->getId()) != self::getSqlTableNameDefault();
  }


  /**
   * возвращает имя таблицы для указанной темы
   * к сожалению, пришлось сделать public: ForumThread::getById()
   * @param int $threadId
   * @return string
   */
  public static function getSqlTableNameByThreadId($threadId)
  {
    $cache    = Yii::app()->cache;
    $cacheKey = self::getCacheKeyByThreadId($threadId);
    $shardId  = $cache->get($cacheKey);
    if($shardId === false)
    {
      $result = Db::fetch('
        select shard_id
        from {{forum_thread_shard}}
        where thread_id = :thread_id
        limit 1',
        [ ':thread_id' => $threadId, ]);
      $shardId = $result ? $result->shard_id : null;

      $cache->set($cacheKey, $shardId);
    }

    return $shardId ? self::getSqlTableNameByYear($shardId) : self::getSqlTableNameDefault();
  }
  private static function getCacheKeyByThreadId($threadId)
  {
    return implode('~', [ '~10~', __CLASS__, $threadId, ]);
  }
  public static function flushCacheByThreadId($threadId)
  {
    $cache       = Yii::app()->cache;
    $arrThreadId = is_array($threadId) ? $threadId : [ $threadId, ];
    foreach($arrThreadId as $threadId)
    {
      $cacheKey = self::getCacheKeyByThreadId($threadId);
      $cache->set($cacheKey, $value = false);
    }
  }


  /**
   * возвращает название таблицы, где хранятся темы указанного года
   * @param int $year
   * @return string
   * @throws Exception
   */
  public static function getSqlTableNameByYear($year)
  {
    $year = (int) $year;
    if($year < 2009 || $year > 2020) throw new Exception('unknown shard id: '.$year);
    return '{{forum_threads_'.$year.'}}';
  }


  /**
   * возвращает года, за которые у нас может быть архив
   * используется для рисования виджета архива
   * @return int[]
   */
  public static function getYearList()
  {
    return range(2009, 2018);
  }


  /**
   * это самый старый год из нашего архива?
   * используется для рисования виджета архива
   * @param $year
   * @return bool
   */
  public static function isYearOldest($year)
  {
    $array = self::getYearList();
    return reset($array) == $year;
  }


};


