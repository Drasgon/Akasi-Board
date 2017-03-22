<?php

	$config = parse_ini_file(dirname(__FILE__).'/../config/callstack_config.ini');
	$config_messages = $config['messages'];
	
	if($config_messages == TRUE)
	{
		ini_set('display_startup_errors',1);
		ini_set('display_errors',1);
		error_reporting(-1);
	}
	else
	{
		error_reporting(0);
	}
	
	$callstack = debug_backtrace();
	
	// Check if the index.php is present. If not so, the file was loaded directly via ajax 
	// or by evil people who want to do evil things. We should prevent them from doing so :3
	
	function findIndex($callstack, $config_messages)
	{
		$file = end($callstack);
		$file = $file['file'];
		$file = str_replace('\\', '/', $file);
		
		$parts = explode('/', $file);

		$filename = end($parts);
		
		// If people are evil, do evil things to them :3
		if($filename != 'index.php')
		{
			header("Location: http://{$_SERVER['SERVER_NAME']}/");
			exit();
		}
	}
	
	
	findIndex($callstack, $config_messages);
?>