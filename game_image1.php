<?php
$access = "public";

include "/var/www/poojyam.in//inc/common.php";

$game = Game::getGameDetails($_REQUEST['game_id']);
$boxes = Game::getGameBox($_REQUEST['game_id']);

$temp   = $game['players'];
$p1     = array_pop($temp);
$p2     = array_pop($temp);

if($p2['score']>$p1['score']){
    $t1 = $p2;
    $p2 = $p1;
    $p1 = $t1;
}

if($p1['score']==$p2['score']){
    $status = "Equals";
}else{
    $status = "Beats";
}


$player1 = $p1['name'];
$player2 = $p2['name'];
$score = $p1['score'].' / '.$p2['score'];

$back_image = BASE.'img/fb_share.png';
$font       = BASE."fonts/Melbourne-Bold.ttf";
$font_mal   = BASE."fonts/AnjaliOldLipi.ttf";

$image_1 = imagecreatefrompng($back_image);
imagealphablending($image_1,true);

$white  = imagecolorallocate($image_1, 255,255, 255);
$color1 = imagecolorallocate($image_1, 255,200, 100);
$color2 = imagecolorallocate($image_1, 50,50, 50);

$colors = array(
    imagecolorallocate($image_1,92, 184, 92),
    imagecolorallocate($image_1,240, 173, 78),
    imagecolorallocate($image_1,217, 83, 79),
    imagecolorallocate($image_1,91, 192, 222)
);

$l = 0;
foreach($game['players'] as &$pl){
    $pl['color_m'] = $colors[$l];
    $l++;
}


$font_size = 12;

$details1 = calculateTextBox($font_size,0,$font_mal,ucwords($player1));
imagettftext($image_1, $font_size, 0, $details1['left']+40, $details1['top']+490,$color2,$font_mal,ucwords($player1));

$details1 = calculateTextBox($font_size,0,$font_mal,$p1['score']);
imagettftext($image_1, $font_size, 0, $details1['left']+225, $details1['top']+490,$color2,$font_mal,$p1['score']);



$details1 = calculateTextBox($font_size,0,$font_mal,ucwords($player2));
imagettftext($image_1, $font_size, 0, $details1['left']+40, $details1['top']+523,$color2,$font_mal,ucwords($player2));

$details1 = calculateTextBox($font_size,0,$font_mal,$p2['score']);
imagettftext($image_1, $font_size, 0, $details1['left']+225, $details1['top']+523,$color2,$font_mal,$p2['score']);



//// DRAW SCORE GRID


/*$px     = 60 + (strlen($player2) / 2);
imagettftext($image_1, 100, 0, $px, 300,$color2,$font_mal,$player2);*/
//print_r($game);
//print_r($boxes);
for($i=0;$i<$game['size'];$i++){
    for($j=0;$j<$game['size'];$j++){
        if(isset($boxes["$i,$j"])){
            
            $plyer = $game['players'][$boxes["$i,$j"]['user_id']];
            
            $first_letter = mb_str_split($plyer['name'],0,1);
            $first_letter = strtoupper($first_letter[0]);
            $details1 = calculateTextBox(20,0,$font_mal,$first_letter);
            
            $left = $details1['left']+31+($j*56);
            $top = $details1['top']+95+($i*53);
            
            $left   = floor($left + (55/2) -($details1['width']/2));
            $top    = floor($top + (55/2) -($details1['height']/2));
            
            imagettftext($image_1, 20, 0, $left, $top,$plyer['color_m'],$font_mal,$first_letter);
        }
    }
}


//print_r(calculateTextBox(100,0,$font_mal,$player1));

//die();














function drawline($x1,$y1,$x2,$y2){
    global $image_1,$xo,$yo;
    $x1 = $xo+$x1;
    $x2 = $xo+$x2;
    $y1 = $yo-$y1;
    $y2 = $yo-$y2;
    imageline($image_1,$x1,$y1,$x2,$y2,IMG_COLOR_BRUSHED);
}

