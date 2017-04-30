<?php
$access = "public";
include "/var/www/poojyam.in//inc/common.php";
	
if(!isset($_SESSION['user_id'])){
	setcookie('login_redirect','game_join/'.$_REQUEST['game_id'],0,'/');
	header('location:'.HOME.'openid/facebook');
	die();
}

header('location:'.HOME.'game_join/'.$_REQUEST['game_id']);
die();