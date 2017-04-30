<?php

class User{
    
    static function authenticateUserhash($user_id,$secure_hash){
        if($res = mysql_query("SELECT * FROM users WHERE user_id=$user_id AND secure_hash='$secure_hash'")){
            return mysql_fetch_assoc($res);
        }
        die();
    }
    
    static function updateUserStatus($user_id,$status){
        mysql_query("UPDATE users SET status='$status' WHERE user_id=$user_id");
        mysql_query("UPDATE players SET player_status='$status' WHERE user_id=$user_id");
        return true;
    }
    
    static function login($email,$password){
        $email      = mysql_real_escape_string($email);
        $password   = mysql_real_escape_string($password);
        $res        = mysql_query("SELECT * FROM users WHERE email='$email' AND password=SHA2('$password',512)");
        return mysql_fetch_assoc($res);
    }
    
    static function getUserDetails($user_id){
        $res = mysql_query("SELECT * FROM users WHERE user_id=$user_id");
        if($ret = mysql_fetch_assoc($res)){
            if(strlen($ret['image'])<5){
                $ret['image'] = getEmailGavathar($ret['email'],50);
            }
            return $ret;
        }
        return false;
    }
    
    static function getPlayingGames($user_id){
        $res = mysql_query("
        SELECT 
            g.game_id 
        FROM 
            games g
        JOIN 
            players p ON (p.game_id=g.game_id AND user_id=$user_id)
        WHERE 
            status='created' OR  
            status='playing';
        ");
        
        $game_ids = array();
        while($row = mysql_fetch_array($res)){
            $game_ids[] = $row['game_id'];
        }
        
        $arr  = array();
        if(count($game_ids)>0){
            $game_ids = implode(",",$game_ids);
            $res = mysql_query("SELECT DISTINCT user_id FROM players WHERE game_id IN ($game_ids) AND player_status='online' AND user_id!=$user_id");
            
            while($row = mysql_fetch_assoc($res)){
                $arr[] = $row['user_id'];
            }
        }
        return $arr;
    }
    
    
    static function EmailAvailable($email_id){
        $email_id= mysql_real_escape_string($email_id);
        $res = mysql_query("SELECT 1 FROM users WHERE email='$email_id'");
        if(mysql_num_rows($res)==0){
            return true;
        }
        return false;
    }
    
    static function createUser($email,$name,$password,$email_status='not_verified'){
        $email  = mysql_real_escape_string($email);
        $name   = mysql_real_escape_string($name);
        $password   = mysql_real_escape_string($password);
        $mail_hash  = sha1(md5(rand(100000,99999999)));
        $stats = mysql_query("
                    INSERT INTO users (email,name,password,email_status,email_hash,secure_hash)
                    VALUES ('$email','$name',SHA2('$password',512),'$email_status','$mail_hash',SHA2('$mail_hash',512))");
        if($user_id = mysql_insert_id()){
            $user = User::getUserDetails($user_id);
            if($email_status=='not_verified'){
                $content = HOST . " Email varification .</br> Please click following link to procced with email varification ";
                $content .= HOME.'email_varify/'.$user['email_hash'];
                Email::SMTPmail(HOST.' Email Varification ',$content,$user['email']);
            }
            return true;
        }
        return false;
    }
    
    static function varifyEmail($email_hash){
        $email_hash = mysql_real_escape_string($email_hash);
        mysql_query("UPDATE users SET email_status='verified' WHERE email_hash='$email_hash'");
        if(mysql_affected_rows()>0){
            return true;
        }
        return false;
    }
    
    
    static function openid_action($name,$email,$provider,$fb_user_id=0,$profile_image=''){
        $res = mysql_query("SELECT * FROM users WHERE email='$email'");
        if($row=mysql_fetch_assoc($res)){
            if($fb_user_id!=0){
                mysql_query("UPDATE users SET fb_user_id=$fb_user_id WHERE user_id=".$row['user_id']);
            }
            if(strlen($profile_image)>5){
                $profile_image = mysql_real_escape_string($profile_image);
                mysql_query("UPDATE users SET image='$profile_image' WHERE user_id=".$row['user_id']);
            }
            $_SESSION['user_id']    = $row['user_id'];

            if(isset($_COOKIE['login_redirect'])){
                $rurl = $_COOKIE['login_redirect'];
                setcookie('login_redirect','',1,'/');
                unset($_COOKIE['login_redirect']);
                header('location:'.HOME.$rurl);
                die();
            }
            echo '<script>if( window.popoppo ){ window.opener.location="http://poojyam.in/game_new";window.close(); } else { window.location="http://poojyam.in/game_new";}</script>';
        }else{
            self::createUser($email,$name,rand(100000,10000000),'verified');
            $res = mysql_query("SELECT * FROM users WHERE email='$email'");
            if($row=mysql_fetch_assoc($res)){
                if($fb_user_id!=0){
                    mysql_query("UPDATE users SET fb_user_id=$fb_user_id WHERE user_id=".$row['user_id']);
                }
                if(strlen($profile_image)>5){
                    $profile_image = mysql_real_escape_string($profile_image);
                    mysql_query("UPDATE users SET image='$profile_image' WHERE user_id=".$row['user_id']);
                }
                $_SESSION['user_id']    = $row['user_id'];
                
                if(isset($_COOKIE['login_redirect'])){
                    $rurl = $_COOKIE['login_redirect'];
                    setcookie('login_redirect','',1,'/');
                    unset($_COOKIE['login_redirect']);
                    header('location:'.HOME.$rurl);
                    die();
                }
                echo '<script>if(window.popoppo){ window.opener.location="http://poojyam.in/game_new";window.close(); } else { window.location="http://poojyam.in/game_new";}</script>';
            }
        }
        die();
    }
    
    /* Make All users offline */
    static function makeAllOffline(){
        if(
            mysql_query("UPDATE users SET `status`='offline' WHERE user_id>0 AND user_id!=100;") &&
            mysql_query("UPDATE player_status SET status='offline' WHERE player_id>0 AND user_id!=100")
            ){
                return true;
        }
        return false;
    }
    
    /* Get all online users */
    static function getAllOnlineUsers($user_id){
        $res = mysql_query("SELECT * FROM users WHERE status='online' AND user_id!=$user_id");
        $users = array();
        while($row = mysql_fetch_assoc($res)){
            $users[] = $row;
        }
        return $users;
    }
    
    static function getUsers(){
        $res= mysql_query("SELECT * FROM users");
        $users = array();
        while($row = mysql_fetch_assoc($res)){
            $users[] = $row;
        }
        return $users;
    }
    
}
