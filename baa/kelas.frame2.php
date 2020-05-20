<?php
	// *** Main ***
	
	session_start();
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	
	$opk_kelas = GetSetVar('opk_kelas', '');
	$prodi = GetSetVar('prodi', '');
	$program = GetSetVar('program', '');
	$opk_page = GetSetVar('opk_page', 0);
	$opk_tahun = GetSetVar('opk_tahun', '');
	$opk_tahun_filter = GetSetVar('opk_tahun_filter', '');
	$opk_sort = GetSetVar('opk_sort', 'Nilai');
	$kelamin_by = GetSetVar('kelamin_by', '');
	$nilai_dari = GetSetVar('nilai_dari', '');
	$nilai_sampai = GetSetVar('nilai_sampai', '');
	
	
	
	function loadJavaScripts()
	{	echo "
					<SCRIPT LANGUAGE='JavaScript'>

					function CheckAll(chk)
					{	
						start = (document.getElementById('Start'+chk)).value;
						total = (document.getElementById('Jumlah'+chk)).value;
						for (i = start; i <= total; i++)
						{	
							(document.getElementById(chk+i)).checked = true;
						}
					}
					
					function UnCheckAll(chk)
					{
						start = (document.getElementById('Start'+chk)).value;
						total = (document.getElementById('Jumlah'+chk)).value;
						
						for (i = start; i <= total; i++)
						{
							(document.getElementById(chk+i)).checked = false;
						}
					}
					
					function CekNilai(myform)
					{	
						nilai_dari = (document.getElementById('nilai_dari')).value;
						nilai_sampai = (document.getElementById('nilai_sampai')).value;						
						errormessage = \"\";
						tempcheck = 0;
						
						if(nilai_dari == \"\") { errormessage += \"Nilai awal tidak boleh kosong. \\n\"; tempcheck++;}
						else if(nilai_dari < 0) { errormessage += \"Nilai awal harus lebih besar dari. 0\\n\"; }
						if(nilai_sampai == '') { errormessage += \"Nilai akhir tidak boleh kosong. \\n\"; tempcheck++;}
						else if(nilai_sampai > 100) { errormessage += \"Nilai akhir harus lebih kecil dari 100. \\n\"; }
						if(tempcheck == 2)
						{	if(nilai_dari > nilai_sampai) { errormessage += \"Nilai awal lebih besar dari nilai akhir.\\n\"; }
						}
						
						if(errormessage != \"\") 
						{ 	alert(\"ERROR.\\n\\n\"+errormessage+\"\\nHarap dicek kembali.\"); 
							(document.getElementById('nilai_dari')).value='';
							(document.getElementById('nilai_sampai')).value='';
						}
						else
						{	myform.submit();
						}
					}
					
					function changePage(page)
					{	prevpage = page-1;
						top.frames['frame2'].location = 'kelas.frame2.php?opk_page='+prevpage;
					}
					
					function tahunsubmit()
					{	
						
							empty_session_kelas() 
						
					}
					
					function refresh_frame1() {
						x = top.frames['frame1'].location = 'kelas.frame1.php';
					}
					
					function fnEditFilter(gel, md, id) {
						
						lnk = '../$_SESSION[mnux].flt.php?gel='+gel+'&md='+md;
						//alert(lnk);
						win2 = window.open(lnk, '', 'width=620, height=600, scrollbars, status');
						if (win2.opener == null) childWindow.opener = self;
						}

					function turnOffFilter()
					{	
						turn_off_filter()
					}
					
					
					</script>
		";		
	}
?>
	
<!DOCTYPE html>
<html lang="en">
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META http-equiv="cache-control" content="max-age=0">
  <META http-equiv="pragma" content="no-cache">
  <META http-equiv="expires" content="0" />
  <META http-equiv="content-type" content="text/html; charset=UTF-8">
  
  <META content="Arisal Yanuarafi" name="author" />
  <META content="sistem Informasi Manajemen" name="description" />
<?php loadJavaScripts(); ?>
  <link rel="stylesheet" type="text/css" href="../themes/default/ddcolortabs.css" />
    <link rel="stylesheet" type="text/css" href="../themes/default/index.css" />
    <!-- The styles -->
	<link id="bs-css" href="../themes/<?=$_Themes;?>/css/bootstrap-cerulean.css" rel="stylesheet">
    <!-- jQuery -->
	<script src="../themes/<?=$_Themes;?>/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" languange="javascript" src="../themes/<?=$_Themes;?>/js/arisal.js"></script>
  
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
	$gos = (empty($_REQUEST['gos']))? 'DaftarMahasiswa' : $_REQUEST['gos'];
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
	
	function DaftarMahasiswa()
	{	//echo "SESSION urut nama: $_SESSION[kelas_urut_nama]<br>";
		
		if($_SESSION['opk_sort']=='Nilai')	$order = "NilaiUjian DESC, MhswID ASC";	
		elseif($_SESSION['opk_sort']=='MhswID')	$order = "MhswID";	
		else $order = "Nama ASC";
		if(!empty($_SESSION['prodi'])) $wherejurusan = "and ProdiID='$_SESSION[prodi]'";
		else $wherejurusan = '';
		if(!empty($_SESSION['program'])) $whereprogram = "and ProgramID='$_SESSION[program]'";
		else $whereprogram = '';
		if(!empty($_SESSION['opk_tahun_filter'])) $wheretahunakademik = "and TahunID='$_SESSION[opk_tahun_filter]'";
		else $wheretahunakademik = '';
		if(!empty($_SESSION['kelamin_by']))
		{	if($_SESSION['kelamin_by']=='W')
			{	$wherefilter .= "and Kelamin='W'"; }
			else
			{	$wherefilter .= "and Kelamin='P'"; }
		}
		
		$wherefilter .= "and NilaiUjian >= '$_SESSION[nilai_dari]' and NilaiUjian <= '$_SESSION[nilai_sampai]'";
		
		$limit = 40;
		$start_page = 40*$_SESSION['opk_page'];
		$s2="select * from `mhsw` 
				where KelasID >=0 
				$wheretahunakademik
				$wherejurusan
				$whereprogram
				$wherefilter
				order by $order 
				limit $start_page, $limit
				";
		//echo "Select: $s2";
		$r2=_query($s2);
		
		$s3="select MhswID from `mhsw` 
				where KelasID >= 0
				$wheretahunakademik
				$wherejurusan
				$whereprogram
				$wherefilter";
		$r3 = _query($s3);
		$n3 = _num_rows($r3);
				
		
		if(empty($_SESSION['opk_sort'])) $_SESSION['opk_sort']='Nilai';
		if($_SESSION['opk_sort']=='Nilai') { $sort_nilai='checked'; $sort_nama=''; }
		else if($_SESSION['opk_sort']=='MhswID') { $sort_nim='checked'; $sort_nama=''; $sort_nilai=''; }
		else { $sort_nilai=''; $sort_nama='checked'; }
		
		if($_SESSION['kelamin_by']=='P') { $pria = 'checked'; $wanita=''; }
		else { $wanita='checked'; $pria=''; }
	
		$optjurusan = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
		$optprog = GetOption2('program', 'ProgramID', 'ProgramID', $_SESSION['program'], "KodeID='".KodeID."'", 'ProgramID');
		$opttahun = GetOption2('tahun', "concat(TahunID, ' (', ProdiID, if(ProdiID='','','-'), ProgramID, ')')", 'TahunID', $_SESSION['opk_tahun_filter'], "NA='N'", 'TahunID');
		$optkelamin = GetOption2('kelamin', "concat(Kelamin, ' - ', Nama)", 'Kelamin', $_SESSION['kelamin_by'], '', 'Kelamin');
		
		echo "<table class=box cellspacing=1 align=center width=595>
				<form name='filter_data' action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden name='gos' value=''/>
				<input type=hidden name='opk_page' value='0' />
				
				<tr>
					<td class=inp>Filter Tahun Akademik</td>
					<td class=ul1 colspan=2><input type=text name='opk_tahun_filter' value='$_SESSION[opk_tahun_filter]' size=3 maxlength=10>
											<input type=submit name='Set' value='Set'></td>
					<td class=inp>Urutkan berdasarkan:</td>
					<td class=ul1><input type=radio name='opk_sort' value='Nilai' $sort_nilai onChange='this.form.submit()'>Nilai
								  <input type=radio name='opk_sort' value='nama' $sort_nama onChange='this.form.submit()'>Nama
								  <input type=radio name='opk_sort' value='MhswID' $sort_nim onChange='this.form.submit()'>NPM</td>
				</tr>
				<tr>
					<td class=inp>Filter Prodi:</td>
					<td class=ul1 colspan=5><select name='prodi' onChange='this.form.submit()' />$optjurusan </select>
											<select name='program' onChange='this.form.submit()' />$optprog</select></td>	
					</tr>
				<tr><td class=inp>Filter Kelamin:</td>
				<td class=ul1><select name='kelamin_by' onChange=\"this.form.submit()\">$optkelamin</select></td>
				<td class=inp>Filter Nilai:</td>
				<td class=ul1 colspan=3><input type=text id='nilai_dari' name='nilai_dari' value='$_SESSION[nilai_dari]' size=2 maxlength=3> s/d
							    <input type=text id='nilai_sampai' name='nilai_sampai' value='$_SESSION[nilai_sampai]' size=2 maxlength=3>
								&nbsp<input type=button name='Filter' value='Filter Nilai' onClick=\"CekNilai(this.form)\"></td>
			 </tr>
				</form>
			</table>";
		
		echo "<table class=box cellspacing=1 align=center width=595>
				<form name='myform' action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]'>
				<input type=hidden name='gos' value='AllocateAll'>
				<tr>
					<th class=ttl><input type=button name='CheckAllKelas' value='Cek Semua' onClick=\"CheckAll('PM')\" /></th>	
					<th class=ttl>No. Mhsw</th>
					<th class=ttl colspan=2>Nama</th>
					<th class=ttl>Prodi</th>
					<th class=ttl>Nilai</th>
				</tr>
			";	
		$x = $start_page;
		
		if(!empty($_REQUEST['CopyPilihan']))
		{	$CopyPilihan = $_REQUEST['CopyPilihan']; 		
		}
		while($w2=_fetch_array($r2))
		{ 	$x++;	
			
			$checkthis = ($CopyPilihan[$x]=='')? '' : 'checked';
			echo "<tr>
					<td class=ul1 align=center>$x. <input type=checkbox id='PM$x' name='PilihMahasiswa[]' value='$w2[MhswID] $checkthis'></td>
					<td class=ul1>$w2[MhswID]</td>
					<td class=ul1>$w2[Nama]</td>
					<td align=right><img src='../img/$w2[Kelamin].bmp'></td>
					<td class=ul1 align=center>$w2[ProdiID]</td>
					<td class=ul1 colspan=2>$w2[NilaiUjian]</td>
				</tr>";
		}
		
		if($n3!=0)
		{	$totalpage = floor(($n3/$limit))+1;
			$fontpage = ($_SESSION['opk_page']+1 == 1)? '<font color=red>1</font>' : '<font color=green>1</font>'; 
			$pagestring = "<a href='#' onClick=\"changePage(1); this.form.submit();\">$fontpage</a>";
			for($j=2; $j <= $totalpage; $j++)
			{	$fontpage = ($j==$_SESSION['opk_page']+1)? '<font color=red>' : '<font color=green>'; 
				$pagestring .= ", <a href='#' onClick=\"changePage($j); this.form.submit();\">$fontpage$j</font></a>";
			}
			
			$nextstartpage = $start_page+1;
			echo "<tr>
					<td align=center><input type=button name='UnCheckAllKelas' value = 'Clear Semua' onClick=\"UnCheckAll('PM')\" /></td>
					<td colspan=4 align=center><input type=submit name='AllocateAll' value='Alokasikan Mahasiswa yang Dicek ke Kelas ini'/>
							<input type=hidden id='StartPM' name='StartPM' value='$nextstartpage' />
							<input type=hidden id='JumlahPM' name='JumlahPM' value='$x' />
					</td>
				</tr>
				<tr>
					<td class=inp>Total Peserta:</td>
					<td class=ul1><b>$n3</b></td>
				</tr>
				<tr>
					<td class=inp colspan=2>Halaman: </td>
					<td class=ul1 colspan=3>$pagestring</td>
				</tr>";
		}
		echo "		</form>
			</table>";
		
	}
	
	function AllocateAll()
	{	
		$PilihMahasiswa = $_REQUEST['PilihMahasiswa'];
	
		if(!empty($_SESSION['opk_kelas']))
		{	
			$s9 = "select * from `kelas` where KelasID='$_SESSION[opk_kelas]' ";
			$r9 = _query($s9);
			$w9 = _fetch_array($r9);
			$x9 = $w9['KapasitasSekarang'];
			$tempalert = 'OK';
			
			foreach($PilihMahasiswa as $terpilih)
			{	
				$x9++;
				if($x9 > $w9['KapasitasMaksimum'])
				{	$tempalert = 'Kelas penuh';
					break;	}
				else
				{	$sz = "update `mhsw` set KelasID='$_SESSION[opk_kelas]' where MhswID='$terpilih'";
					$rz = _query($sz);	}
			}
		
			$s7 = "select MhswID from `mhsw` where KelasID='$_SESSION[opk_kelas]'"; 
			$r7 = _query($s7);
			$x7 = _num_rows($r7);
		
			$s8 = "update `kelas` set KapasitasSekarang='$x7' where KelasID='$_SESSION[opk_kelas]' ";
			$r8 = _query($s8);
			
			$NamaKelas = GetaField('kelas', 'KelasID', $_SESSION['opk_kelas'], 'Nama');
			
			if($tempalert == 'OK')
			{
				echo Konfirmasi("Berhasil", 
				"Penempatan mahasiswa yang dipilih ke dalam kelas: <b>$NamaKelas</b> berhasil.<br />
				Tampilan akan kembali ke semula dalam 2 detik.");
			}
			else
			{	echo ErrorMsg("Kelas Penuh",
				"Penempatan seluruh mahasiswa yang dipilih ke dalam kelas: <b>$NamaKelas</b> gagal.<br />
				<br><b>Kelas Penuh.</b><br><br>
				Tampilan akan kembali ke semula dalam 2 detik.");
	
			}
			
			echo "<script type='text/javascript'>refresh_frame1()</script>";
			echo "<script type='text/javascript'>window.onload=setTimeout('window.location=\"?mnux=$_SESSION[mnux]\"', 2000);</script>";
		}
		else
		{	echo ErrorMsg("Gagal", 
				"Kelas belum dipilih. Silakan memilih kelas terlebih dahulu.<br>
					Tampilan akan kembali ke semula dalam 1 detik");
			/*echo "<table><form action='?' method=POST>";
			$xx = 0;
			foreach($PilihMahasiswa as $terpilih)
			{	$xx++;
				echo "<input type=hidden name='CopyPilihan[$xx]' value='Y'>";
			}	
					
			echo "
				</form></table>"; */
			//echo "	<script type='text/javascript'>window.onload=setTimeout('this.form.submit()', 1500);</script>";
			echo "<script type='text/javascript'>window.onload=setTimeout('window.location=\"?mnux=$_SESSION[mnux]\"', 1500);</script>";
		}
	}
	
?>


  <script>
  JSFX_FloatDiv("divInfo", 0, 100).flt();
  </script>
</BODY>

</HTML>
