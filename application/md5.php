<!DOCTYPE html>
<html>
<head>
<title>MD5</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../files/style.css">
<link rel="shortcut icon" href="../files/img/favicon.ico">
</head>
<body>
<br>
<center>
<br><br>
<b>Введите пароль</b>
<br><br>
<form method="post" action="md5.php">
<input size="20" maxlength="100" type="text" name="data"><br><br>
<input style="height:25px;width:130px;" type="submit" name="submit" value="Получить хэш MD5">
</form>
<br><br>
<?php
$x = $_POST['data'];
if(!empty($x)){
	echo 'Пароль: '.$x.'<br>Хэш: <b>'.md5($x).'</b><br><br>';
	if($_SERVER['REMOTE_ADDR'] != '127.0.0.1'){
		echo 'IP: '.$_SERVER['REMOTE_ADDR'].'<br>Хэш: <b>'.md5($_SERVER['REMOTE_ADDR']).'</b>';
	}
}
?>
</center>
</body>
</html>