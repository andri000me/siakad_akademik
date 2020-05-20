<?php 
// Rekap Dosen Mengajar
// Author 	: Arisal Yanuarafi
// Start 	: 15 September 2014
session_start();
   include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

// *** Parameters ***
	$TahunID = GetSetVar('ThnAkd');
	$ProgramID = GetSetVar('ProgramID');
	$ProdiID = GetSetVar('ProdiID');
// Tgl Mulai
$TglMulai_y = GetSetVar('TglMulai_y', date('Y'));
$TglMulai_m = GetSetVar('TglMulai_m', date('m'));
$TglMulai_d = GetSetVar('TglMulai_d', date('d'));
$_SESSION['TglMulai'] = "$TglMulai_y-$TglMulai_m-$TglMulai_d";
// Tgl Selesai
$TglSelesai_y = GetSetVar('TglSelesai_y', date('Y'));
$TglSelesai_m = GetSetVar('TglSelesai_m', date('m'));
$TglSelesai_d = GetSetVar('TglSelesai_d', date('d'));
$_SESSION['TglSelesai'] = "$TglSelesai_y-$TglSelesai_m-$TglSelesai_d";

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiTgl' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function KonfirmasiTgl() {
  KonfirmasiTanggal("../$_SESSION[mnux].xls2.php", "Cetak");
}

function Cetak(){
		// Filter 
	if (!empty($_SESSION['ThnAkd']))   $whr[] = "j.TahunID='$_SESSION[ThnAkd]'";
	if (!empty($_SESSION['ProgramID']))   $whr[] = "j.ProgramID='$_SESSION[ProgramID]'";
	if (!empty($_SESSION['ProdiID']))   $whr[] = "j.ProdiID='$_SESSION[ProdiID]'";
		
		$_whr = implode(' and ', $whr);
 		$_whr = (empty($_whr))? '' : ' and ' . $_whr;

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-dosen-mengajar-$_SESSION[ProdiID]-$_SESSION[ThnAkd]-$_SESSION[ProgramID]");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

	$s = "SELECT j.ProdiID, p.Nama from jadwal j left outer join prodi p on p.ProdiID=j.ProdiID where 1 $_whr group by ProdiID";
	$r =_query($s);
	?>
  <style>
table,font { font-family:'Trebuchet MS'; line-height:100%;mso-number-format:"\@"; }
.header{ font-family:'Trebuchet MS'; font-size:15px; line-height:90%; }
.garis {height:0px; line-height:0px;}
</style>
<table border=0>
<tr><td class=header align="center" colspan="12"><strong> BIRO ADMINISTRASI AKADEMIK & KEMAHASISWAAN (BAAK)<BR />
					REKAP DOSEN MENGAJAR
					<?php $thn=GetaField('tahun',"TahunID",$_SESSION['ThnAkd'],'Nama');
					echo $thn;  ?> 
                    </strong></td></tr>
 </table>
<?php while ($w = _fetch_array($r)) {
	echo "<h1>Prodi $w[Nama]</h1>";
	$s1 = "SELECT p.Pertemuan from jadwal j left outer join presensi p on p.JadwalID = j.JadwalID  where '$_SESSION[TglMulai]' <= p.Tanggal
      and p.Tanggal <= '$_SESSION[TglSelesai]' $_whr group by p.Pertemuan";
	$r1 = _query($s1); $pertemuan = '';
	while ($w1 = _fetch_array($r1)) {
		$pertemuan .= "<th class=ttl align=center colspan=2>Pertemuan $w1[Pertemuan]</th>";
	}
	?>
	<table class="bsc" border="1" width="900">
    <tr>
    <th class="ttl" align="center">No.</th>
    <th class="ttl" align="center">Matakuliah</th>
	<th class="ttl" align="center">SKS</th>
	<th class="ttl" align="center">FKT</th>
	<th class="ttl" align="center">PRG</th>
     <th class="ttl" align="center">Pengampu</th>
     <th class="ttl" align="center">Peserta</th>
     <th class="ttl" align="center">Hari</th>
     <th class="ttl" align="center">Jam</th>
    <?php echo $pertemuan;?>
  </tr> 
<?php 
		$s2 = "SELECT j.Nama as MK, j.SKS, d.Nama as DSN, h.Nama as HR, j.JamMulai, j.ProgramID, j.JadwalID, f.Nama as FKT
				from jadwal j 
				left outer join dosen d on d.Login=j.DosenID
				left outer join hari h on h.HariID=j.HariID
				left outer join prodi p on p.ProdiID = j.ProdiID
				left outer join fakultas f on f.FakultasID=p.FakultasID
				where 1 $_whr";
		$r2 = _query($s2); $n=0;
		while ($w2 = _fetch_array($r2)) {
			$n++;
			$peserta = GetaField("krs", "JadwalID", $w2['JadwalID'],"COUNT(KRSID)");
			echo "<tr>
					<td>$n</td>
					<td>$w2[MK]</td>
					<td>$w2[SKS]</td>
					<td>$w2[FKT]</td>
					<td>$w2[ProgramID]</td>
					<td>$w2[DSN]</td>
					<td>$peserta</td>
					<td>$w2[HR]</td>
					<td>$w2[JamMulai]</td>";
			$r1 = _query($s1);
			while ($w3 = _fetch_array($r1)) {
				$ptm = GetFields("presensi p left outer join dosen d on d.Login=p.DosenID", "p.Pertemuan='$w3[Pertemuan]' and p.JadwalID", $w2['JadwalID'], 
						"d.Nama,date_format(p.Tanggal, '%d/%m/%y') as DT,p.JamMulai");
				echo "<td>$ptm[Nama]</td><td>$ptm[DT] $ptm[JamMulai]</td>";
			}
		}
	} //while $w
} // function
?>