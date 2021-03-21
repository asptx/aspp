<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
echo '<menu><ul>';
if(empty($q) && empty($g_id) && empty($s)){
	$style = 'class="current" ';
}
else{
	$style = '';
}
echo '<span class="menu indt_10"><li class="no_ls"><a href="'.$admin_page.'?q=logout">'.$trans['left_menu']['lm1'].'</a></li></span>
<span class="menu indt_3"><li class="no_ls"><a '.$style.'href="'.$admin_page.'">'.$trans['left_menu']['lm2'].'</a></li></span>';
if(!empty($g_id) && empty($s) && $q != 's_create'){
	echo '<span class="menu indt_3"><li class="no_ls"><a href="'.$admin_page.'?q=g_delete&g='.$g_id.'" onclick="if(cnf){return confirm(\'Delete this group?\') ? true : false;}">'.$trans['left_menu']['lm3'].'</a></li></span>';
}
if($q == 's_create'){
	$style = 'class="current" ';
}
else{
	$style = '';
}
if(!empty($g_id)){
	echo '<span class="menu indt_3"><li class="no_ls"><a '.$style.'href="'.$admin_page.'?q=s_create&g='.$g_id.'">'.$trans['left_menu']['lm4'].'</a></li></span>';
}
if(!empty($g_id) && empty($s) && $q != 's_create' && file_exists($folder_log.'/'.$g_id.'.db')){
	$db = new SQLite3($folder_log.'/'.$g_id.'.db');
	$db->busyTimeout($timeout_2);
	$db->exec("PRAGMA journal_mode = WAL;");
	$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table';");
	$array = $res->fetchArray(SQLITE3_ASSOC);
	$db->close();
	if(!empty($array['name'])){
		echo '<span class="menu indt_3"><li class="no_ls"><a href="'.$admin_page.'?q=g_del_log&g='.$g_id.'" onclick="if(cnf){return confirm(\'Delete this log?\') ? true : false;}">'.$trans['left_menu']['lm9'].'</a></li></span>';
	}
}
if(!empty($g_id) && !empty($s)){
	echo '<span class="menu indt_3"><li class="no_ls"><a href="'.$admin_page.'?q=s_delete&g='.$g_id.'&s='.$s.'&n='.$s_name.'" onclick="if(cnf){return confirm(\'Delete this stream?\') ? true : false;}">'.$trans['left_menu']['lm5'].'</a></li></span>';
}
if(!empty($g_id) && !empty($s)){
	$db = new SQLite3($folder_log.'/'.$g_id.'.db');
	$db->busyTimeout($timeout_2);
	$db->exec("PRAGMA journal_mode = WAL;");
	$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table';");
	while($array = $res->fetchArray(SQLITE3_ASSOC)){
		$t_temp = $array['name'];
		if($db->querySingle("SELECT * FROM '$t_temp' WHERE nstream = '$s_name';")){
			echo '<span class="menu indt_3"><li class="no_ls"><a href="'.$admin_page.'?q=s_del_log&g='.$g_id.'&s='.$s.'&n='.$s_name.'" onclick="if(cnf){return confirm(\'Delete this log?\') ? true : false;}">'.$trans['left_menu']['lm9'].'</a></li></span>';
			break;
		}
	}
	$db->close();
}
if($q == 'editor'){
	$style = 'class="current" ';
}
else{
	$style = '';
}
echo '<span class="menu indt_3"><li class="no_ls"><a '.$style.'href="'.$admin_page.'?q=editor">'.$trans['left_menu']['lm8'].'</a></li></span>';
$files = scandir($folder_ini);
$x = 0;
$y = '';
foreach($files as $v){
	if($v != "." && $v != ".." && $v != ".htaccess"){
		$a = unserialize(file_get_contents($folder_ini.'/'.$v));
		$g_n = $a[0]['g_name'];
		$g_s = $a[0]['g_status'];
		$f_n = str_ireplace('.ini', '', $v);
		if($g_s == '0'){
			$gx = 'off';
		}
		else{
			$gx = 'on';
		}
		if($g_id == $f_n){$g_current = ' current';}
		else{$g_current = '';}
		$g_style_a = 'class="'.$gx.$g_current.'"';
		$data[] = array("g_name" => "$g_n", "f_name"=>"$f_n", "g_style_a"=>"$g_style_a");
	}
}
if(!empty($data)){
	echo '</ul>
<ul><div class="title_menu align_center bold">'.$trans['left_menu']['lm6'].'</div>';
	sort($data);
	foreach($data as $v){
		echo '<span class="menu indt_3"><li class="icon_g"><a '.$v['g_style_a'].' href="'.$admin_page.'?g='.$v['f_name'].'">'.$v['g_name'].'</a></li></span>';
		$x++;
	}
}
echo '</ul>';
if(file_exists($folder_ini.'/'.$g_id.'.ini')){
	$g_data = unserialize(file_get_contents($folder_ini.'/'.$g_id.'.ini'));
	$count_s = count($g_data);
	$count_s--;
	if(!empty($g_data[1])){
		echo '<ul><div class="title_menu align_center bold">'.$trans['left_menu']['lm7'].'</div></ul>
<ul id="sorting">';
		$x = 1;
		while(!empty($g_data[$x])){
			if($g_data[$x]['s_status'] == '0'){
				$sx = 'off';
			}
			else{
				$sx = 'on';
			}
			if($g_data[$x]['s_name'] == $s_name){
				$s_current = ' current';
			}
			else{
				$s_current = '';
			}
			$s_style = 'class="'.$sx.$s_current.'"';
			echo '<span id="sid_'.$x.'" class="menu indt_3"><li class="icon_s"><a '.$s_style.' href="'.$admin_page.'?g='.$g_id.'&s='.$x.'">'.$g_data[$x]['s_name'].'</a></li></span>';
			$x++;
		}
		echo '</ul>';
	}
}
echo '</menu>';
?>