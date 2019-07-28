<?php
declare(strict_types = 1);

namespace Block\Amp;

use nt\Forum\Topic;

/**
 * Class TopicBlock
 * @package Block\Amp
 */
class TopicBlock extends \BlockBase
{
  use
    \nt\traits\HasSection,
    \nt\traits\HasSubSection,
    \nt\traits\HasTopic,
    \nt\traits\HasPage;

  /**
   * инициализация блока
   * @return TopicBlock
   */
  public function init()
  {

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $this->setPage($page);

    list($section, $subSection) = \mobile\helpers\SectionHelper::sectionFromGet();

    $this->setForumTopic(Topic::getByIdOrNull((int) $_GET['id']))->getForumTopic()->checkUrl();
    $this->checkPageAndRedirect();

    $this->setSection($section);
    $this->setSubSection($subSection);

    $expertMessage = current(\nt\Forum\Message::getMessageExpertByTopic($this->getForumTopic()));
    $pageMax = ceil($this->getForumTopic()->getAnswerCountAll() / \Yii::app()->params['limits']['messagesOnThread']); // ceil, т.к. считаем от единицы
    if($pageMax < 1) $pageMax = 1;

    if( ! $this->getForumTopic()->isAmp() ) \Yii::app()->controller->redirect(
      str_replace('www.', 'm.', $this->getForumTopic()->getSiteUrl($absolute = true)),
      $terminate = true,
      $statusCode = 302
    );


    if($this->getPage() > $pageMax) throw new \CHttpException(404, 'Страница не найдена');
    if(empty($expertMessage)) $expertMessage = null;

    $this->addView('AuthorBlock',
      (new \Block\Amp\Topic\AuthorBlock())
      ->setForumTopic($this->getForumTopic())
      ->setSection($this->getSection())
      ->setSubSection($this->getSubSection())
    );

    $this->addView('ExpertBlock',
      (new \Block\Amp\Topic\ExpertBlock())
        ->setForumTopic($this->getForumTopic())
        ->setExpertMessage($expertMessage)
    );

    $this->addView('CommentsBlock',
      (new \Block\Amp\Topic\CommentsBlock())
        ->setForumTopic($this->getForumTopic())
        ->setPage($page)
    );

    $pagerBasicUrl = str_replace('m.', 'amp.', \Yii::app()->params['baseMobileUrl'].$this->getForumTopic()->getSiteUrl());

    $this->addView('PagerBlock',
      (new \Block\Amp\Topic\PagerBlock())
        ->setForumTopic($this->getForumTopic())
        ->setPage($page)
        ->setMaxPage((int)$pageMax)
        ->setUrl($pagerBasicUrl)
    );

    $this->addView('FooterPagerBlock',
      (new \Block\Amp\Topic\PagerBlock())
        ->setForumTopic($this->getForumTopic())
        ->setPage($page)
        ->setMaxPage((int)$pageMax)
        ->setUrl($pagerBasicUrl)
        ->setIsSticky(false)
    );

    return parent::init();
  }

  /**
   * @return mixed
   */
  public function getSection()
  {
    return $this->section;
  }


  /**
   * @return mixed
   */
  public function getSubSection()
  {
    return $this->subSection;
  }

  /**
   * возвращает title страницы
   * @return string
   */
  public function getPageTitle() : string
  {
    return $this->getForumTopic()->getName() ?? $this->getForumTopic()->getTitle();
  }

  private function checkPageAndRedirect(): void
  {
    if(isset($_GET['page']) && $_GET['page'] <= 1){
      \Yii::app()->controller->redirect($this->getForumTopic()->getSiteUrl(), $terminate = true, $statusCode = 301);
    }
  }

  /**
   * возвращает canonical страницы
   * @return string
   */
  public function getUrlCanonical() : string
  {
    $url = $this->getForumTopic()->getSiteUrl($absolute = true);
    $url = str_replace('amp.', 'www.', $url).( $this->getPage() > 1 ? $this->getPage().'/' : '');
    return $url;
  }
}