<?php
include "/var/www/poojyam.in//inc/common.php";

require_once BASE.'/lib/openid.php';
require_once BASE.'/lib/facebook/facebook.php';

if(isset($_SESSION['fb_'.$GLOBALS['CONFIG']['facebook']['APP_ID'].'_user_id'])){
	$facebook = new Facebook(array(
        'appId' => $GLOBALS['CONFIG']['facebook']['APP_ID'],
        'secret' => $GLOBALS['CONFIG']['facebook']['APP_SECRET'],
    ));
    $fb_user = $facebook->getUser();
	$friends = $facebook->api(array
	(
		'method'        => 'fql.query',
		'query'     	=> "SELECT uid,name,pic_square FROM user WHERE online_presence IN ('active', 'idle') AND uid IN (SELECT uid2 FROM friend WHERE uid1 = $fb_user)"
	));
}
$players_online = User::getAllOnlineUsers($user['user_id']);


$tab = 1;
$ws_status = 1;
include BASE."header.php";

if(!isset($_REQUEST['join'])){
	if($game = Game::getNewUserGame($user['user_id'])){
		$game_id = $game['game_id'];
	}
}

if(empty($game_id)) {
	echo  "Critical Error";
	die();
}


mysql_query("UPDATE users SET name='കമ്പ്യൂട്ടര്‍' WHERE user_id=100;");

?>
<script>
	var socket;
	var user_id = <?php echo $user['user_id'];?>;
	var secure_hash = '<?php echo $user['secure_hash'];?>';
	var game_id = <?php echo $game_id; ?>;
	var home = "<?php echo HOME?>";
	
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
			$('#chatAudio')[0].play();
			var item = $("#players tr[user_id="+res.user_id+"]");
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
				if($("#players tr").length>1){
					$("#start_game").removeClass('disabled');
				}
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
		payload.user_id 	= $("#game_size").val();
		socket.send(JSON.stringify(payload));
	}
	
	$(document).ready(function(){
		$("#start_game").click(function(){
			
			if($(".user_selected[checked]").length<=1){
				alert('Need 2 or more players to start the game');
				return;
			}
			$(this).addClass("disabled");
			payload = new Object();
			payload.action 		= 'start_game';
			payload.game_id 	= game_id;
			payload.game_size 	= $('#game_size :selected').text();
			socket.send(JSON.stringify(payload));
		});
		
		$("#start_game_system").click(function(){
			$(this).attr("disabled","disabled");
			$(this).html('<img src="'+home+'img/294.GIF"/>');
			payload = new Object();
			payload.action 		= 'start_game_system';
			payload.game_id 	= game_id;
			payload.game_size 	= $('#game_size :selected').text();
			socket.send(JSON.stringify(payload));
		});
		
		$(".send_fb_invite").click(function(){
			var message = "Join this game playonline with me "+home+"join/"+game_id;
			var fb_user_id = $(this).parent().parent().parent().attr('fb_user_id');
			$(this).html('<img src="'+home+'img/294.GIF"/>');
			var last_clicked = $(this);
			$.ajax(home+'ajax_interface.php?action=send_fb_message&to_user='+fb_user_id+"&message="+message).done(function(data){
				last_clicked.removeClass('send_fb_invite');
				if(data==0){
					last_clicked.html('<?php echo $l[38]?>');
				}else{
					last_clicked.html('<?php echo $l[39]?>');
				}
			});
		});
		
		$(".invite-fbpost").click(function(){
			$.ajax(home+'ajax_interface.php?action=send_fb_inviteall&game_id='+game_id).done(function(data){
				if(data==0){
					$(".invite-fbpost").html('<?php echo $l[38]?>');
				}else{
					$(".invite-fbpost").html('<?php echo $l[39]?>');
					$(this).slideSown();
				}
			});
		});
		
		$(".send_user_invite").click(function(){
			
			var to_user_id = $(this).parent().parent().parent().attr('po_user_id');
			
			payload = new Object();
			payload.action 		= 'send_invite';
			payload.game_id 	= game_id;
			payload.to_user_id 	= to_user_id;
			if(socket.send(JSON.stringify(payload))){
				$(this).removeClass('send_user_invite');
				$(this).html('<?php echo $l[39]?>');
			}
			
		});	
	
	});
