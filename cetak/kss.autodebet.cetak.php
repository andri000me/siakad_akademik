<?php

session_start();
include "../sisfokampus.php";
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $_HeaderPrn, $_lf;
  echo "<body bgcolor=#EEFFFF>";
   // Parameters
  $pos = $_SESSION['KSS-POS'];
  $max = $_SESSION['KSS-MAX'];
  $nmf = $_SESSION['KSS-FILE'];
  $_khsid = $_SESSION['khsid'];
  $khsid = $_khsid[$pos];
  if ($pos < $max) {
    $mhswid = GetaField('khs',"TahunID = '$_SESSION[tahun]' and KHSID", $khsid,'mhswid');
    //$mhswid = $_SESSION['mhswid'];
    $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join fakultas f on prd.FakultasID=f.FakultasID", 
    'm.MhswID', $mhswid, 
    "m.MhswID, m.Nama, m.TempatLahir, m.TanggalLahir, m.PenasehatAkademik, 
    m.Alamat, m.Kota, m.KodePos,
    m.ProdiID, m.ProgramID,
    prd.Nama as PRD, prg.Nama as PRG, f.Nama as FAK");
  $tahun = $_SESSION['tahun'];
  //$khsid = $_REQUEST['khsid'];
  $khs = GetFields("khs", 'KHSID', $khsid, '*');
  //if ($khs['Cetak'] == 'N') CetakKSS1($tahun, $mhsw, $khs);
  //else GagalCetak($mhsw, $khs);
  CetakKSS1($tahun, $mhsw, $khs);
  
  echo "<p>Proses Lembar Rencana Studi: <font size=+2>$pos/$max</font><br />
  $khsid &raquo; $mhsw[Nama]</p>";
  echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
  $nmf = HOME_FOLDER  .  DS . "tmp/kss.$_SESSION[prodi]$_SESSION[DariNPM].$_SESSION[_Login].dwoprn";
    echo "<p>Pembuatan file Cetak Lembar Rencana Studi telah selesai.<br />
	Untuk memulai mencetak klik: <a href='$nmf'><img src='img/printer.gif' border=0></a></p>";
  }
  $_SESSION['KSS-POS']++;
}

function CetakKSS1($tahun, $mhsw, $khs) {
  global $_HeaderPrn, $_lf;
  $stm = GetFields('statusmhsw', 'StatusMhswID', $khs['StatusMhswID'], '*');
  if ($stm['Nilai'] == 0) {}  
  // Buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/kss.$_SESSION[prodi]$_SESSION[DariNPM].$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'a');
  fwrite($f, chr(18).chr(27).chr(15).chr(27).chr(67).chr(18));
  //.chr(27).chr(67).chr(18)
  fwrite($f, $_lf.$_lf);
  // Isinya
  $brs = 15;
  $arr = array();
  $div = str_pad('', 154, '-'). $_lf;
  for ($i=0; $i <= $brs; $i++) $arr[$i] = '';
  TuliskanDataUtama($mhsw, $khs, $arr);
  if ($stm['Nilai'] == 0) TuliskanStatusMhsw($mhsw, $khs, $arr, $stm);
  else TuliskanIsiKRS($mhsw, $khs, $arr);
  TuliskanKanan($mhsw, $khs, $arr);
  
  for ($i=0; $i <= $brs; $i++) fwrite($f, $arr[$i].$_lf);
  fwrite($f, chr(27).chr(18).chr(67).chr(66));
  //fwrite($f, chr(12));  
  fclose($f);
  //include "dwoprn.php";
  //DownloadDWOPRN($nmf);
}

