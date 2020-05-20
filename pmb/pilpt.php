<?php
//mengkoneksikan ke server MySQL
session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Data Aplikan");

$cariPT=$_GET['cariPT'];
//men-query yang memiliki id propinsi sama
//$w=GetFields('propinsi', "PropinsiID",$idPropinsi,'NamaDaerah,DaerahID');
$s="SELECT Kota,Nama,PerguruanTinggiID FROM perguruantinggi WHERE Nama like '%$cariPT%' order by Nama";
$r=_query($s);
while($w = _fetch_array($r)) {
echo"<option value='$w[PerguruanTinggiID]'>$w[PerguruanTinggiID] - <b>$w[Nama]</b> - $w[Kota]</option>";
}
echo"<option value='99999999'>99999999 - Perguruan Tinggi Lainnya</option>";
?>