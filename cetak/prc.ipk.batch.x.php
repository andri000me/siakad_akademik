<?php
// Author: Emanuel Setio Dewo
// 19 April 2006
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
$Proses = $_REQUEST['Proses'];

// Jalankan Proses
$Proses();
include_once "disconnectdb.php";

// *** Proses IPK & Total SKS
function ProsesIPK() {
  global $_lf;
  // Ambil data mahasiswa
  $s = "select m.MhswID
    from mhsw m
      left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    where sm.Keluar='N'
      and m.ProgramID='$_REQUEST[prid]'
      and m.ProdiID='$_REQUEST[prodi]'
    order by m.MhswID";
  $r = _query($s);
  $div = str_pad('-', 79, '-').$_lf;  
  // Buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/prcipkbatch.dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, "Proses Batch IPK: ".date('d-m-Y').$_lf);
  fwrite($f, "Program: $_REQUEST[prid], Program Studi: $_REQUEST[prodi]". $_lf.$div);
  // Mulai proses
  $jmlMhsw = 0;
  while ($w = _fetch_array($r)) {
    $jmlMhsw++;
    $hsl = ProsesIndividu($w['MhswID']);
    $isi = $jmlMhsw .'. '.$w['MhswID']. ' : ' .$hsl . $_lf;
    fwrite($f, $isi);
  }
  fwrite($f, $div);
  fwrite($f, "Selesai proses. Jumlah diproses: $jmlMhsw Mahasiswa");
  // Tutup file
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}
function ProsesIndividu($mhswid) {
  $s = "select j.MKKode, krs.BobotNilai, krs.SKS
    from krs krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
    where krs.MhswID='$mhswid'
      and j.JenisJadwalID='K'
    order by j.MKKode asc, krs.BobotNilai desc";
  $r = _query($s);
  $n = 0; $mk = '';
  $_sks = 0;
  $_bbt = 0;
  $_nxk = 0;
  while ($w = _fetch_array($r)) {
    if ($mk != $w['MKKode']) {
      $mk = $w['MKKode'];
      $nxk = $w['SKS'] * $w['BobotNilai'];
      $_nxk += $nxk;
      $_sks += $w['SKS'];
    }
  }
  $_ipk = ($_sks == 0)? 0 : $_nxk/$_sks;
  $si = "update mhsw set IPK=$_ipk, TotalSKS=$_sks where MhswID='$mhswid'";
  $ri = _query($si);
  return $_sks . ', ' . $_ipk;
}
// *** Simpan Setup Keuangan
function Simpan_Setup_Keuangan() {
// Simpan setup keuangan dulu
  $HutangNext = $_REQUEST['HutangNext'];
  $DepositNext = $_REQUEST['DepositNext'];
  $HutangPrev = $_REQUEST['HutangPrev'];
  $DepositPrev = $_REQUEST['DepositPrev'];
  $s = "update keusetup set HutangNext='$HutangNext', DepositNext='$DepositNext',
    HutangPrev='$HutangPrev', DepositPrev='$DepositPrev' ";
  $r = _query($s);
  //echo $s;
  echo "Setup berhasil disimpan. <br />
    <input type=button name='Tutup' value='Tutup' onClick='javascript:window.close()'>";
}
// *** Proses Keuangan ***
function Proses_Tutup_Keuangan() {
  global $_lf;
  $tahun = $_REQUEST['tahun'];
  $keu = GetFields("keusetup", "NA", "N", "*");
  $prid = $_REQUEST['prid'];
  $prodi = $_REQUEST['prodi'];
  // Ambil data next semester
  $s0 = "select TahunID from tahun
    where TahunID>'$tahun'
      and ProgramID='$prid' and ProdiID='$prodi'
    limit 1";
  $r0 = _query($s0);
  if (_num_rows($r0) == 0) die ("Tahun akademik setelah <b>$tahun</b> belum dibuat. <br />
    Hubungi Ka BAA untuk membuat tahun akademik baru terlebih dahulu.");
  $w0 = _fetch_array($r0);
  $tahun2 = $w0['TahunID'];
  echo "<p>Proses untuk <b>$tahun</b>, Program: <b>$prid</b>, Program Studi: <b>$prodi</b><br />
    Nilai hutang/deposit akan ditransfer ke: <b>$tahun2</b></p><hr size=1>";
  if (!empty($tahun) && !empty($prid) && !empty($prodi)) {
    $s = "select khs.KHSID, khs.MhswID, khs.Sesi, khs.SKS, khs.BIPOTID,
      khs.SaldoAwal, khs.Biaya, khs.Potongan, khs.Bayar, khs.Tarik
      from khs khs
      where khs.TahunID='$tahun'
        and khs.ProgramID='$prid'
        and khs.ProdiID='$prodi'
        and khs.Tutup='N'
        order by khs.MhswID";
    $r = _query($s);
    $btn = "<input type=button name='Tutup' value='Tutup' onClick='javascript:window.close()'>";
    echo $btn . "<pre>";
    while ($w = _fetch_array($r)) {
      $posisi = $w['Biaya']-$w['Bayar']-$w['Potong']+$w['Tarik'];
      $TrxID = ($posisi >=0)? 1 : -1;
      // Jika ada transaksi
      if ($posisi > 0) {
        // set data semester ini
        $str1 = "$keu[HutangNext]=>$posisi, $keu[HutangPrev]=>$posisi";
        $s1 = "insert into bipotmhsw (TahunID, PMBMhswID, PMBID, MhswID,
          BIPOT2ID, BIPOTNamaID, TrxID, Jumlah, Besar, 
          Dibayar, Catatan,
          LoginBuat, TanggalBuat)
          values('$tahun', 1, '', '$w[MhswID]',
          '0', '$keu[HutangNext]', -1, 1, '0', 
          '$posisi', 'Transfer ke semester berikutnya',
          '$_SESSION[_Login]', now() )";
        $r1 = _query($s1);
        // Set balance
        $s1a = "update khs set Bayar=Bayar+$posisi where KHSID=$w[KHSID]";
        $r1a = _query($s1a);
        // set data semester depan
        $s2 .= "insert into bipotmhsw (TahunID, PMBMhswID, PMBID, MhswID,
          BIPOT2ID, BIPOTNamaID, TrxID, Jumlah, Besar,
          Dibayar, Catatan, LoginBuat, TanggalBuat)
          values('$tahun2', -1, '', '$w[MhswID]',
          '0', '$keu[HutangNext]', 1, 1, '$posisi',
          '0', 'Transfer dari semester sebelumnya', '$_SESSION[_Login]', now() )";
        $r2 = _query($s2);
        // Set balance semester berikutnya
        $s2a = "update khs set Biaya=Biaya+$posisi where MhswID='$w[MhswID]' and TahunID='$tahun2' ";
        $r2a = _query($s2a);
      }
      elseif ($posisi < 0) {
        $s1 = "$keu[DepositNext]=>$posisi, $keu[DepositPrev]=>$posisi";
      }
      else {
        $str1 = " >< ";
      }
      echo str_pad($w['MhswID'], 20) .
        str_pad(number_format($posisi), 20, ' ', STR_PAD_LEFT). ' ' . $str1. 
        $_lf;
    }
    echo "</pre>". $btn;
  }
  else echo ErrorMsg("Gagal Proses",
    "Tentukan Tahun Akademik, Program, dan Program Studi terlebih dahulu.");
}
?>