function GagalCetak($mhsw, $khs) {
  echo ErrorMsg("Tidak Dapat Dicetak",
    "Tidak dapat mencetak Kartu Studi Semester (KSS) karena sudah pernah dicetak. <br />
    Sudah pernah dicetak <b>$khs[KaliCetak]</b> kali.
    <hr size=1 color=silver>
    Pilihan: <input type=button name='Tutup' value='Tutup' onClick='javascript:window.close()'>");
}
function TuliskanDataUtama($mhsw, $khs, &$arr) {
  $mrg = str_pad(' ', 15);
  $TGL = FormatTanggal($mhsw['TanggalLahir']);
  $SKRG = date('d-m-Y');
  $thn = GetFields("tahun", "ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and TahunID",
    $khs['TahunID'], "Nama, date_format(TglAkhirKSS, '%d-%m-%Y') as TNIL");
  $rek = GetaField('pejabat', 'JabatanID', 'REKTOR', 'Nama');
  $pa = GetaField('dosen d', "d.Login", $mhsw['PenasehatAkademik'], "concat(d.Nama, ', ', d.Gelar)");
  $Alamat = (empty($mhsw['Alamat'])) ? $mhsw['AlamatAsal'] : $mhsw['Alamat'];
	$Kota = (empty($mhsw['Kota'])) ? $mhsw['KotaAsal'] : $mhsw['Kota'];
	if (strlen($Alamat) > 45) {
		$needle = ' ';
		$piece = $pos = strripos($Alamat, $needle);
		$Alamat1 = substr($Alamat,0,$piece);
		$Alamat2 = strstr($Alamat,substr($Alamat,$piece,$piece));
		$Alamat2 = str_pad($Alamat2, 25);
	} else {
		$Alamat1 = $Alamat;
		$Alamat2 = '';
	}
  $Alamat1 = str_replace(chr(13), ' ', $Alamat1);
  $Alamat1 = str_replace(chr(10), '', $Alamat1);
	$arr[0] .= $mrg . str_pad($thn['Nama'], 59);
  $arr[1] .= $mrg . str_pad($mhsw['MhswID'], 59);
  $arr[2] .= $mrg . str_pad($mhsw['Nama'], 59);
  $arr[3] .= $mrg . str_pad($mhsw['TempatLahir'] . ', '. $TGL, 59);
  $arr[4] .= $mrg . str_pad($Alamat1, 59);
  //$arr[5] .= $mrg . str_pad($Alamat2, 59);
	$arr[5] .= $mrg . str_pad($Kota . ' '. $mhsw['KodePos'], 59);
  $arr[6] .= $mrg . str_pad($mhsw['FAK']. '/ '. $mhsw['PRD'], 59);
  $arr[7] .= $mrg . str_pad($pa, 59);
  $arr[8] .= $mrg . str_pad(' ', 30) . str_pad($SKRG, 29);
  $arr[9] .= str_pad(' ', 74);
  $arr[10] .= str_pad('   ' . $thn['TNIL'], 74);
  $arr[11] .= str_pad(' ', 74);
  $arr[12] .= str_pad(' ', 74); 
  $arr[13] .= str_pad($rek, 65, ' ', STR_PAD_LEFT);
}
function TuliskanStatusMhsw($mhsw, $khs, &$arr, $stm) {
  $arr[6] .= str_pad(strtoupper($stm['Nama']), 61, ' ', STR_PAD_BOTH);
  for ($i = 1; $i < 13; $i++) $arr[$i] .= str_pad(' ', 68, ' ');
}
function TuliskanIsiKRS($mhsw, $khs, &$arr) {
  $s = "select j.MKKode, LEFT(j.Nama, 39) as NM, j.SKS, j.JenisJadwalID, krs.StatusKRSID,
    j.NamaKelas
    from krs krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
    where krs.KHSID='$khs[KHSID]' and j.JenisJadwalID='K' and j.JadwalSer = 0
    order by j.MKKode";
  $r = _query($s);
  $i = 0; $sks = 0;
  while ($w = _fetch_array($r)) {
    $sks += $w['SKS'];
    $stt = ($w['StatusKRSID'] != 'A')? " ($w[StatusKRSID])" : '';
    $w['NM'] .= ($w['JenisJadwalID'] == 'K')? '' : "($w[JenisJadwalID])";
    $arr[$i] .= str_pad($w['MKKode'], 10) .
      str_pad($w['NM'].$stt, 40) .
      str_pad($w['SKS'], 6). 
      str_pad($w['NamaKelas'], 12, ' ');
    $i++;
  }
  for ($j=$i; $j < 13; $j++) $arr[$j] .= str_pad(' ', 68, ' ');
  $arr[13] .= str_pad($sks, 60, ' ', STR_PAD_LEFT);
}
function TuliskanKanan($mhsw, $khs, &$arr) {
  $arr[6] .= $mhsw['MhswID'];
  $arr[7] .= $mhsw['Nama'];
}  
?>
