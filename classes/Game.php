<?php

class Game{
    
    static function getGameDetails($game_id){
        $res = mysql_query("SELECT * FROM games WHERE game_id=$game_id");
        if($game = mysql_fetch_assoc($res)){
            $res = mysql_query("SELECT * FROM players p JOIN users u ON(u.user_id=p.user_id) WHERE game_id=$game_id ORDER BY player_id");
            $game['players'] = array();
            while($row = mysql_fetch_assoc($res)){
				if(strlen($row['image'])<5){
					$row['image'] = getEmailGavathar($row['email'],50);
				}
                $game['players'][$row['user_id']] = $row;
            }
            return $game;
        }
        return false;
    }
    
    static function getGameBox($game_id){
        $res = mysql_query('SELECT * FROM boxes WHERE game_id='.$game_id);
        $data = array();
        while($row = mysql_fetch_assoc($res)){
            $data[$row['x'].','.$row['y']] = $row;
        }
        return $data;
    }
    
    static function checkActionStatus($game_id,$points){
        $tempd = mysql_query('SELECT * FROM actions WHERE game_id='.$game_id.' AND x1='.$points[0].' AND y1='.$points[1].' AND x2='.$points[2].' AND y2='.$points[3]);
		if($eee = mysql_fetch_array($tempd)){
			return true;
		}
        return false;
    }
    
    static function lineclicked($user_id,$game_id,$point){
        
        mysql_query('INSERT INTO actions(user_id,game_id,x1,y1,x2,y2) VALUES('.$user_id.','.$game_id.','.implode(',',$point).')');
        
        $ret['check'] = implode(',',$point);
        $ret['cross'] = array();
        if(self::checkBoxStatus($game_id,$point[0],$point[1]) && self::isboxedclear($game_id,$point[0],$point[1])){
            mysql_query('INSERT INTO boxes (game_id,user_id,x,y) VALUES('.$game_id.','.$user_id.','.$point[0].','.$point[1].')');
            $ret['cross'][] = array('user_id'=>$user_id,'point'=>$point[0].','.$point[1]);
        }
            
        if($point[0]==$point[2]){
            if(self::checkBoxStatus($game_id,$point[0]-1,$point[1]) && self::isboxedclear($game_id,$point[0]-1,$point[1])){
                mysql_query('INSERT INTO boxes (game_id,user_id,x,y) VALUES('.$game_id.','.$user_id.','.($point[0]-1).','.$point[1].')');
                $ret['cross'][] = array('user_id'=>$user_id,'point'=>($point[0]-1).','.$point[1]);
            }
        }
        
        if($point[1]==$point[3]){
            if(self::checkBoxStatus($game_id,$point[0],$point[1]-1) && self::isboxedclear($game_id,$point[0],$point[1]-1)){
                mysql_query('INSERT INTO boxes (game_id,user_id,x,y) VALUES('.$game_id.','.$user_id.','.$point[0].','.($point[1]-1).')');
                $ret['cross'][] = array('user_id'=>$user_id,'point'=>($point[0].','.($point[1]-1)));
            }
        }
        
        $ret['next_turn'] = $user_id;
        
        if(count($ret['cross'])==0){
            // Find the next player
            $take_next = false;
            $res = mysql_query('SELECT user_id FROM players WHERE game_id='.$game_id.' ORDER BY player_id ');
			
			/*
            while($row = mysql_fetch_assoc($res)){
                if($ret['next_turn']==$user_id){
                    $ret['next_turn'] = $row['user_id'];
                }
                if($take_next==true){
                    $ret['next_turn'] = $row['user_id'];
                }
                if($row['user_id']==$user_id){
                    $take_next=true;
                }
            }*/
			
			$userids = array();
			while($row=mysql_fetch_assoc($res)){
				$userids[] = $row['user_id'];
			}
            			
			foreach($userids as $tmp){
				if($ret['next_turn'] == $tmp){
					$nxt = current($userids);
					if($nxt>0){
						$ret['next_turn'] = $nxt;
					}else{
						$ret['next_turn'] = reset($userids);
					}
					break;
				}
			}
            
            mysql_query('UPDATE games SET next_turn='.$ret['next_turn'].' WHERE game_id='.$game_id);
        }else{
            
            self::updateGameScores($game_id);
            $ret['scores'] = array();
            $res = mysql_query('SELECT user_id,score FROM players WHERE game_id='.$game_id);
            while($row=mysql_fetch_assoc($res)){
                $ret['scores'][]   = $row;
            }
            
        }
        
        return $ret;
    }
    
