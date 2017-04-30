<?php

define('EVT',"live");

if(EVT=='live'){
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $CONFIG['db']['host']   = "localhost";
    $CONFIG['db']['user']   = "root";
    $CONFIG['db']['password']   = "indian02";
    $CONFIG['db']['name']   = "bvk";
}else{
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $CONFIG['db']['host']   = "localhost";
    $CONFIG['db']['user']   = "root";
    $CONFIG['db']['password']   = "flock6454";
    $CONFIG['db']['name']   = "bvk";
}

$DEV_MODE = false;
if(isset($_REQUEST['pdebug'])){
    $DEV_MODE = true;
}

$GLOBALS['CONFIG']['facebook']['APP_ID']		= "672026416193343";
$GLOBALS['CONFIG']['facebook']['APP_SECRET']	= "b498f8de9aa4d004a13870746ab9f70f";

$GLOBALS['CONFIG']['EMAIL']['host']              = 'email-smtp.us-east-1.amazonaws.com';
$GLOBALS['CONFIG']['EMAIL']['port']              = 465;
$GLOBALS['CONFIG']['EMAIL']['username']          = 'AKIAJ5PZUCJWHTEBABUA';
$GLOBALS['CONFIG']['EMAIL']['password']          = 'Arp7suz5HJCFH91YBqlP+kWjdaj8rMLP/Z+M++pYxbhu';
$GLOBALS['CONFIG']['EMAIL']['from']              = "updates@poojyam.in";

     
$GLOBALS['CONFIG']['hash_gavathar']		= "http://107.170.28.215/progle/gavathar/{hash}/{size}/image.png";

define('HOME',"http://poojyam.in/");
define('HOST',"poojyam.in");