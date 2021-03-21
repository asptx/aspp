<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'conf' && !empty($d)){
	$error = '';
	$z = '';
	if(!empty($s)){
		$z = '&s='.$s;
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_URL, "http://$d/$file_api?key=$key_api");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $curl_ua);
	$api_conf = curl_exec($ch);
	$curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($curl_code == 200 && !empty($api_conf)){
		$api_conf = unserialize($api_conf);
	}
	else{
		$error = '<div class="align_center red indt_10">'.$trans['error']['e1'].'</div>';
		$api_conf = array('id'=>'','cf_ip'=>'','em_referer'=>'','em_useragent'=>'','em_lang'=>'','ipv6'=>'','ptr'=>'','rd_bots'=>'','rd_se'=>'','rotator'=>'','n_cookies'=>'','t_cookies'=>'','m_cookies'=>'','connect'=>'','conf_lc'=>'','status'=>'','ip_serv_seodor'=>'','sign_ref'=>'','sign_ua'=>'');
		$l_ch_conf = 'no data';
	}
	if(!empty($api_conf['conf_lc'])){
		$ex_lch = explode(" ", $api_conf['conf_lc']);
		$l_ch_conf = $ex_lch[0].', at '.$ex_lch[1];
	}
	echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | Domain: '.$d.'</div>'.$error.'
<ul class="tab-menu clearfix indt_20">
<li style="width:25%"><a href="#tab-1" class="tab-menu--trigger">Main</a></li>
<li style="width:25%"><a href="#tab-2" class="tab-menu--trigger">Bots</a></li>
<li style="width:25%"><a href="#tab-3" class="tab-menu--trigger">Cookies</a></li>
<li style="width:25%"><a href="#tab-4" class="tab-menu--trigger">SEoDOR</a></li>
</ul>
<div class="indt_20">
<form method="post" action="'.$admin_page.'?q=conf&g='.$g_id.$z.'&d='.$d.'">
<div id="jsAccordionToTabs" class="tab-container">
<section id="tab-1" class="tab-container--section indt_3">
<a href="#tab-1" class="tab-menu-mobile" data-tab-label="Main"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><span>'.$trans['api']['a1'].'</span> <input class="i100" name="id" type="text" value="'.$api_conf['id'].'" maxlength="50"></div>
<div class="item"><span>'.$trans['api']['a15'].'</span> <select name="connect" size = "1">
<option'; if($api_conf['connect'] == '0'){echo ' selected="selected"';} echo ' value="0">file_get_contents</option>
<option'; if($api_conf['connect'] == '1'){echo ' selected="selected"';} echo ' value="1">CURL</option>
</select></div>
<div class="item">'.$trans['api']['a20'].'</div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($api_conf['rd_bots'] == 1){echo ' checked="checked"';} echo ' name="rd_bots"> <span>'.$trans['api']['a8'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($api_conf['rd_se'] == 1){echo ' checked="checked"';} echo ' name="rd_se"> <span>'.$trans['api']['a9'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($api_conf['cf_ip'] == 1){echo ' checked="checked"';} echo ' name="cf_ip"> <span>'.$trans['api']['a2'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($api_conf['rotator'] == 1){echo ' checked="checked"';} echo ' name="rotator"> <span>'.$trans['api']['a10'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($api_conf['status'] == 1){echo ' checked="checked"';} echo ' name="status"> <span>'.$trans['api']['a16'].'</span></div>
</div>
</div>
</section>
<section id="tab-2" class="tab-container--section indt_3">
<a href="#tab-2" class="tab-menu-mobile" data-tab-label="Bots"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item">'.$trans['api']['a3'].'</div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($api_conf['em_useragent'] == 1){echo ' checked="checked"';} echo ' name="em_useragent"> <span>'.$trans['api']['a4'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($api_conf['em_referer'] == 1){echo ' checked="checked"';} echo ' name="em_referer"> <span>'.$trans['api']['a5'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($api_conf['em_lang'] == 1){echo ' checked="checked"';} echo ' name="em_lang"> <span>'.$trans['api']['a6'].'</span></div>
<div class="item ind_ch">&#10551; <input class="checkbox" type="checkbox"'; if($api_conf['ipv6'] == 1){echo ' checked="checked"';} echo ' name="ipv6"> <span>'.$trans['api']['a7'].'</span></div>
<div class="item"><input class="checkbox" type="checkbox"'; if($api_conf['ptr'] == 1){echo ' checked="checked"';} echo ' name="ptr"> <span>'.$trans['api']['a24'].'</span></div>
<div class="item">'.$trans['api']['a18'].'<br>
<textarea name="sign_ref" rows="4">'.$api_conf['sign_ref'].'</textarea>
</div>
<div class="item">'.$trans['api']['a19'].'<br>
<textarea name="sign_ua" rows="4">'.$api_conf['sign_ua'].'</textarea>
</div>
</div>
</div>
</section>
<section id="tab-3" class="tab-container--section indt_3">
<a href="#tab-3" class="tab-menu-mobile" data-tab-label="Cookies"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><span>'.$trans['api']['a11'].'</span> <input class="i100" name="n_cookies" type="text" value="'.$api_conf['n_cookies'].'" maxlength="50"></div>
<div class="item"><span>'.$trans['api']['a12'].'</span> <input class="i50" name="t_cookies" type="number" value="'.$api_conf['t_cookies'].'" maxlength="50"> s.</div>
<div class="item"><span>'.$trans['api']['a13'].'</span> <select name="m_cookies" size = "1">
<option'; if($api_conf['m_cookies'] == '0'){echo ' selected="selected"';} echo ' value="0">LastAccessed</option>
<option'; if($api_conf['m_cookies'] == '1'){echo ' selected="selected"';} echo ' value="1">CreationTime</option>
</select></div>
</div>
</div>
</section>
<section id="tab-4" class="tab-container--section indt_3">
<a href="#tab-4" class="tab-menu-mobile" data-tab-label="SEoDOR"></a>
<div class="tab-container--inner">
<div class="block">
<div class="item"><span>'.$trans['api']['a17'].'</span> <input class="i100" name="ip_serv_seodor" type="text" value="'.$api_conf['ip_serv_seodor'].'" maxlength="50"></div>
</div>
</div>
</section>
</div>';
	if(empty($error)){
		echo '<div class="align_center cb">
<input class="button" type="submit" name="button" value="Submit">
</div>';
	}
	echo '</form>
</div>';
}
?>