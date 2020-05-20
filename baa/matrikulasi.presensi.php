<?php
	// *** Main ***
	
	session_start();
	$matri_kelompok = GetSetVar('matri_kelompok', '');
	$matri_tahun = GetSetVar('matri_tahun', '');
	loadJavaScripts();
	
	$sub = (empty($_REQUEST['sub']))? 'DaftarMatrikulasi' : $_REQUEST['sub'];
	$sub();
	// *** Functions ***
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
	
	function DaftarMatrikulasi()
	{	
		$s = "select Nama, MhswID, Kelamin, ProdiID, NilaiUjian, MatriHadir from `mhsw` where MatriID='$_SESSION[matri_kelompok] order by Nama'"; 
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
		echo "<p><table class=box cellspacing=1 align=center width=395>
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
			
		echo "<table class=box cellspacing=1 align=center width=395>
				<form name='form2' action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden name='sub' value='KehadiranSiswa' />
				
				<tr>
					<th class=ttl>No</th>
					<th class=ttl>Mahasiswa di Kelompok ini</th>
					<th class=ttl align=center><input type=button name='CheckAllMember' value='Cek Semua' onClick=\"CheckAll('HM')\" /></th>	
				</tr>
			";
		
		if(!empty($_SESSION['matri_kelompok']))
		{	$x1 = 0;
			while($w=_fetch_array($r))
			{	$x1++;
				$ck = ($w['MatriHadir'] == 'Y')? 'checked' : '';
				echo "<tr>
						<td class=ul1 width=12 align=right>$x1.</td> 
						<td class=ul1>$w[Nama] <img src='img/$w[Kelamin].bmp'></img><font size=1 color=teal>$w[ProdiID] - $w[NilaiUjian]</font></td>
						<td class=ul1 align=center><input type=checkbox id='HM$x1' name='PilihKehadiran[]' value='$w[MhswID]' $ck>
													<input type=hidden name='SemuaPilihan[]' value='$w[MhswID]'></td>
					</tr>";
			}
			echo "<input type=hidden id='JumlahHM' name='JumlahHM' value='$x1'>";
			if($xx>0)
			{
				echo "
					<tr>
						<td></td>
						<td class=ul1 align=center><input type=submit name='Kehadiran Siswa' value='Simpan Kehadiran'/></td>
						<td class=ul1 align=center><input type=button name='UnCheckAllMember' value='Clear Semua' onClick=\"UnCheckAll('HM')\" /></td>
					</tr>";
			}
		}
		echo  "
				</form>
			</table>";
	}
	
	function KehadiranSiswa()
	{	$Pilihan = $_REQUEST['PilihKehadiran'];
		$SemuaPilihan = $_REQUEST['SemuaPilihan'];
		$JumlahSemua = $_REQUEST['JumlahHM'];
	
		if(empty($Pilihan)) $Pilihan = array(-1);
		$count = 0;
		foreach($Pilihan as $chosen)
		{	
			while($count<$JumlahSemua)
			{	
				$cek = GetaField('mhsw', 'MhswID', $SemuaPilihan[$count], 'MatriHadir');
				if($SemuaPilihan[$count] == $chosen)
				{	if($cek == 'N')
					{	$se = "update `mhsw` set MatriHadir='Y' where MhswID='$chosen'";
						$re = _query($se);
					}
					$count++;
					break;
				}
				else
				{	if($cek == 'Y')
					{	$se = "update `mhsw` set MatriHadir='N', MatriNilai='' where MhswID='$SemuaPilihan[$count]'";
						$re = _query($se);
					}					
					$count++;
				}
			}
			if($chosen == -1) break;
		}
		
		echo Konfirmasi("Berhasil", 
			"Kehadiran mahasiswa berhasil disimpan.<br />
			Tampilan akan kembali ke semula dalam 1 detik.");
		echo "<script type='text/javascript'>window.onload=setTimeout('window.location=\"?mnux=$_SESSION[mnux]\"', 1000);</script>";
		
	}
?>