    static function getGameActions($game_id){
        $res = mysql_query("SELECT * FROM actions WHERE game_id=$game_id");
        $data = array();
        while($row= mysql_fetch_assoc($res)){
            $data[implode(',',array($row['x1'],$row['y1'],$row['x2'],$row['y2']))] = $row;
        }
        return $data;
    }
    
    static function isboxedclear($game_id,$x,$y){
        $res = mysql_query("SELECT * FROM boxes WHERE x=$x AND y=$y AND game_id=$game_id");
        if($row=mysql_fetch_array($res)){
            return false;
        }
        return true;
    }
    
    static function checkBoxStatus($game_id,$x,$y){
        $cases = array();
        $cases[] = '('.implode(' AND ',array('x1='.$x,'y1='.$y,'x2='.($x+1),'y2='.$y)).')';
        $cases[] = '('.implode(' AND ',array('x1='.$x,'y1='.$y,'x2='.$x,'y2='.($y+1))).')';
        $cases[] = '('.implode(' AND ',array('x1='.$x,'y1='.($y+1),'x2='.($x+1),'y2='.($y+1))).')';
        $cases[] = '('.implode(' AND ',array('x1='.($x+1),'y1='.$y,'x2='.($x+1),'y2='.($y+1))).')';
        $res = mysql_query('SELECT count(1) as matches_count FROM actions WHERE game_id='.$game_id.' AND ('.implode(' OR ',$cases).')');
        echo 'SELECT count(1) as matches_count FROM actions WHERE game_id='.$game_id.' AND ('.implode(' OR ',$cases).')';
        if($row=mysql_fetch_assoc($res)){
            if($row['matches_count']==4){
                return true;
            }
        }
        return false;
    }
    
