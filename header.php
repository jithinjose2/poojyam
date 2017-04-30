<?php
$tab = (isset($tab))?$tab:0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="description" content="Poojyam Vettu Kali  is a traditional puzzle game played by many of the south Indian school students, especially during their free hours or breaks or when classes were too boring!! That point of time the game was played using a paper and pen, and it was called as 'Poojyam Vettu Kali' in malayalam language. Here the game is RE-DESIGNED by making a web version of the same. It is engineered to play real time with opponents, enhanced with sharing game experience in facebook. It is a Cool and Intelligent Puzzle Game, You will Love it!!">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $l[17]?></title>
	<link rel="shortcut icon" href="<?php echo HOME?>favicon.ico">
	<link rel="icon" type="image/png" href="<?php echo HOME?>favicon.ico"/>
	<meta property="og:image" content="<?php echo HOME?>img/poojyam_vettu_kali.png" />
	<!-- css stylesheets -->
    <link href="<?php echo HOME?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo HOME?>css/style.css" rel="stylesheet">
	
	<script type="text/javascript">
		<?php
		$jsls = array(70,71,72,73,74,75,61,63);
		foreach($jsls as $jsl){
			echo 'l'.$jsl.' = "'.$l[$jsl].'";';
		}
		?>
	</script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="<?php echo HOME?>js/modernizr.custom.js"></script>
	
    <!-- Stylesheets -->
    <link href="<?php echo HOME?>css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo HOME?>css/authenticity.css" rel="stylesheet">
	
	<!-- Font Awesome CDN -->
	<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	
	<!-- Google Fonts -->
	<link href="<?php echo HOME?>css/css" rel="stylesheet" type="text/css">
	<link href="<?php echo HOME?>css/css(1)" rel="stylesheet" type="text/css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	<script src="http://poojyam.in/js/jquery.filtertable.min.js"></script>
	
    <script src="<?php echo HOME?>js/bootstrap.min.js"></script>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	  
		ga('create', 'UA-49630022-1', 'poojyam.in');
		ga('send', 'pageview');
	  
	  </script>
	
