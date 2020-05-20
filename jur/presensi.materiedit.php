<?php
// Author : Arisal Yanuarafi
// Email  : arisal.yanuarafi@yahoo.com
// Start  : 24 Oktober 2012

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit Materi", 1);

// *** Parameters ***
$pid = $_REQUEST['pid'];
$jid = $_REQUEST['jid'];

// *** Main ***
TampilkanJudul("Edit Materi");
$gos = (empty($_REQUEST['gos']))? 'Materi' : $_REQUEST['gos'];
$gos($pid,$jid);
  echo "<script>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=Edit&JadwalID=$jid';
    self.close();
    return false;
  }
  </script>";
function Materi ($pid,$jid) {
$Materi = GetaField('presensi',"PresensiID", $pid, 'Catatan');
$Materi = nl2br($Materi);
echo "<form name='EditMateri' method=POST action='../$_SESSION[mnux].materiedit.php'>
		<table class=box width=100% cellspacing=1>
		<input type=hidden name=gos value='SimpanMateri'>
		<input type=hidden name=pid value='$pid'>
		<input type=hidden name=jid value='$jid'>
		<tr>
			<td class='inp'>Materi:</td>
			<td class='ul1'>
				<textarea name='Materi' id='EditMateris' rows=5 cols=40>$Materi</textarea>
			</td>
		</tr>
		<tr>
			<td colspan=2 class='ul1' align=center><input type=submit value=Simpan>
										<input type=button name='Tutup' value='Batal' onClick=\"ttutup()\" /></td>
		</tr>
		</table>";
}

function SimpanMateri($pid,$jid) {
$Materi = sqling($_REQUEST['Materi']);
$Materi = nl2br($Materi);
$s = "update presensi set Catatan='$Materi' where PresensiID='$pid'";
$r = _query($s);
echo "<script>
opener.location='../index.php?mnux=$_SESSION[mnux]&gos=Edit&JadwalID=$jid';
    self.close();
	</script>";
}
		