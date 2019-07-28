<?php

/**
 * #1550: импорт ответов экспертов
 * Class ImportForumExpertAnswerCommand
 */
class ImportForumExpertAnswerCommand extends WomanConsoleCommand
{

  /**
   * панеслася!
   * @throws Exception
   */
  public function actionIndex()
  {
    $this->setRobotName('ForumExpertAnswerImporter');
    NtHelper::init();

    // get xml
    $url = 'https://www.b17.ru/api_woman_ru.php?mod=get_expert_answer_list';
    $this->log('download xml: '.$url);
    $xml = file_get_contents($url);
    $this->log('done, xml size: '.number_format(strlen($xml), 0, '.', ','));

    // process all topics
    $countCreated = 0;
    $countExists  = 0;
    $countError   = 0;
    $xml = simplexml_load_string($xml);
    foreach($xml->topic_list->topic as $xmlTopic)
    {
      try
      {
        $this->log('process topic id: '.$xmlTopic->woman_topic_id);
        $isCreated = $this->processXmlTopic($xmlTopic);
        // лаконичнее, чем через if
        $this->log($isCreated ? 'message created success' : 'message already exists');
        $countCreated += $isCreated ? 1 : 0;
        $countExists  += $isCreated ? 0 : 1;
      }
      catch(Exception $e)
      {
        $this->log('exception: '.$e->getMessage());
        ErrorLogHelper::createByException($e)->save();
        $countError++;
      }
    }

    // show results
    $this->log('count message created: '.$countCreated);
    $this->log('count message already exists: '.$countExists);
    $this->log('count errors: '.$countError);
    if($countError) throw new Exception('errors detected');
  }


  /**
   * process xml topic: create expert message
   * return bool: is message created (false: message already exists)
   * @param SimpleXMLElement $xmlTopic
   * @return bool
   * @throws Exception
   */
  private function processXmlTopic(SimpleXMLElement $xmlTopic) : bool
  {
    // get and check topic
    $this->log('get and check topic...');
    $topic = \nt\Forum\Topic::getById($xmlTopic->woman_topic_id.'');
    $index = 'siteExpertTagId';
    $tagId = (int) \Yii::app()->params[$index];
    if(! $tagId) throw new Exception('application property '.$index.' not exists');
    if(! $topic->hasTagId($tagId)) throw new Exception('topic '.$topic->getId().' has no expert tag '.$tagId);

    // get woman user
    $this->log('get expert user...');
    $user = $this->getUserExpertByXmlTopic($xmlTopic);

    // check answer already exists
    $this->log('check answer already exists...');
    if($this->isForumMessageExists($topic, $xmlTopic, $user)) return false;

    // create message
    $this->createForumMessage($topic, $xmlTopic, $user);

    // update expert_answer field
    $this->log('update expert_answer value of  '.$topic->getId());
    if(! $this->updateForumThread($topic)) throw new Exception('topic '.$topic->getId().' can not update expert_answer');

    // flush caches
    $url = $topic->getUrl($absolute = true).'?nocache';
    $this->log('query url: '.$url);
    if(file_get_contents($url) === false) throw new Exception('query url failed');
    $url = str_replace('http://www.', 'http://m.', $url);
    $this->log('query url: '.$url);
    if(file_get_contents($url) === false) throw new Exception('query url failed');
    return true;
  }


  /**
   * check: is expert answer already exists
   * @param \nt\Forum\Topic $topic
   * @param SimpleXMLElement $xmlTopic
   * @param User $user
   * @return bool
   */
  private function isForumMessageExists(\nt\Forum\Topic $topic, SimpleXMLElement $xmlTopic, User $user) : bool
  {
    return (bool) Db::fetch('
      select 1
      from   {{forum_messages}}
      where  thread_id = :thread_id and user_id = :user_id and created_at = :created_at
      limit  1',
      [
        ':thread_id'  => $topic->getId(),
        ':user_id'    => $user->id,
        ':created_at' => date('Y-m-d H:i:s', $xmlTopic->b17_unixtime.''),
      ]);
  }


