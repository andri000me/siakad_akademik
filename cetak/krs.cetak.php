<?php
// Author: Emanuel Setio Dewo
// 19 April 2006
session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
include_once "mhswkeu.sav.php";
Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $_lf;
  // Setup Parameter
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields('khs khs
    left outer join program prg on khs.ProgramID=prg.ProgramID
    left outer join prodi prd on khs.ProdiID=prd.ProdiID
    left outer join mhsw m on khs.MhswID=m.MhswID', 
    'KHSID', $khsid,
    "khs.*, m.BatasStudi, m.Nama as NamaMhsw, m.ProdiID, m.Autodebet, prg.Nama as PRG, prd.Nama as PRD");
  $_REQUEST['mhswid'] = $khs['MhswID'];
  $_REQUEST['pmbmhswid'] = 1;
  $_REQUEST['DariKRS'] = 1; // Menandai bahwa ini dieksekusi dari modul KRS
  $MhswID = $_REQUEST['mhswid'];
  $thn = GetFields("tahun", "ProgramID='$khs[ProgramID]' and ProdiID='$khs[ProdiID]' and TahunID",
    $khs['TahunID'], "*");
  $angmhsw = GetaField('mhsw', 'MhswID', $MhswID, 'TahunID');
  // Menetapkan KRS atau KPRS
  $skrg = date('Y-m-d');
  // Jika autodebet
  
  if ($khs['Autodebet'] == 'Y') {
    $tglad = FormatTanggal($thn['TglAutodebetSelesai']);
    $tglad2 = FormatTanggal($thn['TglAutodebetSelesai2']);
    $str1 = "Anda harus menyetor ke rekening Anda paling lambat $tglad.";
    $str2 = "untuk di-autodebet oleh bank. Lewat tgl tersebut akan didenda 10%";
    $str3 = "dari BPP SKS. Pembayaran BPP & Denda paling lambat tgl $tglad2.";
    $str4 = "Lewat tanggal tersebut Anda harus CUTI KULIAH.";
  }
  // Jika bukan autodebet
  else {
    // KRS
    if ($khs['Sesi'] <= 1) {
      $str1 = '';
      $str2 = '';
    }
    elseif ($angmhsw >= 2002){
      $tglad = FormatTanggal($thn['TglAutodebetSelesai']);
      $tglad2 = FormatTanggal($thn['TglAutodebetSelesai2']);
      $str1 = "Anda harus menyetor ke rekening Anda paling lambat $tglad.";
      $str2 = "untuk di-autodebet oleh bank. Lewat tgl tersebut akan didenda 10%";
      $str3 = "dari BPP SKS. Pembayaran BPP & Denda paling lambat tgl $tglad2.";
      $str4 = "Lewat tanggal tersebut Anda harus CUTI KULIAH.";
    }
    elseif ($thn['TglKRSMulai'] <= $skrg and $skrg <= $thn['TglCetakKSS1']) {
      $tglkrsselesai = FormatTanggal($thn['TglKRSSelesai']);
      $tglcetakkss = FormatTanggal($thn['TglCetakKSS1']);
      $tglbayarselesai = FormatTanggal($thn['TglBayarSelesai']);
      $str1 = "Cetak KSS paling lambat tanggal $tglcetakkss"; 
      $str2 = "dengan menyerahkan KRS ini ke BAA. Lewat tgl tersebut,";
      $str3 = "mahasiswa terkena denda 10% sampai tanggal $tglbayarselesai.";
      $str4 = "Sesudah tanggal ini, jika belum cetak KSS, Anda harus CUTI KULIAH.";
    }
    // KPRS
    elseif ($thn['TglUbahKRSMulai'] <= $skrg and $skrg <= $thn['TglCetakKSS2']) {
      $tglkrsselesai = FormatTanggal($thn['TglUbahKRSSelesai']);
      $tglcetakkss = FormatTanggal($thn['TglCetakKSS2']);
      $str1 = "Cetak ulang KSS paling lambat tanggal $tglcetakkss"; 
      $str2 = "dengan menyerahkan KPRS ini ke BAA.";
      $str3 = "Lewat tanggal tersebut tidak ada perubahan rencana studi";
      $str4 = "yang tercetak di KRS.";
    }
    else {
      $str1 = "Masa pencetakan KRS/KPRS sudah lewat.";
      $str2 = "Anda harus cuti kuliah.";
    }
  }
  // Hitung BIPOT mhsw dulu
  PrcBIPOTSesi();
  // Buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(18).chr(27).chr(15).chr(27).chr(67).chr(33)); // chr67+chr33 -> membuat menjadi 33 baris
  // Format Kertas
  $brs = 0; $maxbrs = 10;
  
  // Buat header
  $mrghdr =  str_pad(' ', 28);
  $mrg = str_pad(' ', 10);
  $spasihdr = 35;
  $BatasStudi = NamaTahun($khs['BatasStudi'], $khs['ProdiID']);
  $hdr = $_lf.$_lf.$_lf.$_lf. 
    $mrghdr . $khs['TahunID'] . ' ' . $thn['Nama'] . $_lf.
    $mrghdr . str_pad($khs['PRD'], $spasihdr) .         $str1.$_lf.
    $mrghdr . str_pad($khs['MhswID'], $spasihdr) .      $str2.$_lf.
    $mrghdr . str_pad($khs['NamaMhsw'], $spasihdr) .    $str3.$_lf.
    $mrghdr . str_pad($BatasStudi, $spasihdr) .  $str4.$_lf.
    $_lf.$_lf.$_lf.$_lf.$_lf;
  fwrite($f, $hdr);
  // Tampilkan isi KRS
  $s = "select krs.*, j.MKKode, j.Nama, j.NamaKelas, j.JenisJadwalID, j.JadwalSer, 
    time_format(j.JamMulai, '%H:%i') as JM,
    time_format(j.JamSelesai, '%H:%i') as JS,
    h.Nama as HR
    from krstemp krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
      left outer join hari h on j.HariID=h.HariID
    where krs.KHSID='$khsid' and krs.NA='N'
    order by j.MKKode, j.NamaKelas, j.HariID, j.JenisJadwalID";
  $r = _query($s);
  $sks = 0;
  while ($w = _fetch_array($r)) {
    $brs++;
    if($brs > $maxbrs) {
       fwrite($f, $mrg . "Bersambung...");
       fwrite($f, chr(12));
       fwrite($f, $hdr);
       $brs = 1;
    }                                                         
    $sks += ($w['JenisJadwalID']=='R' or ($w['StatusKRSID'] == 'S' and $w['JadwalSer'] > 0))? 0 : $w['SKS'];
    $_sks = ($w['JenisJadwalID'] != 'K')? '' : $w['SKS'];
    $skstampil = ($_sks == 0) ? '' : $_sks;
    $w['Nama'] .= ($w['JenisJadwalID'] != 'K')? " ($w[JenisJadwalID])" : '';
    
	if ($w['StatusKRSID'] == 'S' and $w['JadwalSer'] > 0 ) {
      $w['Nama'] = '';
      $w['MKKode'] = '';
    }
    $isi = $mrg . 
      str_pad($w['MKKode'], 8).
      str_pad($w['Nama'], 40).
      str_pad($skstampil, 4, ' ', STR_PAD_LEFT) . '     '.
      str_pad($w['NamaKelas'], 8) .
      str_pad($w['HR'], 8). 
      $w['JM'] . '-' .$w['JS'] . '    '.
      $w['CatatanError'].
      $_lf;
    fwrite($f, $isi);
  }
  fwrite($f, str_pad("Total SKS : ", 58, ' ', STR_PAD_LEFT) . 
  str_pad($sks, 4, ' ', STR_PAD_LEFT) . $_lf);
  
  // Ambil KRS GAGAL
  $s = "select krs.*, j.MKKode, j.Nama, j.NamaKelas, j.JenisJadwalID,
    time_format(j.JamMulai, '%H:%i') as JM,
    time_format(j.JamSelesai, '%H:%i') as JS,
    h.Nama as HR
    from krstemp krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
      left outer join hari h on j.HariID=h.HariID
    where krs.KHSID='$khsid' and krs.NA='Y'
    order by j.MKKode, j.JenisJadwalID";
  $r = _query($s);
  if (_num_rows($r)>0) {
    fwrite($f, $mrg."KRS GAGAL:".$_lf);
    $_sks = ($w['JenisJadwalID'] != 'K')? '' : $w['SKS'];
    $w['Nama'] .= ($w['JenisJadwalID'] != 'K')? " ($w[JenisJadwalID])" : ''; 
    while ($w = _fetch_array($r)) {
      $isi = $mrg . 
      str_pad($w['MKKode'], 8).
      str_pad($w['Nama'], 40).
      str_pad($_sks, 4, ' ', STR_PAD_LEFT) . '       '.
      str_pad($w['NamaKelas'], 3) .        
      str_pad($w['HR'], 10). 
      $w['JM'] . '-' .$w['JS'] . ' '.
      $w['CatatanError'].
      $_lf;
      fwrite($f, $isi);
    }
    fwrite($f, $_lf);
  }
  // Ambil BIPOT mhsw Lama
  $s = "select bm.*, LEFT(bn.Nama, 14) as BNama
    from bipotmhsw bm
      left outer join bipotnama bn on bm.BIPOTNamaID=bn.BIPOTNamaID
    where bm.MhswID='$MhswID' and bm.TahunID='$khs[TahunID]'
      and (bm.Jumlah * bm.Besar) > bm.Dibayar
    order by bn.Urutan";
  //fwrite($f, $s);
  if ($khs['Sesi'] <= 1) {
    $s = "select bm.*, LEFT(bn.Nama, 14) as BNama
    from bipotmhsw bm
      left outer join bipotnama bn on bm.BIPOTNamaID=bn.BIPOTNamaID
    where bm.MhswID='$MhswID' and bm.TahunID='$khs[TahunID]'
    order by bn.Urutan";
  }
  $r = _query($s); $tbia = 0;
  $arrbia = array(); $col = 0; $mcol = 4;
  fwrite($f, $_lf);
  while ($w = _fetch_array($r)) {
    $bia = $w['TrxID'] * $w['Jumlah'] * $w['Besar'];
    $tbia += $bia;
    $_bia = number_format($bia);
    $isi = str_pad($w['BNama'], 15).
      str_pad($_bia, 12, ' ', STR_PAD_LEFT). '  ';
    if ($col < $mcol) {
    }
    else {
      fwrite($f, $_lf);
      $col = 0;
    }
    if ($col == 0) fwrite($f, $mrg);
    $col++;
    fwrite($f, $isi);
  }
  fwrite($f, $_lf.$mrg. "Tot Biaya:  Rp ". str_pad(number_format($tbia), 12, ' ', STR_PAD_LEFT).$_lf);
  $tgl = date('d-m-Y  H:i');
  fwrite($f, str_pad("Dicetak oleh: $_SESSION[_Login], $tgl", 114, ' ', STR_PAD_LEFT).$_lf);
  
  // Tutup file
  fwrite($f, chr(12));
  fclose($f);
  // Tambahkan counter cetak KRS
  $s = "update khs set CetakKRS=CetakKRS+1 where KHSID=$khs[KHSID]";
  $r = _query($s);
  if (empty($_REQUEST['prn'])) {
    TampilkanFileDWOPRN($nmf, "krs");
  }
  else {
    include_once "dwoprn.php";
    DownloadDWOPRN($nmf);
  }
}
?>
