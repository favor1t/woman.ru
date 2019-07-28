<?php

/**
 * Класс для сбора данных и отправки в GrayLog
 * example:
 * GrayLogHelper::onEventForumThreadCreate(
$thread,
[
'_source' => 'site',
'_contentType' => 'thread'
]
);
 * Class GrayLogHelper
 */
class GrayLogHelper
{

    /**
     * @var array
     */
    private static $status = [
        'ForumThread' => [
            0 => 'В ожидании модерации',
            1 => 'Открыта',
            2 => 'Закрыта',
            3 => 'Скрыта',
        ],
        'ForumMessage' => [
            0 => 'Скрытые',
            1 => 'Активные',
        ],
        'Complaint' => [
            0 => 'Рассмотренна',
            1 => 'Активна',
        ],
        'Comment' => [
            0 => 'Скрытые',
            1 => 'Активные',
        ],
        'Ban' => [
            0 => 'Не действует',
            1 => 'Действует'
        ]
    ];
    /**
     * @var array
     */
    private static $status_ext = [
        'ForumThread' => [
            1   => 'Новая',
            3   => 'Отложена',
            2   => 'Вызвала сомнения',
            31  => 'Спам',
            32  => 'Оскорбления',
            33  => 'Дубликат',
            34  => 'Противозаконная',
            35  => 'По просьбе пользователя',
            36  => 'Агрессия к модераторам',
            37  => 'Потенциальный Холивар',
        ]
    ];
    /**
     * @var string
     */
    //private static $host = '10.0.3.251';
    //private static $host = '192.168.57.158';    // админкский чатик, 2017-10-16
    private static $host = '10.51.12.158';    // WMN-1082

    /**
     * @var int
     */
    private static $port = 12411;

    /**
     * @var array
     * DateTime => [datetime],
     * Source =>[mobile|site|api],
     * IP => [IP],
     * UserAgent => [User-agent],
     * ContentType => [Thread|message|complane|comment],
     * ID => [id],
     * UserType => [Aninomous|Autharization|AutharizationAnonimous],
     */
    private static $messages = [];


    /**
     * имя логирования для последующего разделея по потокам (stream)
     * @var array
     */
    private static $logName = [
        'user' => 'content_user_create',
        'moderator' => 'content_moderatoin',
        'development' => 'development_log',
    ];

    /**
     * Тип пользователя
     * @var array
     */
    private static $userType = [
        'anonim' => 'Anonymous',
        'auth' => 'Authorized',
        'auth_anonim' => 'Authorized_Anonymous',
    ];

    /**
     * Тип источника события
     * @var array
     */
    private static $source = [
        'desktop' => 'Desktop',
        'mobile' => 'Mobile',
        'api' => 'Application',
        'undefined' => 'undefined',
    ];

    /**
     * Тип логируемого контента
     * @var array
     */
    private static $contentType = [
        'thread' => 'Тема форума',
        'message' => 'Сообщение форума',
        'complaint' => 'Жалоба',
        'comment' => 'Комментарий',
        'undefined' => 'undefined',
    ];

    /**
     * Send data to GrayLog
     */
    private static function sendMessageAll()
    {
        if (!count(self::$messages)) return;

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        foreach (self::$messages as $msg) {
            $len = strlen($msg);
            $sendLen = socket_sendto($sock, $msg, $len, 0, self::$host, self::$port);
            if ($len !== $sendLen) {
                $exception = new Exception('Can not send message to GrayLog [catched]');
                ErrorLogHelper::createByException($exception)->save();
            }
        }
        socket_close($sock);

        self::$messages = [];
    }

    /**
     * @param array $array
     * @param $event string
     */
    private static function sendMessage($event, array $array)
    {
        $array = self::prepareArray($array, $event);
        self::$messages[] = json_encode($array);
        self::sendMessageAll();
    }

    /**
     * @param array $array
     * @return mixed|string
     */
    private static function getSource(array $array = [])
    {
        $key = isset($array['_source']) ? $array['_source']:'undefined';
        return isset(self::$source[$key]) ? self::$source[$key] : $array['_source'];
    }

    /**
     * @param array $arrays
     * @return mixed
     */
    private static function getContentType(array $array = [])
    {
        if (isset($array['_contentType']))
            return isset(self::$contentType[$array['_contentType']]) ? self::$contentType[$array['_contentType']] : $array['_contentType'];
        return self::$contentType['undefined'];
    }

