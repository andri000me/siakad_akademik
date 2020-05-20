<?php
include "../sisfokampus.php";
// Author: Emanuel Setio Dewo
// 20 June 2006
// www.sisfokampus.net

// *** functions ***
function DetailBIPOT() {
  global $_lf;
  $bipotid = $_REQUEST['bipotid'];
  $bpt = GetFields('bipot', 'BIPOTID', $bipotid, '*');
  $prg = GetaField('program', 'ProgramID', $bpt['ProgramID'], 'Nama');
  $prd = GetaField('prodi', 'ProdiID', $bpt['ProdiID'], 'Nama');
  
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].bipot.dwoprn";
  $f = fopen($nmf, 'w');
  $mxc = 114;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $hdr = str_pad("Daftar Biaya & Potongan $bpt[TahunID]", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad($bpt['Nama'], $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Program: $prg, Prodi: $prd", $mxc, ' ', STR_PAD_BOTH).$_lf.
    $grs . "No. Prio ".
    str_pad("Nama", 30).
    str_pad("Jumlah", 12, ' ', STR_PAD_LEFT). ' '.
    str_pad("Penarikan", 12).
    str_pad("Oto?", 5).' '.
    str_pad("St.Awal", 12).' '.
    str_pad("St.Mhsw", 12).' '.
    str_pad("Grade", 5).' '.
    $_lf . $grs;
  
  $s = "select b2.*, bn.Nama, format(b2.Jumlah, 0) as JML,
      t.Nama as NMTRX, s.Nama as SAAT
      from bipot2 b2
      left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
      left outer join saat s on b2.SaatID=s.SaatID
      left outer join trx t on b2.TrxID=t.TrxID
      where b2.BIPOTID='$bipotid' and KodeID='$_SESSION[KodeID]'
      order by b2.TrxID, b2.Prioritas, b2.GradeNilai";
  $r = _query($s); $n = 0;
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
    $n++;
    $jml = number_format($w['Jumlah']);
    $sa = TRIM($w['StatusAwalID'], '.');
    $sa = str_replace('.', ',', $sa);
    $sm = TRIM($w['StatusMhswID'], '.');
    $sm = str_replace('.', ',', $sm);
    fwrite($f, str_pad($n, 4).
      str_pad($w['Prioritas'], 5).
      str_pad($w['Nama'], 30).
      str_pad($jml, 12, ' ', STR_PAD_LEFT). ' '.
      str_pad($w['SAAT'], 12).
      str_pad($w['Otomatis'], 5, ' ', STR_PAD_BOTH) . ' '.
      str_pad($sa, 12). ' '.
      str_pad($sm, 12). ' '.
      str_pad($w['GradeNilai'], 5, ' ', STR_PAD_BOTH).
      $_lf);
  }
  fwrite($f, $grs);
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}

// *** Main ***
if (!empty($_REQUEST['gos'])) {
  include_once "db.mysql.php";
  include_once "connectdb.php";
  include_once "dwo.lib.php";
  include_once "parameter.php"; 
  $_REQUEST['gos']();
  include_once "disconnectdb.php";
}
?>
