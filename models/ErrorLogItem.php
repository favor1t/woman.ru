<?php

// #2522: лог ошибок
class ErrorLogItem extends ErrorLogItemBase
{

	public function save()
	{
		return \ErrorLogHelper::save($this);
	}

};


