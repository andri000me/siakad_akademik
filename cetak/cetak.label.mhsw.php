<?php
//include "db.mysql.php";
//include_once "connectdb.php";
//include_once "dwo.lib.php";
//include_once "parameter.php";
include "../sisfokampus.php";
CetakLabelMHSW();
//include_once "disconnectdb.php";

function CetakLabelMHSW(){
	global $_lf, $_HeaderPrn;
	
	$tahun = $_REQUEST['tahun'];
  $darinpm = $_REQUEST['DariNPM'];
  $sampainpm = $_REQUEST['SampaiNPM'];
  $alamat = $_REQUEST['alamat']+0;
  	
	$labelmhsw = HOME_FOLDER  .  DS . "tmp/labelmhswt.dwoprn";
	$mrg = str_pad(' ', 5, ' ');
	$Aktif = ($darinpm == $sampainpm) ? "" : "and k.StatusMhswID = 'A'";
	
	$s = "select m.MhswID, m.Nama, m.Alamat, m.AlamatAsal, m.Kota, m.KodePos
	     from mhsw m left outer join khs k on k.MhswID = m.MhswID
    where ('$darinpm' <= m.MhswID)
      and (m.MhswID <= '$sampainpm') 
      and k.TahunID = '$tahun' $aktif";
  $r = _query($s);
	//echo "<pre>$s</pre>";
	//exit;
	$f = fopen($labelmhsw, 'w');
	$n = 0;
	
	fwrite($f, $_HeaderPrn);
  while ($w = _fetch_array($r)) {
    
    $Alamat = (empty($w['Alamat'])) ? $w['AlamatAsal'] : $w['Alamat'];
    $Kota   = (empty($w['Kota']))   ? $w['KotaAsal']   : $w['Kota'];
    $KodePos= (empty($w['KodePos']))? $w['KodePosAsal']: $w['KodePos'];
    
    if ($alamat == 1) {
      fwrite($f, chr(27).chr(15));
      fwrite($f, $mrg . $w['Nama'].' '.$w['MhswID'] . $_lf);
      fwrite($f, $mrg . $Alamat . $_lf);
      fwrite($f, $mrg . $Kota.' /'.$KodePos . $_lf);
      fwrite($f, chr(27).chr(18));
      fwrite($f, $_HeaderPrn);
      fwrite($f, $_lf.$_lf.$_lf.$_lf.$_lf.$_lf);
    } else {
      fwrite($f, chr(27).chr(15));
      fwrite($f, $mrg . $w['Nama'].' '.$w['MhswID'] . $_lf);
      fwrite($f, chr(27).chr(18));
      fwrite($f, $_HeaderPrn);
      fwrite($f, $_lf.$_lf);
    }
  }
  fwrite($f, chr(27).chr(18).chr(67).chr(66));
  fclose($f);
  TampilkanFileDWOPRN($labelmhsw, "cetak.label");
  //include_once "dwoprn.php";
  //DownloadDWOPRN($labelmhsw);
}
?>
