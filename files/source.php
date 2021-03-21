<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$se_label = 0;
if($q == 'sources' && empty($d)){
	$db = new SQLite3($folder_log.'/'.$g_id.'.db');
	$db->busyTimeout($timeout_2);
	$db->exec("PRAGMA journal_mode = WAL;");
	$date = date('Y-m-d', $table);
	if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table'")){
		if(!empty($s_name)){
			echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | '.$trans['stream']['s1'].': '.$s_name.'</div>';
		}
		else{
			echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.'</div>';
		}
		echo '<div class="align_center h_auto indt_20 indb_10">';
		$width_table = 'width="100%" ';
		echo '<table id="example" class="cell-border display compact responsive" '.$width_table.'cellspacing="0">
<thead>
<tr>
<th></th>';
		foreach($col_source as $name => $v){
			$v_ex = explode(':', $v);
			if($v_ex[0] == 0){$dtclass = ' class="none"';}
			if($v_ex[0] == 1){$dtclass = '';}
			$dp = 'data-priority="'.$v_ex[1].'"';
			echo "<th $dp$dtclass>$name</th>\n";
		}
		echo '</tr>
</thead>
<tfoot>
<tr>
<th></th>';
		foreach($col_source as $name => $v){
			echo "<th>$name</th>\n";
		}
		echo '</tr>
</tfoot>
<tbody>';
		if(empty($s)){
			$res = $db->query("SELECT * FROM '$table' WHERE bot = '$empty';");
			$st = '';
		}
		else{
			$res = $db->query("SELECT * FROM '$table' WHERE nstream = '$s_name' AND bot = '$empty';");
			$st = "&s=$s";
		}
		$a = array();
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$domain = trim($array['domain']);
			if(!in_array($domain, $a)){
				$a[] = $array['domain'];
			}
		}
		if(!empty($a)){
			asort($a);
			foreach($a as $domain){
				$sources_profit = 0;
				if(!empty($s)){
					$query = "SELECT COUNT (*) FROM '$table' WHERE domain = '$domain' AND nstream = '$s_name' AND bot = '$empty'";
				}
				else{
					$query = "SELECT COUNT (*) FROM '$table' WHERE domain = '$domain' AND bot = '$empty'";
				}
				if(!empty($s)){
					$query_pb = "SELECT * FROM '$table' WHERE domain = '$domain' AND nstream = '$s_name' AND bot = '$empty';";
				}
				else{
					$query_pb = "SELECT * FROM '$table' WHERE domain = '$domain' AND bot = '$empty';";
				}
				$res = $db->query("$query_pb;");
				while($array = $res->fetchArray(SQLITE3_ASSOC)){
					if($array['postback'] != $empty){
						$sources_profit = $sources_profit + $array['postback'];
					}
				}
				if($sources_profit == 0){
					$sources_profit = $empty;
				}
				$sources_visitors = $db->querySingle("$query;");
				if($sources_visitors == 0){
					$sources_visitors = $empty;
				}
				$sources_unique = $db->querySingle("$query AND uniq = 'yes';");
				if($sources_unique == 0){
					$sources_unique = $empty;
				}
				$sources_se = $db->querySingle("$query AND se != '$empty' AND uniq = 'yes';");
				if($sources_se == 0){
					$sources_se = $empty;
				}
				else{
					$se_label = 1;
				}
				$sources_computers = $db->querySingle("$query AND device = 'computer' AND uniq = 'yes';");
				if($sources_computers == 0){
					$sources_computers = $empty;
				}
				$sources_tablets = $db->querySingle("$query AND device = 'tablet' AND uniq = 'yes';");
				if($sources_tablets == 0){
					$sources_tablets = $empty;
				}
				$sources_phones = $db->querySingle("$query AND device = 'phone' AND uniq = 'yes';");
				if($sources_phones == 0){
					$sources_phones = $empty;
				}
				$sources_wap = $db->querySingle("$query AND operator != '$empty' AND uniq = 'yes';");
				if($sources_wap == 0){
					$sources_wap = $empty;
				}
				$td = '<td></td>
<td class="stdicon"><a class="sicon" title="View" target="_blank" href="//'.$domain.'">&#10003;</a><a class="sicon" title="Log" href="'.$admin_page.'?q=log&g='.$g_id.$st.'&col=domain&find='.$domain.'&t='.$table.'">&#9783;</a><a class="sicon" title="Postback" href="'.$admin_page.'?q=pb&range=12&from='.$date.'&to='.$date.'&g='.$g_id.$st.'&d='.$domain.'">&#36;</a><a class="sicon" title="Config" href="'.$admin_page.'?q=conf&g='.$g_id.$st.'&d='.$domain.'">&#9998;</a><a class="slink" href="'.$admin_page.'?q=sources&g='.$g_id.$st.'&d='.$domain.'&t='.$table.'">'.$domain.'</a></td>
<td>'.$sources_visitors.'</td>
<td>'.$sources_unique.'</td>
<td>'.$sources_se.'</td>
<td>'.$sources_wap.'</td>
<td>'.$sources_computers.'</td>
<td>'.$sources_tablets.'</td>
<td>'.$sources_phones.'</td>
<td>'.$sources_profit.'</td>';
				echo '<tr align="center">'.$td.'</tr>';
			}
		}
		echo '</tbody>