    /**
     * @param array $array
     * @param $event
     * @return array
     */
    private static function prepareArray(array $array, $event = 'undefined')
    {
        $array['timestamp'] = time();
        $array['version'] = '1.1';
        //$array['host'] = preg_replace('#^https?://#', '', Yii::app()->getBaseUrl(true));
        $array['host'] = php_uname('n');
        $array['short_message'] = $event;
        $array['full_message'] = $event;
        $array['level'] = 6;
        return $array;
    }

    /**
     * @param $thread
     * @param array $array
     * @return array
     */
    private static function prepareForum($thread, array $array = [], $logName = 'undefined')
    {
        $datetime = date('Y-m-d H:i:s');
        $array['_type_source'] = self::getSource($array);


        $array['_userType'] = self::$userType['anonim'];
        if(\Yii::app()->user->id > 0)
            $array['_userType'] = self::$userType['auth'];
        if(isset($thread->anonymously) && $thread->anonymously == '0' && ($thread->user_id > 0 || \Yii::app()->user->id > 0))
            $array['_userType'] = self::$userType['auth'];
        if(isset($thread->anonymously) && $thread->anonymously == 1 && \Yii::app()->user->id > 0)
            $array['_userType'] = self::$userType['auth_anonim'];

        $array['_contentType'] = self::getContentType($array);
        $array['_user_ip'] = $thread->user_ip;
        $array['_user_agent'] = $thread->user_agent;
        $array['_dateTime'] = $datetime;
        $array['_id'] = $thread->id;
        $array['_log_name'] = $logName;

        return $array;
    }

    /**
     * @param ForumThread $thread
     * @param array $array
     */
    static function onEventForumThreadCreate(ForumThread $thread, array $array = [])
    {
        if(!isset($array['_contentType']))
            $array['_contentType'] = self::$contentType['thread'];

        $array['contentGroup'] = 'common_thread';
        if(
            isset($thread->tags)    &&
            is_array($thread->tags) &&
            in_array(Tag::getSiteExpertId(), $thread->tags)
        ) $array['contentGroup'] = 'expert_thread';

        $array['_userId'] = $thread->user_id > 0 ? 'u:'.$thread->user_id : 'a:'.self::getAnonymousId();

        $array = self::prepareForum($thread, $array, self::$logName['user']);
        self::sendMessage('Forum_Thread_Create', $array);
    }

    /**
     * @param ForumMessage $thread
     * @param array $array
     */
    static function onEventForumMessageCreate(ForumMessage $message, array $array = [])
    {
        if(!isset($array['_contentType']))
            $array['_contentType'] = self::$contentType['message'];

        $array['_userId'] = $message->user_id > 0 ? 'u:'.$message->user_id : 'a:'.self::getAnonymousId();
        if(! $message->user_id > 0 && Yii::app()->user->id > 0) $array['_userId'] = 'u:'.Yii::app()->user->id;
        $array['_targetId'] = $message->thread_id ?: 0;
        $array['contentGroup'] = 'common_message';
        if($message->user_id > 0 && $array['_targetId'] > 0){
            $experts = Expert::getAll();
            if(
                isset($experts[$message->user_id]->section_id) &&
                is_array($experts[$message->user_id]->section_id)
            ){
                //находим у thread его секции и смотрим, совпадают ли они с экспертом
                $thread = ForumThread::getById((int) $array['_targetId']);
                if(isset($thread->sections) &&
                    is_array($thread->sections) &&
                    count(array_intersect($thread->sections, $experts[$message->user_id]->section_id))
                ) $array['contentGroup'] = 'expert_message';
            }

        }
        $array = self::prepareForum($message, $array, self::$logName['user']);
        self::sendMessage('Forum_Message_Create', $array);
    }

    /**
     * @param Complaint $thread
     * @param array $array
     */
    static function onEventForumComplaintCreate(Complaint $complaint, array $array = [])
    {
        if(!isset($array['_contentType']))
            $array['_contentType'] = self::$contentType['complaint'];

        $array['_userId'] = $complaint->user_id > 0 ? 'u:'.$complaint->user_id : '0';
        if($array['_userId'] == '0')
            $array['_userId'] = \Yii::app()->user->id ? 'u:'.\Yii::app()->user->id : 'a:'.self::getAnonymousId();
        $array['_targetId'] = $complaint->target_id ?: 0;
        $array = self::prepareForum($complaint, $array, self::$logName['user']);
        $array['_contentGroup'] = self::getComplaintTextByTargetType((int)$complaint->target_type);

        $array['_complaintType'] = "undefined";
        if(isset($complaint->type) && !empty($complaint->type) && Complaint::$types2[$complaint->type]) $array['_complaintType'] = Complaint::$types2[$complaint->type];
        self::sendMessage('Forum_Complaint_Create', $array);
    }

