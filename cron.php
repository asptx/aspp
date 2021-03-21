<?php
define("INDEX", "yes");
@set_time_limit(0);
@ini_set('memory_limit', '-1');
require_once 'config.php';
if(!file_exists('temp')){
	mkdir('temp', 0755);
}
$mode = '';
$header = '';
$request = '';
$time = date("H:i");
if($time == '00:00'){
	$expiry = strtotime(date("Y-m-d", strtotime('-'.$period_log.' day')));
	$db_array = array();
	$files = scandir($folder_log);
	foreach($files as $v){
		if($v != "." && $v != ".." && $v != ".htaccess" && $v != "postback.db" && is_file("$folder_log/$v") && preg_match("~^.+?\.db$~", $v)){
			$db_array[] = $v;
		}
	}
	foreach($db_array as $db_name){
		$db = new SQLite3("$folder_log/$db_name");
		$db->busyTimeout($timeout_1);
		$db->exec("PRAGMA journal_mode = WAL;");
		$db->querySingle("BEGIN IMMEDIATE;");
		$table_del = array();
		$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table';");
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$table = $array['name'];
			if($table <= $expiry){
				$table_del[] = $table;
			}
		}
		if(!empty($table_del)){
			foreach($table_del as $table){
				$db->querySingle("DROP TABLE '$table';");
			}
		}
		$db->querySingle("COMMIT;");
		$db->querySingle("VACUUM;");
		$db->close();
	}
	if(file_exists($folder_log.'/postback.db')){
		$expiry = strtotime(date("Y-m-d", strtotime('-'.($period_pb - 1).' day')));
		$db = new SQLite3($folder_log.'/postback.db');
		$db->busyTimeout($timeout_1);
		$db->exec("PRAGMA journal_mode = WAL;");
		$db->querySingle("BEGIN IMMEDIATE;");
		$pb_del = array();
		$res = $db->query("SELECT * FROM 'postback' WHERE strtotime <= '$expiry';");
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$pb_del[] = $array['cid'];
		}
		if(!empty($pb_del)){
			foreach($pb_del as $cid){
				$db->querySingle("DELETE FROM 'postback' WHERE cid = '$cid';");
			}
		}
		$db->querySingle("COMMIT;");
		$db->querySingle("VACUUM;");
		$db->close();
	}
}
if(check_time($cron_time_1)){
	$url = $update_ip_url;
	$data = curl();
	file_put_contents('temp/webcrawlers.dat', $data, LOCK_EX);
	sort_ip();
	unlink('temp/webcrawlers.dat');
}
if(check_time($cron_time_2) && !empty($key_vt) && file_exists('ini')){
	$arr_ini = array();
	$files = scandir($folder_ini);
	foreach($files as $v){
		$v = trim($v);
		if(preg_match("~^(.+?\.ini)$~", $v)){
			$arr_ini[] = $v;
		}
	}
	$arr_domains = array();
	foreach($arr_ini as $ini){
		$g_data = unserialize(file_get_contents($folder_ini.'/'.$ini));
		$s = 1;
		while(!empty($g_data[$s])){
			$wr = 0;
			$arr_msg = array();
			if(!empty($g_data[$s]['vt'])){
				if(!empty($g_data[$s]['s_out']) && preg_match("~^.*http(s)?:\/\/(.+?)[\/\?].*$~", $g_data[$s]['s_out'], $matches)){
					$domain = $matches[2];
					$delay = 0;
					if(!array_key_exists($domain, $arr_domains)){
						$url = "https://www.virustotal.com/api/v3/domains/$domain";
						$header = array();
						$header[] = "x-apikey: $key_vt";
						$mode = 'vt';
						$response = json_decode(curl(), true);
						$arr_domain = array_change_key_case($response['data']['attributes']['last_analysis_results']);
						$arr_domains[$domain] = $arr_domain;
						$delay = 15;
					}
					$status = 0;
					if(!empty($g_data[$s]['vt_anti'])){
						$sep = separator($g_data[$s]['vt_anti']);
						$arr_anti = explode($sep, $g_data[$s]['vt_anti']);
						foreach($arr_anti as $anti){
							$anti = mb_strtolower(trim($anti));
							if($arr_domains[$domain][$anti]['result'] != 'clean' && $arr_domains[$domain][$anti]['result'] != 'unrated'){
								$arr_msg[] = $arr_domains[$domain][$anti]['engine_name'].' => '.$arr_domains[$domain][$anti]['result'];
								$status = 1;
							}
						}
					}
					if($status == 1){
						if($g_data[$s]['vt_option'] == 0 && !empty($g_data[$s]['vt_out'])){
							$sep = separator($g_data[$s]['vt_out']);
							$vt_out = explode($sep, $g_data[$s]['vt_out']);
							if(!empty($vt_out[0])){
								$out_new = trim($vt_out[0]);
								$g_data[$s]['s_out'] = $out_new;
								unset($vt_out[0]);
								$vt_out = array_values($vt_out);
								$vt_out = implode($sep, $vt_out);
								$g_data[$s]['vt_out'] = $vt_out;
								$wr = 1;
							}
						}
						if($g_data[$s]['vt_option'] == 1 && !empty($g_data[$s]['vt_out'])){
							$url = trim($g_data[$s]['vt_out']);
							$mode = '';
							$out_new = htmlentities(trim(curl()));
							if(!empty($out_new)){
								$g_data[$s]['s_out'] = $out_new;
							}
							$wr = 1;
						}
						if($g_data[$s]['vt_option'] == 2 && !empty($tlg_bot_token) && !empty($tlg_chat_id)){
							$msg = 'Group: '.$g_data[0]['g_name']."\nStream: ".$g_data[$s]['s_name']."\nDomain: ".$domain."\n".implode("\n", $arr_msg)."\n";
							$url = "https://api.telegram.org/bot$tlg_bot_token/sendMessage";
							$request = array(
							'chat_id' => $tlg_chat_id,
							'text' => $msg,
							'parse_mode' => 'html'
							);
							$mode = 'tlg';
							curl();
						}
						if($g_data[$s]['vt_option'] == 3){
							$g_data[$s]['s_status'] = 0;
							$wr = 1;
						}
						if($g_data[$s]['vt_option'] == 4){
							$g_data[0]['g_status'] = 0;
							$wr = 1;
						}
						if($wr == 1){
							$g_id = $g_data[0]['g_id'];
							$g_data_wr = serialize($g_data);
							file_put_contents($folder_ini.'/'.$g_id.'.ini', $g_data_wr."\n", LOCK_EX);
						}
					}
					sleep($delay);
				}
			}
			$s++;
		}
	}
}
if(check_time($cron_time_3) && !empty($tlg_bot_token) && !empty($tlg_chat_id)){
	$free_space = round(disk_free_space($_SERVER['DOCUMENT_ROOT']) / 1024 / 1024);
	if($free_space < $min_free_space){
		$msg = "<b>Warning!</b>\nDrives are at risk of running out of free space.\nDisk space available: $free_space Mb";
		$url = "https://api.telegram.org/bot$tlg_bot_token/sendMessage";
		$request = array(
		'chat_id' => $tlg_chat_id,
		'text' => $msg,
		'parse_mode' => 'html'
		);
		$mode = 'tlg';
		curl();
	}
}
function sort_ip(){
	global $update_ip_mode;
	$tmp_db = file('temp/webcrawlers.dat');
	for($i=0; $i<count($tmp_db); $i++){
		if(trim($tmp_db[$i][0]) == '#'){
			$se = 0;
			if(stristr($tmp_db[$i], 'baidu')){
				$se = 'baidu';
			}
			if(stristr($tmp_db[$i], 'bing') || stristr($tmp_db[$i], 'msnbot')){
				$se = 'bing';
			}
			if(stristr($tmp_db[$i], 'google')){
				$se = 'google';
			}
			if(stristr($tmp_db[$i], 'mail')){
				$se = 'mail';
			}
			if(stristr($tmp_db[$i], 'yahoo')){
				$se = 'yahoo';
			}
			if(stristr($tmp_db[$i], 'yandex')){
				$se = 'yandex';
			}
		}
		else{
			if(!empty($se)){
				$ip = trim($tmp_db[$i]);
				if(!empty($ip) && (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))){
					if($se == 'baidu'){
						$baidu[] = $ip;
					}
					if($se == 'bing'){
						$bing[] = $ip;
					}
					if($se == 'google'){
						$google[] = $ip;
					}
					if($se == 'mail'){
						$mail[] = $ip;
					}
					if($se == 'yahoo'){
						$yahoo[] = $ip;
					}
					if($se == 'yandex'){
						$yandex[] = $ip;
					}
				}
			}
			else{
				$ip = trim($tmp_db[$i]);
				if(!empty($ip) && (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))){
					$others[] = $ip;
				}
			}
		}
	}
	unset($tmp_db);
	if(!empty($baidu)){
		$name = 'baidu';
		save($name, $baidu);
		unset($baidu);
	}
	if(!empty($bing)){
		$name = 'bing';
		save($name, $bing);
		unset($bing);
	}
	if(!empty($google)){
		$name = 'google';
		save($name, $google);
		unset($google);
	}
	if(!empty($mail)){
		$name = 'mail';
		save($name, $mail);
		unset($mail);
	}
	if(!empty($yahoo)){
		$name = 'yahoo';
		save($name, $yahoo);
		unset($yahoo);
	}
	if(!empty($yandex)){
		$name = 'yandex';
		save($name, $yandex);
		unset($yandex);
	}
	if(!empty($others)){
		$name = 'others';
		save($name, $others);
		unset($others);
	}
}
function save($name, $data){
	global $update_ip_mode;
	if($update_ip_mode == 1){
		$file = trim(file_get_contents("database/ip_$name.dat"));
		$file = explode("\n", $file);
		$data = array_merge($file, $data);
		unset($file);
	}
	$data = array_unique($data);
	sort($data);
	$data = implode("\n", $data);
	file_put_contents("database/ip_$name.dat", $data."\n", LOCK_EX);
	unset($data);
}
function separator($str){
	if(stristr($str, ',')){
		$sep = ',';
	}
	else{
		$sep = "\n";
	}
	return $sep;
}
function curl(){
	global $url, $mode, $header, $request, $curl_ua;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $curl_ua);
	if($mode == 'vt'){
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	if($mode == 'tlg'){
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
	}
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}
function check_time($time_array){
	global $time;
	$time_array = explode(',', $time_array);
	foreach($time_array as $v){
		if($time == trim($v)){
			return true;
		}
	}
}
?>