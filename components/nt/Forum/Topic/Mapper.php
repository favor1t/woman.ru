<?php

declare(strict_types = 1);

namespace nt\Forum\Topic;

/**
 * мэппер для работы с темами форума
 * Class Mapper
 */
class Mapper
{
  use \nt\traits\MapperWithCache;

  /**
   * возвращает тему на основании ID или null
   * @param int $topicId
   * @return \nt\Forum\Topic | null
   */
  protected static function getByIdFromDbOrNull($topicId) : ?\nt\Forum\Topic
  {
    $tableName = \ForumThreadArchiveHelper::getSqlTableNameByThreadId($topicId);
    
    $array = \Db::fetchAsArray('
      select id, status, status_ext, type, sections section_id, tags tag_id, created_at, updated_at, user_id, user_name, title, name, body, answers_all answer_count_all, answers answer_count_visible, answers_3h answer_count_3h, answers_12h answer_count_12h, answers_1d answer_count_1d, answers_3d answer_count_3d, answers_7d answer_count_7d, answers_30d answer_count_30d, anonymously is_anonymous, anonymous_id, _extra, comments_mode comment_mode, user_agent, user_cookie, user_ip, title_postfix, last_comment_date date_last_comment, expert_answer
      from '.$tableName.'
      where id = :id
      limit 1',
      [ ':id' => $topicId, ]);
    if(! $array)
    {
      \ErrorLogHelper::createByMessage('can not get topic by id: '.$topicId.' (use table: '.$tableName.')');
      return null;
    }

    $arrExtra = json_decode($array['_extra'], $doArray = true);
    $array['user_avatar_id']   = isset($arrExtra['userpic_id']) ? (int) $arrExtra['userpic_id'] : 0;
    $array['image_collection'] = isset($arrExtra['images'])     ? $arrExtra['images']           : [];
    $array['image_collection'] = \nt\Image\Collection::fromArray($array['image_collection']);
    unset($array['_extra']);

    // cast to need type
    $array['is_anonymous'] = (bool) $array['is_anonymous'];
    $array['user_ip']      = (string) $array['user_ip'];
    $array['mail_from_form']      = $arrExtra['email_form'] ?? '';

    return \nt\Forum\Topic::fromArray($array);
  }

};

