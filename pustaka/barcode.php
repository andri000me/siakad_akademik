<?php

$bar = $_GET['bar'];
$bar = ($_GET['gox']==2) ? "*$bar*" : "*$bar*";
$imgX = ($_GET['gox']==2) ? 210: 158;
$imgY = ($_GET['gox']==2) ? 35 : 50;
$image = ($_GET['gox']==2) ? imagecreatefrompng("barcode_mhsw.png"):imagecreatefrompng("barcode.png");
//$image = imagecreatefrompng("barcode.png");

$textcolor = imagecolorallocate($image, 0,0,0);

//$font =  ($_GET['gox']==2) ? "fre3of9x_0.ttf" : "Interleaved 2of5.ttf"; // 
$font =  ($_GET['gox']==2) ? "fre3of9x_0.ttf" : "fre3of9x_0.ttf"; // 
$fontsize = ($_GET['gox']==2) ? 31 : 27;
$angle = 0;
$box = imagettfbbox($fontsize, $angle, $font, $bar);
$x = (int)($imgX - $box[4]) / 2;
$y = (int)($imgY - $box[5]) / 2;
imagettftext($image, $fontsize, $angle, $x, $y, $textcolor, $font, $bar);

header("Content-type: image/png");
imagepng($image);
imagedestroy ($image);
?>