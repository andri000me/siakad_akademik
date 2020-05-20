<?php
//mengkoneksikan ke server MySQL
session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Data Aplikan");

$idPropinsi=$_GET['idPropinsi'];
//men-query yang memiliki id propinsi sama
//$w=GetFields('propinsi', "PropinsiID",$idPropinsi,'NamaDaerah,DaerahID');
$s="SELECT DISTINCT(NamaKabupaten) as NamaKabupaten,KabupatenID FROM asalsekolah WHERE PropinsiID='$idPropinsi' order by NamaKabupaten";
$r=_query($s);
echo"<option value=''> - Pilih Kabupaten Sekolah -</option>";
while($w = _fetch_array($r)) {
echo"<option value='$w[KabupatenID]'>$w[NamaKabupaten]</option>";
}
?>