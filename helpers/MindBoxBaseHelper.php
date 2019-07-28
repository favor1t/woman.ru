<?php
declare(strict_types=1);

class MindBoxBaseHelper
{

  use Singleton;
  /*** @var string */
  private $baseUrl      = 'https://api.mindbox.ru/v3/operations/';
  /*** @var string */
  private $operation    = null;
  /*** @var string */
  private $endpointId   = 'woman.ru';
  /*** @var string */
  private $contentType  = 'json';
  /*** @var array */
  private $data         = [];
  /*** @var array */
  private $errors       = [];
  /*** @var string */
  private $charset      = 'utf-8';
  /*** @var bool */
  private $isAsync      = true;

  /*** @var array */
  const OPERATIONS      = [
    self::OPERATION_SEND_NEW_PASSWORD_AFTER_RESET   => self::OPERATION_SEND_NEW_PASSWORD_AFTER_RESET,
    self::OPERATION_SEND_CONFIRM_RESET_PASSWORD     => self::OPERATION_SEND_CONFIRM_RESET_PASSWORD,
    self::OPERATION_SEND_REGISTRATION_MAIL          => self::OPERATION_SEND_REGISTRATION_MAIL,
    self::OPERATION_UPDATE_PROFILE_DATA             => self::OPERATION_UPDATE_PROFILE_DATA,
    self::OPERATION_SEND_CONFIRM_EMAIL              => self::OPERATION_SEND_CONFIRM_EMAIL,
    self::OPERATION_SEND_TOPIC_STATUS_ON            => self::OPERATION_SEND_TOPIC_STATUS_ON,
    self::OPERATION_SEND_TOPIC_STATUS_HOROSCOPE     => self::OPERATION_SEND_TOPIC_STATUS_HOROSCOPE,
    self::OPERATION_SEND_DONT_EXPERT_ANSWER         => self::OPERATION_SEND_DONT_EXPERT_ANSWER,
    self::OPERATION_SEND_EXPERT_ANSWER              => self::OPERATION_SEND_EXPERT_ANSWER,
    self::OPERATION_SEND_TOPIC_STATUS_HIDE          => self::OPERATION_SEND_TOPIC_STATUS_HIDE,
  ];
  const OPERATION_SEND_NEW_PASSWORD_AFTER_RESET     = 'OtpravkaNovogoParolya';
  /*** @var string */
  const OPERATION_SEND_CONFIRM_RESET_PASSWORD       = 'PodtverzhdenieVosstanovleniyaParolya';
  /*** @var string */
  const OPERATION_SEND_REGISTRATION_MAIL            = 'RegistraciyaNaWomanRu';
  /*** @var string */
  const OPERATION_UPDATE_PROFILE_DATA               = 'IzmenitProfilWoman';
  /*** @var string */
  const OPERATION_SEND_CONFIRM_EMAIL                = 'PodtverzhdenieEmailIzProfilya';
  /*** @var string */
  const OPERATION_SEND_TOPIC_STATUS_ON              = 'UvedomlenieOPublikaciiTemy';
  /*** @var string */
  const OPERATION_SEND_TOPIC_STATUS_HOROSCOPE       = 'UvedomlenieOPublikaciiTemyHoroscope';
  /*** @var string */
  const OPERATION_SEND_DONT_EXPERT_ANSWER           = 'expertAnswerWontBeAdded';
  /*** @var string */
  const OPERATION_SEND_EXPERT_ANSWER                = 'expertAnswerAdded';
  /*** @var string */
  const OPERATION_SEND_TOPIC_STATUS_HIDE            = 'threadNotPublishedDefault';

  /*** @var array */
  const CONTENT_TYPES   = [
    self::CONTETN_TYPE_JSON  => self::CONTETN_TYPE_JSON,
    self::CONTENT_TYPE_XML   => self::CONTENT_TYPE_XML,
  ];
  /*** @var string */
  const CONTETN_TYPE_JSON       = 'json';
  /*** @var string */
  const CONTENT_TYPE_XML        = 'xml';

  /*** @var array */
  const SUBSCRIBES_TOPICS       = [
    self::SUBSCRIBE_STARS       => self::SUBSCRIBE_STARS,
    self::SUBSCRIBE_CHILDREN    => self::SUBSCRIBE_CHILDREN,
    self::SUBSCRIBE_FASHION     => self::SUBSCRIBE_FASHION,
    self::SUBSCRIBE_BEAUTY      => self::SUBSCRIBE_BEAUTY,
    self::SUBSCRIBE_DIETS       => self::SUBSCRIBE_DIETS,
    self::SUBSCRIBE_GENERAL     => self::SUBSCRIBE_GENERAL,
  ];
  /*** @var string */
  const SUBSCRIBE_STARS         = 'woman.editorial.stars';
  /*** @var string */
  const SUBSCRIBE_CHILDREN      = 'woman.editorial.children';
  /*** @var string */
  const SUBSCRIBE_FASHION       = 'woman.editorial.fashion';
  /*** @var string */
  const SUBSCRIBE_BEAUTY        = 'woman.editorial.beauty';
  /*** @var string */
  const SUBSCRIBE_DIETS         = 'woman.editorial.diets';
  /*** @var string */
  const SUBSCRIBE_GENERAL       = 'woman.editorial.general';