  /**
   * create forum message with expert answer
   * @param \nt\Forum\Topic $topic
   * @param SimpleXMLElement $xmlTopic
   * @param User $user
   * @return ForumMessage
   * @throws Exception
   */
  private function createForumMessage(\nt\Forum\Topic $topic, SimpleXMLElement $xmlTopic, User $user) : ForumMessage
  {
    $forumMessage = new ForumMessage();
    $forumMessage->thread_id   = $topic->getId();
    $forumMessage->created_at  = date('Y-m-d H:i:s', $xmlTopic->b17_unixtime.'');
    $forumMessage->user_id     = $user->id;
    $forumMessage->anonymously = false;
    $forumMessage->user_name   = $user->name;
    $forumMessage->user_agent  = Yii::app()->getRobotName();
    $forumMessage->body        = trim($xmlTopic->b17_text.'');

    $forumMessage->scenario = 'dontCheckBodyLength';
    if(! $forumMessage->validate())
    {
      $this->log('forum message erros: '.print_r($forumMessage->getErrors(), $return = true));
      throw new Exception('validate forum message failed');
    }
    if(! $forumMessage->save()) throw new Exception('save forum message failed');

    \nt\Forum\Message\Manager::onChangedExpert($topic);
    ForumMessageListCached::flushByForumTopicAndPage($topic, $page = 1);

    return $forumMessage;
  }


