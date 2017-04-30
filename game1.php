<?php
include "/var/www/poojyam.in//inc/common.php";

require_once BASE.'/lib/openid.php';
require_once BASE.'/lib/facebook/facebook.php';

$ws_status = 1;
if(!isset($_REQUEST['game_id'])){
	echo 'which game?';
	die();
}

$game_id = $_REQUEST['game_id'];
$user_id = $user['user_id'];

$colors = array("#5CB85C","#F0AD4E","#D9534F","#5BC0DE");

$game = Game::getGameDetails($game_id);
if(count($game['players'])<=1){
	header('location:'.HOME.'game_new');
	die();
}

if(in_array($user_id['user_id'],array_keys($game['players']))){
	$ugm = 'play';			// User game mode2
}else{
	$ugm = 'watch';
}

if(isset($_SESSION['fb_'.$GLOBALS['CONFIG']['facebook']['APP_ID'].'_user_id'])){
	$facebook = new Facebook(array(
		'appId' => $GLOBALS['CONFIG']['facebook']['APP_ID'],
		'secret' => $GLOBALS['CONFIG']['facebook']['APP_SECRET'],
	));
	$fb_user = $facebook->getUser();
}

if (isset($_GET['publish'])){
	try {
		
		$publishStream = $facebook->api("/$fb_user/feed", 'post', array(
			'link'    => 'http://poojyam.in?'.rand(1,9),
			'picture' => 'http://poojyam.in/game_image_'.$game_id.'_result.png',
			'name'    => $l[17],
			'description'=> $l[18]
			)
		);
		if($publishStream){
			echo "1";
			die();
		}
		//as $_GET['publish'] is set so remove it by redirecting user to the base url
	} catch (FacebookApiException $e) {
		var_dump($e);
	}
	echo "0";
	die();
}


$boxes 		= Game::getGameBox($game_id);
$actions 	= Game::getGameActions($game_id);
$chats		= Chat::getGameChats($game_id);

