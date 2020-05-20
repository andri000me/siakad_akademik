<?php
// Author: Emanuel Setio Dewo
// Start: 13 March 2006
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

// *** Parameters ***
$sc = $_GET['q'];
$DosenID = $_REQUEST['DosenID'];
$NamaDosen = $_REQUEST['NamaDosen'];
$prodi = $_REQUEST['prodi'];

if (!empty($DosenID)) {
  if (!empty($NamaDosen)) $NamaDosen = '';
  TampilkanDaftarDosen();
} else if(!empty($sc)) {
  TampilkanAutoComplete();
} else {
  echo "Isi Data!!";
}
//if (empty($DosenID) && empty($NamaDosen)) {
  //if (!empty($sc))

//TampilkanDaftarDosen();

include "disconnectdb.php";

// *** Functions ***
function TampilkanDaftarDosen() {
  global $DosenID, $prodi;
  $arr = array();
  if (!empty($DosenID)) $arr[] = "Login like '$DosenID%' ";
  //if (!empty($prodi)) $arr[] = "INSTR(ProdiID, '.$prodi.')>0 ";
  $whr = (empty($arr))? '' : " and " . implode(' and ', $arr);
  
  $s = "select Login, Nama, concat(Nama, ', ', Gelar) as DSN, Homebase, ProdiID
    from dosen
    where NA='N'
      $whr
    order by Nama";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $_prd = TRIM($w['ProdiID'], '.');
    $_prd = str_replace('.', ',', $_prd);
    if (!empty($w['DSN']))
      echo "$w[DSN]";
    else echo "Dosen Tidak Terdaftar";
  }
}

function TampilkanAutoComplete(){
  global $sc;
  $s = "select Login, concat(Nama, ', ', Gelar) as DSN
    from dosen where Nama like '$sc%'";
	$r = _query($s);
	while ($w=_fetch_array($r)){
		echo "$w[DSN]|$w[Login]\n";
	}
}
?>
