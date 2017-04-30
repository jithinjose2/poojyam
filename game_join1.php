<?php
$access = "public";
include "/var/www/poojyam.in//inc/common.php";

if(!isset($_SESSION['user_id'])){
	setcookie('login_redirect','game_join/'.$_REQUEST['game_id'],0,'/');
	header('location:'.HOME);
	die();
}

$ws_status = 1;

Game::acceptInvite($_SESSION['user_id'],$_REQUEST['game_id']);

# roni marketing. pavarakal tharakal, 

include BASE."header.php";

if(isset($_REQUEST['game_id']) && intval($_REQUEST['game_id'])>0){
	$game = Game::getGameDetails($_REQUEST['game_id']);
	$game_id = $game['game_id'];
}

if(empty($game_id)) {
	echo  "Critical Error";
	die();
}
?>
<script>
	var socket;
	var user_id = <?php echo $user['user_id'];?>;
	var secure_hash = '<?php echo $user['secure_hash'];?>';
	var game_id = <?php echo $game_id; ?>;
	
	
	function checkJson(res) {
		console.log(res);
		
		if(res.action=='authenticated'){
			$('#status').html('<span class="label label-success">'+'<?php echo $l[26]?>'+'</span>');
			setTimeout(function(){
				$('#status').html('<span class="label label-success">'+'<?php echo $l[27]?>'+'</span>');
			}
			,3000);
			joinGame();
			
		}else if(res.action=='player_status_update'){
			var item = $("#players tr[user_id="+res.user_id+"]");
			$('#chatAudio')[0].play();
			if(item.length>0){
				item.find(".name").html(res.name);
				item.find(".player_status").html(res.player_status);
			}else{
				var new_player = $("#players tr").first().clone();
				new_player.find('tr').attr('user_id',res.user_id);
				new_player.find('tr').css('background-color','#F9F9F9');
				new_player.find('.name').html(res.name);
				new_player.find('.image').html('<img src="'+res.image+'" width="30px"/>');
				new_player.find('.player_status').html(res.player_status);
				$("#players").append(new_player);
			}
			
		}else if(res.action =='player_offline'){
			$('#chatAudio')[0].play();
			var item = $("#players tr[user_id="+res.user_id+"]");
			item.find('.player_status').html('offline');
			
		}else if(res.action =='start_game'){
			if(res.game_id==game_id){
				window.location = res.redirect;
			}
			
		}
		
	}
	
	function joinGame(){
		payload = new Object();
		payload.action 		= 'join_game';
		payload.game_id 	= game_id;
		payload.user_id 	= user_id;
		socket.send(JSON.stringify(payload));
	}
</script>
<script src="<?php echo HOME?>/js/bvk.js"></script>
    <section id="signin_alt">
        <div class="section-content">
          <div>
              <div class="container">
                    <div class="row" data-animation="fadeInUp" style="opacity: 1">
                      <div class="col-md-4 col-md-offset-4 card">
						
						<h4><?php echo $l[35]?></h4>
						<span id="rd_status"><?php echo $l[36]?></span></br></br>
						<table id="players" width=100% class="table table-striped" style="color: #333;margin-bottom: 5px;">
							<?php
							foreach($game['players'] as $player) {
								?>
								<tr user_id="<?php echo $player['user_id']?>"  style="background-color: #F9F9F9">
									<td class=""><input class="user_selected" type="checkbox" name="selected[]" checked/></td>
									<td class="image"><img src="<?php echo $player['image']?>" width="30px"/></td>
									<td class="name">&nbsp;<?php echo $player['name']?></td>
									<td class="player_status"><?php echo $player['player_status']?></td>
								</tr>
								<?php
							}
							?>
						</table>
						<div style="padding: 10px;margin-bottom: 30px;text-align: center">
							<img src="<?php echo HOME?>/img/103.GIF"/>
						</div>
					  </div>
					  </div>
					</div>
			  
			  </div>
		  </div>
	</section>
<?php
include BASE."footer.php";