$tab = 100+$game_id;
include BASE."header.php";
?>
<script src="<?php echo HOME?>js/ctm.js"></script>
<script>
	var socket;
	var user_id = <?php echo $user['user_id'];?>;
	var secure_hash = '<?php echo $user['secure_hash'];?>';
	var game_id = <?php echo $game_id; ?>;
	var game_size = <?php echo $game['size']; ?>;
	var facebook_abc = <?php if(isset($fb_user) && ($fb_user>0)) echo "1"; else echo "0"; ?>;
	var online_image  = "<?php echo HOME?>/img/player_online.png";
	var offline_image = "<?php echo HOME?>/img/player_offline.png";
	
	if (!Date.now) {
		Date.now = function() { return new Date().getTime(); };
	}
	
	function checkJson(res) {
		console.log(res);
		
		if(res.action=='authenticated'){
			$('#status').html('<span class="label label-success">'+'<?php echo $l[26]?>'+'</span>');
			setTimeout(function(){
					$('#status').html('<span class="label label-success">'+'<?php echo $l[27]?>'+'</span>');
			}
			,3000);
			
		}else if(res.action=='game_update' && res.game_id==game_id){
			
			$('body').css('cursor', 'auto');
			$('#chatAudio')[0].play();
			if (res.hasOwnProperty('cross')) {
				for(i=0;i<res.cross.length;i++) {
					$("[data='"+res.cross[i].point+"']").addClass('box_user_'+res.cross[i].user_id);
				}
			}
			
			if (res.hasOwnProperty('check')) {
				$("[data='"+res.check+"']").addClass('selected');
				$("[data='"+res.check+"']").addClass("justclicked");
			}
			
			if(res.hasOwnProperty('next_turn')){
				console.log(res.next_turn);
				$(".score-container tr").removeClass('user_selected');
				$(".score-container tr[user_id="+res.next_turn+"]").addClass('user_selected');
				if(parseInt(res.next_turn)==parseInt(user_id)){
					$(".len,.wid").removeClass('locked');
				}else{
					$(".len,.wid").addClass('locked');
				}
				
			}
			
			var total_score = (game_size - 1) * (game_size - 1);
			if(res.hasOwnProperty('scores')){
				var tgs = 0;
				for(i=0;i<res.scores.length;i++) {
					$("[user_id='"+res.scores[i].user_id+"'] .user_score").html(res.scores[i].score);
					$("[p_user_id='"+res.scores[i].user_id+"']").css('width',((res.scores[i].score*100)/total_score)+'%');
					tgs += parseInt(res.scores[i].score);
				}
				if(tgs==total_score){
					$(".share_main_btn").click();
				}
			}
			
		}else if(res.action=='new_chat' && res.game_id==game_id){
			
			$('#chatAudio')[0].play();
			$("#chat_list").append('<li><b>'+res.user_name+' : </b>'+res.text+'</li>');
			//$("#chat_list").scrollTop(10000000000);
			$("#chat_list").animate({ scrollTop: $(document).height() }, "slow");
			if(res.user_id==user_id){
				$("#chat_text").val("");
				$("#chat_text").removeAttr("disabled");
			}
			
		}else if(res.action=='player_offline'){
			var item = $("#players tr[user_id="+res.user_id+"]");
			item.find('.player_status').attr('src',offline_image);
			
		}else if(res.action=='player_online'){
			var item = $("#players tr[user_id="+res.user_id+"]");
			if(item.length>0){
				$('#chatAudio')[0].play();
			}
			item.find('.player_status').attr('src',online_image);
		}
		
	}
	
	$(document).ready(function(){
		
		$(".len,.wid").click(function(){
			if(!$(this).hasClass('locked')){
				payload 			= new Object();
				payload.action 		= 'line_clicked';
				payload.game_id		= game_id;
				payload.clicked_line = $(this).attr('data');
				$('body').css('cursor', 'wait');
				$(this).addClass('selected');
				$(".len,.wid").addClass('locked');
				socket.send(JSON.stringify(payload));
			}
		});
		
		$("#chat_text").on("keypress", function(e) {
			if (e.keyCode == 13){
				var text = $(this).val();
				$(this).attr("disabled","disabled");
				if(text.length>0){
					payload = new Object();
					payload.action 		= 'chat_new_text';
					payload.text 		= text;
					payload.game_id		= game_id;
					socket.send(JSON.stringify(payload));
				}
			}
		})
		$("#chat_list").animate({ scrollTop: $(document).height() }, "slow");
		//$("#chat_list").scrollTop(10000000000);
		
		$(".share_main_btn").click(function(){
			
			$(".game_image_all").css("background-image","url(http://poojyam.in/game_image_"+game_id+"_result.png?cc="+Date.now()+")");
			
			if(facebook_abc==1){
				var url = "<?php echo HOME?>game_play/"+game_id+"/playit?publish";
				$.ajax({url:url}).done(function(data){
					if(data=="0"){
						
					}else{
						//$(".share_fb").html("Shared on facebook");
						$(".share_fb").attr("disabled","disabled");
					}
				});
			}
		});
	});
</script>
<style>
	.user_selected{
		background-color: #d3fac7;
	}
	.locked{
		cursor: inherit;
		background-color: #323232 !important;
		border-color: #323232 !important;
	}
	.selected{
		background-color: #A7A7A7 !important;
		border: 1px solid #A7A7A7;
	}
	<?php
	$i=0;
	foreach($game['players'] as $player){
		?>
		.box_user_<?php echo $player['user_id']?>{
			color: <?php echo $colors[$i]?>;
			font-size: 25px;
			text-align: center;
		}
		.box_user_<?php echo $player['user_id']?>::before{
			content: "<?php
			$chars = mb_str_split($player['name']);
			echo strtoupper($chars[0])?>";
		}
		<?php
		$i++;
	}
	?>
