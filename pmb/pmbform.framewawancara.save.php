<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 12 Juli 2009

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Tempat dan Waktu Wawancara", 1);

// *** Parameters ***
$pmbid = $_REQUEST['pmbid'];
$urutan = $_REQUEST['urutan']+0;
$wawancarausmid = $_REQUEST['wawancarausmid']+0;
$gel = $_REQUEST['gel'];
$ruangid = $_REQUEST['ruangid'];
$jam = $_REQUEST['jam'];
$arrJam = explode('/', $jam);

//Cek apakah sudah ada
$ada = GetaField('wawancara', "PMBID='$pmbid' and PMBPeriodID='$gel' and KodeID", KodeID, 'WawancaraID');

if(empty($ada))
{	$s = "insert into wawancara set PMBID='$pmbid', 
									WawancaraUSMID='$wawancarausmid',
									JamMulaiWawancara = '$arrJam[0]',
									JamSelesaiWawancara = '$arrJam[1]',
									PMBPeriodID='$gel',
									RuangID='$ruangid',
									UrutanDiRuang='$urutan',
									KodeID='".KodeID."',
									TanggalBuat=now(),
									LoginBuat='$_SESSION[_Login]'";
	$r = _query($s);
}
else
{	$wawancara = GetFields('wawancara', "WawancaraID='$ada' and KodeID", KodeID, '*');
	
	if($wawancara['RuangID'] != $ruangid or $wawancara['UrutanDiRuang'] != $urutan)
	{	//$s = "delete from ruangusm where RuangUSMID='$ada'";
		//$r = _query($s);
		
		$s = "update wawancara set RuangID='$ruangid',
									UrutanDiRuang='$urutan',
									JamMulaiWawancara = '$arrJam[0]',
									JamSelesaiWawancara = '$arrJam[1]',
									KodeID='".KodeID."',
									TanggalEdit=now(),
									LoginEdit='$_SESSION[_Login]' where WawancaraID='$ada'";
		$r = _query($s);
	}
	else
	{	$s = "update wawancara set UrutanDiRuang='$urutan',
									JamMulaiWawancara = '$arrJam[0]',
									JamSelesaiWawancara = '$arrJam[1]',
									KodeID='".KodeID."',
									TanggalEdit=now(),
									LoginEdit='$_SESSION[_Login]' where WawancaraID='$ada'";
		$r = _query($s);
	}
}

function GetJam($w, $pmb, $wawancarausm, $n1)
{	
	if($PMBID == $w['PMBID']) $class = 'wrn';
	else $class = 'ul1';

	$tempkapmax = $wawancarausm['Kapasitas'];
	$tempduration = $wawancarausm['PanjangWaktu'];
	$tempminutes = ((substr($wawancarausm['JamSelesai'], 0, 2) - substr($wawancarausm['JamMulai'], 0, 2))*60) +
					(substr($wawancarausm['JamSelesai'], 3, 2) - substr($wawancarausm['JamMulai'], 3, 2));
	$JamMulai = '';
	$JamSelesai = '';
	
	if($tempkapmax == 0 and $tempduration == 0)
	{	$JamMulai = $wawancarausm['JamMulai'];
		$JamSelesai = $wawancarausm['JamSelesai'];
	}
	else if($tempduration == 0)
	{	if($n1 <= $tempkapmax)
		{	$JamMulai = $wawancarausm['JamMulai'];
			$JamSelesai = $wawancarausm['JamSelesai'];
		}	
		else
		{	//$JamMulai = $wawancarausm['JamMulai'];
			//$JamSelesai = $wawancarausm['JamSelesai'];	
		}
	}
	else if($tempkapmax == 0)
	{	
		$tempkapmax2 = floor($tempminutes/$tempduration);
		$tempkapmax2 = ($tempkapmax < 1)? 1 : $tempkapmax2; 
		$rotatepos = $n1%$tempkapmax2;
		
		$tempminutesafter = ($rotatepos)*$tempduration;
		$tempthehour = substr($wawancarausm['JamMulai'], 0, 2) + floor($tempminutesafter/60);
		$temptheminute = substr($wawancarausm['JamMulai'], 3, 2) + $tempminutesafter%60;
		if($temptheminute>=60)
		{	$temptheminute = $temptheminute-60;
			$tempthehour += 1;
		}
		$tempthehour = ($tempthehour < 10)? '0'.$tempthehour : $tempthehour;
		$temptheminute = ($temptheminute < 10)? '0'.$temptheminute : $temptheminute;
		$JamMulai = $tempthehour.':'.$temptheminute.':00';
		
		$tempminutesafter = ($rotatepos+1)*$tempduration;
		$tempthehour = substr($wawancarausm['JamSelesai'], 0, 2) + floor($tempminutesafter/60);
		$temptheminute = substr($wawancarausm['JamSelesai'], 3, 2) + $tempminutesafter%60;
		if($temptheminute>=60)
		{	$temptheminute = $temptheminute-60;
			$tempthehour += 1;
		}
		$tempthehour = ($tempthehour < 10)? '0'.$tempthehour : $tempthehour;
		$temptheminute = ($temptheminute < 10)? '0'.$temptheminute : $temptheminute;
		$JamSelesai = $tempthehour.':'.$temptheminute.':00';
	}
	else
	{	
		$tempmax2 = floor($tempminutes/$tempduration);
		$tempkapmax2 = ($tempkapmax < 1)? 1 : $tempkapmax2;
		$tempcountpersession = ceil($tempkapmax/$tempmax2);
		//echo "MAX2: $tempmax2, KAP: $tempmax2, COUNT: $tempcountpersession";
		if($n1 <= $tempkapmax)
		{	$tempposition = floor(($n1)/$tempcountpersession);
			
			$tempminutesafter = ($tempposition)*$tempduration;
			$tempthehour = substr($wawancarausm['JamMulai'], 0, 2) + floor($tempminutesafter/60);
			$temptheminute = substr($wawancarausm['JamMulai'], 3, 2) + $tempminutesafter%60;
			if($temptheminute>=60)
			{	$temptheminute = $temptheminute-60;
				$tempthehour += 1;
			}
			$tempthehour = ($tempthehour < 10)? '0'.$tempthehour : $tempthehour;
			$temptheminute = ($temptheminute < 10)? '0'.$temptheminute : $temptheminute;
			$JamMulai = $tempthehour.':'.$temptheminute.':00';
			
			$tempminutesafter = ($tempposition+1)*$tempduration;
			$tempthehour = substr($wawancarausm['JamMulai'], 0, 2) + floor($tempminutesafter/60);
			$temptheminute = substr($wawancarausm['JamMulai'], 3, 2) + $tempminutesafter%60;
			if($temptheminute>=60)
			{	$temptheminute = $temptheminute-60;
				$tempthehour += 1;
			}
			$tempthehour = ($tempthehour < 10)? '0'.$tempthehour : $tempthehour;
			$temptheminute = ($temptheminute < 10)? '0'.$temptheminute : $temptheminute;
			$JamSelesai = $tempthehour.':'.$temptheminute.':00';
		}		
		else
		{	//$jadwalerrors[] = "Error ini mungkin diakibatkan perubahan kapasitas ruang ketika ruang sudah ditempati melebihi kapasitas baru.<br>";
		}
	}
	return substr($JamMulai, 0, 5).' - '.substr($JamSelesai, 0, 5);
}
?>
<script>creator.location.reload(true); window.close()</script>
