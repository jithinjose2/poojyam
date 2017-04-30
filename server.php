<?php
$access = "public";
include "/var/www/poojyam.in//inc/common.php";

include BASE."classes/Bot.php";
include BASE."lib/Socket.php";
include BASE."lib/Server.php";
include BASE."lib/Connection.php";

echo "\033[2J";
echo "\033[0;0f";

$GLOBALS['botreq'] =  array();


class Application {
	
	private $connections = array();	// array to hold authenticated connections
	
	public $server;
	
	public function __construct(){
		$this->server = new Server('0.0.0.0', 8000, false);
		$this->server->setMaxClients(100);
		$this->server->setCheckOrigin(false);
		$this->server->setAllowedOrigin('192.168.1.153');
		$this->server->setMaxConnectionsPerIp(100);
		$this->server->setMaxRequestsPerMinute(2000);
		$this->server->setHook($this);
		$this->server->run();
	}
	
	/* Fired when a socket trying to connect */
	public function onConnect($connection_id){
		$this->writeLog( "On connect called : $connection_id");
        return true;
    }
    
	/* Fired when a socket disconnected */
    public function onDisconnect($connection_id){
		
		$this->writeLog( "On disconnect called : $connection_id");
		
		if(isset($this->connections[$connection_id])){
			$user_id = $this->connections[$connection_id];
			unset($this->connections[$connection_id]);
			
			// if it is the single connection of the user make him offline
			if(!in_array($user_id,$this->connections)){
				User::updateUserStatus($user_id,'offline');
				if($update_players = User::getPlayingGames($user_id)){
					$ret = array();
					$ret['user_id'] = $user_id;
					foreach($update_players as $update_player_id){
						$this->sendDataToUser($update_player_id,'player_offline',$ret);
					}
				}
			}
		}
    }
    
	/* Fired when data received */
    public function onDataReceive($connection_id,$data){
		//sleep(rand(1,5));
		$this->writeLog( "Data received from $connection_id : with action ".$data['action']."    ".json_encode($data));
		
		$data = json_decode($data,true);
		
		if(isset($data['action'])){
			$action = 'action_'.$data['action'];
			if( method_exists($this,$action)){
				unset($data['action']);
				$this->$action($connection_id,$data);
			}else{
				echo "\n Caution : Action handler '$action' not found!";
			}
		}
		
    }
	
	/* Used to send data to particular connection */
	public function sendDataToConnection($connection_id,$action,$data){
		$this->writeLog( "Data sending to $connection_id : with action ".$action."    ".json_encode($data));
		$this->server->sendData($connection_id,$action,$data);
		$this->writeLog( "Data send complete $connection_id : with action ".$action);
	}
	
	/* Used to send data to connection authenticated with user user user_id */
	public function sendDataToUser($user_id,$action,$data){
		foreach($this->connections as $connection_id=>$user_id_l){
			echo "C".$connection_id;
			if($user_id_l==$user_id){
				$this->sendDataToConnection($connection_id,$action,$data);
			}
		}
	}
	
	/* Write master log */
	public function writeLog($log){
		$utime = gettimeofday();
		$log = "\n [".date('r')."][".$utime['usec']."]    ".$log;
		echo $log;
		$fp = fopen(BASE."log/scoket-".date("jsF-Y").".txt","a");
		fwrite($fp,$log);
		fclose($fp);
	}
	
	/* used to send data to players who are online */
	public function sendDataToPlayers($game_id,$action,$data){
		$game = Game::getGameDetails($game_id);
		$data['game_id']	= $game_id;
		foreach($game['players'] as $player){
			$this->sendDataToUser($player['user_id'],$action,$data);
		}
	}
	
	
	public function broadcastAct(){
		if(count($GLOBALS['botreq'])>0){
			foreach($GLOBALS['botreq'] as $key=>$game_id){
				$bot = new Bot();
				if($nextline = $bot->nextAction2($game_id)){
					$this->action_line_clicked(0,array('game_id'=>$game_id,'clicked_line'=>$nextline));
				}
				unset($GLOBALS['botreq'][$key]);
			}
		}
	}
	
	
	
	
	
	
	
	
	
