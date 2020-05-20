<?php 
// Rekap Dosen Mengajar
// Author 	: Arisal Yanuarafi
// Start 	: 17 Juni 2012

  	$TahunID = GetSetVar('ThnAkd');
	$ProgramID = GetSetVar('ProgramID');
	$ProdiID = GetSetVar('ProdiID');
	$NIDN = GetSetVar('nds');
	
TampilkanJudul("Rekap Dosen Mengajar");

print_r($_SESSION);

$whr = array();
//option tahun

	 $s="SELECT DISTINCT(TahunID) as TahunID from tahun order by TahunID DESC";
	 $r=_query($s);
	 $optTahun = "<option value=''></option>";
		 while ($w=_fetch_array($r)) {
		 if ($_SESSION['ThnAkd']==$w['TahunID']) {	 		
	 		$optTahun .= "<option value='$w[TahunID]' selected>$w[TahunID]</option>";
		}
		else $optTahun .= "<option value='$w[TahunID]'>$w[TahunID]</option>";
		 }
//option program

	$s="SELECT ProgramID,Nama from program order by ProgramID";
	 $r=_query($s);
	 $optProgram = "<option value=''></option>";
		 while ($w=_fetch_array($r)) {
		 	if ($_SESSION['ProgramID']==$w['ProgramID']) {	 		
	 		$optProgram .= "<option value='$_SESSION[ProgramID]' selected>$_SESSION[ProgramID] - $w[Nama]</option>";
	 		}
		 else $optProgram .= "<option value='$w[ProgramID]'>$w[ProgramID] - $w[Nama]</option>";
		 }
//option prodi

	/*$s="SELECT ProdiID,Nama from prodi order by ProdiID";
	 $r=_query($s);
	 $optProdi = "<option value=''></option>";
		 while ($w=_fetch_array($r)) {
		 	 if ($_SESSION['ProdiID']==$w['ProdiID']) {	 		
	 		$optProdi .= "<option value='$_SESSION[ProdiID]' selected>$_SESSION[ProdiID] - $w[Nama]</option>";
	 		}
			else $optProdi .= "<option value='$w[ProdiID]'>$w[ProdiID] - $w[Nama]</option>";
		 } */
	$optProdi = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
// Mulai dan Selesai
$Mulai = (!empty($_REQUEST['Mulai_y']))? sqling("$_REQUEST[Mulai_y]-$_REQUEST[Mulai_m]-$_REQUEST[Mulai_d]") : $_REQUEST['Mulai'];
$Selesai = (!empty($_REQUEST['Mulai_y']))? sqling("$_REQUEST[Selesai_y]-$_REQUEST[Selesai_m]-$_REQUEST[Selesai_d]") : $_REQUEST['Selesai'] ;
if (empty($Mulai)) $Mulai = GetaField('tahun',"TahunID='$_SESSION[ThnAkd]' and NA", N,'TglKuliahMulai');
if (empty($Selesai)) $Selesai = GetaField('tahun',"TahunID='$_SESSION[ThnAkd]' and NA", N,'TglKuliahMulai');
$optMulai = GetDateOption4(date('Y')-1,date('Y'),$Mulai, 'Mulai');
$optSelesai = GetDateOption4(date('Y')-1,date('Y'),$Selesai, 'Selesai');
$ck = ($_REQUEST['_Periode'] == 'Y')? 'checked' : '';
// *** Filter ***
	//if (!empty($_SESSION['ThnAkd']))   $whr[] = "j.TahunID='$_SESSION[ThnAkd]'";
	if (!empty($_SESSION['ProgramID']))   $whr[] = "j.ProgramID='$_SESSION[ProgramID]'";
	$whr[] = (!empty($_SESSION['ProdiID']))? "j.ProdiID='$_SESSION[ProdiID]'" : "LOCATE(j.ProdiID, '$_SESSION[_ProdiID]')";
	if (!empty($_SESSION['nds']))   $whr[] = "D.Nama='$_SESSION[nds]'";


		
		$_whr = implode(' and ', $whr);
 		$_whr = (empty($_whr))? '' : ' and ' . $_whr;
?>
<form action="?nds=" method="post">
<table class="box" width="900">
	<tr><td class="inp">Tahun Akd:</td><td class="ul1"><select name="ThnAkd"><?php echo $optTahun; ?></select></td>
    	<td class="inp">Pilihan Prodi:</td><td class="ul1"> <select name="ProdiID"><?php echo $optProdi; ?></select></td>
  	<td class="inp">Program ID:</td><td class="ul1"><select name="ProgramID"><?php echo $optProgram; ?></select></td>
    	<td rowspan=2 class="ul1" align="center"><input type="submit" value="Tampilkan"></td></tr>
     <tr>
     	<td class="inp" >Periode:</td><td class="ul1" colspan="5"><?php echo "$optMulai &nbsp;<b>s/d</b>&nbsp; $optSelesai"; ?> <input type="checkbox" value='Y' name='_Periode' <?php echo $ck; ?> /> <sup>Set Periode?</sup>
        </td>
     </tr>
     </table>      
