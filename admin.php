<?php
define("INDEX", "yes");
@set_time_limit(0);
require_once 'config.php';
$start = microtime(true);
$trans = parse_ini_file('files/language/'.$admin_lang.'.ini', true);
require_once 'files/function.php';
require_once 'files/login.php';
require_once 'files/code.php';
require_once 'files/log.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>zTDS <?php echo $version; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="files/img/favicon.ico">
<link rel="stylesheet" type="text/css" href="files/lib/codemirror/codemirror.css">
<link rel="stylesheet" type="text/css" href="files/lib/datatables/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="files/style.css">
<link rel="stylesheet" type="text/css" href="files/lib/jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="files/lib/codemirror/codemirror.js"></script>
<script type="text/javascript" src="files/lib/jquery.min.js"></script>
<script type="text/javascript" src="files/lib/datatables/datatables.min.js"></script>
<script type="text/javascript" src="files/js/top.js"></script>
<script type="text/javascript" src="files/lib/jquery.responsiveTabs.js"></script>
<script type="text/javascript" src="files/lib/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="files/lib/jquery-ui/jquery.ui.touch-punch.min.js"></script>
</head>
<body>
<div class="header">
<div class="logo align_left">zTDS <?php echo $version; ?></div>
<div class="hamburger" id="pull_main">
<div class="h_el"></div>
<div class="h_el"></div>
<div class="h_el"></div>
</div>
</div>
<div class="content">
<div id="left">
<div class="left_block">
<?php require_once 'files/menu.php'; ?>
</div>
</div>
<div id="center">
<div class="center_block align_left">
<?php
require_once 'files/group.php';
require_once 'files/stream.php';
require_once 'files/key.php';
require_once 'files/editor.php';
require_once 'files/source.php';
require_once 'files/postback.php';
require_once 'files/country.php';
require_once 'files/apiset.php';
?>
</div>
</div>
<div id="right">
<div class="right_block align_left">
<?php require_once 'files/stats.php'; ?>
</div>
</div>
</div>
<div style="clear:both;"></div>
<div class="bottom">&copy; root</div>
<?php
if(empty($dg)){
	$dg = '[0,0,0,0,0]';
}
if(empty($dg_se)){
	$dg_se = '[0,0,0,0,0,0]';
}
?>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(init);
function init (){drawChart();<?php if(empty($s) && $se == 1){echo 'drawChart_se();';} ?>}
function drawChart(){
	var data = google.visualization.arrayToDataTable([['Day', '<?php echo $trans['chart']['ch1']; ?>', '<?php echo $trans['chart']['ch2']; ?>', '<?php echo $trans['chart']['ch3']; ?>', '<?php echo $trans['chart']['ch4']; ?>'], <?php echo $dg; ?>]);
	var options = {
		title:'Statistics',
		curveType:'none',
		legend:{position:'bottom'},
		chartArea:{left:60, right:20, top:20, bottom:40, width:'100%', height:'100%'},
	};
	if(document.getElementById('curve_chart')){
		var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
		chart.draw(data, options);
	}
}
function drawChart_se(){
	var data = google.visualization.arrayToDataTable([['Day', 'Google', 'Yandex', 'Mail.ru', 'Yahoo', 'Bing'], <?php echo $dg_se; ?>]);
	var options = {
		title:'Search engines',
		curveType:'none',
		legend:{position:'bottom'},
		chartArea:{left:60, right:20, top:20, bottom:40, width:'100%', height:'100%'},
	};
	if(document.getElementById('curve_chart_se')){
		var chart = new google.visualization.LineChart(document.getElementById('curve_chart_se'));
		chart.draw(data, options);
	}
}
<?php if($confirm == 1){echo "var cnf = true;\n";} else{echo "var cnf = false;\n";} ?>
</script>
<?php
if($debug == 1){
	echo '<div class="debug">'.(microtime(true) - $start).' s.</div>';
}
?>
<script type="text/javascript" src="files/js/bottom.js"></script>
</body>
</html>