<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'countries'){
	$se_label = 0;
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
		$width_table = 'width="70%" ';
		echo '<div class="align_center h_auto indt_20 indb_10">
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
			$res = $db->query("SELECT * FROM '$table' WHERE bot = '$empty'");
		}
		else{
			$res = $db->query("SELECT * FROM '$table' WHERE nstream = '$s_name' AND bot = '$empty'");
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
					$query = "SELECT COUNT (*) FROM '$table' WHERE country = '$country' AND nstream = '$s_name' AND bot = '$empty'";
				}
				else{
					$query = "SELECT COUNT (*) FROM '$table' WHERE country = '$country' AND bot = '$empty'";
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
	}
	$db->close();
	$sort = 3;
	if($se_label == 1){
		$sort = 4;
	}
	echo '</tbody>
</table>
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