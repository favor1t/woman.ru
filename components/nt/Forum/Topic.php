<?php

declare(strict_types = 1);

namespace nt\Forum;

/**
 * тема форума
 * Class Topic
 */
class Topic extends \nt\Forum\Topic\TopicBase
{
  use
    \nt\traits\EntityGetById,
    \nt\traits\EntitySaveInCache,
    \nt\traits\EntityFromArray,
    \nt\traits\EntityVote;

  /**
   * указанный юзер может просматривать тему?
   * @param \WebUser $user
   * @return bool
   */
  public function canUserSee(\WebUser $user) : bool
  {
    if($this->itTypeConsultation()) return false;
    if($this->isStatusVisible()) return true;
    return $user->checkAccess('loadWaitingThreads');
  }


  /**
   * @param array $arrParam
   * @return \nt\Forum\Topic[]
   */
  public static function getByParam(array $arrParam)
  {
    return \nt\Forum\Topic\Manager::getByParam($arrParam);
  }
  /**
   * @param array $arrParam
   * @return int
   */
  public static function getCount(array $arrParam) : int
  {
    return \nt\Forum\Topic\Manager::getCount($arrParam);
  }


  /**
   * форматирование
   * @return string
   */
  public function getAnswerCountAllFormatted() : string
  {
    return number_format($this->getAnswerCountAll(), 0, '.', ' ');
  }


  /**
   * возвращает максимальный номер страницы
   * @return int
   */
  public function getPageNumberMax() : int
  {
    return (int) ceil($this->getAnswerCountAll() / \Yii::app()->params['limits']['messagesOnThread']);
  }
  public function getPageCount() : int
  {
    return $this->getPageNumberMax();
  }


  /**
   * @return string
   */
  public function getBodyBrToP() : string
  {
    return \TextHelper::brToP($this->getBody());
  }


  /**
   * перекроем родителя ради получения массива
   * @TODO: вытащить в исходный трейт
   * @param int | int[] $idOrArrayOfId
   * @param array $arrOption
   * @return \nt\Forum\Topic | \nt\Forum\Topic[]
   */
  public static function getById($idOrArrayOfId, array $arrOption = [])
  {
        if(is_numeric($idOrArrayOfId))
        {
          return \nt\Forum\Topic\Manager::getById((int) $idOrArrayOfId);
        }

        $arrForumTopic = [];

        if (!is_null($idOrArrayOfId)) {
            foreach($idOrArrayOfId as $forumTopicId)
            {
                $forumTopic = self::getById($forumTopicId);
                $arrForumTopic[$forumTopic->getId()] = $forumTopic;
            };

            if(isset($arrOption['sort']) == 'id desc')
            {
                usort($arrForumTopic, function(\nt\Forum\Topic $topic0, \nt\Forum\Topic $topic1) : int
                {
                    return $topic1->getId() - $topic0->getId();
                });
            }
            if(isset($arrOption['sort']) == 'answer count 1d desc')
            {
                usort($arrForumTopic, function(\nt\Forum\Topic $topic0, \nt\Forum\Topic $topic1) : int
                {
                    return $topic1->getAnswerCount1d() - $topic0->getAnswerCount1d();
                });
            }
        }

        return $arrForumTopic;
  }


  /**
   * сахар к статусу темы
   * @return bool
   */
  public function isStatusOpen() : bool
  {
    return $this->getStatus() == \ForumThread::STATUS_OPEN;
  }
  /**
   * сахар к статусу темы
   * @return bool
   */
  public function isStatusClosed() : bool
  {
    return $this->getStatus() == \ForumThread::STATUS_CLOSED;
  }
  /**
   * сахар к статусу темы
   * @return bool
   */
  public function isStatusVisible() : bool
  {
    return $this->isStatusOpen() || $this->isStatusClosed();
  }
  /**
   * cахар к статусу темы
   * @return bool
   */
  public function canWriteMessage() : bool
  {
    return $this->isStatusOpen();
  }


  /**
   * сахар к типу темы
   * @return bool
   */
  public function itTypeConsultation() : bool
  {
    return $this->getType() == \ForumThread::TYPE_CONSULTATION;
  }


