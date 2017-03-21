<?php
function ValidateGet() {
	// Array of all allowed GET keys. If an requested key is not listed here, an error will be thrown.
	$allowedKeys = array('page', 'boardview', 'action', 'threadID', 'form', 'ajaxSend', 'pageNo', 'Tab', 'Image', 'User', 'sortField', 'direction', 'postID', 'token', 'subPage', 'avatarAction');

	include_once('./system/interface/errorpage.php');
	
	foreach ($_GET as $key => $value)
	{
		if (!empty($key)) {
			if (!in_array($key, $allowedKeys)) {
					throwError($getIssue);
						exit();
			}
		} else
				exit();
	}
	
	if(isset($_GET['pageNo']) && (empty($_GET['pageNo']) || $_GET['pageNo'] < 1))
	{
		throwError($getIssue);
		exit();
	}
}
?>