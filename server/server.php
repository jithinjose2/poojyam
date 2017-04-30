<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

mysql_connect("localhost","root","flock6454");
mysql_select_db("webchat");

include "./lib/Socket.php";
include "./lib/Server.php";
include "./lib/Connection.php";

echo "\033[2J";
echo "\033[0;0f";


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
		echo "\nOn connect called : $connection_id";
        return true;
    }
    
	/* Fired when a socket disconnected */
    public function onDisconnect($connection_id){
		if(isset($this->connections[$connection_id])){
			unset($this->connections[$connection_id]);
		}
        echo "\nOn disconnect called : $connection_id";
    }
    
	/* Fired when data received */
    public function onDataReceive($connection_id,$data){
		echo "\nData received from $connection_id :";
		
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
		$this->server->sendData($connection_id,$action,$data);
	}
	
	/* Used to send data to connection authenticated with user user user_id */
	public function sendDataToUser($user_id,$action,$data){
		foreach($this->connections as $connection_id=>$user_id_l){
			if($user_id_l==$user_id){
				$this->sendDataToConnection($connection_id,$action,$data);
			}
		}
	}
	
	
	
	
	
	
	///// RECIVE ACTIONS ////
	public function action_authenticate($connection_id,$data){
		
		if(isset($this->connections[$connection_id])){
			unset($this->connections[$connection_id]);
		}
		
		if(isset($data['secure_hash']) && $data['secure_hash']=='SSSSSSSSSSSS'){
			echo  "\n Client Authenticated : CI:".$connection_id." UID : ".$data['user_id'];
			$this->connections[$connection_id] = $data['user_id'];
			$this->server->sendAll('authenticated','1');
		}else{
			$this->server->closeClient($connection_id);
		}
	}
	
	
}

$app = new Application();