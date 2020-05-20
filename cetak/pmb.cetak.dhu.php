<?php
// Author: Emanuel Setio Dewo
// 06 Feb 2006

session_start();
// *** Buat File ***
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
CetakLabel();
include_once "disconnectdb.php";

function CetakLabel() {
  global $_HeaderPrn, $_EjectPrn, $_lf, $arrHari;
  $FDHU = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login]DHU.dwoprn";
  // *** Data ***
  $prodi = $_REQUEST['prodi'];
  $dataTest = GetFields('pmbusm', "PMBUSMID", $_REQUEST['pmbusmid'], '*');
  $dataProd = GetFields('prodiusm', "ProdiUSMID", $_REQUEST['prodiusmid'], 
    "*, date_format(TanggalUjian, '%d/%m/%Y') as TGL, 
    date_format(TanggalUjian, '%H:%i') as JAM,
    date_format(TanggalUjian, '%w') as HR");
  // *** Cetak ***
  $s = "select p.PMBID, p.Nama, p.PSSBID, p.StatusAwalID
    from pmb p
    where p.PMBPeriodID='$_REQUEST[pmbaktif]' and p.ProdiID='$prodi'
    
    order by p.PMBID";
  $r = _query($s);
  
  $hal = 1; $jmlbrs = 0; $maxbrs = 20;
  $f = fopen($FDHU, 'w');
  fwrite($f, $_HeaderPrn);
  
  CetakHeader($f, $dataTest, $dataProd, $hal);
  // isi
  while ($w = _fetch_array($r)) {
    $jmlbrs++;
    //$pssb = GetaField('pssb', "PSSBID", $w['PSSBID'], 'MhswID');
    $tanda = ($w['StatusAwalID'] <> 'B') ? '(PSSB)' : '';
    fwrite($f, str_pad($jmlbrs, 9, ' ', STR_PAD_LEFT).'. '.
      str_pad($w['PMBID'], 15). ' '.
      str_pad($w['Nama'].' '.$tanda, 50).$_lf.$_lf);
    if ($jmlbrs % $maxbrs == 0) {
      fwrite($f, chr(12));
      $hal++;
      CetakHeader($f, $dataTest, $dataProd, $hal);
    }
  }
  fwrite($f, "         Anda tidak diperkenankan menambah pada baris ini.".$_lf.$_lf);
  for ($i = $jmlbrs; $i <= $maxbrs-1; $i++) fwrite($f, $_lf.$_lf);
  fwrite($f, chr(12));
  fclose($f);
  include_once "dwoprn.php";
  DownloadDWOPRN($FDHU);
}
function CetakHeader($f, $dataTest, $dataProd, $hal=1) {
  global $_lf, $arrHari;
  $mrg = str_pad(' ', 30, ' ');
  $_hari = $dataProd['HR'];
  $hari = $arrHari[$_hari].', '.$dataProd['TGL'];
  $arrProd = GetFields('prodi', 'ProdiID', $dataProd['ProdiID'], 'Nama, FakultasID');
  $fak = GetaField('fakultas', 'FakultasID', $arrProd['FakultasID'], 'Nama');
  $strProd = $fak.' / '.$arrProd['Nama'];
  fwrite($f, chr(15).$_lf.$_lf.$_lf.$_lf.$_lf.$_lf);
  fwrite($f, str_pad($mrg.$_REQUEST['pmbaktif'], 110));
    fwrite($f, $hari.$_lf);
  
  fwrite($f, str_pad($mrg.$strProd, 110));
    fwrite($f, $dataProd['JAM'].$_lf);
  
  fwrite($f, str_pad($mrg.$dataTest['Nama'], 110));
    fwrite($f, $dataProd['RuangID'].$_lf);
    
  fwrite($f, str_pad(' ', 110));
    fwrite($f, $hal);
  fwrite($f, $_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf);
}
?>