  /**
   * create user, if need
   * @param SimpleXMLElement $xmlTopic
   * @param bool $createIfNotExists
   * @return User
   * @throws Exception
   */
  private function getUserExpertByXmlTopic(SimpleXMLElement $xmlTopic, bool $createIfNotExists = true) : User
  {
    $needUpdatePhoto = false;

    // try get woman user id by b17 expert id
    $result = Db::fetch('
      select user_id_woman, expert_photo, expert_info
      from   {{user_expert_b17}}
      where  expert_id = :expert_id
      limit  1', [ ':expert_id' => $xmlTopic->b17_expert_id.'' ]);
    if($result)
    {
      // #1958
      if($result->expert_photo != $xmlTopic->b17_expert_photo.'' || $result->expert_info != $xmlTopic->b17_expert_info.'')
      {
        $needUpdatePhoto = true;
        $this->log('update expert info...');
        $rowCountUpdated = Db::execute('
        update {{user_expert_b17}} 
        set 
          expert_photo = :expert_photo,
          expert_info  = :expert_info
        where expert_id = :expert_id',
          [
            ':expert_photo' => $xmlTopic->b17_expert_photo.'',
            ':expert_info'  => $xmlTopic->b17_expert_info.'',
            ':expert_id'    => $xmlTopic->b17_expert_id.'',
          ]);
        if($rowCountUpdated != 1) throw new Exception('invalid row count updated: '.$rowCountUpdated);
      }

      $user = User::model()->findByPk($result->user_id_woman);
      if(! $user) throw new Exception('can not get user by id: '.$result->user_id_woman);

      if($needUpdatePhoto)
      {
        $this->log('update user image...');
        $this->setUserExpertImage($user, $xmlTopic->b17_expert_photo.'');
      }

      return $user;
    }

    if(! $createIfNotExists) throw new Exception('can not get woman user by b17 expert id: '.$xmlTopic->b17_expert_id);

    // create woman user
    $user = $this->createUserExpertByXmlTopic($xmlTopic);
    Db::execute('
      insert into {{user_expert_b17}} (created_at, expert_id, expert_fio, expert_url, expert_photo, expert_info, user_id_woman)
      values (now(), :expert_id, :expert_fio, :expert_url, :expert_photo, :expert_info, :user_id_woman)',
      [
        ':expert_id'     => $xmlTopic->b17_expert_id.'',
        ':expert_fio'    => $xmlTopic->b17_expert_fio.'',
        ':expert_url'    => $xmlTopic->b17_expert_url.'',
        ':expert_photo'  => $xmlTopic->b17_expert_photo.'',
        ':expert_info'   => $xmlTopic->b17_expert_info.'',
        ':user_id_woman' => $user->id,
      ]);
    Db::execute('
      insert into {{expert_info}} (user_id, title, link, section_id)
      values (:user_id, :title, :link, 
        (
          select array_agg(id)
          from   {{sections}}
          where  1 = 0
            or pid = '.(int) Section::SECTION_ID_RELATIONS.'
            or pid = '.(int) Section::SECTION_ID_PSYCHO.' 
            or id  in (279, 1156) /* Дети - только подсекции "ДО 16 и старше" и "Психология и развитие" */ 
            or id  = 4278         /* Здоровье - только подсекция "Диеты" */
        )
      )', [ ':user_id' => $user->id, ':title' => $xmlTopic->b17_expert_info.'', ':link' => '<a href="'.$xmlTopic->b17_expert_url.'" target="_blank">Страница эксперта на b17.ru</a>', ]);

    return $this->getUserExpertByXmlTopic($xmlTopic, $createIfNotExists = false);
  }


  /**
   * create user expert by topic
   * @param SimpleXMLElement $xmlTopic
   * @return User
   * @throws Exception
   */
  private function createUserExpertByXmlTopic(SimpleXMLElement $xmlTopic) : User
  {
    $this->log('create user expert...');

    $user = new User();
    $user->role     = UserRole::ROLE_SITE_EXPERT;
    $user->status   = User::STATUS_ACTIVE;
    $user->login    = 'b17_'.$this->getStringRandom();
    $user->email    = 'b17_'.$this->getStringRandom().'@b17.ru';
    $user->name     = trim($xmlTopic->b17_expert_fio.'');
    if($user->name  == '') throw new Exception('expert name is empty');
    $user->password = $user->passwordConfirm = $this->getStringRandom();
    if(! $user->validate()) throw new Exception('validate user failed');
    if(! $user->save())     throw new Exception('save user failed');
    if(! $user->id)         throw new Exception('user id is zero');

    $urlImage = $xmlTopic->b17_expert_photo.'';
    if($urlImage != '') $this->setUserExpertImage($user, $urlImage);

    Expert::flushCacheExpertAll();

    return $user;
  }


  /**
   * set user image
   * @param User $user
   * @param string $urlImage
   * @throws Exception
   */
  private function setUserExpertImage(User $user, string $urlImage) : void
  {
    $this->log('set user expert image...');
    
    $content = file_get_contents($urlImage);
    if($content == '') throw new Exception('can not download: '.$urlImage);

    $fileName = '/tmp/b17_'.mt_rand().'.jpg';
    if(file_exists($fileName)) throw new Exception('file already exists: '.$fileName);
    $written = file_put_contents($fileName, $content);
    if($written != strlen($content)) throw new Exception('file wrire error: '.$fileName);

    $index       = 'userpic_small_file';
    $arrFileName = explode('/', parse_url($urlImage, PHP_URL_PATH));
    $_FILES      =
    [
      $index =>
      [
        'name'     => end($arrFileName),
        'tmp_name' => $fileName,
        'error'    => 0,
        'size'     => $written,
      ],
    ];
    if(! $user->updateUserpicFromFiles($index)) throw new Exception('can not set user picture');
    if(! $user->save()) throw new Exception('can not update user');

    unlink($fileName);
  }


  /**
   * return random string
   * @param int $length
   * @return string
   */
  private static function getStringRandom(int $length = 8) : string
  {
    return substr(sha1(microtime(true).mt_rand()), $offst = 0, $length);
  }

  /**
   * @param \nt\Forum\Topic $topic
   * @return bool
   */
  private function updateForumThread(\nt\Forum\Topic $topic) : bool
  {
    $forumThread = ForumThread::model()->findByPk($topic->getId());
    $forumThread->expert_answer = ForumHelper::EXPERT_ANSWER_HAS;
    return $forumThread->save();
  }


};