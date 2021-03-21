<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$limit_h = $limit_h / 3600;
if((!empty($s) || $q == 's_create') && $q != 'countries' && $q != 'sources' && $q != 'pb' && $q != 'conf'){
	if($q == 's_create'){
		$s = '';
	}
	if(!empty($error)){
		echo '<div class="align_center red bold indt_10">'.$error.'</div>';
	}
	if(empty($s) && $q == 's_create'){
		echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | '.$trans['stream']['s2'].'</div>';
	}
	if(!empty($s) && empty($error)){
		echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | '.$trans['stream']['s1'].': '.$s_name.'</div>';
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
				$query = "SELECT COUNT (*) FROM '$table_temp' WHERE nstream = '$s_name' AND bot = '$empty'";
				$ch_visitors = $db->querySingle("$query;");
				$ch_hosts = $db->querySingle("$query AND uniq = 'yes';");
				$ch_wap = $db->querySingle("$query AND operator != '$empty' AND uniq = 'yes';");
				if($chart_bots == 1){
					$ch_bots = $db->querySingle("SELECT COUNT (*) FROM '$table_temp' WHERE nstream = '$s_name' AND bot != '$empty' AND uniq = 'yes';");
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
			}
			else{
				if(empty($dg)){
					$dg = '[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0]';
				}
				else{
					$dg = $dg.',[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0]';
				}
			}
			if($lp == 0){
				break;
			}
			$lp--;
		}
		$db->close();
	}
	echo '<div id="curve_chart" class="chart indt_20"></div>';
	if($q == 's_create'){
		$s = count($g_data);
	}
	echo '<set>
