<?php
	// *** Main ***
	
	session_start();
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	
	$WawancaraUSMID = $_REQUEST['WawancaraUSMID']+0;
	$gel = $_REQUEST['gel'];
	$PMBID = $_REQUEST['PMBID'];
	//echo "ProdiUSM: $ProdiUSMID, GEL: $gel, PMBID: $PMBID, RuangID: $ruangidx";
?>
	
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META http-equiv="cache-control" content="max-age=0">
  <META http-equiv="pragma" content="no-cache">
  <META http-equiv="expires" content="0" />
  <META http-equiv="content-type" content="text/html; charset=UTF-8">
  
  <META content="Emanuel Setio Dewo" name="author" />
  <META content="Sisfo Kampus" name="description" />
  
  <link rel="stylesheet" type="text/css" href="../themes/<?=$_Themes;?>/index.css" />
  <link rel="stylesheet" type="text/css" href="../themes/<?=$_Themes;?>/ddcolortabs.css" />
  
  <script type="text/javascript" language="javascript" src="../include/js/dropdowntabs.js"></script>
  <!-- <script type="text/javascript" language="javascript" src="include/js/jquery.js"></script> -->
  <script type="text/javascript" languange="javascript" src="../floatdiv.js"></script>
  
  
  <script src="../fb/jquery.pack.js" type="text/javascript"></script>
  <link href="../fb/facebox.css" media="screen" rel="stylesheet" type="text/css" />
  <script src="../fb/facebox.js" language='javascript' type="text/javascript"></script>
  
  <script type="text/javascript" language="javascript" src="../include/js/boxcenter.js"></script>
  
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('a[rel*=facebox]').facebox() 
    })
  </script>
  </HEAD>
<BODY>
	
