<?php
declare(strict_types = 1);

class AmpForumController extends Controller
{
  public function actions()
  {
    return
      [
        'thread' => 'application.components.actions.amp.AmpTopicViewAction',
      ];
  }
}