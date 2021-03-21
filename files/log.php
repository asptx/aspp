<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'log' || $q == 'export'){
	if(empty($_GET['col'])){
		$col = 'domain';
	}
	else{
		$col = $_GET['col'];
	}
	$find = '';
	$q_stream = '';
	$q_find = '';
	if(!empty($_GET['find'])){
		$find = htmlentities($_GET['find'], ENT_QUOTES, 'UTF-8');
		$q_find = 'AND '.$col.' LIKE "%'.$find.'%"';
	}
	if(!empty($s_name)){
		$q_stream = "AND nstream = '$s_name'";
	}
	$ldata = base64_encode(serialize(array("table"=>"$table", "q_stream"=>"$q_stream", "q_find"=>"$q_find", "q_group"=>$g_id)));
	$db = new SQLite3($folder_log.'/'.$g_id.'.db');
	$db->busyTimeout($timeout_2);
	$db->exec("PRAGMA journal_mode = WAL;");
	$date = date('Y-m-d', $table);
	if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name='$table'")){
		if($q == 'log'){
			echo '<!DOCTYPE html>
<html>
<head>
<title>zTDS '.$version.'</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="files/img/favicon.ico">
<link rel="stylesheet" href="files/style.css">
<link rel="stylesheet" type="text/css" href="files/lib/datatables/datatables.min.css">
<script type="text/javascript" src="files/lib/jquery.min.js"></script>
<script type="text/javascript" src="files/lib/datatables/datatables.min.js"></script>
</head>
<body>
<div class="header">
<div class="logo align_left">zTDS '.$version.'</div>
</div>
<div class="dlog indt_10">';
			if(!empty($s_name)){
				echo '<div class="align_center bold indt_10">Group: '.$g_name.' | Stream: '.$s_name.'</div>';
			}
			else{
				echo '<div class="align_center bold indt_10">Group: '.$g_name.'</div>';
			}
			echo '<div class="align_center indt_20"><a href="'.$admin_page.'">Main</a> | <a href="javascript:location.reload();">Update</a> | <a href="javascript:history.back();">Back</a> | <a href="'.$admin_page.'?q=export&g='.$g_id.'&s='.$s.'&t='.$table.'">Export</a></div>';
			if(file_exists($folder_log.'/'.$g_id.'.db')){
				$option = '';
				$res = $db->query("SELECT * FROM sqlite_master WHERE type = 'table' ORDER BY name DESC;");
				while(true){
					if($array = $res->fetchArray(SQLITE3_ASSOC)){
						$table_temp = $array['name'];
						$date_temp = date('Y-m-d', $table_temp);
						if(!empty($date_temp)){
							if(!empty($s)){
								$z = '&s='.$s;
							}
							else{
								$z = '';
							}
							$sel = '';
							if($table == $table_temp){
								$sel = 'selected="selected" ';
							}
							$option = $option.'<option '.$sel.'value="'.$table_temp.'">'.$date_temp.'</option>';
						}
					}
					else{
						break;
					}
				}
			}
			echo '<div class="indt_10">
<form class="align_center" method="get" action="'.$admin_page.'">
<input name="q" type="hidden" value="log">
<input name="g" type="hidden" value="'.$g_id.'">';
			if(!empty($s)){
				echo '<input name="s" type="hidden" value="'.$s.'">';
			}
			echo '
<div class="indt_10">
<select class="h20" type="hidden" name="col" size = "1">
<option'; if($col == 'bot'){echo ' selected="selected"';} echo ' value="bot">Bot</option>
<option'; if($col == 'city'){echo ' selected="selected"';} echo ' value="city">City</option>
<option'; if($col == 'country'){echo ' selected="selected"';} echo ' value="country">Country</option>
<option'; if($col == 'device'){echo ' selected="selected"';} echo ' value="device">Device</option>
<option'; if($col == 'domain'){echo ' selected="selected"';} echo ' value="domain">Domain</option>
<option'; if($col == 'ipuser'){echo ' selected="selected"';} echo ' value="ipuser">IP</option>
<option'; if($col == 'keyword'){echo ' selected="selected"';} echo ' value="keyword">Keyword</option>
<option'; if($col == 'lang'){echo ' selected="selected"';} echo ' value="lang">Language</option>
<option'; if($col == 'out'){echo ' selected="selected"';} echo ' value="out">Out</option>
<option'; if($col == 'referer'){echo ' selected="selected"';} echo ' value="referer">Referer</option>
<option'; if($col == 'se'){echo ' selected="selected"';} echo ' value="se">SE</option>
<option'; if($col == 'nstream'){echo ' selected="selected"';} echo ' value="nstream">Stream</option>
<option'; if($col == 'useragent'){echo ' selected="selected"';} echo ' value="useragent">Useragent</option>
<option'; if($col == 'operator'){echo ' selected="selected"';} echo ' value="operator">WAP</option>
</select>
<input class="i150" name="find" type="text" value="'; if(!empty($find)){echo $find;} echo '" maxlength="100">
</div>
<div class="indt_10">
<select class="h20" type="hidden" name="t" size = "1">';
			echo $option;
			echo '</select>
<input class="button_small" type="submit" value="View"></div>
</form>
</div>';
			echo '<div class="indt_20">
<table id="example" class="cell-border display compact responsive" width="90%" cellspacing="0">
<thead>
<tr>';
			foreach($col_log as $name => $v){
				$v_ex = explode(':', $v);
				if($v_ex[0] == 0){$dtclass = ' class="none"';}
				if($v_ex[0] == 1){$dtclass = '';}
				$dp = 'data-priority="'.$v_ex[1].'"';
				echo "<th $dp$dtclass>$name</th>\n";
			}
			echo '</tr>
</thead>
<tfoot>
<tr>';
			foreach($col_log as $name => $v){
				echo "<th>$name</th>\n";
			}
			echo '</tr>
</tfoot>
<tbody>';
			echo '</tbody>
</table>
</div>
<script type="text/javascript" class="init">
$(document).ready(function(){
	$("#example").DataTable({
		"serverSide":true,
		"ajax":"files/sslog.php?q='.$ldata.'",
		"processing":true,
		"language":{
			"processing":"Loading...",
			"info":"Showing _START_ to _END_ of _TOTAL_ entries",
			"infoEmpty":"",
			"infoFiltered":"Total entries: _MAX_",
			"thousands":"",
			"paginate":{
				"first":"First",
				"last":"Last",
				"next":"Next",
				"previous":"Prev"
			},
		},
		"sorting":[[0, "asc"]],
		"info":true,
		"sortable":true,
		"searching":false,
		"orderMulti":true,
		"ordering":true,
		"paging":true,
		"lengthMenu":[[100, 200, 300, 500, 1000, 5000], [100, 200, 300, 500, 1000, 5000]],
		"pageLength":100,
		"dom":"<\'dt_top\'<\'fl\'l><\'fr\'i>>rt<\'dt_bottom\'p>",
		"columnDefs":[{
			className:"td_log", "targets":[2, 3, 4, 5, 15, 16, 17, 18, 19, 20],
		}],
	});
});
</script>
</div>
<div style="clear:both;"></div>
<div class="bottom">&copy; root</div>';
			if($debug == 1){
				echo '<div class="debug">'.(microtime(true) - $start).' s.</div>';
			}
			echo '</body>
</html>';
		}
		if($q == 'export'){
			if(empty($s_name)){
				$res = $db->query("SELECT * FROM '$table';");
			}
			if(!empty($s_name)){
				$res = $db->query("SELECT * FROM '$table' WHERE nstream = '$s_name';");
			}
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$date.xls");
			header("Content-Transfer-Encoding: binary");
			echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">.max_td{max-width:200px; word-wrap:break-word;}</style>
</head>
<body>
<table border="1">
<thead>
<tr>
<th>ID</th>
<th>Time</th>
<th>Group</th>
<th>Stream</th>
<th>Out</th>
<th>Key</th>
<th>Redirect</th>
<th>Device</th>
<th>WAP</th>
<th>Country</th>
<th>City</th>
<th>Region</th>
<th>Lang</th>
<th>Uniq</th>
<th>Bot</th>
<th>IP</th>
<th>Domain</th>
<th>Page</th>
<th>Referer</th>
<th>UA</th>
<th>SE</th>
<th>$</th>
</tr>
</thead>
<tbody>';
			while($array = $res->fetchArray(SQLITE3_ASSOC)){
				echo '<tr align="center">
<td>'.$array['id'].'</td>
<td>'.$array['time'].'</td>
<td class="max_td">'.$array['ngroup'].'</td>
<td class="max_td">'.$array['nstream'].'</td>
<td class="max_td">'.$array['out'].'</td>
<td class="max_td">'.htmlentities($array['keyword'], ENT_QUOTES, 'UTF-8').'</td>
<td>'.$array['redirect'].'</td>
<td>'.$array['device'].'</td>
<td>'.$array['operator'].'</td>
<td>'.$array['country'].'</td>
<td>'.$array['city'].'</td>
<td>'.$array['region'].'</td>
<td>'.$array['lang'].'</td>
<td>'.$array['uniq'].'</td>
<td>'.$array['bot'].'</td>
<td class="max_td">'.$array['ipuser'].'</td>
<td>'.htmlentities($array['domain'], ENT_QUOTES, 'UTF-8').'</td>
<td class="td_log">'.htmlentities(urldecode($array['page']), ENT_QUOTES, 'UTF-8').'</td>
<td class="max_td">'.htmlentities(urldecode($array['referer']), ENT_QUOTES, 'UTF-8').'</td>
<td class="max_td">'.htmlentities($array['useragent'], ENT_QUOTES, 'UTF-8').'</td>
<td>'.$array['se'].'</td>
<td>'.$array['postback'].'</td>
</tr>';
			}
			echo '</tbody>
</table>
</body>
</html>';
		}
	}
	$db->close();
	exit();
}
?>