</table>';
	}
	$db->close();
	$sort = 3;
	if($se_label == 1){
		$sort = 4;
	}
	echo '<script type="text/javascript" class="init">
$(document).ready(function(){
	var t = $("#example").DataTable({
		"paging":true,
		"searching":false,
		"info":false,
		"ordering":true,
		"order":[['.$sort.', "desc"]],
		"lengthMenu":[[50, 100, 200, 300, 500, 1000 -1], [50, 100, 200, 300, 500, 1000, "All"]],
		"pageLength":50,
		"language":{
			"thousands":"",
			"paginate":{
				"first":"First",
				"last":"Last",
				"next":"Next",
				"previous":"Prev"
			},
		},
	},
	{
		"columnDefs":[{
			"searchable":false,
			"orderable":false,
			"targets":0
		}],
		"order":[[1, "asc"]]
	});
	t.on("order.dt search.dt", function(){
		t.column(0, {search:"applied", order:"applied"}).nodes().each(function(cell, i){
			cell.innerHTML = i+1;
		});
	}).draw();
});
</script>
</div>';
}
if($q == 'sources' && !empty($d)){
	$db = new SQLite3($folder_log.'/'.$g_id.'.db');
	$db->busyTimeout($timeout_2);
	$db->exec("PRAGMA journal_mode = WAL;");
	$dt = '';
	$lp = $period_log;
	while(true){
		$table_temp = strtotime(date("Y-m-d", strtotime('-'.$lp.' day')));
		if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table_temp';")){
			$dt = date('Y-m-d', $table_temp);
			$dt = explode("-", $dt);
			if(!empty($dt[2])){
				$dt = $dt[2];
			}
			else{
				$dt = date("d", strtotime('-'.$lp.' day'));
			}
			if(!empty($s)){
				$query = "SELECT COUNT (*) FROM '$table_temp' WHERE domain = '$d' AND nstream = '$s_name' AND bot = '$empty'";
			}
			else{
				$query = "SELECT COUNT (*) FROM '$table_temp' WHERE domain = '$d' AND bot = '$empty'";
			}
			$ch_visitors = $db->querySingle("$query;");
			$ch_hosts = $db->querySingle("$query AND uniq = 'yes';");
			$ch_wap = $db->querySingle("$query AND operator != '$empty' AND uniq = 'yes';");
			if($chart_bots == 1){
				if(!empty($s)){
					$ch_bots = $db->querySingle("SELECT COUNT (*) FROM '$table_temp' WHERE domain = '$d' AND nstream = '$s_name' AND bot != '$empty' AND uniq = 'yes';");
				}
				else{
					$ch_bots = $db->querySingle("SELECT COUNT (*) FROM '$table_temp' WHERE domain = '$d' AND bot != '$empty' AND uniq = 'yes';");
				}
			}
			else{
				$ch_bots = 0;
			}
			if(empty($dg)){
				$dg = '[\''.$dt.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
			}
			else{
				$dg = $dg.',[\''.$dt.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
			}
			$ch_google = $db->querySingle("$query AND se = 'google' AND uniq = 'yes';");
			$ch_yandex = $db->querySingle("$query AND se = 'yandex' AND uniq = 'yes';");
			$ch_mail = $db->querySingle("$query AND se = 'mail' AND uniq = 'yes';");
			$ch_yahoo = $db->querySingle("$query AND se = 'yahoo' AND uniq = 'yes';");
			$ch_bing = $db->querySingle("$query AND se = 'bing' AND uniq = 'yes';");
			if($ch_google != 0 || $ch_yandex != 0 || $ch_mail != 0 || $ch_yahoo != 0 || $ch_bing != 0){
				$se = 1;
			}
			if(empty($dg_se)){
				$dg_se = '[\''.$dt.'\','.$ch_google.','.$ch_yandex.','.$ch_mail.','.$ch_yahoo.','.$ch_bing.']';
			}
			else{
				$dg_se = $dg_se.',[\''.$dt.'\','.$ch_google.','.$ch_yandex.','.$ch_mail.','.$ch_yahoo.','.$ch_bing.']';
			}
		}
		else{
			if(empty($dg)){
				$dg = '[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0]';
			}
			else{
				$dg = $dg.',[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0]';
			}
			if(empty($dg_se)){
				$dg_se = '[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0,0]';
			}
			else{
				$dg_se = $dg_se.',[\''.date("d", strtotime('-'.$lp.' day')).'\',0,0,0,0,0]';
			}
		}
		if($lp == 0){
			break;
		}
		$lp--;
	}
	echo '<div class="align_center h_auto indt_10">';
	if(!empty($s_name)){
		echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | '.$trans['stream']['s1'].': '.$s_name.' | Domain: '.$d.'</div>';
		$se = 0;
	}
	else{
		echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | Domain: '.$d.'</div>';
	}
	echo '<div id="curve_chart" class="chart indt_20"></div>';
	if($se == 1){
		echo '<div id="curve_chart_se" class="chart indt_10"></div>';
	}
	if(empty($dg)){
		$dg = '[0,0,0,0,0]';
	}
	if(empty($dg_se)){
		$dg_se = '[0,0,0,0,0,0]';
	}
	$width_table = 'width="70%" ';
	echo '<div class="indt_20 indb_10">
