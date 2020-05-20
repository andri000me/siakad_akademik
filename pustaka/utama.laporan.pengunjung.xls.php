<?php

session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
  
// *** Parameters ***
$ProdiID = GetSetVar('_pustakaProdiID');
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
  KonfirmasiTanggal("../$_SESSION[mnux].laporan.pengunjung.xls.php", "Cetak");
}

function Cetak() {
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=Pengunjung-Pustaka-".$_SESSION['TglMulai']."-".$_SESSION['TglSelesai']);
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
echo "<style>td {mso-number-format:'\@';}</style>";
	$table = '';
  BuatJudulLaporan($_SESSION['_pustakaProdiID'], $table);
  BuatIsinya($_SESSION['_pustakaProdiID'], $table);

  echo "</table>";
}
function BuatJudulLaporan($_fid, $p) {
  $Mulai = FormatTanggal($_SESSION['TglMulai']);
  $Selesai = FormatTanggal($_SESSION['TglSelesai']);
  echo  "<p><font size=+3>Laporan Pengunjung Pustaka</font></p>";
  echo  "<p><font size=+1>Periode $Mulai ~ $Selesai</font></p>";
}
function BuatIsinya($period, $p) {
	BuatHeaderTabel($_fid, $p);
 $whr = (!empty($_SESSION['_pustakaProdiID']) ? " and m.ProdiID='$_SESSION[_pustakaProdiID]' ": "");
  $s = "select k.Login, m.Nama, p.Nama as PRD, k.Tanggal, k.Jam, j.Nama as Jenjang, pr.Nama as PRG, agt.NamaInstitusi  from pustaka_kunjungan k 
  			left outer join mhsw m on m.MhswID=k.Login
			left outer join prodi p on p.ProdiID=m.ProdiID
			left outer join jenjang j on j.JenjangID=p.JenjangID
			left outer join program pr on pr.ProgramID = m.ProgramID
			left outer join pustaka_anggota agt on agt.AnggotaID=k.Login
			where
      '$_SESSION[TglMulai]' <= k.Tanggal
      and k.Tanggal <= '$_SESSION[TglSelesai]'
	  $whr
    order by  k.Tanggal, k.Jam";
  //echo "Select: $s";
  $r = _query($s);
  
  $n = 0;
  while ($w = _fetch_array($r)) {
      
    $n++;
	echo  "<tr>
				<td>".$n."</td>
				<td>".$w['Tanggal']."</td>
				<td>".$w['Jam']."</td>
				<td>$w[Login]</td>
				<td>$w[Nama]</td>
				<td>$w[PRD]</td>
				<td>$w[Jenjang]</td>
				<td>$w[PRG]</td>
				<td>$w[NamaInstitusi]</td>
			</tr>";
  }
}
function BuatHeaderTabel($fid, $p) {
	echo  "<table><tr><th>Nmr</th>
				<th>Tanggal</th>
				<th>Jam</th>
				<th>NPM</th>
				<th>Nama</th>
				<th>Prodi</th>
				<th>Jenjang</th>
				<th>Program</th>
				<th>Institusi</th>
			</tr>";
  
}

?>
