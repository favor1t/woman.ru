<?php

/**
 * This is the model class for table "{{bans}}".
 *
 * The followings are the available columns in table '{{bans}}':
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $type
 * @property integer $user_id
 * @property string $ip
 * @property string $user_agent
 * @property string $cookie
 * @property string $subnet
 * @property string $start
 * @property string $end
 * @property string $comment
 */
class Ban extends ActiveRecord
{
	const TYPE_USER_ID = 1;
	const TYPE_IP      = 2;
	const TYPE_UA      = 3;
	const TYPE_IP_UA   = 4;
	const TYPE_COOKIE  = 5;
	const TYPE_SUBNET  = 6;

	const PERIOD_1_HOUR    = 1;
	const PERIOD_3_DAYS    = 2;
	const PERIOD_7_DAYS    = 3;
	const PERIOD_1_MONTH   = 4;
	const PERIOD_6_MONTHS  = 5;
	const PERIOD_100_YEARS = 6;

	const PUB_TYPE_PUB                     = 0;
	const PUB_TYPE_FORUM                   = 1;
	const PUB_TYPE_CONTEST                 = 2;
	const PUB_TYPE_CONTEST_MEMBER          = 3;
	const PUB_TYPE_LITERARY_CONTEST_MEMBER = 4;
	const PUB_TYPE_COMPLAINT               = 5;
	const PUB_TYPE_THREAD                  = 6;
	const PUB_TYPE_SURVEY_QUESTION         = 7;

	public static $typeDescription = array(
		self::TYPE_IP_UA   => 'IP + User-Agent',
		self::TYPE_IP      => 'IP',
		//self::TYPE_UA    => 'User-Agent',    //WMN-607
		self::TYPE_COOKIE  => 'Cookie',
		self::TYPE_SUBNET  => 'Подсеть IP',
		self::TYPE_USER_ID => 'Id пользователя'
	);

	private static $pubTypes = array(
		self::PUB_TYPE_PUB                     => 'Бан комментария к публикации',
		self::PUB_TYPE_FORUM                   => 'Бан сообщения форума',
		self::PUB_TYPE_THREAD                  => 'Бан ветки форума',
		self::PUB_TYPE_CONTEST                 => 'Бан комментария конкурса',
		self::PUB_TYPE_PUB                     => 'Бан жалобы',
		self::PUB_TYPE_CONTEST_MEMBER          => 'Бан комментария к участнику конкурса',
		self::PUB_TYPE_LITERARY_CONTEST_MEMBER => 'Бан комментария к участнику литературного конкурса',
		self::PUB_TYPE_SURVEY_QUESTION         => 'Бан комментария к вопросу в опросе',
	);

	public static $periodDesc =  array(
		self::PERIOD_3_DAYS    => '3 дня',
		self::PERIOD_7_DAYS    => '7 дней',
		self::PERIOD_1_MONTH   => '1 месяц',
		self::PERIOD_6_MONTHS  => '6 месяцев',
		self::PERIOD_100_YEARS => 'бессрочно',
	);

	public static $periodIncrement = array(
		self::PERIOD_1_HOUR    => '+1 hour',
		self::PERIOD_3_DAYS    => '+3 days',
		self::PERIOD_7_DAYS    => '+7 days',
		self::PERIOD_1_MONTH   => '+1 month',
		self::PERIOD_6_MONTHS  => '+6 months',
		self::PERIOD_100_YEARS => '+100 years',
	);

	const CACHE_BANLIST_KEY = 'tags.banlist';
	const CACHE_TAG         = 'tags.ban';

	const STATUS_ON  = 1;
	const STATUS_OFF = 0;

