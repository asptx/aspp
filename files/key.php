<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if(!empty($g_id) && $q == 'keys'){
	echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.'</div>';
	$db = new SQLite3($folder_log.'/'.$g_id.'.db');
	$db->busyTimeout($timeout_2);
	$db->exec("PRAGMA journal_mode = WAL;");
	if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table';")){
		$k = date('Y-m-d', $table);
	}
	if(file_exists($folder_keys.'/'.$g_name.'/'.$k.'.dat') && empty($s_name)){
		$keys_f = file_get_contents($folder_keys.'/'.$g_name.'/'.$k.'.dat');
	}
	else{
		$keys_f = '';
	}
	if(file_exists($folder_keys.'/'.$g_name.'/'.$k.'-se.dat') && empty($s_name)){
		$keys_f_se = "\n***** Search Engines *****\n".file_get_contents($folder_keys.'/'.$g_name.'/'.$k.'-se.dat');
	}
	else{
		$keys_f_se = '';
	}
	$keys = $keys_f.$keys_f_se;
	if(!empty($keys)){
		echo '<div class="indt_20 indb_10">
<a name="keywords"></a>
<form method="#" action="#">
<div class="align_left">
<textarea id="code" class="ta" name="keywords">'.$keys_f.$keys_f_se.'</textarea>
</div>
</form>
</div>
<script>
    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
      lineNumbers: true,
    });
</script>';
	}
}
?>