function mergeImages(&$source,$image,$x,$y,$width,$height,$scalebig=false){
    $image_size = getimagesize($image);
    
    if(strpos($image,".png")!==false){
        $image_2    = imagecreatefrompng($image);
        imagealphablending($image_2, true);
        imagesavealpha($image_2, true);
    }else{
        $image_2    = imagecreatefromjpeg($image);
    }
    
    
    if($width<$image_size[0] || $height<$image_size[1]){
        $f  = (($width/$image_size[0])<($height/$image_size[1]))?($width/$image_size[0]):($height/$image_size[1]);
        $old[0] = $image_size[0];
        $old[1] = $image_size[1];
        $image_size[0]  = $f * $image_size[0];
        $image_size[1]  = $f * $image_size[1];
        imagecopyresampled($image_2, $image_2, 0, 0, 0, 0, $image_size[0], $image_size[1], $old[0], $old[1]);
        
    }elseif($width>$image_size[0] && $height>$image_size[1] && $scalebig){
        
        $f  = (($width/$image_size[0])<($height/$image_size[1]))?($width/$image_size[0]):($height/$image_size[1]);
        $old[0] = $image_size[0];
        $old[1] = $image_size[1];
        $image_size[0]  = $f * $image_size[0];
        $image_size[1]  = $f * $image_size[1];
        
        $image_3 = imagecreatetruecolor($image_size[0], $image_size[1]);
        imagealphablending($image_3, true);
        imagesavealpha($image_3, true);
        imagecopyresampled($image_3, $image_2, 0, 0, 0, 0, $image_size[0], $image_size[1], $old[0], $old[1]);
        $image_2 = $image_3;
        
    }
    
    $x      = $x + ($width/2) - ($image_size[0]/2);
    $y      = $y + ($height/2) - ($image_size[1]/2);
    
    imagecopy($source, $image_2, $x, $y, 0, 0, $image_size[0], $image_size[1]);
}

function strslice($str,$len,$append="..."){
    if(strlen($str)>$len){
        $str    = substr($str,0,$len).$append;
    }
    return $str;
}


function calculateTextBox($font_size, $font_angle, $font_file, $text) { 
  $box   = imagettfbbox($font_size, $font_angle, $font_file, $text); 
  if( !$box ) 
    return false; 
  $min_x = min( array($box[0], $box[2], $box[4], $box[6]) ); 
  $max_x = max( array($box[0], $box[2], $box[4], $box[6]) ); 
  $min_y = min( array($box[1], $box[3], $box[5], $box[7]) ); 
  $max_y = max( array($box[1], $box[3], $box[5], $box[7]) ); 
  $width  = ( $max_x - $min_x ); 
  $height = ( $max_y - $min_y ); 
  $left   = abs( $min_x ) + $width; 
  $top    = abs( $min_y ) + $height; 
  // to calculate the exact bounding box i write the text in a large image 
  $img     = @imagecreatetruecolor( $width << 2, $height << 2 ); 
  $white   =  imagecolorallocate( $img, 255, 255, 255 ); 
  $black   =  imagecolorallocate( $img, 0, 0, 0 ); 
  imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black); 
  // for sure the text is completely in the image! 
  imagettftext( $img, $font_size, 
                $font_angle, $left, $top, 
                $white, $font_file, $text); 
  // start scanning (0=> black => empty) 
  $rleft  = $w4 = $width<<2; 
  $rright = 0; 
  $rbottom   = 0; 
  $rtop = $h4 = $height<<2; 
  for( $x = 0; $x < $w4; $x++ ) 
    for( $y = 0; $y < $h4; $y++ ) 
      if( imagecolorat( $img, $x, $y ) ){ 
        $rleft   = min( $rleft, $x ); 
        $rright  = max( $rright, $x ); 
        $rtop    = min( $rtop, $y ); 
        $rbottom = max( $rbottom, $y ); 
      } 
  // destroy img and serve the result 
  imagedestroy( $img ); 
  return array( "left"   => $left - $rleft, 
                "top"    => $top  - $rtop, 
                "width"  => $rright - $rleft + 1, 
                "height" => $rbottom - $rtop + 1 ); 
} 


header('Content-Type: image/png');
//$image_2 = imagecreatetruecolor(225, 135);
//imagecopyresampled($image_2,$image_1,0,0,0,0,225,135,900,540);
imagepng($image_1);




