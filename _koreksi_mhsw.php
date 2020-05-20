<?php
// Start  : 08/01/2009

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Ubah Semua ID Institusi");

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'fnKonfirmasi' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function fnKonfirmasi() {
  echo Konfirmasi("Konfirmasi",
    "Anda akan mengubah data mhsw di bawah ini:<br>
		&bull; 2008TEMPMA001 <br>
		&bull; 2008TEMPMA002 <br>
		&bull; 2008TEMPMA003 <br>
		&bull; 2008TEMPAK004 <br>
		&bull; 2008TEMPAK001 <br>
		&bull; 2008TEMPAK003 
    <hr size=1 color=silver />
    <input type=button name='btnProses' value='Proses'
    onClick=\"location='?gos=fnProses'\" />");
}
function fnProses() {
  $arrMhswID = array('2008TEMPMA001', '2008TEMPMA002', '2008TEMPMA003', '2008TEMPAK004', '2008TEMPAK001', '2008TEMPAK003');
  
  $n = 0;
  foreach($arrMhswID as $MhswIDLama)
  {   $n++;
	  $NIM = GetNextNIM(substr($MhswIDLama, 0, 4), GetFields('mhsw', "MhswID='$MhswIDLama' and KodeID", KodeID, '*'));
		// Proses
		// PMB
		$arrTablesToUpdate = array('bayarmhsw', 'bipotmhsw', 'khs', 'krs', 'pmb', 'presensimhsw', 'prosesstatusmhsw');
		foreach($arrTablesToUpdate as $table)
		{	$s1 = "update $table set MhswID = '$NIM' where MhswID='$MhswIDLama'";
			$r1 = _query($s1);
		}
		$s1 = "update mhsw set MhswID = '$NIM', Login = '$NIM', MhswIDLama='$MhswIDLama' where MhswID='$MhswIDLama'";
		$r1 = _query($s1);
  }
    echo "<li>Table $table, diproses: $n</li>";
  echo "</ol>";
  echo "<font size=+1>Selesai.</font>";
}



?>
