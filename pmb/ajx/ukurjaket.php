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
$w = GetFields('pmb',
		"PMBID",$ID,
		'UkuranJaket,AplikanID,MhswID');
?>
<form class='form-horizontal' id='modal-form' method=post action='?mnux=pmb/pmblulus.jaket'>
<input type=hidden name='gosx' value='SAV'>
<input type='hidden' value='<?php echo $ID?>' name='PMBID'>
<input type='hidden' value='<?php echo $w['MhswID']?>' name='MhswID'>

<table class=\"table table-striped\">
<tr><td class='inp'>Ukuran Jaket</td>
									<td><input type=radio value='S' name='UkuranJaket'> S <br />
										<input type=radio value='M' name='UkuranJaket'> M <br />
                                        <input type=radio value='L' name='UkuranJaket'> L <br />
                                        <input type=radio value='XL' name='UkuranJaket'> XL<br />
                                        <input type=radio value='XXL' name='UkuranJaket'> XXL<br />
                                        <input type=radio value='XXXL' name='UkuranJaket'> XXXL</td></tr>
</table>
</form>
