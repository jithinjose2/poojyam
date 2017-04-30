<?php
$access = "public";

include "/var/www/poojyam.in//inc/common.php";

session_destroy();

header('location:'.HOME);
die();