<?php
 

include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "parameter.php";
include "cekparam.php";

$gos = (empty($_REQUEST['gos']))? "Migration" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Data Migration - Excel to SQL - Ruang");
$gos();

// *** Functions ***

function Migration() {
  echo "<p><table class=box align=center>
    <form action='?' method=POST>
    <input type=hidden name='gos' value='ABSSAV'>";
  echo "<tr><th class=ttl colspan=2>Attendance Transfering</th></tr>
    <tr><td class=inp nowrap>Searching File</td>
      <td class=ul nowrap><INPUT type='file' name='inFile'/></td>
  	</tr>
    <tr><td class=ul colspan=2 align=center>
        <input type=submit name='Transfer' value='Transfer'>
        <input type=reset name='Cancel' value='Cancel'></td></tr>
    </form></table></p>";
}
function ABSSAV() {
  $inFile = $_REQUEST['inFile'];
  echo "Filename is: $inFile<br>";
  
  require_once 'Excel/reader.php';
  $data = new Spreadsheet_Excel_Reader();
  $data->setOutputEncoding('CP1251');
  $data->read($inFile);
  error_reporting(E_ALL ^ E_NOTICE);
  
  $Target_Database = 'binawan';
  $Target_Table = $Target_Database.'.ruang';
  $Target_KodeID = "BINAWAN";
  
  $s="TRUNCATE TABLE $Target_Table";
  $r=_query($s);
  for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
    $w = array();
	
	$w['RuangID'] = trim($data->sheets[0]['cells'][$i][2]);
	$w['Nama'] = trim($data->sheets[0]['cells'][$i][3]);
	$w['Kapasitas'] = trim($data->sheets[0]['cells'][$i][4]);
	$w['KapasitasUjian'] = trim($data->sheets[0]['cells'][$i][4]);
	$w['KolomUjian'] = trim($data->sheets[0]['cells'][$i][5]);
	$w['KampusID'] = trim($data->sheets[0]['cells'][$i][6]);
	$w['Lantai'] = trim($data->sheets[0]['cells'][$i][7]);
	$w['RuangKuliah'] = trim($data->sheets[0]['cells'][$i][8]);
	
	$s = "insert into $Target_Table
          (RuangID, Nama, Kapasitas, KapasitasUjian, KolomUjian, KampusID, Lantai, KodeID, RuangKuliah, UntukUSM
	      )
          values
          ('$w[RuangID]', '$w[Nama]', '$w[Kapasitas]', '$w[KapasitasUjian]', '$w[KolomUjian]', '$w[KampusID]', '$w[Lantai]', 'BINAWAN', '$w[RuangKuliah]', '$w[RuangKuliah]'
	      )";
    $r = _query($s);
  }
  echo "<script>window.location = '?$mnux=$_SESSION[mnux]'</script>";
}
?>
