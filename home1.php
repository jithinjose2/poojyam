<?php
include "/var/www/poojyam.in//inc/common.php";
$tab = 2;

include BASE."header.php";

$games = Game::getUserGames($user['user_id']);


?>
Played/playing games
<style>
    .game_card{
        width: 270px;
        height: 120px;
        float: left;
        background-color: #f7f7f7;
        padding: 10px;
        margin: 5px 5px 20px 5px;
        color : #1a1a1a;
    }
    .game_card:hover{
        background-color: #f0f0f0;
    }
    .game_card .new_game{
        font-size: 24px;
        margin: 22px;
    }
    .masterpadding{
        padding: 2px;
    }
</style>
<script>
    var socket;
	var user_id = <?php echo $user['user_id'];?>;
	var secure_hash = '<?php echo $user['secure_hash'];?>';
</script>
<script>
    function checkJson(){
        
    }
</script>
<script src="<?php echo HOME?>/js/bvk.js"></script>
<section id="signin_alt">
    <div class="section-content">
      <div style="padding-top: 10px; padding-bottom: 50px">
          <div class="container">
                <div class="row" data-animation="fadeInUp" style="opacity: 1">
                        
                        <a href="<?php echo HOME?>game_new" style="color:#EAEAEA">
                            <div class="game_card card">
                                <div class="new_game">+ <?php echo $l[49]?></div>
                            </div>
                        </a>
                        
                        <?php
                        foreach($games as $game){
                            if(count($game['players'])>1){
                                ?>
                                <a href="<?php echo HOME?>game_play/<?php echo $game['game_id']?>/playit" style="color:#EAEAEA">
                                    <div class="game_card card">
                                        <div style="margin-bottom:7px;"><?php echo date('jS,F Y',strtotime($game['insert_date']))?></div>
                                        <div style="clear: both;float: left;">
                                            <?php
                                            $i=0;
                                            foreach($game['players'] as $player){
                                                $i++;
                                                if($i==3){
                                                    break;
                                                }
                                                echo '<div class="masterpadding"><img src="'.$player['image'].'" width="20px"/>&nbsp;&nbsp;'.$player['name'] .'</br></div>';
                                            }
                                            if($i==3){
                                                echo '<div class="masterpadding">പിന്നെ '.(count($game['players'])-2).' '.$l[71].'</br></div>';
                                            }
                                            ?>
                                        </div>
                                        <!--<div style="float: right;width: 90px">
                                            <button class="btn btn-block game" style="margin-top: 15px">കളി സ്ഥലം</button>
                                        </div>-->
                                    </div>
                                </a>
                                <?php
                            }
                        } ?>
                        
                        <a href="#" style="color:#EAEAEA">
                            <div class="game_card card">
                                <div class="fb-like-box" data-href="https://www.facebook.com/poojyam"  style="width: 200px;overflow: hidden;height: 80px;margin-top: 15px" data-height="100" data-colorscheme="light" data-show-faces="false" data-header="false" data-stream="false" data-show-border="false"></div>			</div>
                            </div>
                        </a>
                        
                    </table>

                </div>
            </div>
        </div>
    </div>
</section>
<?php
include BASE."footer.php";

