<?php
$access = "public";
include "/var/www/poojyam.in//inc/common.php";

if(empty($_REQUEST['email_hash'])){
    header('location:'.HOME);
    die();
}

include BASE."header.php";

if(user::varifyEmail($_REQUEST['email_hash'])){
    $message =  $l[19];
}else{
    $error =  $l[20];
}
?>
<?php showAlert() ?>
<?php
include BASE."footer.php";
