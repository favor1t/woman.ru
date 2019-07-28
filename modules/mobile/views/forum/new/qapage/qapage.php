<?php
if(!\ForumHelper::isExpertTopic()) return '';

$topic          = QAPageHelper::getInstance()->getTopic();
$arrMessage     = QAPageHelper::getInstance()->getArrMessages();
$expertMessage  = QAPageHelper::getInstance()->getExpertMessage();

if(!$topic) return '';

$bodyTopic = $topic->getBody();
$bodyTopic = Html::restoreHtml($bodyTopic);
$bodyTopic = StopWordHelper::replaceAbuseWords($bodyTopic);
$bodyTopic = Html::encode($bodyTopic);
$bodyTopic = nl2br($bodyTopic);

$datetime         = new DateTime($topic->getCreatedAt());
$dateCreateTopic  = $datetime->format(DateTime::ATOM);

$userNameTopic    = $topic->getUserNameDisplay();

if(! $topic->isAnonymous())
{
  $userFromTopic = $topic->getUserOrNull();
  if($userFromTopic) $userNameTopic = $userFromTopic->getName();
}
if($userNameTopic == '') $userNameTopic = 'Автор';

$userNameTopic    = \Html::encode($userNameTopic);

$urlTopicBase     = $topic->getSiteUrl($absolute = true);
$urlTopicPage     = $urlTopicBase;
if(QAPageHelper::getInstance()->getPageNumber() > 1) $urlTopicPage = $urlTopicBase . QAPageHelper::getInstance()->getPageNumber().'/';

if($expertMessage){
  $bodyExpert = $expertMessage->getBody();
  $bodyExpert = Html::restoreHtml($bodyExpert);
  $bodyExpert = StopWordHelper::replaceAbuseWords($bodyExpert);
  $bodyExpert = Html::encode($bodyExpert);
  $bodyExpert = nl2br($bodyExpert);

  $datetime           = new DateTime($expertMessage->getCreatedAt());
  $dateCreateExpert   = $datetime->format(DateTime::ATOM);

  $userNameExpert         = 'Эксперт';
  $userFromExpertMessage  = $expertMessage->getUserOrNull();
  if($userFromExpertMessage) $userNameExpert = $userFromExpertMessage->getName();
}
?>

<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "QAPage",
    "mainEntity": {
      "@type": "Question",
      "name": "<?=\Html::encode(Html::restoreHtml($topic->getName()))?>",
      "text": "<?=$bodyTopic?>",
      "answerCount": <?=$topic->getAnswerCountAll()?>,
      "dateCreated": "<?=$dateCreateTopic?>",
      "author": {
        "@type": "Person",
        "name": "<?=$userNameTopic?>"
      },
      <?php if($expertMessage): ?>
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "<?=$bodyExpert?>",
        "dateCreated": "<?=$dateCreateExpert?>",
        "url": "<?=$urlTopicBase?>",
        "author": {
          "@type": "Person",
          "name": "<?=$userNameExpert?>"
        }
      },
      <?php endif; ?>
      <?php if(!empty($arrMessage)):
        $countMessage = 0;
      ?>
      "suggestedAnswer": [
      <?php foreach ($arrMessage as $message):
        $countMessage++;
        $body = $message->getBody();
        $body = Html::restoreHtml($body);
        $body = StopWordHelper::replaceAbuseWords($body);
        $body = Html::encode($body);
        $body = nl2br($body);

        $datetime    = new DateTime($message->getCreatedAt());
        $dateCreate  = $datetime->format(DateTime::ATOM);
        $userName    = $message->getUserName();

        if(! $topic->isAnonymous())
        {
          $userFromMessage = $message->getUserOrNull();
          if($userFromMessage) $userName = $userFromMessage->getName();
        }
        if($userName == '') $userName = 'Гость';

        $userName    = \Html::encode($userName);
      ?>
        {
          "@type": "Answer",
          "text": "<?=$body?>",
          "dateCreated": "<?=$dateCreate?>",
          "url": "<?=$urlTopicPage?>#<?=$message->getId()?>",
          "author": {
            "@type": "Person",
            "name": "<?=$userName?>"
          }
        }<?php if($countMessage < count($arrMessage)) echo ','; ?>
        <?php endforeach; ?>
      ]
      <?php endif; ?>
    }
  }
</script>
