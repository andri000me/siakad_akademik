<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 10 Juli 2009

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Pilih Kursi");

// *** Parameters ***
$gel = sqling($_REQUEST['gel']);
$id = sqling($_REQUEST['id']);

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'PilihKursi' : $_REQUEST['gos'];
$gos($gel, $id);

// *** Functions ***
function PilihKursi($gel, $id)
{	$arrProdi = array();
	$pmb = GetFields('pmb p left outer join pmbformulir pf on p.PMBFormulirID=pf.PMBFormulirID', "p.PMBID='$id' and p.KodeID", KodeID, "p.Pilihan1, p.Pilihan2, p.Pilihan3, p.PMBID, p.Nama, p.ProgramID, pf.USM, pf.Wawancara, pf.Nama as _NamaForm");
	for($i = 1; $i <= 3; $i++) $arrProdi[] = $pmb["Pilihan$i"];
	foreach ($arrProdi as $key => $value) {
      if (is_null($value) || $value=="") unset($arrProdi[$key]);
    } 
	$arrProdi = array_unique($arrProdi);
	
	foreach($arrProdi as $perprodi)
	{	$prodistring .= (empty($prodistring))? "$perprodi": " / $perprodi"; 
	}
	echo "<p><table class=box cellspacing=2 cellpadding=4 width=500 align=center>
			  <tr><td class=inp width=200>PMBID:</td>
				 <td class=ul><b>$pmb[PMBID]</b></td></tr>
			  <tr><td class=inp>Nama:</td>
				  <td class=ul><b>$pmb[Nama]</b></td></tr>
			  <tr><td class=inp>Program:</td>
				  <td class=ul><b>$pmb[ProgramID]</b></td></tr>
			  <tr><td class=inp>Program Studi Pilihan:</td>
				  <td class=ul><b>$prodistring</b.</td></tr>
		  </table></p>";
	
	if($pmb['USM'] == 'Y')
	{	
		$prodistring = ''; 
		foreach($arrProdi as $perprodi)
		{	$prodistring .= (empty($prodistring))? "(INSTR(concat('|', ProdiID, '|'), concat('|', '$perprodi', '|'))" :
													" OR INSTR(concat('|', ProdiID, '|'), concat('|', '$perprodi', '|'))";
		}
		$prodistring .= ')';
		
		$s = "select ProdiUSMID from prodiusm where KodeID='".KodeID."' and PMBPeriodID='$gel' and 
				$prodistring";
		$r = _query($s);
		$n = 0;
		while($w = _fetch_array($r))
		{	
			$n++;
			echo  "<Iframe name='frame$n' src='../$_SESSION[mnux].frame.php?PMBID=$id&ProdiUSMID=$w[ProdiUSMID]&gel=$gel' align=center width=99% height=750 frameborder=0></Iframe>";
		}
	}
	else
	{	echo "<font size=2><b>Tidak ada Ujian Saringan Masuk yang dijadwalkan untuk Formulir $pmb[_NamaForm].</b></font>&nbsp;&nbsp;<input type=button name='Tutup' value='Tutup' onClick=\"window.close()\"";
	}
	
	if($pmb['Wawancara'] == 'Y')
	{	/*$prodistring = ''; 
		foreach($arrProdi as $perprodi)
		{	$prodistring .= (empty($prodistring))? "(INSTR(concat('|', ProdiID, '|'), concat('|', '$perprodi', '|'))" :
													" OR INSTR(concat('|', ProdiID, '|'), concat('|', '$perprodi', '|'))";
		}
		$prodistring .= ')';
		$s = "select WawancaraUSMID from wawancarausm where KodeID='".KodeID."' and PMBPeriodID='$gel' and 
				$prodistring";
		$r = _query($s);
		while($w = _fetch_array($r))
		{*/	
			$n++;
			echo  "<Iframe name='frame$n' src='../$_SESSION[mnux].framewawancara.php?PMBID=$id&gel=$gel' align=center width=99% height=500 frameborder=0></Iframe>";
		//}
	}
	else
	{	echo "<font size=2><b>Tidak ada Wawancara yang dijadwalkan untuk Formulir $pmb[_NamaForm].</b></font>&nbsp;&nbsp;<input type=button name='Tutup' value='Tutup' onClick=\"window.close()\"";
	}
}

?>
