<?php
declare(strict_types = 1);

namespace nt\traits;

trait EntityGetUserHash
{
    public function getUserHash() : string
    {
        return \WebUser::createUserHashByParam([
            'isAnonymous'   => (string) method_exists ($this, 'getIsAnonymous') ? $this->getIsAnonymous() : '',
            'userCoockie'   => (string) method_exists ($this, 'getUserCookie')  ? $this->getUserCookie()  : '',
            'anonymousId'   => (string) method_exists ($this, 'getAnonymousId') ? $this->getAnonymousId() : '',
            'userIp'        => (string) method_exists ($this, 'getUserIp')      ? $this->getUserIp()      : '',
            'userAgent'     => (string) method_exists ($this, 'getUserAgent')   ? $this->getUserAgent()   : '',
            'userId'        => (int)    method_exists ($this, 'getUserId')      ? $this->getUserId()      : '',
            ]);

    }

/*    private function createUserHash(array $param) : string
    {
        $isAnonymous    = isset($param['isAnonymous'])  ? $param['isAnonymous'] : true;
        $userCoockie    = isset($param['userCoockie'])  ? $param['userCoockie'] : '';
        $anonymousId    = isset($param['anonymousId'])  ? $param['anonymousId'] : '';
        $userIp         = isset($param['userIp'])       ? $param['userIp']      : '';
        $userAgent      = isset($param['userAgent'])    ? $param['userAgent']   : '';
        $userId         = isset($param['userId'])       ? $param['userId']      : 0;

        if($userId > 0 && ! $isAnonymous )  return (string) $userId;

        if( ! empty($userCoockie))
            return (string) implode('', unpack('L', md5($userCoockie, true)));

        if( ! empty($anonymousId)) return (string) $anonymousId;

        return (string) \ForumMessage::createAnonymousIdByData($userIp, $userAgent);
    }*/

}