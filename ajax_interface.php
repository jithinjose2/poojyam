<?php
include "/var/www/poojyam.in//inc/common.php";

require_once BASE.'/lib/openid.php';
require_once BASE.'/lib/facebook/facebook.php';
require_once BASE.'/lib/facebook/send.message.php';
 ini_set('display_errors', 1);
    error_reporting(E_ALL);

$action = empty($_REQUEST['action'])?'':$_REQUEST['action'];

if($action=='send_fb_message'){
    $to_user_id = intval($_REQUEST['to_user']);
    $text_message = $_REQUEST['message'];
    if($to_user_id>0 && isset($_SESSION['fb_'.$GLOBALS['CONFIG']['facebook']['APP_ID'].'_user_id'])){
        $facebook = new Facebook(array(
            'appId' => $GLOBALS['CONFIG']['facebook']['APP_ID'],
            'secret' => $GLOBALS['CONFIG']['facebook']['APP_SECRET'],
        ));
        $fb_user = $facebook->getUser();
        $message=new SendMessage($facebook);
        if($message->sendMessage($text_message,$to_user_id)){
            echo "1";
        }
        echo "0";
    }
    
}elseif($action=='game_reject'){
    
    $to_user_id = $user['user_id'];
    $game_id = $_REQUEST['game_id'];
    if($to_user_id>0 && $game_id>0){
        Game::rejectInvite($game_id,$to_user_id);
        echo 1;
    }
    
}elseif($action=='send_fb_inviteall'){
    $user_id = $user['user_id'];
    $game_id = $_REQUEST['game_id'];
    if($user_id>0 && isset($_SESSION['fb_'.$GLOBALS['CONFIG']['facebook']['APP_ID'].'_user_id'])){
        if(Game::gameFBInvite($user_id,$game_id)){
            $facebook = new Facebook(array(
                'appId' => $GLOBALS['CONFIG']['facebook']['APP_ID'],
                'secret' => $GLOBALS['CONFIG']['facebook']['APP_SECRET'],
            ));
            $fb_user = $facebook->getUser();
            $publishStream = $facebook->api("/$fb_user/feed", 'post', array(
                'message' => $l[16].'http://poojyam.in/game_join/'.$game_id,
                'link'    => 'http://poojyam.in/game_join/'.$game_id,
                'picture' => 'http://poojyam.in/img/poojyam_vettu_kali.png',
                'name'    => $l[17],
                'description'=> $l[18]
                )
            );
            if($publishStream){
                echo "1";
                die();
            }
        }
        echo "0";
        die();
    }
}