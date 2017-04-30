<?php
require "/var/www/poojyam.in//lib/class.phpmailer.php";

class Email{
    
    static private $mail=null;
    
    static private function connect(){
        self::$mail = new PHPMailer();
        self::$mail->IsSMTP(true); 
        self::$mail->SMTPSecure = "ssl"; 
        self::$mail->SMTPAuth   = true;  
        self::$mail->Mailer     = "smtp";
        self::$mail->Host       = $GLOBALS['CONFIG']['EMAIL']['host'];
        self::$mail->Port       = $GLOBALS['CONFIG']['EMAIL']['port'];
        self::$mail->Username   = $GLOBALS['CONFIG']['EMAIL']['username'];
        self::$mail->Password   = $GLOBALS['CONFIG']['EMAIL']['password'];
        self::$mail->SMTPDebug = 1;
    }
    
    static function SMTPmail($subject,$message,$to,$from=""){
       // echo $message; die();
        if(self::$mail==null){
            self::connect();
        }
        if($from==""){
            $from =$GLOBALS['CONFIG']['EMAIL']['from'];
        }
        if($to==""){
            $to =$GLOBALS['CONFIG']['EMAIL']['from'];
        }
        try{
            self::$mail->SetFrom($from, 'Poojyam');
            self::$mail->Subject = $subject;
            self::$mail->MsgHTML($message);
            self::$mail->AddAddress($to);
            
            if(self::$mail->Send()){
                return true;
            }else{
                if(true){
                    echo self::$mail->ErrorInfo;
                }
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        
        return false;
    }
    
    static function sendEmailTemplate($template="signup_complete",$to,$subject,$data=array()){
        
        $data['UNSUBSCRIBEHASH'] = md5(Secure::encrypt($to));
        $content = file_get_contents($GLOBALS['CONFIG']['base'].'/emails/'.$template.'.html');
        if(!isset($data['extra']) && !isset($data['EXTRA'])){
            $data['EXTRA'] = "";
        }
        foreach($data as $key=>$value){
            $content = str_replace('{'.strtoupper($key).'}',$value,$content);
        }
        preg_match_all('/{BLOCK:(.)*}/',$content,$matches);
        foreach($matches[0] as $match){
            $match_real = $match;
            $match = str_replace('{BLOCK:','',$match);
            $match = str_replace('}','',$match);
            $block = self::getBlock($match);
            $content = preg_replace('/'.$match_real.'/',$block,$content,1);
            
        }
        $sucess = self::SMTPmail($subject,$content,$to);
        
        $query = array();
        if($sucess){
            $query['status'] = 3;
        }else{
            $query['status'] = 4;
        }
        $query['priority']  = 10;
        $query['email_from']  = $GLOBALS['CONFIG']['EMAIL']['from'];
        $query['email_to']    = $to;
        $query['subject'] = $subject;
        $query['data']  = json_encode($data);
        $query['insert_date']  = time();
        $query['template']  = $template;
        Database::exec('INSERT INTO email_logs '.Database::generateCombined($query,true),$query);
    }
    
    static function getBlock($blockname){
        global $CACHE;
        $content = file_get_contents($GLOBALS['CONFIG']['base'].'/emails/blocks/'.strtolower($blockname).'.html');
        if($blockname=='BESTPRICES'){
            
            $res = Database::exec('SELECT * FROM searchs WHERE status=4 ORDER BY visits LIMIT '.rand(1,$CACHE['searchs_count']).',2');
            $i=0;
            while($search = $res->fetch()){
                $i++;
                
                $content = str_replace("{LINK$i}",formatNormalize($search['query']),$content);
                $content = str_replace("{IMAGE$i}",$search['image'],$content);
                $content = str_replace("{MINPRICE$i}",$search['min_price'],$content);
                $content = str_replace("{MAXPRICE$i}",$search['max_price'],$content);
                $content = str_replace("{NAME$i}",$search['query'],$content);
                $content = str_replace("{LIKES$i}",$search['votes'] + $search['visits'],$content);
                $content = str_replace("{ITEMS$i}",$search['offers_count'],$content);
                
            }
            
        }
        
        return $content;
    }
    
}
