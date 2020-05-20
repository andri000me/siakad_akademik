<?php
// Author : Wisnu
// Email  : -
// Start  : 25 Maret 2009
?>
<form name="periode" action="pmblap.aplikan.cetak.php" method="post">
<table width="300" align="center">
  <tr>
    <td colspan="3"><div align="center">Pilih Periode </div></td>
  </tr>
  <tr>
    <td align="center">Tahun I : 
      <select name="Tahun1">
	<?
	$tahun = date('Y');
	for ($i=$tahun-20;$i<=$tahun;$i++){
		$sel = ($i==$tahun)? "selected='selected'" : "";
		echo "<option $sel>$i</option>";
	}
	?>
	</select>	</td>
	<td align="center" width="30">-</td>
    <td align="center">Tahun II : 
      <select name="Tahun2">
	<?
	for ($i=$tahun-20;$i<=$tahun;$i++){
		$sel = ($i==$tahun)? "selected='selected'" : "";
		echo "<option $sel>$i</option>";
	}
	?>
	</select>	</td>
  </tr>
  <tr>
  <td colspan="3" align="center"><input type="submit" value="Cetak" />&nbsp;&nbsp;<input type="button" value="Tutup" onclick="Javascript:window.close()" /> </td>
  </tr>
</table>
</form>