  /*** @var boolean */
  private $subscribeStars       = false;
  /*** @var boolean */
  private $subscribeChildren    = false;
  /*** @var boolean */
  private $subscribeFashion     = false;
  /*** @var boolean */
  private $subscribeBeauty      = false;
  /*** @var boolean */
  private $subscribeDiets       = false;
  /*** @var boolean */
  private $subscribeGeneral     = false;
  /*** @var User */
  private $user                 = null;
  /*** @var array */
  private $customParams         = [];

  /*** @var boolean */
  private $isSubscriptionsParams = true;

  /*** @return array */
  public function send(): array
  {
    if($this->hasErrors()) {
      return [
        'result' => 'error',
        'error'  => $this->errors,
      ];
    }
    return $this->fetch();
  }

  /*** @return array */
  private function fetch(): array
  {
    $curlUrl        = $this->getUrlForFetch();
    $curlPost       = true;
    $httpHeader     = $this->getHttpHeaderForFetch();
    $postData       = $this->getDataForFetch();
    $returnTransfer = true;
    $timeOut        = 1;


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $curlUrl);
    curl_setopt($ch, CURLOPT_POST,$curlPost);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $returnTransfer);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
    $curlExec = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $result['result']       = 'Ok';
    //TODO: Мы вроде от них ответа больше не ждем, нужна ли нам эта функция
    if($httpCode == 404 ) {
      $this->createErrorByMessage(__CLASS__.": errorCode: $httpCode");
      return [
        'result'  => 'Error',
        'error'   => 'httpCode is '.$httpCode,
      ];
    }

    if (curl_errno($ch))
      {
        $result['status']       = 'Error';
        $result['error']        = curl_error($ch);
        $result['error_code']   = curl_errno($ch);
      }
    else
      {
        $method = 'getResultFrom'.lcfirst($this->contentType).'Answer';
        $result = $this->$method($curlExec);
      }
    curl_close($ch);

    return $result;
  }

  /**
   * @param string $result
   * @return array
   */
  private function getResultFromJsonAnswer(string $result): array
  {
    return (array)json_decode($result);
  }

  /*** @return string */
  private function getUrlForFetch(): string
  {
    $url = $this->baseUrl;

    if($this->isAsync)  $url .= 'async/';
    else                $url .= 'sync/';

    $url .= "?endpointId=$this->endpointId";
    $url .= "&operation=$this->operation";

    return $url;
  }

  /*** @return array */
  private function getHttpHeaderForFetch(): array
  {
    return [
      $this->getContentTypeForFetch(),
      $this->getAuthorizationForFetch(),
      $this->getAcceptForFetch(),
      $this->getAuthorizationForFetch(),
      $this->getUserAgentForFetch(),
      $this->getCustomerIpForFetch(),
    ];
  }

  /*** @return string */
  private function getDataForFetch(): string
  {
    $method = 'getDataAs'.lcfirst($this->contentType).'ForFetch';
    return $this->$method();
  }

  /*** @return string */
  private function getDataAsJsonForFetch(): string
  {
    return json_encode($this->data);
  }

  /*** @return string */
  private function getContentTypeForFetch(): string
  {
    return 'Content-Type: application/'.$this->contentType.'; charset='.$this->charset;
  }

  /*** @return string */
  private function getAcceptForFetch(): string
  {
    return 'Accept: application/'.$this->contentType;
  }

  /*** @return string */
  private function getAuthorizationForFetch(): string
  {
    return 'Authorization: Mindbox secretKey="'.\Yii::app()->params['MindBox']['ApiKey'].'"';
  }

  /*** @return string */
  private function getUserAgentForFetch(): string
  {
    return 'User-Agent: '.(Yii::app()->user->userAgent ?? '');
  }

  /*** @return string */
  private function getCustomerIpForFetch(): string
  {
    return 'X-Customer-IP: '. (Yii::app()->user->ip ?? '');
  }

  /*** @return string */
  public function getContentType(): string
  {
    return $this->contentType;
  }

  /**
   * @param string $contentType
   * @return $this
   */
  protected function setContentType(string $contentType): self
  {
    if(!isset(self::CONTENT_TYPES[$contentType]))
      $this->createErrorByMessage(__CLASS__.": $contentType is not a valid content type");
    $this->contentType = $contentType;
    return $this;
  }

  /*** @return bool */
  protected function isAsync(): bool
  {
    return $this->isAsync;
  }

  /**
   * @param bool $isAsync
   * @return $this
   */
  public function setIsAsync(bool $isAsync): self
  {
    $this->isAsync = $isAsync;
    return $this;
  }

  /**
   * @return string
   */
  public function getOperation(): string
  {
    return $this->operation;
  }

  /**
   * @param string $operation
   * @return $this
   */
  public function setOperation(string $operation): self
  {
    if(!isset(self::OPERATIONS[$operation]))
      $this->createErrorByMessage(__CLASS__.": $operation is not a valid operation value");

    $this->operation = $operation;
    return $this;
  }

  /*** @return bool */
  private function hasErrors(): bool
  {
    return !empty($this->errors);
  }

  /**
   * @param string $errorText
   * @param bool $doWriteToErrorLog
   */
  private function createErrorByMessage(string $errorText, $doWriteToErrorLog = true): void
  {
    if($doWriteToErrorLog)\ErrorLogHelper::createByMessage($errorText);
    $this->errors[] = $errorText;
  }

  /*** @return array */
  protected function getData(): array
  {
    return $this->data;
  }

  /**
   * @param array $data
   * @return $this
   */
  public function setData(array $data): self
  {
    $this->data = $data;
    return $this;
  }

  /*** @return bool */
  protected function isSubscribeStars(): bool
  {
    return $this->subscribeStars;
  }

  /**
   * @param bool $subscribeStars
   * @return $this
   */
  public function setSubscribeStars(bool $subscribeStars): self
  {
    $this->subscribeStars = $subscribeStars;
    return $this;
  }

  /*** @return bool */
  protected function isSubscribeChildren(): bool
  {
    return $this->subscribeChildren;
  }

  /**
   * @param bool $subscribeChildren
   * @return self
   */
  protected function setSubscribeChildren(bool $subscribeChildren): self
  {
    $this->subscribeChildren = $subscribeChildren;
    return $this;
  }

  /*** @return bool */
  protected function isSubscribeFashion(): bool
  {
    return $this->subscribeFashion;
  }

  /*** @param bool $subscribeFashion */
  public function setSubscribeFashion(bool $subscribeFashion)
  {
    $this->subscribeFashion = $subscribeFashion;
  }

  /*** @return bool */
  protected function isSubscribeBeauty(): bool
  {
    return $this->subscribeBeauty;
  }

  /**
   * @param bool $subscribeBeauty
   * @return $this
   */
  public function setSubscribeBeauty(bool $subscribeBeauty): self
  {
    $this->subscribeBeauty = $subscribeBeauty;
    return $this;
  }

  /*** @return bool */
  protected function isSubscribeDiets(): bool
  {
    return $this->subscribeDiets;
  }

  /**
   * @param bool $subscribeDiets
   * @return $this
   */
  public function setSubscribeDiets(bool $subscribeDiets): self
  {
    $this->subscribeDiets = $subscribeDiets;
    return $this;
  }

  /*** @return bool */
  protected function isSubscribeGeneral(): bool
  {
    return $this->subscribeGeneral;
  }

  /**
   * @param bool $subscribeGeneral
   * @return $this
   */
  public function setSubscribeGeneral(bool $subscribeGeneral): self
  {
    $this->subscribeGeneral = $subscribeGeneral;
    return $this;
  }

  /**
   * @param bool $subscribeAll
   * @return $this
   */
  public function setSubscribeAll(bool $subscribeAll): self
  {
    $this->setSubscribeBeauty($subscribeAll);
    $this->setSubscribeChildren($subscribeAll);
    $this->setSubscribeDiets($subscribeAll);
    $this->setSubscribeFashion($subscribeAll);
    $this->setSubscribeGeneral($subscribeAll);
    $this->setSubscribeStars($subscribeAll);
    return $this;
  }

  /*** @return array */
  public function getSubsribesAll(): array
  {
    return [
      self::SUBSCRIBE_STARS         => $this->isSubscribeStars(),
      self::SUBSCRIBE_CHILDREN      => $this->isSubscribeChildren(),
      self::SUBSCRIBE_FASHION       => $this->isSubscribeFashion(),
      self::SUBSCRIBE_BEAUTY        => $this->isSubscribeBeauty(),
      self::SUBSCRIBE_DIETS         => $this->isSubscribeDiets(),
      self::SUBSCRIBE_GENERAL       => $this->isSubscribeGeneral(),
    ];
  }

  /*** @return User */
  protected function getUser(): User
  {
    return $this->user;
  }

  /**
   * @param User $user
   * @return $this
   */
  public function setUser(User $user): self
  {
    $this->user = $user;
    return $this;
  }

  /*** @return array */
  protected function getCustomParameters(): array
  {
    return $this->customParams;
  }

  /**
   * @param string $key
   * @param $value
   * @return $this
   */
  public function setCustomParameter(string $key, $value): self
  {
    $this->customParams[$key] = $value;
    return $this;
  }

  /**
   * @param array $array
   * @return $this
   */
  public function setCustomParameters(array $array): self
  {
    foreach ($array as $key => $value)
      $this->customParams[$key] = $value;
    return $this;
  }

  /*** @return bool */
  public function isSubscriptionsParams(): bool
  {
    return $this->isSubscriptionsParams;
  }

  /**
   * @param bool $isSubscriptionsParams
   * @return $this
   */
  public function setIsSubscriptionsParams(bool $isSubscriptionsParams): self
  {
    $this->isSubscriptionsParams = $isSubscriptionsParams;
    return $this;
  }



};