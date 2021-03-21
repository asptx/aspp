<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$error = '';
$ip_user = trim($_SERVER['REMOTE_ADDR']);
if(!empty($ip_allow) && $ip_user != $ip_allow){
	header('HTTP/1.1 403 Forbidden');
	die('403 Forbidden');
	exit();
}
session_start();
if(!empty($_GET['q']) && $_GET['q'] == 'logout'){
	if($auth_mode == 1){
		unset($_SESSION['auth']);
		session_destroy();
	}
	else{
		setcookie("auth", false, time() - 4800, "/");
	}
	header("Location: $admin_page");
}
if(empty($_SESSION['auth'])){
	$_SESSION['auth'] = '';
}
if(empty($_COOKIE['auth'])){
	$_COOKIE['auth'] = '';
}
if($protect_mode == 2){
	if(!file_exists('temp')){
		mkdir('temp', 0755);
	}
	if(!file_exists('temp/.htaccess')){
		file_put_contents('temp/.htaccess', "<Files *.dat>\nDeny from all\n</Files>", LOCK_EX);
	}
	require_once 'files/lib/GoogleAuthenticator.php';
	$ga = new PHPGangsta_GoogleAuthenticator();
	$qr = '';
	if(!file_exists('temp/'.$file_totp)){
		$secret = $ga->createSecret();
		file_put_contents('temp/'.$file_totp, $secret, LOCK_EX);
		$qrCodeUrl = $ga->getQRCodeGoogleUrl($_SERVER['HTTP_HOST'], $secret);
		$qr = '<div class="align_center indt_20">
<img width="200" src="'.$qrCodeUrl.'">
</div>';
	}
	else{
		$secret = trim(file_get_contents('temp/'.$file_totp));
	}
	$myCode = $ga->getCode($secret);
	$_SESSION['code'] = $myCode;
}
if(isset($_POST['submit'])){
	if($protect_mode == 1){
		$post_code = trim(strtoupper($_POST['code']));
	}
	if($protect_mode == 2){
		$post_code = trim($_POST['code']);
	}
	if($admin_login != trim($_POST['login']) OR $admin_pass != md5(trim($_POST['pass']))){
		$error = '<div class="align_center bold indt_10">Wrong login or password!</div>';
		sleep(1);
	}
	elseif($protect_mode == 1 && (!isset($_SESSION['code']) || empty($_SESSION['code']) || $post_code != $_SESSION['code'])){
		$error = '<div class="align_center bold indt_10">Wrong captcha!</div>';
		sleep(1);
	}
	elseif($protect_mode == 2 && (!isset($_SESSION['code']) || empty($_SESSION['code']) || $post_code != $_SESSION['code'])){
		$error = '<div class="align_center bold indt_10">Wrong code!</div>';
		sleep(1);
	}
	else{
		if($auth_mode == 1){
			$_SESSION['auth'] = $admin_login;
		}
		else{
			setcookie("auth", md5($ip_user.$admin_pass), time()+60*60*24*365, "/");
		}
		header("Location: $admin_page");
	}
}
if(($auth_mode == 1 && !$_SESSION['auth']) || ($auth_mode == 0 && (!isset($_COOKIE['auth']) || (md5($ip_user.$admin_pass) != $_COOKIE["auth"])))){
	echo '<!DOCTYPE html>
<html>
<head>
<title>Authorization</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="files/style.css">
<link rel="shortcut icon" href="files/img/favicon.ico">
</head>
<body>
<div class="align_center bold title indt_50">zTDS '.$version.'</div>
<form method="post">
<div class="align_center indt_50">
Login<br>
<input class="input_auth1 indt_3" type="text" name="login" autofocus>
</div>
<div class="align_center indt_20">
Password<br>
<input class="input_auth1 indt_3" type="password" name="pass">
</div>';
	if($protect_mode == 1){
		echo '<div class="align_center indt_20">
<img style="border: 0px solid gray;" src="files/lib/captcha/captcha.php" width="120" height="40"/><br>
<input class="input_auth2 indt_3" type="text" name="code">
</div>';
	}
	if($protect_mode == 2){
		if(!empty($qr)){
			echo $qr;
		}
		echo '<div class="align_center indt_20">
Code<br>
<input class="input_auth2 indt_3" type="text" name="code">
</div>';
	}
	echo '<div class="align_center">
<input class="align_center button" type="submit" name="submit" value="Submit">
</div>
</form>'.$error.'</body>
</html>';
	exit();
}
?>