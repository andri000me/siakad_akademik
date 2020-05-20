<?php
	// *** Main ***
	
	session_start();
	$matri_kelompok = GetSetVar('matri_kelompok', '');
	$matri_tahun = GetSetVar('matri_tahun', '');
	$matri_mata_uji = GetSetVar('matri_mata_uji', '');
	loadJavaScripts();
	
	$sub = (empty($_REQUEST['sub']))? 'NilaiMatrikulasi' : $_REQUEST['sub'];
	$sub();
	// *** Functions ***
	
	function NilaiMatrikulasi() {
	$f = GetMataUji();
	if(!empty($_SESSION['matri_mata_uji'])) $u = GetNilai();
	echo "<p><table class=bsc cellspacing=1 align=center width=800>
	<tr><td class=ul valign=top width=300>
		$f
		</td>
		<td class=ul1 valign=top>
		$u
		</td>
		</tr>
	</table></p>";
	}
	
	function GetMataUji() {
	  $s = "select MatriMataUjiID, Nama
		from matrimatauji
		where KodeID = '".KodeID."' and NA = 'N'
		order by MatriMataUjiID";
	  $r = _query($s);
	  
	  $a = "<table class=box cellspacing=1 width=100%>
		<tr>
		<th class=ttl colspan=4>Daftar Ujian</th>
		</tr>";
	  while ($w = _fetch_array($r)) {
		if ($w['MatriMataUjiID'] == $_SESSION['matri_mata_uji']) {
		  $ki = "&raquo;";
		  $ka = "&laquo;";
		  $c = "class=inp1";
		}
		else {
		  $ki = '&nbsp;';
		  $ka = '&nbsp;';
		  $c = "class=ul1";
		}
		$a .= "<tr>
		  <td $c width=10>$ki</td>
		  <td $c><a href='?mnux=$_SESSION[mnux]&gos=nilai&sub=&matri_mata_uji=$w[MatriMataUjiID]'>$w[Nama]</a></td>
		  <td $c width=10>$ka</td>
		  </tr>";
	  }
	  $a .= "</table>";
	  return $a;
	}
	
	function GetNilai()
	{	
		$s = "select Nama, MhswID, Kelamin, ProdiID, NilaiUjian, MatriHadir, MatriNilai from `mhsw` 
					where MatriID='$_SESSION[matri_kelompok] order by Nama'"; 
		$r = _query($s);
		$xx = _num_rows($r);
		
		$sss = "select KapasitasSekarang, KapasitasMaksimum from `matrikulasi` where MatriID='$_SESSION[matri_kelompok]'";
		$rrr = _query($sss);
		$www = _fetch_array($rrr);

		$wheretahun = "TahunID='$_SESSION[matri_tahun]'";
		if(empty($_SESSION['matri_tahun']) or $_SESSION['matri_tahun']=='')
		{ 	$optkelompok = "<option value=''>--Isi Tahun Dulu--</option>";  }
		else
		{	$optkelompok = GetOption2('matrikulasi', "Nama", 'Nama', $_SESSION['matri_kelompok'], $wheretahun, 'MatriID');	
			if($optkelompok=='' or empty($optkelompok))
			{	$optkelompok = "<option value=''>--Tidak ada kelompok--</option>";
			}
		}
		$a .= "<p><table class=box cellspacing=1 align=center width=495>
				<form action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden id='gos' name='gos' value='' />
				<tr>
					<td class=inp width=130>Tahun Akademik: </td>
					<td class=ul1 colspan=3><input type=text name='matri_tahun' value='$_SESSION[matri_tahun]' size=3 maxlength=10>
											<input type=submit name='Set' value='Set'>
					</td>
				</tr>
				<tr>
					<td class=inp>Kelompok: </td>
					<td class=ul1 colspan=3><select name='matri_kelompok' onChange='this.form.submit()'>$optkelompok</select></td>
					
				</tr>
				<tr>
					<td class=inp>Kapasitas Sekarang:</td>
					<td class=ul1>$www[KapasitasSekarang]</td>
					<td class=inp width=200>Kapasitas Maksimum:</td>
					<td class=ul1>$www[KapasitasMaksimum]</td>
				</tr>
				</form>
			</table></p>";
			
		$a .= "<table class=box cellspacing=1 align=center width=495>
				<form name='form2' action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden name='sub' value='PenilaianSiswa' />
				
				<tr>
					<th class=ttl width=15>No</th>
					<th class=ttl>Mahasiswa di Kelompok ini</th>
					<th class=ttl width=60>Nilai</th>	
				</tr>
			";
		
		if(!empty($_SESSION['matri_kelompok']))
		{	$x1 = 0;
			while($w=_fetch_array($r))
			{	$x1++;
				$nilai = '';
				$arrMatriNilai = explode('~', $w['MatriNilai']);
				foreach($arrMatriNilai as $MatriNilai)
				{	$arrPerMatriNilai = explode('=', $MatriNilai);
					if($arrPerMatriNilai[0] == $_SESSION['matri_mata_uji'])
					{	$nilai = $arrPerMatriNilai[1];	
						echo $nilai;
					}
				}
				
				$ro = ($w['MatriHadir'] == 'Y')? '' : 'readonly=true';
				$help = ($w['MatriHadir'] == 'Y')? '' : "title='Siswa ini ditandai tidak hadir pada daftar kehadiran'";
				$a .= "<tr>
						<td class=ul1 align=right>$x1.</td> 
						<td class=ul1>$w[Nama] <img src='img/$w[Kelamin].bmp'></img><font size=1 color=teal>$w[ProdiID] - $w[NilaiUjian]</font></td>
						<td class=ul1 align=center><input $help type=text id='NM$x1' name='PilihPenilaian[]' value='$nilai' size=3 maxlength=5 $ro>
													<input type=hidden name='SemuaPilihan[]' value='$w[MhswID]'></td>
	 
					</tr>";
			}
			$a .= "<input type=hidden id='JumlahNM' name='JumlahNM' value='$x1'>";
			if($xx>0)
			{
				$a .= "
					<tr>
						<td></td>
						<td class=ul1 align=center><input type=submit name='Kehadiran Siswa' value='Simpan Nilai'/></td>
						<td class=ul1 align=center><input type=button name='UnCheckAllMember' value='Clear' onClick=\"UnCheckAll('NM')\" /></td>
						<td></td>
					</tr>";
			}
		}
		$a .= "	</form>
			</table>";
			
		return $a;
	}
	
	function PenilaianSiswa()
	{	$Nilai = $_REQUEST['PilihPenilaian'];
		$SemuaPilihan = $_REQUEST['SemuaPilihan'];
	
		$count = 0;
		
		foreach($SemuaPilihan as $chosen)
		{	//echo "COUNT: $count, CHOSEN: $chosen<br>";
			$change = 'N';
			$exist = 'N';
			$nilainya = '';
			
			$NilaiSkrg = GetaField('mhsw', 'MhswID', $chosen, 'MatriNilai');
			if(!empty($Nilai[$count]))
			{
				$arrMatriNilai = explode('~', $NilaiSkrg);
				if(!empty($arrMatriNilai))
				{	foreach($arrMatriNilai as $MatriNilai)
					{	$arrPerMatriNilai = explode('=', $MatriNilai);
						$pernilainya = '';
						if($arrPerMatriNilai[0] == $_SESSION['matri_mata_uji'])
						{	if($arrPerMatriNilai[1] != $Nilai[$count]) $change='Y'; 
							$pernilainya = $arrPerMatriNilai[0]."=".$Nilai[$count];
							$exist = 'Y';
						}
						else $pernilainya = $MatriNilai;
						
						if(empty($nilainya)) $nilainya = $pernilainya;
						else $nilainya .= "~".$pernilainya; 
					}	
				}	
				
				//echo "COUNT: $count, NILAINYA: $nilainya<br>";
				
				if($exist == 'N')
				{	if(empty($nilainya)) $nilainya = $_SESSION['matri_mata_uji'].'='.$Nilai[$count];
					else $nilainya .= '~'.$_SESSION['matri_mata_uji'].'='.$Nilai[$count];
					$change='Y';
				}
			}
			
			//echo "COUNT: $count, NILAINYA: $nilainya<br>";
			//echo "CHANGE: $change<br>";
			if($change == 'Y')
			{	$se = "update `mhsw` set MatriNilai='$nilainya' where MhswID='$chosen'";
				$re = _query($se);
			}
			$count++;
		}
		
		echo Konfirmasi("Berhasil", 
			"Kehadiran mahasiswa berhasil disimpan.<br />
			Tampilan akan kembali ke semula dalam 1 detik.");
		echo "<script type='text/javascript'>window.onload=setTimeout('window.location=\"?mnux=$_SESSION[mnux]\"', 1000);</script>";
	
	}
	function loadJavaScripts()
	{	echo "
			<SCRIPT LANGUAGE='JavaScript'>

			function CheckAll(chk)
			{	
				total = (document.getElementById('Jumlah'+chk)).value;
				for (i = 1; i <= total; i++)
				{	
					(document.getElementById(chk+i)).checked = true;
				}
			}
			
			function UnCheckAll(chk)
			{
				total = (document.getElementById('Jumlah'+chk)).value;
				
				for (i = 1; i <= total; i++)
				{
					(document.getElementById(chk+i)).checked = false;
				}
			}

			</script>
		";		
	}
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
?>