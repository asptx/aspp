<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$log = '';
$z = '';
$ch = '';
$date = '';
if(!empty($g_id)){
	$db = new SQLite3($folder_log.'/'.$g_id.'.db');
	$db->busyTimeout($timeout_2);
	$db->exec("PRAGMA journal_mode = WAL;");
	if(file_exists($folder_log.'/'.$g_id.'.db')){
		if(!empty($_GET['t'])){
			$table = $_GET['t'];
		}
		else{
			$table = $db->querySingle("SELECT name FROM sqlite_master WHERE type = 'table' ORDER BY name DESC;");
		}
		$date = date('Y-m-d', $table);
		if($q != 'conf' && $q != 'pb'){
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
			if(!empty($option)){
				echo '<stat>
<div class="indt_10"><a class="mobile bold pm" href="#" id="pull_statistics">Statistics</a></div>
<mob class="w100">
<div class="align_center indt_10">
<form class="align_center" method="get" action="'.$admin_page.'">';
				if(!empty($q)){
					echo '<input name="q" type="hidden" value="'.$q.'">';
				}
				echo '<input name="g" type="hidden" value="'.$g_id.'">';
				if(!empty($s)){
					echo '<input name="s" type="hidden" value="'.$s.'">';
				}
				if(!empty($d)){
					echo '<input name="d" type="hidden" value="'.$d.'">';
				}
				echo '<select id="stat_date" class="h20" type="hidden" name="t" size = "1">';
				echo $option;
				echo '</select>
</form>
</div>
<div class="align_left">';
			}
			if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table'")){
				if($q != 'sources' && $q != 'countries' && $q != 'pb' && $q != 'keys'){
					if(!empty($s)){
						$query = "SELECT COUNT (*) FROM '$table' WHERE nstream = '$s_name' AND bot = '$empty'";
						$stat_header = $trans['right_menu']['rm1'];
						st();
					}
					else{
						$query = "SELECT COUNT (*) FROM '$table' WHERE bot = '$empty'";
						$stat_header = $trans['right_menu']['rm1'];
						st();
					}
				}
			}
			if(!empty($option)){
				echo '</div>
</mob>
</stat>';
			}
		}
		if($q == 'pb'){
			echo '<stat>
<div class="indt_10"><a class="mobile bold pm" href="#" id="pull_statistics">Statistics</a></div>
<mob class="w100">
<form class="align_center indt_10" method="get" action="'.$admin_page.'">
<input name="q" type="hidden" value="pb">
<input name="g" type="hidden" value="'.$g_id.'">';
			if(!empty($s)){
				echo '<input name="s" type="hidden" value="'.$s.'">';
			}
			if(!empty($d)){
				echo '<input name="d" type="hidden" value="'.$d.'">';
			}
			echo '<select id="pb_range" name="range" type="hidden">
<option'; if($range == '1'){echo ' selected="selected"';} echo ' value="1">Today</option>
<option'; if($range == '2'){echo ' selected="selected"';} echo ' value="2">Yesterday</option>
<option'; if($range == '3'){echo ' selected="selected"';} echo ' value="3">Last 7 days</option>
<option'; if($range == '4'){echo ' selected="selected"';} echo ' value="4">Last 14 days</option>
<option'; if($range == '5'){echo ' selected="selected"';} echo ' value="5">This week</option>
<option'; if($range == '6'){echo ' selected="selected"';} echo ' value="6">Last week</option>
<option'; if($range == '7'){echo ' selected="selected"';} echo ' value="7">This month</option>
<option'; if($range == '8'){echo ' selected="selected"';} echo ' value="8">Last month</option>
<option'; if($range == '9'){echo ' selected="selected"';} echo ' value="9">This year</option>
<option'; if($range == '10'){echo ' selected="selected"';} echo ' value="10">Last year</option>
<option'; if($range == '11'){echo ' selected="selected"';} echo ' value="11">All times</option>
<option'; if($range == '12'){echo ' selected="selected"';} echo ' value="12">Range</option>
</select>
<div class="range_selection" style="display:none;"><input type="text" value="'.$date_from.'" id="from" name="from" style="width:66px;" readonly> - <input type="text" value="'.$date_to.'" id="to" name="to" style="width:66px;" readonly></div>
<input name="t" type="hidden" value="'.$table.'">';
			echo '<div class="indt_10 range_submit" style="display:none;"><input class="button_small" type="submit" value="View"></div>
</form>
<div class="align_center indt_10"><b>Profit:</b> '.$total_profit.'</div>
</mob>
</stat>';
		}
		if($q == 'conf'){
			echo '<div class="align_center indt_10"><b>Last config change</b><br>'.$l_ch_conf.'</div>
<div class="align_center indt_20"><input class="button_small indb_15" type="button" value="Reload" onClick="javascript:location.reload();"></div>';
		}
		echo '<div class="indt_10"><ul>';
		if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table';")){
			if($q == 'sources'){
				$class_sources = 'class="current" ';
			}
			else{
				$class_sources = '';
			}
			if($q == 'countries'){
				$class_countries = 'class="current" ';
			}
			else{
				$class_countries = '';
			}
			if($q == 'pb'){
				$class_postback = 'class="current" ';
			}
			else{
				$class_postback = '';
			}
			if(!empty($s)){$st = '&s='.$s;}
			else{$st = '';}
			echo '<div class="menu"><li class="no_ls"><a href="'.$admin_page.'?q=log&g='.$g_id.$st.'&t='.$table.'">'.$trans['right_menu']['rm10'].'</a></li></div>
<div class="menu indt_3"><li class="no_ls"><a '.$class_countries.'href="'.$admin_page.'?q=countries&g='.$g_id.$st.'&t='.$table.'">'.$trans['right_menu']['rm13'].'</a></li></div>
<div class="menu indt_3"><li class="no_ls"><a '.$class_sources.'href="'.$admin_page.'?q=sources&g='.$g_id.$st.'&t='.$table.'">'.$trans['right_menu']['rm11'].'</a></li></div>';
			if(empty($s)){
				if(file_exists($folder_keys.'/'.$g_name.'/'.$date.'.dat') || file_exists($folder_keys.'/'.$g_name.'/'.$date.'-se.dat')){
					if($q == 'keys'){
						$class_keys = 'class="current" ';
					}
					else{
						$class_keys = '';
					}
					echo '<div class="menu indt_3"><li class="no_ls"><a '.$class_keys.'href="'.$admin_page.'?q=keys&g='.$g_id.'&t='.$table.'">'.$trans['right_menu']['rm12'].'</a></li></div>';
				}
			}
			echo '
<div class="menu indt_3"><li class="no_ls"><a '.$class_postback.'href="'.$admin_page.'?q=pb&g='.$g_id.$st.'&range=1&t='.$table.'">'.$trans['right_menu']['rm14'].'</a></li></div>';
		}
		echo '</ul></div>';
	}
	$db->close();
}
?>