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
  KonfirmasiTanggal("../$_SESSION[mnux].laporan.adm.anggota.xls.php", "Cetak");
}

function Cetak() {
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=Adm.Anggota-Pustaka-".$_SESSION['TglMulai']."-".$_SESSION['TglSelesai']);
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
  echo  "<p><font size=+3>Laporan Anggota Baru</font></p>";
  echo  "<p><font size=+1>Periode $Mulai ~ $Selesai</font></p>";
}
function BuatIsinya($period, $p) {
	BuatHeaderTabel($_fid, $p);
  $s = "select p.AnggotaID,p.Nama, p.Handphone, p.Email, p.Alamat, p.Kunjungan, p.NA, p.StatusMhswID, p.InstitusiID, p.NamaInstitusi, m.member_since_date from 
  			pustaka_anggota p 
  			left outer join app_pustaka1.member m on m.member_id = p.AnggotaID
  			where  
  			'$_SESSION[TglMulai]' <= m.member_since_date
      and m.member_since_date <= '$_SESSION[TglSelesai]'
      and m.member_type_id in (3,2)
  			 order by p.InstitusiID, p.Nama";
  //echo "Select: $s";
  $r = _query($s);
  
  $n = 0;
  while ($w = _fetch_array($r)) {
      
    $n++;
	echo  "<tr>
				<td>".$n."</td>
				<td>".$w['AnggotaID']."</td>
				<td>".$w['Nama']."</td>
				<td>$w[Email]</td>
				<td>$w[Alamat]</td>
				<td>$w[Handphone]</td>
				<td>".(empty($w['PRD']) ? $w['InstitusiID']:$w['PRD'])."</td>
				<td>$w[NamaInstitusi]</td>
				<td>$w[member_since_date]</td>
			</tr>";
  }
}
function BuatHeaderTabel($fid, $p) {
	echo  "<table><tr><th>Nmr</th>
				<th>AnggotaID</th>
				<th>Nama</th>
				<th>Email</th>
				<th>Alamat</th>
				<th>Handphone</th>
				<th>Prodi</th>
				<th>Nama Instansi</th>
				<th>Tanggal Daftar</th>
			</tr>";
  
}

?>
