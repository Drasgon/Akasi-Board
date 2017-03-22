<?php
class ErrorHandler
{
	private $development_mode = TRUE;
	
	public function __construct()
	{
		if($this->development_mode == TRUE)
		{
			ini_set('display_startup_errors',1);
			ini_set('display_errors',1);
			error_reporting(-1);
		}
		else
		{
			ini_set('display_startup_errors',0);
			ini_set('display_errors',0);
			error_reporting(0);
		}
	}
}
?>