<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 27 Apr 2014  */
	
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
// cek apakah yang mengakses adalah pihak berwenang
if (!empty($_SESSION['_Login']) || $_SESSION['_LevelID']!=120) $ID= GetSetVar('a'); 
$w = GetFields('pmb p left outer join prodi pr on pr.ProdiID=p.ProdiID',
		"p.PMBID",$ID,
		'p.UangKesehatan,p.AplikanID,p.PMBID,p.PMBPeriodID,p.ProdiID,pr.Nama,p.StatusAwalID');
$bayar = GetFields('pmbklinikbayar', "PMBID", $ID, "*");
$BiayaTes = (empty($bayar['Jumlah']))? GetaField('pmbperiod',"PMBPeriodID",$w['PMBPeriodID'],"BiayaTesKesehatan"):$bayar['Jumlah'];
$BiayaTes = ($w['StatusAwalID']=='S') ? "0":"100000";

?>
<form class='form-horizontal' id='modal-form' method=post action='?mnux=pmb/pmbform.biayateskesehatan'>
<input type=hidden name='gosx' value='SAV'>
<input type=hidden name='cetak' value='Y'>
<input type='hidden' value='<?php echo $ID?>' name='PMBID'>
<input type='hidden' value='<?php echo $w['AplikanID']?>' name='AplikanID'>

<table class=\"table table-striped\">
<tr><td class='inp'>ID Maru</td><td><?php echo $w['PMBID']?></td></tr>
<tr><td class='inp'>Prodi</td><td><?php echo $w['Nama']?></td></tr>
<tr><td class='inp'>Biaya Tes Kesehatan</td><td><input type=text name='Jumlah' value='<?php echo $BiayaTes?>'  /></td></tr>
<tr><td class='inp'>Sudah Diterima?</td><td><input type=radio value='Y' <?php echo ($bayar['Jumlah'] > 0 ? 'checked="checked"':"")?> name='UangKesehatan'> <img src="img/diterima.gif" /> Sudah <input type=radio name='UangKesehatan' value='N'> <img src="img/ditolak.gif" /> Belum</td></tr>
</table>
</form>
