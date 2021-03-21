<?php
header('Access-Control-Allow-Origin: *');
define("INDEX", "yes");
@ini_set('max_execution_time', $max_ex_time);
require_once 'config.php';
if($disable_tds == 1){
	exit();
}
$bot = $empty;
$cf_country = $empty;
$page = $empty;
$s_api_mac_exe = 0;
$s_ch_ua = '';
$postback = '';
$parameter_1 = '';
$parameter_2 = '';
$parameter_3 = '';
$parameter_4 = '';
$parameter_5 = '';
if(!empty($folder_tds)){
	$path = $_SERVER['HTTP_HOST'].'/'.$folder_tds;
}
else{
	$path = $_SERVER['HTTP_HOST'];
}
if(!empty($_GET['pb']) == $key_postback){
	if(!empty($_GET['cid']) && stristr($_GET['cid'], $cid_delimiter) && !empty($_GET['profit'])){
		$get_cid = $_GET['cid'];
		$profit = SQLite3::escapeString(floatval($_GET['profit']));
		if(empty($profit) || !is_numeric($profit)){
			err_404();
		}
	}
	else{
		err_404();
	}
	$ex_cid = explode($cid_delimiter, $get_cid);
	if(!empty($ex_cid[0]) && !empty($ex_cid[1])){
		$id = $ex_cid[0];
		$cid = $ex_cid[1];
	}
	else{
		err_404();
	}
	if(!file_exists($folder_log.'/'.$id.'.db')){
		err_404();
	}
	$db_pb = new SQLite3($folder_log.'/postback.db');
	$db_pb->busyTimeout($timeout_1);
	$db_pb->exec("PRAGMA journal_mode = WAL;");
	$db_pb->querySingle("BEGIN IMMEDIATE;");
	if(!$db_pb->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = 'postback';")){
		$db_pb->querySingle("PRAGMA encoding = 'UTF-8'; PRAGMA journal_mode = WAL; CREATE TABLE 'postback' (id INTEGER PRIMARY KEY, date TEXT, time TEXT, domain TEXT, page TEXT, device TEXT, operator TEXT, country TEXT, city TEXT, profit TEXT, ngroup TEXT, nstream TEXT, cid TEXT, strtotime INTEGER);");
		$db_pb->exec("CREATE INDEX 'pb_idx1' ON 'postback' (domain, ngroup, nstream, strtotime);");
		$db_pb->exec("CREATE INDEX 'pb_idx2' ON 'postback' (cid);");
	}
	$db = new SQLite3($folder_log.'/'.$id.'.db');
	$db->busyTimeout($timeout_1);
	$db->exec("PRAGMA journal_mode = WAL;");
	$db->querySingle("BEGIN IMMEDIATE;");
	$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table' ORDER BY name DESC;");
	$end = 0;
	while(true){
		if($array = $res->fetchArray(SQLITE3_ASSOC)){
			$table = $array['name'];
			$test_1 = $db->querySingle("SELECT id FROM '$table' WHERE cid = '$cid';");
			if(!empty($test_1)){
				$db->querySingle("UPDATE '$table' SET postback = '$profit' WHERE cid = '$cid';");
				$test_2 = $db_pb->querySingle("SELECT id FROM 'postback' WHERE cid = '$cid';");
				if(empty($test_2)){
					$postback_data = $db->query("SELECT * FROM '$table' WHERE cid = '$cid';");
					$date = date('Y-m-d', $table);
					if($array = $postback_data->fetchArray(SQLITE3_ASSOC)){
						$db_pb->querySingle("INSERT INTO 'postback' (date, time, domain, page, device, operator, country, city, profit, ngroup, nstream, cid, strtotime) VALUES ('".$date."', '".$array['time']."', '".SQLite3::escapeString($array['domain'])."', '".SQLite3::escapeString($array['page'])."', '".$array['device']."', '".$array['operator']."', '".SQLite3::escapeString($array['country'])."', '".SQLite3::escapeString($array['city'])."', '$profit', '".SQLite3::escapeString($array['ngroup'])."', '".SQLite3::escapeString($array['nstream'])."', '".$array['cid']."', '".$array['strtotime']."')");
					}
				}
				else{
					$db_pb->querySingle("UPDATE 'postback' SET profit = '$profit' WHERE cid = '$cid';");
				}
				break;
			}
		}
		else{
			break;
		}
	}
	$db->querySingle("COMMIT;");
	$db_pb->querySingle("COMMIT;");
	$db->close();
	$db_pb->close();
	exit();
}
if(!empty($_GET['api'])){
	$api_data = @unserialize(base64_decode($_GET['api']));
	$key_api_host = trim($api_data['key_api']);
	$id = trim($api_data['id']);
	$ipuser = trim($api_data['ip']);
	$referer = trim($api_data['referer']);
	$useragent = trim($api_data['useragent']);
	$se = trim($api_data['se']);
	$lang = trim($api_data['lang']);
	$uniq = trim($api_data['uniq']);
	$key = trim($api_data['key']);
	$domain = trim($api_data['domain']);
	$page = trim($api_data['page']);
	$cf_country = trim($api_data['cf_country']);
	$parameter_1 = urldecode(trim($api_data['par_1']));
	$parameter_2 = urldecode(trim($api_data['par_2']));
	$parameter_3 = urldecode(trim($api_data['par_3']));
	$parameter_4 = urldecode(trim($api_data['par_4']));
	$parameter_5 = urldecode(trim($api_data['par_5']));
	$counter = $empty;
	if($key_api != $key_api_host){
		exit();
	}
}
else{
	if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ".") > 0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ",") > 0){
			$ip = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
			$ipuser = trim($ip[0]);
		}
		elseif(strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ".") > 0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ",") === false){
			$ipuser = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
		}
	}
	if(empty($ipuser)){
		$ipuser = trim($_SERVER['REMOTE_ADDR']);
	}
	if(!filter_var($ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$ipuser = $empty;
	}
	if(!empty($_SERVER['HTTP_USER_AGENT'])){
		$useragent = $_SERVER['HTTP_USER_AGENT'];
	}
	else{
		$useragent = $empty;
	}
	if(!empty($_SERVER['HTTP_REFERER'])){
		$referer = $_SERVER['HTTP_REFERER'];
	}
	else{
		$referer = $empty;
	}
	if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	}
	else{
		$lang = $empty;
	}
	$se = $empty;
}
$ip_stop_file = file('database/ip_blacklist.dat');
if(search_in_database($ip_stop_file, $ipuser)){
	exit();
}
if(empty($_GET['api'])){
	$in_url = $_SERVER['HTTP_HOST'].trim($_SERVER['REQUEST_URI']);
	if(preg_match("~^$path\/([^/]*)(/)?$~", $in_url, $matches)){
		$id = $matches[1];
	}
	if(preg_match("~^$path\/(.+?)\/(.+?)$~", $in_url, $matches)){
		$id = $matches[1];
		$key = $matches[2];
	}
	if(preg_match("~^$path\/(.+?)\?.+?=(.+?)?$~", $in_url, $matches)){
		$id = $matches[1];
		$par = array();
		foreach($_GET as $name => $value){
			if($name == $name_get_ex){
				$parameters = base64_decode($value);
				break;
			}
			else{
				if($name == $name_get_key){
					$key = urldecode(trim($value));
				}
				else{
					$par[] = array("name"=>$name, "value"=>$value);
				}
			}
		}
		if(empty($parameters)){
			if(!empty($par[0])){$parameter_1 = urldecode($par[0]['value']);}
			if(!empty($par[1])){$parameter_2 = urldecode($par[1]['value']);}
			if(!empty($par[2])){$parameter_3 = urldecode($par[2]['value']);}
			if(!empty($par[3])){$parameter_4 = urldecode($par[3]['value']);}
			if(!empty($par[4])){$parameter_5 = urldecode($par[4]['value']);}
		}
		if(!empty($parameters)){
			$parameters = @unserialize($parameters);
			if(!empty($parameters['key'])){$key = $parameters['key'];}
			if(!empty($parameters['referer'])){$referer = $parameters['referer'];}else{$referer = $empty;}
			if(!empty($parameters['par_1'])){$parameter_1 = urldecode($parameters['par_1']);}
			if(!empty($parameters['par_2'])){$parameter_2 = urldecode($parameters['par_2']);}
			if(!empty($parameters['par_3'])){$parameter_3 = urldecode($parameters['par_3']);}
			if(!empty($parameters['par_4'])){$parameter_4 = urldecode($parameters['par_4']);}
			if(!empty($parameters['par_5'])){$parameter_5 = urldecode($parameters['par_5']);}
		}
	}
	if(preg_match("~^http.*://(.+?)/.*$~", $referer, $matches)){
		$domain = $matches[1];
	}
	else{
		$domain = 'unknown';
	}
}
if(!empty($key)){
	$key = urldecode($key);
	if(substr($key, 0, 1) == '%'){
		$key = urldecode($key);
	}
	if(utf8_bad_find($key) !== false){
		$key = iconv('windows-1251', 'utf-8', $key);
	}
}
if(!empty($id) && file_exists($folder_ini.'/'.$id.'.ini')){
	$data_ini = @unserialize(file_get_contents($folder_ini.'/'.$id.'.ini'));
}
else{
	trash();
}
$g_name = $data_ini[0]['g_name'];
$g_redirect = $data_ini[0]['g_redirect'];
$g_header = $data_ini[0]['g_header'];
$g_out = $data_ini[0]['g_out'];
$g_status = $data_ini[0]['g_status'];
$g_uniq_method = $data_ini[0]['g_uniq_method'];
$g_uniq_time = $data_ini[0]['g_uniq_time'];
$g_firewall = $data_ini[0]['g_firewall'];
$g_f_queries = $data_ini[0]['g_f_queries'];
$g_f_time = $data_ini[0]['g_f_time'];
$g_save_keys = $data_ini[0]['g_save_keys'];
$g_save_keys_se = $data_ini[0]['g_save_keys_se'];
$g_curl = $data_ini[0]['g_curl'];
$g_geo = $data_ini[0]['g_geo'];
if($g_status == 0){
	if($g_redirect == 'api' || $g_redirect == 'iframe' || $g_redirect == 'iframe_redirect' || $g_redirect == 'js_selection' || $g_redirect == 'javascript' || $g_redirect == 'stop'){
		exit();
	}
	else{
		trash();
	}
}
if($g_geo == 'cf' && !empty($_GET['api'])){
	$country = $cf_country;
	$city = $empty;
	$region = $empty;
}
if($g_geo == 'sypex' || ($g_geo == 'cf' && empty($_GET['api'])) || $cf_country == $empty){
	require_once 'files/lib/sypex/SxGeo.php';
	$SxGeo = new SxGeo('files/lib/sypex/SxGeo.dat');
	$country = $SxGeo->getCountry($ipuser);
	if(empty($country)){
		$country = $empty;
	}
	else{
		$country = strtolower($country);
	}
	$SxGeo = new SxGeo('files/lib/sypex/SxGeoCity.dat');
	$geodata = $SxGeo->getCityFull($ipuser);
	if(empty($geodata["city"]["name_en"])){
		$city = $empty;
	}
	else{
		$city = strtolower($geodata["city"]["name_en"]);
	}
	if(empty($geodata["region"]["iso"])){
		$region = $empty;
	}
	else{
		$region = strtolower($geodata["region"]["iso"]);
	}
}
require_once 'files/lib/Mobile_Detect.php';
$detect = new Mobile_Detect;
$detect->setUserAgent($useragent);
$device = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$wap = file('database/wap.dat');
if(search_in_database($wap, $ipuser)){
	$operator = $label;
}
if(empty($operator)){
	$operator = $empty;
}
if(empty($_GET['api'])){
	if(!isset($_COOKIE[$name_cookies.'_'.$id])){
		SetCookie($name_cookies.'_'.$id, 0, time() + $g_uniq_time, '/');
		$c_counter = 0;
		if($g_uniq_method == 0){
			$uniq = 'yes';
		}
	}
	else{
		$c_counter = $_COOKIE[$name_cookies.'_'.$id] + 1;
		SetCookie($name_cookies.'_'.$id, $c_counter, time() + $g_uniq_time, '/');
		$uniq = 'no';
	}
}
$db = new SQLite3($folder_log.'/'.$id.'.db');
$db->busyTimeout($timeout_1);
$db->exec("PRAGMA journal_mode = WAL;");
$db->querySingle("BEGIN IMMEDIATE;");
if($g_uniq_method == 1){
	$uniq = 'no';
	$y = strtotime("- $g_uniq_time seconds");
	$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table' ORDER BY name DESC;");
	while(true){
		if($array = $res->fetchArray(SQLITE3_ASSOC)){
			$table = $array['name'];
			$count = $db->querySingle("SELECT COUNT(id) FROM '$table';");
			$z = $db->querySingle("SELECT strtotime FROM '$table' WHERE id = '$count';");
			if($z < $y){
				$uniq = 'yes';
				break;
			}
			if($db->querySingle("SELECT strtotime FROM '$table' WHERE uniq = 'yes' AND ipuser = '$ipuser' AND strtotime > $y;")){
				break;
			}
		}
		else{
			$uniq = 'yes';
			break;
		}
	}
}
if($g_firewall == 1){
	$c = 0;
	$y = strtotime("- $g_f_time seconds");
	$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table' ORDER BY name DESC;");
	$end = 0;
	while(true){
		if($array = $res->fetchArray(SQLITE3_ASSOC)){
			$table = $array['name'];
			$count = $db->querySingle("SELECT COUNT (*) FROM '$table';");
			$z = $db->querySingle("SELECT strtotime FROM '$table' WHERE id = '$count';");
			if($z > $y){
				$c = $db->querySingle("SELECT COUNT (*) FROM '$table' WHERE ipuser = '$ipuser' AND strtotime > $y;");
			}
			if($c >= $g_f_queries){
				exit();
			}
		}
		else{
			break;
		}
	}
}
$z = 0;
$x = 1;
while(true){
	if(!empty($data_ini[$x])){
		$z = 1;
		if($data_ini[$x]['s_status'] != 1){
			$z = 0;
		}
		if($z != 0){
			$stream = $data_ini[$x]['s_name'];
			if($data_ini[$x]['limit'] == 1){
				if($data_ini[$x]['limit_type'] == 1){
					$table = strtotime(date('Y-m-d'));
					if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table';")){
						$c = $db->querySingle("SELECT COUNT (*) FROM '$table' WHERE nstream = '$stream';");
						if($c >= $data_ini[$x]['limit_с']){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['limit_type'] == 2){
					$c = 0;
					$limit_h = $data_ini[$x]['limit_h'];
					$y = strtotime("- $limit_h seconds");
					$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table' ORDER BY name DESC;");
					while(true){
						if($array = $res->fetchArray(SQLITE3_ASSOC)){
							$table = $array['name'];
							$count = $db->querySingle("SELECT COUNT (*) FROM '$table';");
							$r = $db->querySingle("SELECT strtotime FROM '$table' WHERE id = '$count';");
							if($r > $y){
								$c = $db->querySingle("SELECT COUNT (*) FROM '$table' WHERE strtotime > $y AND nstream = '$stream';");
							}
							if($c >= $data_ini[$x]['limit_с']){
								$z = 0;
								break;
							}
						}
						else{
							break;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['lang_flag'] != 2 && !empty($data_ini[$x]['lang'])){
				if($data_ini[$x]['lang_flag'] == 0){
					if(stristr($data_ini[$x]['lang'], $lang)){
						$z = 0;
					}
				}
				if($data_ini[$x]['lang_flag'] == 1){
					if(!stristr($data_ini[$x]['lang'], $lang)){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['country_flag'] != 2 && !empty($data_ini[$x]['country'])){
				if($data_ini[$x]['country_flag'] == 0){
					if(stristr($data_ini[$x]['country'], $country)){
						$z = 0;
					}
				}
				if($data_ini[$x]['country_flag'] == 1){
					if(!stristr($data_ini[$x]['country'], $country)){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['city_flag'] != 2 && !empty($data_ini[$x]['city'])){
				$c = 0;
				$city_x = explode(',', $data_ini[$x]['city']);
				if($data_ini[$x]['city_flag'] == 0){
					while(!empty($city_x[$c])){
						if(strcasecmp(trim($city_x[$c]), $city) == 0){
							$c = 'end';
							break;
						}
						$c++;
					}
					if($c == 'end'){
						$z = 0;
					}
				}
				if($data_ini[$x]['city_flag'] == 1){
					while(!empty($city_x[$c])){
						if(strcasecmp(trim($city_x[$c]), $city) == 0){
							$c = 'end';
							break;
						}
						$c++;
					}
					if($c != 'end'){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['region_flag'] != 2 && !empty($data_ini[$x]['region'])){
				$c = 0;
				$region_x = explode(',', $data_ini[$x]['region']);
				if($data_ini[$x]['region_flag'] == 0){
					while(!empty($region_x[$c])){
						if(strcasecmp(trim($region_x[$c]), $region) == 0){
							$c = 'end';
							break;
						}
						$c++;
					}
					if($c == 'end'){
						$z = 0;
					}
				}
				if($data_ini[$x]['region_flag'] == 1){
					while(!empty($region_x[$c])){
						if(strcasecmp(trim($region_x[$c]), $region) == 0){
							$c = 'end';
							break;
						}
						$c++;
					}
					if($c != 'end'){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['ua_text_flag'] != 2 && !empty($data_ini[$x]['ua_text'])){
				$ua_text = $data_ini[$x]['ua_text'];
				$c = 0;
				$ua_text_x = explode(',', $ua_text);
				if($data_ini[$x]['ua_text_flag'] == 0){
					if(substr($ua_text, 0, 1) == '/'){
						if(preg_match($ua_text, $useragent, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($ua_text_x[$c])){
							if(stristr($useragent, $ua_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c == 'end'){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['ua_text_flag'] == 1){
					if(substr($ua_text, 0, 1) == '/'){
						if(!preg_match($ua_text, $useragent, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($ua_text_x[$c])){
							if(stristr($useragent, $ua_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c != 'end'){
							$z = 0;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['referer_text_flag'] != 2 && !empty($data_ini[$x]['referer_text'])){
				$referer_text = $data_ini[$x]['referer_text'];
				$c = 0;
				$referer_text_x = explode(',', $referer_text);
				if($data_ini[$x]['referer_text_flag'] == 0){
					if(substr($referer_text, 0, 1) == '/'){
						if(preg_match($referer_text, $referer, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($referer_text_x[$c])){
							if(stristr($referer, $referer_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c == 'end'){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['referer_text_flag'] == 1){
					if(substr($referer_text, 0, 1) == '/'){
						if(!preg_match($referer_text, $referer, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($referer_text_x[$c])){
							if(stristr($referer, $referer_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c != 'end'){
							$z = 0;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['domain_text_flag'] != 2 && !empty($data_ini[$x]['domain_text'])){
				$domain_text = $data_ini[$x]['domain_text'];
				$c = 0;
				$domain_text_x = explode(',', $domain_text);
				if($data_ini[$x]['domain_text_flag'] == 0){
					if(substr($domain_text, 0, 1) == '/'){
						if(preg_match($domain_text, $domain, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($domain_text_x[$c])){
							if(strcasecmp(trim($domain_text_x[$c]), $domain) == 0){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c == 'end'){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['domain_text_flag'] == 1){
					if(substr($domain_text, 0, 1) == '/'){
						if(!preg_match($domain_text, $domain, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($domain_text_x[$c])){
							if(strcasecmp(trim($domain_text_x[$c]), $domain) == 0){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c != 'end'){
							$z = 0;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['key_text_flag'] != 2 && !empty($data_ini[$x]['key_text'])){
				$key_text = $data_ini[$x]['key_text'];
				upper_replace();
				$c = 0;
				$key_text_x = explode(',', $key_text);
				if($data_ini[$x]['key_text_flag'] == 0){
					if(substr($key_text, 0, 1) == '/'){
						if(preg_match($key_text, $key, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($key_text_x[$c])){
							if(stristr($key, $key_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c == 'end'){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['key_text_flag'] == 1){
					if(substr($key_text, 0, 1) == '/'){
						if(!preg_match($key_text, $key, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($key_text_x[$c])){
							if(stristr($key, $key_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c != 'end'){
							$z = 0;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['computer'] != 2){
				if($data_ini[$x]['computer'] == 0 && $device == 'computer'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['phone'] != 2){
				if($data_ini[$x]['phone'] == 0 && $device == 'phone'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['tablet'] != 2){
				if($data_ini[$x]['tablet'] == 0 && $device == 'tablet'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['beeline'] != 2){
				if($data_ini[$x]['beeline'] == 0 && $operator == 'beeline'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['megafon'] != 2){
				if($data_ini[$x]['megafon'] == 0 && $operator == 'megafon'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['mts'] != 2){
				if($data_ini[$x]['mts'] == 0 && $operator == 'mts'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['tele2'] != 2){
				if($data_ini[$x]['tele2'] == 0 && $operator == 'tele2'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['azerbaijan'] != 2){
				if($data_ini[$x]['azerbaijan'] == 0 && $operator == 'azerbaijan'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['belarus'] != 2){
				if($data_ini[$x]['belarus'] == 0 && $operator == 'belarus'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['kazakhstan'] != 2){
				if($data_ini[$x]['kazakhstan'] == 0 && $operator == 'kazakhstan'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['ukraine'] != 2){
				if($data_ini[$x]['ukraine'] == 0 && $operator == 'ukraine'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['wap-1'] != 2){
				if($data_ini[$x]['wap-1'] == 0 && $operator == 'wap-1'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['wap-2'] != 2){
				if($data_ini[$x]['wap-2'] == 0 && $operator == 'wap-2'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['wap-3'] != 2){
				if($data_ini[$x]['wap-3'] == 0 && $operator == 'wap-3'){
					$z = 0;
				}
			}
		}
		if($data_ini[$x]['beeline'] == 1 || $data_ini[$x]['megafon'] == 1 || $data_ini[$x]['mts'] == 1 || $data_ini[$x]['tele2'] == 1 || $data_ini[$x]['azerbaijan'] == 1 || $data_ini[$x]['belarus'] == 1 || $data_ini[$x]['kazakhstan'] == 1 || $data_ini[$x]['ukraine'] == 1 || $data_ini[$x]['wap-1'] == 1 || $data_ini[$x]['wap-2'] == 1 || $data_ini[$x]['wap-3'] == 1){
			if($operator == $empty){
				$z = 0;
			}
		}
		if($z != 0){
			if($data_ini[$x]['unique_user'] == 0){
				if($uniq != 'yes'){
					$z = 0;
				}
			}
			if($data_ini[$x]['unique_user'] == 1){
				if($uniq == 'yes'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['yabrowser'] != 2){
				if($data_ini[$x]['yabrowser'] == 0){
					if(stristr($useragent, 'yabrowser') || stristr($useragent, 'YandexSearchBrowser')){
						$z = 0;
					}
				}
				if($data_ini[$x]['yabrowser'] == 1){
					if(!stristr($useragent, 'yabrowser') || !stristr($useragent, 'YandexSearchBrowser')){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['referer'] != 2){
				if($data_ini[$x]['referer'] == 0){
					if($referer == $empty){
						$z = 0;
					}
				}
				if($data_ini[$x]['referer'] == 1){
					if($referer != $empty){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['ch_list_ip_flag'] != 2){
				if(!empty($data_ini[$x]['list_ip_file'])){
					$list_ip = file('database/'.$data_ini[$x]['list_ip_file']);
					if($data_ini[$x]['ch_list_ip_flag'] == 0){
						if(search_in_database($list_ip, $ipuser)){
							$z = 0;
						}
					}
					if($data_ini[$x]['ch_list_ip_flag'] == 1){
						if(!search_in_database($list_ip, $ipuser)){
							$z = 0;
						}
					}
				}
			}
		}
		if($z == 1){
			$s_name = $data_ini[$x]['s_name'];
			$s_redirect = $data_ini[$x]['redirect'];
			$s_header = $data_ini[$x]['s_header'];
			$b_header = $data_ini[$x]['b_header'];
			$s_distribution_type = $data_ini[$x]['distribution_type'];
			$s_out = $data_ini[$x]['s_out'];
			$s_remote = $data_ini[$x]['remote'];
			$s_remote_cache = $data_ini[$x]['remote_cache'];
			$s_remote_regexp = $data_ini[$x]['remote_regexp'];
			$s_remote_reserved_out = $data_ini[$x]['remote_reserved_out'];
			$s_remote_url = $data_ini[$x]['remote_url'];
			$s_separation = $data_ini[$x]['separation'];
			$s_separation_file = $data_ini[$x]['separation_file'];
			$s_curl = $data_ini[$x]['s_curl'];
			$s_bot_curl = $data_ini[$x]['b_curl'];
			$s_bot_redirect = $data_ini[$x]['bot_redirect'];
			$s_out_bot = $data_ini[$x]['out_bot'];
			$s_remote_ip_ch = $data_ini[$x]['remote_ip_ch'];
			$s_ch_ipv6 = $data_ini[$x]['ch_ipv6'];
			$s_ch_bot_ip_baidu = $data_ini[$x]['ch_bot_ip_baidu'];
			$s_ch_bot_ip_bing = $data_ini[$x]['ch_bot_ip_bing'];
			$s_ch_bot_ip_google = $data_ini[$x]['ch_bot_ip_google'];
			$s_ch_bot_ip_mail = $data_ini[$x]['ch_bot_ip_mail'];
			$s_ch_bot_ip_yahoo = $data_ini[$x]['ch_bot_ip_yahoo'];
			$s_ch_bot_ip_yandex = $data_ini[$x]['ch_bot_ip_yandex'];
			$s_ch_bot_ip_others = $data_ini[$x]['ch_bot_ip_others'];
			$s_save_ip = $data_ini[$x]['save_ip'];
			$s_ch_list_ua = $data_ini[$x]['ch_list_ua'];
			$s_ch_ua = $data_ini[$x]['ch_ua'];
			$s_ch_empty_ua = $data_ini[$x]['ch_empty_ua'];
			$s_ch_empty_ref = $data_ini[$x]['ch_empty_ref'];
			$s_ch_empty_lang = $data_ini[$x]['ch_empty_lang'];
			$s_ch_ptr = $data_ini[$x]['ch_ptr'];
			$s_chance = $data_ini[$x]['chance'];
			$s_api_mac_exe = $data_ini[$x]['api_mac_exe'];
			$s_api_mac = $data_ini[$x]['api_mac'];
			$s_api_mac_prob = $data_ini[$x]['api_mac_prob'];
			break;
		}
		$x++;
	}
	else{
		break;
	}
}
if((!empty($s_name) && $s_ch_ua == 1) || empty($s_name)){
	if(stristr($useragent, 'baidu')){$bot = 'baidu';}
	if(stristr($useragent, 'bing') || stristr($useragent, 'msnbot')){$bot = 'bing';}
	if(stristr($useragent, 'google')){$bot = 'google';}
	if(stristr($useragent, 'mail.ru')){$bot = 'mail';}
	if(stristr($useragent, 'yahoo')){$bot = 'yahoo';}
	if(stristr($useragent, 'yandex.com/bots')){$bot = 'yandex';}
	if($bot == $empty && $useragent != $empty){
		$signature_ua = file('database/signature_ua.dat');
		for($i = 0; $i<count($signature_ua); $i++){
			$signature_ua_x = trim($signature_ua[$i]);
			if(stristr($useragent, $signature_ua_x)){
				$bot = 'sign_ua';
				break;
			}
		}
	}
	if($bot == $empty && $referer != $empty){
		$signature_ref = file('database/signature_ref.dat');
		for($i = 0; $i<count($signature_ref); $i++){
			$signature_ref_x = trim($signature_ref[$i]);
			if(stristr($referer, $signature_ref_x)){
				$bot = 'sign_ref';
				break;
			}
		}
	}
}
if(!empty($s_name)){
	if($bot == $empty && $s_ch_ipv6 == 1 && filter_var($ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$bot = 'ipv6';
	}
	if($bot == $empty && $s_ch_empty_ua == 1){
		if($useragent == $empty || $useragent == ' '){
			$bot = 'empty_ua';
		}
	}
	if($bot == $empty && $s_ch_empty_ref == 1){
		if($referer == $empty){
			$bot = 'empty_ref';
		}
	}
	if($bot == $empty && $s_ch_empty_lang == 1){
		if($lang == $empty){
			$bot = 'empty_lang';
		}
	}
	if($bot == $empty && $s_ch_ptr == 1 && $ipuser != $empty){
		$ptr = gethostbyaddr($ipuser);
		if(stristr($ptr, 'baidu')){$bot = 'baidu';}
		if(stristr($ptr, 'bing') || stristr($ptr, 'msnbot')){$bot = 'bing';}
		if(stristr($ptr, 'google')){$bot = 'google';}
		if(stristr($ptr, 'mail.ru')){$bot = 'mail';}
		if(stristr($ptr, 'yahoo')){$bot = 'yahoo';}
		if(stristr($ptr, 'yandex')){$bot = 'yandex';}
	}
	if($bot == $empty && $s_ch_list_ua == 1){
		$bots_ua = file('database/ua_blacklist.dat');
		for($i = 0; $i<count($bots_ua); $i++){
			$bots_ua[$i] = trim($bots_ua[$i]);
			if($useragent == $bots_ua[$i]){
				$bot = 'ua_blacklist';
			}
		}
	}
	if(($bot != $empty && $s_save_ip == 1) && ($bot == 'baidu' || $bot == 'bing' || $bot == 'google' || $bot == 'mail' || $bot == 'yahoo' || $bot == 'yandex')){
		$bots_ip_file = file('database/ip_'.$bot.'.dat');
		if(!search_in_database($bots_ip_file, $ipuser)){
			file_put_contents('database/ip_'.$bot.'.dat', $ipuser."\n", FILE_APPEND | LOCK_EX);
		}
	}
	if($s_ch_bot_ip_baidu == 1 && $bot == $empty){
		$bots_ip = file('database/ip_baidu.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'baidu';
		}
	}
	if($s_ch_bot_ip_bing == 1 && $bot == $empty){
		$bots_ip = file('database/ip_bing.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'bing';
		}
	}
	if($s_ch_bot_ip_google == 1 && $bot == $empty){
		$bots_ip = file('database/ip_google.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'google';
		}
	}
	if($s_ch_bot_ip_mail == 1 && $bot == $empty){
		$bots_ip = file('database/ip_mail.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'mail';
		}
	}
	if($s_ch_bot_ip_yahoo == 1 && $bot == $empty){
		$bots_ip = file('database/ip_yahoo.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'yahoo';
		}
	}
	if($s_ch_bot_ip_yandex == 1 && $bot == $empty){
		$bots_ip = file('database/ip_yandex.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'yandex';
		}
	}
	if($s_ch_bot_ip_others == 1 && $bot == $empty){
		$bots_ip = file('database/ip_others.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'others';
		}
	}
	if($bot == $empty && $s_remote_ip_ch == 1 && !empty($remote_ip_ch_url)){
		$remote_ip_ch_url = str_ireplace('[IP]', $ipuser, $remote_ip_ch_url);
		curl_remote_ip_ch();
		if(!empty($res)){
			if(stristr($res, $remote_ip_ch_sign)){
				$bot = 'remote';
			}
		}
	}
	if($bot != $empty){
		$header = $b_header;
		if($s_bot_redirect == 'api'){
			$out = $s_out_bot;
			$redirect = 'api';
			out();
		}
		if($s_bot_redirect == 'curl'){
			$out = $s_out_bot;
			$redirect = 'curl';
			$curl = $s_bot_curl;
			out();
		}
		if($s_bot_redirect == 'http_redirect'){
			$out = $s_out_bot;
			$redirect = 'http_redirect';
			out();
		}
		if($s_bot_redirect == 'javascript'){
			$out = $s_out_bot;
			$redirect = 'javascript';
			out();
		}
		if($s_bot_redirect == 'meta_refresh'){
			$out = $s_out_bot;
			$redirect = 'meta_refresh';
			out();
		}
		if($s_bot_redirect == 'show_out'){
			$out = $s_out_bot;
			$redirect = 'show_out';
			out();
		}
		if($s_bot_redirect == 'show_page_html'){
			$out = $s_out_bot;
			$redirect = 'show_page_html';
			out();
		}
		if($s_bot_redirect == 'show_text'){
			$out = $s_out_bot;
			$redirect = 'show_text';
			out();
		}
		if($s_bot_redirect == 'stop'){
			$redirect = 'stop';
			unset($out);
			out();
		}
		if($s_bot_redirect == 'under_construction'){
			$redirect = 'under_construction';
			unset($out);
			out();
		}
		if($s_bot_redirect == '403_forbidden'){
			$redirect = '403_forbidden';
			unset($out);
			out();
		}
		if($s_bot_redirect == '404_not_found'){
			$redirect = '404_not_found';
			unset($out);
			out();
		}
		if($s_bot_redirect == '500_server_error'){
			$redirect = '500_server_error';
			unset($out);
			out();
		}
	}
	if($s_separation == 1 && !empty($key) && !empty($s_separation_file)){
		if($bot == $empty || $s_bot_redirect == 'skip'){
			s_separation();
		}
	}
	if($s_remote == 1 && !empty($s_out) && stristr($s_out, '[REMOTE]')){
		$s_remote_url = html_entity_decode($s_remote_url, ENT_QUOTES, 'UTF-8');
		if($s_remote_cache != 0){
			$st_now = strtotime("now");
			$st = strtotime("- $s_remote_cache seconds");
			temp();
			if(!file_exists('temp/remote_'.$g_name.'_'.$s_name)){
				remote_pars();
			}
			else{
				$file = file('temp/remote_'.$g_name.'_'.$s_name);
				$dat = explode(';', $file[0]);
				if($dat[0] > $st){
					$s_out = str_ireplace('[REMOTE]', $dat[1], $s_out);
				}
				else{
					remote_pars();
				}
			}
		}
		else{
			if(stristr($s_remote_url, '[IP]')){
				$s_remote_url = str_ireplace('[IP]', $ipuser, $s_remote_url);
			}
			if(stristr($s_remote_url, '[COUNTRY]')){
				$s_remote_url = str_ireplace('[COUNTRY]', $country, $s_remote_url);
			}
			if(stristr($s_remote_url, '[CITY]')){
				$s_remote_url = str_ireplace('[CITY]', $city, $s_remote_url);
			}
			if(stristr($s_remote_url, '[LANG]')){
				$s_remote_url = str_ireplace('[LANG]', $lang, $s_remote_url);
			}
			if(stristr($s_remote_url, '[KEY]')){
				$s_remote_url = str_ireplace('[KEY]', $key, $s_remote_url);
			}
			remote_pars();
		}
	}
}
if(!empty($s_out) && stristr($s_out, '|||')){
	$out_ex = explode("|||", $s_out);
	if($s_distribution_type == 'rotator'){
		if(empty($_GET['api'])){
			if(isset($out_ex[$c_counter])){
				$out = trim($out_ex[$c_counter]);
				$counter = $c_counter;
			}
			else{
				$out = trim($out_ex[0]);
				$counter = 0;
				SetCookie($name_cookies.'_'.$id, 0, time() + $g_uniq_time, '/');
			}
		}
		else{
			$out = $s_out;
		}
	}
	if($s_distribution_type == 'evenly'){
		$table = strtotime(date('Y-m-d'));
		if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table';")){
			$query = $db->query("SELECT * FROM '$table' WHERE nstream = '$s_name' ORDER BY id DESC LIMIT 1;");
			$row = $query->fetchArray();
			$c = $row['counter'];
			if(is_numeric($c)){
				$c++;
			}
			else{
				$c = 0;
			}
			$counter = $c;
			if(empty($out_ex[$c])){
				$out = trim($out_ex[0]);
				$counter = 0;
			}
			else{
				$out = trim($out_ex[$c]);
			}
		}
		else{
			$out = trim($out_ex[0]);
			$counter = 0;
		}
	}
	if($s_distribution_type == 'random'){
		$rand = mt_rand(0, count($out_ex)-1);
		$counter = $rand;
		$out = trim($out_ex[$rand]);
	}
}
else{
	if(!empty($s_out)){
		$out = $s_out;
	}
}
if(empty($s_redirect)){
	$redirect = $g_redirect;
	$header = $g_header;
	$out = $g_out;
	$curl = $g_curl;
	$mac = '';
	$s_api_mac_prob = '';
	$s_api_mac_time = '';
}
else{
	$redirect = $s_redirect;
	$header = $s_header;
	$curl = $s_curl;
}
if($redirect == 'stop'){
	unset($out);
}
out();
exit();
function out(){
	global $redirect, $out, $key, $lang, $country, $city, $region, $device, $operator, $bot, $uniq, $path, $s_chance, $g_save_keys_se, $parameter_1, $parameter_2, $parameter_3, $parameter_4, $parameter_5, $key_se, $ipuser, $key_api, $key_api_host, $debug, $domain, $useragent, $id, $cid, $cid_delimiter, $curl_cache, $s_name, $g_name, $header, $par, $s_api_mac_exe, $s_api_mac, $s_api_mac_prob, $s_redirect, $empty;
	$mac_output = '';
	if($s_redirect == 'api' && $s_api_mac_exe == 1 && !empty($s_api_mac) && $s_api_mac_prob > mt_rand(1, 100)){
		$mac_input = $s_api_mac;
	}
	else{
		$mac_input = '';
	}
	header("Content-Type: $header; charset=UTF-8");
	$out = trim(html_entity_decode($out, ENT_QUOTES, 'UTF-8'));
	if(stristr($out, '[PATH]')){
		$out = str_ireplace('[PATH]', $path, $out);
	}
	if($redirect == 'javascript' || $redirect == 'js_selection'){
		if(empty($s_chance)){$s_chance = 100;}
		$rand = mt_rand(1, 100);
		if($s_chance < $rand){
			$redirect = 'stop';
			$out = 'chance';
		}
	}
	if(stristr($out, '[KEY]')){
		logs();
		save_keys();
		$key = urlencode($key);
		$out = str_ireplace('[KEY]', $key, $out);
	}
	else{
		logs();
		save_keys();
	}
	if($g_save_keys_se == 1){
		keys_se();
		save_keys_se();
	}
	if(!empty($par)){
		foreach($par as $v){
			$name = $v['name'];
			$value = $v['value'];
			if(stristr($out, '['.$name.']')){
				$out = str_ireplace('['.$name.']', $value, $out);
			}
		}
	}
	if(stristr($out, '[PAR-1]')){
		$out = str_ireplace('[PAR-1]', $parameter_1, $out);
	}
	if(stristr($out, '[PAR-2]')){
		$out = str_ireplace('[PAR-2]', $parameter_2, $out);
	}
	if(stristr($out, '[PAR-3]')){
		$out = str_ireplace('[PAR-3]', $parameter_3, $out);
	}
	if(stristr($out, '[PAR-4]')){
		$out = str_ireplace('[PAR-4]', $parameter_4, $out);
	}
	if(stristr($out, '[PAR-5]')){
		$out = str_ireplace('[PAR-5]', $parameter_5, $out);
	}
	if(stristr($out, '[IP]')){
		$out = str_ireplace('[IP]', $ipuser, $out);
	}
	if(stristr($out, '[COUNTRY]')){
		$out = str_ireplace('[COUNTRY]', $country, $out);
	}
	if(stristr($out, '[CITY]')){
		$out = str_ireplace('[CITY]', $city, $out);
	}
	if(stristr($out, '[LANG]')){
		$out = str_ireplace('[LANG]', $lang, $out);
	}
	if(stristr($out, '[KEY_SE]')){
		keys_se();
		$key_se = urlencode($key_se);
		$out = str_ireplace('[KEY_SE]', $key_se, $out);
	}
	if(stristr($out, '[DOMAIN]')){
		$out = str_ireplace('[DOMAIN]', $domain, $out);
	}
	if(stristr($out, '[USERAGENT]')){
		$out = str_ireplace('[USERAGENT]', $useragent, $out);
	}
	if(stristr($out, '[REGION]')){
		$out = str_ireplace('[REGION]', $region, $out);
	}
	if(stristr($out, '[DEVICE]')){
		$out = str_ireplace('[DEVICE]', $device, $out);
	}
	if(stristr($out, '[CID]')){
		$out = str_ireplace('[CID]', $id.$cid_delimiter.$cid, $out);
	}
	if(stristr($out, 'RANDNUM')){
		$out = randnum($out);
	}
	if(stristr($out, 'RANDSTR')){
		$out = randstr($out);
	}
	if(stristr($out, 'RANDLINE')){
		$out = randline($out);
	}
	if(stristr($out, 'RANDDFL')){
		$out = randdfl($out);
	}
	if(!empty($mac_input)){
		if(stristr($mac_input, 'RANDNUM')){
			$mac_input = randnum($mac_input);
		}
		if(stristr($mac_input, 'RANDSTR')){
			$mac_input = randstr($mac_input);
		}
		if(stristr($mac_input, 'RANDLINE')){
			$mac_input = randline($mac_input);
		}
		if(stristr($mac_input, 'RANDDFL')){
			$mac_input = randdfl($mac_input);
		}
		$mac_output = $mac_input;
	}
	if($redirect == 'api'){
		if($debug != 1){
			if($key_api != $key_api_host){
				exit();
			}
		}
		$array = array();
		$array[0] = $out;
		$array[1] = 0;
		$array[2] = $country;
		$array[3] = $region;
		$array[4] = $city;
		$array[5] = $device;
		$array[6] = $operator;
		$array[7] = $bot;
		$array[8] = $uniq;
		$array[9] = $lang;
		$array[10] = $mac_output;
		$api_data = serialize($array);
		echo $api_data;
		exit();
	}
	if($redirect == 'curl'){
		temp();
		if(!empty($s_name)){
			$file_name = 'curl_'.$g_name.'_'.$s_name;
		}
		else{
			$file_name = 'curl_'.$g_name;
		}
		if($curl_cache != 0){
			$st_now = strtotime("now");
			$st = strtotime("- $curl_cache minutes");
			if(!file_exists("temp/$file_name")){
				curl($out, $key, $file_name);
			}
			else{
				$data = file("temp/$file_name");
				if($data[0] > $st){
					unset($data[0]);
					$data = implode('', $data);
					echo $data;
					exit();
				}
				else{
					curl($out, $key, $file_name);
				}
			}
		}
		else{
			curl($out, $key, $file_name);
		}
	}
	if($redirect == 'http_redirect'){
		header("Location: $out");
		exit();
	}
	if($redirect == 'iframe'){
		echo 'var splashpage = {
	splashenabled: 1,
	splashpageurl: "'.$out.'",
	enablefrequency: 0,
	displayfrequency: "2 days",
	cookiename: ["splashpagecookie", "path=/"],
	autohidetimer: 0,
	launch: false,
	browserdetectstr:(window.opera && window.getSelection) || (!window.opera && window.XMLHttpRequest),
	output: function(){
		document.write(\'<style>body {overflow: hidden;}</style>\');
		document.write(\'<div id="slashpage" style="position: absolute; z-index: 10000; color: white; background-color:white">\');
		document.write(\'<iframe name="splashpage-iframe" src="about:blank" style="margin:0; border:0; padding:0; width:100%; height: 100%"></iframe>\');
		document.write(\'<br />&nbsp;</div>\');
		this.splashpageref = document.getElementById("slashpage");
		this.splashiframeref = window.frames["splashpage-iframe"];
		this.splashiframeref.location.replace(this.splashpageurl);
		this.standardbody = (document.compatMode == "CSS1Compat") ? document.documentElement : document.body;
		if(!/safari/i.test(navigator.userAgent)) this.standardbody.style.overflow = "hidden";
		this.splashpageref.style.left = 0;
		this.splashpageref.style.top = 0;
		this.splashpageref.style.width = "100%";
		this.splashpageref.style.height = "100%";
		this.moveuptimer = setInterval("window.scrollTo(0,0)", 50);
	},
	closeit: function(){
		clearInterval(this.moveuptimer);
		this.splashpageref.style.display = "none";
		this.splashiframeref.location.replace("about:blank");
		this.standardbody.style.overflow = "auto";
	},
	init: function(){
		if(this.enablefrequency == 1){
			if(/sessiononly/i.test(this.displayfrequency)){
				if(this.getCookie(this.cookiename[0] + "_s") == null){
					this.setCookie(this.cookiename[0] + "_s", "loaded");
					this.launch = true;
				}
			}
			else if(/day/i.test(this.displayfrequency)){
				if(this.getCookie(this.cookiename[0]) == null || parseInt(this.getCookie(this.cookiename[0])) != parseInt(this.displayfrequency)){
					this.setCookie(this.cookiename[0], parseInt(this.displayfrequency), parseInt(this.displayfrequency));
					this.launch = true;
				}
			}
			} else this.launch = true; if(this.launch){
				this.output();
				if(parseInt(this.autohidetimer) > 0) setTimeout("splashpage.closeit()", parseInt(this.autohidetimer) * 1000);
			}
	},
	getCookie: function(Name){
		var re = new RegExp(Name + "=[^;]+", "i");
		if(document.cookie.match(re)) return document.cookie.match(re)[0].split("=")[1];
		return null;
	},
	setCookie: function(name, value, days){
		var expireDate = new Date();
		if(typeof days != "undefined"){
			var expstring = expireDate.setDate(expireDate.getDate() + parseInt(days));
			document.cookie = name + "=" + value + "; expires=" + expireDate.toGMTString() + "; " + splashpage.cookiename[1];
		} else document.cookie = name + "=" + value + "; " + splashpage.cookiename[1];
	}
};
if(splashpage.browserdetectstr && splashpage.splashenabled == 1) splashpage.init();';
		exit();
	}
	if($redirect == 'iframe_redirect'){
		echo '<!DOCTYPE html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
<iframe src="javascript:parent.location=\''.$out.'\'" style="visibility:hidden"></iframe>
<script>
	function go() {location.replace("'.$out.'")}
	window.setTimeout("go()", 1000)
</script>
</body>
</html>';
		exit();
	}
	if($redirect == 'iframe_selection'){
		echo '<script type="text/javascript">
function process(){
	top.location = "'.$out.'";
}
window.onerror = process;
if(top.location.href != window.location.href){
	process()
}
</script>';
		exit();
	}
	if($redirect == 'js_redirect'){
		echo '<!DOCTYPE html>
<head>
<meta http-equiv="refresh" content="1; URL='.$out.'">
<script type="text/javascript">window.location = "'.$out.'";</script>
</head>
<body>
The Document has moved <a href="'.$out.'">here</a>
</body>
</html>';
		exit();
	}
	if($redirect == 'js_selection'){
		echo 'function process(){
	window.location = "'.$out.'";
}
window.onerror = process;
process()';
		exit();
	}
	if($redirect == 'javascript'){
		echo $out;
		exit();
	}
	if($redirect == 'meta_refresh'){
		echo '<!DOCTYPE html>
<head>
<meta http-equiv="refresh" content="0; URL='.$out.'">
</head>
<body>
</body>
</html>';
		exit();
	}
	if($redirect == 'show_out'){
		if($debug != 1){
			if($key_api != $key_api_host){
				exit();
			}
		}
		$array = array();
		$array[0] = $out;
		$array[1] = 1;
		$show_out_data = serialize($array);
		echo $show_out_data;
		exit();
	}
	if($redirect == 'show_page_html'){
		echo '<!DOCTYPE html>
<head>
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>'
.$out.
'</body>
</html>';
		exit();
	}
	if($redirect == 'show_text'){
		echo $out;
		exit();
	}
	if($redirect == 'stop'){
		exit();
	}
	if($redirect == 'under_construction'){
		echo '<!DOCTYPE html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex, nofollow">
<meta name="description" content="Under construction">
<title>Страница в разработке</title>
</head>
<body>
<br>
<center><img src="http://'.$path.'/files/img/404.png" border=0></center>
</body>
</html>';
		exit();
	}
	if($redirect == '403_forbidden'){
		header('HTTP/1.0 403 Forbidden', true, 403);
		echo '<!DOCTYPE html>
<head>
<title>Access forbidden!</title>
</head>
<body>
<h1>Access forbidden!</h1>
<p>
You don\'t have permission to access the requested object. It is either read-protected or not readable by the server.
<br>
If you think this is a server error, please contact the <a href="mailto:[no address given]">webmaster</a>.
</p>
<h2>Error 403</h2>
</body>
</html>';
		exit();
	}
	if($redirect == '404_not_found'){
		header('HTTP/1.0 404 Not Found', true, 404);
		echo '<!DOCTYPE html>
<head>
<title>Object not found!</title>
</head>
<body>
<h1>Object not found!</h1>
<h2>Error 404</h2>
</body>
</html>';
		exit();
	}
	if($redirect == '500_server_error'){
		header('HTTP/1.0 500 Internal Server Error', true, 500);
		echo '<!DOCTYPE html>
<head>
<title>Server error!</title>
</head>
<body>
<h1>Server error!</h1>
<p>
The server encountered an internal error and was unable to complete your request. Either the server is overloaded or there was an error in a CGI script.
</p>
<h2>Error 500</h2>
</body>
</html>';
		exit();
	}
}
function search_in_database($net, $ip){
	global $label;
	$label = '';
	$ip = trim($ip);
	for($i = 0; $i<count($net); $i++){
		if(trim($net[$i][0]) == '#'){
			$label = trim(substr($net[$i], 1));
		}
		if(trim($net[$i]) != false && trim($net[$i][0]) != '#'){
			$net[$i] = trim($net[$i]);
			if($ip == $net[$i]){
				return true;
			}
		}
		if(trim($net[$i]) != false && trim($net[$i][0]) != '#' && strstr($net[$i], '/')){
			$net_ex = explode('/', $net[$i]);
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($net_ex[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
				$range_start = ip2long($net_ex[0]);
				$range_end = $range_start + pow(2, 32-intval($net_ex[1])) - 1;
				$ip_long = ip2long($ip);
				if($ip_long >=$range_start && $ip_long <= $range_end){
					return true;
				}
			}
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && filter_var($net_ex[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
				$ip_bin = inet_pton($ip);
				list($first_addr_str, $prefix_len) = $net_ex;
				$first_addr_bin = inet_pton($first_addr_str);
				$first_addr_hex = unpack('H*', $first_addr_bin);
				$first_addr_hex = reset($first_addr_hex);
				$flex_bits = 128 - $prefix_len;
				$last_addr_hex = $first_addr_hex;
				$n_pos = 31;
				while($flex_bits > 0){
					$orig = substr($last_addr_hex, $n_pos, 1);
					$orig_val = hexdec($orig);
					$new_val = $orig_val | (pow(2, min(4, $flex_bits)) - 1);
					$new = dechex($new_val);
					$last_addr_hex = substr_replace($last_addr_hex, $new, $n_pos, 1);
					$flex_bits -= 4;
					$n_pos -= 1;
				}
				$last_addr_bin = pack('H*', $last_addr_hex);
				if($ip_bin >= $first_addr_bin && $ip_bin <= $last_addr_bin){
					return true;
				}
			}
		}
		if(trim($net[$i]) != false && trim($net[$i][0]) != '#' && strstr($net[$i], '-')){
			$net_ex = explode('-', $net[$i]);
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var(trim($net_ex[0]), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
				if(ip2long($ip) >= ip2long(trim($net_ex[0])) && ip2long($ip) <= ip2long(trim($net_ex[1]))){
					return true;
				}
			}
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && filter_var(trim($net_ex[0]), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
				$ip_bin = inet_pton($ip);
				$first_addr_bin = inet_pton(trim($net_ex[0]));
				$last_addr_bin = inet_pton(trim($net_ex[1]));
				if($ip_bin >= $first_addr_bin && $ip_bin <= $last_addr_bin){
					return true;
				}
			}
		}
	}
	$label = '';
}
function logs(){
	global $id, $g_name, $s_name, $folder_log, $out, $key, $redirect, $device, $operator, $country, $city, $region, $lang, $uniq, $bot, $ipuser, $referer, $useragent, $domain, $page, $se, $period_log, $db, $empty, $log_bots, $log_out, $log_ref, $log_key, $log_ua, $log_page, $counter, $timeout_1, $postback, $cid, $cid_length, $index_mode;
	if($log_bots != 1 && $bot != $empty){
		return;
	}
	$time = date("H:i:s");
	$strtotime = strtotime("now");
	$table = strtotime(date('Y-m-d'));
	if(!$db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table';")){
		$db->querySingle("PRAGMA encoding = 'UTF-8'; PRAGMA journal_mode = WAL; CREATE TABLE '$table' (id INTEGER PRIMARY KEY, time TEXT, ngroup TEXT, nstream TEXT, out TEXT, keyword TEXT, redirect TEXT, device TEXT, operator TEXT, country TEXT, city TEXT, region TEXT, lang TEXT, uniq TEXT, bot TEXT, ipuser TEXT, referer TEXT, useragent TEXT, domain TEXT, page TEXT, se TEXT, strtotime INTEGER, counter TEXT, cid TEXT, postback TEXT);");
		if($index_mode == 1 || $index_mode == 2){
			$db->exec("CREATE INDEX '$table"."_uniq' ON '$table' (ipuser,uniq,strtotime);");
			$db->exec("CREATE INDEX '$table"."_limit' ON '$table' (nstream,strtotime);");
		}
		if($index_mode == 2){
			$db->exec("CREATE INDEX '$table"."_country' ON '$table' (country,nstream,bot,domain,se,uniq);");
			$db->exec("CREATE INDEX '$table"."_sources' ON '$table' (domain,nstream,bot,device,operator,se,uniq);");
			$db->exec("CREATE INDEX '$table"."_group' ON '$table' (uniq,bot,se,nstream,device,operator);");
		}
	}
	$g_name_wr = SQLite3::escapeString($g_name);
	$s_name_wr = SQLite3::escapeString($s_name);
	$out_wr = SQLite3::escapeString(htmlentities($out, ENT_QUOTES, 'UTF-8'));
	$key_wr = SQLite3::escapeString($key);
	$city_wr = SQLite3::escapeString($city);
	$region_wr = SQLite3::escapeString($region);
	$lang_wr = SQLite3::escapeString($lang);
	$ipuser_wr = SQLite3::escapeString($ipuser);
	$referer_wr = SQLite3::escapeString($referer);
	$useragent_wr = SQLite3::escapeString($useragent);
	$domain_wr = SQLite3::escapeString($domain);
	$page_wr = SQLite3::escapeString($page);
	$operator_wr = $operator;
	$postback_wr = $postback;
	$cid = substr(md5(microtime(1)), 0, $cid_length);
	if(empty($s_name_wr)){$s_name_wr = $empty;}
	if(empty($key_wr)){$key_wr = $empty;}
	if(empty($out_wr)){$out_wr = $empty;}
	if(empty($postback_wr)){$postback_wr = $empty;}
	if(!empty($log_out)){
		$log_out = explode(",", $log_out);
		$x = 0;
		foreach($log_out as $v){
			if($redirect == trim($v)){
				$out_wr = $empty;
				break;
			}
		}
	}
	if($log_ref != 1){
		$referer_wr = $empty;
	}
	if($log_ua != 1){
		$useragent_wr = $empty;
	}
	if($log_key != 1){
		$key_wr = $empty;
	}
	if($log_page != 1){
		$page_wr = $empty;
	}
	$db->querySingle("INSERT INTO '$table' (time, ngroup, nstream, out, keyword, redirect, device, operator, country, city, region, lang, uniq, bot, ipuser, referer, useragent, domain, page, se, strtotime, counter, cid, postback) VALUES ('$time', '$g_name_wr', '$s_name_wr', '$out_wr', '$key_wr', '$redirect', '$device', '$operator', '$country', '$city_wr', '$region_wr', '$lang_wr', '$uniq', '$bot', '$ipuser_wr', '$referer_wr', '$useragent_wr', '$domain_wr', '$page_wr', '$se', '$strtotime', '$counter', '$cid', '$postback_wr')");
	$db->querySingle("COMMIT;");
	$db->close();
}
function utf8_bad_find($str){
	$utf8_bad =
	'([\x00-\x7F]'.
	'|[\xC2-\xDF][\x80-\xBF]'.
	'|\xE0[\xA0-\xBF][\x80-\xBF]'.
	'|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}'.
	'|\xED[\x80-\x9F][\x80-\xBF]'.
	'|\xF0[\x90-\xBF][\x80-\xBF]{2}'.
	'|[\xF1-\xF3][\x80-\xBF]{3}'.
	'|\xF4[\x80-\x8F][\x80-\xBF]{2}'.
	'|(.{1}))';
	$pos = 0;
	while(preg_match('/'.$utf8_bad.'/S', $str, $matches)){
		$bytes = strlen($matches[0]);
		if(isset($matches[2]))
			return $pos;
		$pos += $bytes;
		$str = substr($str, $bytes);
	}
	return false;
}
function trash(){
	global $trash_url, $trash_mode;
	if(empty($trash_url) || $trash_mode == 0){
		exit();
	}
	if($trash_mode == 1){
		header("Location: $trash_url");
		exit();
	}
	if($trash_mode == 2){
		header('HTTP/1.0 403 Forbidden', true, 403);
		exit();
	}
	if($trash_mode == 3){
		header('HTTP/1.0 404 Not Found', true, 404);
		exit();
	}
}
function sep_str(){
	global $sep_str, $sep_data;
	$sep_str = trim(array_shift($sep_data));
	if(!empty($sep_str)){
		implode("\n", $sep_data);
	}
	else{
		$sep_str = 'end';
	}
}
function s_separation(){
	global $s_out, $s_separation_file, $key, $sep_data, $sep_str;
	upper_replace();
	$sep_data = file_get_contents('database/'.$s_separation_file);
	$sep_data = explode("\n", $sep_data);
	while($sep_str != 'end'){
		sep_str();
		if($sep_str != 'end'){
			$x = explode(";", $sep_str);
			$sep_name = $x[0];
			if(stristr($key, $sep_name)){
				$s_out = $x[1];
				break;
			}
		}
	}
}
function save_keys(){
	global $g_save_keys, $bot, $key, $folder_keys, $g_name, $empty;
	if($g_save_keys == 1 && $bot == $empty && !empty($key)){
		$date = date("Y-m-d");
		if(!file_exists($folder_keys)){
			mkdir($folder_keys, 0755);
		}
		if(!file_exists($folder_keys.'/'.$g_name)){
			mkdir($folder_keys.'/'.$g_name, 0755);
			file_put_contents($folder_keys.'/'.$g_name.'/'.'.htaccess', "<Files *.dat>\nDeny from all\n</Files>", LOCK_EX);
		}
		file_put_contents($folder_keys.'/'.$g_name.'/'.$date.'.dat', $key."\n", FILE_APPEND | LOCK_EX);
	}
}
function keys_se(){
	global $bot, $referer, $empty, $key_se;
	if($bot == $empty && !empty($referer)){
		$key_se = '';
		if(stristr($referer, 'google') || stristr($referer, 'yandex') || stristr($referer, 'mail.ru') || stristr($referer, 'rambler.ru') || stristr($referer, 'tut.by') || stristr($referer, 'nigma.ru')){
			$query = '';
			if(stristr($referer, 'google') && !stristr($referer, '&q=&') && !stristr($referer, '?q=&')){$query = 'q';}
			if(stristr($referer, 'mail.ru') && !stristr($referer, '&q=&') && !stristr($referer, '?q=&')){$query = 'q';}
			if(stristr($referer, 'rambler.ru') && !stristr($referer, '&query=&') && !stristr($referer, '?query=&')){$query = 'query';}
			if(stristr($referer, 'tut.by') && !stristr($referer, '&query=&') && !stristr($referer, '?query=&')){$query = 'query';}
			if(stristr($referer, 'yandex') && !stristr($referer, '&text=&') && !stristr($referer, '?text=&')){$query = 'text';}
			if(stristr($referer, 'nigma.ru') && !stristr($referer, '&s=&') && !stristr($referer, '?s=&')){$query = 's';}
			if(preg_match("~^.*[?&]$query=(.+?)&.*$~", $referer, $matches)){
				$key_se = trim(urldecode($matches[1]));
			}
			else{
				if(preg_match("~^.*[?&]$query=(.*)$~", $referer, $matches)){
					$key_se = trim(urldecode($matches[1]));
				}
			}
			if(!empty($key_se)){
				if(utf8_bad_find($key_se) !== false){
					$key_se = iconv('windows-1251', 'utf-8', $key_se);
				}
			}
		}
	}
}
function save_keys_se(){
	global $bot, $folder_keys, $g_name, $empty, $key_se;
	if($bot == $empty && !empty($key_se)){
		$date = date("Y-m-d");
		if(!file_exists($folder_keys)){
			mkdir($folder_keys, 0755);
		}
		if(!file_exists($folder_keys.'/'.$g_name)){
			mkdir($folder_keys.'/'.$g_name, 0755);
			file_put_contents($folder_keys.'/'.$g_name.'/'.'.htaccess', "<Files *.dat>\nDeny from all\n</Files>", LOCK_EX);
		}
		file_put_contents($folder_keys.'/'.$g_name.'/'.$date.'-se.dat', $key_se."\n", FILE_APPEND | LOCK_EX);
	}
}
function upper_replace(){
	global $key;
	$key_array = array($key);
	$search = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я');
	$replace = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я');
	$res = str_ireplace($search, $replace, $key_array);
	$key = $res[0];
}
function remote_pars(){
	global $s_out, $s_remote_url, $s_remote_regexp, $s_remote_reserved_out, $s_remote_cache, $st_now, $g_name, $s_name;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_URL, $s_remote_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$pars = trim(curl_exec($ch));
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200){
		if(!empty($s_remote_regexp) && substr($s_remote_regexp, 0, 1) == '/'){
			$s_remote_regexp = trim(html_entity_decode($s_remote_regexp, ENT_QUOTES, 'UTF-8'));
			if(preg_match($s_remote_regexp, $pars, $matches)){
				if(!empty($matches[1])){
					$pars_res = $matches[1];
				}
			}
			else{
				$pars_res = '';
			}
		}
		else{
			$pars_res = $pars;
		}
		if(empty($pars_res)){
			$pars_res = $s_remote_reserved_out;
		}
		if($s_remote_cache != 0){
			$dat = $st_now.';'.$pars_res;
			file_put_contents('temp/remote_'.$g_name.'_'.$s_name, $dat, LOCK_EX);
		}
		$s_out = str_ireplace('[REMOTE]', $pars_res, $s_out);
	}
	else{
		$s_out = $s_remote_reserved_out;
		if($s_remote_cache != 0){
			$dat = $st_now.';'.$s_out;
			file_put_contents('temp/remote_'.$g_name.'_'.$s_name, $dat, LOCK_EX);
		}
	}
	curl_close($ch);
}
function err_404(){
	header('HTTP/1.0 404 Not Found', true, 404);
	exit();
}
function curl($out, $key, $file_name){
	global $curl_ua, $curl, $curl_cache;
	$ch = curl_init($out);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $curl_ua);
	$data = curl_exec($ch);
	curl_close($ch);
	if(utf8_bad_find($data) !== false){
		$data = iconv('windows-1251', 'utf-8', $data);
	}
	$curl_ex = explode("\n", $curl);
	foreach($curl_ex as $v){
		if(stristr($v, '|||')){
			$str = explode("|||", $v);
			$find = trim(html_entity_decode($str[0], ENT_QUOTES, 'UTF-8'));
			$replace = trim(html_entity_decode($str[1], ENT_QUOTES, 'UTF-8'));
			if(stristr($replace, '[KEY]')){
				$replace = str_ireplace('[KEY]', $key, $replace);
			}
			$data = str_ireplace($find, $replace, $data);
		}
	}
	if($curl_cache != 0){
		$st_now = strtotime("now");
		$dat = $st_now."\n".$data;
		file_put_contents("temp/$file_name", $dat, LOCK_EX);
	}
	echo $data;
	exit();
}
function temp(){
	if(!file_exists('temp')){
		mkdir('temp', 0755);
	}
}
function curl_remote_ip_ch(){
	global $remote_ip_ch_url, $res;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_URL, $remote_ip_ch_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$res = curl_exec($ch);
	$res = trim($res);
	curl_close($ch);
}
function randline($dat){
	if(preg_match_all("~\[RANDLINE-\((.+?)\)-([0-9])*(/u)?\]~", $dat, $matches)){
		foreach($matches[1] as $f){
			if(!file_exists('database/'.$f) || !is_file('database/'.$f)){
				$k = array_search($f, $matches[1]);
				unset($matches[0][$k]);
				unset($matches[1][$k]);
				unset($matches[2][$k]);
				unset($matches[3][$k]);
				$matches[0] = array_values($matches[0]);
				$matches[1] = array_values($matches[1]);
				$matches[2] = array_values($matches[2]);
				$matches[3] = array_values($matches[3]);
				$dat = preg_replace('~\[RANDLINE-\('.$f.'\)-[0-9]*(/u)?\]~', '', $dat, 1);
			}
		}
		$x = 0;
		while(!empty($matches[1][$x])){
			$rf = file('database/'.$matches[1][$x]);
			if(count($rf) < $matches[2][$x]){
				$matches[3][$x] = '';
			}
			if(!empty($rf)){
				$rand_str = array();
				$y = 0;
				while($matches[2][$x] != $y){
					if($matches[3][$x] == '/u'){
						while(true){
							$str = trim($rf[array_rand($rf)]);
							if(!in_array($str, $rand_str)){
								$rand_str[] = $str;
								break;
							}
						}
					}
					else{
						$rand_str[] = trim($rf[array_rand($rf)]);
					}
					$y++;
				}
				$rand_str = implode(';', $rand_str);
			}
			$dat = preg_replace('~\[RANDLINE-\('.$matches[1][$x].'\)-[0-9]*(/u)?\]~', $rand_str, $dat, 1);
			$x++;
		}
	}
	return $dat;
}
function randdfl($dat){
	if(preg_match_all("~\[RANDDFL-\((.+?)\)-([0-9]*)(/u)?\]~", $dat, $matches)){
		foreach($matches[1] as $f){
			if(!file_exists('database/'.$f) || !is_dir('database/'.$f)){
				$k = array_search($f, $matches[1]);
				unset($matches[0][$k]);
				unset($matches[1][$k]);
				unset($matches[2][$k]);
				unset($matches[3][$k]);
				$matches[0] = array_values($matches[0]);
				$matches[1] = array_values($matches[1]);
				$matches[2] = array_values($matches[2]);
				$matches[3] = array_values($matches[3]);
				$dat = preg_replace('~\[RANDDFL-\('.$f.'\)-[0-9]*(/u)?\]~', '', $dat, 1);
			}
			else{
				if(!file_exists('database/'.$f.'/randdfl.dat')){
					$dir = opendir('database/'.$f);
					$list_arr = array();
					while(false !== ($file = readdir($dir))){
						if ($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'randdfl.dat') {
							$list_arr[] = $file;
						}
					}
					if(!empty($list_arr)){
						$list = implode("\n", $list_arr);
						file_put_contents('database/'.$f.'/randdfl.dat', $list, LOCK_EX);
					}
				}
			}
		}
		$x = 0;
		while(!empty($matches[1][$x])){
			$rand_str = '';
			if(file_exists('database/'.$matches[1][$x].'/randdfl.dat')){
				$list_arr = file('database/'.$matches[1][$x].'/randdfl.dat');
				if(!empty($list_arr)){
					$rf = trim($list_arr[array_rand($list_arr)]);
					if(!empty($rf)){
						$rf_arr = file('database/'.$matches[1][$x].'/'.$rf);
						if(!empty($rf_arr)){
							$rand_str = array();
							$y = 0;
							while($matches[2][$x] != $y){
								if($matches[3][$x] == '/u'){
									while(true){
										$str = trim($rf_arr[array_rand($rf_arr)]);
										if(!in_array($str, $rand_str)){
											$rand_str[] = $str;
											break;
										}
									}
								}
								else{
									$rand_str[] = trim($rf[array_rand($rf)]);
								}
								$y++;
							}
							$rand_str = implode(';', $rand_str);
						}
					}
				}
			}
			$dat = preg_replace('~\[RANDDFL-\('.$f.'\)-[0-9]*(/u)?\]~', $rand_str, $dat, 1);
			$x++;
		}
	}
	return $dat;
}
function randstr($dat){
	if(preg_match_all("~\[RANDSTR-\((.+?)\)-([0-9]*)\]~", $dat, $matches)){
		$x = 0;
		while(!empty($matches[0][$x])){
			$set = $matches[1][$x];
			$count = $matches[2][$x];
			$str_len = strlen($set) - 1;
			$rand_str = '';
			while(!empty($count)){
				$sym = $set[mt_rand(0, $str_len)];
				$rand_str = $rand_str.$sym;
				$count--;
			}
			$dat = preg_replace('~\[RANDSTR-\('.$matches[1][$x].'\)-('.$matches[2][$x].')\]~', $rand_str, $dat, 1);
			$x++;
		}
	}
	return $dat;
}
function randnum($dat){
	if(preg_match_all("~\[RANDNUM-([0-9]*)-([0-9]*)\]~", $dat, $matches)){
		$x = 0;
		while(!empty($matches[0][$x])){
			$rand_num = mt_rand($matches[1][$x], $matches[2][$x]);
			$dat = preg_replace('~\[RANDNUM-'.$matches[1][$x].'-'.$matches[2][$x].'\]~', $rand_num, $dat, 1);
			$x++;
		}
	}
	return $dat;
}
?>