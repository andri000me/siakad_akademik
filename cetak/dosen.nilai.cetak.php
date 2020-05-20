<?php
// Author: Emanuel Setio Dewo
// 24 April 2006
// www.sisfokampus.net
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak($_REQUEST['t']);
include_once "disconnectdb.php";

function Cetak($t='UTS') {
  global $_lf;
  $jdwl = GetFields('jadwal', 'JadwalID', $_REQUEST['jdwlid'], "*");
  $dsn = TRIM($jdwl['DosenID'], '.');
  $arrdsn = explode('.', $dsn);
  $strdsn = (empty($arrdsn))? "GaAdaNih" : implode(',', $arrdsn);
  $nmdsn = GetArrayTable("select concat(Nama, ', ', Gelar) as DSN from dosen where Login in ($strdsn) order by Nama",
    "Login", "DSN");
  // Buat file
  //$nmf = HOME_FOLDER  .  DS . "tmp/uts.$_REQUEST[jdwlid].dwoprn";
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  $maxkol = 80;
  $maxbrs = 40;
  fwrite($f, chr(27).chr(77));
  // Buat header
  $div = str_pad('-', $maxkol, '-').$_lf;
  $NamaTahun = NamaTahun($jdwl['TahunID']);
  $adagrade = ($t == 'FINAL') ? 'Grade' : '';
  $adanilai = ($t == 'FINAL') ? '       ' : "Nilai  ";
  $hdr = $_lf . $_lf .
    str_pad("*** Hasil Nilai $t ***", $maxkol, ' ', STR_PAD_BOTH) . $_lf.$_lf.
    "Semester       : ". $NamaTahun . $_lf.
    "Matakuliah     : ". str_pad($jdwl['MKKode'].' - '.$jdwl['Nama'], 30) . $_lf.
    "Kelas          : ". $jdwl['NamaKelas'] . $_lf.
    "Dosen Pengampu : ". $nmdsn . $_lf.
    
    $div.
    "No. NPM             Nama Mahasiswa               $adanilai$adagrade". $_lf.
    $div;
  fwrite($f, $hdr);
  // Tuliskan isinya
  $s = "select krs.*, LEFT(m.Nama, 30) as NamaMhsw
    from krs krs
      left outer join mhsw m on krs.MhswID=m.MhswID
    where krs.JadwalID='$jdwl[JadwalID]'
    order by krs.MhswID";
  $r = _query($s); $n = 0;
  $brs = 0;
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    $grd = ($t == 'FINAL')? $w['GradeNilai'] : '';
    fwrite($f, str_pad($n.'.', 4) .
      str_pad($w['MhswID'], 15) . ' '.
      str_pad($w['NamaMhsw'], 30) . ' '.
      str_pad($w[$t], 3, ' ', STR_PAD_LEFT) . '  '.
      str_pad($grd, 3).
      $_lf);
    if ($brs > $maxbrs) {
      $hal++;
      fwrite($f, $div);
      $brs = 1;
      fwrite($f, "Hal. ".$hal.$_lf);
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
  }
  fwrite($f, $div);
  fwrite($f, str_pad(' ', 50). "Paraf Dosen" . $_lf. $_lf . $_lf . $lf);
  fwrite($f, str_pad(' ', 50). $nmdsn);
  // Tutup file
  fwrite($f, chr(12));
  fclose($f);
  // Cetak
  include_once "dwoprn.php";
  DownloadDWOPRN($nmf);
}
?>
