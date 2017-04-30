<?php
include "/var/www/poojyam.in//inc/common.php";
include "/var/www/poojyam.in//classes/Bot.php";

if(!(isset($user) && ($user['user_id']==1 || $user['user_id']==4))){
    echo "end";
    die();
}

$game_id = (isset($_REQUEST['game_id']))?intval($_REQUEST['game_id']):4;

$bot = new Bot();
echo "next best move for game $game_id is ";

echo "\n</br>Algoritham 1 : ".$bot->nextAction($game_id);
echo "\n</br>Algoritham 2 : ".$bot->nextAction2($game_id);


//die();

$users = User::getUsers();

$keys = array_keys($users[0]);

echo '<table>';
echo '<tr>';
foreach($keys as $Key){
    echo '<td>'.$Key.'</td>';
}
echo '</tr>';
foreach($users as $user){
    echo '<tr>';
    foreach($user as $Key){
        echo '<td>'.$Key.'</td>';
    }
    echo '</tr>';

}
echo '</table>';

?>