  /**
   * сахар к режиму комментирования
   * @return bool
   */
  public function isCommentModeRegistred() : bool
  {
    return $this->getCommentMode() == \ForumThread::COMMENTS_MODE_REGISTERED;
  }


  /**
   * возвращает ссылку на тему
   * @param bool $absolute
   * @param array $arrParam
   * @return string
   * @throws \Exception
   */
  public function getSiteUrl(bool $absolute = false, array $arrParam = []) : string
  {
    $arrParamUrl = [];
    foreach([ 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', ] as $param)
    {
      if(isset($arrParam[$param])) $arrParamUrl[] = urlencode($param).'='.urlencode($arrParam[$param]);
    }
    $paramUrl = implode('&', $arrParamUrl);

    return ($absolute ? \Yii::app()->params['baseUrl'] : '')
      .'/'.$this->getSectionMain()->getWebName()
      .'/'.$this->getSubSection()->getWebName()
      .'/thread/'.$this->getId()
      .'/'.((isset($arrParam['page']) && $arrParam['page'] > 1) ? $arrParam['page'].'/' : '')
      .($paramUrl != '' ? '?'.$paramUrl : '')
      .(isset($arrParam['anchor']) ? '#'.$arrParam['anchor'] : '');
  }
  public function getUrl(bool $absolute = false, array $arrParam = []) : string
  {
    return $this->getSiteUrl($absolute, $arrParam);
  }


  /**
   * вызовут при добавлении сообщения к теме форума: инкрементируем счетчик сообщений в кеше
   * @param int $forumTopicId
   */
  public static function onMessageCreated(int $forumTopicId) : void
  {
    $forumTopic = self::getByIdFromCache($forumTopicId);
    if($forumTopic) $forumTopic->incrementAnswerCountAll()->saveInCache();
  }


  /**
   * увеличивает счетчик "сообщений всего"
   * @return $this
   */
  public function incrementAnswerCountAll() : self
  {
    return $this->setAnswerCountAll($this->getAnswerCountAll() + 1);
  }


  /**
   * обновляет данные в кеше в случае измнения модели на вумане
   * @param \ForumThread $forumThread
   */
  public static function onWomanModelChanged(\ForumThread $forumThread) : void
  {
    $forumTopic = self::getByIdFromCache((int) $forumThread->id);
    if(! $forumTopic) return;

    // CDbExpression::expression == 'NOW()'
    $updatedAt = $forumThread->updated_at;
    while(true)
    {
      if(! $updatedAt instanceOf \CDbExpression) break;
      if($updatedAt->expression == 'NOW()')
      {
        $updatedAt = date('Y-m-d H:i:s');
        break;
      }
      throw new Exception('unknown expression: '.$updatedAt->expression);
    }

    $imageCollection = \nt\Image\Collection::fromArray($forumThread->images->toArrayForDb());

    $forumTopic
      ->setStatus((int) $forumThread->status)
      ->setStatusExt((int) $forumThread->status_ext)
      ->setType($forumThread->type)
      ->setSectionId($forumThread->sections)
      ->setTagId($forumThread->tags)
      ->setAnswerCountAll($forumThread->answers_all)
      ->setAnswerCountVisible($forumThread->answers)
      ->setAnswerCount3h($forumThread->answers_3h)
      ->setAnswerCount12h($forumThread->answers_12h)
      ->setAnswerCount1d($forumThread->answers_1d)
      ->setAnswerCount3d($forumThread->answers_3d)
      ->setAnswerCount7d($forumThread->answers_7d)
      ->setAnswerCount30d($forumThread->answers_30d)
      ->setCreatedAt($forumThread->created_at)
      ->setUpdatedAt($updatedAt)
      ->setIsAnonymous((bool) $forumThread->anonymously)
      ->setAnonymousId((string) $forumThread->anonymous_id)
      ->setName($forumThread->name)
      ->setTitle($forumThread->title)
      ->setBody((string)$forumThread->body)
      ->setUserId($forumThread->user_id)
      ->setUserName($forumThread->user_name)
      ->setUserAvatarId((int) $forumThread->userpic_id)
      ->setCommentMode((int) $forumThread->comments_mode)
      ->setDateLastComment((string)$forumThread->last_comment_date)
      ->setUserAgent($forumThread->user_agent)
      ->setUserCookie($forumThread->user_cookie)
      ->setUserIp($forumThread->user_ip)
      ->setTitlePostfix($forumThread->title_postfix)
      ->setImageCollection($imageCollection)
      ->saveInCache();
  }

  /*
   * Модель Topic в ForumThread
   */
  public static function convertToForumThread(array $array) : array {

      return array_map(function($topic)
      {
          $forumThread = new \ForumThread();

          $forumThread->id                = $topic->getId();
          $forumThread->status            = $topic->getStatus();
          $forumThread->status_ext        = $topic->getStatusExt();
          $forumThread->type              = $topic->getType();
          $forumThread->sections          = $topic->getSectionId();
          $forumThread->tags              = $topic->getTagId();
          $forumThread->answers_all       = $topic->getAnswerCountAll();
          $forumThread->answers           = $topic->getAnswerCountVisible();
          $forumThread->answers_3h        = $topic->getAnswerCount3h();
          $forumThread->answers_12h       = $topic->getAnswerCount12h();
          $forumThread->answers_1d        = $topic->getAnswerCount1d();
          $forumThread->answers_3d        = $topic->getAnswerCount3d();
          $forumThread->answers_7d        = $topic->getAnswerCount7d();
          $forumThread->answers_30d       = $topic->getAnswerCount30d();
          $forumThread->created_at        = $topic->getCreatedAt();
          $forumThread->updated_at        = $topic->getUpdatedAt();
          $forumThread->anonymously       = $topic->getIsAnonymous();
          $forumThread->anonymous_id      = $topic->getAnonymousId();
          $forumThread->name              = $topic->getName();
          $forumThread->title             = $topic->getTitle();
          $forumThread->body              = $topic->getBody();
          $forumThread->user_id           = $topic->getUserId();
          $forumThread->user_name         = $topic->getUserName();
          $forumThread->userpic_id        = $topic->getUserAvatarId();
          $forumThread->comments_mode     = $topic->getCommentMode();
          $forumThread->user_agent        = $topic->getUserAgent();
          $forumThread->user_cookie       = $topic->getUserCookie();
          $forumThread->user_ip           = $topic->getUserIp();
          $forumThread->title_postfix     = $topic->getTitlePostfix();
          $forumThread->last_comment_date = $topic->getDateLastComment();
          return $forumThread;

      }, $array);
  }

  public function asForumThread() : \ForumThread
  {
      return current(self::convertToForumThread([$this]));
  }


  /**
   * @return array
   */
  public function asArrayForJs() : array
  {
    return
    [
      'topicId'            => $this->getId(),
      'name'               => $this->getName(),
      'createdAt'          => $this->getCreatedAt(),
      'status'             => $this->getStatusAsString(),
      'answerCountAll'     => $this->getAnswerCountAll(),
      'answerCountVisible' => $this->getAnswerCountVisible(),
    ];
  }
  public function getStatusAsString() : string
  {
    $array =
    [
      \IForumThreadStatus::STATUS_AWAITING => 'awaiting',
      \IForumThreadStatus::STATUS_OPEN     => 'open',
      \IForumThreadStatus::STATUS_CLOSED   => 'closed',
      \IForumThreadStatus::STATUS_HIDE     => 'hidden',
    ];
    $status = $this->getStatus();
    if(! isset($array[$status])) throw new \Exception('can not get status as string by status: '.$status);
    return $array[$status];
  }


  /**
   * возвращает отображаемое имя автора (с учетом анонимности)
   * @return string
   */
  public function getUserNameDisplay() : string
  {
    $userName = $this->getUserName();
    if(! $this->isAnonymous() && $userFromTopic = $this->getUserOrNull()) $userName = $userFromTopic->getName();
    return $userName ?: 'Автор';
  }


  /**
   * возвращает информацию о первом сообщении за указанный период
   * используется для создания ссылки "сообщений за три часа", "сообщений за сутки"...
   * может вернуть false
   * @TODO: замерить производительность
   * @param string $period
   * @return array | bool
   */
  public function getFirstMessageInfoByPeriod(string $period)
  {
    return \ForumThread::getPageAndMessageByTimestamp($this->getId(), $period);
  }


  /**
   * тема находится в архиве?
   * используется для "надо отображать количество сообщений за период?"
   * @return bool
   */
  public function isArchived() : bool
  {
    return \ForumThreadArchiveHelper::isForumThreadArchived($this);
  }


  /**
   * возвращает сообщения указанной страницы
   * @param int $page
   * @return \nt\Forum\Message[]
   */
  public function getMessageByPage(int $page) : array
  {
    return \nt\Forum\Message::getByTopicAndPage($this, $page);
  }


  /**
   * @param \ForumMessage | mobile\models\forum\Message $forumMessage
   * @return Topic
   */
  public function onForumMessageChanged($forumMessage) : self
  {
    \nt\Forum\Topic::flushCacheById($this->getId());
    return $this;
  }


  /**
   * #1681: расчет популярных секций форума
   * @return array
   */
  public static function getPopularSectionInfo() : array
  {
    return \nt\Forum\Topic\Manager::getPopularSectionInfo();
  }


  // это ни разу не проверка, это редирект
  public function checkUrl()
  {
      preg_match('#^/([^/]+/[^/]+)/thread/#iU', $_SERVER['REQUEST_URI'], $arrMatchCurrent);
      preg_match('#^/([^/]+/[^/]+)/thread/#iU', $this->getSiteUrl(), $arrMatchValid);

      if (isset($arrMatchValid[1], $arrMatchCurrent[1]) && $arrMatchCurrent[1] == $arrMatchValid[1]) return;

      \Yii::app()->controller->redirect($this->getSiteUrl(), $terminate = true, $statusCode = 301);
  }

  public function redirectToLastSeenMessage(int $pageNumber = 0)
  {
    $offset = isset($_GET['lastSeenMessageDiff']) ? (int) $_GET['lastSeenMessageDiff'] + 1 : false; //тк отсчет от 0
    if($offset === false || $offset < 1) return;

    $newPageNumber = (int) ceil(( $this->getAnswerCountAll() - $offset) / \Yii::app()->params['limits']['messagesOnThread']);

    //if($newPageNumber == $pageNumber) return;

    $arrForumMessage = $this->getMessageByPage($newPageNumber);
    $messageNumber   = count($arrForumMessage) - $offset % \Yii::app()->params['limits']['messagesOnThread'];
    if($messageNumber < 0) $messageNumber = 0;
    $messageId = isset($arrForumMessage[$messageNumber]) ? $arrForumMessage[$messageNumber]->getId() : '';

    $params = $_GET;
    $addToUrl = [];
    foreach ($params as $index => $value){
      if(in_array($index, ['wic', 'wil', 'wid'])) $addToUrl[$index] = $value;
    }

    $uri = $this->getSiteUrl($absolute = false);
    if($newPageNumber > 1) $uri .= $newPageNumber . '/';
    $uri = \UrlHelper::addParametersToUrl($uri,$addToUrl);
    $uri .= '#m'.$messageId;
    \Yii::app()->controller->redirect($uri, $terminate = true);

  }

  public function hasSection() : bool
  {
      if( empty( $this->getSectionId() ) ) return false;

      foreach ( $this->getSectionId() as $sectionId)
          if($sectionId < 1) return false;

      return true;
  }

  /**
   * @return bool
   */
  public function isAmp(): bool
  {
    return $this->isPsychologies();
  }

  /**
   * @todo почему-то getSectionId() не отдает родительскую секцию.
   * @return bool
   */
  public function isPsychologies(): bool
  {
    return ($this->getSectionMain()  && \Section::SECTION_ID_PSYCHO === $this->getSectionMain()->getId());
  }

  /**
   * @return bool
   */
  public function isExpertTopic(): bool
  {
    return in_array(\Yii::app()->params['siteExpertTagId'], $this->getTagId());
  }
};

