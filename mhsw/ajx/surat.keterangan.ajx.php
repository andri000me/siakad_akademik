<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 17 Sept 2013  */
	
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";

if ($_SESSION['_LevelID']==1) { $MhswID = GetSetVar('MhswID'); }
if ($_SESSION['_LevelID']==120) {$MhswID = $_SESSION['_Login'];}
$Untuk = $_GET['d'];
$w = GetFields('mhsw', "MhswID", $MhswID, '*');
echo "<form class='form-horizontal' enctype='multipart/form-data' method=post action='?'>
<input type=hidden name='gos' value='SAV'>
<input type=hidden name='d' value='".$Untuk."'>
<input type='hidden' value='$MhswID' name='MhswID'>
<label class='control-label'>".(($Untuk=='0')? 'Nama Ayah':'Nama Ibu')."</label><div class='controls'><input type=text Name='Nama' size=30 value='".(($Untuk=='0')? $w['NamaAyah']:$w['NamaIbu'])."'></div>
<label class='control-label'>NIP/NRP/NIK/No.Pening</label><div class='controls'><input type=text Name='NIP' size=20 value='".(($Untuk=='0')? $w['NIPAyah']:$w['NIPIbu'])."'></div>
<label class='control-label'>Pangkat/Golongan</label><div class='controls'><input type=text Name='PangkatGol' size=30 value='".(($Untuk=='0')? $w['PangkatGolAyah']:$w['PangkatGolIbu'])."'></div>
<label class='control-label'>Instansi</label><div class='controls'><input type=text Name='Instansi' size=60 value='".(($Untuk=='0')? $w['InstansiAyah']:$w['InstansiIbu'])."'></div>
 							<div class=\"form-actions\">
								<button type=\"submit\" class=\"btn btn-primary\">Cetak</button>
								<button class=\"btn\" type=button onclick=\"location.href='?mnux=loginprc&gos=berhasil'\">Batal</button>
							  </div></form>";
?>