<?php

$template = '_relatedWithImages';

$model = isset($model) ? $model : $block->owner;
if ($model) {
	$this->renderBodyBlock($template, ['model' => $model]);
}