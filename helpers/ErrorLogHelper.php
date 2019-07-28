<?php


// #2522: лог ошибок
class ErrorLogHelper
{

	private static $isInited = false;


	public static function init()
	{
		if(self::$isInited) return;

		register_shutdown_function(function()
		{
		  $arrError = error_get_last();
		  if(! is_array($arrError)) return;

		  self::onError($arrError);
		});

		self::$isInited = true;
	}



	private static function getTrace($objectOrNull = null)
	{
    $arrTrace = $objectOrNull ? $objectOrNull->getTrace() : debug_backtrace();
    $arrTrace = array_map(function($array)
  	{
  		return isset($array['file'], $array['line']) ? $array['file'].' : '.$array['line'] : 'unknown';
  	}, $arrTrace);
    return implode("\n", $arrTrace);
	}


    /**
     * @param Exception $exception
     * @return ErrorLogItem
     */
	public static function createByException(Exception $exception)
	{
    $action = 'not defined';
    if(Yii::app()->controller && Yii::app()->controller->action) $action = get_class(Yii::app()->controller->action);

    return (new ErrorLogItem())
      ->setRowCreatedAtAsNow()
      ->setServerName(php_uname('n'))
      ->setController(get_class(Yii::app()->controller))
      ->setAction($action)
      ->setUri(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '')
      ->setMessage($exception->getMessage())
      ->setFileName($exception->getFile())
      ->setFileLine($exception->getLine())
      ->setTrace(self::getTrace($exception))
      ->setDomain(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown')
      ->setReferer(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'unknown')
      ->setIsHidden(false)
      ->setUserAgent();
	}


	public static function createByError(Error $error)
	{
    $action = 'not defined';
    if(Yii::app()->controller && Yii::app()->controller->action) $action = get_class(Yii::app()->controller->action);

    return (new ErrorLogItem())
      ->setRowCreatedAtAsNow()
      ->setServerName(php_uname('n'))
      ->setController(get_class(Yii::app()->controller))
      ->setAction($action)
      ->setUri(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '')
      ->setMessage($error->getMessage())
      ->setFileName($error->getFile())
      ->setFileLine($error->getLine())
      ->setTrace(self::getTrace($error))
      ->setDomain(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown')
      ->setReferer(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'unknown')
      ->setIsHidden(false)
      ->setUserAgent();
	}


	public static function realOnError(array $arrError)
	{
		$trace = isset($arrError['trace']) ? $arrError['trace'] : self::getTrace();

	  $action = 'unknown';
	  $controller = Yii::app()->controller;
	  if($controller && $controller->action) $action = get_class($controller->action);

	  (new \ErrorLogItem())
	    ->setRowCreatedAtAsNow()
	    ->setServerName(php_uname('n'))
	    ->setController(get_class(Yii::app()->controller))
	    ->setAction($action)
	    ->setUri(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '')
	    ->setMessage($arrError['message'])
	    ->setFileName($arrError['file'])
	    ->setFileLine($arrError['line'])
	    ->setTrace($trace)
	    ->setDomain(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown')
	    ->setReferer(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'unknown')
	    ->setIsHidden(false)
      ->setUserAgent()
	    ->save();
	}
	public static function onError(array $arrError)
	{
	  try
	  {
	  	self::realOnError($arrError);
	  }
	  catch(Exception $e)
	  {
	  	//vd($e, 1);
	  }
	}


	public static function save(ErrorLogItem $errorLogItem)
	{
		if($errorLogItem->getId()) throw new Exception('can not save ErrorLogItem: its has id');

		// хрень какая-то...
		//Db::begin();

    $prefixUri = '';
    if(strpos($errorLogItem->getUri(), '/userapi/asyncContent/getWidget') >= 0) $prefixUri = isset($_GET['widget']) ? '?widget='.$_GET['widget'] : '';

		$res = Yii::app()->db->createCommand('
			insert into {{error_log}} (row_created_at, server_name, controller, action, uri, message, file_name, file_line, trace, domain, referer, is_hidden, user_agent)
			values (:row_created_at, :server_name, :controller, :action, :uri, :message, :file_name, :file_line, :trace, :domain, :referer, :is_hidden, :user_agent)')->execute(
			[
				':row_created_at' => $errorLogItem->getRowCreatedAt(),
				':server_name'    => $errorLogItem->getServerName(),
				':controller'     => $errorLogItem->getController(),
				':action'         => $errorLogItem->getAction(),
				':uri'            => $errorLogItem->getUri() . $prefixUri,
				':message'        => $errorLogItem->getMessage(),
				':file_name'      => $errorLogItem->getFileName(),
				':file_line'      => $errorLogItem->getFileLine(),
				':trace'          => $errorLogItem->getTrace().'',
				':domain'         => $errorLogItem->getDomain().'',
				':referer'        => $errorLogItem->getReferer().'',
				':is_hidden'      => $errorLogItem->getIsHidden() ? 1 : 0,
        ':user_agent'     => $errorLogItem->getUserAgent() ?: '',
			]);
		//Db::commit();

		$cacheKey = self::getCacheKeyInfoForWatchDog();
		Yii::app()->cache->set($cacheKey, $value = false);
	}



	private static function getCacheKeyInfoForWatchDog()
	{
		return __CLASS__.'~'.'info_for_watchdog';
	}


	private static function getSqlWhereSkipUnused() : string
  {
    $skipByPredtest = php_uname('n') == 'vi8100predtest' ? " and message not like '%last visible topics day diff too big:%' " : '';

    return "
$skipByPredtest
and message != 'Страница не найдена'
and message != 'Страница не существует'
and message != 'File Upload Mime headers garbled'
and message != 'title is empty'
and message != 'article id invalid'
and message != 'contest id invalid'
and message !~ '^email invalid: .+\.(top|xyz)$'
and message not like '%Object not in prerequisite state%woman_comment_likes_id_seq%'
and message not like 'can%t find ForumThread'
and message not like 'Невозможно обработать запрос%'
and message not like 'Системе не удалось найти запрашиваемое действие%'
and message not like 'query for unexists forum topic page:%'
and message not like 'current user can not see \\\\Forum\\\\Topic by id:%'
and message not like 'forum hidden by section settings: 277'
and not (message like 'can%t find Publication' and uri like '/WomanTV/%') ";
  }

	
	private static function getInfoForWatchDogFromDb()
	{
    $result = Yii::app()->db->createCommand("
			select to_char(row_created_at, 'YYYY-MM-DD HH24:MI:00') as date, count(*) as cnt
			from {{error_log}}
			where 1 = 1
			  and row_created_at > (NOW() - '15 MINUTES'::INTERVAL)
        ".self::getSqlWhereSkipUnused()."
			group by date")->queryAll();
    $arrLog = [];
    foreach($result as $item)
    {
      $arrLog[$item['date']] = $item['cnt'];
    }

    return $arrLog;
	}
	public static function getInfoForWatchDog()
	{
		$cache           = Yii::app()->cache;
		$cacheKey        = self::getCacheKeyInfoForWatchDog();
		$arrErrorInfo    = $cache->get($cacheKey);
		$isDataFromCache = $arrErrorInfo !== false;
		if(! $isDataFromCache)
		{
			$arrErrorInfo = self::getInfoForWatchDogFromDb();	
			$cache->set($cacheKey, $arrErrorInfo);
		}

    for($time = strtotime('-15 minutes'); $time <= time(); $time += 60)
    {
      $date = date('Y-m-d H:i:00', $time);
      if(! isset($arrErrorInfo[$date])) $arrErrorInfo[$date] = 0;
    }

    return 
    [
			'time'               => Yii::app()->db->createCommand('SELECT date_part(\'epoch\', CURRENT_TIMESTAMP)::int')->queryScalar(),
			'is_data_from_cache' => $isDataFromCache,
			'error'              => $arrErrorInfo,
    ];
	}


	public static function getForDisplayOnAdminPage()
	{
		$arrErrorLogItem = [];
		$result = Yii::app()->db->createCommand("
			select id, row_created_at, server_name, controller, action, uri, message, file_name, file_line, trace, domain, referer, is_hidden, user_agent
			from {{error_log}}
			where 1 = 1
        ".self::getSqlWhereSkipUnused()."
				and is_hidden = 0
			order by row_created_at desc
			limit 25")->queryAll();
		foreach($result as $res)
		{
			$errorLogItem = (new ErrorLogItem())
				->setId($res['id'])
		    ->setRowCreatedAt($res['row_created_at'])
		    ->setServerName($res['server_name'])
		    ->setController($res['controller'])
		    ->setAction($res['action'])
		    ->setUri($res['uri'])
		    ->setMessage($res['message'])
		    ->setFileName($res['file_name'])
		    ->setFileLine($res['file_line'])
		    ->setTrace($res['trace'])
				->setDomain($res['domain'])
				->setReferer($res['referer'])
				->setIsHidden($res['is_hidden'])
			    ->setUserAgent($res['user_agent']);
		  $arrErrorLogItem[$errorLogItem->getId()] = $errorLogItem;
		}
		return $arrErrorLogItem;
	}


	public static function hideAll()
	{
		return Yii::app()->db->createCommand('
			update {{error_log}} 
			set   is_hidden = 1 
			where is_hidden = 0')->execute();
	}


  public static function createByMessage($message)
  {
    $trace = self::getTrace();
    self::realOnError(['message' => $message, 'trace' => $trace, 'file' => __FILE__, 'line' => __LINE__]);
  }


};

