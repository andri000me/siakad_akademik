<?php
// Author: Emanuel Setio Dewo
// 06 Feb 2006

// *** Buat File ***
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
CetakLabel();
include_once "disconnectdb.php";

function CetakLabel() {
  global $_HeaderPrn, $_EjectPrn, $_lf;

  $LabelMeja = HOME_FOLDER  .  DS . "tmp/LabelUjian.dwoprn";
  // *** Cetak ***
  if (!empty($_REQUEST['pmbid'])) {
    $whr = "where p.PMBID='$_REQUEST[pmbid]' ";
  }
  else {
    $whr = "where p.PMBPeriodID='$_REQUEST[pmbaktif]' and p.ProdiID='$_REQUEST[prodi]' ";
  }
  $s = "select p.PMBID, p.Nama, p.ProdiID, concat(f.Nama, ' / ', pr.Nama) as PRD, p.RuangID
    from pmb p
    left outer join prodi pr on p.ProdiID=pr.ProdiID
    left outer join fakultas f on pr.FakultasID=f.FakultasID
    $whr and p.PSSBID = ''
    order by p.PMBID";
  $r = _query($s);
  
  $f = fopen($LabelMeja, 'w');
  $n = 0;
  fwrite($f, $_HeaderPrn);
  while ($w = _fetch_array($r)) {
    $n++;
    fwrite($f, chr(27).chr(14));
    fwrite($f, $w['PMBID']);
    fwrite($f, chr(27).chr(119).'0'.$_lf);
    fwrite($f, chr(27).chr(15));
    fwrite($f, $w['Nama'].$_lf);
    fwrite($f, $w['PRD'].$_lf);
    fwrite($f, $w['RuangID'].$_lf);
    fwrite($f, chr(27).chr(18));
    fwrite($f, $_HeaderPrn);
    fwrite($f, $_lf.$_lf.$_lf.$_lf.$_lf);
  }
  fwrite($f, chr(27).chr(18));
  fclose($f);
  include_once "dwoprn.php";
  DownloadDWOPRN($LabelMeja);
}
?>