<div class="indt_20">
<a class="mobile bold pm" href="#" id="pull_setting">Setting</a>
<mob class="w100">
<ul class="tab-menu clearfix">
<li><a href="#tab-1" class="tab-menu--trigger">Main</a></li>
<li><a href="#tab-2" class="tab-menu--trigger">Devices</a></li>
<li><a href="#tab-3" class="tab-menu--trigger">WAP</a></li>
<li><a href="#tab-4" class="tab-menu--trigger">Geo</a></li>
<li><a href="#tab-5" class="tab-menu--trigger">Filters</a></li>
<li><a href="#tab-6" class="tab-menu--trigger">Bots</a></li>
<li><a href="#tab-7" class="tab-menu--trigger">Remote</a></li>
<li><a href="#tab-8" class="tab-menu--trigger">Limit</a></li>
<li><a href="#tab-9" class="tab-menu--trigger">API</a></li>
<li><a href="#tab-10" class="tab-menu--trigger">VT</a></li>
</ul>
<div id="jsAccordionToTabs" class="tab-container">
<form method="post" action="'.$admin_page.'?g='.$g_id.'&s='.$s.'">
<section id="tab-1" class="tab-container--section indt_3">
<a href="#tab-1" class="tab-menu-mobile pm" data-tab-label="Main"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item">'.$trans['stream']['s3'].'<br><input class="i150" name="s_name" type="text" value="'.$s_name.'" maxlength="30"></div>
<div class="item"><span>'.$trans['stream']['s4'].'</span> <select name="redirect" size = "1">
<option'; if($redirect == 'api'){echo ' selected="selected"';} echo ' value="api">API</option>
<option'; if($redirect == 'curl'){echo ' selected="selected"';} echo ' value="curl">CURL</option>
<option'; if($redirect == 'http_redirect'){echo ' selected="selected"';} echo ' value="http_redirect">HTTP redirect</option>
<option'; if($redirect == 'iframe'){echo ' selected="selected"';} echo ' value="iframe">Iframe</option>
<option'; if($redirect == 'iframe_redirect'){echo ' selected="selected"';} echo ' value="iframe_redirect">Iframe redirect</option>
<option'; if($redirect == 'iframe_selection'){echo ' selected="selected"';} echo ' value="iframe_selection">Iframe selection</option>
<option'; if($redirect == 'js_redirect'){echo ' selected="selected"';} echo ' value="js_redirect">JS redirect</option>
<option'; if($redirect == 'js_selection'){echo ' selected="selected"';} echo ' value="js_selection">JS selection</option>
<option'; if($redirect == 'javascript'){echo ' selected="selected"';} echo ' value="javascript">JavaScript</option>
<option'; if($redirect == 'meta_refresh'){echo ' selected="selected"';} echo ' value="meta_refresh">Meta refresh</option>
<option'; if($redirect == 'show_out'){echo ' selected="selected"';} echo ' value="show_out">Show out</option>
<option'; if($redirect == 'show_page_html'){echo ' selected="selected"';} echo ' value="show_page_html">Show page html</option>
<option'; if($redirect == 'show_text'){echo ' selected="selected"';} echo ' value="show_text">Show text</option>
<option'; if($redirect == 'stop'){echo ' selected="selected"';} echo ' value="stop">Stop</option>
<option'; if($redirect == 'under_construction'){echo ' selected="selected"';} echo ' value="under_construction">Under construction</option>
<option'; if($redirect == '403_forbidden'){echo ' selected="selected"';} echo ' value="403_forbidden">403 Forbidden</option>
<option'; if($redirect == '404_not_found'){echo ' selected="selected"';} echo ' value="404_not_found">404 Not Found</option>
<option'; if($redirect == '500_server_error'){echo ' selected="selected"';} echo ' value="500_server_error">500 Server Error</option>
</select></div>
<div class="item"><span>Header</span> <select name="s_header" size = "1">
<option'; if($s_header == 'text/html'){echo ' selected="selected"';} echo ' value="text/html">text/html</option>
<option'; if($s_header == 'text/plain'){echo ' selected="selected"';} echo ' value="text/plain">text/plain</option>
<option'; if($s_header == 'application/javascript'){echo ' selected="selected"';} echo ' value="application/javascript">application/javascript</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s45'].'</span> <select name="distribution_type" size = "1">
<option'; if($distribution_type == 'rotator'){echo ' selected="selected"';} echo ' value="rotator">Rotator</option>
<option'; if($distribution_type == 'evenly'){echo ' selected="selected"';} echo ' value="evenly">Evenly</option>
<option'; if($distribution_type == 'random'){echo ' selected="selected"';} echo ' value="random">Random</option>
</select></div>
<div class="item">'.$trans['stream']['s5'].'<br><textarea name="s_out" rows="4">'.$s_out.'</textarea></div>
<div class="item">'.$trans['stream']['s50'].'<br><textarea name="s_curl" rows="4">'.$s_curl.'</textarea></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($separation == 1){echo ' checked="checked"';} echo ' name="separation"> <span>'.$trans['stream']['s7'].'</span><br>
<input class="i150 indt_3" name="separation_file" type="text" value="'.$separation_file.'" maxlength="30"></div>
<div class="item"><span>'.$trans['stream']['s42'].'</span> <input class="i40" name="chance" type="number" value="'.$chance.'" maxlength="100"> %</div>
<div class="item"><input class="checkbox" type="checkbox"'; if($s_status == 1){echo ' checked="checked"';} echo ' name="s_status"> <span>'.$trans['stream']['s39'].'</span></div>
<div class="item">
'.$trans['stream']['s40'].'<br>
<textarea name="comment" rows="4">'.$comment.'</textarea>
</div>
</div>
</div>
</section>
<section id="tab-2" class="tab-container--section indt_3">
<a href="#tab-2" class="tab-menu-mobile pm" data-tab-label="Devices"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><span>'.$trans['stream']['s9'].'</span> <select name="computer" size = "1">
<option'; if($computer == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($computer == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($computer == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s10'].'</span> <select name="phone" size = "1">
<option'; if($phone == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($phone == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($phone == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s11'].'</span> <select name="tablet" size = "1">
<option'; if($tablet == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($tablet == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($tablet == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
</div>
</div>
</section>
<section id="tab-3" class="tab-container--section indt_3">
<a href="#tab-3" class="tab-menu-mobile pm" data-tab-label="WAP"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><span>'.$trans['stream']['s13'].'</span> <select name="beeline" size = "1">
<option'; if($beeline == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($beeline == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($beeline == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s14'].'</span> <select name="megafon" size = "1">
<option'; if($megafon == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($megafon == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($megafon == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s15'].'</span> <select name="mts" size = "1">
<option'; if($mts == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($mts == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($mts == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s16'].'</span> <select name="tele2" size = "1">
<option'; if($tele2 == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($tele2 == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($tele2 == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s17'].'</span> <select name="azerbaijan" size = "1">
<option'; if($azerbaijan == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($azerbaijan == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($azerbaijan == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s43'].'</span> <select name="belarus" size = "1">
<option'; if($belarus == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($belarus == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($belarus == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s44'].'</span> <select name="kazakhstan" size = "1">
<option'; if($kazakhstan == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($kazakhstan == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($kazakhstan == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s46'].'</span> <select name="ukraine" size = "1">
<option'; if($ukraine == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($ukraine == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($ukraine == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s18'].'</span> <select name="wap-1" size = "1">
<option'; if($wap_1 == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($wap_1 == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($wap_1 == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s22'].'</span> <select name="wap-2" size = "1">
<option'; if($wap_2 == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($wap_2 == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($wap_2 == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s27'].'</span> <select name="wap-3" size = "1">
<option'; if($wap_3 == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($wap_3 == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($wap_3 == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
</div>
</div>
</section>
<section id="tab-4" class="tab-container--section indt_3">
<a href="#tab-4" class="tab-menu-mobile pm" data-tab-label="Geo"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><span>'.$trans['stream']['s19'].'</span>
<select name="country_flag" size = "1">
<option'; if($country_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($country_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($country_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<textarea class="indt_3" name="country" rows="4">'.$country.'</textarea></div>
<div class="item"><span>'.$trans['stream']['s20'].'</span>
<select name="city_flag" size = "1">
<option'; if($city_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($city_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($city_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<textarea class="indt_3" name="city" rows="4">'.$city.'</textarea></div>
<div class="item"><span>'.$trans['stream']['s48'].'</span>
<select name="region_flag" size = "1">
<option'; if($region_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($region_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($region_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<textarea class="indt_3" name="region" rows="4">'.$region.'</textarea></div>
</div>
</div>
</section>
<section id="tab-5" class="tab-container--section indt_3">
<a href="#tab-5" class="tab-menu-mobile pm" data-tab-label="Filters"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><span>'.$trans['stream']['s21'].'</span>
<select name="lang_flag" size = "1">
<option'; if($lang_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($lang_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($lang_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<textarea class="indt_3" name="lang" rows="4">'.$lang.'</textarea></div>
<div class="item"><span>'.$trans['stream']['s23'].'</span>
<select name="ua_text_flag" size = "1">
<option'; if($ua_text_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($ua_text_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($ua_text_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<textarea class="indt_3" name="ua_text" rows="4">'.$ua_text.'</textarea></div>
<div class="item"><span>'.$trans['stream']['s24'].'</span>
<select name="referer_text_flag" size = "1">
<option'; if($referer_text_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($referer_text_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($referer_text_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<textarea class="indt_3" name="referer_text" rows="4">'.$referer_text.'</textarea></div>
<div class="item"><span>'.$trans['stream']['s49'].'</span>
<select name="domain_text_flag" size = "1">
<option'; if($domain_text_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($domain_text_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($domain_text_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<textarea class="indt_3" name="domain_text" rows="4">'.$domain_text.'</textarea></div>
<div class="item"><span>'.$trans['stream']['s25'].'</span>
<select name="key_text_flag" size = "1">
<option'; if($key_text_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($key_text_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($key_text_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<textarea class="indt_3" name="key_text" rows="4">'.$key_text.'</textarea></div>
<div class="item"><span>'.$trans['stream']['s26'].'</span>
<select name="ch_list_ip_flag" size = "1">
<option'; if($ch_list_ip_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($ch_list_ip_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($ch_list_ip_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<input class="i150 indt_3" name="list_ip_file" type="text" value="'.$list_ip_file.'" maxlength="30"></div>
<div class="item"><span>'.$trans['stream']['s36'].'</span> <select name="unique_user" size = "1">
<option'; if($unique_user == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($unique_user == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($unique_user == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s37'].'</span> <select name="yabrowser" size = "1">
<option'; if($yabrowser == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($yabrowser == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($yabrowser == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s38'].'</span> <select name="referer" size = "1">
<option'; if($referer == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($referer == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($referer == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
</div>
</div>
</section>
<section id="tab-6" class="tab-container--section indt_3">
<a href="#tab-6" class="tab-menu-mobile pm" data-tab-label="Bots"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><span>'.$trans['stream']['s28'].'</span> <select name="bot_redirect" size = "1">
<option'; if($bot_redirect == 'api'){echo ' selected="selected"';} echo ' value="api">API</option>
<option'; if($bot_redirect == 'curl'){echo ' selected="selected"';} echo ' value="curl">CURL</option>
<option'; if($bot_redirect == 'http_redirect'){echo ' selected="selected"';} echo ' value="http_redirect">HTTP redirect</option>
<option'; if($bot_redirect == 'javascript'){echo ' selected="selected"';} echo ' value="javascript">JavaScript</option>
<option'; if($bot_redirect == 'meta_refresh'){echo ' selected="selected"';} echo ' value="meta_refresh">Meta Refresh</option>
<option'; if($bot_redirect == 'show_out'){echo ' selected="selected"';} echo ' value="show_out">Show out</option>
<option'; if($bot_redirect == 'show_page_html'){echo ' selected="selected"';} echo ' value="show_page_html">Show page html</option>
<option'; if($bot_redirect == 'show_text'){echo ' selected="selected"';} echo ' value="show_text">Show text</option>
<option'; if($bot_redirect == 'skip'){echo ' selected="selected"';} echo ' value="skip">Skip</option>
<option'; if($bot_redirect == 'stop'){echo ' selected="selected"';} echo ' value="stop">Stop</option>
<option'; if($bot_redirect == 'under_construction'){echo ' selected="selected"';} echo ' value="under_construction">Under construction</option>
<option'; if($bot_redirect == '403_forbidden'){echo ' selected="selected"';} echo ' value="403_forbidden">403 Forbidden</option>
<option'; if($bot_redirect == '404_not_found'){echo ' selected="selected"';} echo ' value="404_not_found">404 Not Found</option>
<option'; if($bot_redirect == '500_server_error'){echo ' selected="selected"';} echo ' value="500_server_error">500 Server Error</option>
</select></div>
<div class="item"><span>Header</span> <select name="b_header" size = "1">
<option'; if($b_header == 'text/html'){echo ' selected="selected"';} echo ' value="text/html">text/html</option>
<option'; if($b_header == 'text/plain'){echo ' selected="selected"';} echo ' value="text/plain">text/plain</option>
<option'; if($b_header == 'application/javascript'){echo ' selected="selected"';} echo ' value="application/javascript">application/javascript</option>
</select></div>
<div class="item"><span>'.$trans['stream']['s29'].'</span><br><textarea name="out_bot" rows="4">'.$out_bot.'</textarea></div>
<div class="item">'.$trans['stream']['s50'].'<br><textarea name="b_curl" rows="4">'.$b_curl.'</textarea></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($ch_ua == 1){echo ' checked="checked"';} echo ' name="ch_ua"> <span>'.$trans['stream']['s41'].'</span></div>
<div class="item">'.$trans['stream']['s56'].'</div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_empty_ua == 1){echo ' checked="checked"';} echo ' name="ch_empty_ua"> <span>'.$trans['stream']['s30'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_empty_ref == 1){echo ' checked="checked"';} echo ' name="ch_empty_ref"> <span>'.$trans['stream']['s57'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_empty_lang == 1){echo ' checked="checked"';} echo ' name="ch_empty_lang"> <span>'.$trans['stream']['s55'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_ipv6 == 1){echo ' checked="checked"';} echo ' name="ch_ipv6"> <span>'.$trans['stream']['s47'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($ch_ptr == 1){echo ' checked="checked"';} echo ' name="ch_ptr"> <span>'.$trans['stream']['s31'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($ch_list_ua == 1){echo ' checked="checked"';} echo ' name="ch_list_ua"> <span>'.$trans['stream']['s32'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($remote_ip_ch == 1){echo ' checked="checked"';} echo ' name="remote_ip_ch"> <span>'.$trans['stream']['s35'].'</span></div>
<div class="item">'.$trans['stream']['s34'].':</div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_bot_ip_baidu == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_baidu"> <span>Baidu</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_bot_ip_bing == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_bing"> <span>Bing</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_bot_ip_google == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_google"> <span>Google</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_bot_ip_mail == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_mail"> <span>Mail.ru</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_bot_ip_yahoo == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_yahoo"> <span>Yahoo!</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_bot_ip_yandex == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_yandex"> <span>Yandex</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($ch_bot_ip_others == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_others"> <span>Others</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($save_ip == 1){echo ' checked="checked"';} echo ' name="save_ip"> <span>'.$trans['stream']['s33'].'</span></div>
</div>
</div>
</section>
<section id="tab-7" class="tab-container--section indt_3">
<a href="#tab-7" class="tab-menu-mobile pm" data-tab-label="Remote"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><input class="checkbox" type="checkbox"'; if($remote == 1){echo ' checked="checked"';} echo ' name="remote"> <span>'.$trans['stream']['s6'].'</span> <input class="i50" name="remote_cache" type="number" value="'.$remote_cache.'" maxlength="100"> <span>s.</span><br>
<textarea class="indt_3" name="remote_url" rows="4">'.$remote_url.'</textarea></div>
<div class="item">'.$trans['stream']['s8'].'<br><textarea name="remote_regexp" rows="4">'.$remote_regexp.'</textarea></div>
<div class="item">'.$trans['stream']['s12'].'<br><textarea name="remote_reserved_out" rows="4">'.$remote_reserved_out.'</textarea></div>
</div>
</div>
</section>
<section id="tab-8" class="tab-container--section indt_3">
<a href="#tab-8" class="tab-menu-mobile pm" data-tab-label="Limit"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><input class="checkbox" type="checkbox"'; if($limit == 1){echo ' checked="checked"';} echo ' name="limit"> <span>'.$trans['stream']['s51'].'</span> <input class="i50" name="limit_с" type="number" value="'.$limit_с.'" maxlength="50"> <span>'.$trans['stream']['s52'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="radio" name="limit_type" value="1"'; if($limit_type == 1){echo ' checked="checked"';} echo '> <span>'.$trans['stream']['s53'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="radio" name="limit_type" value="2"'; if($limit_type == 2){echo ' checked="checked"';} echo '> <span>'.$trans['stream']['s54'].'</span> <input class="i40" name="limit_h" type="number" value="'.$limit_h.'" maxlength="50"> <span>h.</span></div>
</div>
</div>
</section>
<section id="tab-9" class="tab-container--section indt_3">
<a href="#tab-9" class="tab-menu-mobile pm" data-tab-label="API"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><input class="checkbox" type="checkbox"'; if($api_mac_exe == 1){echo ' checked="checked"';} echo ' name="api_mac_exe"> <span>'.$trans['stream']['s58'].'</span><br><textarea name="api_mac" rows="4">'.$api_mac.'</textarea>
</div>
<div class="item"><span>'.$trans['api']['a23'].'</span> <input class="i40" name="api_mac_prob" type="number" value="'.$api_mac_prob.'" maxlength="50"> %</div>
</div>
</div>
</section>
<section id="tab-10" class="tab-container--section indt_3">
<a href="#tab-10" class="tab-menu-mobile pm" data-tab-label="VirusTotal"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><input class="checkbox" type="checkbox"'; if($vt == 1){echo ' checked="checked"';} echo ' name="vt"> <span>'.$trans['stream']['s59'].'</span></div>
<div class="item"><span>'.$trans['stream']['s62'].'</span> <select name="vt_option" size = "1">
<option'; if($vt_option == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['stream']['s63'].'</option>
<option'; if($vt_option == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['stream']['s64'].'</option>
<option'; if($vt_option == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['stream']['s65'].'</option>
<option'; if($vt_option == '3'){echo ' selected="selected"';} echo ' value="3">'.$trans['stream']['s66'].'</option>
<option'; if($vt_option == '4'){echo ' selected="selected"';} echo ' value="4">'.$trans['stream']['s67'].'</option>
<option'; if($vt_option == '5'){echo ' selected="selected"';} echo ' value="5">'.$trans['stream']['s68'].'</option>
</select></div>
<div class="item">'.$trans['stream']['s61'].'<br><textarea name="vt_anti" rows="4">'.$vt_anti.'</textarea></div>
<div class="item">'.$trans['stream']['s60'].'<br><textarea name="vt_out" rows="4">'.$vt_out.'</textarea></div>
</div>
</div>
</section>
<input class="button" type="submit" name="button" value="Submit">
</form>
</div>
</mob>
</div>
</set>';
}
?>