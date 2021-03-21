<?php
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
define("INDEX", "yes");
require_once '../config.php';
@ini_set('memory_limit', '-1');
$dt_arr = array(
"0" => "id",
"1" => "time",
"2" => "ngroup",
"3" => "nstream",
"4" => "out",
"5" => "keyword",
"6" => "redirect",
"7" => "device",
"8" => "operator",
"9" => "country",
"10" => "city",
"11" => "region",
"12" => "lang",
"13" => "uniq",
"14" => "bot",
"15" => "ipuser",
"16" => "domain",
"17" => "page",
"18" => "referer",
"19" => "useragent",
"20" => "se",
"21" => "postback",
);
$length = $_GET['length'];
$start = $_GET['start'];
$offset = $start;
$order_column = $_GET['order'][0]['column'];
$order_dir = $_GET['order'][0]['dir'];
$order_name = $dt_arr[$order_column];
$data = unserialize(base64_decode($_GET['q']));
$table = $data['table'];
$q_group = $data['q_group'];
$q_stream = $data['q_stream'];
$q_find = $data['q_find'];
$db = new SQLite3('../'.$folder_log.'/'.$q_group.'.db');
$db->busyTimeout(60);
$db->exec("PRAGMA journal_mode = WAL;");
$count = $db->querySingle("SELECT COUNT(*) FROM '$table' WHERE id != 0 $q_stream $q_find;");
$data = array();
if(!empty($count)){
	$query = "SELECT * FROM '$table' WHERE id != 0 $q_stream $q_find ORDER BY $order_name $order_dir LIMIT $length OFFSET $offset;";
	$res = $db->query($query);
	$data['recordsTotal'] = $count;
	$data['recordsFiltered'] = $count;
	while($array = $res->fetchArray(SQLITE3_ASSOC)){
		$data['data'][] = array(
		$array['id'],
		$array['time'],
		htmlentities($array['ngroup'], ENT_QUOTES, 'UTF-8'),
		htmlentities($array['nstream'], ENT_QUOTES, 'UTF-8'),
		htmlentities($array['out'], ENT_QUOTES, 'UTF-8'),
		htmlentities($array['keyword'], ENT_QUOTES, 'UTF-8'),
		$array['redirect'],
		$array['device'],
		$array['operator'],
		htmlentities($array['country'], ENT_QUOTES, 'UTF-8'),
		htmlentities($array['city'], ENT_QUOTES, 'UTF-8'),
		htmlentities($array['region'], ENT_QUOTES, 'UTF-8'),
		htmlentities($array['lang'], ENT_QUOTES, 'UTF-8'),
		$array['uniq'],
		$array['bot'],
		htmlentities($array['ipuser'], ENT_QUOTES, 'UTF-8'),
		htmlentities($array['domain'], ENT_QUOTES, 'UTF-8'),
		htmlentities(urldecode($array['page']), ENT_QUOTES, 'UTF-8'),
		htmlentities(urldecode($array['referer']), ENT_QUOTES, 'UTF-8'),
		htmlentities($array['useragent'], ENT_QUOTES, 'UTF-8'),
		$array['se'],
		htmlentities($array['postback'], ENT_QUOTES, 'UTF-8'),
		);
	}
}
else{
	$data = array('data'=>'');
	$data['recordsTotal'][] = 0;
	$data['recordsFiltered'] = 0;
}
echo json_encode($data);
$db->close();
?>