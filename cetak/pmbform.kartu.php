<?php
// Author: Emanuel Setio Dewo
// 25 Jan 2006
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

function PMBKRT_UTAMA(&$arr, $w, $hal=2) {
  global $_lf;
  $i = 0;
  $arr[$i+0] .= str_pad(' ', 60, ' ');
  $arr[$i+1] .= str_pad($w['PMBID'], 83, ' ');
  $arr[$i+2] .= str_pad(' ', 60, ' ');
  $arr[$i+3] .= str_pad($w['PRD'], 60, ' ',STR_PAD_RIGHT);
  $arr[$i+4] .= str_pad(' ', 60, ' ');
  $arr[$i+5] .= str_pad($w['Nama'], 60, ' ', STR_PAD_RIGHT);
  $arr[$i+6] .= str_pad(' ', 60, ' ');
  $arr[$i+7] .= str_pad($w['TL'], 60, ' ', STR_PAD_RIGHT);
  $arr[$i+8] .= str_pad(' ', 60, ' ');
  if ($hal == 1) {
    $arr[$i+9] .= str_pad(' ', 25, ' ').str_pad(date('d-m-Y'), 24, ' ', STR_PAD_RIGHT);
  }
  else $arr[$i+9] .= str_pad(' ', 40, ' ');
}
function PMBKRT() {
  global $_FKartuUSM, $_lf, $_HeaderPrn, $_EjectPrn;
  $w = GetFields('pmb pm left outer join prodi pr on pm.Pilihan1=pr.ProdiID', 
    "pm.PMBID", $_REQUEST['pmbid'], 
    "pm.PMBID, pm.Nama, pm.ProdiID, pr.Nama as PRD,
    concat(pm.TempatLahir, ', ', date_format(pm.TanggalLahir, '%d-%m-%Y')) as TL, 
    pm.PMBPeriodID,
    pm.Pilihan1, pm.Pilihan2, pm.Pilihan3");
  $margin = '          ';
  $arr = array();
  for ($i=0; $i<=10; $i++) $arr[$i] = $margin;
  
  // Tuliskan halaman 1
  PMBKRT_UTAMA($arr, $w, 1);
  
  // Tulis halaman 2
  /*$_prodi = array();
  for ($i=0; $i<=2; $i++) {
    if (!empty($w['Pilihan'.$i])) $_prodi[] = $w['Pilihan'.$i];
  }
  $_inprodi = implode(', ', $_prodi); */
  $s = "select pu.*, pmu.Nama as UJN,
    date_format(pu.TanggalUjian, '%d/%m/%y') as TGL, 
    date_format(pu.TanggalUjian, '%H:%i') as JAM
    from prodiusm pu
    left outer join pmbusm pmu on pu.PMBUSMID=pmu.PMBUSMID
    where pu.ProdiID='$w[ProdiID]' and pu.PMBPeriodID='$w[PMBPeriodID]'
    order by TanggalUjian";
  //echo "<pre>$s</pre>";
  $r = _query($s);
  $n = 3;
  for ($i=1; $i<$n; $i++) $arr[$i] .= str_pad(' ', 55, ' ');
  while ($u = _fetch_array($r)) {
    if (!empty($u['RuangID'])) {
      $arrRg = explode(',', $u['RuangID']);
      $strRg = $arrRg[0];
    }
    else $strRg = '';
    
    $arr[$n] .= str_pad($u['TGL'], 13, ' ').
      str_pad($u['JAM'], 10, ' ').
      str_pad($u['UJN'], 20, ' ').
      str_pad($strRg, 34, ' ');
    $n++;
  }
  for ($i=$n; $i <=10; $i++) $arr[$i] .= str_pad(' ', 77, ' ');
  
  // Tulis halaman 3
  PMBKRT_UTAMA($arr, $w);
  
  // Tuliskan ke file
  $f = fopen($_FKartuUSM, 'w');
  fwrite($f, chr(27).chr(15));
  for ($i=0; $i<=10; $i++) fwrite($f, $arr[$i].$_lf);
  fwrite($f, $_lf.$_lf.$_lf.$_lf);
  fclose($f);
}

// *** Main ***
PMBKRT();
include_once "disconnectdb.php";

// download
include_once "dwoprn.php";
DownloadDWOPRN($_FKartuUSM);
?>
