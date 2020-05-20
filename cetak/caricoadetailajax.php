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
$fakid = $_GET['fakul'];

TampilkanAutoComplete();

include "disconnectdb.php";

// *** Functions ***

function TampilkanAutoComplete(){
  global $sc, $fakid, $level;
  $whr = (!empty($fakid)) ? "and FakultasID = '$fakid'" : '';
  $s = "select COAID, Nama
    from coa where LevelID= '2' $whr and COAID like '$sc%'";
	$r = _query($s);
	while ($w=_fetch_array($r)){
		echo "$w[COAID]| $w[Nama]\n";
	}
}
?>
