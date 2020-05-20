<?php
session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak();
include_once "disconnectdb.php";
  
function RincianMhsw($w){
  $s = "select m.Nama as NamaM,krs.Mhswid as IDM from krs left outer join mhsw m on m.MhswID = krs.MhswID
        where krs.JadwalID = '$w' order by krs.Mhswid";
		
  $r = _query($s);
  $arin = array(); $n = 0;
  while ($ww = _fetch_array($r)) {
    $n++;
    $arin[] = '              '. $n . '. ' . $ww['IDM'] . ' » ' . $ww['NamaM']; 
  }
  return (empty($arin))? '' : implode($_lf, $arin) . $_lf; 
}  

function Cetak() {
  global $_lf;
  // Parameters
  $jdwlid = $_REQUEST['JadwalID'];
  $jdwl = GetFields("jadwal", 'JadwalID', $jdwlid, "*");
  if (empty($jdwl)) die("Data tidak ditemukan.");
  // Buat File
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $hal = 0;
  $n = 0; $brs = 0;
  $maxbaris = 20;
  $mrgisi = str_pad(' ', 11, ' ');
  //$hdr = BuatHeader($jdwl, $);
  
  // Daftar mhsw
  $s = "select m.Nama as NamaM,krs.Mhswid as IDM 
    from krstemp as krs left outer join mhsw m on m.MhswID = krs.MhswID
        where 
	krs.JadwalID = '$jdwlid' 
	and krs.NA = 'N'
	order by krs.Mhswid";
  $r = _query($s);
  $jumrec = _num_rows($r);
  $jumhal = ceil($jumrec/$maxbaris);
  $hdr = BuatHeader($jdwl, $jumhal, $hal);
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
    if ($brs >= $maxbaris) {
	  fwrite($f, chr(12));
	  fwrite($f, BuatHeader($jdwl, $jumhal, $hal));
      $brs = 0;
	}
    //$brs++; $n++;
    //fwrite($f, str_pad($n.'. ', 4) . $w['IDM'] . ' » ' . $w['NamaM']);
    //fwrite($f, $_lf);
    $brs++; $n++;
    $item = $mrgisi . str_pad($n.'. ', 5) . str_pad($w['IDM'], 12). $w['NamaM'];
    //if ($brs == 2) $item2 = "                   Jumlah Tatap Muka : " . $jdwl['Kehadiran'] . 'x';
     // else $item2 = '';
    
    //fwrite($f, $mrgisi . str_pad($n.'. ', 5) . $w['MhswID']. '   ' . str_pad($NamaMhsw, 50));
    if (!empty($item)) fwrite($f, $item . $item2 . $_lf.$_lf);
  }
  
  //fclose($f);
  //TampilkanFileDWOPRN($nmf);
  for ($i=$brs; $i<$maxbaris; $i++)
    fwrite($f, $mrgisi . str_pad(' ', 5) . "** Tidak diperkenankan menambah pada baris ini **" . $_lf.$_lf);
  fwrite($f, $_lf . "Dicetak Oleh : " . $_SESSION['_Login'] . ', ' . Date("d-m-Y H:i"));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}

function BuatHeader($jdwl, $jumhal, &$hal) {
  global $_lf;
  $hal++;
  // data program
  $prg = TRIM($jdwl['ProgramID'], '.');
  $_prg = explode('.', $prg);
  $prg = $_prg[0];
  // data prodi
  $prd = TRIM($jdwl['ProdiID'], '.');
  $_prd = explode('.', $prd);
  if(in_array(99, $_prd)) {
    $prd = 99;
  } else {
  $prd = $_prd[0];
  }
  $prodi = GetaField('prodi', "ProdiID", $prd, "Nama");
  $nmsesi = GetaField("tahun", "ProdiID='$prd' and ProgramID='$prg' and TahunID", 
    $jdwl['TahunID'], 'Nama');
  // Dosen pengampu
  $arrdosen = explode('.', TRIM($jdwl['DosenID'], '.'));
  $strdosen = implode(',', $arrdosen);$hari = GetaField("hari", "HariID", $jdwl['HariID'], 'Nama');
  $JM = substr($jdwl['JamMulai'], 0, 5);
  $JS = substr($jdwl['JamSelesai'], 0, 5);
  $RG = $jdwl['RuangID'];
  $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
    "Login", "Nama", '<br />');
  
  $mrg = str_pad(' ', 28);
  $mrg1 = str_pad(' ', 15);
  /*$a = chr(15) . $_lf.$_lf.
  	   str_pad("*** Daftar Nama Mahasiswa Yang Mengikuti Mata Kuliah ***",114,' ',STR_PAD_BOTH).$_lf.$_lf.$_lf.$_lf .
       $mrg . str_pad("Tahun       : ",2,' ').str_pad($nmsesi, 50) . $_lf .
       $mrg . str_pad("Jurusan     : ",2,' ').str_pad($prodi, 50) .$_lf.
       $mrg . str_pad("Mata Kuliah : ",2,' ').str_pad($jdwl['MKKode'].' - '.$jdwl['Nama'] . ' ' . 
         $jdwl['NamaKelas']. 
         ' ('.$jdwl['SKS'].' SKS)', 50) . $RG.$_lf.
       $mrg . str_pad("Dosen       : ",2,' ').str_pad($dosen, 50) . 
	   $_lf.$_lf;
  return $a;*/
  $a = chr(27).chr(15) . $_lf.$_lf.$_lf.$_lf.
       str_pad("DAFTAR HADIR MAHASISWA SEMENTARA", 80, ' ', STR_PAD_LEFT).
       $ss .
       $_lf.$_lf.
       $mrg . str_pad($nmsesi, 70) . $mrg1 . $hari . $_lf .
       $mrg . str_pad($prodi , 70) . $mrg1 . $JM . ' - ' . $JS .$_lf.
       $mrg . str_pad($jdwl['MKKode'].' - '.$jdwl['Nama'] . ' ' . 
         $mrg1 . $jdwl['NamaKelas']. 
         ' ('.$jdwl['SKS'].' SKS) ' .$RES, 70). $mrg1 . $RG.$_lf.
       $mrg . str_pad($dosen, 70) . $mrg1 . $hal.'/'.$jumhal .
       $_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf;
  return $a;
}
?>
