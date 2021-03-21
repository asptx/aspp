<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$g_uniq_time = $g_uniq_time / 3600;
$g_f_time = $g_f_time / 3600;
if(empty($s) && $q != 's_create' && $q != 'editor' && $q != 'countries' && $q != 'sources' && $q != 'pb' && $q != 'conf' && $q != 'keys'){
	if(!empty($error)){
		echo '<div class="align_center red bold indt_10">'.$error.'</div>';
	}
	if(empty($g_id)){
		echo '<div class="align_center bold indt_10">'.$trans['group']['g1'].'</div>';
	}
	if(!empty($_GET['g'])){
		$g_get = $_GET['g'];
	}
	else{
		$g_get = '';
	}
	if(!empty($g_id) && empty($error)){
		$db = new SQLite3($folder_log.'/'.$g_id.'.db');
		$db->busyTimeout($timeout_2);
		$db->exec("PRAGMA journal_mode = WAL;");
		$dx = '';
		$lp = $period_log;
		while(true){
			$table_temp = strtotime(date("Y-m-d", strtotime('-'.$lp.' day')));
			if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table_temp';")){
				$dx = date('Y-m-d', $table_temp);
				$dx = explode("-", $dx);
				if(!empty($dx[2])){
					$dx = $dx[2];
				}
				else{
					$dx = date("d", strtotime('-'.$lp.' day'));
				}
				$query = "SELECT COUNT (*) FROM '$table_temp' WHERE bot = '$empty'";
				$ch_visitors = $db->querySingle("$query;");
				$ch_hosts = $db->querySingle("$query AND uniq = 'yes';");
				$ch_wap = $db->querySingle("$query AND operator != '$empty' AND uniq = 'yes';");
				if($chart_bots == 1){
					$ch_bots = $db->querySingle("SELECT COUNT (*) FROM '$table_temp' WHERE bot != '$empty' AND uniq = 'yes';");
				}
				else{
					$ch_bots = 0;
				}
				if(empty($dg)){
					$dg = '[\''.$dx.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
				}
				else{
					$dg = $dg.',[\''.$dx.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
				}
				$ch_google = $db->querySingle("$query AND se = 'google' AND uniq = 'yes';");
				$ch_yandex = $db->querySingle("$query AND se = 'yandex' AND uniq = 'yes';");
				$ch_mail = $db->querySingle("$query AND se = 'mail' AND uniq = 'yes';");
				$ch_yahoo = $db->querySingle("$query AND se = 'yahoo' AND uniq = 'yes';");
				$ch_bing = $db->querySingle("$query AND se = 'bing' AND uniq = 'yes';");
				if($ch_google != 0 || $ch_yandex != 0 || $ch_mail != 0 || $ch_yahoo != 0 || $ch_bing != 0){
					$se = 1;
				}
				if(empty($dg_se)){
					$dg_se = '[\''.$dx.'\','.$ch_google.','.$ch_yandex.','.$ch_mail.','.$ch_yahoo.','.$ch_bing.']';
				}
				else{
					$dg_se = $dg_se.',[\''.$dx.'\','.$ch_google.','.$ch_yandex.','.$ch_mail.','.$ch_yahoo.','.$ch_bing.']';
				}
			}
			else{
				if(empty($dg)){
					$dg = '[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0]';
				}
				else{
					$dg = $dg.',[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0]';
				}
				if(empty($dg_se)){
					$dg_se = '[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0,0]';
				}
				else{
					$dg_se = $dg_se.',[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0,0]';
				}
			}
			if($lp == 0){
				break;
			}
			$lp--;
		}
		$db->close();
		echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.'</div>';
	}
	echo '<div id="curve_chart" class="chart indt_20"></div>';
	if($se == 1){
		echo '<div id="curve_chart_se" class="chart indt_10"></div>';
	}
	echo '<set>
