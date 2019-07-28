<?php

// #2522: лог ошибок
class ErrorLogItemBase
{
	use TraitHasRowCreatedAt;


	private $id         = null;
	private $serverName = null;
	private $controller = null;
	private $action     = null;
	private $uri        = null;
	private $message    = null;
	private $fileName   = null;
	private $fileLine   = null;
	private $trace      = null;
	private $domain     = null;
	private $referer    = null;
	private $isHidden   = null;
	private $userAgent  = null;


	public function setId($id)
	{
	  $this->id = $id;
	  return $this;
	}
	public function getId()
	{
	  return $this->id;
	}

	
	public function setServerName($serverName)
	{
	  $this->serverName = $serverName;
	  return $this;
	}
	public function getServerName()
	{
	  return $this->serverName;
	}

	
	public function setController($controller)
	{
	  $this->controller = $controller;
	  return $this;
	}
	public function getController()
	{
	  return $this->controller;
	}
	

	public function setAction($action)
	{
	  $this->action = $action;
	  return $this;
	}
	public function getAction()
	{
	  return $this->action;
	}

	
	public function setUri($uri)
	{
	  $this->uri = $uri;
	  return $this;
	}
	public function getUri()
	{
	  return $this->uri;
	}

	
	public function setMessage($message)
	{
	  $this->message = $message;
	  return $this;
	}
	public function getMessage()
	{
	  return $this->message;
	}
	

	public function setFileName($fileName)
	{
	  $this->fileName = $fileName;
	  return $this;
	}
	public function getFileName()
	{
	  return $this->fileName;
	}
	

	public function setFileLine($fileLine)
	{
	  $this->fileLine = $fileLine;
	  return $this;
	}
	public function getFileLine()
	{
	  return $this->fileLine;
	}
	

	public function setTrace($trace)
	{
	  $this->trace = $trace;
	  return $this;
	}
	public function getTrace()
	{
	  return $this->trace;
	}
	

	public function setDomain($domain)
	{
	  $this->domain = $domain;
	  return $this;
	}
	public function getDomain()
	{
	  return $this->domain;
	}
	
	
	public function setReferer($referer)
	{
	  $this->referer = $referer;
	  return $this;
	}
	public function getReferer()
	{
	  return $this->referer;
	}
	

	public function setIsHidden($isHidden)
	{
	  $this->isHidden = $isHidden;
	  return $this;
	}
	public function getIsHidden()
	{
	  return $this->isHidden;
	}

	public function setUserAgent(string $userAgent = null)
    {
        $this->userAgent = $userAgent;
        if($userAgent == null){
            $this->userAgent = !\Yii::app() instanceof \WomanConsoleApplication &&
                                isset(\Yii::app()->user) &&
                                isset(\Yii::app()->user->userAgent) ?
                                    \Yii::app()->user->userAgent :
                                    'not user agent';
        }

        return $this;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

};
