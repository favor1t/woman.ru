<?php

declare(strict_types = 1);

namespace nt\Forum\Topic\Expert;

/**
 * мэппер для работы с экспертными темами форума
 * Class ExpertMapper
 */
class Mapper
{

    protected static function getRealMessageByPage(int $page = 1) : array
    {
        $offset = ($page - 1) * \ForumThread::EXPERT_MESSAGE_PER_PAGE_LIMIT;
        return \Db::fetchAllAsArray('
                SELECT id, thread_id topic_id, status, user_id, anonymously is_anonymous, anonymous_id, body, created_at, user_agent, user_cookie, user_ip, user_name, _extra
                FROM {{forum_messages}} fm
                WHERE 1 = 1
                    AND user_id IN ('.implode(',', array_map('intval', \Expert::getUserIdsAll())).')
                    AND (
                        fm.sections = \'{}\'
                        OR
                        EXISTS (
                            SELECT 1 
                            FROM {{expert_info}} ei 
                            WHERE 1 = 1
                                AND user_id = fm.user_id 
                                AND ei.section_id && fm.sections 
                                LIMIT 1
                         )
                     )
                    AND status = '.\ForumMessage::STATUS_ON . '
                ORDER BY created_at DESC
                LIMIT ' . \ForumThread::EXPERT_MESSAGE_PER_PAGE_LIMIT . '
                OFFSET ' .$offset
        );

    }


    protected static function getRealCountMessage($array = []) : int
    {
        $sqlDateTimeWhere = isset($array['date']) ? 'AND created_at > \''. addslashes($array['date']) .'\' ' : '';
        $expertMessageList = \Db::fetch('
                SELECT count(id)
                FROM {{forum_messages}} fm
                WHERE 1 = 1
                    AND user_id IN (SELECT user_id FROM {{expert_info}})
                    AND EXISTS (
                        SELECT 1 
                        FROM {{expert_info}} ei 
                        WHERE 1 = 1
                            AND user_id = fm.user_id 
                            LIMIT 1
                     )'
          .$sqlDateTimeWhere.
          'AND status = '.\ForumMessage::STATUS_ON
    );
        return (int) $expertMessageList->count;
    }
};