	protected static $banlist;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Ban the static model class
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
		return '{{bans}}';
	}

	public function relations()
	{
		return array(
			'publication' => array(self::HAS_ONE, 'Publication', array('id' => 'pub_id')),
			'complaint' => array(self::HAS_ONE, 'Complaint', array('id' => 'pub_id')),
			'forum' => array(self::HAS_ONE, 'ForumMessage', array('id' => 'pub_id')),
			'thread' => array(self::HAS_ONE, 'ForumThread', array('id' => 'pub_id')),
			'contest' => array(self::BELONGS_TO, 'Contest', 'pub_id'),
			'member' => array(self::BELONGS_TO, 'ContestMember', 'pub_id'),
			'user' => array(self::BELONGS_TO, 'User', array('user_id' => 'id')),
		);
	}

	public function getPublication()
	{
		switch ($this->pub_type)
		{
			case self::PUB_TYPE_PUB:
				return $this->getRelated('publication', $this->isNewRecord);

			case self::PUB_TYPE_FORUM:
				return $this->getRelated('forum', $this->isNewRecord);

			case self::PUB_TYPE_THREAD:
				return $this->getRelated('thread', $this->isNewRecord);

			case self::PUB_TYPE_CONTEST:
				return $this->getRelated('contest', $this->isNewRecord);

			case self::PUB_TYPE_COMPLAINT:
				return $this->getRelated('complaint', $this->isNewRecord);

			case self::PUB_TYPE_CONTEST_MEMBER:
			case self::PUB_TYPE_LITERARY_CONTEST_MEMBER:
				return $this->getRelated('member', $this->isNewRecord);

			default:
				return false;
		}
	}

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'timestamp' => 'application.components.behaviors.TimestampBehavior',
			'actionLog' => 'application.components.behaviors.ActionLogBehavior'
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('type', 'required'),
			array('type, user_id, status', 'numerical', 'integerOnly'=>true),
			array('ip, subnet', 'length', 'max'=>39),
			array('name, updated_at, ban_start, ban_end, comment, period, user_agent, cookie', 'safe'),
			array('pub_type', 'in', 'range'=>array_keys(self::$pubTypes)),
			array('pub_id', 'numerical', 'integerOnly'=>true),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created_at' => 'Ctime',
			'updated_at' => 'Mtime',
			'type' => 'Type',
			'user_id' => 'User',
			'ip' => 'Ip',
			'user_agent' => 'User Agent',
			'cookie' => 'Cookie',
			'subnet' => 'Subnet',
			'ban_start' => 'Start',
			'ban_end' => 'End',
			'comment' => 'Комментарий банящего',
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
		$criteria->compare('created_at',$this->ctime,true);
		$criteria->compare('updated_at',$this->mtime,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('user_agent',$this->user_agent,true);
		$criteria->compare('cookie',$this->cookie,true);
		$criteria->compare('subnet',$this->subnet,true);
		$criteria->compare('ban_start',$this->ban_start,true);
		$criteria->compare('ban_end',$this->ban_end,true);
		$criteria->compare('comment',$this->comment,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function afterSave()
	{
		// обновляем версию тэга кэша
		CacheTag::refresh(self::CACHE_TAG);
		
		if($this->isNewRecord) $this->onCreate();
		
		return parent::afterSave();
	}

	public function afterDelete()
	{
		// обновляем версию тэга кэша
		CacheTag::refresh(self::CACHE_TAG);
		return parent::afterDelete();
	}
	
	
	protected function onCreate() {
	    
		$this->raiseEvent( 'onCreate', new CModelEvent($this) );
	}

	protected function beforeValidate()
	{
		// устанавливаем имя
		if ($this->scenario == 'insert')
		{
			$this->name = self::getTypeDesc($this->type).' '.$this->getValue(false);
		}
		
		// подсеть банится всегда на один час
		if ($this->type == self::TYPE_SUBNET)
		{
			$this->ip = null;
			$this->period = self::PERIOD_1_HOUR;
		}
		
		$this->ban_start = $this->ban_start ?: DateHelper::mysqlDate(time());
		$this->ban_end   = strtotime($this->ban_start);
		$this->ban_end   = $this->incrementByPeriod($this->ban_end, $this->period);
		$this->ban_end   = DateHelper::mysqlDate($this->ban_end);
		
		$properties[self::TYPE_IP]     = array('ip');
		$properties[self::TYPE_UA]     = array('user_agent');
		$properties[self::TYPE_IP_UA]  = array('ip', 'user_agent');
		$properties[self::TYPE_COOKIE] = array('cookie');
		$properties[self::TYPE_SUBNET] = array('subnet');
		$properties[self::TYPE_USER_ID] = array('user_id');
		foreach ($properties[$this->type] as $propertyName)
		{
			if (!$this->{$propertyName})
			{
				$this->addError($propertyName, "$propertyName - обязательное поле");
				return false;
			}
		}
		
		// проверяем уникальность бана
		if ($this->scenario == 'insert')
		{
			foreach ($properties[$this->type] as $propertyName)
			{
				$conditions[] = $propertyName.' = :'.$propertyName;
				$params[':'.$propertyName] = $this->{$propertyName};
			}
			$conditions[]      = 'type = :type';
			$conditions[]      = 'status = :status';
			$conditions[]      = 'ban_start < :end';
			$conditions[]      = 'ban_end > :start';
			$params[':type']   = $this->type;
			$params[':status'] = self::STATUS_ON;
			$params[':start']  = $this->ban_start;
			$params[':end']    = $this->ban_end;
			if (Ban::model()->count(implode(' AND ', $conditions), $params) > 0)
			{
				$this->addError(null, 'Такой бан уже существует');
				return false;
			}
		}
		
		return parent::beforeValidate();
	}

	/**
	 * Возвращает текстовое описание типа бана
	 * @param int $type
	 * @return string
	 */
	public static function getTypeDesc($type = null)
	{
		if (!$type) {
			return self::$typeDescription;
		}

		if (isset(self::$typeDescription[$type])) {
			return self::$typeDescription[$type];
		}

		return null;
	}

	/**
	 * Возвращает все типы бана
	 * @return multitype:string
	 */
	public static function getAllTypes()
	{
		return array_keys(self::$typeDescription);
	}

	public static function getStatusDesc($status = null)
	{
		$desc = array(
			self::STATUS_ON  => 'Действует',
			self::STATUS_OFF => 'Не действует'
		);
		
		return $status === null ? $desc : $desc[$status];
	}

	public static function getPeriodDesc($period = null)
	{
		return $period ? (isset(self::$periodDesc[$period]) ? self::$periodDesc[$period] : null) : self::$periodDesc;
	}

	protected function incrementByPeriod($time, $period)
	{
		assert('!empty($time) && !empty($period)');
		if (!isset(self::$periodIncrement[$period]))
			throw new CException('Invalid period');

		return strtotime(self::$periodIncrement[$period], $time);
	}

	/**
	 * Возвращает банлист
	 * @return array
	 */
	public static function getBanlist()
	{
		if (self::$banlist)
			return self::$banlist;
		
		// пробуем достать банлист из кэша
		$banlist = Yii::app()->cache->get(self::CACHE_BANLIST_KEY);
		if ($banlist)
		{
			self::$banlist = $banlist;
			return $banlist;
		}
		
		self::model()->setDisabledBehaviors(array('timestamp', 'actionLog'));
		
		// создаем пустой банлист
		$banlist = array();
		foreach (self::getAllTypes() as $type)
		{
			$banlist[$type] = array();
		}

		// извлекаем актуальные баны из БД
		$cmd = self::model()->getDbConnection()->createCommand();
		$cmd->from(self::model()->tableName())
			->where('status = :status 
					AND ban_start <= :time 
					AND ban_end >= :time');
		
		$bans = $cmd->query(array(':status' => self::STATUS_ON, 
						  ':time'   => date('Y-m-d H:i:s')));
	
		// добавляем баны в банлист
		$minTime = false;
		foreach($bans as $ban)
		{
			$end = strtotime($ban['ban_end']);
			if ($minTime === false || $minTime > $end)
				$minTime = $end;
			
			$value = self::getValueByType($ban['type'], $ban['user_id'], $ban['ip'], $ban['user_agent'], $ban['cookie']);
			$banlist[$ban['type']][$value] = $end;
			
			// если поле user_id не пустое, добавляем бан для данного userId
			if ($ban['user_id'])
				$banlist[self::TYPE_USER_ID][self::getValueByType(self::TYPE_USER_ID, $ban['user_id'])] = $end;
		}
		
		// кэшируем банлист
		$expire     = $minTime - time(); // на минимальное время бана
		$dependency = new TagsCacheDependency(array(self::CACHE_TAG), $expire, true); // тэгированный кэш с защитой от состояния гонки
		Yii::app()->cache->set(self::CACHE_BANLIST_KEY, $banlist, 0, $dependency);
		
		return self::$banlist = $banlist;
	}

	/**
	 * Возвращает значение, по которому пользователь забанен
	 * @return string
	 */
	public function getValue($returnHash = true)
	{
		return self::getValueByType($this->type, $this->user_id, $this->ip, $this->user_agent, $this->cookie, $returnHash);
	}

	/**
	 * Возвращает значение, по которому пользователь забанен
	 */
	public static function getValueByType($type, $userId = null, $ip = null, $userAgent = null, $cookie = null, $returnHash = true)
	{
		self::assertTypeExist($type);
		switch ($type)
		{
			case self::TYPE_USER_ID:
				return $userId;
			break;
			
			case self::TYPE_IP:
				return $ip;
			break;
			
			case self::TYPE_UA:
				return !is_null($userAgent) ? ( $returnHash ? md5($userAgent) : $userAgent) : null;
			break;
			
			case self::TYPE_IP_UA:
				return !is_null($ip) && !is_null($userAgent) ? ( $returnHash ? md5($ip.$userAgent) : $ip.', '.$userAgent) : null;
			break;
			
			case self::TYPE_COOKIE:
				return !is_null($cookie) ? ( $returnHash ? md5($cookie) : $cookie) : null;
			break;
			
			case self::TYPE_SUBNET:
				return !is_null($ip) ? self::subnetOfIp($ip) : null;
			break;
			
			default:
				return null;
		}
	}

	public function getSubnetOfIp()
	{
		return self::subnetOfIp($this->ip);
	}

	public static function subnetOfIp($ip)
	{
		assert('!empty($ip)');
		return substr($ip, 0, strrpos($ip, '.'));
	}

  /**
   * проверяет, забанен ли пользователь
   * возвращает false, если не забанен, в противном случае - timestamp окончания бана
   * @param string | null $userId
   * @param string | null $ip
   * @param string | null $userAgent
   * @param string | null $cookie
   * @return int | false
   */
	public static function check($userId = null, $ip = null, $userAgent = null, $cookie = null)
	{
		foreach(self::getAllTypes() as $type)
		{
      // извращенческая логика обработки куки
      $cookieParam = $type == self::TYPE_COOKIE ? $cookie : null;
			$key = self::getValueByType($type, $userId, $ip, $userAgent, $cookieParam);
			if($key === null) continue;

			$time = self::getBanlist()[$type][$key] ?? null;
			if($time >= time()) return $time;
		}

		// пользователь не зобанен
		return false;
	}


    public function getCountAllBansPerUser($user_id)
    {
		assert('is_numeric($user_id)');
		return $this->dbConnection->createCommand('SELECT COUNT(*) FROM {{bans}} WHERE user_id=:user_id')->queryScalar(array('user_id' => $user_id));
    }

	public function scopes()
	{
		$userTypes = self::getAllTypes();
		unset($userTypes[array_search(self::TYPE_SUBNET, $userTypes)]);
		$userCriteria = new CDbCriteria();
		$userCriteria->addInCondition('type', $userTypes);
		$userCriteria->order = 'ban_start DESC';
		
		$subnetCriteria = new CDbCriteria();
		$subnetCriteria->addColumnCondition(array('type' => self::TYPE_SUBNET));
		$subnetCriteria->order = 'ban_start DESC';
		
		return array(
			'typeUser' => $userCriteria,
			'typeSubnet' => $subnetCriteria,
		);
	}

	public static function getKeyValuePairsForBanUrl($data)
	{
		assert('!empty($data)');
		$pairs = array();
		if (is_array($data))
			foreach ($data as $k=>$v){
				
				// Попытаюсь объяснить для чего нужна эта строка:
				// При вызове контроллера бана, все входящие параметры ему передаются в пути урла.
				// URL выглядит следующим образом: /moderator/ban/create/Ban[ip]/192.168.151.76/Ban[user_agent]/Mozilla..../ и т.д.
				// Некоторые параметры (например user_agent или return_url) могут содержать слэши. Поэтому их нужно кодировать в urlencode
				// Yii, при парсинге урла, первыv делом декодирует его, потом разбивает по слэшам, ищет пары ключ=>значение, и засовывает все это в $_REQUEST
				// Таким образом, чтобы наши кодированные слэши не вылезли до момента разбиения, нам нужно кодировать их дважды.
				// Но контроллер может передать уже кодированное значение. Поэтому мы сначала выполняем urldecode()
				
				$v = urlencode(urlencode(urldecode($v)));
				
				$pairs['Ban['.$k.']'] = $v;
				
			}

		return $pairs;
	}

	/**
	 * Фабричный метод, возвращает бан IP
	 * @param string $ip
	 */
	public static function createForIp($ip)
	{
		assert('!empty($ip)');
		$ban         = new static;
		$ban->type   = self::TYPE_IP;
		$ban->name   = 'Бан IP '.$ip;
		$ban->status = self::STATUS_ON;
		$ban->ip     = $ip;
		$ban->period = self::PERIOD_1_MONTH;
		
		return $ban;
	}

	/**
	 * Фабричный метод, возвращает бан SUBNET
	 * @param string $ip
	 */
	public static function createForSubnet($ip)
	{
		assert('!empty($ip)');
		$subnet = self::subnetOfIp($ip);

		$ban         = new static;
		$ban->type   = self::TYPE_SUBNET;
		$ban->name   = 'Бан SUBNET '.$subnet;
		$ban->status = self::STATUS_ON;
		$ban->ip     = $ip;
		$ban->period = self::PERIOD_1_HOUR;
		$ban->subnet = $subnet;
		
		return $ban;
	}

	/**
	 * @param $model Publication
	 * @param $comments array|Comment[]
	 */
	public static function banUsersFromComments($model, $comments)
	{
		assert('!empty($comments)');
		foreach ($comments as $comment)
		{
			$ban = new Ban();
			$ban->period = Ban::PERIOD_3_DAYS;
			$ban->ban_start = DateHelper::mysqlDate(time());

			$ban->ip = $comment->user_ip;
			$ban->user_agent = $comment->user_agent;
			$ban->cookie = $comment->user_cookie;
			if ($comment->user_id)
			{
				$ban->user_id = $comment->user_id;
			}

			$ban->comment = $comment->body;
			$ban->pub_id = $model->id;
			$ban->pub_type = $model->type;

			$ban->type = Ban::TYPE_IP;
			$ban->save();
		}
	}

	public static function assertTypeExist($type){
		assert('!empty($type) && array_key_exists($type, Ban::$typeDescription)');
	}
}
