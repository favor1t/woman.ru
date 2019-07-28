<?php
declare(strict_types=1);

/**
 * Class MindBoxHelper
 */
class MindBoxHelper extends MindBoxBaseHelper
{
  /*** @return array */
  public function sendNewPasswordAfterReset(): array
  {
    return $this
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_NEW_PASSWORD_AFTER_RESET)
      ->setIsSubscriptionsParams(false)
      ->setIsAsync(false)
      ->setData($this->initData())
      ->send();
  }

  /*** @return array */
  public function sendConfirmResetPassword(): array
  {
    return $this
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_CONFIRM_RESET_PASSWORD)
      ->setIsAsync(false)
      ->setIsSubscriptionsParams(false)
      ->setData($this->initData())
      ->send();
  }

  /*** @return array */
  public function sendRegistrationMail(): array
  {
    return $this
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_REGISTRATION_MAIL)
      ->setIsAsync(false)
      ->setData($this->initData())
      ->send();
  }

  /*** @return array */
  public function sendUserProfileData(): array
  {
    return $this
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_UPDATE_PROFILE_DATA)
      ->setIsAsync(false)
      ->setData($this->initData())
      ->send();
  }

  /*** @return array */
  public function sendConfirmEmail(): array
  {
    return $this
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_CONFIRM_EMAIL)
      ->setIsAsync(false)
      ->setIsSubscriptionsParams(false)
      ->setData($this->initData())
      ->send();
  }

  /*** @return array */
  public function sendStatusTopicEmail(int $status, array $params): array
  {
    if(
      ( $status >= ForumThread::EXT_STATUS_HIDE_SPAM
        && $status <= ForumThread::EXT_STATUS_HIDE_HOLYWAR )
      || $status === ForumThread::STATUS_HIDE
    ){
      $this->setRejectionReasonByStatus($status);
      $status = ForumThread::STATUS_HIDE;
    }

    $methodName = 'sendTopicStatus'.$status.'Email';

    if(method_exists($this,$methodName))
      return $this->$methodName($params);

    return [
      'result' => 'Error',
      'error'  => $methodName.': method is not exist',
    ];
  }


  /*** @param int $status */
  private function setRejectionReasonByStatus(int $status): void
  {
    $rejectionReasons = [
      	ForumThread::EXT_STATUS_HIDE_SPAM         => "Причина: содержит рекламу",
        ForumThread::EXT_STATUS_HIDE_ABUSE        => "Причина: содержит оскорбления или нецензурную брань",
        ForumThread::EXT_STATUS_HIDE_DUBLICATE    => "Причина: дублирует уже существующую тему",
        ForumThread::EXT_STATUS_HIDE_ILLIGAL      => "Причина: нарушает законодательство РФ",
        ForumThread::EXT_STATUS_HIDE_USER_REQUEST => "",
        ForumThread::EXT_STATUS_HIDE_AGGRESSION   => "Причина: содержит агрессию в адрес администрации форума",
        ForumThread::EXT_STATUS_HIDE_HOLYWAR      => "Причина: содержание темы провоцирует на агрессивную реакцию",
    ];

    $rejectionReason = '';
    if(isset($rejectionReasons[$status])) $rejectionReason = $rejectionReasons[$status];

    $this->setCustomParameter('rejectionReason', $rejectionReason);
  }

  /*** @return array */
  private function sendTopicStatus1Email(array $customParams): array
  {
    return $this
      ->setCustomParameter('threadLink', $customParams['link'])
      ->setCustomParameter('threadName', $customParams['title'])
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_TOPIC_STATUS_ON)
      ->setIsAsync(false)
      ->setIsSubscriptionsParams(false)
      ->setData($this->initData())
      ->send();
  }

  /*** @return array */
  private function sendTopicStatus100Email(array $customParams): array
  {
    return $this
      ->setCustomParameter('threadLink', $customParams['link'])
      ->setCustomParameter('threadName', $customParams['title'])
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_TOPIC_STATUS_HOROSCOPE)
      ->setIsAsync(false)
      ->setIsSubscriptionsParams(false)
      ->setData($this->initData())
      ->send();
  }

  /*** @return array */
  private function sendTopicStatus3Email(array $customParams): array
  {
    return $this
      ->setCustomParameter('threadBody', $customParams['threadBody'])
      ->setCustomParameter('threadName', $customParams['title'])
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_TOPIC_STATUS_HIDE)
      ->setIsAsync(false)
      ->setIsSubscriptionsParams(false)
      ->setData($this->initData())
      ->send();
  }


  /**
   * @return array
   */
  public function sendExpertAnswerEmail(): array
  {
    return $this
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_EXPERT_ANSWER)
      ->setIsAsync(false)
      ->setIsSubscriptionsParams(false)
      ->setData($this->initData())
      ->send();
  }

  /**
   * @return array
   */
  public function sendExpertAnswerAbsenceEmail(): array
  {
    return $this
      ->setContentType(self::CONTETN_TYPE_JSON)
      ->setOperation(self::OPERATION_SEND_DONT_EXPERT_ANSWER)
      ->setIsAsync(false)
      ->setIsSubscriptionsParams(false)
      ->setData($this->initData())
      ->send();
  }

  /*** @return array */
  private function initData(): array
  {
    $result = [
      'customer' => $this->getCustomerForInitData(),
      ];
    if(! empty($this->getCustomParameters()))
      $result['emailMailing']['customParameters'] = $this->getCustomParameters();

    return $result;
  }

  /*** @return array */
  private function getCustomerForInitData(): array
  {
    $user = $this->getUser();

    $result = [
      'email'             => $user->email,
    ];

    if($this->isSubscriptionsParams())
      $result['subscriptions'] = $this->getSubscribeForInitData();

    if($this->getOperation() == self::OPERATION_UPDATE_PROFILE_DATA) {
      if(isset($user->id) && ! empty($user->id))
        $result['ids']['womanUserId']  = $user->id;
      if(isset($user->birthday) && ! empty($user->birthday))
        $result['birthDate']    = $user->birthday;
      if(isset($user->place) && ! empty($user->place))
        $result['customFields']['cityByUser']   = $user->place;
      if(isset($user->subscribe_email) && ! empty($user->subscribe_email))
        $result['email']        = $user->subscribe_email;
      if(isset($user->sex) && ! empty($user->sex))
        $result['sex']        = $user->sex;
    }

    return $result;
  }

  /*** @return array */
  private function getSubscribeForInitData(): array
  {
    $subscribesParams = [];
    foreach ( $this->getSubsribesAll() as $topicName => $isSubscribe){
      $subsribe= [
        'topic'           => $topicName,
        'pointOfContact'  => 'email',
        'brand'           => 'Hsmedia',
        'isSubscribed'    => $isSubscribe,
      ];
      if($this->getOperation() !== self::OPERATION_UPDATE_PROFILE_DATA)
        $subsribe['valueByDefault'] = false;

      $subscribesParams[] = $subsribe;
    }

    return $subscribesParams;
  }

  /**
   * @param array $array
   * @return MindBoxHelper
   */
  public function setIsSubscrubeByArray(array $array): self
  {
    foreach ($array as $topicId)
      $this->setIsSubscrubeBySubscribeTopicId((int)$topicId, $isSubscribe = true);
    return $this;
  }

  /**
   * @param int $subscribeToipicId
   * @param bool $isSubscribe
   */
  private function setIsSubscrubeBySubscribeTopicId(int $subscribeToipicId, bool $isSubscribe)
  {
    switch ($subscribeToipicId)
    {
      case 1:
        $this->setSubscribeStars($isSubscribe);
        break;
      case 2:
        $this->setSubscribeBeauty($isSubscribe);
        break;
      case 3:
        $this->setSubscribeFashion($isSubscribe);
        break;
      case 4:
        $this->setSubscribeDiets($isSubscribe);
        break;
      case 6:
        $this->setSubscribeChildren($isSubscribe);
        break;
    }
  }

};