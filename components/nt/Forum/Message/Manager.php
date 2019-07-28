<?php

declare(strict_types = 1);

namespace nt\Forum\Message;

/**
 * манагер для работы с сообщениями тем форума
 * Class Manager
 */
class Manager
{
  use
    \nt\traits\SqlWhereByNewStatus,
    \nt\traits\SqlWhereByTopicId;
  /**
   * возвращает сообщение по ID
   * @param int $forumMessageId
   * @return \nt\Forum\Message
   */
  public static function getById(int $forumMessageId) : \nt\Forum\Message
  {
    $array = \Db::fetchAsArray('      
      select id, thread_id topic_id, status, user_id, anonymously is_anonymous, anonymous_id, body, created_at, user_agent, user_cookie, user_ip, user_name, _extra
      from   {{forum_messages}}
      where  id = :id', [ ':id' => $forumMessageId, ]);
    if(! $array) throw new \Exception('can not get forum message by id: ', $forumMessageId);

    $array = self::parseExtra($array);
    return \nt\Forum\Message::fromArray($array);
  }
  /**
   * возвращает сообщение по ID
   * @param int $forumMessageId
   * @return \nt\Forum\Message
   */
  public static function getByIdOrNull(int $forumMessageId) : ?\nt\Forum\Message
  {
    $array = \Db::fetchAsArray('      
      select id, thread_id topic_id, status, user_id, anonymously is_anonymous, anonymous_id, body, created_at, user_agent, user_cookie, user_ip, user_name, _extra
      from   {{forum_messages}}
      where  id = :id', [ ':id' => $forumMessageId, ]);
    if(! $array) return null;

    $array = self::parseExtra($array);
    return \nt\Forum\Message::fromArray($array);
  }
  /**
   * возвращает массив сообщений указанной темы с указанной страницы
   * @param \nt\Forum\Topic $forumTopic
   * @param int $page
   * @return \nt\Forum\Message[]
   */
  public static function getByTopicAndPage(\nt\Forum\Topic $forumTopic, int $page) : array
  {
    $cacheKey = self::getCacheKeyForumMessageByForumTopicIdAndPage($forumTopic->getId(), $page);
    return \nt\Cache::get($cacheKey, $entityId = null, function() use ($forumTopic, $page)
    {
      return self::realGetByTopicAndPage($forumTopic, $page);
    });
  }
  /**
   * возвращает массив сообщений указанной темы с указанной страницы
   * @param \nt\Forum\Topic $forumTopic
   * @param int $page
   * @return \nt\Forum\Message[]
   */
  private static function realGetByTopicAndPage(\nt\Forum\Topic $forumTopic, int $page = null, bool $onlyExpert = false) : array
  {
    // информация из индексной таблицы о первом сообщении на нужной странице
    if($page !== null)
        $arrMessageInfo = self::getMessageInfo($forumTopic, $page);

    if(!in_array(\Tag::getSiteExpertId(), $forumTopic->getTagId()) && $onlyExpert === true) return [];
    //{
    //убрал из выборки сообщения от экспертов WMN-745 + WMN-
    $expertIds = count(\ExpertHelper::getUserIdsBySection($forumTopic)) > 0 ? implode(\ExpertHelper::getUserIdsBySection($forumTopic), ',') : null ;

    //для случая, когда выбираются только сообщения от экспертов, но экспертов нет
    if($expertIds === null && $onlyExpert === true)
        return [];

    if($expertIds !== null)
        $whereByUserId = ' and user_id '.($onlyExpert ? ' in ' : ' not in ').' ('.$expertIds.')';

    if($expertIds !== null && $onlyExpert === true)
        $whereStatusIsOpen = ' and status = '.\ForumMessage::STATUS_ON;

    //}

    // выборка сообщений из БД
    $array = \Db::fetchAllAsArray('
      select id, thread_id topic_id, status, user_id, anonymously is_anonymous, anonymous_id, body, created_at, user_agent, user_cookie, user_ip, user_name, _extra
      from {{forum_messages}}
      where 1 = 1 '.
        ($whereByUserId ?? '') .
        ($whereStatusIsOpen ?? '') .'
        and thread_id = :thread_id
        '.(isset($arrMessageInfo['message_id']) ? ' and id >= '.(int) $arrMessageInfo['message_id'] : '').'
      order by id
      '.(isset($arrMessageInfo['offset']) ? ' offset '.(int) $arrMessageInfo['offset'] : '').'
      limit :limit',
      [ ':thread_id' => $forumTopic->getId(), ':limit' => self::getMessageCountPerPage(), ]);

    // конструирование сообщений
    return array_map(function(array $array)
    {
      $array = self::parseExtra($array);
      return \nt\Forum\Message::fromArray($array);
    }, $array);
  }


  /**
   * выдирает значения из экстры, запихивает их в массив
   * @param array $array
   */
  private static function parseExtra(array $array) : array
  {
    $arrExtra = json_decode($array['_extra'], $doArray = true);
    $array['user_avatar_id']   = isset($arrExtra['userpic_id']) ? (int) $arrExtra['userpic_id'] : 0;
    $array['image_collection'] = isset($arrExtra['images'])     ? $arrExtra['images']           : [];
    $array['image_collection'] = \nt\Image\Collection::fromArray($array['image_collection']);
    unset($array['_extra']);

    // cast to need type
    $array['is_anonymous'] = (bool) $array['is_anonymous'];
    $array['user_ip']      = (string) $array['user_ip'];

    return $array;
  }


  /**
   * возвращает информацию из индексной таблицы
   * суть в том, что в индексной таблице есть записи не для всех страниц темы форума, поэтому приходиццо извращаццо
   * @param \nt\Forum\Topic $forumTopic
   * @param int $page
   * @return array
   */
  private static function getMessageInfo(\nt\Forum\Topic $forumTopic, int $page) : array
  {
    return \nt\Cache::get(static::class.'~'.'message_info', $entityId = $forumTopic->getId().'~'.$page, function() use ($forumTopic, $page)
    {
      $result = \Db::fetch('
        select message_id, page
        from {{forum_thread_pages}}
        where thread_id = :thread_id and page <= :page
        order by page desc
        limit 1',
        [ ':thread_id' => $forumTopic->getId(), ':page' => $page, ]);
      return
      [
        'message_id' => $result ? $result->message_id : null,
        'offset'     => $result ? ($page - $result->page) * self::getMessageCountPerPage() : null,
      ];
    });
  }


  /**
   * возвращает количество сообщений на страницу форума
   * тупо алиас, чтобы меньше писать
   * @return int
   */
  private static function getMessageCountPerPage() : int
  {
    return \Yii::app()->params['limits']['messagesOnThread'];
  }


  /**
   * возвращает ключ кеша для хранения массивая сообщений указанной темы и ейной страницы
   * @param int $forumThreadId
   * @param int $page
   * @return string
   */
  private static function getCacheKeyForumMessageByForumTopicIdAndPage(int $forumThreadId, int $page) : string
  {
    return static::class.'~'.'message_list'.'~'.$forumThreadId.'~'.$page;
  }


  /**
   * сносит сообщения из кеша в случае изменения модели вумана
   * @param \ForumMessage | mobile\models\forum\Message $forumMessage
   */
  public static function onWomanForumMessageChanged($forumMessage) : void
  {
    $page     = self::getPageByForumMessage($forumMessage);
    $cacheKey = self::getCacheKeyForumMessageByForumTopicIdAndPage((int) $forumMessage->thread_id, $page);
    \nt\Cache::delete($cacheKey, $entityId = null);
  }


  /**
   * возвращает номер страницы на основании сообщения
   * нуна для сноса кеша при изменении сообщения
   * @param \ForumMessage | mobile\models\forum\Message $forumMessage
   * @return int
   */
  private static function getPageByForumMessage($forumMessage) : int
  {
    // не смог в CTE, поэтому через два запроса
    // суть в том, что в индексной таблице есть записи не для всех страниц темы форума, поэтому приходиццо извращаццо

    // найдем ближайшую известную страницу
    $resultPage = \Db::fetch('
      select page, message_id
      from {{forum_thread_pages}}
      where thread_id = :thread_id and message_id <= :message_id
      order by page desc
      limit 1',
      [
        ':thread_id'  => $forumMessage->thread_id,
        ':message_id' => $forumMessage->id,
      ]);
    if(! $resultPage) return 1;

    // узнаем количество сообщений между известным и переданным нам сообщениями
    $resultMessage = \Db::fetch('
      select count(*) message_count
      from {{forum_messages}}
      where 1 = 1
        and thread_id = :thread_id
        /* ни фига не уверен, где именно нужно строгое сравнение, но тесты прошли */
        and :id_0 < id and id <= :id_1',
      [
        ':thread_id' => $forumMessage->thread_id,
        ':id_0'      => $resultPage->message_id,
        ':id_1'      => $forumMessage->id,
      ]);
    if(! $resultMessage) return $resultPage->page;

    $page = $resultPage->page + floor($resultMessage->message_count / self::getMessageCountPerPage());
    return (int) $page;
  }

    /**
     * возвращает массив сообщений экспертов в указанной теме.
     * @param \nt\Forum\Topic $forumTopic
     * @return \nt\Forum\Message[]
     */
    public static function getMessageExpertByTopic(\nt\Forum\Topic $forumTopic) : array
    {
        $cacheKey = self::getCacheKeyForumMessageExpertByForumTopicId($forumTopic);
        return \nt\Cache::get($cacheKey, $entityId = null, function() use ($forumTopic)
        {
            return self::realGetByTopicAndPage($forumTopic, $page = null, $onlyExpert = true);
        });
    }

    /**
     * возвращает ключ кеша для хранения массивая сообщений экспертов в указанной теме.
     * @param int $forumThreadId
     * @return string
     */
    private static function getCacheKeyForumMessageExpertByForumTopicId(\nt\Forum\Topic $forumTopic) : string
    {
        return static::class.'~'.'message_list_expert'.'~'.$forumTopic->getId();
    }

    /*
     * для сброса кеша при добавлении комментария эксперта в ForumAddMessageAction
     */
  /**
   * @param \nt\Forum\Topic $forumTopic
   * @return string
   */
  private static function getCacheKeyExpert(\nt\Forum\Topic $forumTopic) : string
    {
        return self::getCacheKeyForumMessageExpertByForumTopicId($forumTopic);
    }

  /**
   * @param \nt\Forum\Topic $forumTopic
   * @return bool
   */
  public static function onChangedExpert(\nt\Forum\Topic $forumTopic)
    {
        $cacheKey = self::getCacheKeyExpert($forumTopic);
        return \nt\Cache::delete($cacheKey, $entityId = null);
    }


    /*
    public static function getLastAnswerFromExperts() : array
    {
        $cacheKey = self::getCacheKeyByLastExpertMessage();
        return \nt\Cache::get($cacheKey, $entityId = null, function()
        {
            return self::getRealLastAnswerFromExperts();
        }, $expire = 60*60);
    }

    private static function getCacheKeyByLastExpertMessage() : string
    {
        return static::class.'~'.'last_expert_message';
    }

    private static function getRealLastAnswerFromExperts() : array
    {
      return [];

        // выборка сообщений из БД WMN-1584
        $array = \Db::fetchAll('
                    select *
                    from
                    (
                     select i.user_id,
                     (
                       select created_at
                       from {{forum_messages}} m
                       where m.user_id = i.user_id and m.created_at > now() - interval \'30 days\'
                       order by created_at desc
                       limit 1
                     ) created_at_message_last
                     from {{expert_info}} i
                    ) z
                    where created_at_message_last is not null
                    order by created_at_message_last desc
      ');

        $result = [];
        foreach($array as $obj){
            $result[$obj->user_id] = User::getByIdOrNull($obj->user_id);
        }

        return $result;
    }
    */


  /**
   * #1644: последние ответы экспертов
   * @return array
   */
  public static function getForumExpertLastAnswerInfo() : array
  {
    /*
    return \Db::fetchAllAsArray("
      select *
      from
      (
       select i.user_id,
       (
         select created_at
         from {{forum_messages}} m
         where m.user_id = i.user_id and m.created_at > now() - interval '30 days'
         order by created_at desc
         limit 1
       ) created_at_message_last
       from {{expert_info}} i
      ) z
      where created_at_message_last is not null
      order by created_at_message_last desc");
    */
    return \Db::fetchAllAsArray("
      with m as 
      (
        select user_id, created_at
        from woman_forum_messages
        where created_at > now() - interval '30 days'
      ) 
      select user_id_woman user_id, max(m.created_at) created_at_message_last
      from woman_user_expert_b17 e
      join m on m.user_id = e.user_id_woman
      group by user_id_woman
      order by created_at_message_last desc");
  }


  /**
   * #1683: последние темы с ответами экспертов
   * @return array
   * @throws \Exception
   */
  public static function getForumExpertTopicWithAnswerLastInfo() : array
  {
    $tagId = (int) \Yii::app()->params['siteExpertTagId'];
    if(! $tagId) throw new \Exception('site expert tag id not defined');

    return \Db::fetchAllAsArray("
      select t.id topic_id, t.created_at topic_created_at, t.answers_all as answers_cnt, m.user_id user_id_expert, m.created_at answer_created_at
      from woman_forum_threads t
      join woman_forum_messages m on m.thread_id = t.id
      join woman_user_expert_b17 e on e.user_id_woman = m.user_id
      where 1 = 1
        and t.status in (".\ForumThread::STATUS_OPEN.", ".\ForumThread::STATUS_CLOSED.")
        and t.tags && array[${tagId}]
        and t.created_at > now() - interval '30 days'
      order by t.created_at desc
      limit 5");
  }

  /**
   * @param array $arrParam
   * @return [\nt\Forum\Topic[] \nt\Forum\Messages[]] | [int,int]
   * @throws \Exception
   */
  public static function getByParam(array $arrParam, bool $onlyTopics = true)
  {
    $createdAtStart = $arrParam['created_at_start'] ?? null;

    $arrSqlWhere = array_filter(
      [
        self::getSqlWhereByStatus($arrParam),
        self::getSqlWhereByUser($arrParam),
        self::getSqlWhereByPeriod($arrParam),
        self::getSqlWhereBySection($arrParam),
        self::getSqlWhereByNotSection($arrParam),
        $createdAtStart == '' ? null : "created_at > '".addslashes($createdAtStart)."'",
        $arrParam['sql_where_native'] ?? null,
      ],
      function($value) : bool
      {
        return $value != '';
      });

    $calcCount = $arrParam['calc_count'] ?? false;
    $limit = (int) ($arrParam['limit'] ?? 0);

    $result = \Db::fetchAll('
      select '.($calcCount ? 'count(*) cnt' : 'thread_id, id' ).'
      from {{forum_messages}}
      where '.implode(' and ', $arrSqlWhere).'
      '.self::getSqlOrderBy($arrParam).'
      '.self::getSqlOffset($arrParam).'
      '.self::getSqlLimit($arrParam));
    if($calcCount) return $result[0]->cnt;

    // @TODO: это надо запихать в саму тему, в ейный трейт
    $arrForumTopic = [];
    $arrMessages = [];
    foreach($result as $result)
    {
      if(count($arrForumTopic) > $limit) continue;
      $arrForumTopic[$result->thread_id] = \nt\Forum\Topic::getById($result->thread_id);
      $arrMessages[$result->id] = \nt\Forum\Message::getById($result->id);
    }
    if($onlyTopics) return $arrForumTopic;
    return ['topics' => $arrForumTopic, 'messages' => $arrMessages];
  }

  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereByStatus(array $arrParam) : ?string
  {
    if($arrParam['visible_only'] ?? null) return 'status in ('.\IForumThreadStatus::STATUS_OPEN.', '.\IForumThreadStatus::STATUS_CLOSED.')';
    return null;
  }
  /**
   * @param array $arrParam
   * @return null|string
   */
  private static function getSqlWhereByUser(array $arrParam) : ?string
  {
    $user = $arrParam['user'] ?? null;
    return $user ? 'user_id = '.(int) $user->id : null;
  }
  /**
   * @param array $arrParam
   * @return null|string
   */
  private static function getSqlOrderBy(array $arrParam) : string
  {
    $orderBy = $arrParam['order_by'] ?? null;
    if($orderBy == '') return '';

    $column = preg_replace('# (asc|desc)$#iU', '', $orderBy);
    if(! in_array($column, [ 'created_at', 'updated_at', 'answers_1d', 'answers_3d', 'answers_7d', 'answers_30d', 'answers_3h', 'answers_12h', ])) throw new \Exception('column invalid: '.$column);

    return 'order by '.$orderBy;
  }

  /**
   * @param array $arrParam
   * @return null|string
   */
  private static function getSqlLimit(array $arrParam) : string
  {
    $limit = $arrParam['limit'] ?? null;
    if($limit == '') return '';

    return ' limit '.$limit;
  }

  /**
   * @param array $arrParam
   * @return null|string
   */
  private static function getSqlOffset(array $arrParam) : string
  {
    if( !isset($arrParam['offset'])) return '';
    if( $arrParam['offset'] <= 0) return '';


    return ' offset '.((int)$arrParam['offset']);
  }

  /**
   * @param array $arrParam
   * @return null|string
   */
  private static function getSqlWhereByPeriod(array $arrParam) : string
  {
    $period = $arrParam['period'] ?? null;
    if($period == '') return '';
    return "created_at > NOW() - INTERVAL'".addslashes($period)."'";
  }


  /**
   * @param array $arrParam
   * @return string
   */
  private static function getSqlWhereBySection(array $arrParam): string
  {
    if(! isset($arrParam['section']) || empty($arrParam['section'])) return '';
    return "sections && '{" . implode(",", $arrParam['section']) . "}'::integer[]";
  }

  /**
   * @param array $arrParam
   * @return string
   */
  private static function getSqlWhereByNotSection(array $arrParam): string
  {
    if(! isset($arrParam['not_section']) || empty($arrParam['not_section'])) return '';
    return "not sections && '{" . implode(",", $arrParam['not_section']) . "}'::integer[]";
  }


  /**
   * @param array $arrParam
   * @return bool
   */
  public static function hasMessageByParams(array $arrParam): bool
  {
    ksort($arrParam);
    $cacheKey = __METHOD__ . '~' . implode('~', $arrParam);
    return (bool)\nt\Cache::get($cacheKey, $entityId = null, function() use ($arrParam)
    {
      return self::realHasMessageByParams($arrParam);
    }, $expire = 55);
  }

  private static function realHasMessageByParams(array $arrParam): int
  {
    $createdAtStart = $arrParam['created_at_start'] ?? null;

    $arrSqlWhere = array_filter(
      [
        self::getSqlWhereByStatus($arrParam),
        self::getSqlWhereByUser($arrParam),
        self::getSqlWhereByPeriod($arrParam),
        self::getSqlWhereBySection($arrParam),
        self::getSqlWhereByNotSection($arrParam),
        self::getSqlWhereByTopicId($arrParam),
        self::getSqlWhereByNewStatus($arrParam),
        $createdAtStart == '' ? null : "created_at > '".addslashes($createdAtStart)."'",
        $arrParam['sql_where_native'] ?? null,
      ],
      function($value) : bool
      {
        return $value != '';
      });

    $result = \Db::fetchAll('
      select id
      from {{forum_messages}}
      where '.implode(' and ', $arrSqlWhere).'
      '.self::getSqlOrderBy($arrParam) . '
      '.self::getSqlOffset($arrParam) . '
      '.self::getSqlLimit($arrParam));

    return (int) $result;
  }

};



