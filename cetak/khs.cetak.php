<?php
// Author: Emanuel Setio Dewo
// 19 April 2006
session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $_lf;
  echo "<body bgcolor=#EEFFFF>";
  // Parameters
  $pos = $_SESSION['KHS-POS'];
  $max = $_SESSION['KHS-MAX'];
  $nmf = $_SESSION['KHS-FILE'];
  $_khsid = $_SESSION['khsid'];
  $khsid = $_khsid[$pos];
  if ($pos < $max) {
    // Buat file
    $f = fopen($nmf, 'a');
  
    // Buat KHS
    $khs = GetFields("khs khs
      left outer join mhsw m on khs.MhswID=m.MhswID
      left outer join program prg on khs.ProgramID=prg.ProgramID
      left outer join prodi prd on khs.ProdiID=prd.ProdiID", 
      "khs.JumlahMK <> 0 and khs.KHSID", $khsid, 
      "khs.*, m.Nama as NamaMhsw, m.BatasStudi, prg.Nama as PRG, prd.Nama as PRD");
    $thn = GetFields("tahun", "ProgramID='$khs[ProgramID]' and ProdiID='$khs[ProdiID]' and TahunID", $khs['TahunID'], "*");
    $bal = ($khs['Biaya'] - $khs['Potongan']) - ($khs['Bayar'] + $khs['Tarik']);
    $prsBal = $bal + $bal * 5/100;
    $_bal = number_format($prsBal);
    $peringatan = "    Anda masih memiliki utang sebesar Rp.$_bal. $_lf 
    Anda tidak akan mendapat KHS dan juga tidak dapat mendaftar KRS $_lf
    sebelum utang ini anda lunasi. $_lf
    Lakukan pembayaran di Bank sesuai dengan prosedur yang berlaku.";
    fwrite($f, chr(27).chr(64).chr(27).chr(18).chr(27).chr(67).chr(33)); // chr67+chr33 -> membuat menjadi 33 baris
    // Buat header
    $mrghdr = str_pad(' ', 18, ' ');
	  $_BatasStudi = NamaTahun($khs['BatasStudi']);
    $hdr = $_lf.$_lf.$_lf.$_lf.
      $mrghdr . $thn['Nama'] . $_lf.
      $mrghdr . $khs['PRG'] . '/' . $khs['PRD'] . $_lf.
      $mrghdr . $khs['MhswID'] .$_lf.
      $mrghdr . $khs['NamaMhsw'] . $_lf.
      $mrghdr . $_BatasStudi . 
      $_lf.$_lf.$_lf.$_lf.$_lf;
    fwrite($f, $hdr);
    /*if ($bal <= 0) {
      $isi = $_lf . GetIsiKHS($khsid, $khs);
    } else { 
      $isi = $_lf . "$peringatan" . $_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf;
    }*/
	 if ($bal <= 0) {
      $isi = $_lf . GetIsiKHS($khsid, $khs);
    } else { 
      $isi = $_lf . "$peringatan" . $_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf;
    }
    fwrite($f, $isi);
    $tgl = date('d-m-Y');
    fwrite($f, str_pad(' ', 67) . $tgl.$_lf);
    fwrite($f, chr(12));
  
    // Tutup file
    fclose($f);
    // refresh page
    echo "<p>Proses KHS: <font size=+2>$pos/$max</font><br />
	$khsid &raquo; $khs[NamaMhsw]</p>";
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    echo "<p>Pembuatan file Cetak KHS telah selesai.<br />
	Untuk memulai mencetak klik: <a href='$nmf'><img src='img/printer.gif' border=0></a></p>";
    //echo "<p>Untuk Preview Hasil Cetak klik di <a href=blanko.preview.php?nmf=$nmf target=_blank>sini</a></p>";
    echo "<p>Untuk melihat preview klik <a href=blanko.preview.php?nmf=$nmf target=_blank><img src='img/view.png' border=0></a></p>";
  }
  $_SESSION['KHS-POS']++;
}
function GetIsiKHS($khsid, $khs) {
  global $_lf;
  $s = "select krs.KRSID, krs.MKKode, krs.MhswID, left(mk.Nama, 35) as NamaMK,
    krs.SKS, krs.GradeNilai, krs.BobotNilai, j.SKS as JSKS
    from krs krs
      left outer join mk mk on krs.MKID=mk.MKID
      left join jadwal j on j.JadwalID = krs.JadwalID
    where krs.TahunID='$khs[TahunID]' and krs.MhswID='$khs[MhswID]'
      and j.JadwalSer = 0
	    and krs.StatusKRSID='A'
      and (j.JenisJadwalID <> 'R' or j.JenisJadwalID is NULL) 
      and krs.GradeNilai not in ('')
    order by krs.MKKode";
  $r = _query($s); 
  $isi = array();
  for ($i=0; $i<14; $i++) $isi = '';
  $_sks = 0; $_nxk = 0;
  $mrgkiri = str_pad(' ', 6, ' ');
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    //$TAkah = GetaField('');
    if ($w['GradeNilai'] <> '-'){
      $_sks += $w['JSKS'];
      
      
    }
    $nxk = $w['SKS'] * $w['BobotNilai'];
    $_nxk += $nxk;
    $isi[] = $mrgkiri.str_pad($w['MKKode'], 7) . ' '.
      str_pad($w['NamaMK'], 35) . ' '.
      str_pad($w['JSKS'], 3, ' ', STR_PAD_LEFT) . '    '.
      str_pad($w['GradeNilai'], 3) . ''.
      str_pad($nxk, 4, ' ', STR_PAD_LEFT). ' ';
  }
  for ($i = $n; $i < 12; $i++) $isi[] = $mrgkiri . str_pad(' ', 59, ' ');

  // Tuliskan summary

  $mrgknn = str_pad(' ', 6);
  $bts = str_pad(' ', 59);
  for ($i=0; $i<14; $i++) {
    $isi[$i] = str_pad($isi[$i], 59, ' ');
    //$isi[$i] = (empty($isi[$i]))? $bts.$mrgknn : $isi[$i];
  }
  $ips = ($_sks == 0) ? 0 : number_format($_nxk/$_sks, 2);
  $ipk = GetaField('mhsw', 'MhswID', $khs['MhswID'], 'IPK')+0;
  //$ips_ = GetaField();
  $TotalSKS = GetaField('mhsw', 'MhswID', $khs['MhswID'], 'TotalSKS');

  $arr = GetFields('krsprc', "GradeNilai not in ('', '-') and MhswID", $khs['MhswID'], 
    "sum(SKS) as TSKS");
  $BebanMax = GetBebanMax($khs['ProdiID'], $khs['IPS']+0);
  $isi[1] .= $mrgknn . str_pad($_sks, 6, ' ', STR_PAD_LEFT);
  $isi[3] .= $mrgknn . str_pad($_nxk, 6, ' ', STR_PAD_LEFT);
  $isi[5] .= $mrgknn . str_pad($khs['IPS']+0, 6, ' ', STR_PAD_LEFT); //$khs['IPS']
  $isi[7] .= $mrgknn . str_pad($ipk, 6, ' ', STR_PAD_LEFT);
  $isi[9] .= $mrgknn . str_pad($TotalSKS, 6, ' ', STR_PAD_LEFT);
  $isi[11] .= $mrgknn . str_pad($BebanMax, 6, ' ', STR_PAD_LEFT);
  return (empty($isi))? $_lf : implode($_lf, $isi) . $_lf;
}
function GetBebanMax($prd, $ips) {
  $maxsks = GetaField('maxsks', "DariIP <= $ips and $ips <= SampaiIP and ProdiID", $prd, 'SKS')+0;
  return $maxsks;
}

?>