    static function updateGameScores($game_id){
        mysql_query("
            UPDATE 
                players
            SET
                score = (SELECT count(*) AS score FROM boxes WHERE boxes.user_id=players.user_id AND game_id=$game_id)
            WHERE
                game_id=$game_id;
        ");
		// Check game completed or not
		$res = mysql_query("SELECT count(*) as boxed_count FROM boxes WHERE game_id=$game_id");
		if($row = mysql_fetch_assoc($res)){
			if($row['boxed_count']>=36){
				mysql_query("UPDATE games SET status='ended' WHERE size>0 AND game_id=$game_id");
			}
		}
    }
    
    static function getUserGames($user_id){
        $res = mysql_query('SELECT game_id FROM players WHERE user_id='.$user_id);
        $data = array();
        while($row = mysql_fetch_assoc($res)){
            $data[$row['game_id']] = self::getGameDetails($row['game_id']);
        }
        return $data;
    }
    
    static function getNewUserGame($user_id){
        $user_id = intval($user_id);
        $res = mysql_query("SELECT game_id FROM games WHERE owner_id=$user_id AND status='created'");
        if($row = mysql_fetch_assoc($res)){
            mysql_query("UPDATE players SET player_status='online' WHERE user_id=$user_id AND game_id=".$row['game_id']);
            return self::getGameDetails($row['game_id']);
        }else{
            if(mysql_query("INSERT INTO games (owner_id,insert_date,status,next_turn) VALUES($user_id,NOW(),'created',$user_id)")){
                $game_id = mysql_insert_id();
                mysql_query("INSERT INTO players(user_id,game_id,player_status) VALUES($user_id,$game_id,'online')");
                return self::getGameDetails($game_id);
            }else{
                return false;
            }
        }
        return false;
    }
    
    static function joinGame($user_id,$game_id){
        $res = mysql_query("SELECT game_id FROM games WHERE status='created' AND game_id=$game_id");
        if($row = mysql_fetch_assoc($res)){
            $res = mysql_query("SELECT game_id FROM players WHERE user_id=$user_id && game_id=$game_id");
            if(mysql_num_rows($res)==0){
                if(mysql_query("INSERT INTO players (game_id,user_id,player_status) VALUES($game_id,$user_id,'online')")){
                    return true;
                }
            }else{
                return mysql_query("UPDATE players SET player_status='online' WHERE user_id=$user_id AND game_id=$game_id");
                return true;
            }
        }
        return false;
    }
    
    static function getUserPlayingGames($user_id){
        $query = "
        SELECT
            game_id,group_concat(u.name) as player_concat
        FROM
            players p
        LEFT JOIN
            users u
        ON 
            p.user_id = u.user_id
        WHERE
            game_id IN (SELECT game_id FROM games WHERE status='playing' AND game_id IN (SELECT game_id FROM players WHERE user_id=$user_id))
        group by 
            p.game_id;
        ";
        $res = mysql_query($query);
        $games = array();
        while($row=mysql_fetch_assoc($res)){
            $games[$row['game_id']] = $row['player_concat'];
        }
        return $games;
    }
    
    
    static function startGame($game_id){
        return mysql_query("UPDATE games SET status='playing' WHERE game_id=$game_id");
    }
	
	static function sendInvite($user_id,$game_id,$invite_type,$invite_id){
		$user_id = intval($user_id);
		$game_id = intval($game_id);
		$invite_type = intval($invite_type);
		$invite_id = intval($invite_id);
		
		$check_query = mysql_query("SELECT invite_id FROM invites WHERE user_id=$user_id AND game_id=$game_id AND invite_type=$invite_type AND invite_type_id=$invite_id");
		if($row = mysql_fetch_assoc($check_query)){
			return mysql_query("UPDATE invites SET status='invited' WHERE invite_id=".$row['invite_id']);
		}else{
			return mysql_query("INSERT INTO invites (user_id,game_id,invite_type,invite_type_id) VALUES($user_id,$game_id,$invite_type,$invite_id)");
		}
		return true;
	}
	
	static function rejectInvite($game_id,$to_user_id){
		$game_id = intval($game_id);
		$to_user_id = intval($to_user_id);
		return mysql_query("UPDATE invites SET status='rejected' WHERE game_id=$game_id AND invite_type_id=$to_user_id");
	}
	
	static function acceptInvite($to_user_id,$game_id){
		return mysql_query("UPDATE invites SET status='accepted' WHERE invite_type_id=$to_user_id AND game_id=$game_id");
	}
	
	static function getActiveuserInvites($user_id){
		$invites = mysql_query("
			SELECT
				i.invite_id,i.game_id,i.user_id,u.name,u.image,u.email
			FROM
				invites i
			JOIN
				users u ON(
					u.user_id = i.user_id
				)
			JOIN
				games g ON(
					g.game_id=i.game_id AND
					g.status='created'
				)
			WHERE
				i.status='invited' AND
				(i.invite_type_id=$user_id OR i.invite_type_id=(SELECT fb_user_id FROM users WHERE user_id=$user_id AND fb_user_id>0))
		");
		$data = array();
		while($invite = mysql_fetch_assoc($invites)){
			if(strlen($invite['image'])<5){
                $invite['image'] = getEmailGavathar($invite['email'],50);
            }
			$data[] = $invite;
		}
		return $data;
	}
	
	
	static function gameFBInvite($user_id,$game_id){
		$query = "UPDATE games SET fb_invited=1 WHERE game_id=$game_id AND owner_id=$user_id AND fb_invited=0";
		if(mysql_query($query)){
			if(mysql_affected_rows()==1){
				return true;
			}
		}
		return false;
	}
	
	static function getBotActNeededGames(){
		$query = mysql_query("SELECT game_id FROM games WHERE status='playing' AND next_turn=100");
		$game_ids = array();
		while($row = mysql_fetch_array($query)){
			$game_ids[] = $row['game_id'];
		}
		return $game_ids;
	}
	
}