<?php
// Author: Rinamay
// Email: r1n4m4y@yahoo.com
// Start: 05/11/2007

include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "parameter.php";
include "cekparam.php";

$gos = (empty($_REQUEST['gos']))? "Migration" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Data Migration - Excel to SQL - Mahasiswa");
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
  require_once 'Excel/reader.php';
  $data = new Spreadsheet_Excel_Reader();
  $data->setOutputEncoding('CP1251');
  $data->read($inFile);
  error_reporting(E_ALL ^ E_NOTICE);
  
  $Target_Database = 'binawan';
  $Target_Table = $Target_Database.'mhsw';
  $Target_KodeID = "BINAWAN";
  
  $s="TRUNCATE TABLE ";
  $r=_query($s);
  for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
    $w = array();
	
	$w['MhswID'] = trim($data->sheets[0]['cells'][$i][1]);
	$w['TahunID'] = trim($data->sheets[0]['cells'][$i][2]);
	$w['Nama'] = trim($data->sheets[0]['cells'][$i][3]);
	$w['Kelamin'] = trim($data->sheets[0]['cells'][$i][4]);
	$w['TempatLahir'] = trim($data->sheets[0]['cells'][$i][5]);
	$w['TanggalLahir'] = trim($data->sheets[0]['cells'][$i][6]);
	$w['Agama'] = trim($data->sheets[0]['cells'][$i][7]);
	$w['Alamat'] = trim($data->sheets[0]['cells'][$i][8]);
	$s = "insert into $Target_Database
          (MhswID, Login, LevelID, Password, PMBID, 
		   TahunID, KodeID, Nama, 
		   Foto, StatusMhswID, ProgramID, ProdiID, KelasID,
		   Kelamin, TempatLahir, TanggalLahir, Agama, Alamat, 
		   Kota, RT, RW, KodePos, Propinsi, Negara, 
		   Telepon, Handphone 
	      )
          values
          ('$w[MhswID]', '$w[Login]', '120', '$w[Password]', '$w[PMBID]', 
		   '$w[TahunID]', '$Target_KodeID', '$w[Nama]', 
		   '$w[Foto]', '$w[StatusMhswID]', '$w[ProgramID]', '$w[ProdiID]', '$w[KelasID]',
		   '$w[Kelamin]', '$w[TempatLahir]', '$w[TanggalLahir]', '$w[Agama]', '$w[Alamat]', 
		   '$w[Kota]', '$w[RT]', '$w[RW]', '$w[KodePos]', '$w[Propinsi]', '$w[Negara]', 
		   '$w[Telepon]', '$w[Handphone]' 
	      )";
    $r = _query($s);
  }
  echo "<script>window.location = '?$mnux=$_SESSION[mnux]'</script>";
}
?>
