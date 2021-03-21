<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'pb'){
	if(!empty($error)){
		echo '<div class="align_center red bold indt_10">'.$error.'</div>';
	}
	$db_pb = new SQLite3($folder_log.'/postback.db');
	$db_pb->busyTimeout($timeout_2);
	$db_pb->exec("PRAGMA journal_mode = WAL;");
	$total_profit = 0;
	if(empty($d)){
		echo '<div class="align_center h_auto indt_10">';
		if(!empty($s_name)){
			echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | '.$trans['stream']['s1'].': '.$s_name.'</div>';
		}
		else{
			echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.'</div>';
		}
		$width_table = 'width="90%" ';
		echo '<div class="indt_20 indb_10">
<table id="example" class="cell-border display compact responsive" '.$width_table.'cellspacing="0">
<thead>
<tr>
<th data-priority="1"></th>
<th data-priority="1">Domain</th>
<th data-priority="2">N</th>
<th data-priority="1">$</th>
</tr>
</thead>
<tfoot>
<tr>
<th data-priority="1"></th>
<th data-priority="1">Domain</th>
<th data-priority="2">N</th>
<th data-priority="1">$</th>
</tr>
</tfoot>
<tbody>';
		if(empty($s)){
			$res = $db_pb->query("SELECT * FROM 'postback' WHERE ngroup = '$g_name' AND strtotime >= '$dfs' AND strtotime <= '$dts';");
			$st = '';
		}
		else{
			$res = $db_pb->query("SELECT * FROM 'postback' WHERE ngroup = '$g_name' AND nstream = '$s_name' AND strtotime >= '$dfs' AND strtotime <= '$dts';");
			$st = "&s=$s";
		}
		$a = array();
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$domains = trim($array['domain']);
			if(!in_array($domains, $a)){
				$a[] = $array['domain'];
			}
		}
		if(!empty($a)){
			foreach($a as $v){
				$profit = 0;
				$conversions = 0;
				if(!empty($s)){
					$query = "SELECT * FROM 'postback' WHERE domain = '$v' AND ngroup = '$g_name' AND nstream = '$s_name' AND strtotime >= '$dfs' AND strtotime <= '$dts'";
				}
				else{
					$query = "SELECT * FROM 'postback' WHERE domain = '$v' AND ngroup = '$g_name' AND strtotime >= '$dfs' AND strtotime <= '$dts'";
				}
				$res = $db_pb->query("$query;");
				while($array = $res->fetchArray(SQLITE3_ASSOC)){
					if(is_numeric($array['profit'])){
						$profit = $profit + $array['profit'];
						$total_profit = $total_profit + $array['profit'];
						$conversions++;
					}
				}
				if($total_profit != 0 && $conversions != 0){
					$g_range = '';
					if($range == 12){
						$g_range = '&from='.$date_from.'&to='.$date_to;
					}
					echo '<tr align="center">
<td></td>
<td class="stdicon"><a class="sicon" title="View" target="_blank" href="//'.$v.'">&#10003;</a><a class="sicon" title="Log" href="'.$admin_page.'?q=log&g='.$g_id.$st.'&column=domain&find='.$v.'&t='.$table.'">&#9783;</a><a class="sicon" title="Postback" href="'.$admin_page.'?q=pb&range='.$range.$g_range.'&g='.$g_id.$st.'&d='.$v.'">&#36;</a><a class="sicon" href="'.$admin_page.'?q=del_pb&d='.$v.'&range='.$range.$g_range.'&g='.$g_id.$st.'" title="Delete postback" onclick="return confirm(\'Delete postback ?\') ? true : false;">&#9747;</a><a class="sicon" title="Config" href="'.$admin_page.'?q=conf&g='.$g_id.$st.'&d='.$v.'">&#9998;</a><a class="slink" href="'.$admin_page.'?q=sources&g='.$g_id.$st.'&d='.$v.'&t='.$table.'">'.$v.'</a></td>
<td>'.$conversions.'</td>
<td>'.$profit.'</td>
</tr>';
				}
			}
		}
		echo '</tbody>';
		$sort = 3;
	}
	else{
		echo '<div class="align_center h_auto indt_10">';
		if(!empty($s_name)){
			echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | '.$trans['stream']['s1'].': '.$s_name.' | Domain: '.$d.'</div>';
		}
		else{
			echo '<div class="align_center bold indt_10">'.$trans['group']['g2'].': '.$g_name.' | Domain: '.$d.'</div>';
		}
		$width_table = 'width="90%" ';
		echo '<div class="indt_20 indb_10">
<table id="example" class="cell-border display compact responsive" '.$width_table.'cellspacing="0">
<thead>
<tr>
<th data-priority="1"></th>';
		foreach($col_pb as $name => $v){
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
		foreach($col_pb as $name => $v){
			echo "<th>$name</th>\n";
		}
		echo '</tr>
</tfoot>
<tbody>';
		if(empty($s)){
			$res = $db_pb->query("SELECT * FROM 'postback' WHERE domain = '$d' AND ngroup = '$g_name' AND strtotime >= '$dfs' AND strtotime <= '$dts';");
		}
		else{
			$res = $db_pb->query("SELECT * FROM 'postback' WHERE domain = '$d' AND ngroup = '$g_name' AND nstream = '$s_name' AND strtotime >= '$dfs' AND strtotime <= '$dts';");
		}
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			if(is_numeric($array['profit'])){
				$total_profit = $total_profit + $array['profit'];
			}
			country_names($array['country']);
			echo '<tr align="center">
<td></td>
<td>'.$array['date'].'</td>
<td>'.$array['time'].'</td>
<td>'.htmlentities(urldecode($array['page']), ENT_QUOTES, 'UTF-8').'</td>
<td>'.$array['device'].'</td>
<td>'.$array['operator'].'</td>
<td title="'.$cn.'">'.$array['country'].'</td>
<td>'.$array['city'].'</td>
<td>'.$array['profit'].'</td>
</tr>';
		}
		echo '</tbody>';
		$sort = 1;
	}
	echo '</table>
</div>
<script type="text/javascript" class="init">
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
	$db_pb->close();
}
?>