<?php
	// *** Main ***
	
	session_start();
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	
	$matri_kelompok = GetSetVar('matri_kelompok', '');
	$prodi = GetSetVar('prodi', '');
	$matri_tahun = GetSetVar('matri_tahun', '');
	loadJavaScripts();
	
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
					
					function BebaskanKelompok(gos, myform, tahun)
					{	if(confirm('Apakah anda akan mendealokasikan semua kelompok siswa di Tahun '+tahun+'?'))
						{
							(document.getElementById('gos')).value = gos;
							myform.submit();
						}
					}
					
					function refresh_frame2() {
						x = top.frames['frame2'].location = 'matrikulasi.frame2.php';
					}
	
					</script>
		";		
	}
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
  <!--<script type="text/javascript" language="javascript" src="include/js/jquery.autocomplete.js"></script>-->
  <!--<script type="text/javascript" language="javascript" src="include/js/jtip.js"></script>-->

  </HEAD>
<BODY>
	
<?php	
	$gos = (empty($_REQUEST['gos']))? 'DaftarMatrikulasi' : $_REQUEST['gos'];
	$gos();
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
	
	function DaftarMatrikulasi()
	{	
		$s = "select Nama, MhswID, Kelamin, ProdiID, NilaiUjian from `mhsw` where MatriID='$_SESSION[matri_kelompok]'"; 
		$r = _query($s);
		$xx = _num_rows($r);
		
		$sss = "select KapasitasSekarang, KapasitasMaksimum from `matrikulasi` where MatriID='$_SESSION[matri_kelompok]'";
		$rrr = _query($sss);
		$www = _fetch_array($rrr);
		
		/*for($q = date("Y"); $q >= 2003; $q--)
		{	$arraytahun[] = $q;	}
		$opttahun = GetOptionsFromData($arraytahun, $_SESSION['matri_tahun']);	*/
		//$opttahun = GetOption2('tahun', "concat(TahunID, ' (', ProdiID, if(ProdiID='','','-'), ProgramID, ')')", 'TahunID', $_SESSION['matri_tahun'], "NA='N'", 'TahunID');
		$wheretahun = "TahunID='$_SESSION[matri_tahun]'";
		if(empty($_SESSION['matri_tahun']) or $_SESSION['matri_tahun']=='')
		{ 	$optkelompok = "<option value=''>--Isi Tahun Dulu--</option>";  }
		else
		{	$optkelompok = GetOption2('matrikulasi', "Nama", 'Nama', $_SESSION['matri_kelompok'], $wheretahun, 'MatriID');	
			if($optkelompok=='' or empty($optkelompok))
			{	$optkelompok = "<option value=''>--Tidak ada kelompok--</option>";
			}
		}
		echo "<table class=box cellspacing=1 align=center width=395>
				<form action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden id='gos' name='gos' value='' />
				<tr>
					<td class=inp width=130>Tahun Akademik: </td>
					<td class=ul1 colspan=3><input type=text name='matri_tahun' value='$_SESSION[matri_tahun]' size=3 maxlength=10>
											<input type=submit name='Set' value='Set'>
											<input type=button name='Kosongkan' value='Bebaskan' onClick=\"BebaskanKelompok('BebaskanSemuaKelompok', this.form, '$_SESSION[matri_tahun]')\"></td>
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
			</table>";
			
		echo "<table class=box cellspacing=1 align=center width=395>
				<form name='form2' action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden name='gos' value='DealokasiSiswa' />
				
				<tr>
					<th class=ttl>Mahasiswa di Kelompok ini</th>
					<th class=ttl align=center><input type=button name='CheckAllMember' value='Cek Semua' onClick=\"CheckAll('DM')\" /></th>	
				</tr>
			";
		
		if(!empty($_SESSION['matri_kelompok']))
		{	$x1 = 0;
			while($w=_fetch_array($r))
			{	$x1++;
				echo "<tr>
						<td class=ul1>$x1. $w[Nama] <img src='../img/$w[Kelamin].bmp'></img><font size=1 color=teal>$w[ProdiID] - $w[NilaiUjian]</font></td>
						<td class=ul1 align=center><input type=checkbox id='DM$x1' name='PilihDealokasi[]' value='$w[MhswID]'></td>
					</tr>";
			}
			echo "<input type=hidden id='JumlahDM' name='JumlahDM' value=$x1>";
			if($xx>0)
			{
				echo "
					<tr>
						<td class=ul1 align=center><input type=submit name='DealokasiSiswa' value='Dealokasi Siswa yang Dicek'/></td>
						<td class=ul1 align=center><input type=button name='UnCheckAllMember' value='Clear Semua' onClick=\"UnCheckAll('DM')\" /></td>
					</tr>";
			}
		}
		echo  "
				</form>
			</table>";
	}
	
	function DealokasiSiswa()
	{	$Pilihan = $_REQUEST['PilihDealokasi'];
		
		foreach($Pilihan as $chosen)
		{	$se = "update `mhsw` set MatriID='' where MhswID='$chosen'";
			$re = _query($se);
		}
		
		$s5 = "select * from `mhsw` where MatriID='$_SESSION[matri_kelompok]'"; 
		$r5 = _query($s5);
		$x5 = _num_rows($r5);
		
		$s6 = "update `matrikulasi` set KapasitasSekarang='$x5' where MatriID='$_SESSION[matri_kelompok]' ";
		$r6 = _query($s6);
		
		echo Konfirmasi("Berhasil", 
			"Dealokasi mahasiswa yang dipilih berhasil.<br />
			Tampilan akan kembali ke semula dalam 1 detik.");
		echo "<script type='text/javascript'>refresh_frame2()</script>";
		echo "<script type='text/javascript'>window.onload=setTimeout('window.location=\"?mnux=$_SESSION[mnux]\"', 1000);</script>";
		
	}
	
	function BebaskanSemuaKelompok()
	{	$s = "select MatriID from `matrikulasi` where KodeID='".KodeID."' and TahunID='$_SESSION[matri_tahun]'";
		$r = _query($s);
		
		while($w=_fetch_array($r))
		{	$sx = "update `mhsw` set MatriID='0' where KodeID='".KodeID."' and MatriID='$w[MatriID]'";
			$rx = _query($sx);	
		}
		
		$sc = "update `matrikulasi` set KapasitasSekarang='0' where KodeID='".KodeID."' and TahunID='$_SESSION[matri_tahun]'";
		$rc = _query($sc);
		
		echo Konfirmasi("Berhasil", 
			"Semua kelompok di tahun akademik $_SESSION[matri_tahun] telah dibebaskan dari semua anggota.<br />
			Tampilan akan kembali ke semula dalam 1 detik.");
		echo "<script type='text/javascript'>refresh_frame2()</script>";
		echo "<script type='text/javascript'>window.onload=setTimeout('window.location=\"?mnux=$_SESSION[mnux]\"', 1000);</script>";
		
	}
?>

  <script>
  JSFX_FloatDiv("divInfo", 0, 100).flt();
  </script>
</BODY>

</HTML>
