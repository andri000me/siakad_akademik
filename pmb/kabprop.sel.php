<?php
//mengkoneksikan ke server MySQL
session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Data Aplikan");

$sekolah=$_GET['sekolah'];

if ($sekolah=='sma') {
   //Propinsi Sekolah
  $s12 = "select distinct(PropinsiID) as PropinsiID,NamaPropinsi from asalsekolah where PropinsiID != '' and PropinsiID is not Null order by NamaPropinsi";
  $r12=_query($s12);
  $optionPropSekolah = "<option value='' selected>- Propinsi Asal Sekolah -</option>";
  while ($w12 = _fetch_array($r12)) {
  $optionPropSekolah .=  "<option value='$w12[PropinsiID]'>$w12[NamaPropinsi]</option>";
  }
  
  echo '<select name="PropinsiSekolah" onChange="javascript:kabsel(this)" >'.$optionPropSekolah.'</select> <select name="kabupatenSekolah" onChange="javascript:pilsekolah(this)" id="kabsek"></select>';
}
else {
  echo '<div style="cursor:pointer; color:blue;"><input Type="text" name="kabupatenPT" onChange="javascript:pilPT(this)" id="kabPT" size=30> <b>Cari</b> * Masukan sebagian kata dari nama PT Asal</div>';
  }
?>