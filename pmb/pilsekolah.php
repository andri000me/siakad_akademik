<?php
//mengkoneksikan ke server MySQL
session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Data Aplikan");

$idKab=$_GET['idKab'];
//men-query yang memiliki id propinsi sama
//$w=GetFields('propinsi', "PropinsiID",$idPropinsi,'NamaDaerah,DaerahID');
$s="SELECT Nama,SekolahID FROM asalsekolah WHERE KabupatenID='$idKab' order by Nama";
$r=_query($s);
while($w = _fetch_array($r)) {
echo"<option value='$w[SekolahID]'>$w[Nama]</option>";
}
echo"<option value='99999999'>SMA/SMK/MA Lain-lain</option>";
?>