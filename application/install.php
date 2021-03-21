<?php
@ini_set('display_errors', 0);
@error_reporting(0);
$url = 'http://ztds.info/download.php?q=ztds';
?>
<!DOCTYPE html>
<html>
<head>
<title>Installation</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php
$success = '<span style="color:green">ok</span><br>';
$error = '<span style="color:red">error</span><br>';
$test = 0;
if(mkdir('test', 0755)){
	echo "Создание папок: $success";
}
else{
	echo "Создание папок: $error";
	err();
}
if(file_put_contents('test/test.ini', '[test]'."\n".'a = "b"', LOCK_EX)){
	echo "Создание файлов: $success";
}
else{
	echo "Создание файлов: $error";
	rmdir('test');
	err();
}
if(parse_ini_file('test/test.ini')){
	echo "parse_ini_file: $success";
	unlink('test/test.ini');
	rmdir('test');
}
else{
	echo "parse_ini_file: $error";
	unlink('test/test.ini');
	rmdir('test');
	err();
}
if(class_exists('SQLite3')){
	echo "SQLite: $success";
}
else{
	echo "SQLite: $error";
	err();
}
if(extension_loaded('gd')){
	echo "GD Library: $success";
}
else{
	echo "GD Library: $error";
	err();
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
$data = curl_exec($ch);
if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200){
	echo "Загрузка: $success";
	if(file_put_contents('ztds.zip', $data, LOCK_EX)){
		echo "Сохранение архива: $success";
	}
	else{
		echo "Сохранение архива: $error";
		err();
	}
}
else{
	echo "Загрузка: $error";
	err();
}
curl_close($ch);
$zip = new ZipArchive;
if($zip->open('ztds.zip') === TRUE){
	$path = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'];
	if(preg_match("~^(.+?)\/install\.php$~", $path, $matches)){
		$path = $matches[1];
	}
	else{
		$path = '';
	}
	if(!empty($path) && $zip->extractTo("$path")){
		echo "Распаковка: $success";
	}
	else{
		echo "Распаковка: $error";
		unlink('ztds.zip');
		err();
	}
	$zip->close();
}
else{
	echo "Распаковка: $error";
	unlink('ztds.zip');
	err();
}
if(unlink('ztds.zip')){
	echo "Удаление архива: $success";
}
else{
	echo "Удаление архива: $error";
	err();
}
echo '<br>
<span style="color:green">Installation successful!</span>
<br><br>
<a href="admin.php">Вход в админку</a>
<br>login/pass: admin<br>';
unlink('install.php');
err();
function err(){
	echo '</body>
</html>';
exit();
}
?>