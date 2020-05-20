<?php
// Author: Emanuel Setio Dewo
// 01 May 2006
// Hari Buruh Internasional
// www.sisfokampus.net

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
  $jdwl = GetFields('jadwal', 'JadwalID', $_REQUEST['jdwlid'], "*");
  $jnsr = ($jdwl['JenisJadwalID'] == 'R') ? "(RESPONSI) " : "";
  $dsn = TRIM($jdwl['DosenID'], '.');
  $arrdsn = explode('.', $dsn);
  $strdsn = (empty($arrdsn))? "GaAdaNih" : implode(',', $arrdsn);
  $nmdsn = GetArrayTable("select concat(Nama, ', ', Gelar) as DSN from dosen where Login in ($strdsn) order by Nama",
    "Login", "DSN");
  // Ambil program
  $prg = TRIM($jdwl['ProgramID'], '.');
  $_prg = explode('.', $prg);
  $prg = $_prg[0];
  $PRG = GetaField('program', 'ProgramID', $prg, 'Nama');
  // Ambil prodi
  $prd = TRIM($jdwl['ProdiID'], '.');
  $_prd = explode('.', $prd);
  $prd = $_prd[0];
  // Ambil fakultas
  $FakultasID = GetaField('prodi', 'ProdiID', $prd, 'FakultasID');
  $Fak = GetaField('fakultas', 'FakultasID', $FakultasID, 'Nama');
  // Nama tahun
  $thn = GetaField('tahun', "ProgramID='$prg' and ProdiID='$prd' and TahunID", $jdwl['TahunID'], 'Nama');
  // Buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].rinci.dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(80).chr(27).chr(108).chr(10));
  // Buat header
  $MaxCol = 117;
  $div = str_pad('-', $MaxCol, '-').$_lf;
  $jen = GetaField('jenisjadwal', 'JenisJadwalID', $jdwl['JenisJadwalID'], 'Nama');
  $tgl = date('d-m-Y H:i');
  $Hal = 1;
  $hdr = $_lf . $_lf .
    "Tgl.  : " . $tgl . "           *** Daftar Rincian Nilai Mahasiswa $jnsr***" . $_lf.
    "Form  : UKW " . $_lf. 
    "Fak.  : " . str_pad($Fak, 40) .     "Dosen  : " . $nmdsn . $_lf.
    "Jur.  : " . str_pad($prd, 40) .     "M.K.   : " . "$jdwl[MKKode] -  $jdwl[Nama]" . $_lf.
    "Sem.  : " . str_pad($thn, 40) .     "Kelas  : " . str_pad($jdwl['NamaKelas'], 40) . " Hal: #" . $_lf.
    $div.
    "No. NPM             Nama Mahasiswa                  Tgs1  Tgs2  Tgs3  Tgs4  Tgs5  Pres   UTS   UAS  Resp    Nilai Grd". $_lf.
    "                                                   ".
    str_pad(number_format($jdwl['Tugas1'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '.
    str_pad(number_format($jdwl['Tugas2'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '.
    str_pad(number_format($jdwl['Tugas3'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '.
    str_pad(number_format($jdwl['Tugas4'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '.
    str_pad(number_format($jdwl['Tugas5'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '.
    str_pad(number_format($jdwl['Presensi'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '.
    str_pad(number_format($jdwl['UTS'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '.
    str_pad(number_format($jdwl['UAS'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '.
    str_pad(number_format($jdwl['Responsi'], 1).'%', 5, ' ', STR_PAD_LEFT). ' '. 
    $_lf.
    $div;
  $hdr1 = str_replace('#', $Hal, $hdr);
  fwrite($f, $hdr1);
  // Tuliskan isinya
  //GetArrayTable($sql, $key, $label, $separator=', ') {
  $ikut = GetArrayTable("select StatusKRSID, Nama from statuskrs where Ikut='Y' order by StatusKRSID",
    "StatusKRSID", "StatusKRSID", ', ', "'");
  $s = "select krs.*, LEFT(m.Nama, 30) as NamaMhsw, sk.Ikut, sk.Hitung
    from krs krs
      left outer join mhsw m on krs.MhswID=m.MhswID
      left outer join statuskrs sk on krs.StatusKRSID=sk.StatusKRSID
    where krs.JadwalID='$jdwl[JadwalID]' and krs.StatusKRSID in ($ikut)
    order by krs.MhswID";
  $r = _query($s); $n = 0;
  $brs = 0; $maxbrs = 50;
  $arrNilai = array();
  $JmlMhsw = 0;
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    $w['NilaiAkhir'] = number_format($w['NilaiAkhir'], 2);
    $NilaiAkhir = ($w['Hitung'] == 'Y')? $w['NilaiAkhir'] : '-';
    $GradeNilai = ($w['Hitung'] == 'Y')? $w['GradeNilai'] : $w['StatusKRSID'];
    if ($NilaiAkhir != '-') {
      $arrNilai[$w['GradeNilai']] += 1;
      $JmlMhsw++;
    }
    fwrite($f, str_pad($n.'.', 4) .
      str_pad($w['MhswID'], 15) . ' '.
      str_pad($w['NamaMhsw'], 30) . ' '.
      str_pad($w['Tugas1'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['Tugas2'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['Tugas3'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['Tugas4'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['Tugas5'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['Presensi'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['UTS'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['UAS'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['Responsi'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($NilaiAkhir, 8, ' ', STR_PAD_LEFT) . ' '.
      str_pad($GradeNilai, 5, ' ', STR_PAD_RIGHT).
      $_lf);
    if ($brs > $maxbrs) {
      $Hal++; $brs=1;
      fwrite($f, $div);
      fwrite($f, chr(12));
      $hdr1 = str_replace('#', $Hal, $hdr);
      fwrite($f, $hdr1);
    }
  }
  fwrite($f, $div);
  // Ambil nilai
  $s = "select * from nilai where ProdiID='$prd' order by Bobot desc";
  $r = _query($s);
  $str = array();
  $akh = ''; $cnt = _num_rows($r); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $Jml = (empty($arrNilai[$w['Nama']]))? '0' : $arrNilai[$w['Nama']]+0;
    $persen = ($JmlMhsw == 0)? 0 : number_format($Jml / $JmlMhsw *100, 1);
    $tnd = ($cnt == $n)? "< " : ">=";
    $nil = ($cnt == $n)? $akh : $w['NilaiMin'];
    $str[] = $tnd. str_pad($nil, 5, ' ', STR_PAD_LEFT). " = " .
      str_pad($w['Nama'], 3) . "=" . str_pad($Jml, 3) . "=" .
      str_pad($persen, 5, ' ', STR_PAD_LEFT).'%    ';
    $akh = $w['NilaiMin'];
  }
  $dua = ceil(sizeof($str)/2);
  $foot = array(); $n = 0;
  for ($i = 0; $i < sizeof($str); $i++) {
    $foot[$n] .= $str[$i];
    $n++;
    if ($n >= $dua) $n = 0;
  }
  // Buat tanda tangan
  $foot[0] .= "Paraf Dosen:                        Pimpinan Fakultas:";
  $foot[3] .= $nmdsn;
  for ($i = 0; $i < sizeof($foot); $i++)
    fwrite($f, $foot[$i].$_lf);
  // Tutup file
  fwrite($f, chr(12));
  fclose($f);
  // Cetak
  include_once "dwoprn.php";
  DownloadDWOPRN($nmf);
}

?>