	///// RECIVE ACTIONS ////
	// function to authenticate user connection
	public function action_authenticate($connection_id,$data){
		
		if(isset($this->connections[$connection_id])){
			unset($this->connections[$connection_id]);
		}
		
		if(isset($data['secure_hash']) && isset($data['user_id']) && User::authenticateUserhash($data['user_id'],$data['secure_hash'])){
			echo  "\n Client Authenticated : CI:".$connection_id." UID : ".$data['user_id'];
			$this->connections[$connection_id] = $data['user_id'];
			$this->sendDataToConnection($connection_id,'authenticated','1');
			User::updateUserStatus($data['user_id'],'online');
			// Update game players is that user is online
			if(isset($data['game_id']) && $data['game_id']>0){
				$this->sendDataToPlayers($data['game_id'],'player_online',array('user_id'=>$data['user_id']));
			}
		}else{
			$this->server->closeClient($connection_id);
		}
	}
	
	
	// function to respond to user line click
	public function action_line_clicked($connection_id,$data){
		if($connection_id==0){
			$user_id	= 100;
		}else{
			$user_id	= $this->connections[$connection_id];
		}
		
		if(isset($data['game_id']) && isset($data['clicked_line'])){
			$game = Game::getGameDetails($data['game_id']);
			$points = explode(',',$data['clicked_line']);
			if(
				$game['next_turn']==$user_id &&
				count($points)==4 &&
				!Game::checkActionStatus($data['game_id'],$points) &&
				$points[0]<=$game['size'] &&
				$points[1]<=$game['size'] &&
				$points[2]<=$game['size'] &&
				$points[3]<=$game['size']){
				// action ok insert it
				
				$ret	= Game::lineclicked($user_id,$data['game_id'],$points);
				$ret['game_id'] = $data['game_id'];
				foreach($game['players'] as $user_id=>$player_id){
					$this->sendDataToUser($user_id,'game_update',$ret);
				}
				if(isset($ret['next_turn']) && $ret['next_turn']==100){
					$GLOBALS['botreq'][] = $data['game_id'];
					/*
					$bot = new Bot();
					if($nextline = $bot->nextAction2($data['game_id'])){
						$this->action_line_clicked(0,array('game_id'=>$data['game_id'],'clicked_line'=>$nextline));
					}*/
				}
			}else{
				echo "here";
			}
		}
	}
	
	public function action_join_game($connection_id,$data){
		$user_id	= $this->connections[$connection_id];
		if(isset($data['game_id'])){
			if(Game::joinGame($user_id,$data['game_id'])){
				$user = User::getUserDetails($user_id);
				$ret = array();
				$ret['user_id'] = $user_id;
				$ret['game_id'] = $data['game_id'];
				$ret['name']	= $user['name'];
				$ret['image']	= $user['image']; 
				$ret['player_status'] = 'online';
				$this->sendDataToPlayers($data['game_id'],'player_status_update',$ret);
			}
		}
	}
	
	public function action_chat_new_text($connection_id,$data){
		$user_id	= $this->connections[$connection_id];
		if(!empty($data['game_id']) && $data['game_id']>0 &&!empty($data['text'])){
			if(Chat::addChat($user_id,$data['game_id'],$data['text'])){
				$user = User::getUserDetails($user_id);
				$ret = array();
				$ret['user_name'] 	= $user['name'];
				$ret['user_id'] 	= $user['user_id'];
				$ret['game_id'] 	= $data['game_id'];
				$ret['time']		= time();
				$ret['text']		= $data['text'];
				$this->sendDataToPlayers($data['game_id'],'new_chat',$ret);
			}
		}
	}
	
	public function action_start_game($connection_id,$data){
		$user_id	= $this->connections[$connection_id];
		if(!empty($data['game_id']) && $data['game_id']>0 && !empty($data['game_size'])){
			$temp = Game::getNewUserGame($user_id);
			if($data['game_id']==$temp['game_id'] && count($temp['players'])>1){
				if(Game::startGame($data['game_id'])){
					$ret = array();
					$ret['game_id'] = $data['game_id'];
					$ret['redirect'] = HOME.'game_play/'.$data['game_id'].'/playit';
					$this->sendDataToPlayers($data['game_id'],'start_game',$ret);
				}
			}
		}
	}
	
	public function action_start_game_system($connection_id,$data){
		$user_id	= $this->connections[$connection_id];
		if(Game::joinGame(100,$data['game_id'])){
			if(Game::startGame($data['game_id'])){
				$ret = array();
				$ret['game_id'] = $data['game_id'];
				$ret['redirect'] = HOME.'game_play/'.$data['game_id'].'/playit';
				$this->sendDataToPlayers($data['game_id'],'start_game',$ret);
			}
		}
	}
	
	public function action_send_invite($connection_id,$data){
		$user_id	= $this->connections[$connection_id];
		if(!empty($data['game_id']) && $data['game_id']>0){
			if(isset($data['to_user_id']) && $data['to_user_id']>0){
				Game::sendInvite($user_id,$data['game_id'],1,$data['to_user_id']);
				
				$user_details = User::getUserDetails($user_id);
				$datas = array();
				$datas['name'] = $user_details['name'];
				$datas['image'] = $user_details['image'];
				$datas['user_id'] = $user_id;
				$datas['game_id'] = $data['game_id'];
				$this->sendDataToUser($data['to_user_id'],'game_invite',$datas);
			}
		}
	}
	
}


/*
//pcntl_signal(SIGINT, 'shutdown');  
//pcntl_signal(SIGTERM, 'shutdown');

function shutdown(){
	echo  "\n Starting Server shutdown sequence..";
	if(User::makeAllOffline()){
		echo  "\n Server Server shutdown sequence completed";
	}
	echo  "\n GAME OVER, Good buy";
	die();
}
register_shutdown_function('shutdown');
*/

// Initiate App Update Process
User::makeAllOffline();
$GLOBALS['botreq'] = Game::getBotActNeededGames();
$app = new Application();



