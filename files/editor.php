<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'editor'){
	echo '<div class="align_center bold indt_10">'.$trans['editor']['ed1'].'</div>
<div class="align_center indt_20">
<nav class="editor-menu">';
	$files = scandir('database');
	$x = 0;
	foreach($files as $v){
		if($v != "." && $v != ".." && $v != ".htaccess" && is_file('database/'.$v)){
			$f_n = str_ireplace('.dat', '', $v);
			if($x != 0){
				echo ' | ';
			}
			echo '<a class="link_tdn" href="'.$admin_page.'?q=editor&f='.$v.'">'.$f_n.'</a>';
			$x++;
		}
	}
	echo '</nav>
</div>';
	if(!empty($e_file)){
		$e_file_data = file_get_contents('database/'.$e_file);
	}
	else{
		$e_file_data = '';
	}
	if(!empty($e_file)){
		$l_change = date("Y-m-d H:i:s", filemtime('database/'.$e_file));
		$ex_lch = explode(" ", $l_change);
		$l_change = $ex_lch[0].', at '.$ex_lch[1];
		echo '<div class="align_center bold indt_10">'.$e_file.'</div>';
		echo '<div class="align_center">Last modified: '.$l_change.'</div>';
	}
	else{
		echo '<div class="align_center bold indt_10">'.$trans['editor']['ed2'].'</div>';
	}
	echo '<div class="ta_editor">
<form method="post" action="'.$admin_page.'?q=editor&f='.$e_file.'">
<div class="align_left indt_20">
<textarea id="code" name="e_file_data" rows="20">'.$e_file_data.'</textarea>';
	if(!empty($e_file)){
		echo '<div class="align_center"><input class="button" type="submit" name="button" value="Submit"></div>';
	}
	echo '</div>
</form>
</div>
<script type="text/javascript">
    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
      lineNumbers: true,
    });
</script>';
}
?>