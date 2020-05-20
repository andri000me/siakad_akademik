<?php
// Author: Emanuel Setio Dewo
// 22 March 2006

include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
include_once "terbilang.php";

$mhswid = $_REQUEST['mhswid'];
$pmbid = $_REQUEST['pmbid'];
$pmbmhswid = $_REQUEST['pmbmhswid']+0;
$khsid = $_REQUEST['khsid']+0;
$rekid = $_REQUEST['rekid'];
$bpmblank = GetSetVar('bpmblank');

if (!empty($rekid)) {
  // Data
  if ($pmbmhswid == 0) {
    $mhsw = GetFields("pmb m
      left outer join program prg on m.ProgramID=prg.ProgramID
      left outer join prodi prd on m.ProdiID=prd.ProdiID",
      "PMBID", $pmbid,
      "m.*, prg.Nama as PRG, prd.Nama as PRD");
    $khs = array();
    $khs['TahunID'] = $mhsw['PMBPeriodID'];
    $nomernya = $mhsw['PMBID'];
  }
  else {
    $mhsw = GetFields("mhsw m
      left outer join program prg on m.ProgramID=prg.ProgramID
      left outer join prodi prd on m.ProdiID=prd.ProdiID",
      "MhswID", $mhswid,
      "m.*, prg.Nama as PRG, prd.Nama as PRD");
    $khs = GetFields('khs', 'KHSID', $khsid, '*');
    $nomernya = $mhsw['MhswID'];
  }
  $NamaBank = GetaField('rekening', 'RekeningID', $rekid, 'Nama');
  // Buat Nomer BPK
  $BayarMhswID = GetNextBPM();
  // Tambahkan Data
  $s = "insert into bayarmhsw (BayarMhswID, TahunID, RekeningID,
    PMBID, MhswID, TrxID, PMBMhswID,
    Proses, LoginBuat, TanggalBuat)
    values ('$BayarMhswID', '$khs[TahunID]', '$rekid',
    '$mhsw[PMBID]', '$mhsw[MhswID]', 1, $pmbmhswid,
    0, '$_SESSION[_Login]', now())";
  $r = _query($s);
  // Buat cetakannya
  $isi = $_lf.$_lf;
  // header
  $mrg = str_pad(' ', 10, ' ');
  $isi .= str_pad($BayarMhswID, 60, ' ', STR_PAD_LEFT).$_lf.$_lf.$_lf.$_lf;
  $isi .= $mrg.$mrg . $rekid . $_lf.$_lf.$_lf.$_lf;
  $isi .= $mrg.$mrg . $khs['TahunID'] . $_lf.$_lf;
  $isi .= $mrg.$mrg . $nomernya . ' / ' . $mhsw['Nama'] . $_lf.$_lf.$_lf.$_lf;
  $isi .= $mrg.$mrg . $NamaBank . $_lf.$_lf;
  // masuk ke detail
  $isi .= $_lf.$_lf;
  $arr = array();
  $jml = 0; $krg = 0; $nmr = 0;
  if ($_REQUEST['bpmblank'] == 1) {
    // Ambil isinya
    if ($pmbmhswid == 0) {
      $whr = "and bm.PMBID='$mhsw[PMBID]' ";
    }
    else {
      $whr = "and bm.MhswID='$mhsw[MhswID]' ";
    }
    $s1 = "select bm.*, 
    format(bm.Besar, 0) as BSR,
    format(bm.Besar*bm.Jumlah, 0) as TOT,
    format(bm.Besar*bm.Jumlah - bm.Dibayar, 0) as KRG,
    bn.Nama
    from bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
      left outer join bipotnama bn on bm.BIPOTNamaID=bn.BIPOTNamaID
    where bm.PMBMhswID='$pmbmhswid'
      and bm.TahunID='$khs[TahunID]'
      and bm.TrxID=1 and bn.RekeningID='$rekid'
      and (bm.Besar*bm.Jumlah - bm.Dibayar)>0
      $whr
    order by bn.Baris";
    $r1 = _query($s1);
    $isi .= $mrg . "No." . str_pad('Deskripsi', 30, ' ') . "Jml   ".
      str_pad('Besar', 12, ' ', STR_PAD_LEFT). '   '.
      str_pad('Total', 12, ' ', STR_PAD_LEFT).
      str_pad('Kekurangan', 12, ' ', STR_PAD_LEFT).$_lf.
      $mrg.str_pad('-', 78, '-').$_lf;
    while ($w1 = _fetch_array($r1)) {
      $nmr++;
      $jml += $w1['Jumlah'] * $w1['Besar'];
      $krg += $w1['Jumlah'] * $w1['Besar'] - $w1['Dibayar'];
      $isi .= $mrg . 
        str_pad($nmr, 3, ' ').
        str_pad($w1['Nama'], 30, ' ') .
        str_pad($w1['Jumlah'], 3, ' ', STR_PAD_LEFT) . ' x '.
        str_pad($w1['BSR'], 12, ' ', STR_PAD_LEFT) . ' = '.
        str_pad($w1['TOT'], 12, ' ', STR_PAD_LEFT).
        str_pad($w1['KRG'], 12, ' ', STR_PAD_LEFT).
        $_lf.$_lf;
    }
    $isi .= $mrg . str_pad('-', 78, '-').$_lf;
    $tot = number_format($krg);
    $isi .= $mrg . str_pad(' ', 54, ' ') .
      str_pad($tot, 24, ' ', STR_PAD_LEFT) . $_lf.$_lf.$_lf;
    $isi .= $mrg . SpellNumberID($krg). ' rupiah'.$_lf;
  }
  
  
  // Buat Tujuan
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, $isi);
  fwrite($f, chr(12));
  fclose($f);

  /*
  // Download
  header("---DwnLdDt");
  header("Content-Length: ".filesize($nmf));
  header("Content-type: application/dwoprn");
  header("Content-Disposition: attachment; filename=\"$nmf\"");
  header("Content-ID: $nmf");
  header("Content-Description: Download Data");
  readfile($nmf);
  */
  //DownloadDWOPRN($nmf);
  TampilkanFileDWOPRN($nmf);
}

include_once "disconnectdb.php";
?> 