</form>
<?php 
			$s = "SELECT DISTINCT(j.JadwalID),D.NIDN, D.Nama as DOS, D.Gelar,D.Gelar1,m.Sesi,p.Nama as JUR,j.MKKode,j.JumlahMhsw,j.NamaKelas,k.Nama as KELAS,
					j.Nama as MAT, concat(h.Nama,' <sup>',left(j.JamMulai,5),' - ',left(j.JamSelesai,5)) as HAR,r.Nama as PRO,j.Kehadiran as Presensi, j.SKS as Kehadiran
					FROM `jadwal` j left outer join mk m on m.MKID=j.MKID
					left outer join kelas k on j.NamaKelas=k.KelasID, dosen D, prodi p, hari h,program r,jenjang n
					WHERE 
					p.ProdiID = j.ProdiID
					AND r.ProgramID = j.ProgramID
					AND n.JenjangID = p.JenjangID
					AND h.HariID = j.HariID
					AND D.Login = j.DosenID
					$_whr
					AND j.TahunID='$_SESSION[ThnAkd]'
					ORDER BY D.Nama,j.ProdiID,j.ProgramID,m.Sesi,j.HariID,j.JamMulai";
	$r		=_query($s);

	?>
	<table class="bsc" width="900">
    <?php 
	echo "<tr><td colspan=9 align=center><a href=baa/rekap.dosen.mengajar.xls.php?thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&prd=$_SESSION[ProdiID]&mk=$w[MKID]&nds=$_GET[nds]&Periode=$_REQUEST[_Periode]&Mulai=$Mulai&Selesai=$Selesai><img src='img/xls.png'></a> Download | <a href='baa/rekap.dosen.mengajar.xls2.php?thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&prd=$_SESSION[ProdiID]' target='_blank'><img src='img/xls.png'></a> Rekap XLS</td></tr>
	<tr><td colspan=8><sup>*) Klik Pada Nama Dosen untuk menampilkan data dosen bersangkutan saja.</sup></td></tr>";
	?>
    <tr>
    <th class="ttl" align="center">No.</th>
    <th class="ttl" align="center" >Smtr</th>
    <th class="ttl" align="center" >NIDN</th>
    <th class="ttl" align="center">Nama Dosen<sup>Gelar</sup></th>
     <th class="ttl" align="center">Kelas <sup>Peserta</sup></th>
    <th class="ttl" align="center">Matakuliah <sub>Kode MK</sub></th>
    <th class="ttl" align="center">Hari <sup>Jam</sup></th>
    <th class="ttl" align="center"><sup>Pertemuan</sup></th>
  </tr>
    <?php 
	$Dosen ='';
	$sks = 0;
	while ($w=_fetch_array($r)) {
	$n++;
	if ($_REQUEST['_Periode']=='Y') {
	$w['Presensi'] = GetaField('presensi',"Tanggal >= '$Mulai' and Tanggal <= '$Selesai' and JadwalID",$w[JadwalID],"count(PresensiID)");
	}
	if (empty($w[Presensi])) $Pertemuan="<sub>--</sub>";
	else $Pertemuan="<b>$w[Presensi]</b> <sup>&times;</sup>";
	echo "<tr><td class=inp align=center>$n</td>
	<td class=ul1>$w[Sesi]</td>
	<td class=ul1>$w[NIDN]</td>
	<td class=ul1><a href='?nds=$w[DOS]&_Periode=$_REQUEST[_Periode]&Mulai=$Mulai&Selesai=$Selesai'>$w[Gelar1]$w[DOS] <sup>$w[Gelar]</sup></a></td>
	<td class=ul1>$w[KELAS] <sup>$w[JumlahMhsw]</sub></td>
	<td class=ul1>$w[MAT] <sub>$w[MKKode]</sub> <b>$w[Kehadiran]</b></td>
	<td class=inp>$w[HAR]</sup></td>
	<td class=ul1 align=center>$Pertemuan</td>
	</tr>";
$sks += $w['Kehadiran'];
	} 
	if (!empty($_SESSION['nds'])) echo "<tr><td class=ul colspan=7 align=center><h2>TOTAL $sks SKS</h2></td></tr>"; ?>
	</tr></table>
    <?php //echo "Tanggal >= '$Mulai' and Tanggal <= '$Selesai' and JadwalID";
	?>