<div class="indt_20"><a class="mobile bold pm" href="#" id="pull_setting">Setting</a></div>
<mob>
<form method="post" action="'.$admin_page.'">
<div class="block">
<div class="item">'.$trans['group']['g3'].'<br><input class="i150" name="g_name" type="text" value="'.$g_name.'" maxlength="30"></div>
<div class="item">'.$trans['group']['g4'].'<br><input class="i150" name="g_id" type="text" value="'.$g_id.'" maxlength="15"></div>
<div class="item"><span>'.$trans['group']['g5'].'</span> <select name="g_redirect" size = "1">
<option'; if($g_redirect == 'api'){echo ' selected="selected"';} echo ' value="api">API</option>
<option'; if($g_redirect == 'curl'){echo ' selected="selected"';} echo ' value="curl">CURL</option>
<option'; if($g_redirect == 'http_redirect'){echo ' selected="selected"';} echo ' value="http_redirect">HTTP redirect</option>
<option'; if($g_redirect == 'iframe'){echo ' selected="selected"';} echo ' value="iframe">Iframe</option>
<option'; if($g_redirect == 'iframe_redirect'){echo ' selected="selected"';} echo ' value="iframe_redirect">Iframe redirect</option>
<option'; if($g_redirect == 'iframe_selection'){echo ' selected="selected"';} echo ' value="iframe_selection">Iframe selection</option>
<option'; if($g_redirect == 'js_redirect'){echo ' selected="selected"';} echo ' value="js_redirect">JS redirect</option>
<option'; if($g_redirect == 'js_selection'){echo ' selected="selected"';} echo ' value="js_selection">JS selection</option>
<option'; if($g_redirect == 'javascript'){echo ' selected="selected"';} echo ' value="javascript">JavaScript</option>
<option'; if($g_redirect == 'meta_refresh'){echo ' selected="selected"';} echo ' value="meta_refresh">Meta refresh</option>
<option'; if($g_redirect == 'show_out'){echo ' selected="selected"';} echo ' value="show_out">Show out</option>
<option'; if($g_redirect == 'show_page_html'){echo ' selected="selected"';} echo ' value="show_page_html">Show page html</option>
<option'; if($g_redirect == 'show_text'){echo ' selected="selected"';} echo ' value="show_text">Show text</option>
<option'; if($g_redirect == 'stop'){echo ' selected="selected"';} echo ' value="stop">Stop</option>
<option'; if($g_redirect == 'under_construction'){echo ' selected="selected"';} echo ' value="under_construction">Under construction</option>
<option'; if($g_redirect == '403_forbidden'){echo ' selected="selected"';} echo ' value="403_forbidden">403 Forbidden</option>
<option'; if($g_redirect == '404_not_found'){echo ' selected="selected"';} echo ' value="404_not_found">404 Not Found</option>
<option'; if($g_redirect == '500_server_error'){echo ' selected="selected"';} echo ' value="500_server_error">500 Server Error</option>
</select></div>
<div class="item"><span>Header</span> <select name="g_header" size = "1">
<option'; if($g_header == 'text/html'){echo ' selected="selected"';} echo ' value="text/html">text/html</option>
<option'; if($g_header == 'text/plain'){echo ' selected="selected"';} echo ' value="text/plain">text/plain</option>
<option'; if($g_header == 'application/javascript'){echo ' selected="selected"';} echo ' value="application/javascript">application/javascript</option>
</select></div>
<div class="item">'.$trans['group']['g6'].'<br><textarea name="g_out" rows="4">'.$g_out.'</textarea></div>
<div class="item">'.$trans['group']['g10'].'<br><textarea name="g_curl" rows="4">'.$g_curl.'</textarea></div>
<div class="item"><span>Geo</span> <select name="g_geo" size = "1">
<option'; if($g_geo == 'sypex'){echo ' selected="selected"';} echo ' value="sypex">Sypex Geo</option>
<option'; if($g_geo == 'cf'){echo ' selected="selected"';} echo ' value="cf">Cloudflare</option>
</select></div>
<div class="item"><span>'.$trans['group']['g7'].'</span> <select name="g_uniq_method" size = "1">
<option'; if($g_uniq_method == '0'){echo ' selected="selected"';} echo ' value="0">Cookies</option>
<option'; if($g_uniq_method == '1'){echo ' selected="selected"';} echo ' value="1">IP</option>
</select></div>
<div class="item"><span>'.$trans['group']['g8'].'</span> <input class="i40" name="g_uniq_time" type="number" value="'.$g_uniq_time.'" size="1" maxlength="20"> <span>h.</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($g_firewall == 1){echo ' checked="checked"';} echo ' name="g_firewall"> <span>'.$trans['group']['g14'].'</span> <input class="i50" name="g_f_queries" type="number" value="'.$g_f_queries.'" size="1" maxlength="20"> <span>'.$trans['group']['g15'].'</span> <input class="i40" name="g_f_time" type="number" value="'.$g_f_time.'" size="1" maxlength="20"> <span>h.</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($g_save_keys == 1){echo ' checked="checked"';} echo ' name="g_save_keys"> <span>'.$trans['group']['g9'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($g_save_keys_se == 1){echo ' checked="checked"';} echo ' name="g_save_keys_se"> <span>'.$trans['group']['g13'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($g_status == 1){echo ' checked="checked"';} echo ' name="g_status"> <span>'.$trans['group']['g11'].'</span></div>
<div class="item">
'.$trans['group']['g12'].'<br>
<textarea name="g_comment" rows="4">'.$g_comment.'</textarea>
</div>
<div class="align_center">
<input class="button" type="submit" name="button" value="Submit">
</div>
<input name="g_get" type="hidden" value="'.$g_get.'">
</div>
</form>
</mob>
</set>';
}
?>