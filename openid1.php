<?php
$access = "public";
require_once "/var/www/poojyam.in//inc/common.php";

require_once BASE.'/lib/openid.php';
require_once BASE.'/lib/facebook/facebook.php';

$provider = (isset($_REQUEST['provider']))?$_REQUEST['provider']:'';

if($provider=="google"){
    
    $openid = new LightOpenID("Poojyam.in");
    if(isset($_REQUEST['step'])){
        $openid->validate();
        $data = $openid->getAttributes();
        User::openid_action($data['namePerson/first']." ".$data['namePerson/last'],$data['contact/email'],"google");
        die();
    }
        $openid->identity = 'https://www.google.com/accounts/o8/id';
        $openid->required = array(
            'namePerson/first',
            'namePerson/last',
            'contact/email'
        );
        $openid->returnUrl  = HOME.'openid1.php?provider=google&step=2';
        header('Location:'.$openid->authUrl()) ;
        die();
        
}elseif($provider=="facebook"){
    $facebook = new Facebook(array(
        'appId' => $GLOBALS['CONFIG']['facebook']['APP_ID'],
        'secret' => $GLOBALS['CONFIG']['facebook']['APP_SECRET'],
    ));
    $user = $facebook->getUser();
    
    if (isset($_REQUEST['step']) || $user!=0) {
        try {
            $user_profile = $facebook->api('/me');
            $headers = get_headers('https://graph.facebook.com/'.$user_profile['id'].'/picture',1);
            if(isset($headers['Location'])) {
                $user_profile['profile_image'] = $headers['Location']; // string
            } else {
                $user_profile['profile_image'] = '';
            }
        } catch (FacebookApiException $e) {
            $user = null;
        }
        if (!empty($user_profile )) {
            User::openid_action($user_profile['name'],$user_profile['email'],"facebook",$user,$user_profile['profile_image']);
            die();
        } else {
            die("There was an error.");
        }
    } else {
        $permissions = array('email','user_status','user_online_presence','friends_online_presence','xmpp_login','publish_stream');
        $login_url = $facebook->getLoginUrl(array( "scope" => $permissions));
        header("Location:$login_url");
        die();
    }
    
}
?>