</script>
<script src="<?php echo HOME?>js/bvk.js"></script>

    <section id="signin_alt">
        <div  class="section-content">
          <div >
              <div class="container">
					<div class="row" data-animation="fadeInUp" style="opacity: 1">
						<!--<div class="title" style="margin-bottom: 20px; color: white;">
							<h4>പുതിയ കളി</h4>
						</div>-->
					</div>
                    <div class="row" data-animation="fadeInUp" style="opacity: 1">
						
						<div class="col-md-4 card" style="padding: 20px">
						  
							
							<div style="color:black">
								<select id="game_size" style="display: none">
									<?php
									for($i=7;$i<15;$i++){
										echo '<option>'.$i.'</option>';
									}
									?>
								</select>
								<?php echo $l[39]?>:<span style="float: right;color: #1717E2;cursor: pointer" data-toggle="modal" data-target="#my-modal-box-help"><?php echo $l[31]?></span><br><br> 
								<span id="game_url">
								  <input type="text" value="<?php echo HOME.'game_join/'.$game['game_id']?>" class="form-control"/>
								</span><span style="font-size: 11px;"><?php echo $l[40]?></span>
								<br/></br>
								<?php echo $l[41]?> : 
								<table id="players" width=100% class="table table-striped" style="color: #333">
									<?php
									foreach($game['players'] as $player) {
										?>
										<tr user_id="<?php echo $player['user_id']?>" style="background-color: #F9F9F9">
											<td class=""><input class="user_selected" type="checkbox" name="selected[]" checked/></td>
											<td class="image"><img src="<?php echo $player['image']?>" width="30px"/></td>
											<td class="name">&nbsp;<?php echo $player['name']?></td>
											<td class="player_status"><?php echo $player['player_status']?></td>
										</tr>
										<?php
									}
									?>
								</table>
								<button name="submit_login" type="submit" class="btn btn-block signin <?php if(count($game['players'])<=1) echo 'disabled';?>" id="start_game"><?php echo $l[42]?></button>
								<button type="submit" class="btn btn-block signin" id="start_game_system"><?php echo $l[43]?></button>
							</div>
						  
						</div>
						<div class="col-md-1" style="padding: 20px">
						</div>
						<div class="col-md-4 card" style="padding: 20px">
							<?php if((isset($friends) && count($friends)>0) || (isset($players_online) && count($players_online)>0)) { ?>
							<?php echo $l[44]?><br/><br/>
							<?php if($game['fb_invited']==0){ ?>
							<button class="btn btn-info invite-fbpost"><?php echo $l[45]?></button><br><br>
							<?php } ?>
							<div style="overflow-x: hidden;max-height: 300px;border: 1px solid white;">
								<table border=0 id="facebook_onliners" style="width: 100%;">
									<tbody>
									<?php if (isset($friends)) { foreach($friends as $friend) { ?>
									<tr fb_user_id="<?php echo $friend['uid']?>">
										<td><img src="<?php echo $friend['pic_square'];?>"/></td>
										<td><?php echo $friend['name'];?></td>
										<td>&nbsp;&nbsp;</td>
										<td><span style="float : right;"><button class="btn btn-info send_fb_invite"><?php echo $l[46]?></button></span></td>
									</tr>
									<?php } }  ?>
									<?php  if (isset($players_online)) {  foreach($players_online as $player_online) { if($player_online['user_id']==100) continue; ?>
									<tr po_user_id="<?php echo $player_online['user_id']?>">
										<td><img src="<?php echo $player_online['image'];?>"/></td>
										<td><?php echo $player_online['name'];?></td>
										<td>&nbsp;&nbsp;</td>
										<td><span style="float : right;"><button class="btn btn-info send_user_invite"><?php echo $l[46]?></button></span></td>
									</tr>
									<?php }} ?>
									</tbody>
								</table>
								
							</div>
							<?php } if(!isset($friends)) { ?>
							<?php echo $l[47]?>
							<?php } ?>
						</div>
					  </div>
					</div>
					<br><br>
			  </div>
		  </div>
	</section>
	<script>
		$(document).ready(function() {
			$('#facebook_onliners').filterTable();
			$('.filter-table input').addClass('form-control');
		});
	</script>
<?php
include BASE."footer.php";