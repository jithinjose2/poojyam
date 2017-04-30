				<footer style="position: fixed;bottom: -20px;width: 100%;left: 0px">
					<?php if(isset($invites) && count($invites)>0) { ?>
					<div class="alert alert-info" style="width:300px; margin:0 auto; margin-bottom:10px;">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<div data-toggle="modal" data-target="#my-modal-box-invites"><?php echo $l[21]?><span class="badge"><?php echo count($invites);?></span></div>
					</div>
					<?php } ?>
                    <nav class="navbar navbar-default" style="border-radius:0px;">
                        <div class="navbar-header">
                            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#b-menu-2">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="collapse navbar-collapse" id="b-menu-2">
                            <div class="container">
								<ul class="nav navbar-nav">
		                            <li class="active"><a href="http://poojyam.in/home"><span class="glyphicon glyphicon-home"></span><?php echo $l[22]?></a></li>
		                            <li data-toggle="modal" data-target="#my-modal-box-help"><a href="#"><span class="glyphicon glyphicon-question-sign"></span> <?php echo $l[23]?></a></li>
									<li>
										<a href="?lan=<?php echo ($lan=='mal')?'eng':'mal';?>">
											<div id="switch_lan" lan="<?php echo $lan?>">
											<span class="glyphicon glyphicon-repeat"></span>
											<?php echo ($lan=='mal')?'Switch To English':'Switch to Malayalam';?>
											</div>
										</a>
									</li>
		                        </ul>
		                        <ul class="nav navbar-nav">
		                            <li data-toggle="modal" data-target="#my-modal-box-share"><a href="#"><span class="glyphicon glyphicon-share"></span><?php echo $l[24]?></a></li>
		                        </ul>
		                        <p class="navbar-text navbar-right">&copy <?php echo $l[25]?> 2014</p>
							</div>                        
						</div>
                    </nav>
                </footer>
            </div>
        </div>
    </div>
	<audio id="chatAudio">
		<source src="<?php echo HOME?>notify.ogg" type="audio/ogg">
		<source src="<?php echo HOME?>notify.mp3" type="audio/mpeg">
		<source src="<?php echo HOME?>notify.wav" type="audio/wav">
	</audio>
    <!-- add javascripts -->
	<?php
	if($DEV_MODE){
		echo '<pre>';
			echo '<h4>$_REQUEST</h4>';
			print_r($_REQUEST);
		echo '</pre>';
		echo '<pre>';
			echo '<h4>$_SESSION</h4>';
			print_r($_SESSION);
		echo '</pre>';
		echo '<pre>';
			echo '<h4>$_COOKIE</h4>';
			print_r($_COOKIE);
		echo '</pre>';
		echo '<pre>';
			echo '<h4>$DBLOGS</h4>';
			print_r($DBLOGS);
		echo '</pre>';
	}
	?>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=672026416193343";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
</body>
</html>