<?php
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
define("INDEX", "yes");
require_once '../config.php';
if(!empty($_GET['g'])){
	$g_id = $_GET['g'];
	if(!empty($_POST['sid'])){
		$s_data = unserialize(file_get_contents('../'.$folder_ini.'/'.$g_id.'.ini'));
		$new_s_data = array();
		$new_s_data[] = $s_data[0];
		foreach($_POST['sid'] as $v){
			$v = trim($v);
			if(!empty($v)){
				$new_s_data[] = $s_data[$v];
			}
		}
		$s_data = serialize($new_s_data);
		file_put_contents('../'.$folder_ini.'/'.$g_id.'.ini', $s_data."\n", LOCK_EX);
	}
}
?>