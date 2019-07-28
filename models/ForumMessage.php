<?php

/**
 * This is the model class for table "forum_message".
 *
 * The followings are the available columns in table 'forum_message':
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 * @property string $name
 * @property string $sections
 * @property integer $thread_id
 * @property integer $user_id
 * @property string $_extra
 * @property integer $has_images
 * @property integer $thread_type
 * @property integer $anonymously
 */
class ForumMessage extends ActiveRecord implements Viewable
{
	use SectionTrait;
	/**
	 * Интервал между добавлениями одним пользователем 2х сообщений к одной сущности
	 */
	const INTERVAL_BETWEEN_2_MESSAGES = 10;

	const TYPE_FORUM_MESSAGE = 1;
	const TYPE_CONSULTATION_QUESTION = 2;

	private static $types = array(
		self::TYPE_FORUM_MESSAGE => 'Сообщение форума',
		self::TYPE_CONSULTATION_QUESTION => 'Вопрос консультации',
	);

	const STATUS_OFF = 0;
	const STATUS_ON = 1;

	const CACHE_TAG = 'tags.forumMessage';

	public $thread_type = self::TYPE_FORUM_MESSAGE;

	public static $statuses = array(
		self::STATUS_OFF => 'Скрытые',
		self::STATUS_ON  => 'Активные',
	);

	const MESSAGE_BODY_MAX_LEN = 2000;
	const MESSAGE_BODY_MAX_LEN_EDIT = 2000;
	const MESSAGE_BODY_MIN_LEN = 1;
	const MESSAGE_USER_NAME_MAX_LEN = 32;

	public static $classMap = array(
		self::TYPE_FORUM_MESSAGE 		 => 'ForumMessage',
		self::TYPE_CONSULTATION_QUESTION => 'ConsultationQuestion',
	);

	public $anonymously;
	//public $email;
	public $subscribe;
	public $translit;
	public $deleteUserpic;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ForumMessage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{forum_messages}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		$rules = array(
			array('body, user_name', 'XssValidator'),
			array('body, user_name', 'filter', 'filter' => [$obj = new \CHtmlPurifier(), 'purify']),
			
			
			array('body', 'required', 'message' => 'Введите текст сообщения.'),
			array('body', 'length', 'min' => self::MESSAGE_BODY_MIN_LEN, 'message' => 'Длина текста сообщения не может быть меньше '. self::MESSAGE_BODY_MIN_LEN .' символов.'),
			array('thread_id', 'required', 'message' => 'Не указана тема форума.'),
			array('translit, subscribe', 'boolean'),

			array('anonymously, user_name', 'safe'),
			array('thread_type', 'in', 'range' => array_keys(self::$types)),
			array('status', 'numerical', 'integerOnly'=>true),
		);

		$rules[] = array('body', 'length', 'max' => self::MESSAGE_BODY_MAX_LEN, 'message' => 'Длина текста сообщения не может превышать '. self::MESSAGE_BODY_MAX_LEN .' символов.', 'on' => 'insert', 'except' => 'dontCheckBodyLength');
		$rules[] = array('body', 'length', 'max' => self::MESSAGE_BODY_MAX_LEN_EDIT, 'message' => 'Длина текста сообщения при редактировании не может превышать '. self::MESSAGE_BODY_MAX_LEN_EDIT .' символов.', 'on' => 'update');

		if (isset(Yii::app()->user) &&  Yii::app()->user->isGuest)
		{
			$rules[] = array('user_name', 'required', 'message' => 'Укажите, пожалуйста, своё имя.');
			$rules[] = array('user_name', 'length', 'max' => self::MESSAGE_USER_NAME_MAX_LEN, 'message' => 'Длина имени автора не может превышать '. self::MESSAGE_USER_NAME_MAX_LEN .' символов.');
			$rules[] = array('user_name, translit, subscribe', 'safe');
			$rules[] = array('email', 'email', 'allowEmpty' => true, 'message' => 'Укажите, пожалуйста, правильный e-mail адрес.');

		}

