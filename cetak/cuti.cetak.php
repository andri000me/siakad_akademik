<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="author" content="Emanuel Setio Dewo">
  <meta name="generator" content="Sisfo Kampus, www.sisfokampus.net">
  <title>Cetak Formulir Cuti</title>
  </head>
  <body>
  
<?php
// *** includes ***
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
include_once "terbilang.php";

// *** Main ***
$mhswid = $_REQUEST['mhswid'];
$mhsw = GetFields("mhsw", 'MhswID', $mhswid, "*");
$tahun = $_REQUEST['tahun'];
CetakFormulirCuti($mhswid, $mhsw, $tahun);

// functions
function CetakFormulirCuti($mhswid, $mhsw, $tahun) {
  global $_lf, $KodeID;
  // Data mhsw
  //$krs = GetFields("krs", "StatusKRSID='A' and MhswID", $mhsw['MhswID'], "sum(SKS) as TSKS, format(sum(SKS*BobotNilai)/sum(SKS), 2) as IPK");
  //$_ips = GetFields('krs left join jadwal j on krs.JadwalID = j.JadwalID', "(j.JenisJadwalID is null or j.JenisJadwalID <> 'R') and StatusKRSID='A' and (GradeNilai<>'-' or GradeNilai <> '' and not GradeNilai is NULL) and krs.Final = 'Y' and krs.MhswID",$mhswid,
    //"sum(krs.SKS * BobotNilai)/sum(krs.SKS) as IPS, sum(krs.SKS) as SKS");
  $thn = GetFields('tahun', "ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and TahunID", $tahun, "*");
  $khs = GetFields('khs', "TahunID='$tahun' and MhswID", $mhswid, "*");
  $regakhr = GetaField('khs', "TahunID < '$tahun' and StatusMhswID='A' and MhswID", $mhswid, 'TahunID', "Order By TahunID DESC");
  //$_Balance = $khs['Biaya'] - $khs['Potongan'] + $khs['Tarik'] - $khs['Bayar'];
  $bolos = GetAfield('khs left outer join tahun on tahun.TahunID = khs.TahunID', "khs.TahunID < '$tahun' and tahun.ProdiID = '$khs[ProdiID]' and khs.TahunID > '$regakhr' and tahun.SP = 'N' and StatusMhswID = 'P' and MhswID", $mhswid, "count(khs.TahunID)");
  $_Balance = HitungHutang($mhswid, $regakhr);
  if ($_Balance >= 0) {
    $jdl = "Kewajiban Keuangan";
  } else { 
  $jdl = "Deposit Sebesar";
  $_Balance = str_replace('-', '', $_Balance);
  }
  $bls = ($bolos != 0) ? "(Bolos $bolos semester)" : '' ;
  $Balance = number_format($_Balance);
  $pernah = GetArrayTable("select TahunID from khs where MhswID='$mhswid' and StatusMhswID='C' order by TahunID",
    "TahunID", "TahunID", ', ');
  $pernah = (empty($pernah))? '-' : $pernah;
  $TglLahir = FormatTanggal($mhsw['TanggalLahir']);
  // Buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/cuti.dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(18));
  fwrite($f, chr(27).chr(108).chr(5)); // margin
  fwrite($f, $_lf.$_lf);
  // Tuliskan data
  $mrg = '   ';
  fwrite($f, str_pad("Formulir Permohonan Cuti Kuliah", 79, ' ', STR_PAD_BOTH).$_lf.$_lf);
  fwrite($f, "Saya, yang bertandatangan di bawah ini:".$_lf);
  fwrite($f, $mrg . str_pad("Nama Mahasiswa", 25, ' '). " : " . $mhsw['Nama'] . $_lf);
  fwrite($f, $mrg . str_pad("NPM", 25, ' ') . " : " . $mhsw['MhswID'] . $_lf);
  fwrite($f, $mrg . str_pad("Tempat, Tgl Lahir", 25, ' ') . " : " . $mhsw['TempatLahir'] . ', ' . $TglLahir . $_lf);
  fwrite($f, $mrg . str_pad("SKS yang telah diambil", 25, ' ') . " : " . $mhsw['TotalSKS'] . ' SKS'. $_lf);
  fwrite($f, $mrg . str_pad("Registrasi Akhir", 25, ' ') . " : " . NamaTahun($regakhr) . " $bls" .$_lf);
  fwrite($f, $mrg . str_pad("IPK (Index Prestasi Kum.)", 25, ' ') . " : " . $mhsw['IPK'] . $_lf);
  fwrite($f, $mrg . str_pad("Alamat", 25, ' ') . " : " . $mhsw['Alamat'] . $_lf);
  fwrite($f, $mrg . str_pad(' ', 25, ' ') . '   ' . $mhsw['Kota'] .' '. $mhsw['KodePos'] . $_lf);
  fwrite($f, $mrg . str_pad($jdl, 25, ' ') . " : Rp. " . $Balance .$_lf);
  fwrite($f, $mrg . str_pad("Pernah cuti kuliah", 25, ' ') . " : " . $pernah . $_lf. $_lf);
  
  fwrite($f, "Mengajukan permohonan cuti kuliah pada semester $thn[Nama] karena:".$_lf . $_lf);
  $garis = $mrg . str_pad('_', 75, '_');
  fwrite($f, $garis . $_lf . $_lf .$garis . $_lf . $_lf);
  
  // footer 1
  fwrite($f, str_pad(' ', 45, ' ') . "Jakarta, " . date('d-m-Y') . $_lf);
  fwrite($f, str_pad("Disetujui/Tidak Disetujui", 45, ' ') . "Mahasiswa" . $_lf);
  fwrite($f, "Pimpinan Fakultas,". $_lf.$_lf.$_lf.$_lf.$_lf);
  fwrite($f, str_pad("(                            )", 45, ' ') . "( $mhsw[Nama] )" . $_lf.$_lf);
  // footer 2
  $pa = GetaField('dosen', 'Login', $mhsw['PenasehatAkademik'], "concat(Nama, ', ', Gelar)");
  $kaperpus = GetaField('pejabat', 'JabatanID', 'KAPERPUS', 'Nama');
  fwrite($f, str_pad("Mengetahui :", 45, ' ') . $_lf);
  fwrite($f, str_pad("Perpustakaan $KodeID :", 45, ' ') . $_lf);
  fwrite($f, str_pad("Mahasiswa ini tidak memiliki ", 45, ' ') . "Mengetahui :" . $_lf);
  fwrite($f, str_pad("pinjaman buku yg belum dikembalikan.", 45, ' '). "P.A.".$_lf);
  fwrite($f, $_lf.$_lf.$_lf.$_lf);
  fwrite($f, str_pad("($kaperpus)", 45, ' '). "($pa)" . $_lf);
  
  fwrite($f, $_lf.$_lf);
  fwrite($f, "Catatan: Formulir ini harus dikembalikan ke TU Fakultas untuk dibuatkan".$_lf);
  fwrite($f, "SK Cuti Kuliah");
  fwrite($f, chr(12));  
  // Jangan lupa tutup file
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'cuti');

}

function HitungHutang($mhswid, $regakhr){
  $s0 = "Select (Biaya - Potongan + Tarik - Bayar) as JML from khs where TahunID >= $regakhr and MhswID=$mhswid  and StatusMhswID='A'";
  $r0 = _query($s0);
  while ($w0 = _fetch_array($r0)){
    $jmltot += $w0['JML'];
  }
  return $jmltot;
}

include_once "disconnectdb.php";
?>

  </body>
</html>