    /**
     * @param Comment $comment
     * @param array $array
     */
    static function onEventArticleComment(Comment $comment, array $array = [])
    {
        if(!isset($array['_contentType']))
            $array['_contentType'] = self::$contentType['comment'];

        $array['_userId'] = $comment->user_id > 0 ? 'u:'.$comment->user_id : '0';
        if($array['_userId'] == '0')
            $array['_userId'] = \Yii::app()->user->id ? 'u:'.\Yii::app()->user->id : 'a:'.self::getAnonymousId();
        $array['_targetId'] = $comment->article_id ?: 0;
        $array = self::prepareForum($comment, $array, self::$logName['user']);
        $array['contentGroup'] = 'common_comment';
        self::sendMessage('Article_Comment_Create', $array);
    }

    /**
     * В связи с тем, что планируется "удалить" поле "user_agent" в таблицах - собираем anonimous_id самостоятельно
     * из объекта WebUser
     * @return float|int|string
     */
    static private function getAnonymousId()
    {
        $ip  = \Yii::app()->user->ip ?: 'unknow';
        $userAgent = \Yii::app()->user->userAgent ?: 'unknow';
        return self::createAnonymousId($ip,$userAgent);
    }


    /*Методы для логирования изменения статуса Тем форума, Сообщений форума, Комментарией, Жалоб*/
    /**
     * @param $obj
     * @param CWebUser $user
     * @param array $array
     * @param string $logName
     * @return array
     */
    private static function prepareModeratorChange($obj, CWebUser $user = null, array $post = [], $logName = 'undefined')
    {
        $array['_log_name'] = $logName;
        $array['_moderatorId'] = isset($post['_moderatorId']) ? $post['_moderatorId'] : null;
        $array['_moderatorName'] = isset($post['_moderatorName']) ? $post['_moderatorName'] : null;

        if($array['_moderatorId'] === null)
            $array['_moderatorId'] = $user ? $user->id : -1;
        if($array['_moderatorName'] === null)
            $array['_moderatorName'] = $user ? $user->name : 'undefined';

        $userIp = isset($obj->user_ip)? $obj->user_ip : 'unknow';
        $userAgent = isset($obj->user_agent)? $obj->user_agent : 'unknow';
        $array['_userCreatedId'] = (isset($obj->user_id) && $obj->user_id > 0)? 'u:'.$obj->user_id: 'a:'.self::createAnonymousId($userIp,$userAgent);
        $array['_contentID'] = $obj->id;

        return $array;
    }
    private static function createAnonymousId($ip = 'unknow', $userAgent = 'unknow')
    {
        return abs(implode('', unpack('L', md5($ip . $userAgent, true) )));
    }
    /**
     * @param ForumThread $obj
     * @param CWebUser $user
     * @param array $array
     */
    static function onEventModeratorThreadChange(ForumThread $obj, CWebUser $user = null, array $array = [])
    {
        $array = self::prepareModeratorChange($obj,$user, $array, self::$logName['moderator']);
        $array['_contentType'] = 'forum_thread';
        $array['contentGroup'] = self::getContentCroupByForumThread($obj);
        self::sendMessage('Thread_Moderator_Change', $array);
    }

    /**
     * @param ForumMessage $obj
     * @param CWebUser $user
     * @param array $array
     */
    static function onEventModeratorMessageChange(ForumMessage $obj, CWebUser $user = null, array $array = [])
    {
        $array = self::returnChangedFields($obj, $user, $array, self::$logName['moderator']);
        $array['_contentType'] = 'forum_message';
        $array['_targetId'] = $obj->thread_id ?: 0;
        self::sendMessage('Message_Moderator_Change', $array);
    }

    /**
     * @param Complaint $obj
     * @param CWebUser $user
     * @param array $array
     */
    static function onEventModeratorComplaintChange(Complaint $obj, CWebUser $user = null, array $array = [])
    {
        $array = self::prepareModeratorChange($obj, $user, $array, self::$logName['moderator']);
        $array['_contentType'] = 'complaint';
        $array['_targetId'] = $obj->target_id ?: 0;

        self::sendMessage('Complaint_Moderator_Change', $array);
    }