</style>
<script src="<?php echo HOME?>/js/bvk.js"></script>
<div class="row row-offcanvas row-offcanvas-right" >
	<div class="col-xs-12 col-sm-9">
		<div class="row">
			<!-- column 1 -->
			<div class="col-sm-6">
				<!-- box 1 -->
				<div class="game-field">
				<?php
				$locked = ($user['user_id']!=$game['next_turn'])?'locked':'';
				$size = $game['size'];
				for($j=0;$j<$size;$j++){        
					for($i=0;$i<$size-1;$i++){
						echo '<div class="dot"></div>';
						$point = implode(',',array($j,$i,$j,$i+1));
						$selected = isset($actions[$point])?'selected':'';
						$point_id = $j.$i.$j.($i+1);
						echo '<div class="len  '.$locked.' '.$selected.'" id="'.$point_id.'" data="'.$point.'"></div>';
					}
					echo '<div class="dot"></div>';
					echo '<div class="clear"></div>';
					
					if($j<$size-1){
						for($i=0;$i<$size;$i++){										
							$point = implode(',',array($j,$i,$j+1,$i));
							$selected = isset($actions[$point])?'selected':'';
							$point_id = $j.$i.($j+1).$i;
							echo '<div class="wid  '.$locked.' '.$selected.'" id="'.$point_id.'" data="'.$point.'"></div>';
							$point = $j.','.$i;
							$point_id = $j.$i;
							$char  = '';
							if($i<$size-1){
								$temp = '';
								if(isset($boxes[$point])){
									$temp = "box_user_".$boxes[$point]['user_id'];
								}
								echo '<div class="box '.$temp.'" id="'.$point_id.'" data="'.$point.'"></div>';
							}										
						}
						echo '<div class="clear"></div>';
					}
				}
			?>
		 </div>
	</div> <!-- /column 1 -->
	
	<!-- column 2 -->
	<div class="col-sm-6">
		<!-- box 3 -->
		<div class="panel panel-default">
			<div class="panel-heading">
			  <h4 class="panel-title"><?php echo $l[28]?><span style="float: right;color: #1717E2;cursor: pointer" data-toggle="modal" data-target="#my-modal-box-help"><?php echo $l[31]?></span></h4>
			</div>
			<div class="panel-body">
			<table class="table table-condensed score-container" id="players" >
				<thead>
					<tr>
						<th colspan=2><?php echo $l[29]?></th>
						<th><?php echo $l[30]?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($game['players'] as $player){
						$class = ($player['user_id']==$game['next_turn'])?'user_selected':'';
						if($player['status']=='online'){
							$player['status_image'] = "player_online.png";
						}else{
							$player['status_image'] = "player_offline.png";
						}
						?>
						<tr user_id="<?php echo $player['user_id']?>" class="<?php echo $class?>">
							<td class="user_name"><img src="<?php echo HOME.'/img/'.$player['status_image']?>" class="player_status"/></td>
							<td class="user_name"><?php echo $player['name']?></td>
							<td class="user_score"><?php echo $player['score']?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<div class="progress progress-striped active">
				<?php
				$pclasses = array("progress-bar-success","progress-bar-warning","progress-bar-danger","progress-bar-info");
				$total_score = ($size-1 ) * ($size-1 );
				$i = 0; 
				foreach($game['players'] as $player){
					$per= (int)(($player['score'] *100 )/$total_score);
					echo '<div p_user_id="'.$player['user_id'].'" class="progress-bar '.$pclasses[$i].'" style="width: '.$per.'%"></div>';
					$i++;
				}
				?>
			</div>
			
			<div>
				<button class="btn btn-primary share_main_btn" style="float: right;margin-top: 15px" data-toggle="modal" data-target="#my-modal-box-share"><?php echo $l[32]?></button>
				<div class="fb-like-box" data-href="https://www.facebook.com/poojyam"  style="width: 200px;overflow: hidden;height: 60px" data-height="100" data-colorscheme="light" data-show-faces="false" data-header="false" data-stream="false" data-show-border="false"></div>			</div>
				</div>                                
			</div>
		</div> <!-- /column 2 -->
	</div><!--/row-->
</div><!--/span-->

<!-- column 3 (sidebar)-->
<div class="col-sm-3 sidebar-offcanvas" id="sidebar">
	<!-- box 6 -->
	<div class="panel panel-info">
		<div class="panel-heading">
		  <h4 class="panel-title"><?php echo $l[33]?></h4>
		</div>
		<div class="panel-body">
			<div style="height:250px;">
				<ul style="list-style: none;padding: 0px;overflow-y: scroll;height: 249px;padding: 5px;" id="chat_list">
				<?php
				foreach($chats as $chat){
					echo '<li><b>';
					echo $game['players'][$chat['user_id']]['name']." : </b>";
					echo $chat['text'];
					echo '</li>';
				}
				?>
				</ul>        
			</div>
			<p>
				<div class="form-group">
				  <input class="form-control" id="chat_text" placeholder="<?php echo $l[34]?>">
				</div>                
			</p>
		</div>
	</div>
</div> <!--column 3 (sidebar) -->
</div><!--/row-->
<?php include BASE."footer.php"; ?>