</head>
<body lan="<?php echo $lan?>">
	<!-- modal box -->
    <div class="modal fade" id="my-modal-box-share" tabindex="-1" role="dialog" aria-labelledby="my-modal-box-l" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <div class="modal-title" id="my-modal-box-l">
              <h4 style="text-align: center;font-size:20px;margin: 0px">
				<button class="btn btn-primary share_fb"><?php echo $l[48]?></button>
				&nbsp;&nbsp;&nbsp;
				<a href="http://poojyam.in/game_new">
					<button class="btn btn-primary"> <?php echo $l[49]?></button>
				</a>
			  </h4>
            </div>
          </div><!-- /.modal-header -->
		  <?php if(isset($game)) { ?>
          <div class="modal-body game_image_all" style="text-align: center;background-image: url(http://poojyam.in/game_image_<?php echo $game['game_id']?>_result.png?cc=1);height: 310px;background-size: 100%;">
            </div>
		  <?php } ?>
          </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="modal fade" id="my-modal-box-help" tabindex="-1" role="dialog" aria-labelledby="my-modal-box-l" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <div class="modal-title" id="my-modal-box-l">
              <h4><?php echo $l[31]?></h4>
            </div>
          </div><!-- /.modal-header -->
          <div class="modal-body">            
			<div class="tabs">				
				<div class="tab">
					<input id="tab-1" name="tab-group-1" checked="checked" type="radio">
					<label for="tab-1"><?php echo $l[50]?></label>
				   
					<div class="content">
						<p><?php echo $l[51]?></p>						
						<p><?php echo $l[52]?><img src="<?php echo HOME?>img/social_logins.png"/></p>
						<p><?php echo $l[53]?></p>
						<p><?php echo $l[54]?></p>
					</div> 
				</div>
				
				<div class="tab">
					<input id="tab-2" name="tab-group-1" type="radio">
					<label for="tab-2"><?php echo $l[55]?></label>
				   
					<div class="content">
						<p><?php echo $l[56]?>.</p>
					</div> 
				</div>
				
				<div class="tab">
					<input id="tab-3" name="tab-group-1" type="radio">
					<label for="tab-3"><?php echo $l[57]?></label>
				   
					<div class="content">
						<p><?php echo $l[58]?>.</p>
						<br>
						<div style="width: 59%; float: left;"><p><?php echo $l[59]?></p></div>
						<div style="width: 40%; float: right;"><img src="<?php echo HOME?>img/kali.png" style="float: right; width: 100%;" /></div>
						<div style="style="clear: both;"></div>
					</div> 
				</div>
			</div>
            </div>
          </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->	
	<div class="modal fade" id="my-modal-box-invites" tabindex="-1" role="dialog" aria-labelledby="my-modal-box-l" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <div class="modal-title" id="my-modal-box-l">
              <h4><?php echo $l[60]?></h4>
            </div>
          </div><!-- /.modal-header -->
          <div class="modal-body">
				<table width="100%">
				<?php
				if(isset($user)){
					$invites = Game::getActiveuserInvites($user['user_id']);
					foreach($invites as $invite){
						echo '<tr>';
						echo '<td><img src="'.$invite['image'].'" width=40px/></td>';
						echo '<td width="50%">'.$invite['name'].'</td>';
						echo '<td><a class="btn btn-info" href="http://poojyam.in/game_join/'.$invite['game_id'].'" style="float:right;">'.$l[61].'</a></td>';
						echo '<td><button class="btn btn-warning game_reject" game_id="'.$invite['game_id'].'" user_id="'.$user['user_id'].'" style="float:right;">'.$l[63].'</button></td>';
						echo '</tr>';
					}
				}
				?>
				</table>
          </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
	
	
    
    <!-- fixed navigation bar -->
    <div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
      <div class="container">
		<div class="navbar-header">
            
        </div>
        <div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#b-menu-1">
			  <span class="sr-only">Toggle navigation</span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo HOME?>">
				<img src="<?php echo HOME?>img/logo.png" alt="<?php echo $l[17]?>" height="30" width="30" style="margin-top:-20px; margin-bottom:-20px;">
				&nbsp;<?php echo $l[17]?>
			</a>
        </div>
        <div class="collapse navbar-collapse" id="b-menu-1">
          <ul class="nav navbar-nav navbar-right">
			<?php
			
			if(!empty($ws_status)){
				echo '<li id="status" style="padding-top:15px;margin-right:10px"><span class="label label-default">'.$l[62].'</span></li>';
			}
			
			if(isset($user) && isset($user['user_id'])){
				$user_playing_games = Game::getUserPlayingGames($user['user_id']);
				
				if(count($user_playing_games)>0){
					?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-folder-open"></span>
							&nbsp;&nbsp;<?php echo $l[69]?>&nbsp;
							<b class="caret"></b>
						</a>
						
						<ul class="dropdown-menu">
							<?php
							foreach($user_playing_games as $game_id_t=>$name){
								if(count($name)<=2){
									$name	= str_replace(',',' '.$l[64].' ',$name);
								}else{
									$name	= $name[0]." {$l[65]} ".(count($name)-1)." $l[65] ";
								}
								
								echo '<li ';
								if($tab==100+$game_id_t) echo 'class="active"';
								echo '><a href="'.HOME,'game_play/'.$game_id_t.'/playit">'.$name.'</a></li>';
							}
							?>
						</ul>
					</li>
					<?php 
				}
				
			}
			
			if(isset($user) && is_array($user)) {
				?>
				<li <?php if($tab==1) echo 'class="active"'; ?>><a href="<?php echo HOME?>game_new"><span class="glyphicon glyphicon-play-circle"></span>&nbsp; <?php echo $l[49]?></a></li>
				
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="<?php echo $user['image']?>" width="30px" style="margin-top:-20px; margin-bottom:-20px;"/>
						&nbsp;&nbsp;<?php echo isset($user['name'])?$user['name']:''; ?>&nbsp;&nbsp;
						<b class="caret"></b>
					</a>
					
					<ul class="dropdown-menu">
						<li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $user['name']?></a></li>
						<li><a href="<?php echo HOME?>logout.php"><span class="glyphicon glyphicon-log-out"></span> <?php echo $l[68]?></a></li>
					</ul>
				</li>
			<?php } ?>
          </ul>
        </div> <!-- /.nav-collapse -->
      </div> <!-- /.container -->
    </div> <!-- /.navbar -->
    <div id="slider" class="carousel slide" data-ride="carousel">
        <div class="item active">
            <div class="container" style="padding-top=200px;">
				<div class="message_box">					
				</div>
                <!-- 2-column layout -->