    /**
     * @param Comment $obj
     * @param CWebUser $user
     * @param array $array
     */
    static function onEventModeratorCommentChange(Comment $obj, CWebUser $user = null, array $array = [])
    {
        $array = self::prepareModeratorChange($obj, $user, $array, self::$logName['moderator']);
        $array['_contentType'] = 'comment';
        $array['_targetId'] = $obj->article_id ?: 0;

        self::sendMessage('Comment_Moderator_Change', $array);
    }

    /**
     * @param AutoComplaint $obj
     * @param CWebUser $user
     * @param array $array
     */
    static function onEventModeratorAutoComplaintChange(AutoComplaint $obj, CWebUser $user = null, array $array = [])
    {
        $array = self::prepareModeratorChange($obj, $user, $array, self::$logName['moderator']);
        $array['_contentType'] = 'auto_complaint';

        self::sendMessage('AutoComplaint_Moderator_Change', $array);
    }


    /**
     * @param ForumThread $thread
     * @param CWebUser $user
     * @param array $post
     * @param string $logName
     * @return mixed
     */
    private static function returnChangedFields($thread, CWebUser $user = null, array $post, $logName= 'undefined')
    {
        $array = self::prepareModeratorChange($thread, $user, $post, $logName);
        $keys = ['name','status', 'sections', 'title','user_name', 'body', 'anonymously', 'comments_mode', 'status_ext'];
        
        $array['_change_fields'] = '';

        if(isset($post['_change_fields']))
            $array['_change_fields'] = $post['_change_fields'];

        if(isset($post['_Status']))
            $array['_Status'] = $post['_Status'];

        if(isset($post['_Status_ext']))
            $array['_Status_ext'] = $post['_Status_ext'];

        foreach ($post as $k => $v)
        {
            if(!in_array($k,$keys)) continue;
            if(isset($thread->$k) && $thread->$k != $v){
                $array['_change_fields'] .= 'изменен ['.$k.'] ';
                if($k == 'status')
                    $array['_'.ucfirst($k)] = isset(self::$status[get_class($thread)][$thread->$k])? self::$status[get_class($thread)][$thread->$k] : $thread->$k;
                if($k == 'status_ext')
                    $array['_'.ucfirst($k)] = isset(self::$status_ext[get_class($thread)][$thread->$k])? self::$status_ext[get_class($thread)][$thread->$k] : $thread->$k;
            }
            unset($post[$k]);
        }
        return $array;
    }

    /**
     * @param ForumThread $thread
     * @param CWebUser $user
     * @param array $post
     */
    static function onEventModeratorThreadChangeFields(ForumThread $thread, CWebUser $user = null, array $post = [])
    {
        $array = self::returnChangedFields($thread, $user, $post, self::$logName['moderator']);
        $array['_contentType'] = 'forum_thread';
        $array['contentGroup'] = self::getContentCroupByForumThread($thread);
        self::sendMessage('Thread_Moderator_Change', $array);
    }


    /**
     * @param array $thread
     * @param array $post
     * @param CWebUser $user
     * @param string $logName
     * @return mixed
     */
    private static function prepareQueueArray(array $thread, array $post, CWebUser $user = null, $logName = 'undefined')
    {
        $array['_log_name'] = $logName;

        $array['_moderatorId'] = $user ? $user->id : -1;
        $array['_moderatorName'] = $user ? $user->name : 'undefined';

        $array['_contentID'] = $post['id'] ? $post['id'] : 0;
        $array['_change_fields'] = '';
        $array['_userCreatedId'] = (isset($thread['user_id']) && $thread['user_id'] > 0 )? 'u:'.$thread['user_id']: 'a:'.$thread['anonymous_id'];

        $array['_Status'] = isset(self::$status['ForumThread'][$thread['status']])
            ? self::$status['ForumThread'][$thread['status']]
            : '';
        $array['_Status_ext'] = isset(self::$status_ext['ForumThread'][$thread['status_ext']])
            ? self::$status_ext['ForumThread'][$thread['status_ext']]
            : '';

        if(is_array($post['data']))
            foreach ($post['data'] as $k=>$v)
                if($v!=$thread[$k]){
                    $array['_change_fields'] .= 'изменен ['.$k.']; ';
                    if($k == 'status')
                        $array['_'.ucfirst($k)] = isset(self::$status['ForumThread'][$v])? self::$status['ForumThread'][$v] : $v;
                    if($k == 'status_ext')
                        $array['_'.ucfirst($k)] = isset(self::$status_ext['ForumThread'][$v])? self::$status_ext['ForumThread'][$v] : $v;
                }
        return $array;
    }

