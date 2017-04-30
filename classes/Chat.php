<?php

class Chat{
    
    static function addChat($user_id,$game_id,$text){
        
        //confirm users is acually in game
        $res = mysql_query("
        SELECT 
            user_id 
        FROM 
            players p
        JOIN
            games g ON(g.game_id=p.game_id AND (g.status='created' OR g.status='playing' OR g.status='ended'))
        WHERE 
            p.user_id=$user_id AND 
            p.game_id=$game_id
        ");
        if(mysql_numrows($res)==1){
            if(mysql_query("INSERT INTO chats(user_id,text,game_id) VALUES($user_id,'$text',$game_id)")){
                return true;
            }
        }
        
        return false;
    }
    
    static function getGameChats($game_id){
        $res = mysql_query('SELECT * FROM chats WHERE game_id='.$game_id." ORDER BY chat_time ASC");
        $data = array();
        while($row=mysql_fetch_assoc($res)){
            $row['time'] = date('r',strtotime($row['chat_time']));
            $data[]   = $row;
        }
        return $data; 
    }
    
}
