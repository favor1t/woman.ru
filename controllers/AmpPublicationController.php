<?php

declare(strict_types = 1);

// #93
class AmpPublicationController extends Controller 
{


	// вот это выводит StarPerson, да
	public function actionIndex()
	{
   	$this->runAction(new AmpStarPersonPageAction($this));
	}


	// а вот это выводит статью
	public function actions()
	{
		return
		[
			'view' => 'application.components.actions.amp.AmpPublicationViewAction',
		];
	}



	public function getTemplatePath() : String
	{
		return \Yii::app()->basePath.'/Template/Block/Amp/Publication';
	}



	/*
	 * Помогает старому движку дотянуться до вьюшек нового дивжка, для самовывода блоков из body публикации
	 */
	public function renderBodyBlock($viewFile, $data = null, $return = false)
	{
		$file = $this->getTemplatePath().'/Blocks/'.$viewFile.'.tpl';
		if(! file_exists($file)) throw new Exception('can not render body block: '.$viewFile);

		return $this->renderFile($file, $data, $return);
	}



};