    /**
     * @param array $thread
     * @param array $array
     * @param CWebUser $user
     */
    static function onEventModeratorThreadQueueUpdate(array $thread, array $array, CWebUser $user = null)
    {
        $array = self::prepareQueueArray($thread, $array, $user, self::$logName['moderator']);
        $array['contentGroup'] = self::getContentCroupByArray($thread);
        self::sendMessage('Moderator_Queue_Change_Thread', $array);
    }


    /**
     * @param Ban $ban
     * @param CWebUser $user
     * @param array $post
     * @param string $logName
     * @return mixed
     */
    private static function prepareBan(Ban $ban, CWebUser $user = null, array $post = [], $logName = 'undefined')
    {
        $array['_moderatorId'] = $user ? $user->id : -1;
        $array['_moderatorName'] = $user ? $user->name : 'undefined';
        $array['_log_name'] = $logName;
        $array['_userCreatedId'] = (isset($ban->user_id) && ($ban->user_id>0))? 'u:'.$ban->user_id: 'a:unknow';
        $array['_contentID'] = $ban->id;
        $array['_Status'] = \Ban::getStatusDesc($ban->status);
        $array['_change_fields'] = isset($post['_change_fields']) ? $post['_change_fields'] : '' ;
        $keys = ['name','status', 'sections', 'title','user_name', 'body', 'anonymously', 'comments_mode', 'status_ext','anonymously'];
        if(is_array($post))
            foreach($post as $k => $v){
                if(isset($ban->$k) && in_array($k, $keys))
                    if($ban->$k != $v)
                        $array['_change_fields'] .= 'изменен ['.$k.'] ';
            }

        return $array;
    }

    /**
     * @param Ban $ban
     * @param CWebUser $user
     * @param array $array
     */
    static function onEventModeratorBanCreate(Ban $ban, CWebUser $user = null, array $array = [])
    {
        $array = self::prepareBan($ban, $user, $array, self::$logName['moderator']);
        $array['_contentType'] = 'ban';
        self::sendMessage('Moderator_Ban_Create', $array);
    }

    /**
     * @param Ban $ban
     * @param CWebUser $user
     * @param array $array
     */
    static function onEventModeratorBanChange(Ban $ban, CWebUser $user = null, array $array = [])
    {
        $array = self::prepareBan($ban, $user, $array, self::$logName['moderator']);
        $array['_contentType'] = 'ban';
        self::sendMessage('Moderator_Ban_Change', $array);
    }

    /**
     * @param  $requestData
     * @return mixed
    */

    private static function prepareSendMindboxRequest($requestData, $operationName = 'undefind', $logName = 'undefind') {
      $array['_log_name'] = $logName;
      $array['_mindbox_request'] = strval($requestData);
      $array['_mindbox_operation_name'] = $operationName;

      return $array;
    }

    /**
     * @param $requestData
     * @param string $operationName
     * @param array $array
    */
    static function onEventSendMindboxRequest($requestData, string $operationName) {
        $array = self::prepareSendMindboxRequest($requestData, $operationName, self::$logName['development']);
        self::sendMessage('Send Operation Request To Mindbox', $array);
    }

    private static function getContentCroupByForumThread(ForumThread $thread) : string
    {
        if(
            isset($thread->tags)    &&
            is_array($thread->tags) &&
            in_array(Tag::getSiteExpertId(), $thread->tags)
        ) return 'expert_thread';

        return 'common_thread';

    }

    private static function getContentCroupByArray(Array $thread) : string
    {

        if(
            isset($thread['tags'])    &&
            is_array($thread['tags']) &&
            in_array(Tag::getSiteExpertId(), $thread['tags'])
        ) return 'expert_thread';

        return 'common_thread';
    }

    private static function getComplaintTextByTargetType(int $type) : string
    {
        $default = [
            1 => "Comment_complaint",
            2 => "Thread_complaint",
            3 => "Message_complaint",
            4 => "Consultation_complaint"
        ];
        return isset($default[$type]) ? $default[$type] : 'undefined';
    }


}