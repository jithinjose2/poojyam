<?php
include "/var/www/poojyam.in//inc/common.php";
$tab = 2;

include BASE."header.php";

$games = Game::getUserGames($user['user_id']);


?>
Played/playing games
<style>
    .game_card{
        width: 250px;
        float: left;
        height: 100px;
        background-color: #70708c;
        padding: 10px;
        margin: 17px
    }
    .game_card:hover{
        background-color: #8282D6;
    }
    .game_card .new_game{
        font-size: 24px;
        margin: 22px;
    }
    .masterpadding{
        padding: 2px;
    }
</style>
<script src="<?php echo HOME?>/js/bvk.js"></script>
<section id="signin_alt" class="authenty signin-alt">
    <div class="section-content">
      <div class="wrap" style="padding-top: 10px">
          <div class="container">
                <div class="row" data-animation="fadeInUp" style="opacity: 1">
                        
                        <div href="<?php echo HOME?>game_new">
                            <div class="game_card">
                                <div class="new_game">+ New Game</div>
                            </div>
                        </div>
                        
                        <?php
                        foreach($games as $game){
                            if(count($game['players'])>1){
                                ?>
                                <a href="<?php echo HOME?>game_play/<?php echo $game['game_id']?>/playit" style="color:#EAEAEA">
                                    <div class="game_card">
                                        <div style="margin-bottom:7px;"><?php echo date('jS,F Y',strtotime($game['insert_date']))?></div>
                                        <div style="clear: both;float: left;width: 150px;">
                                            <?php
                                            foreach($game['players'] as $player){
                                                echo '<div class="masterpadding"><img src="'.$player['image'].'" width="20px"/>&nbsp;&nbsp;'.$player['name'] .'</br></div>';
                                            }
                                            ?>
                                        </div>
                                        <div style="float: left;width: 75px">
                                            <button class="btn btn-block game" style="margin-top: 15px"><?php echo $l[70]?></button>
                                        </div>
                                    </div>
                                </a>
                                <?php
                            }
                        } ?>
                        
                    </table>

                </div>
            </div>
        </div>
    </div>
</section>
<?php
include BASE."footer.php";

