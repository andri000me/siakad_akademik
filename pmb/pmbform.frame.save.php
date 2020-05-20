<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 12 Juli 2009

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Tempat Duduk", 1);

// *** Parameters ***
$pmbid = $_REQUEST['pmbid'];
$urutan = $_REQUEST['urutan']+0;
$prodiusmid = $_REQUEST['prodiusmid']+0;
$gel = $_REQUEST['gel'];
$ruangid = $_REQUEST['ruangid'];

//Cek apakah sudah ada
$ada = GetaField('ruangusm', "PMBID='$pmbid' and ProdiUSMID='$prodiusmid' and PMBPeriodID='$gel' and KodeID", KodeID, 'RuangUSMID');

if(empty($ada))
{	$s = "insert into ruangusm set PMBID='$pmbid', 
									ProdiUSMID='$prodiusmid',
									PMBPeriodID='$gel',
									RuangID='$ruangid',
									UrutanDiRuang='$urutan',
									KodeID='".KodeID."',
									TanggalBuat=now(),
									LoginBuat='$_SESSION[_Login]'";
	$r = _query($s);
}
else
{	$ruangusm = GetFields('ruangusm', "RuangUSMID='$ada' and KodeID", KodeID, '*');
	
	if($ruangusm['RuangID'] != $ruangid or $ruangusm['UrutanDiRuang'] != $urutan)
	{	//$s = "delete from ruangusm where RuangUSMID='$ada'";
		//$r = _query($s);
		
		$s = "update ruangusm set RuangID='$ruangid',
									UrutanDiRuang='$urutan',
									KodeID='".KodeID."',
									TanggalEdit=now(),
									LoginEdit='$_SESSION[_Login]' where RuangUSMID='$ada'";
		$r = _query($s);
	}
	else
	{	$s = "update ruangusm set UrutanDiRuang='$urutan',
									KodeID='".KodeID."',
									TanggalEdit=now(),
									LoginEdit='$_SESSION[_Login]' where RuangUSMID='$ada'";
		$r = _query($s);
	}
}
?>
<script>creator.location.reload(true); window.close()</script>
