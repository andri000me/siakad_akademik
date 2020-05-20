<?php
session_start();

$possible = '789bcdfghjkmnpqrstvwxyz';
$characters = 5;
$str = '';
$i = 0;

while ($i < $characters) { 
  $str .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
  $i++;
}
$_SESSION['captchacode'] = $str;


$imgX = 340;
$imgY = 70;
$image = imagecreatefrompng("images/captchabg.png");

$textcolor = imagecolorallocate($image, 46,40,31);

$font = "cts.ttf"; 
$fontsize = 30;
$angle = 16;
$box = imagettfbbox($fontsize, $angle, $font, $_SESSION['captchacode']);
$x = (int)($imgX - $box[4]) / 2;
$y = (int)($imgY - $box[5]) / 2;
imagettftext($image, $fontsize, $angle, $x, $y, $textcolor, $font, $_SESSION['captchacode']);

header("Content-type: image/png");
imagepng($image);
imagedestroy ($image);
?>