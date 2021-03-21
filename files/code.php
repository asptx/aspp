<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$se = 0;
$period_log--;
if(!empty($_GET['t'])){$table = $_GET['t'];}
else{$table = strtotime(date('Y-m-d'));}
if(!file_exists($folder_ini)){
	mkdir($folder_ini, 0755);
	file_put_contents($folder_ini.'/'.'.htaccess', "<Files *.ini>\nDeny from all\n</Files>", LOCK_EX);
}
if(!file_exists($folder_log)){
	mkdir($folder_log, 0755);
	file_put_contents($folder_log.'/'.'.htaccess', "<Files *.db>\nDeny from all\n</Files>", LOCK_EX);
}
if(!empty($_GET['q'])){$q = $_GET['q'];} else{$q = '';}
if(!empty($_GET['g'])){$g_id = $_GET['g'];} else{$g_id = '';}
if(!empty($_GET['s'])){$s = $_GET['s'];} else{$s = '';}
if(!empty($_GET['n'])){$name = $_GET['n'];} else{$name = '';}
if(!empty($_GET['d'])){$d = $_GET['d'];} else{$d = '';}
if(!empty($_GET['range'])){$range = $_GET['range'];} else{$range = 1;}
if($q == 'conf' && !empty($d) && !empty($_POST['id']) && $_POST['button'] == "Submit"){
	if(!empty($_POST['cf_ip'])){$api_conf['cf_ip'] = 1;}else{$api_conf['cf_ip'] = 0;}
	if(!empty($_POST['em_referer'])){$api_conf['em_referer'] = 1;} else{$api_conf['em_referer'] = 0;}
	if(!empty($_POST['em_useragent'])){$api_conf['em_useragent'] = 1;} else{$api_conf['em_useragent'] = 0;}
	if(!empty($_POST['em_lang'])){$api_conf['em_lang'] = 1;} else{$api_conf['em_lang'] = 0;}
	if(!empty($_POST['ipv6'])){$api_conf['ipv6'] = 1;} else{$api_conf['ipv6'] = 0;}
	if(!empty($_POST['ptr'])){$api_conf['ptr'] = 1;} else{$api_conf['ptr'] = 0;}
	if(!empty($_POST['rd_bots'])){$api_conf['rd_bots'] = 1;} else{$api_conf['rd_bots'] = 0;}
	if(!empty($_POST['rd_se'])){$api_conf['rd_se'] = 1;} else{$api_conf['rd_se'] = 0;}
	if(!empty($_POST['rotator'])){$api_conf['rotator'] = 1;} else{$api_conf['rotator'] = 0;}
	if(!empty($_POST['status'])){$api_conf['status'] = 1;} else{$api_conf['status'] = 0;}
	$api_conf['id'] = trim($_POST['id']);
	$api_conf['n_cookies'] = trim($_POST['n_cookies']);
	if(check_num(trim($_POST['t_cookies']), 0)){$api_conf['t_cookies'] = trim($_POST['t_cookies']);}
	else{$api_conf['t_cookies'] = 3600;}
	$api_conf['ip_serv_seodor'] = trim($_POST['ip_serv_seodor']);
	$api_conf['sign_ref'] = htmlentities(trim($_POST['sign_ref']), ENT_QUOTES, 'UTF-8');
	$api_conf['sign_ua'] = htmlentities(trim($_POST['sign_ua']), ENT_QUOTES, 'UTF-8');
	$api_conf['connect'] = $_POST['connect'];
	$api_conf['m_cookies'] = $_POST['m_cookies'];
	$api_conf['conf_lc'] = date("Y-m-d H:i:s");
	$api_conf = base64_encode(serialize($api_conf));
	$url = "http://$d/$file_api?key=$key_api&conf=$api_conf";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $curl_ua);
	curl_exec($ch);
	curl_close($ch);
	$z = '';
	if(!empty($s)){
		$z = '&s='.$s;
	}
	header("Location: $admin_page?q=conf&g=$g_id$z&d=$d");
}
if(!empty($_GET['f'])){
	$e_file = preg_replace('/[^a-z0-9\._-]/i', '', $_GET['f']);
	if(!file_exists('database/'.$e_file) || $e_file == '.htaccess'){
		$e_file = '';
	}
}
else{
	$e_file = '';
}
if(!empty($_POST['e_file_data']) && $_POST['button'] == "Submit"){
	$e_file_data = trim($_POST['e_file_data']);
	$ex = explode("\n", $e_file_data);
	foreach($ex as $v){
		$v = trim($v);
		if(!empty($v)){
			$arr[] = $v;
		}
	}
	if(!empty($arr)){
		$e_file_data = implode("\n", $arr);
	}
	else{
		$e_file_data = '';
	}
	file_put_contents('database/'.$e_file, $e_file_data."\n", LOCK_EX);
	header("Location: $admin_page?q=editor&f=$e_file");
}
if($q == "g_delete" && !empty($g_id)){
	if(file_exists($folder_ini.'/'.$g_id.'.ini')){
		unlink($folder_ini.'/'.$g_id.'.ini');
	}
	if(file_exists($folder_log.'/'.$g_id.'.db')){
		unlink($folder_log.'/'.$g_id.'.db');
	}
	header("Location: $admin_page");
}
if($q == "g_del_log" && !empty($g_id)){
	if(file_exists($folder_log.'/'.$g_id.'.db')){
		unlink($folder_log.'/'.$g_id.'.db');
	}
	header("Location: $admin_page?g=$g_id");
}
if($q == "s_del_log" && !empty($g_id) && !empty($s)){
	if(file_exists($folder_log.'/'.$g_id.'.db')){
		$t_del = array();
		$db = new SQLite3($folder_log.'/'.$g_id.'.db');
		$db->busyTimeout($timeout_2);
		$db->exec("PRAGMA journal_mode = WAL;");
		$db->querySingle("BEGIN IMMEDIATE;");
		$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table';");
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$t_tmp = $array['name'];
			$db->query("DELETE FROM '$t_tmp' WHERE nstream = '$name';");
			if($db->querySingle("SELECT COUNT (*) FROM '$t_tmp';") == 0){
				$t_del[] = $t_tmp;
			}
		}
		if(!empty($t_del)){
			foreach($t_del as $t_tmp){
				$db->query("DROP TABLE '$t_tmp';");
			}
		}
		$db->querySingle("COMMIT;");
		$db->close();
	}
	header("Location: $admin_page?g=$g_id&s=$s");
}
if($q == 's_delete' && !empty($s)){
	if(file_exists($folder_ini.'/'.$g_id.'.ini')){
		$g_data = unserialize(file_get_contents($folder_ini.'/'.$g_id.'.ini'));
		unset($g_data[$s]);
		$g_data = serialize(array_values($g_data));
		file_put_contents($folder_ini.'/'.$g_id.'.ini', $g_data, LOCK_EX);
	}
	if(file_exists($folder_log.'/'.$g_id.'.db')){
		$db = new SQLite3($folder_log.'/'.$g_id.'.db');
		$db->busyTimeout($timeout_2);
		$db->exec("PRAGMA journal_mode = WAL;");
		$db->querySingle("BEGIN IMMEDIATE;");
		$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table';");
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$t_temp = $array['name'];
			$db->query("DELETE FROM '$t_temp' WHERE nstream = '$name';");
		}
		$db->querySingle("COMMIT;");
		$db->close();
	}
	header("Location: $admin_page?g=$g_id");
}
if(!empty($g_id) && file_exists($folder_ini.'/'.$g_id.'.ini')){
	$g_data = unserialize(file_get_contents($folder_ini.'/'.$g_id.'.ini'));
	$g_id = $g_data[0]['g_id'];
	$g_name = $g_data[0]['g_name'];
	$g_redirect = $g_data[0]['g_redirect'];
	$g_header = $g_data[0]['g_header'];
	$g_out = $g_data[0]['g_out'];
	$g_curl = $g_data[0]['g_curl'];
	$g_geo = $g_data[0]['g_geo'];
	$g_status = $g_data[0]['g_status'];
	$g_uniq_method = $g_data[0]['g_uniq_method'];
	$g_uniq_time = $g_data[0]['g_uniq_time'];
	$g_firewall = $g_data[0]['g_firewall'];
	$g_f_queries = $g_data[0]['g_f_queries'];
	$g_f_time = $g_data[0]['g_f_time'];
	$g_save_keys = $g_data[0]['g_save_keys'];
	$g_save_keys_se = $g_data[0]['g_save_keys_se'];
	$g_comment = $g_data[0]['g_comment'];
}
else{
	$g_name = '';
	$g_id = '';
	$g_redirect = 'http_redirect';
	$g_header = 'text/html';
	$g_out = 'http://site.com';
	$g_curl = '';
	$g_geo = 'sypex';
	$g_status = 1;
	$g_uniq_method = 0;
	$g_uniq_time = 86400;
	$g_firewall = 0;
	$g_f_queries = 100;
	$g_f_time = 86400;
	$g_save_keys = 1;
	$g_save_keys_se = 0;
	$g_comment = '';
}
if(!empty($s) && !empty($g_data[$s]['s_name'])){
	$s_name = $g_data[$s]['s_name'];
	$redirect = $g_data[$s]['redirect'];
	$s_header = $g_data[$s]['s_header'];
	$b_header = $g_data[$s]['b_header'];
	$distribution_type = $g_data[$s]['distribution_type'];
	$s_out = $g_data[$s]['s_out'];
	$remote = $g_data[$s]['remote'];
	$remote_cache = $g_data[$s]['remote_cache'];
	$remote_regexp = $g_data[$s]['remote_regexp'];
	$remote_reserved_out = $g_data[$s]['remote_reserved_out'];
	$remote_url = $g_data[$s]['remote_url'];
	$separation = $g_data[$s]['separation'];
	$separation_file = $g_data[$s]['separation_file'];
	$s_curl = $g_data[$s]['s_curl'];
	$b_curl = $g_data[$s]['b_curl'];
	$computer = $g_data[$s]['computer'];
	$phone = $g_data[$s]['phone'];
	$tablet = $g_data[$s]['tablet'];
	$beeline = $g_data[$s]['beeline'];
	$megafon = $g_data[$s]['megafon'];
	$mts = $g_data[$s]['mts'];
	$tele2 = $g_data[$s]['tele2'];
	$azerbaijan = $g_data[$s]['azerbaijan'];
	$belarus = $g_data[$s]['belarus'];
	$kazakhstan = $g_data[$s]['kazakhstan'];
	$ukraine = $g_data[$s]['ukraine'];
	$wap_1 = $g_data[$s]['wap-1'];
	$wap_2 = $g_data[$s]['wap-2'];
	$wap_3 = $g_data[$s]['wap-3'];
	$country_flag = $g_data[$s]['country_flag'];
	$country = $g_data[$s]['country'];
	$city_flag = $g_data[$s]['city_flag'];
	$city = $g_data[$s]['city'];
	$region_flag = $g_data[$s]['region_flag'];
	$region = $g_data[$s]['region'];
	$lang_flag = $g_data[$s]['lang_flag'];
	$lang = $g_data[$s]['lang'];
	$ua_text_flag = $g_data[$s]['ua_text_flag'];
	$ua_text = $g_data[$s]['ua_text'];
	$referer_text_flag = $g_data[$s]['referer_text_flag'];
	$referer_text = $g_data[$s]['referer_text'];
	$domain_text_flag = $g_data[$s]['domain_text_flag'];
	$domain_text = $g_data[$s]['domain_text'];
	$key_text_flag = $g_data[$s]['key_text_flag'];
	$key_text = $g_data[$s]['key_text'];
	$ch_list_ip_flag = $g_data[$s]['ch_list_ip_flag'];
	$list_ip_file = $g_data[$s]['list_ip_file'];
	$bot_redirect = $g_data[$s]['bot_redirect'];
	$out_bot = $g_data[$s]['out_bot'];
	$remote_ip_ch = $g_data[$s]['remote_ip_ch'];
	$ch_ipv6 = $g_data[$s]['ch_ipv6'];
	$ch_bot_ip_baidu = $g_data[$s]['ch_bot_ip_baidu'];
	$ch_bot_ip_bing = $g_data[$s]['ch_bot_ip_bing'];
	$ch_bot_ip_google = $g_data[$s]['ch_bot_ip_google'];
	$ch_bot_ip_mail = $g_data[$s]['ch_bot_ip_mail'];
	$ch_bot_ip_yahoo = $g_data[$s]['ch_bot_ip_yahoo'];
	$ch_bot_ip_yandex = $g_data[$s]['ch_bot_ip_yandex'];
	$ch_bot_ip_others = $g_data[$s]['ch_bot_ip_others'];
	$save_ip = $g_data[$s]['save_ip'];
	$ch_list_ua = $g_data[$s]['ch_list_ua'];
	$ch_ua = $g_data[$s]['ch_ua'];
	$ch_empty_ua = $g_data[$s]['ch_empty_ua'];
	$ch_empty_ref = $g_data[$s]['ch_empty_ref'];
	$ch_empty_lang = $g_data[$s]['ch_empty_lang'];
	$ch_ptr = $g_data[$s]['ch_ptr'];
	$chance = $g_data[$s]['chance'];
	$unique_user = $g_data[$s]['unique_user'];
	$yabrowser = $g_data[$s]['yabrowser'];
	$referer = $g_data[$s]['referer'];
	$s_status = $g_data[$s]['s_status'];
	$comment = $g_data[$s]['comment'];
	$limit = $g_data[$s]['limit'];
	$limit_type = $g_data[$s]['limit_type'];
	$limit_с = $g_data[$s]['limit_с'];
	$limit_h = $g_data[$s]['limit_h'];
	$api_mac_exe = $g_data[$s]['api_mac_exe'];
	$api_mac = $g_data[$s]['api_mac'];
	$api_mac_prob = $g_data[$s]['api_mac_prob'];
	$vt = $g_data[$s]['vt'];
	$vt_option = $g_data[$s]['vt_option'];
	$vt_anti = $g_data[$s]['vt_anti'];
	$vt_out = $g_data[$s]['vt_out'];
}
else{
	$s_name = '';
	$redirect = 'http_redirect';
	$s_header = 'text/html';
	$b_header = 'text/html';
	$distribution_type = 'rotator';
	$s_out = 'http://site.com';
	$remote = 0;
	$remote_cache = 1800;
	$remote_regexp = '';
	$remote_reserved_out = '';
	$remote_url = '';
	$separation = 0;
	$separation_file = 'separation.dat';
	$s_curl = '';
	$b_curl = '';
	$computer = 2;
	$phone = 2;
	$tablet = 2;
	$beeline = 2;
	$megafon = 2;
	$mts = 2;
	$tele2 = 2;
	$azerbaijan = 2;
	$belarus = 2;
	$kazakhstan = 2;
	$ukraine = 2;
	$wap_1 = 2;
	$wap_2 = 2;
	$wap_3 = 2;
	$country_flag = 2;
	$country = '';
	$city_flag = 2;
	$city = '';
	$region_flag = 2;
	$region = '';
	$lang_flag = 2;
	$lang = '';
	$ua_text_flag = 2;
	$ua_text = '';
	$referer_text_flag = 2;
	$referer_text = '';
	$domain_text_flag = 2;
	$domain_text = '';
	$key_text_flag = 2;
	$key_text = '';
	$ch_list_ip_flag = 2;
	$list_ip_file = 'ip_choice.dat';
	$bot_redirect = 'skip';
	$out_bot = '';
	$remote_ip_ch = 0;
	$ch_ipv6 = 0;
	$ch_bot_ip_baidu = 0;
	$ch_bot_ip_bing = 0;
	$ch_bot_ip_google = 1;
	$ch_bot_ip_mail = 0;
	$ch_bot_ip_yahoo = 0;
	$ch_bot_ip_yandex = 1;
	$ch_bot_ip_others = 0;
	$save_ip = 0;
	$ch_list_ua = 0;
	$ch_ua = 1;
	$ch_empty_ua = 1;
	$ch_empty_ref = 0;
	$ch_empty_lang = 0;
	$ch_ptr = 0;
	$chance = 100;
	$unique_user = 2;
	$yabrowser = 2;
	$referer = 2;
	$s_status = 1;
	$comment = '';
	$limit = 0;
	$limit_type = 1;
	$limit_с = 1000;
	$limit_h = 21600;
	$api_mac_exe = 0;
	$api_mac = '';
	$api_mac_prob = 20;
	$vt = 0;
	$vt_option = 0;
	$vt_anti = 'Avira,Dr.Web,ESET,Google Safebrowsing,Kaspersky,Yandex Safebrowsing';
	$vt_out = '';
}
if(!empty($_POST['g_id']) && $_POST['button'] == "Submit"){
	clear_cache();
	$g_id = htmlentities(trim($_POST['g_id']), ENT_QUOTES, 'UTF-8');
	$g_name = htmlentities(trim($_POST['g_name']), ENT_QUOTES, 'UTF-8');
	$g_redirect = $_POST['g_redirect'];
	$g_header = htmlentities($_POST['g_header'], ENT_QUOTES, 'UTF-8');
	$g_out = htmlentities(trim($_POST['g_out']), ENT_QUOTES, 'UTF-8');
	$g_curl = htmlentities(trim($_POST['g_curl']), ENT_QUOTES, 'UTF-8');
	$g_geo = $_POST['g_geo'];
	if(!empty($_POST['g_status'])){$g_status = 1;} else{$g_status = 0;}
	$g_uniq_method = $_POST['g_uniq_method'];
	if(check_num(trim($_POST['g_uniq_time']), 0)){$g_uniq_time = trim($_POST['g_uniq_time'])*3600;} else{$g_uniq_time = 1*3600;}
	if(!empty($_POST['g_firewall'])){$g_firewall = 1;} else{$g_firewall = 0;}
	if(check_num(trim($_POST['g_f_queries']), 0)){$g_f_queries = trim($_POST['g_f_queries']);} else{$g_f_queries = 100;}
	if(check_num(trim($_POST['g_f_time']), 0)){$g_f_time = trim($_POST['g_f_time'])*3600;} else{$g_f_time = 1*3600;}
	if(!empty($_POST['g_save_keys'])){$g_save_keys = 1;} else{$g_save_keys = 0;}
	if(!empty($_POST['g_save_keys_se'])){$g_save_keys_se = 1;} else{$g_save_keys_se = 0;}
	$g_comment = htmlentities(trim($_POST['g_comment']), ENT_QUOTES, 'UTF-8');
	if(!empty($_POST['g_get']) && !empty($_POST['g_get'])){$g_get = $_POST['g_get'];}
	else{$g_get = '';}
	$files = scandir($folder_ini);
	$x = 0;
	$y = '';
	foreach($files as $v){
		if($v != "." && $v != ".." && $v != ".htaccess"){
			$a = unserialize(file_get_contents($folder_ini.'/'.$v));
			if($g_name == $a[0]['g_name']){
				if($g_id != $a[0]['g_id']){
					$error = $trans['error']['e2'];
					break;
				}
			}
		}
	}
	if(empty($g_id)){$error = $trans['error']['e3'];}
	if(empty($g_name)){$error = $trans['error']['e4'];}
	if(empty($error)){
 		if(file_exists($folder_ini.'/'.$g_id.'.ini')){
			$g_data = unserialize(file_get_contents($folder_ini.'/'.$g_id.'.ini'));
		}
		if(!empty($g_get) && $g_id != $g_get && file_exists($folder_ini.'/'.$g_get.'.ini')){
			$g_data = unserialize(file_get_contents($folder_ini.'/'.$g_get.'.ini'));
		}
		$g_data[0]['g_id'] = $g_id;
		$g_data[0]['g_name'] = $g_name;
		$g_data[0]['g_redirect'] = $g_redirect;
		$g_data[0]['g_header'] = $g_header;
		$g_data[0]['g_out'] = $g_out;
		$g_data[0]['g_curl'] = $g_curl;
		$g_data[0]['g_geo'] = $g_geo;
		$g_data[0]['g_status'] = $g_status;
		$g_data[0]['g_uniq_method'] = $g_uniq_method;
		$g_data[0]['g_uniq_time'] = $g_uniq_time;
		$g_data[0]['g_firewall'] = $g_firewall;
		$g_data[0]['g_f_queries'] = $g_f_queries;
		$g_data[0]['g_f_time'] = $g_f_time;
		$g_data[0]['g_save_keys'] = $g_save_keys;
		$g_data[0]['g_save_keys_se'] = $g_save_keys_se;
		$g_data[0]['g_comment'] = $g_comment;
		$g_data = serialize($g_data);
		file_put_contents($folder_ini.'/'.$g_id.'.ini', $g_data."\n", LOCK_EX);
		header("Location: $admin_page?g=$g_id");
 	}
}
if(!empty($_POST['redirect']) && $_POST['button'] == "Submit"){
	clear_cache();
	$s_name = htmlentities(trim($_POST['s_name']), ENT_QUOTES, 'UTF-8');
	if(empty($s_name)){$error = $trans['error']['e5'];}
	if(!empty($g_data[$s]['s_name']) && $g_data[$s]['s_name'] != $s_name){
		foreach($g_data as $value){
			if(!empty($value['s_name']) && $value['s_name'] == $s_name){
				$error = $trans['error']['e6'];
				break;
			}
		}
	}
	$redirect = $_POST['redirect'];
	$s_header = htmlentities($_POST['s_header'], ENT_QUOTES, 'UTF-8');
	$b_header = htmlentities($_POST['b_header'], ENT_QUOTES, 'UTF-8');
	$distribution_type = $_POST['distribution_type'];
	$s_out = htmlentities(trim($_POST['s_out']), ENT_QUOTES, 'UTF-8');
	if(!empty($_POST['remote'])){$remote = 1;} else{$remote = 0;}
	if(is_numeric(trim($_POST['remote_cache'])) && trim($_POST['remote_cache']) >= 0){
		$remote_cache = trim($_POST['remote_cache']);
	}
	else{$remote_cache = 1800;}
	$remote_regexp = htmlentities(trim($_POST['remote_regexp']), ENT_QUOTES, 'UTF-8');
	$remote_reserved_out = htmlentities(trim($_POST['remote_reserved_out']), ENT_QUOTES, 'UTF-8');
	$remote_url = htmlentities(trim($_POST['remote_url']), ENT_QUOTES, 'UTF-8');
	if(!empty($_POST['separation'])){$separation = 1;} else{$separation = 0;}
	$separation_file = htmlentities(trim($_POST['separation_file']), ENT_QUOTES, 'UTF-8');
	$s_curl = htmlentities(trim($_POST['s_curl']), ENT_QUOTES, 'UTF-8');
	$b_curl = htmlentities(trim($_POST['b_curl']), ENT_QUOTES, 'UTF-8');
	$computer = $_POST['computer'];
	$phone = $_POST['phone'];
	$tablet = $_POST['tablet'];
	$beeline = $_POST['beeline'];
	$megafon = $_POST['megafon'];
	$mts = $_POST['mts'];
	$tele2 = $_POST['tele2'];
	$azerbaijan = $_POST['azerbaijan'];
	$belarus = $_POST['belarus'];
	$kazakhstan = $_POST['kazakhstan'];
	$ukraine = $_POST['ukraine'];
	$wap_1 = $_POST['wap-1'];
	$wap_2 = $_POST['wap-2'];
	$wap_3 = $_POST['wap-3'];
	$country_flag = $_POST['country_flag'];
	$country = htmlentities(trim($_POST['country']), ENT_QUOTES, 'UTF-8');
	$city_flag = $_POST['city_flag'];
	$city = htmlentities(trim($_POST['city']), ENT_QUOTES, 'UTF-8');
	$region_flag = $_POST['region_flag'];
	$region = htmlentities(trim($_POST['region']), ENT_QUOTES, 'UTF-8');
	$lang_flag = $_POST['lang_flag'];
	$lang = htmlentities(trim($_POST['lang']), ENT_QUOTES, 'UTF-8');
	$ua_text_flag = $_POST['ua_text_flag'];
	$ua_text = htmlentities(trim($_POST['ua_text']), ENT_QUOTES, 'UTF-8');
	$referer_text_flag = $_POST['referer_text_flag'];
	$referer_text = htmlentities(trim($_POST['referer_text']), ENT_QUOTES, 'UTF-8');
	$domain_text_flag = $_POST['domain_text_flag'];
	$domain_text = htmlentities(trim($_POST['domain_text']), ENT_QUOTES, 'UTF-8');
	$key_text_flag = $_POST['key_text_flag'];
	$key_text = htmlentities(trim($_POST['key_text']), ENT_QUOTES, 'UTF-8');
	$ch_list_ip_flag = $_POST['ch_list_ip_flag'];
	$list_ip_file = htmlentities(trim($_POST['list_ip_file']), ENT_QUOTES, 'UTF-8');
	$bot_redirect = $_POST['bot_redirect'];
	$out_bot = htmlentities(trim($_POST['out_bot']), ENT_QUOTES, 'UTF-8');
	if(!empty($_POST['remote_ip_ch'])){$remote_ip_ch = 1;} else{$remote_ip_ch = 0;}
	if(!empty($_POST['ch_ipv6'])){$ch_ipv6 = 1;} else{$ch_ipv6 = 0;}
	if(!empty($_POST['ch_bot_ip_baidu'])){$ch_bot_ip_baidu = 1;}	else{$ch_bot_ip_baidu = 0;}
	if(!empty($_POST['ch_bot_ip_bing'])){$ch_bot_ip_bing = 1;} else{$ch_bot_ip_bing = 0;}
	if(!empty($_POST['ch_bot_ip_google'])){$ch_bot_ip_google = 1;} else{$ch_bot_ip_google = 0;}
	if(!empty($_POST['ch_bot_ip_mail'])){$ch_bot_ip_mail = 1;} else{$ch_bot_ip_mail = 0;}
	if(!empty($_POST['ch_bot_ip_yahoo'])){$ch_bot_ip_yahoo = 1;}	else{$ch_bot_ip_yahoo = 0;}
	if(!empty($_POST['ch_bot_ip_yandex'])){$ch_bot_ip_yandex = 1;} else{$ch_bot_ip_yandex = 0;}
	if(!empty($_POST['ch_bot_ip_others'])){$ch_bot_ip_others = 1;} else{$ch_bot_ip_others = 0;}
	if(!empty($_POST['save_ip'])){$save_ip = 1;}	else{$save_ip = 0;}
	if(!empty($_POST['ch_list_ua'])){$ch_list_ua = 1;} else{$ch_list_ua = 0;}
	if(!empty($_POST['ch_ua'])){$ch_ua = 1;}	else{$ch_ua = 0;}
	if(!empty($_POST['ch_empty_ua'])){$ch_empty_ua = 1;}	else{$ch_empty_ua = 0;}
	if(!empty($_POST['ch_empty_ref'])){$ch_empty_ref = 1;}	else{$ch_empty_ref = 0;}
	if(!empty($_POST['ch_empty_lang'])){$ch_empty_lang = 1;}	else{$ch_empty_lang = 0;}
	if(!empty($_POST['ch_ptr'])){$ch_ptr = 1;} else{$ch_ptr = 0;}
	if(check_num(trim($_POST['chance']), 100)){$chance = trim($_POST['chance']);} else{$chance = 100;}
	$unique_user = $_POST['unique_user'];
	$yabrowser = $_POST['yabrowser'];
	$referer = $_POST['referer'];
	if(!empty($_POST['s_status'])){$s_status = 1;} else{$s_status = 0;}
	$comment = htmlentities(trim($_POST['comment']), ENT_QUOTES, 'UTF-8');
	if(!empty($_POST['limit'])){$limit = 1;} else{$limit = 0;}
	$limit_type = $_POST['limit_type'];
	if(check_num(trim($_POST['limit_с']), 0)){$limit_с = trim($_POST['limit_с']);} else{$limit_с = 1000;}
	if(check_num(trim($_POST['limit_h']), 0)){$limit_h = trim($_POST['limit_h'])*3600;} else{$limit_h = 6*3600;}
	if(!empty($_POST['api_mac_exe'])){$api_mac_exe = 1;} else{$api_mac_exe = 0;}
	$api_mac = htmlentities(trim($_POST['api_mac']), ENT_QUOTES, 'UTF-8');
	if(check_num(trim($_POST['api_mac_prob']), 100)){$api_mac_prob = trim($_POST['api_mac_prob']);} else{$api_mac_prob = 0;}
	if(!empty($_POST['vt'])){$vt = 1;} else{$vt = 0;}
	$vt_option = $_POST['vt_option'];
	$vt_anti = htmlentities(trim($_POST['vt_anti']), ENT_QUOTES, 'UTF-8');
	$vt_out = htmlentities(trim($_POST['vt_out']), ENT_QUOTES, 'UTF-8');
	if(empty($error)){
		if(!empty($g_data[$s]['s_name']) && $g_data[$s]['s_name'] == $s_name){
			$n = $s;
		}
		else{
			$n = count($g_data);
		}
		$g_data[$n] = array(
		's_name'=>$s_name,
		'redirect'=>$redirect,
		's_header'=>$s_header,
		'b_header'=>$b_header,
		'distribution_type'=>$distribution_type,
		's_out'=>$s_out,
		'remote'=>$remote,
		'remote_cache'=>$remote_cache,
		'remote_regexp'=>$remote_regexp,
		'remote_reserved_out'=>$remote_reserved_out,
		'remote_url'=>$remote_url,
		'separation'=>$separation,
		'separation_file'=>$separation_file,
		's_curl'=>$s_curl,
		'b_curl'=>$b_curl,
		'computer'=>$computer,
		'phone'=>$phone,
		'tablet'=>$tablet,
		'beeline'=>$beeline,
		'megafon'=>$megafon,
		'mts'=>$mts,
		'tele2'=>$tele2,
		'azerbaijan'=>$azerbaijan,
		'belarus'=>$belarus,
		'kazakhstan'=>$kazakhstan,
		'ukraine'=>$ukraine,
		'wap-1'=>$wap_1,
		'wap-2'=>$wap_2,
		'wap-3'=>$wap_3,
		'country_flag'=>$country_flag,
		'country'=>$country,
		'city_flag'=>$city_flag,
		'city'=>$city,
		'region_flag'=>$region_flag,
		'region'=>$region,
		'lang_flag'=>$lang_flag,
		'lang'=>$lang,
		'ua_text_flag'=>$ua_text_flag,
		'ua_text'=>$ua_text,
		'referer_text_flag'=>$referer_text_flag,
		'referer_text'=>$referer_text,
		'domain_text_flag'=>$domain_text_flag,
		'domain_text'=>$domain_text,
		'key_text_flag'=>$key_text_flag,
		'key_text'=>$key_text,
		'ch_list_ip_flag'=>$ch_list_ip_flag,
		'list_ip_file'=>$list_ip_file,
		'bot_redirect'=>$bot_redirect,
		'out_bot'=>$out_bot,
		'remote_ip_ch'=>$remote_ip_ch,
		'ch_ipv6'=>$ch_ipv6,
		'ch_bot_ip_baidu'=>$ch_bot_ip_baidu,
		'ch_bot_ip_bing'=>$ch_bot_ip_bing,
		'ch_bot_ip_google'=>$ch_bot_ip_google,
		'ch_bot_ip_mail'=>$ch_bot_ip_mail,
		'ch_bot_ip_yahoo'=>$ch_bot_ip_yahoo,
		'ch_bot_ip_yandex'=>$ch_bot_ip_yandex,
		'ch_bot_ip_others'=>$ch_bot_ip_others,
		'save_ip'=>$save_ip,
		'ch_list_ua'=>$ch_list_ua,
		'ch_ua'=>$ch_ua,
		'ch_empty_ua'=>$ch_empty_ua,
		'ch_empty_ref'=>$ch_empty_ref,
		'ch_empty_lang'=>$ch_empty_lang,
		'ch_ptr'=>$ch_ptr,
		'chance'=>$chance,
		'unique_user'=>$unique_user,
		'yabrowser'=>$yabrowser,
		'referer'=>$referer,
		's_status'=>$s_status,
		'comment'=>$comment,
		'limit'=>$limit,
		'limit_type'=>$limit_type,
		'limit_с'=>$limit_с,
		'limit_h'=>$limit_h,
		'api_mac_exe'=>$api_mac_exe,
		'api_mac'=>$api_mac,
		'api_mac_prob'=>$api_mac_prob,
		'vt'=>$vt,
		'vt_option'=>$vt_option,
		'vt_anti'=>$vt_anti,
		'vt_out'=>$vt_out,
		);
		$g_data = serialize($g_data);
		file_put_contents($folder_ini.'/'.$g_id.'.ini', $g_data."\n", LOCK_EX);
		header("Location: $admin_page?g=$g_id&s=$s");
	}
}
if(file_exists($folder_ini.'/'.$g_id.'.ini')){
	$g_data = unserialize(file_get_contents($folder_ini.'/'.$g_id.'.ini'));
}
if(!empty($g_data[$s]['s_name']) && $g_data[$s]['s_name'] != $s_name){
	$s_name = $g_data[$s]['s_name'];
	$redirect = $g_data[$s]['redirect'];
	$s_header = $g_data[$s]['s_header'];
	$b_header = $g_data[$s]['b_header'];
	$distribution_type = $g_data[$s]['distribution_type'];
	$s_out = $g_data[$s]['s_out'];
	$remote = $g_data[$s]['remote'];
	$remote_cache = $g_data[$s]['remote_cache'];
	$remote_regexp = $g_data[$s]['remote_regexp'];
	$remote_reserved_out = $g_data[$s]['remote_reserved_out'];
	$remote_url = $g_data[$s]['remote_url'];
	$separation = $g_data[$s]['separation'];
	$separation_file = $g_data[$s]['separation_file'];
	$s_curl = $g_data[$s]['s_curl'];
	$b_curl = $g_data[$s]['b_curl'];
	$computer = $g_data[$s]['computer'];
	$phone = $g_data[$s]['phone'];
	$tablet = $g_data[$s]['tablet'];
	$beeline = $g_data[$s]['beeline'];
	$megafon = $g_data[$s]['megafon'];
	$mts = $g_data[$s]['mts'];
	$tele2 = $g_data[$s]['tele2'];
	$azerbaijan = $g_data[$s]['azerbaijan'];
	$belarus = $g_data[$s]['belarus'];
	$kazakhstan = $g_data[$s]['kazakhstan'];
	$ukraine = $g_data[$s]['ukraine'];
	$wap_1 = $g_data[$s]['wap-1'];
	$wap_2 = $g_data[$s]['wap-2'];
	$wap_3 = $g_data[$s]['wap-3'];
	$country_flag = $g_data[$s]['country_flag'];
	$country = $g_data[$s]['country'];
	$city_flag = $g_data[$s]['city_flag'];
	$city = $g_data[$s]['city'];
	$region_flag = $g_data[$s]['region_flag'];
	$region = $g_data[$s]['region'];
	$lang_flag = $g_data[$s]['lang_flag'];
	$lang = $g_data[$s]['lang'];
	$ua_text_flag = $g_data[$s]['ua_text_flag'];
	$ua_text = $g_data[$s]['ua_text'];
	$referer_text_flag = $g_data[$s]['referer_text_flag'];
	$referer_text = $g_data[$s]['referer_text'];
	$domain_text_flag = $g_data[$s]['domain_text_flag'];
	$domain_text = $g_data[$s]['domain_text'];
	$key_text_flag = $g_data[$s]['key_text_flag'];
	$key_text = $g_data[$s]['key_text'];
	$ch_list_ip_flag = $g_data[$s]['ch_list_ip_flag'];
	$list_ip_file = $g_data[$s]['list_ip_file'];
	$bot_redirect = $g_data[$s]['bot_redirect'];
	$out_bot = $g_data[$s]['out_bot'];
	$remote_ip_ch = $g_data[$s]['remote_ip_ch'];
	$ch_ipv6 = $g_data[$s]['ch_ipv6'];
	$ch_bot_ip_baidu = $g_data[$s]['ch_bot_ip_baidu'];
	$ch_bot_ip_bing = $g_data[$s]['ch_bot_ip_bing'];
	$ch_bot_ip_google = $g_data[$s]['ch_bot_ip_google'];
	$ch_bot_ip_mail = $g_data[$s]['ch_bot_ip_mail'];
	$ch_bot_ip_yahoo = $g_data[$s]['ch_bot_ip_yahoo'];
	$ch_bot_ip_yandex = $g_data[$s]['ch_bot_ip_yandex'];
	$ch_bot_ip_others = $g_data[$s]['ch_bot_ip_others'];
	$save_ip = $g_data[$s]['save_ip'];
	$ch_list_ua = $g_data[$s]['ch_list_ua'];
	$ch_ua = $g_data[$s]['ch_ua'];
	$ch_empty_ua = $g_data[$s]['ch_empty_ua'];
	$ch_empty_ref = $g_data[$s]['ch_empty_ref'];
	$ch_empty_lang = $g_data[$s]['ch_empty_lang'];
	$ch_ptr = $g_data[$s]['ch_ptr'];
	$chance = $g_data[$s]['chance'];
	$unique_user = $g_data[$s]['unique_user'];
	$yabrowser = $g_data[$s]['yabrowser'];
	$referer = $g_data[$s]['referer'];
	$s_status = $g_data[$s]['s_status'];
	$comment = $g_data[$s]['comment'];
	$limit = $g_data[$s]['limit'];
	$limit_type = $g_data[$s]['limit_type'];
	$limit_с = $g_data[$s]['limit_с'];
	$limit_h = $g_data[$s]['limit_h'];
	$api_mac_exe = $g_data[$s]['api_mac_exe'];
	$api_mac = $g_data[$s]['api_mac'];
	$api_mac_prob = $g_data[$s]['api_mac_prob'];
	$vt = $g_data[$s]['vt'];
	$vt_option = $g_data[$s]['vt_option'];
	$vt_anti = $g_data[$s]['vt_anti'];
	$vt_out = $g_data[$s]['vt_out'];
}
$db_pb = new SQLite3($folder_log.'/postback.db');
$db_pb->busyTimeout($timeout_2);
$db_pb->exec("PRAGMA journal_mode = WAL;");
if(!$db_pb->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = 'postback';")){
	$db_pb->querySingle("PRAGMA encoding = 'UTF-8'; PRAGMA journal_mode = WAL; CREATE TABLE 'postback' (id INTEGER PRIMARY KEY, date TEXT, time TEXT, domain TEXT, page TEXT, device TEXT, operator TEXT, country TEXT, city TEXT, profit TEXT, ngroup TEXT, nstream TEXT, cid TEXT, strtotime INTEGER);");
	$db_pb->exec("CREATE INDEX 'pb_idx1' ON 'postback' (domain, ngroup, nstream, strtotime);");
	$db_pb->exec("CREATE INDEX 'pb_idx2' ON 'postback' (cid);");
}
if(!empty($_GET['from'])){
	$date_from = $_GET['from'];
}
if(!empty($_GET['to'])){
	$date_to = $_GET['to'];
}
if($range == 1){
	$date_from = date('Y-m-d');
	$date_to = $date_from;
}
if($range == 2){
	$date_from = date('Y-m-d', strtotime('yesterday'));
	$date_to = $date_from;
}
if($range == 3){
	$date_from = date('Y-m-d', strtotime('-6 days'));
	$date_to = date('Y-m-d');
}
if($range == 4){
	$date_from = date('Y-m-d', strtotime('-13 days'));
	$date_to = date('Y-m-d');
}
if($range == 5){
	$date_from = date('Y-m-d', strtotime('monday this week'));
	$date_to = date('Y-m-d');
}
if($range == 6){
	$date_from = date('Y-m-d', strtotime('monday last week'));
	$date_to = date('Y-m-d', strtotime('sunday last week'));
}
if($range == 7){
	$date_from = date('Y-m-01');
	$date_to = date('Y-m-d');
}
if($range == 8){
	$date_from = date('Y-m-d', strtotime('first day of last month'));
	$date_to = date('Y-m-d', strtotime('last day of last month'));
}
if($range == 9){
	$date_from = date('Y-01-01');
	$date_to = date('Y-m-d');
}
if($range == 10){
	$date_from = date('Y-01-01', strtotime('last year'));
	$date_to = date('Y-12-31', strtotime('last year'));
}
$exq = '';
if($range == 11){
	if(!empty($s_name)){
		$exq = "AND nstream = '$s_name'";
	}
	$date_from = $db_pb->querySingle("SELECT date FROM 'postback' WHERE ngroup = '$g_name' $exq ORDER BY strtotime ASC;");
	if(empty($date_from)){
		$date_from = date('Y-m-d');
	}
	$date_to = date('Y-m-d');
}
$dfs = strtotime($date_from);
$dts = strtotime($date_to) + 86400;
if($dfs > $dts - 86400){
	$error = $trans['error']['e7'];
}
$db_pb->close();
if($q == "del_pb" && !empty($d)){
	$db_pb = new SQLite3($folder_log.'/postback.db');
	$db_pb->busyTimeout($timeout_2);
	$db_pb->exec("PRAGMA journal_mode = WAL;");
	$db_pb->querySingle("BEGIN IMMEDIATE;");
	$db_pb->querySingle("DELETE FROM 'postback' WHERE domain = '$d' AND ngroup = '$g_name' $exq AND strtotime >= '$dfs' AND strtotime <= '$dts';");
	$db_pb->querySingle("COMMIT;");
	$db_pb->querySingle("VACUUM;");
	$db_pb->close();
	if(!empty($s)){$st = '&s='.$s;}
	else{$st = '';}
	if($range == 12){$ft = "&from=$date_from&to=$date_to";}
	else{$ft = '';}
	header("Location: $admin_page?q=pb&range=$range&g=$g_id$st$ft");
}
?>