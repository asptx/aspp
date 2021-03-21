<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
function st(){
	global $trans, $db, $table, $empty, $s_name, $query, $stat_header, $stats;
	$stat_hits = $db->querySingle("$query");
	$stat_hosts = $db->querySingle("$query AND uniq = 'yes';");
	$stat_se = $db->querySingle("$query AND uniq = 'yes' AND se != '$empty';");
	if($stats['Device'] == '1'){
		$stat_computer = $db->querySingle("$query AND device = 'computer' AND uniq = 'yes';");
		$stat_tablet = $db->querySingle("$query AND device = 'tablet' AND uniq = 'yes';");
		$stat_phone = $db->querySingle("$query AND device = 'phone' AND uniq = 'yes';");
	}
	if($stats['WAP'] == '1'){
		$stat_beeline = $db->querySingle("$query AND operator = 'beeline' AND uniq = 'yes';");
		$stat_megafon = $db->querySingle("$query AND operator = 'megafon' AND uniq = 'yes';");
		$stat_mts = $db->querySingle("$query AND operator = 'mts' AND uniq = 'yes';");
		$stat_tele2 = $db->querySingle("$query AND operator = 'tele2' AND uniq = 'yes';");
		$stat_azerbaijan = $db->querySingle("$query AND operator = 'azerbaijan' AND uniq = 'yes';");
		$stat_belarus = $db->querySingle("$query AND operator = 'belarus' AND uniq = 'yes';");
		$stat_kazakhstan = $db->querySingle("$query AND operator = 'kazakhstan' AND uniq = 'yes';");
		$stat_ukraine = $db->querySingle("$query AND operator = 'ukraine' AND uniq = 'yes';");
		$stat_wap_1 = $db->querySingle("$query AND operator = 'wap-1' AND uniq = 'yes';");
		$stat_wap_2 = $db->querySingle("$query AND operator = 'wap-2' AND uniq = 'yes';");
		$stat_wap_3 = $db->querySingle("$query AND operator = 'wap-3' AND uniq = 'yes';");
	}
	echo '<div class="pl_5 indt_10">
<b>'.$stat_header.'</b><br>
'.$trans['right_menu']['rm2'].': '.$stat_hits.'<br>
'.$trans['right_menu']['rm3'].': '.$stat_hosts.'<br>
'.$trans['right_menu']['rm15'].': '.$stat_se.'<br>';
	if($stats['Device'] == '1'){
		echo '<b>'.$trans['right_menu']['rm5'].'</b><br>
'.$trans['right_menu']['rm6'].': '.$stat_computer.'<br>
'.$trans['right_menu']['rm7'].': '.$stat_tablet.'<br>
'.$trans['right_menu']['rm8'].': '.$stat_phone.'<br>';
	}
	if($stats['WAP'] == '1'){
		echo '<b>'.$trans['right_menu']['rm9'].'</b><br>
'.$trans['stream']['s13'].': '.$stat_beeline.'<br>
'.$trans['stream']['s14'].': '.$stat_megafon.'<br>
'.$trans['stream']['s15'].': '.$stat_mts.'<br>
'.$trans['stream']['s16'].': '.$stat_tele2.'<br>
'.$trans['stream']['s17'].': '.$stat_azerbaijan.'<br>
'.$trans['stream']['s43'].': '.$stat_belarus.'<br>
'.$trans['stream']['s44'].': '.$stat_kazakhstan.'<br>
'.$trans['stream']['s46'].': '.$stat_ukraine.'<br>
'.$trans['stream']['s18'].': '.$stat_wap_1.'<br>
'.$trans['stream']['s22'].': '.$stat_wap_2.'<br>
'.$trans['stream']['s27'].': '.$stat_wap_3.'<br>';
	}
	echo '</div>';
}
function country_names($coutry_code){
	global $cn;
	$country_names = array(
	"af"=>"Afghanistan",
	"ax"=>"Åland Islands",
	"al"=>"Albania",
	"dz"=>"Algeria",
	"as"=>"American Samoa",
	"ad"=>"Andorra",
	"ao"=>"Angola",
	"ai"=>"Anguilla",
	"aq"=>"Antarctica",
	"ag"=>"Antigua and Barbuda",
	"ar"=>"Argentina",
	"am"=>"Armenia",
	"aw"=>"Aruba",
	"au"=>"Australia",
	"at"=>"Austria",
	"az"=>"Azerbaijan",
	"bs"=>"Bahamas",
	"bh"=>"Bahrain",
	"bd"=>"Bangladesh",
	"bb"=>"Barbados",
	"by"=>"Belarus",
	"be"=>"Belgium",
	"bz"=>"Belize",
	"bj"=>"Benin",
	"bm"=>"Bermuda",
	"bt"=>"Bhutan",
	"bo"=>"Bolivia",
	"bq"=>"Caribbean Netherlands Bonaire",
	"ba"=>"Bosnia and Herzegovina",
	"bw"=>"Botswana",
	"bv"=>"Bouvet Island",
	"br"=>"Brazil",
	"io"=>"British Indian Ocean Territory",
	"bn"=>"Brunei",
	"bg"=>"Bulgaria",
	"bf"=>"Burkina Faso",
	"bi"=>"Burundi",
	"cv"=>"Cape Verde",
	"kh"=>"Cambodia",
	"cm"=>"Cameroon",
	"ca"=>"Canada",
	"ky"=>"Cayman Islands",
	"cf"=>"Central African Republic",
	"td"=>"Chad",
	"cl"=>"Chile",
	"cn"=>"China",
	"cx"=>"Christmas Island",
	"cc"=>"Cocos (Keeling) Islands",
	"co"=>"Colombia",
	"km"=>"Comoros",
	"cg"=>"Congo",
	"cd"=>"Democratic Republic of the Congo",
	"ck"=>"Cook Islands",
	"cr"=>"Costa Rica",
	"ci"=>"Cote d'Ivoire",
	"hr"=>"Croatia",
	"cu"=>"Cuba",
	"cw"=>"Curaçao",
	"cy"=>"Cyprus",
	"cz"=>"Czechia",
	"dk"=>"Denmark",
	"dj"=>"Djibouti",
	"dm"=>"Dominica",
	"do"=>"Dominican Republic",
	"ec"=>"Ecuador",
	"eg"=>"Egypt",
	"sv"=>"El Salvador",
	"gq"=>"Equatorial Guinea",
	"er"=>"Eritrea",
	"ee"=>"Estonia",
	"et"=>"Ethiopia",
	"fk"=>"Falkland Islands",
	"fo"=>"Faroe Islands",
	"fj"=>"Fiji",
	"fi"=>"Finland",
	"fr"=>"France",
	"gf"=>"French Guiana",
	"pf"=>"French Polynesia",
	"tf"=>"French Southern Territories",
	"ga"=>"Gabon",
	"gm"=>"Gambia",
	"ge"=>"Georgia",
	"de"=>"Germany",
	"gh"=>"Ghana",
	"gi"=>"Gibraltar",
	"gr"=>"Greece",
	"gl"=>"Greenland",
	"gd"=>"Grenada",
	"gp"=>"Guadeloupe",
	"gu"=>"Guam",
	"gt"=>"Guatemala",
	"gg"=>"Guernsey",
	"gn"=>"Guinea",
	"gw"=>"Guinea-Bissau",
	"gy"=>"Guyana",
	"ht"=>"Haiti",
	"hm"=>"Heard Island",
	"va"=>"Vatican",
	"hn"=>"Honduras",
	"hk"=>"Hong Kong",
	"hu"=>"Hungary",
	"is"=>"Iceland",
	"in"=>"India",
	"id"=>"Indonesia",
	"ir"=>"Iran",
	"iq"=>"Iraq",
	"ie"=>"Ireland",
	"im"=>"Isle of Man",
	"il"=>"Israel",
	"it"=>"Italy",
	"jm"=>"Jamaica",
	"jp"=>"Japan",
	"je"=>"Jersey",
	"jo"=>"Jordan",
	"kz"=>"Kazakhstan",
	"ke"=>"Kenya",
	"ki"=>"Kiribat",
	"kp"=>"North Korea",
	"kr"=>"South Korea",
	"kw"=>"Kuwait",
	"kg"=>"Kyrgyzstan",
	"la"=>"Laos",
	"lv"=>"Latvia",
	"lb"=>"Lebanon",
	"ls"=>"Lesotho",
	"lr"=>"Liberia",
	"ly"=>"Libya",
	"li"=>"Liechtenstein",
	"lt"=>"Lithuania",
	"lu"=>"Luxembourg",
	"mo"=>"Macau",
	"mk"=>"Macedonia",
	"mg"=>"Madagascar",
	"mw"=>"Malawi",
	"my"=>"Malaysia",
	"mv"=>"Maldives",
	"ml"=>"Mali",
	"mt"=>"Malta",
	"mh"=>"Marshall Islands",
	"mq"=>"Martinique",
	"mr"=>"Mauritania",
	"mu"=>"Mauritius",
	"yt"=>"Mayotte",
	"mx"=>"Mexico",
	"fm"=>"Micronesia",
	"md"=>"Moldova",
	"mc"=>"Monaco",
	"mn"=>"Mongolia",
	"me"=>"Montenegro",
	"ms"=>"Montserrat",
	"ma"=>"Morocco",
	"mz"=>"Mozambique",
	"mm"=>"Myanmar",
	"na"=>"Namibia",
	"nr"=>"Nauru",
	"np"=>"Nepal",
	"nl"=>"Netherlands",
	"nc"=>"New Caledonia",
	"nz"=>"New Zealand",
	"ni"=>"Nicaragua",
	"ne"=>"Niger",
	"ng"=>"Nigeria",
	"nu"=>"Niue",
	"nf"=>"Norfolk Island",
	"mp"=>"Northern Mariana Islands",
	"no"=>"Norway",
	"om"=>"Oman",
	"pk"=>"Pakistan",
	"pw"=>"Palau",
	"ps"=>"Palestine",
	"pa"=>"Panama",
	"pg"=>"Papua New Guinea",
	"py"=>"Paraguay",
	"pe"=>"Peru",
	"ph"=>"Philippines",
	"pn"=>"Pitcairn",
	"pl"=>"Poland",
	"pt"=>"Portugal",
	"pr"=>"Puerto Rico",
	"qa"=>"Qatar",
	"re"=>"Reunion",
	"ro"=>"Romania",
	"ru"=>"Russia",
	"rw"=>"Rwanda",
	"bl"=>"Saint Barthélemy",
	"sh"=>"Saint Helena",
	"kn"=>"Saint Kitts and Nevis",
	"lc"=>"Saint Lucia",
	"mf"=>"Saint Martin",
	"pm"=>"Saint Pierre and Miquelon",
	"vc"=>"Saint Vincent and the Grenadines",
	"ws"=>"Samoa",
	"sm"=>"San Marino",
	"st"=>"Sao Tome and Principe",
	"sa"=>"Saudi Arabia",
	"sn"=>"Senegal",
	"rs"=>"Serbia",
	"sc"=>"Seychelles",
	"sl"=>"Sierra Leone",
	"sg"=>"Singapore",
	"sx"=>"Sint Maarte",
	"sk"=>"Slovakia",
	"si"=>"Slovenia",
	"sb"=>"Solomon Islands",
	"so"=>"Somalia",
	"za"=>"South Africa",
	"gs"=>"South Georgia",
	"ss"=>"South Sudan",
	"es"=>"Spain",
	"lk"=>"Sri Lanka",
	"sd"=>"Sudan",
	"sr"=>"Suriname",
	"sj"=>"Svalbard and Jan Mayen",
	"sz"=>"Swaziland",
	"se"=>"Sweden",
	"ch"=>"Switzerland",
	"sy"=>"Syria",
	"tw"=>"Taiwan",
	"tj"=>"Tajikistan",
	"tz"=>"Tanzania",
	"th"=>"Thailand",
	"tl"=>"Timor-Leste",
	"tg"=>"Togo",
	"tk"=>"Tokelau",
	"to"=>"Tonga",
	"tt"=>"Trinidad and Tobago",
	"tn"=>"Tunisia",
	"tr"=>"Turkey",
	"tm"=>"Turkmenistan",
	"tc"=>"Turks and Caicos Islands",
	"tv"=>"Tuvalu",
	"ug"=>"Uganda",
	"ua"=>"Ukraine",
	"ae"=>"United Arab Emirates",
	"gb"=>"United Kingdom",
	"us"=>"United States of America",
	"um"=>"United States Minor Outlying Islands",
	"uy"=>"Uruguay",
	"uz"=>"Uzbekistan",
	"vu"=>"Vanuatu",
	"ve"=>"Venezuela",
	"vn"=>"Vietnam",
	"vg"=>"Virgin Islands (British)",
	"vi"=>"Virgin Islands (U.S.)",
	"wf"=>"Wallis and Futuna",
	"eh"=>"Western Sahara",
	"ye"=>"Yemen",
	"zm"=>"Zambia",
	"zw"=>"Zimbabwe",
	"xk"=>"Kosovo",
	"-"=>"unknown");
	if(!empty($country_names[$coutry_code])){
		$cn = $country_names[$coutry_code];
	}
	else{
		$cn = strtoupper($coutry_code);
	}
}
function clear_cache(){
	global $file_totp;
	if(file_exists('temp')){
		$objects = scandir('temp');
		foreach($objects as $object){
			if($object != "." && $object != ".." && $object != ".htaccess" && $object != "webcrawlers.dat" && $object != $file_totp){
				if(is_file('temp/'.$object)){
					unlink('temp/'.$object);
				}
			}
		}
	}
}
function check_num($num, $max){
	if($max > 0 && is_numeric($num) && $num > 0 && $num <= $max){
		return true;
	}
	if($max == 0 && is_numeric($num) && $num > 0){
		return true;
	}
}
?>