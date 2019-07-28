<?php 
/**
 * @var Page\AmpPage $this
 */

// вот этот хитрый символ в html теге - реально нужен
?><!doctype html>
<html ⚡ lang="ru">
<head>
  <meta charset="utf-8" />
  <title><?=$this->getMainBlock()->getPageTitle()?></title>
  <link rel="canonical" href="<?=$this->getMainBlock()->getUrlCanonical() ?: $this->getUrlCanonical()?>" />
  <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
  <meta name="referrer" content="origin">
  <script async src="https://cdn.ampproject.org/v0.js"></script>
  <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
  <script async custom-element="amp-accordion" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>
  <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
  <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
  <?php \PageHead::display() ?>
  <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
  <style amp-custom><?php require 'AmpPage.css'; ?></style>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:b,i,500,400&subset=cyrillic">
</head>
<body class="page">
  <?php $this->displayView('SidebarBlock')?>
  <div class="page__container">
    <amp-analytics type="googleanalytics">
    <script type="application/json">{"vars":{"account":"UA-3365753-21"},"triggers":{"trackPageview":{"on":"visible","request":"pageview"}}}</script>
    </amp-analytics>
    <amp-analytics type="metrika">
      <script type="application/json">{
        "vars":{"counterId":"49156609"},
        "triggers": {
          "notBounce": {
              "on": "timer",
              "timerSpec": {
                  "immediate": false,
                  "interval": 15,
                  "maxTimerLength": 16
              },
          "request": "notBounce"
          }
        }}</script>
    </amp-analytics>
    <?php
    $this
      ->displayView('Header')
      ->displayMainBlock()
      ->displayView('Footer');
    ?>

  </div>
</body>
</html>