<table id="example" class="cell-border display compact responsive" '.$width_table.'cellspacing="0">
<thead>
<tr>
<th></th>
<th>Country</th>
<th>Visitors</th>
<th>Unique</th>
<th>SE</th>
</tr>
</thead>
<tfoot>
<tr>
<th></th>
<th>Country</th>
<th>Visitors</th>
<th>Unique</th>
<th>SE</th>
</tr>
</tfoot>
<tbody>';
	if(empty($s)){
		$res = $db->query("SELECT * FROM '$table' WHERE bot = '$empty' AND domain = '$d'");
	}
	else{
		$res = $db->query("SELECT * FROM '$table' WHERE nstream = '$s_name' AND bot = '$empty' AND domain = '$d'");
	}
	$a = array();
	while($array = $res->fetchArray(SQLITE3_ASSOC)){
		$country = trim($array['country']);
		if(!in_array($country, $a)){
			$a[] = $array['country'];
		}
	}
	if(!empty($a)){
		asort($a);
		foreach($a as $country){
			if(!empty($s)){
				$query = "SELECT COUNT (*) FROM '$table' WHERE country = '$country' AND nstream = '$s_name' AND bot = '$empty' AND domain = '$d'";
			}
			else{
				$query = "SELECT COUNT (*) FROM '$table' WHERE country = '$country' AND bot = '$empty' AND domain = '$d'";
			}
			$countries_visitors = $db->querySingle("$query;");
			if($countries_visitors == 0){
				$countries_visitors = $empty;
			}
			$countries_uniq = $db->querySingle("$query AND uniq = 'yes';");
			if($countries_uniq == 0){
				$countries_uniq = $empty;
			}
			$countries_se = $db->querySingle("$query AND se != '$empty' AND uniq = 'yes';");
			if($countries_se == 0){
				$countries_se = $empty;
			}
			else{
				$se_label = 1;
			}
			country_names($country);
			$country = strtoupper($country);
			$td = '<td></td>
<td title="'.$cn.'">'.$country.'</td>
<td>'.$countries_visitors.'</td>
<td>'.$countries_uniq.'</td>
<td>'.$countries_se.'</td>';
			echo '<tr align="center">'.$td.'</tr>';
		}
	}
	$db->close();
	$sort = 3;
	if($se_label == 1){
		$sort = 4;
	}
	echo '</tbody>
</table>
</div>
<script type="text/javascript" class="init">
$(document).ready(function(){
	var t = $("#example").DataTable({
		"paging":true,
		"searching":false,
		"info":false,
		"ordering":true,
		"order":[['.$sort.', "desc"]],
		"lengthMenu":[[50, 100, -1], [50, 100, "All"]],
		"pageLength":50,
		"language":{
			"thousands":"",
			"paginate":{
				"first":"First",
				"last":"Last",
				"next":"Next",
				"previous":"Prev"
			},
		},
	},
	{
		"columnDefs":[{
			"searchable":false,
			"orderable":false,
			"targets":0
		}],
		"order":[[1, "asc"]]
	});
	t.on("order.dt search.dt", function(){
		t.column(0, {search:"applied", order:"applied"}).nodes().each(function(cell, i){
			cell.innerHTML = i+1;
		});
	}).draw();
});
</script>
</div>';
}
?>