<?php	
	$gos = (empty($_REQUEST['gos']))? 'ListKelas' : $_REQUEST['gos'];
	$gos($PMBID, $WawancaraUSMID, $gel);
	// *** Functions ***
	
	function GetOptionsFromData($sourceArray, $chosen)
	{	
			$optresult = "";
			if($chosen == '' or empty($chosen))	
			{ 	$optresult .= "<option value='' selected></option>"; }
			else { $optresult .= "<option value=''></option>"; }
			for($i=0; $i < count($sourceArray); $i++)
			{	if($chosen == $sourceArray[$i])
				{	$optresult .= "<option value='$sourceArray[$i]' selected>$sourceArray[$i]</option>"; }
				else
				{ 	$optresult .= "<option value='$sourceArray[$i]'>$sourceArray[$i]</option>"; }
			}
			return $optresult;
	}
	
	function ListKelas($PMBID, $WawancaraUSMID, $gel)
	{	$ada = GetaField('wawancara', "PMBPeriodID='$gel' and PMBID='$PMBID' and KodeID", KodeID, 'WawancaraUSMID');
		$prodi = GetaField('pmb', "PMBID='$PMBID' and KodeID", KodeID, "Pilihan1");
		
		if(empty($ada))
		{	$s = "select WawancaraUSMID, RuangID, Tanggal, JamMulai, JamSelesai from `wawancarausm` 
					where ProdiID='$prodi' and PMBPeriodID='$gel' and KodeID='".KodeID."'
					order by Tanggal, JamMulai, JamSelesai";
			$r = _query($s);
			$n = _num_rows($r);
			while($w = _fetch_array($r))
			{	$ruangidx = $w['RuangID'];
				$n1 = GetaField('wawancara', "WawancaraUSMID='$w[WawancaraUSMID]' and PMBPeriodID='$gel' and KodeID", KodeID, "count(WawancaraID)");
				$tempkapmax = GetaField('wawancarausm', 'WawancaraUSMID', $w['WawancaraUSMID'], 'Kapasitas');
				$tempduration = GetaField('wawancarausm', 'WawancaraUSMID', $w['WawancaraUSMID'], 'PanjangWaktu');
				$tempwawan = GetFields('wawancarausm', 'WawancaraUSMID', $w['WawancaraUSMID'], 'JamMulai, JamSelesai');
				$tempminutes = ((substr($tempwawan['JamSelesai'], 0, 2) - substr($tempwawan['JamMulai'], 0, 2))*60) +
								(substr($tempwawan['JamSelesai'], 3, 2) - substr($tempwawan['JamMulai'], 3, 2));
				
				if($tempkapmax == 0 and $tempduration == 0)
				{	$nextpos = $n1+1;
						
					$sx = "insert into `wawancara` set PMBID='$PMBID', WawancaraUSMID='$w[WawancaraUSMID]', PMBPeriodID='$gel', RuangID='$ruangidx', 
							UrutanDiRuang='$nextpos', KodeID='".KodeID."', Tanggal='$w[Tanggal]', JamMulai='$w[JamMulai]', JamSelesai='$w[JamSelesai]', 
							JamMulaiWawancara = '$w[JamMulai]', JamSelesaiWawancara = '$w[JamSelesaiWawancara]',
							LoginBuat='$_SESSION[_Login]', TanggalBuat=now()";
					$rx = _query($sx);
					$WawancaraUSMID=$w['WawancaraUSMID'];
					break;
				}
				else if($tempduration == 0)
				{	
					if($n1 < $tempkapmax)
					{	
						$nextpos = $n1+1;
						
						$sx = "insert into `wawancara` set PMBID='$PMBID', WawancaraUSMID='$w[WawancaraUSMID]', PMBPeriodID='$gel', RuangID='$ruangidx', 
								UrutanDiRuang='$nextpos', KodeID='".KodeID."', Tanggal='$w[Tanggal]', JamMulai='$w[JamMulai]', JamSelesai='$w[JamSelesai]',
								JamMulaiWawancara = '$w[JamMulai]', JamSelesaiWawancara = '$w[JamSelesaiWawancara]',
								LoginBuat='$_SESSION[_Login]', TanggalBuat=now()";
						$rx = _query($sx);
						$WawancaraUSMID=$w['WawancaraUSMID'];
						break;
					}
					else if($n1 == $tempkapmax)
					{	// continue foreach
						if($w['WawancaraUSMID'] == $lastWawanUSMID)
						{	$jadwalerrors[] = "Semua kelas wawancara untuk prodi $prodi di gel ini sudah penuh. </br>
							Tambahkan ruang di Setup Wawancara atau cama ini tidak bisa mengikuti wawancara.";
							break;
						}
					}		
					else
					{	//$jadwalerrors[] = "Error ini mungkin diakibatkan perubahan kapasitas ruang ketika ruang sudah ditempati melebihi kapasitas baru.<br>";
					}
				}
				else if($tempkapmax == 0)
				{	
					$tempkapmax2 = floor($tempminutes/$tempduration);
					$tempkapmax2 = ($tempkapmax < 1)? 1 : $tempkapmax2; 
					$rotatepos = $n1%$tempkapmax2;
					$nextpos = $n1+1;
					
					$tempminutesafter = ($rotatepos)*$tempduration;
					$tempthehour = substr($w['TanggalWawancara'], 11, 2) + floor($tempminutesafter/60);
					$temptheminute = substr($w['TanggalWawancara'], 14, 2) + $tempminutesafter%60;
					if($temptheminute>=60)
					{	$temptheminute = $temptheminute-60;
						$tempthehour += 1;
					}
					$tempthehour = ($tempthehour < 10)? '0'.$tempthehour : $tempthehour;
					$temptheminute = ($temptheminute < 10)? '0'.$temptheminute : $temptheminute;
					$tempJamMulai = $tempthehour.':'.$temptheminute.':00';
					
					$tempminutesafter = ($rotatepos+1)*$tempduration;
					$tempthehour = substr($w['TanggalWawancara'], 11, 2) + floor($tempminutesafter/60);
					$temptheminute = substr($w['TanggalWawancara'], 14, 2) + $tempminutesafter%60;
					if($temptheminute>=60)
					{	$temptheminute = $temptheminute-60;
						$tempthehour += 1;
					}
					$tempthehour = ($tempthehour < 10)? '0'.$tempthehour : $tempthehour;
					$temptheminute = ($temptheminute < 10)? '0'.$temptheminute : $temptheminute;
					$tempJamSelesai = $tempthehour.':'.$temptheminute.':00';
					
					$sx = "insert into `wawancara` set PMBID='$PMBID', WawancaraUSMID='$w[WawancaraUSMID]', PMBPeriodID='$gel', RuangID='$ruangidx', 
							UrutanDiRuang='$nextpos', KodeID='".KodeID."', Tanggal='$w[Tanggal]', JamMulai='$w[JamMulai]', JamSelesai='$w[JamSelesaiWawancara]', 
							JamMulaiWawancara = '$tempJamMulai', JamSelesaiWawancara = '$tempJamSelesai',
							LoginBuat='$_SESSION[_Login]', TanggalBuat=now()";
					$rx = _query($sx);
					$WawancaraUSMID=$w['WawancaraUSMID'];
					break;
				}
				else
				{	
					$tempmax2 = floor($tempminutes/$tempduration);
					$tempkapmax2 = ($tempkapmax < 1)? 1 : $tempkapmax2;
					$tempcountpersession = ceil($tempkapmax/$tempmax2);
					//echo "TEMPMINUTES: $tempminutes, TEMPMAX2 = $tempmax2, TEMPCOUNTPERSESSION = $tempcountpersession <br>";
				
					if($n1 < $tempkapmax)
					{	$nextpos = $n1+1;
					
						$tempposition = floor(($nextpos-1)/$tempcountpersession);
						
						$tempminutesafter = ($tempposition)*$tempduration;
						$tempthehour = substr($w['TanggalWawancara'], 11, 2) + floor($tempminutesafter/60);
						$temptheminute = substr($w['TanggalWawancara'], 14, 2) + $tempminutesafter%60;
						if($temptheminute>=60)
						{	$temptheminute = $temptheminute-60;
							$tempthehour += 1;
						}
						$tempthehour = ($tempthehour < 10)? '0'.$tempthehour : $tempthehour;
						$temptheminute = ($temptheminute < 10)? '0'.$temptheminute : $temptheminute;
						$tempJamMulai = $tempthehour.':'.$temptheminute.':00';
						
						$tempminutesafter = ($tempposition+1)*$tempduration;
						$tempthehour = substr($w['TanggalWawancara'], 11, 2) + floor($tempminutesafter/60);
						$temptheminute = substr($w['TanggalWawancara'], 14, 2) + $tempminutesafter%60;
						if($temptheminute>=60)
						{	$temptheminute = $temptheminute-60;
							$tempthehour += 1;
						}
						$tempthehour = ($tempthehour < 10)? '0'.$tempthehour : $tempthehour;
						$temptheminute = ($temptheminute < 10)? '0'.$temptheminute : $temptheminute;
						$tempJamSelesai = $tempthehour.':'.$temptheminute.':00';
						
						$sx = "insert into `wawancara` set PMBID='$PMBID', WawancaraUSMID='$w[WawancaraUSMID]', PMBPeriodID='$gel', RuangID='$ruangidx', 
								UrutanDiRuang='$nextpos', KodeID='".KodeID."', Tanggal='$w[Tanggal]', JamMulai='$w[JamMulai]', JamSelesai='$w[JamSelesaiWawancara]', 
								JamMulaiWawancara = '$tempJamMulai', JamSelesaiWawancara = '$tempJamSelesai',
								LoginBuat='$_SESSION[_Login]', TanggalBuat=now()";
						$rx = _query($sx);
						$WawancaraUSMID=$w['WawancaraUSMID'];
						break;
					}		
					else
					{	//$jadwalerrors[] = "Error ini mungkin diakibatkan perubahan kapasitas ruang ketika ruang sudah ditempati melebihi kapasitas baru.<br>";
					}
				}
			}
			
			if(empty($WawancaraUSMID)) echo "<font size=6><b>Tidak ada tempat lagi untuk penempatan wawancara cama ini. Harap menghubungi KaPMB untuk menambah tempat.</b></font>";
		}
		//bila cama telah ditempatkan di salah satu slot wawancara...
		else 
		{	if(empty($WawancaraUSMID)) $WawancaraUSMID = $ada;
		}
		
	
	// Tampilkan pilihan wawancara
	$wawancarausm = GetFields('wawancarausm', "WawancaraUSMID='$WawancaraUSMID' and KodeID", KodeID, "*");
	echo  "<p><table class=bsc width=100%><tr width=100%>
					<td class=ul1><font size=5>Wawancara </font>
				    <input type=button name='Tutup' value='Tutup' onClick=\"ttutup()\">
					<br><sup>Kapasitas Maksimum : $wawancarausm[Kapasitas], Panjang Waktu Per Wawancara : $wawancarausm[PanjangWaktu]</sup>
				  </tr>
				  </table></p>";

	$wawancara = GetFields('wawancara', "PMBPeriodID='$gel' and PMBID='$PMBID' and KodeID", KodeID, '*');
	$s = "select * from wawancarausm where KodeID='".KodeID."' and ProdiID='$prodi' and PMBPeriodID='$gel' order by Tanggal, JamMulai, JamSelesai";
	$r = _query($s);			
	$n = 0;
	echo "<table class=bsc cellspacing=1 align=center><tr>";
	while($w = _fetch_array($r))
	{	$n++;
		
		$sel = ($WawancaraUSMID == $w['WawancaraUSMID'])? 'class=menuaktif' : 'class=menuitem';
		$WaktuWawancara = "Ruang $w[RuangID], Tanggal $w[Tanggal], Jam ".substr($w['JamMulai'], 0, 5)." - ".substr($w['JamSelesai'], 0, 5);
		echo "<td $sel><a href='?mnux=$_SESSION[mnux]&gos=&WawancaraUSMID=$w[WawancaraUSMID]&PMBID=$PMBID&gel=$gel'>$WaktuWawancara</a></td>";
				
		/*$_Status = ($w['WawancaraUSMID'] == $wawancara['WawancaraUSMID']) ? 
						'Terdaftar' : "<a href='#' onClick=\"PilihWawancaraIni('$PMBID', '$gel', '$w[RuangID]', '$w[WawancaraUSMID]')\" >Pindah ke Ruang ini</a>";
		
		echo "<tr><td class=ul1>$n</td>
				  <td class=ul1>$w[Tanggal]</td>
				  <td class=ul1>$w[JamMulai] - $w[JamSelesai]</td>
				  <td class=ul1>$w[RuangID]</td>
				  <td class=ul1>$JumlahSiswa</td>
				  <td class=ul1>$w[Kapasitas]</td>
				  <td class=ul1>$_Status</td>
			  </tr>";*/	
	}
	echo "</tr></table>";
	
	
	$s = "select * from wawancara where WawancaraUSMID='$WawancaraUSMID' and PMBPeriodID='$gel' and KodeID='".KodeID."' order by UrutanDiRuang";
	$r = _query($s);
	$n = 0;
	echo "<table class=bsc cellspacing=1 border=1 align=center>
			<tr><th class=ttl width=20>#</th>
			  <th class=ttl width=100 align=center>PMBID</th>
			  <th class=ttl width=150 align=center>Nama</th>
			  <th class=ttl width=100 align=center>Jam Wawancara</th>
			  <th class=ttl width=100>Status</th>
			</tr>";
	while($w = _fetch_array($r))
	{	$n++;
		
		$pmb = GetFields('pmb', "PMBID='$w[PMBID]' and KodeID", KodeID, "*");
		if($PMBID == $w['PMBID']) $class = 'wrn';
		else $class = 'ul1';
		$_Status = (empty($w['HasilWawancara']))? "<b>Belum Wawancara</b>" : 'Sudah Wawancara';
		
		if($n < $w['UrutanDiRuang'])
		{	
			while($n < $w['UrutanDiRuang'])
			{	$Jam = GetJam($w, $pmb, $wawancarausm, $n);
				$tempJam = str_replace(' - ', '/', $Jam);
				echo "<tr>
						  <td class=ul1>$n</td>
						  <td class=ul1 align=center><a href='#' onClick=\"PilihWawancaraIni('$PMBID', '$n','$gel', '$wawancara[RuangID]', '$WawancaraUSMID', '$tempJam')\" >Tempatkan Di Waktu ini</a></td>
						  <td class=ul1 align=center>-</td>
						  <td class=ul1 align=center>$Jam</td>
						  <td class=ul1 align=center></td>
					  </tr>";
				$n++;
			}
			$Jam = GetJam($w, $pmb, $wawancarausm, $n);
			echo "<tr>
					  <td class=$class>$n</td>
					  <td class=$class align=center>$pmb[PMBID]</td>
					  <td class=$class align=center>$pmb[Nama]</td>
					  <td class=$class align=center>$Jam</td>
					  <td class=$class align=center>$_Status</td>
				  </tr>";
		}
		else if($n == $w['UrutanDiRuang'])
		{	$Jam = GetJam($w, $pmb, $wawancarausm, $n);
			echo "<tr>
					  <td class=$class>$n</td>
					  <td class=$class align=center>$pmb[PMBID]</td>
					  <td class=$class align=center>$pmb[Nama]</td>
					  <td class=$class align=center>$Jam</td>
					  <td class=$class align=center>$_Status</td>
				  </tr>
			  ";
		}
		else
		{	echo "Seharusnya tidak boleh ke sini: $n<br>";
		}
		//echo "COUNT: $n, Urutan: $w[UrutanDiRuang]<br>";
	}
	$kapmax = GetaField('wawancarausm', "WawancaraUSMID='$WawancaraUSMID' and KodeID", KodeID, "Kapasitas");
	if($kapmax > 0)
	{	if($n <= $kapmax)
		{	
			$n++;
			while($n <= $kapmax)
			{
				$Jam = GetJam($w, $pmb, $wawancarausm, $n);
				$tempJam = str_replace(' - ', '/', $Jam);
				echo "<tr>
						  <td class=ul1>$n</td>
						  <td class=ul1 align=center><a href='#' onClick=\"PilihWawancaraIni('$PMBID', '$n','$gel', '$wawancara[RuangID]', '$WawancaraUSMID', '$tempJam')\" >Tempatkan Di Waktu ini</a></td>
						  <td class=ul1 align=center>-</td>
						  <td class=ul1 align=center>$Jam</td>
						  <td class=ul1 align=center></td>
					  </tr>";
				$n++;
			}
		}
	}
	else
	{	$n++;
		echo "<tr>
				  <td class=ul1>$n</td>
				  <td class=ul1 align=center><a href='#' onClick=\"PilihWawancaraIni('$PMBID', '$n','$gel', '$wawancara[RuangID]', '$WawancaraUSMID', '$tempJam')\" >Tempatkan Di Waktu ini</a></td>
				  <td class=ul1 align=center>-</td>
				  <td class=ul1 align=center>$Jam</td>
				  <td class=ul1 align=center></td>
			  </tr>";
		echo "<tr><td colspan=10><b>Karena kapasitas untuk wawancara ini tidak ditentukan, maka Ruangan ini dapat memuat peserta yang tidak terbatas</b></td></tr>";
	}
	
	
	echo "</table>
			<script>
				function PilihWawancaraIni(pmbid, urutan, gel, ruangid, wawancarausmid, jam)
				{	lnk = '../$_SESSION[mnux].framewawancara.save.php?pmbid='+pmbid+'&urutan='+urutan+'&gel='+gel+'&ruangid='+ruangid+'&wawancarausmid='+wawancarausmid+'&jam='+jam;
					win2 = window.open(lnk, '', 'width=0, height=0, scrollbars, status');
					if (win2.opener == null) childWindow.opener = self;
					win2.creator = self;
				}
				function ttutup() {
					top.opener.location='../index.php?mnux=$_SESSION[mnux]&_pmbPage=0';
					top.close();
					return false;
				}
			  </script>
	";
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

  <script>
  JSFX_FloatDiv("divInfo", 0, 100).flt();
  </script>
</BODY>

</HTML>
