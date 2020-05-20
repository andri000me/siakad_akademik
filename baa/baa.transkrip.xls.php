<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
$_uasProdi = $_REQUEST['_uasProdi2'];
$_uasProg  = $_REQUEST['_uasProg2'];
$_uasTahun = $_REQUEST['_uasTahun2'];

$namafile = "rekap-jadwal-uas.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-jadwal-uas");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

$s3="select Nama from tahun where TahunID='$_uasTahun'";
$r3=mysql_query($s3);
 while ($w3 = mysql_fetch_array($r3)) {
 $nmTahun=$w3[Nama];
 }
// Buat Header
  echo "<table class=box border=1 cellspacing=1 align=center>";
  $hdr = "
  <tr><td colspan=9 align=center border=0><h2>Jadwal Ujian Akhir Semester $nmTahun</h2></td></tr>
  <tr><th class=ttl width=40>#</th>
      <th class=ttl width=100>Hari<br />Tanggal</th>
	  <th class=ttl width=100>Mulai <br />Jam</th>
	  <th class=ttl >Nama Kelas</th>
	  <th class=ttl >Mata Kuliah</th>
      <th class=ttl >Peserta</th>
	  <th class=ttl >Lokal</th>
	  <th class=ttl >Penguji</th>
	  <th class=ttl >Pengawas</th>
	  </tr>";

 
  $s1 = "select  ke.Nama as NamaKelas,do.Nama,do.Login,ju.DosenID as Pengawas,ja.Nama as MK,ja.MKKode,ju.RuangID as _UASRuang,ju.JadwalUASID,
	            date_format(ju.Tanggal, '%d-%M-%Y') as _UASTanggal,
			    huas.Nama as _UASHari, ju.JumlahMhsw as _JumlahMhswUAS,
			    LEFT(ju.JamMulai, 5) as _UASJamMulai, LEFT(ju.JamSelesai, 5) as _UASJamSelesai
				from kelas ke, dosen do, jadwal ja, jadwaluas ju left outer join hari huas on huas.HariID = date_format(ju.Tanggal, '%w')
				where ke.KelasID=ja.NamaKelas and do.Login=ja.DosenID and ja.JadwalID=ju.JadwalID and ju.TahunID='$_uasTahun' order by ju.Tanggal, _UASJamMulai, MK ,  NamaKelas DESC";
	  
  $r1 = mysql_query($s1); $n = 0;
  $HariID = -320;
  $kanan = "<img src='img/kanan.gif' />";
  echo $hdr;
  while ($w = mysql_fetch_array($r1)) {
    $n++;
		$s2="select upper(Nama) as Nama from dosen where Login='$w[Login]'";
		$r2 = mysql_query($s2);
		while ($w2 = mysql_fetch_array($r2)) {
			$nmDosen=$w2['Nama'];
		}
			$s3="select upper(Nama) as Nama from dosen where Login='$w[Pengawas]'";
			$r3 = mysql_query($s3);
			while ($w3 = mysql_fetch_array($r3)) {
			$nmPengawas=$w3['Nama'];
			}
echo "<tr><td>$n</td>
		<td align=center>$w[_UASHari]<br />$w[_UASTanggal]</td>
		<td align=center>$w[_UASJamMulai] - $w[_UASJamSelesai]</td>
		<td align=center>$w[NamaKelas]</td>
		<td>$w[MK] ($w[MKKode])</td>
		<td align=center>$w[_JumlahMhswUAS]</td>
		<td align=center>$w[_UASRuang]</td>
		<td>$nmDosen</td>
		<td>$nmPengawas</td>
		</tr>";
    }

	  

	

  echo "</table></p>";

?>