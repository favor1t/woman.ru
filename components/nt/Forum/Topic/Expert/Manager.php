<?php

declare(strict_types = 1);

namespace nt\Forum\Topic\Expert;

/**
 * манагер для работы с экспертными темами форума
 * Class ExpertManager
 */
class Manager extends Mapper
{
    public static function getMessageByPage(int $page = 1) : array
    {
        return \nt\Cache::get(static::class, $page, function($page)
        {
            return self::createObjectsArrayFromArray(self::getRealMessageByPage($page));
        }, $expire = 60*15);
    }

    private static function createObjectsArrayFromArray(array $expertMessageList) : array
    {
        $createMessage = function($array) {
            $arrExtra = json_decode($array['_extra'], $doArray = true);
            $array['user_avatar_id']   = isset($arrExtra['userpic_id']) ? (int) $arrExtra['userpic_id'] : 0;
            $array['image_collection'] = isset($arrExtra['images'])     ? $arrExtra['images']           : [];
            $array['image_collection'] = \nt\Image\Collection::fromArray($array['image_collection']);
            unset($array['_extra']);

            // cast to need type
            $array['is_anonymous'] = (bool) $array['is_anonymous'];
            $array['user_ip']      = (string) $array['user_ip'];

            return \nt\Forum\Message::fromArray($array);
        };

        $result = [];
        foreach ($expertMessageList as $array){
            $topic = \nt\Forum\Expert::getById((int)$array['topic_id']);
            $experMessage =  $createMessage($array);
            $topic->setExpertMessage($experMessage);
            $result[] = $topic;

        }

        return $result;
    }

    public static function getCountMessage(array $array = [], $cache = true) : int
    {
      if($cache){
        return \nt\Cache::get(static::class.'~'.__METHOD__.implode('~',$array), 0, function() use ($array) {
          return self::getRealCountMessage($array);
        }, $expire = 60*15);
      }
      return self::getRealCountMessage($array);
    }
};