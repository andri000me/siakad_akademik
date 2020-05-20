<?php
// Author: Emanuel Setio Dewo
// 22 March 2006
session_start();
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
      "m.*, prg.Nama as PRG, prd.Nama as PRD, prd.ProdiID as PRDID");
    $khs = array();
    $khs['TahunID'] = $mhsw['PMBPeriodID'];
    $nomernya = $mhsw['PMBID'] . " " . '('.$mhsw['PRDID'].')';
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
  $s = "insert into # (BayarMhswID, TahunID, RekeningID,
    PMBID, MhswID, TrxID, PMBMhswID,
    Proses, LoginBuat, TanggalBuat)
    values ('$BayarMhswID', '$khs[TahunID]', '$rekid',
    '$mhsw[PMBID]', '$mhsw[MhswID]', 1, $pmbmhswid,
    0, '$_SESSION[_Login]', now())";
  // tambahkan data asli
  $str = str_replace('#', 'bayarmhsw', $s);
  $r = _query($str);
  // tambahkan data cek
  $str = str_replace('#', 'bayarmhswcek', $s);
  $r = _query($str);
  // Buat cetakannya
  $isi = $_lf;
  // header
  $mrg = str_pad(' ', 8, ' ');
  $isi .= str_pad($BayarMhswID, 50, ' ', STR_PAD_LEFT).$_lf.$_lf.$_lf.$_lf.$_lf;
  $isi .= $mrg.$mrg . $rekid . $_lf.$_lf;
  $isi .= $mrg.$mrg . $khs['TahunID'] . $_lf;
  $isi .= $mrg.$mrg . $nomernya . ' / ' . $mhsw['Nama'] . $_lf.$_lf;
  $isi .= $mrg.$mrg . $NamaBank . $_lf.$_lf.$_lf.$_lf;
  // masuk ke detail
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
    bn.Nama, bn.Baris, bn.Detil
    from bipotmhsw bm
      left outer join bipotnama bn on bm.BIPOTNamaID=bn.BIPOTNamaID
      where bm.PMBMhswID='$pmbmhswid'
      and bm.TahunID='$khs[TahunID]'
      and bm.TrxID=1 and bn.RekeningID='$rekid'
      and (bm.Besar*bm.Jumlah - bm.Dibayar)>0
      $whr
    order by bn.Baris";
    $r1 = _query($s1);
    /*
    
    $isi .= $mrg . "No." . str_pad('Deskripsi', 30, ' ') . "Jml   ".
      str_pad('Besar', 12, ' ', STR_PAD_LEFT). '   '.
      str_pad('Total', 12, ' ', STR_PAD_LEFT).
      str_pad('Kekurangan', 12, ' ', STR_PAD_LEFT).$_lf.
      $mrg.str_pad('-', 78, '-').$_lf;
    */
    $baris = array(); $detil = array();
    for ($i = 0; $i <= 10; $i++) {
      $baris[$i] = 0;
      $detil[$i] = '';
    }
    while ($w1 = _fetch_array($r1)) {
      $nmr++;
      $jml += $w1['Jumlah'] * $w1['Besar'];
      $krg = $w1['Jumlah'] * $w1['Besar'] - $w1['Dibayar'];
	    $tott += $w1['Jumlah'] * $w1['Besar'] - $w1['Dibayar'];
      $i = ($w1['Baris'] == 0)? 10 : $w1['Baris'];
      $baris[$i] += $krg;
      if (empty($w1['Detil'])) {
        $detil[$i] = number_format($baris[$i]);
      }
      else $detil[$i] = ($w1['Detil']=='N')? '' : $w1['Jumlah'] . 'x' . number_format($w1['Besar']);
      /*
      $isi .= $mrg . 
        str_pad($nmr, 3, ' ').
        str_pad($w1['Nama'], 30, ' ') .
        str_pad($w1['Jumlah'], 3, ' ', STR_PAD_LEFT) . ' x '.
        str_pad($w1['BSR'], 12, ' ', STR_PAD_LEFT) . ' = '.
        str_pad($w1['TOT'], 12, ' ', STR_PAD_LEFT).
        str_pad($w1['KRG'], 12, ' ', STR_PAD_LEFT).
        $_lf.$_lf;
      */
    }
    // Tampilkan detail
    $MaxRow = 10;
    for ($i = 1; $i <= $MaxRow; $i++) {
      if ($baris[$i] == 0) $isi .= $_lf.$_lf;
      else {
        $tmp = '';
        if (!empty($detil[$i])) {
          $str = explode('x', $detil[$i]);
          $tmp = str_pad($str[0], 7, ' ', STR_PAD_LEFT).
            str_pad($str[1], 20, ' ', STR_PAD_LEFT);
        }
        $isi .= $mrg . str_pad($tmp, 30).
          str_pad(number_format($baris[$i]), 15, ' ', STR_PAD_LEFT).$_lf.$_lf; 
      }
    }
    //$isi .= $mrg . str_pad('-', 78, '-').$_lf;
    $tot = number_format($tott);
    $isi .= $mrg . str_pad(' ', 30, ' ') .
      str_pad($tot, 15, ' ', STR_PAD_LEFT) . $_lf.$_lf.$_lf;
	//$isi_ = wordwrap(SpellNumberID($tott).'rupiah',43,"\n".$mrg);  
    $isi .= $mrg . $isi_.$_lf;
    $isi .= $_lf.$_lf.$_lf.$_lf.$_lf.$_lf;
    $tgl = date('d-m-Y');
    $isi .= $mrg. "Dicetak oleh: $_SESSION[_Login], $tgl".$_lf;
    $isi .= $_lf.$_lf.$_lf.$_lf.$_lf;
  }
  
  else {
     $tgl = date('d-m-Y');
     $isi .= $_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf;
     $isi .= $mrg. "Dicetak oleh: $_SESSION[_Login], $tgl".$_lf;
     $isi .= $_lf.$_lf.$_lf.$_lf.$_lf;
  }
  
  
  // Buat Tujuan
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(64).chr(27).chr(18).chr(27).chr(67).chr(60));
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
