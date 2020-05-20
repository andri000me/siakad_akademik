<?php session_start(); $MhswID=$_GET['a'];
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
$w = GetFields('ta_surat', "MhswID", $MhswID, "*");?>
<form class='form-horizontal' id='modal-form' action='jur/ta.surat.pengantar.php' method="post" target="_blank">
<input type='hidden' value='<?=$MhswID?>' name='MhswID'>
<table class=\"table table-striped\">
<tr><td class='inp'>Tujuan surat<br>Contoh: <b>Bapak Direktur Utama</b></td>
	<td><input type="text" name="Tujuan" size="30" maxlength="100" value="<?=$w['Tujuan']?>" /></td></tr>
<tr><td class='inp'>c.q. surat<br>misal: <b>c.q. Bapak Kepala Personalia</b><br>Biarkan kosong bila tidak ada c.q. surat</td>
	<td><input type="text" name="cq" size="30" maxlength="100" value="<?=$w['cq']?>" /></td></tr>
<tr><td class='inp'>Nama instansi/perusahaan<br>Contoh: <b>PT. Angin Ribut</b></td>
	<td><input type="text" name="Instansi" size="30" maxlength="100" value="<?=$w['Instansi']?>" /></td></tr>
<tr><td class='inp'>Alamat instansi/perusahaan<br>Contoh: <b>Padang</b></td>
	<td><input type="text" name="Kota" size="20" maxlength="50" value="<?=$w['Kota']?>" /></td></tr>
</table>
</form>

