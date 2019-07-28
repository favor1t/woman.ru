<?php

declare(strict_types = 1);

namespace nt\Forum\Topic;

/**
 * выгрузка темы форума для нового движка форума
 * Class Exporter
 */
class Exporter
{

  /**
   * @param int $topicId
   * @return array | null
   */
  public static function exportById(int $topicId) : ?array
  {
    $topic = \nt\Forum\Topic::getByIdOrNull($topicId);
    if(! $topic) return null;

    $arrMessage = \Db::fetchAllAsArray("
      select id as message_id, if(status = 0, 'hidden', 'visible') status, user_id, created_at, updated_at, position_number, user_name, user_ip, user_cookie, user_agent, anonymously, anonymous_id, body
      from   {{forum_messages}} 
      where  thread_id = :thread_id and body != ''",
      [ ':thread_id' => $topic->getId(), ]);

    $arrUserId = [ (int) $topic->getUserId(), ];
    foreach($arrMessage as $arrMessageItem) $arrUserId[] = (int) $arrMessageItem['user_id'];
    $arrUserId = array_unique($arrUserId);

    return
    [
      'topic_id'             => $topic->getId(),
      'created_at'           => $topic->getCreatedAt(),
      'updated_at'           => $topic->getUpdatedAt(),
      'status'               => self::getStatus($topic),
      'title'                => $topic->getName(),
      'answer_count_all'     => $topic->getAnswerCountAll(),
      'answer_count_visible' => $topic->getAnswerCountVisible(),
      'answer_count_3h'      => $topic->getAnswerCount3h(),
      'answer_count_12h'     => $topic->getAnswerCount12h(),
      'answer_count_1d'      => $topic->getAnswerCount1d(),
      'answer_count_3d'      => $topic->getAnswerCount3d(),
      'answer_count_7d'      => $topic->getAnswerCount7d(),
      'answer_count_30d'     => $topic->getAnswerCount30d(),
      'last_message_at'      => $topic->getDateLastComment(),
      'table_name'           => \ForumThreadArchiveHelper::getSqlTableNameByThreadId($topic->getId()),
      'user_id'              => $topic->getUserId(),
      'user_name'            => $topic->getUserName(),
      'is_anonymously'       => $topic->getIsAnonymous(),
      'user_cookie'          => $topic->getUserCookie(),
      'user_agent'           => $topic->getuserAgent(),
      'user_ip'              => $topic->getUserIp(),
      'section'              => $topic->getSectionId(),
      'tag'                  => $topic->getTagId(),
      'body'                 => $topic->getBody(),
      'message_page_info'    => \Db::fetchAllAsArray('
                                  select page, message_id 
                                  from   {{forum_thread_pages}} 
                                  where  thread_id = :thread_id',
                                  [ ':thread_id' => $topic->getId(), ]),
      'message'              => $arrMessage,
      'user'                 => \Db::fetchAllAsArray('
                                  select id as user_id, created_at, updated_at, email as mail, status as is_active, (select 1 from {{expert_info}} i where user_id = id) as is_expert, password as password_hash, name as nickname, userpic_id
                                  from {{users}}
                                  where id in ('.implode(',', $arrUserId).')'),
    ];
  }

  private static function getStatus(\nt\Forum\Topic $topic) : string
  {
    $array =
    [
      \IForumThreadStatus::STATUS_AWAITING => [
                                                'null'                                  => 'awaiting_new',
                                                \ForumThread::EXT_STATUS_AWAITING_NEW   => 'awaiting_new',
                                                \ForumThread::EXT_STATUS_AWAITING_DOUBT => 'awaiting_doubt',
                                                \ForumThread::EXT_STATUS_AWAITING_ASIDE => 'awaiting_aside',
                                              ],
      \IForumThreadStatus::STATUS_OPEN   => [ 'null' => 'open',   ],
      \IForumThreadStatus::STATUS_CLOSED => [ 'null' => 'closed', ],
      \IForumThreadStatus::STATUS_HIDE   => [
                                              'null' => 'hidden_on_import',
                                              \ForumThread::EXT_STATUS_HIDE_SPAM         => 'hidden_as_spam',
                                              \ForumThread::EXT_STATUS_HIDE_ABUSE        => 'hidden_as_abuse',
                                              \ForumThread::EXT_STATUS_HIDE_DUBLICATE    => 'hidden_as_dublicate',
                                              \ForumThread::EXT_STATUS_HIDE_ILLIGAL      => 'hidden_as_illigal',
                                              \ForumThread::EXT_STATUS_HIDE_USER_REQUEST => 'hidden_as_user_request',
                                              \ForumThread::EXT_STATUS_HIDE_AGGRESSION   => 'hidden_as_aggression',
                                              \ForumThread::EXT_STATUS_HIDE_HOLYWAR      => 'hidden_as_holywar',
                                            ],
    ];

    $status    = $topic->getStatus();
    $statusExt = $topic->getStatusExt() ?? 'null';
    $result    = $array[$status][$statusExt] ?? null;
    if($result) return $result;
    throw new \Exception('unknown status: '.$status.', '.$statusExt);
  }

};

