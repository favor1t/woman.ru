<?php

declare(strict_types = 1);

namespace nt\Forum\Topic;

/**
 * манагер для работы с темами форума
 * Class Manager
 */
class Manager extends \nt\Forum\Topic\Mapper
{

  /**
   * @param array $arrParam
   * @return \nt\Forum\Topic[] | int
   * @throws \Exception
   */
  public static function getByParam(array $arrParam)
  {
    $createdAtStart = $arrParam['created_at_start'] ?? null;

    $arrSqlWhere = array_filter(
    [
      self::getSqlWhereByStatus($arrParam),
      self::getSqlWhereBySectionInclude($arrParam),
      self::getSqlWhereBySectionExclude($arrParam),
      self::getSqlWhereByUser($arrParam),
      self::getSqlWhereByAnswersAll($arrParam),
      self::getSqlWhereByCreatedAtInterval($arrParam),
      self::getSqlWhereByUpdatedAtInterval($arrParam),
      self::getSqlWhereByAnswersPeriod($arrParam),
      $createdAtStart == '' ? null : "created_at > '".addslashes($createdAtStart)."'",
      $arrParam['sql_where_native'] ?? null,
    ],
    function($value) : bool
    {
      return $value != '';
    });

    $calcCount = $arrParam['calc_count'] ?? false;
    $limit = (int) ($arrParam['limit'] ?? 0);

    $tableName = '{{forum_threads}}';
    if(isset($arrParam['shardYear']) &&  $arrParam['shardYear'] > 0) $tableName = '{{forum_threads_'.$arrParam['shardYear'].'}}';
    if(!isset($arrParam['shardYear'])) $arrParam['shardYear'] = date("Y");

    $result = \Db::fetchAll('
      select '.($calcCount ? 'count(*) cnt' : 'id' ).'
      from '.$tableName.'
      where '.implode(' and ', $arrSqlWhere).'
      '.self::getSqlOrderBy($arrParam).'
      '.self::getSqlOffset($arrParam).'
      '.($limit ? 'limit '.$limit : ''));
    if($calcCount) return $result[0]->cnt;

    // @TODO: это надо запихать в саму тему, в ейный трейт
    $arrForumTopic = [];
    foreach($result as $result)
    {
      $arrForumTopic[] = \nt\Forum\Topic::getById($result->id);
    }

    if(count($arrForumTopic) < $limit && $arrParam['shardYear'] === date("Y")){
      $years = \ForumThreadArchiveHelper::getYearList();
      arsort($years);
      foreach ($years as $year)
      {
          if((int)$year < $arrParam['shardYear']){
           $arrParam['shardYear'] = $year;
           $arrForumTopic = array_merge($arrForumTopic, self::getByParam($arrParam));
           if(count($arrForumTopic) >= $limit) return array_slice($arrForumTopic, 0, $limit);
          }
      }
    }
    return $arrForumTopic;
  }
  /**
   * @param array $arrParam
   * @return int
   * @throws \Exception
   */
  public static function getCount(array $arrParam) : int
  {
    return self::getByParam($arrParam + [ 'calc_count' => true, ]);
  }



  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereByStatus(array $arrParam) : ?string
  {
    if($arrParam['visible_only'] ?? null) return 'status in ('.\IForumThreadStatus::STATUS_OPEN.', '.\IForumThreadStatus::STATUS_CLOSED.')';
    return null;
  }
  /**
   * @param array $arrParam
   * @return null|string
   */
  private static function getSqlWhereByUser(array $arrParam) : ?string
  {
    $user = $arrParam['user'] ?? null;
    return $user ? 'user_id = '.(int) $user->id : null;
  }
  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereBySectionInclude(array $arrParam) : ?string
  {
    $section = $arrParam['section'] ?? null;
    if($section && is_numeric($section)) $section = \nt\Section::getById($section);
    if(! $section || $section->isMain()) return null;

    $arrSectionId = [ (int) $section->getId() => true, ];
    if($arrParam['use_section_child'] ?? null)
    {
      foreach($section->getChild() as $section)
      {
        $arrSectionId[(int) $section->getId()] = true;
      }
    }
    return 'sections && array['.implode(', ', array_keys($arrSectionId)).']';
  }
  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereBySectionExclude(array $arrParam) : ?string
  {
    if($arrParam['skip_section_horoscope'] ?? null) return 'not sections && array['.implode(', ', \SectionHelper::getHoroscopeSectionIds()).']';
    return null;
  }
  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereByAnswersAll(array $arrParam) : ?string
  {
    if(isset($arrParam['answers_all']) &&  $arrParam['answers_all'] > 0 ) return 'answers_all > '.(int) $arrParam['answers_all'];
    return null;
  }
  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereByCreatedAtInterval(array $arrParam) : ?string
  {
    if(isset($arrParam['created_at_interval']) &&  $arrParam['created_at_interval'] > 0 ) return 'created_at > now() - interval \''.$arrParam['created_at_interval'].' days\'';
    return null;
  }
  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereByUpdatedAtInterval(array $arrParam) : ?string
  {
    if(isset($arrParam['updated_at_interval']) &&  $arrParam['updated_at_interval'] > 0 ) return 'updated_at > now() - interval \''.$arrParam['updated_at_interval'].' days\'';
    return null;
  }
  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereByAnswersPeriod(array $arrParam) : ?string
  {
    if(isset($arrParam['answersPeriodSql'])) return addslashes($arrParam['answersPeriodSql']);
    return null;
  }

  /**
   * @param array $arrParam
   * @return string
   * @throws \Exception
   */
  private static function getSqlOrderBy(array $arrParam) : string
  {
    $orderBy = $arrParam['order_by'] ?? null;
    if($orderBy == '') return '';

    $column = preg_replace('# (asc|desc)$#iU', '', $orderBy);
    if(! in_array($column, [ 'created_at', 'updated_at', 'answers_1d', 'answers_3d', 'answers_7d', 'answers_30d', 'answers_3h', 'answers_12h', 'answers_all'])) throw new \Exception('column invalid: '.$column);

    return 'order by '.$orderBy;
  }



  /**
   * #1681: расчет популярных секций форума
   * @return array
   */
  public static function getPopularSectionInfo() : array
  {
    return \Db::fetchAllAsArray("
      select s.id section_id, count(*) topic_cnt
      from   woman_forum_threads t
      /* на всякий случай */
      join   woman_sections s on s.id = t.sections[1]
      where  t.created_at > now() - interval '1 day'
      group  by section_id
      order  by topic_cnt desc
      limit  5");
  }

  /**
   * @param array $arrParam
   * @return null|string
   */
  private static function getSqlOffset(array $arrParam) : string
  {
    if( !isset($arrParam['offset'])) return '';
    if( $arrParam['offset'] <= 0) return '';


    return ' offset '.((int)$arrParam['offset']);
  }

  /**
   * #WMN-2178C получение id топиков с экспертным тэгом
   * № и пустым полем expert_answer
   * @return array
   */
  public static function getExpertTagTopic(int $limit = 100) : array
  {
    return \Db::fetchAll("
      select id
      from   woman_forum_threads 
      where  tags && array[".\Tag::getSiteExpertId()."]  
        and expert_answer is null 
        and 
          (status = ".\ForumThread::STATUS_OPEN." or status = ".\ForumThread::STATUS_CLOSED.")
      limit ".$limit);
  }


};
