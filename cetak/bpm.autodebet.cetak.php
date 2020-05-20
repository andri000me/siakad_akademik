<?php

session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
include_once "terbilang.php";
Cetak();
include_once "disconnectdb.php"; 

function Cetak(){
  global $_HeaderPrn, $_lf;
  echo "<body bgcolor=#EEFFFF>";
   // Parameters
  $pos = $_SESSION['BPM-POS'];
  $max = $_SESSION['BPM-MAX'];
  $nmf = $_SESSION['BPM-FILE'];
  $_khsid = $_SESSION['khsid'];
  $khsid = $_khsid[$pos];
  if ($pos < $max) {
  $mhsw = GetFields("khs left outer join mhsw m on m.MhswID = khs.MhswID","khs.KHSID",$khsid,'khs.*,m.Nama,m.MhswID');
  $NamaBank = GetaField('rekening', 'RekeningID', $_SESSION['rekid'], 'Bank');
  $BayarMhswID = GetNextBPM();
  $isi = $_lf;
  // header
  $mrg = str_pad(' ', 8, ' ');
  $isi .= str_pad($BayarMhswID, 50, ' ', STR_PAD_LEFT).$_lf.$_lf.$_lf.$_lf.$_lf;
  $isi .= $mrg.$mrg . $_SESSION['rekid'] . $_lf.$_lf;
  $isi .= $mrg.$mrg . $_SESSION['tahun'] . $_lf;
  $isi .= $mrg.$mrg . $mhsw['MhswID'] . ' / ' . $mhsw['Nama'] . $_lf.$_lf;
  $isi .= $mrg.$mrg . $NamaBank . $_lf.$_lf.$_lf.$_lf;
  // masuk ke detail
  $arr = array();
  $jml = 0; $krg = 0; $nmr = 0;
  $whr = "and bm.MhswID='$mhsw[MhswID]' ";
    $s1 = "select bm.*, 
    format(bm.Besar, 0) as BSR,
    format(bm.Besar*bm.Jumlah, 0) as TOT,
    format(bm.Besar*bm.Jumlah - bm.Dibayar, 0) as KRG,
    bn.Nama, bn.Baris, bn.Detil
    from bipotmhsw bm
      left outer join bipotnama bn on bm.BIPOTNamaID=bn.BIPOTNamaID
    where bm.PMBMhswID='1'
      and bm.TahunID='$_SESSION[tahun]'
      and bm.TrxID=1 and bn.RekeningID='$_SESSION[rekid]'
      and (bm.Besar*bm.Jumlah - bm.Dibayar)>0
      $whr
    order by bn.Baris";
    $r1 = _query($s1);
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
  
  // Buat Tujuan
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'a');
  fwrite($f, chr(27).chr(18).chr(27).chr(67).chr(60));
  fwrite($f, $isi);
  fwrite($f, chr(12));
  fclose($f);
  //TampilkanFileDWOPRN($nmf);
  echo "<p>Proses BPM: <font size=+2>$pos/$max</font><br />
  $khsid &raquo; $mhsw[Nama]</p>";
  echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    echo "<p>Pembuatan file BPM telah selesai.<br />
	Untuk memulai mencetak klik: <a href='$nmf'><img src='img/printer.gif' border=0></a></p>";
  }
  $_SESSION['BPM-POS']++;
}
 
  
?>