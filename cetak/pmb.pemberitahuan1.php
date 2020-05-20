<?php
// Author: Emanuel Setio Dewo
// 23 Jan 2006
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

$pmbid = array();
$pmbid = $_REQUEST['pmbid'];
$period = GetFields('pmbperiod', 'PMBPeriodID', $_REQUEST['pmbaktif'], '*');
if (!empty($pmbid)) {
  // buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  fwrite($f, chr(27).chr(108).chr(5));
  // Buat surat beberapa sekaligus
  for ($i=0; $i < sizeof($pmbid); $i++) {
    // Ambil data
    $pmb = GetFields("pmb p
      left outer join program prg on p.ProgramID=prg.ProgramID
      left outer join prodi prd on p.ProdiID=prd.ProdiID
      left outer join fakultas f on prd.FakultasID=f.FakultasID
      left outer join asalsekolah a on p.AsalSekolah=a.SekolahID",
      'PMBID', $pmbid[$i],
      "p.*, prd.Nama as PRD, prg.Nama as PRG, f.Nama as FAK,
      a.Nama as AsalSekolah");
    // Ambil template
    $namatemplate = "template/$_REQUEST[pmbaktif].$pmb[LulusUjian].PMB.txt";
    $ft = fopen($namatemplate, 'r');
    $tpl = fread($ft, filesize($namatemplate));
    fclose($ft);
    // Pakai template
    $_t = $tpl . chr(12);
    $_t = str_replace('~PMBID~', $pmb['PMBID'], $_t);
    $_t = str_replace('~Nama~', $pmb['Nama'], $_t);
    $_t = str_replace('~AsalSekolah~', $pmb['AsalSekolah'], $_t);
    $_t = str_replace('~FAK~', $pmb['FAK'], $_t);
    $_t = str_replace('~PRD~', $pmb['PRD'], $_t);
    $_t = str_replace('~PRG~', $pmb['PRG'], $_t);
    $_t = str_replace('~TglMulai~', FormatTanggal($period['BayarMulai']), $_t);
    $_t = str_replace('~TglSelesai~', FormatTanggal($period['BayarSelesai']), $_t);
    $_t = str_replace('~BayarSelesai~', FormatTanggal($period['BayarSelesai']), $_t);
    $_t = str_replace('~Tanggal~', date('d/m/Y'), $_t);
    // BIPOT
    $tot = 0;
    $bipot2 = GetBipot2($pmb, $pmb['BIPOTID'], $tot);
    $_t = str_replace('~BIPOT~', $bipot2, $_t);
    // Catatan diskon
    $cttDiskon = ($pmb['Diskon']>0)? GetDiskon($pmb, $bipotid, $pmb['Diskon']) : '';
    $_t = str_replace('~DISKON~', $cttDiskon, $_t);
    $_t = str_replace('~TOTALBIAYA~', number_format($tot), $_t);
    // Tuliskan ke file
    //fwrite($f, chr(27).chr(108).chr(0));
    fwrite($f, $_t);
  }
  fclose($f);
  if (empty($_REQUEST['prn'])) {
    TampilkanFileDWOPRN($nmf, '');
  }
  else {
    include_once "dwoprn.php";
    DownloadDWOPRN($nmf);
  }
}
function GetDiskon($pmb, $bipotid, $Diskon) {
  global $_lf;
  $s = "select b2.*, bn.Nama, bn.DefJumlah, bn.DefBesar, bn.Diskon
    from bipot2 b2
    left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
    where b2.BIPOTID='$bipotid' and b2.SaatID=1
      and INSTR(b2.StatusAwalID, '.$pmb[StatusAwal].')>0
    order by b2.Prioritas";
  $r = _query($s); $total = 0;
  $a = "Berdasarkan hasil rapat, Anda dinyatakan mendapat potongan SPP sebesar ".$Diskon."%".$_lf;
  while ($w = _fetch_array($r)) {
    $jml = ($w['DefJumlah'] == 0)? 1 : $w['DefJumlah'];
    //$bsr = ($w['Diskon'] == 'Y')? $w['Jumlah']-($w['Jumlah'] * $Diskon / 100) : $w['Jumlah'];
    $total += ($jml * $w['Jumlah'] * $Diskon/100);
    //$a .= $jml . ' x '. $bsr . ' ' . (($w['Diskon']=='Y')? 'DISKON' : '').$_lf;
  }
  $tot = $tot - $total;
  return $a .
    "sehingga Anda cukup membayar: Rp. ".number_format($tot);
}

function GetBipot2($pmb, $bipotid, &$total) {
  global $_lf;
  $s0 = "select b2.*, bn.Nama, bn.DefJumlah, bn.DefBesar, bn.Diskon
    from bipot2 b2
    left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
    where b2.BIPOTID='$bipotid' and b2.SaatID=1
      and INSTR(b2.StatusAwalID, '.$pmb[StatusAwalID].')>0
    order by b2.Prioritas";
  $r0 = _query($s0);
  $thn = substr($w['PMBID'], 0, 4);
  $a = ''; $n = 0; $total = 0;
  while ($w0 = _fetch_array($r0)) {
    if ($w0['Jumlah'] == 0) {}
    elseif ($w0['GunakanGradeNilai'] == 'Y') {
      if (strpos($w0['GradeNilai'], ".$pmb[GradeNilai].") === false) {}
      else {
        $n++;
        $a .= InsertBIPOT($n, $w0, $tot, $bipotid, $pmb);
        $total += $tot;
      }
    }
    else {
    $n++;
    $a .= InsertBIPOT($n, $w0, $tot, $bipotid, $pmb);
    $total += $tot;
    }
  }
  $strtotal = str_pad(' ', 57, ' '). str_pad('-', 15, '-').$_lf;
  $strtotal .= str_pad('Total :', 57, ' ', STR_PAD_LEFT) .
    str_pad(number_format($total), 15, ' ', STR_PAD_LEFT);
  return $a . $strtotal;
}

function InsertBIPOT($n, $w, &$tot, $bipotid, $pmb) {
  global $_lf;
  $a = str_pad($n, 5, ' ', STR_PAD_LEFT) .'. ';
  $a .= str_pad($w['Nama'], 30, ' ');
  if ($w['DefJumlah'] > 1) {
    // Jika BPP SKS
    if ($w['BIPOTNamaID'] == 5) {
      $detbipot = GetFields('bipot', "BIPOTID", $bipotid, "*");
      $_prd = $detbipot['ProdiID'];
      $w['DefJumlah'] = GetaField('prodi', 'ProdiID', $_prd, "DefSKS");
    }
    $det = $w['DefJumlah']." x ".number_format($w['Jumlah']);
    $jml = $w['DefJumlah'] * $w['Jumlah'];
    $a .= str_pad($det, 15, ' ', STR_PAD_LEFT);
  }
  else {
    $a .= str_pad(' ', 15, ' ');
    $jml = $w['Jumlah'];
  }
  $tot = $jml;
  $a .= str_pad(number_format($jml), 20, ' ', STR_PAD_LEFT);
  return $a . $_lf;
}

/*
function InsertBIPOT($n, $w, &$tot) {
  global $_lf;
  $a = str_pad($n, 2, ' ', STR_PAD_LEFT) .'. ';
  $a .= str_pad($w['Nama'], 30, ' ');
  if ($w['DefJumlah'] > 0) {
    $det = $w['DefJumlah']." x ".number_format($w['Jumlah']);
    $jml = $w['DefJumlah'] * $w['Jumlah'];
    $a .= str_pad($det, 15, ' ', STR_PAD_LEFT);
  }
  else {
    $a .= str_pad(' ', 15, ' ');
    $jml = $w['Jumlah'];
  }
  $tot = $jml;
  $a .= str_pad(number_format($jml), 20, ' ', STR_PAD_LEFT);
  return $a . $_lf;
}
*/

include_once "disconnectdb.php";
?>
