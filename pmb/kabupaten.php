<?php
//mengkoneksikan ke server MySQL
session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Data Aplikan");

$idPropinsi=$_GET['idPropinsi'];
//men-query yang memiliki id propinsi sama
//$w=GetFields('propinsi', "PropinsiID",$idPropinsi,'NamaDaerah,DaerahID');
$s="SELECT NamaDaerah,DaerahID FROM propinsi WHERE PropinsiID='$idPropinsi' order by NamaDaerah";
$r=_query($s);
while($w = _fetch_array($r)) {
echo"<option value='$w[DaerahID]'>$w[NamaDaerah]</option>";
}
?>