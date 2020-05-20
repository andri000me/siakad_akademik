<?php
include "../sisfokampus.php";
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "parameter.php";

$sck = $_GET['q'];
$MKKode = $_REQUEST['MKKode'];
$Nama = $_REQUEST['Nama'];
$prodi = $_REQUEST['prodi'];

if (!empty($MKKode)) {
  if (!empty($Nama)) $Nama = '';
  TampilkanDaftarMK();
} else if(!empty($sck)) {
  TampilkanAutoCompleteMK();
} else {
  echo "Isi Data!!";
}

function TampilkanDaftarMK() {
  global $MKKode, $Nama, $prodi;
  $arr = array();
  if (!empty($MKKode)) $arr[] = "mk.MKKode like '$MKKode%' ";
  if (!empty($Nama)) $arr[] = "mk.Nama like '%$Nama%' ";
  $whr = (empty($arr))? '' : " and " . implode(' and ', $arr);
  $s = "select mk.MKID, mk.MKKode, mk.Nama, mk.Nama_en, mk.SKS, mk.Sesi, mk.KurikulumID
    from mk
      left outer join kurikulum kur on mk.KurikulumID=kur.KurikulumID
    where mk.ProdiID='$prodi'
      $whr
      and kur.NA='N'
      and mk.NA='N'
    order by MKKode";
    
  $r = _query($s);
  $w = _fetch_array($r);
    echo "$w[Nama]|$w[MKID]";
}

function TampilkanAutoCompleteMK(){
  global $sck, $prodi;
  $s = "select mk.MKKode, mk.Nama, mk.MKID
    from mk 
      left outer join kurikulum kur on mk.KurikulumID=kur.KurikulumID
    where mk.Nama like '$sck%'
      and kur.NA='N'
      and mk.NA='N'
      and mk.ProdiID='$prodi'
    order by mk.MKKode";
	$r = _query($s);
	//echo "<pre>$s</pre>";
	while ($w=_fetch_array($r)){
		echo "$w[Nama]|$w[MKKode]|$w[MKID]\n";
	}
}
?>