		return $rules;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'owner' => array(self::BELONGS_TO, 'User', 'user_id', 'together' => false),
			'thread' => array(self::BELONGS_TO, 'ForumThread', 'thread_id', 'together' => false),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'class' => 'Class',
			'created_at' => 'created_at',
			'updated_at' => 'updated_at',
			'status' => 'Status',
			'name' => 'Name',
			'sections' => 'Sections',
			'thread_id' => 'Thread',
			'user_id' => 'User',
			'_extra' => 'Extra',
			'has_images' => 'Has Images',
			'thread_type' => 'Thread Type',
			'user_name' => 'имя'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('class',$this->class,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('sections',$this->sections,true);
		$criteria->compare('thread_id',$this->thread_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('_extra',$this->_extra,true);
		$criteria->compare('has_images',$this->has_images);
		$criteria->compare('thread_type',$this->thread_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return array
	 */
	public function arrays()
	{
		return array('sections');
	}

	public function getBaseType()
	{
		return PublicationHelper::BASE_TYPE_FORUM_MESSAGE;
	}

	public function getBaseClass()
	{
		return PublicationHelper::getBaseClassByType($this->getBaseType());
	}

	/**
	 * remove quote from body
	 */
	public function deleteQuote(){
		//на случай если цитата есть, а has_quote == 0
		//if($this->has_quote){
		$this->body = preg_replace('#'.QuoteWidget::REGEXP_BODY. '#s','',$this->body);
		$this->has_quote = 0;
		$this->save();
		//} else {
		//	return false;
		//}
	}

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'timestamp' => 'application.components.behaviors.TimestampBehavior',
			'sectionBehavior' => 'application.components.behaviors.SectionBehavior',
		);
	}

	/**
	 * Return view url
	 * @deprecated Удалить, сделать специализированные методы
	 * @return string
	 */
	public function getUrl()
	{
		assert('!empty($this->id)');
		$this->checkIsExistingRecord();
		return '/moderator/forum/message/update/id/'.$this->id;
	}

	/**
	*   Возвращает полный урл сообщений, для просмотра
	*/


	/**
	 * Rerurn edit url
	 * @return string
	 */
	public function getEditUrl(){
		assert('!empty($this->id)');
		$this->checkIsExistingRecord();
		return Yii::app()->getController()->createUrl('/moderator/forum/message/update', array('id' => $this->id));
	}

	/**
	 * Rerurn delete url
	 * @return string
	 */
	public function getDeleteUrl()
	{
		assert('!empty($this->id)');
		$this->checkIsExistingRecord();
		return Yii::app()->getController()->createUrl('/moderator/forum/message/delete', array('id' => $this->id));
	}

	/**
	 * @return bool
	 * @throws CException
	 */
	protected function checkIsExistingRecord()
	{
		if ($this->getIsNewRecord())
		{
			throw new CException('This method call only an existing record.');
		}
		return true;
	}

	protected function extraProperties()
	{
		$e = parent::extraProperties();
		$e['images']     = [];
		$e['has_quote']  = 0;
		$e['userpic_id'] = '';
		$e['answer']     = '';
		$e['email']      = '';
		return $e;
	}


	public function propertyClassMap()
	{
		return array(
			'images' => 'ImageCollection'
		);
	}

	/**
	 * Параметры кэширования по-умолчанию
	 * @return self
	 */
	public function defaultCache($queryCount = 1)
	{
		$dependency = new TagsCacheDependency(array(self::CACHE_TAG), 180, true);
		return $this->cache(200, $dependency, $queryCount);
	}

	public function scopes()
	{
		$tableAlias = $this->getTableAlias(false, false);
		return array(
			'recentByTime' => array(
				'order' => $tableAlias.'.created_at DESC',
			),
			'recent' => array(
				'order' => $tableAlias.'.id DESC',
			),
			'first' => array(
				'order' => $tableAlias.'.id ASC',
			),
			'recentImages' => array(
				'condition' => $tableAlias.'.has_images = 1',
				'order' => $tableAlias.'.created_at DESC',
			),
			'indexById' => array(
				'index' => $tableAlias.'.id',
			),
			'indexByPosition' => array(
				'order' => $tableAlias.'.position_number ASC',
			),
			'last' => array(
				'order' => $tableAlias.'.id DESC',
				'limit' => 1,
			),
			'active' => array(
				'condition' => $tableAlias.'.status = '.self::STATUS_ON,
			),
		);
	}

	public function defaultScope()
	{
		return array(
			'condition' => 'thread_type = '.$this->thread_type
		);
	}

	public function limit($offset = null, $limit = null)
	{
		$c = new CDbCriteria;
		if ($offset)
			$c->offset = $offset;

		if ($limit)
			$c->limit = $limit;

		$this->getDbCriteria()->mergeWith($c);
		return $this;
	}

	public function forThread($threadId)
	{
		assert('!empty($threadId)');
		$tableAlias = $this->getTableAlias(false, false);

		$c = new CDbCriteria;
		//$c->addCondition($tableAlias.'.thread_id = :thread_id AND '.$tableAlias.'.status = :status');
        $c->addCondition($tableAlias.'.thread_id = :thread_id');
        //$c->params = array('thread_id' => $threadId,'status' => self::STATUS_ON);
        $c->params = array('thread_id' => $threadId);
		$c->order = $tableAlias.'.id ASC';
		$this->getDbCriteria()->mergeWith($c);

		return $this;
	}

	public function forFilter($filter)
	{
		if(!$filter)return $this;

		$tableAlias = $this->getTableAlias(false, false);
		$interval = DateHelper::convertInterval($filter);
		$c = new CDbCriteria;
		$c->addCondition($tableAlias.'.created_at >=:created_at');
		$c->order = $tableAlias.'.id ASC';
		$c->params = array('created_at' => date('Y-m-d H:i:s', time() - $interval));
		$this->getDbCriteria()->mergeWith($c);

		return $this;
	}

	public function getUserpicSmallUrl($absolute = false)
	{
		if (!$this->anonymously && $this->userpic_id && $userpic = Userpic::getUserPicByPk($this->userpic_id))
			return $userpic->image->getWebname($absolute);
		elseif (!$this->anonymously && $this->user_id && $this->owner)
			return $this->owner->getUserpicSmallUrl($absolute);
		elseif($this->owner && $this->anonymously && $this->owner->userpic_id != $this->userpic_id && Userpic::isDefault($this->userpic_id)){
			$userpic = Userpic::getUserPicByPk($this->userpic_id);
			return $userpic->image->getWebname($absolute);
		} else
			return Userpic::getDefaultUserpicSmall($absolute);
	}

	public function getUserpicUrl($absolute = false)
	{
		return (!$this->anonymously && $this->user_id && $this->owner
			? $this->owner->getUserpicUrl($absolute)
			: Userpic::getDefaultUserpic($absolute)
		);
	}

	public static function ban($ids, $banIps = false)
	{
		assert('!empty($ids)');
		if (!is_array($ids))
			$ids = array($ids);

		self::model()->updateByPk($ids, array('status' => 0));

		if ($banIps)
		{
			$ips = array();
			$messages = ForumMessage::model()->findAllByPk($ids);

			foreach ($messages as $message)
			{
				if ($message->user_ip && !isset($ips[$message->user_ip]))
				{
					$ips[$message->user_ip] = 1;

					$ban = new Ban();
					$ban->type = Ban::TYPE_IP;
					$ban->period = Ban::PERIOD_6_MONTHS;
					$ban->ban_start = DateHelper::mysqlDate(time());
					$ban->ip = $message->user_ip;
					$ban->save();
				}
			}
			unset($messages);
			unset($ips);
		}
	}

	public function getUserName()
	{
		if (!$this->anonymously && $this->user_id && $this->owner)
		{ // нет галки анонимности и зарегестрированный пользователь
			return $this->owner->name;
		}
		elseif($this->user_name)
		{
			return $this->user_name;
		}
		else
		{
			return 'Гость';
		}
	}

	public function getUserUrl()
	{
		return !$this->anonymously && $this->user_id && $this->owner ? $this->owner->getSiteUrl() : '';
	}

	public function getPage() {
		assert('!empty($this->id)');
		assert('!empty($this->thread_id)');

		$command = Yii::app()->db->createCommand()
						->select('page')
						->from('{{forum_thread_pages}}')
						->where('thread_id=:thread_id AND message_id <= :message_id')
						->order('page DESC')
						->limit(1);

		return (int)$command->queryScalar(array(
			':thread_id' => $this->thread_id,
			':message_id' => $this->id,
		));
	}

	/**
	 * Возвращает адрес сообщения на сайте. $params['permanent'] говорит в каком стиле выдать параметр с номер сообщения,
	 * как папку или как якорь.
	 *
	 * @param boolean $absolute
	 * @param array $params
	 * @return string
	 */
	public function getSiteUrl($absolute = false, $params = array())
	{
		if (!$this->thread_id || !$this->thread)
			return false;

		$url = $this->thread->getSiteUrl($absolute, $params);
		if (!$url)
			return false;

		if (substr($url, -1, 1) != '/')
			$url .= '/';

		if (!empty($params['permanent'])) {
			$url .= 'message/';
		} elseif(!empty($params['superpermanent'])){
			$sql = "SELECT page FROM {{forum_thread_pages}}".
				" WHERE thread_id=".$this->thread->id." AND message_id<=".$this->id." ORDER BY message_id DESC LIMIT 1";
			$row_lower = Yii::app()->db->cache(86400)->createCommand($sql)->queryRow();

			$sql = "SELECT page FROM {{forum_thread_pages}}".
				" WHERE thread_id=".$this->thread->id." AND message_id>=".$this->id." ORDER BY message_id ASC LIMIT 1";
			$row_upper = Yii::app()->db->cache(86400)->createCommand($sql)->queryRow();

			if($row_lower){ // есть граница снизу
				if($row_upper){ // есть граница сверху
					if($row_upper['page'] - $row_lower['page'] > 1){ // есть разрыв в таблице страниц
						$url .= 'message/';
					} else {
						$url .= $row_lower['page'].'/#m';
					}
				} else { // последняя страница
					$url .= $row_lower['page'].'/#m';
				}
			} else { //границы снизу нет, или первая страница или проблемы с таблицей страниц
				$url .= 'message/';
			}
		} else {
			$page = $this->getPage();
			$url .= ($page > 1 ? $page.'/' : '').'#m';
		}

		return $url.$this->id;
	}

	/**
	 * Возвращает адрес сообщения на мобильном сайте
	 *
	 * @param boolean $absolute
	 * @param array $params
	 * @return string
	 */
	public function getMobileUrl($absolute = false, $params = array())
	{
		if (!$this->thread_id || !$this->thread)
			return false;

		$url = $this->thread->getMobileUrl($absolute, $params);
		if (!$url)
			return true;

		return $url.'#m'.$this->id;
	}

	public function getUserPic()
	{
		return Yii::app()->user->getCookieUserPicId();
	}

	public function getDeleteUserpic()
	{
		return !Yii::app()->user->isGuest || !$this->anonymously;
	}


	public function beforeValidate()
	{
		//если это вставка нового сообщения - проверить правила вставки
		if ($this->scenario=='insert' && $this->thread && $this->thread->comments_mode == ForumThread::COMMENTS_MODE_REGISTERED && (!$this->owner || !$this->owner->emailIsConfirmed))
			$this->addError('body', 'Чтобы оставить сообщение, необходимо зарегистрироваться и подтвердить свой e-mail.');

		/**
		* Поля со значением по умолчанию интерпретируются как пустые поля
		*/
		if (preg_match('/^(\[quote.*?\[\/quote\])?\s*(Ваш текст|Ваш вопрос)\s*$/', $this->body))
			$this->body = '';

		if ($this->user_name === 'Ваше имя')
			$this->user_name = 'Гость';
	    
	    // проверка юзернейма на стоп-слова
	    if(!StopWordHelper::checkStopWords($this->user_name, $arrParamLog =
        [
          // see StopWordHelper
          'target'             => 2,
          'field'              => 1,
          'site'               => (defined('MOBILE_APP') && MOBILE_APP) ? 2 : 1,
          'user_agent'         => isset(Yii::app()->user) ? Yii::app()->user->userAgent : null,
          'is_kaptcha_checked' => null,
        ])){
	        $this->addError('user_name', 'Такое имя пользователя запрещено');
	    }

			
		if ($this->email === 'Ваш E-mail')
			$this->email = '';

		// проверка на стоп-слова
		if(!StopWordHelper::checkStopWords($this->body, $arrParamLog =
      [
        // see StopWordHelper
        'target'             => 2,
        'field'              => 2,
        'site'               => (defined('MOBILE_APP') && MOBILE_APP) ? 2 : 1,
        'user_agent'         => isset(Yii::app()->user) ? Yii::app()->user->userAgent : null,
        'is_kaptcha_checked' => null,
      ])){
	        $this->addError('body', 'Такое сообщение не может быть опубликовано');
	    }

		//если он анонимен - запомним его данные в куках
		if ($this->anonymously)
		{
			Yii::app()->request->cookies['userPic'] = new CHttpCookie('userPic', $this->userpic_id);
			Yii::app()->request->cookies['userName'] = new CHttpCookie('userName', $this->user_name);
			Yii::app()->request->cookies['email'] = new CHttpCookie('email', $this->email);
		}

		//если транслит - сделаем его
		if ((bool) $this->translit)
			$this->body = Utils::translitUnquotedText($this->body);

		$this->anonymous_id = $this->createAnonymousId();

		return parent::beforeValidate();
	}

	protected function beforeSave(){
		$beforeSave = parent::beforeSave();
		if($this->isNewRecord && $beforeSave){
			$forum_thread = ForumThread::model()->resetScope()->findByPk($this->thread_id);
			assert('!empty($forum_thread)');
			$this->position_number = $forum_thread->answers_all + 1;
		}
		return $beforeSave;
	}

	public function cacheTagSpecific()
	{
		return CacheTag::cacheTagWithParams(self::CACHE_TAG, array('id' => $this->id));
	}
    
    
  protected function afterFind()
	{
    if(StopWordHelper::canFilterAbuseWords()) $this->filterAbuse();
		return parent::afterFind();
	}

	
  public function filterAbuse()
  {
	    $this->body = StopWordHelper::replaceAbuseWords($this->body);
	    $this->user_name = StopWordHelper::replaceAbuseWords($this->user_name);
	}
	
	
	public function cacheTag()
	{
		return self::CACHE_TAG;
	}

	protected function afterSave()
	{
		$this->refreshCacheTags();

		if ($this->scenario == 'insert')
		{
			if(
				Yii::app()->params['limits']['messagesOnThread'] &&
				($this->position_number % Yii::app()->params['limits']['messagesOnThread'] == 1)
			){
				try{
                    $inTransaction = (bool)Yii::app()->db->getCurrentTransaction();
                    if (!$inTransaction) {
                        $transaction = Yii::app()->db->beginTransaction();
                    } else {
                        $transaction = false;
                    }

					$page = ceil($this->position_number / Yii::app()->params['limits']['messagesOnThread']);
					// вставляем новую строку только в том случае, если строки с такими thread_id и page еще нет
					$sql = <<<SQL
INSERT INTO {{forum_thread_pages}} (thread_id, page, message_id, created_at)
	SELECT t.*
		FROM (VALUES (:thread_id :: int, :page :: int, :id :: int, :created_at :: timestamp without time zone)) AS t(thread_id, page, message_id, created_at)
			WHERE NOT EXISTS (SELECT 1 FROM {{forum_thread_pages}} w WHERE w.thread_id=t.thread_id AND w.page=t.page );
SQL;
					Yii::app()->db->createCommand($sql)
						->execute(array(
							':thread_id'=>(int)$this->thread_id,
							':page' => (int)$page,
							':id' => (int)$this->id,
							':created_at' => $this->created_at
						));

					if ($transaction) {
						$transaction->commit();
					}
				} catch(CDbException $e){
					// ignore duplicate entry
                    if ($transaction) {
                        $transaction->rollback();
                    }
				}
			}

			/*if ($this->subscribe)
				$this->processSubscribe();*/

			if ($this->user_id)
			{
				if ($owner = $this->owner)
				{
					if (!$this->anonymously)
					{
						if (Yii::app()->request->getParam('deleteUserPic'))
						{
							$owner->userpic_small = null;
							$owner->userpic_id = null;
						}
						else if ($this->userpic_id)
						{
							$owner->userpic_small = null;
							$owner->userpic_id = $this->userpic_id;
						}
					}
					if ($owner->anonymous_sending != $this->anonymously)
					{
						$owner->anonymous_sending = $this->anonymously;
					}
					$owner->save(false);
				}
			}

			// отправляем задачу в очередь
			if (Yii::app()->params['notifications'] != Subscription::NOTIFY_NOTHING) {
				$thread_name = '';
				if (isset($this->thread->name))
					$thread_name = $this->thread->name;
				//Yii::app()->notificator->send($this->thread_id, Subscription::TYPE_THREAD, $thread_name , $this->body, $this->user_name);
			}
		}

		//если был не открыт, а стал - открыт
		if ($this->status == self::STATUS_ON && $this->initialStatus == self::STATUS_OFF)
		{
			// если он есть и не анонимен
			if ($this->user_id && !$this->anonymously)
			{
				$user = $this->owner;
				if ($user)
				{
					//увеличиваем кол-во сообщений пользователя
					$user->count_messages++;

					// увеличиваем кол-во картинок, если они есть в сообщении
					if ($this->has_images && $this->images instanceof ImageCollection)
						$user->count_attached += $this->images->count();

					$user->save(false);
				}
			}

			if ($this->thread && $this->thread instanceof ForumThread)
				$this->thread->incrementComment($this);

			if (array_key_exists('consultation', $this->relations()) && $this->consultation instanceof Consultation){
				$this->consultation->incrementComment($this);
			}
		}
		//если был открытым, а стал закрытым - наоборот
		elseif ($this->status == self::STATUS_OFF && $this->initialStatus == self::STATUS_ON)
		{
			//если он есть и не анонимен
			if ($this->user_id && !$this->anonymously)
			{
				$user = $this->owner;
				if ($user)
				{
					// уменьшаем кол-во тем пользователя
					$user->count_messages--;

					// уменьшаем кол-во картинок, если они есть в треде
					if ($this->has_images && $this->images instanceof ImageCollection)
						$user->count_attached -= $this->images->count();

					$user->save(false);
				}
			}

			if ($this->thread && $this->thread instanceof ForumThread)
				$this->thread->decrementComment();

			if (array_key_exists('consultation', $this->relations()) && $this->consultation instanceof Consultation){
				$this->consultation->decrementComment();
			}
		}

		if ($this->scenario == 'insert')
		{
			/*
			$sql = "UPDATE ".ForumThread::model()->tableName()." SET answers_all = answers_all + 1 WHERE id=".$this->thread_id." RETURNING id, answers_all";
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			$answers = $row['answers_all'];
			$prefix = SummaryManager::MESSAGE_THREAD_ALL_PREFIX;
			$tag = $prefix.$this->thread_id;
			Yii::app()->cache->set($tag, $answers);
			*/

			ForumThread::onMessageCreated($this);
		}

		if($this->initialStatus != $this->status){
			$this->onStatusChange();
		}
		
		if(!$this->isNewRecord) $this->onChange();


		NtHelper::init();
		\nt\Forum\Message::onWomanModelChanged($this);

		// #WD-3932: кеширование рендера страницы форума
    ForumMessageListCached::onForumMessageChanged($this->thread_id, $this->id);

		return parent::afterSave();
	}

	protected function onStatusChange() {
		$this->raiseEvent(
			 'onStatusChange',
				 new CModelEvent($this, array(
					 'initialStatus'	=> $this->initialStatus,
					 'currentStatus'	=> $this->status
				 ))
		);
	}
	
	protected function onChange() {
	    
		$this->raiseEvent( 'onChange', new CModelEvent($this) );
	}

	protected function afterDelete()
	{
		$this->refreshCacheTags();

		//если пользователь есть и не анонимен
		if ($this->user_id && !$this->anonymously)
		{
			$user = $this->owner;
			if ($user)
			{
				// уменьшаем кол-во тем пользователя
				$user->count_messages--;

				// уменьшаем кол-во картинок, если они есть в треде
				if ($this->has_images && $this->images instanceof ImageCollection)
					$user->count_attached -= $this->images->count();

				$user->save(false);
			}
		}

		if ($this->thread && $this->thread instanceof ForumThread)
			$this->thread->decrementComment();

		if (isset($this->consultation) && $this->consultation instanceof Consultation)
			$this->consultation->decrementComment();

		return parent::afterDelete();
	}

	protected function refreshCacheTags()
	{
		CacheTag::refresh(self::CACHE_TAG);
		CacheTag::refresh(CacheTag::cacheTagWithParams(self::CACHE_TAG, array('id' => $this->id)));
		CacheTag::refresh(CacheTag::cacheTagWithParams(self::CACHE_TAG, array('thread_id' => $this->thread_id)));
	}

	protected function processSubscribe()
	{
		$s = new Subscription();
		$s->type = Subscription::TYPE_THREAD;
		$s->target_id = $this->thread_id;

		if (!Yii::app()->user->isGuest)
		{
			// зарегистрированный пользователь
			$s->user_id = $this->user_id;
			$s->status  = $this->owner->emailIsConfirmed ? Subscription::STATUS_ON : Subscription::STATUS_WAITING;

		}
		elseif ($this->email)
		{
			// незарегистрированный, указал email
			$s->email   = $this->email;
			$s->status  = Subscription::STATUS_WAITING;

		} else {
			return false;
		}

		$s->subscribe_code 	= StringHelper::hash();
		return $s->save();
	}

	/**
	 * named scope. message со статусами
	 * @param int $status
	 * @return self
	 */
	public function withStatus($status)
	{
		assert('!empty($ids)');
		if (!is_int($status))
			$status = (int) $status;

		$tableAlias = $this->getTableAlias(false, false);
		$c = new CDbCriteria();
		$c->addCondition($tableAlias.'.status = '.$status);
		$this->getDbCriteria()->mergeWith($c);

		return $this;
	}

	public function deactivate()
	{
		$this->status = self::STATUS_OFF;
		return $this;
	}

	public static function hideById($ids)
	{
		assert('!empty($ids)');
		if (!is_array($ids))
			$ids = array($ids);

		$messages = static::model()->resetScope()->findAllByPk($ids);
		foreach ($messages as $message)
		{
		    \GrayLogHelper::onEventModeratorMessageChange($message, \Yii::app()->user ,
                [
                    '_change_fields' => '[Status]',
		            '_Status' => ForumMessage::$statuses[ForumMessage::STATUS_OFF],
                ]
            );
			$message->deactivate()->save(false);
		}

		return $messages;
	}

	public function getIsActive()
	{
		return $this->status === self::STATUS_ON;
	}

	public function getBodyRestored()
	{
		/**
		 * @todo: Хак, т.к. у нас в базе данных есть часть сообщений у которых body json_encode'жен
		 * Надо будет потом сделать миграцию, чтобы подправить такие сообщения и потом можно будет выкинуть этот хак
		 */
		if(!ltrim($this->body)){
			$str = '';
		} else {
			if(is_numeric($this->body)){
				return $this->body;
			} else {
				$str = JSON::decode($this->body);
				if(!$str){
					$str = $this->getBody();
				} elseif(substr($this->body, 0, 1) == '+' && substr($str, 0, 1) != '+'){
					$str = '+'.$str;
				} elseif(is_array($str)){
                    $str = implode(' ', $str);
                }
			}
		}
		return Html::restoreHtml($str);
	}

	/**
	 * Загружает треды для списка сообщений
	 *
	 * @param array $threads
	 */
	public static function preloadThreads(array $messages)
	{
		if (!$messages || !is_array($messages) || !count($messages))
			return false;

		// группируем сообщения по уникальным id тредов
		$ids = array();
		foreach ($messages as $message)
		{
			if ($message->thread_id && !isset($ids[$message->thread_id]))
			{
				if (!isset($ids[$message->thread_id]))
					$ids[$message->thread_id] = array();

				$ids[$message->thread_id][] = $message->id;
			}
		}

		// грузим объекты
		$threads = ForumThread::model()->indexById()->findAllByPk(array_keys($ids));

		foreach ($threads as $thread_id => $thread)
		{
			if (isset($ids[$thread_id]))
			{
				foreach ($ids[$thread_id] as $id)
					$messages[$id]->thread = $thread;
			}
		}
	}

	/**
	 * Возвращает адрес для бана автора треда
	 * @return string
	 */
	public function getBanUrl($returnUrl = false)
	{
		$this->checkIsExistingRecord();

		$fields = array(
			'pub_id' => $this->id,
			'pub_type' => Ban::PUB_TYPE_FORUM,
			 'ip' => $this->user_ip,
			 'user_agent' => $this->user_agent,
			 'cookie' => $this->user_cookie,
		);

		if ($this->user_id)
			$fields['user_id'] = $this->user_id;

		$fields = Ban::getKeyValuePairsForBanUrl($fields);
		if ($returnUrl)
			$fields = $fields + array('returnUrl' => $returnUrl);

		return Yii::app()->getController()->createUrl('ban/create/').'?'.http_build_query($fields);
	}

	public function createAnonymousId()
	{
		return self::createAnonymousIdByData($this->user_ip, $this->user_agent);
	}


	// \nt\Forum\Message
	public static function createAnonymousIdByData($userIp, $userAgent)
	{
		return abs(implode('', unpack('L', md5($userIp.$userAgent, true))));
	}



	public function getAnonymousId()
	{
		return $this->anonymous_id ?: $this->anonymous_id = $this->createAnonymousId();
	}

	public function instantiate($attributes)
	{
		assert('is_array($attributes)');
		$attributes['thread_type'] = isset($attributes['thread_type'])?$attributes['thread_type']:self::TYPE_FORUM_MESSAGE;
		if (isset($attributes['thread_type']))
		{
			if (isset(self::$classMap[$attributes['thread_type']]))
			{
				$class = self::$classMap[$attributes['thread_type']];
				return new $class(null);
			}
			else
			{
				throw new CException(
					Yii::t(
						'woman',
						'Invalid type: {type}',
						array('type' => $attributes['thread_type'])
					)
				);
			}
		}
		else
		{
			throw new CException(
				Yii::t(
					'woman',
					'Type not set',
					array('type' => $attributes['thread_type'])
				)
			);
		}
	}



	private function getBodySplittedByQuote()
	{
		$arrResult =
		[
			'quote' =>	[
							'nickname' => null,
							'body'     => null,
						],
			'body'	=>  $this->body,
		];

		if( (preg_match('/\[quote=("|&quot;)(.+)("|&quot;).+\](.+)\[\/quote\]/iUs', $arrResult['body'], $arrMatch)) || (preg_match('/\[quote=(\"|&quot;)(.+)(\"|&quot;)\](.+)\[\/quote\]/iUs', $arrResult['body'], $arrMatch)))
		{
			$arrResult['quote']['nickname'] = $arrMatch[2];
			$arrResult['quote']['body']     = $arrMatch[4];
			$arrResult['body']              = str_replace($arrMatch[0], '', $arrResult['body']);
		}

		return $arrResult;
	}

	public function getQuote()
	{
		$array = $this->getBodySplittedByQuote();
		return $array['quote']['nickname'] !== null ? $array['quote'] : null;
	}

	public function getBodyWithoutQuote()
	{
		return $this->getBodySplittedByQuote()['body'];
	}
	
	public function getBodyWithoutQuoteAndAnswer()
	{
		$body = strip_tags(preg_replace('/<br\s?\/?>/ui', " ", $this->getBodyWithoutQuote()));
		
		if(preg_match('/(.*)Ответ:(.*)$/usim', $body, $matches) && !empty($matches[1])){
			$body = $matches[1];
		}
		
		return $body;
	}
	
	public function getAnswerText(){		
		if(!empty($this->answer)) return $this->answer;
		
		$body = strip_tags(preg_replace('/<br\s?\/?>/ui', " ", $this->getBodyWithoutQuote()));
		
		if(preg_match('/(.*)Ответ:(.*)$/usim', $body, $matches) && !empty($matches[2])){
			return $matches[2];
		}	
	}	

    /**
     * Вернет очищенный body
     * @return string
     */
    public function getBody()
    {
        return ForumHelper::sanitize_quote($this->body);
    }


  /**
   * #1053
   * копи-паста Comment::checkSpam
   * @param string $message
   * @throws Exception
   */
	public static function checkSpam(string $message)
	{
    $commentCount = Db::fetch("
  		select count(*) cnt
  		from {{forum_messages}}
  		where 1 = 1
  			and created_at > now() - '5 minutes'::interval
  			and status     = ".self::STATUS_ON."
  			and md5(body)  = :check_sum",
      [ ':check_sum' => md5($message), ])->cnt;
    if($commentCount < 3) return;

    Db::execute('
  		insert into {{forum_message_spam}} (created_at, ip, user_agent, post)
  		values (now(), :ip, :user_agent, :post)',
      [
        ':ip'         => Yii::app()->request->getUserHostAddress(),
        ':user_agent' => Yii::app()->request->getUserAgent(),
        ':post'       => json_encode($_POST, $option = JSON_UNESCAPED_UNICODE),
      ]);

    throw new Exception('spam detected');
	}

  /**
   * Формирование строки цитаты
   * @param string $user_name
   * @param string|number $quote_id
   * @param string $quote_body
   * @return string
   */
	private function quoteString($user_name, $quote_id, $quote_body)
	{
		return '[quote="'.$user_name.'" message_id="'.$quote_id.'"]'.$quote_body.'[/quote]';
	}

  /**
   * Добавление текста цитаты в комментарий по id комментария
   * @param string|number $quote_id
   */
	public function addQuoteById($quote_id)
	{
		$quote = \ForumMessage::model()->findByPk($quote_id);
		if (!$quote)
		{
			$this->addError('_', 'Не найден комментарий цитаты');
			return;
		}

		$this->body = $this->quoteString($quote->user_name, $quote->id, $quote->getBodyWithoutQuote()) . $this->body;
		$this->has_quote = 1;
	}

	private static function getUserHashId(array $array) : string {

        $isAnonymous    = isset($array['isAnonymous'])  ? $array['isAnonymous'] : false;
        $userCoockie    = isset($array['userCoockie'])  ? $array['userCoockie'] : '';
        $anonymousId    = isset($array['anonymousId'])  ? $array['anonymousId'] : '';
        $userIp         = isset($array['userIp'])       ? $array['userIp']      : '';
        $userAgent      = isset($array['userAgent'])    ? $array['userAgent']   : '';
        $userId         = isset($array['userId'])       ? $array['userId']      : 0;

        if($userId > 0 && $isAnonymous)  return $userId;

        if( ! empty($userCoockie))
            return implode('', unpack('L', md5($userCoockie, true)));
        if( ! empty($anonymousId)) return $anonymousId;

        return self::createAnonymousIdByData($userIp, $userAgent);
    }
};