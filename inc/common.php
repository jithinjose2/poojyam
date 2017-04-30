<?php
session_start();
define('BASE','/var/www/poojyam.in//');

include BASE."inc/config.php";

if(isset($_GET['lan']) && in_array($_GET['lan'],array('mal','eng'))){
    $_COOKIE['lan'] = $_GET['lan'];
    setcookie('lan',$_GET['lan'],time()+60*60*24*30*30);
}

$lan = 'eng';
if(isset($_COOKIE['lan']) && $_COOKIE['lan']=='mal'){
    $lan = 'mal';
    include BASE."inc/language_mal.php";
}else{
    include BASE."inc/language_eng.php";
}

include BASE."classes/User.php";
include BASE."classes/Game.php";
include BASE."classes/Chat.php";
include BASE."classes/Email.php";

mysql_connect($CONFIG['db']['host'],$CONFIG['db']['user'],$CONFIG['db']['password']);
mysql_select_db($CONFIG['db']['name']);

if(!(isset($access) && $access=='public')){
    if(!isset($_SESSION['user_id'])){
        $_SESSION['from_page'] = HOME.$_SERVER['REQUEST_URI'];
        header('location:'.HOME.'index.php');
        die();
    }
}

if(isset($_SESSION['user_id'])){
    $user = User::getUserDetails($_SESSION['user_id']);
}

function showAlert(){
    global $message,$error;
    if(!empty($message)){
        if(is_array($message)){
            $message = implode(" </br>",$message);
        }
        echo '<div class="alert alert-success">'.$message.'</div>';
    }
    
    if(!empty($error)){
        if(is_array($error)){
            $error = implode(" </br>",$error);
        }
        echo '<div class="alert alert-danger">'.$error.'</div>';
    }
    
}


function getEmailGavathar($email,$size){
    $default = str_replace('{hash}',md5( strtolower( trim( $email ) ) ),$GLOBALS['CONFIG']['hash_gavathar']);
    $default = str_replace('{size}',$size,$default);
    return "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
}

function mb_str_split( $string ) { 
    return preg_split('/(?<!^)(?!$)/u', $string ); 
}