<?php
// Author: Markus Hardiyanto
// Email : markus.hardiyanto@gmail.com
// Creation Date: 23 November 2006
// Description: Code to create excel file for Nilai Mahasiswa

session_start();
// *** Buat File ***
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
BuatExcel();
include_once "disconnectdb.php";

function BuatExcel()
{
	$jdwl = GetFields('jadwal', 'JadwalID', $_REQUEST['jdwlid'], "*");
	$dsn = TRIM($jdwl['DosenID'], '.');
	$arrdsn = explode('.', $dsn);
	$strdsn = (empty($arrdsn))? "GaAdaNih" : implode(',', $arrdsn);
	$nmdsn = GetArrayTable("select concat(Nama, ', ', Gelar) as DSN from dosen where Login in ($strdsn) order by Nama","Login", "DSN");
	$NamaTahun = NamaTahun($jdwl['TahunID']);
  
	// Olah nilai dari GET value di URL
	$_strTM = ($jdwl['tugasmandiri'] == 0)? '' : $jdwl['tugasmandiri'];
	
	// query database nilai yang ingin ditampilkan
	$s = "SELECT k.*, m.Nama AS NamaMhsw
		  FROM krs k
		  LEFT OUTER JOIN mhsw m ON k.MhswID=m.MhswID
		  WHERE k.JadwalID='$_REQUEST[jdwlid]'
		  ORDER BY k.MhswID";
	$r = _query($s);
	
	// Buat file Excel
	include_once "Spreadsheet/Excel/Writer.php";
	$xls =& new Spreadsheet_Excel_Writer();
	$xls->send("daftarnilai.xls");
	$sheet =& $xls->addWorksheet('Nilai Mahasiswa');
	
	// Format untuk title cell
	$formattitle =&$xls->addFormat();
	$formattitle->setAlign('center');
	$formattitle->setBold();
	$formattitle->setSize(16);
	
	// Format untuk header cell
	$formatheader =& $xls->addFormat();
	$formatheader->setBorder(1);
	$formatheader->setAlign('center');
	$formatheader->setBold();
	
	// Format untuk data cell
	$format =& $xls->addFormat();
	$format->setAlign('center');
	$format->setBorder(1);
	
	// Format spesifik untuk nama mahasiswa
	$formatmhs =& $xls->addFormat();
	$formatmhs->setAlign('left');
	$formatmhs->setBorder(1);
	
	// Cetak header file excel
	$sheet->setMerge(0,3,0,7);
	$sheet->write(0,3,"Hasil Nilai Mahasiswa", $formattitle);
	
	$sheet->setMerge(2,0,2,1);
	$sheet->write(2,0,"Semester:");
	$sheet->write(2,2,"$NamaTahun");
	$sheet->setMerge(3,0,3,1);
	$sheet->write(3,0,"Matakuliah:");
	$sheet->write(3,2,"$jdwl[MKKode] -  $jdwl[Nama]");
	$sheet->setMerge(4,0,4,1);
	$sheet->write(4,0,"Kelas:");
	$sheet->write(4,2,"$jdwl[NamaKelas]");
	$sheet->setMerge(5,0,5,1);
	$sheet->write(5,0,"Dosen Pengampu:");
	$sheet->write(5,2,"$nmdsn");
	
	$sheet->setMerge(6,0,7,0);
	$sheet->setColumn(0,0,4);
	$sheet->write(6,0,"#",$formatheader);
	$sheet->write(7,0,"",$formatheader);
	$sheet->write(8,0,"",$formatheader);	
	
	$sheet->setMerge(6,1,7,1);
	$sheet->setColumn(1,1,11);
	$sheet->write(6,1,"NPM",$formatheader);
	$sheet->write(7,1,"",$formatheader);
	$sheet->write(8,1,"",$formatheader);
	
	$sheet->setMerge(6,2,7,2);
	$sheet->setColumn(2,2,32);
	$sheet->write(6,2,"Mahasiswa",$formatheader);
	$sheet->write(7,2,"",$formatheader);
	$sheet->write(8,2,"",$formatheader);
	
	$sheet->setMerge(6,3,6,7);
	$sheet->write(6,3,"Tugas Mandiri $_strTM%",$formatheader);
	$sheet->write(6,4,"",$formatheader);
	$sheet->write(6,5,"",$formatheader);
	$sheet->write(6,6,"",$formatheader);
	$sheet->write(6,7,"",$formatheader);
	$sheet->setColumn(3,7,5.5);
	$sheet->write(7,3,"1",$formatheader);
 	$sheet->write(7,4,"2",$formatheader);
 	$sheet->write(7,5,"3",$formatheader);
	$sheet->write(7,6,"4",$formatheader);
 	$sheet->write(7,7,"5",$formatheader);
 	
 	$sheet->write(8,3,"$jdwl[Tugas1]%",$formatheader);
 	$sheet->write(8,4,"$jdwl[Tugas2]%",$formatheader);
 	$sheet->write(8,5,"$jdwl[Tugas3]%",$formatheader);
	$sheet->write(8,6,"$jdwl[Tugas4]%",$formatheader);
 	$sheet->write(8,7,"$jdwl[Tugas5]%",$formatheader);
 	
 	$sheet->setMerge(6,8,7,8);
 	$sheet->setColumn(8,11,6); // Set column from 8 to 10 -> Pres, UTS, UAS, Resp
 	$sheet->write(6,8,"Pres",$formatheader);
 	$sheet->write(7,8,"",$formatheader);
 	$sheet->write(8,8,"$jdwl[Presensi]%",$formatheader);
 	
 	$sheet->setMerge(6,9,7,9);
 	$sheet->write(6,9,"UTS",$formatheader);
 	$sheet->write(7,9,"",$formatheader);
 	$sheet->write(8,9,"$jdwl[UTS]%",$formatheader);
 	
 	$sheet->setMerge(6,10,7,10);
 	$sheet->write(6,10,"UAS",$formatheader);
 	$sheet->write(7,10,"",$formatheader);
 	$sheet->write(8,10,"$jdwl[UAS]%",$formatheader);
 	
 	$sheet->setMerge(6,11,7,11);
 	$sheet->write(6,11,"Resp",$formatheader);
 	$sheet->write(7,11,"",$formatheader);
 	$sheet->write(8,11,"$jdwl[Responsi]%",$formatheader);
 	
 	$sheet->setMerge(6,12,6,13);
 	$sheet->write(6,12,"Nilai Akhir",$formatheader);
 	$sheet->write(6,13,"",$formatheader);
 	$sheet->setColumn(12,13,6.45);
 	$sheet->write(7,12,"Nilai",$formatheader);
 	$sheet->write(7,13,"Grade",$formatheader);
 	$sheet->write(8,13,"",$formatheader);
 	
 	// Cetak data
 	while ($w = _fetch_array($r))
 	{
		$nomer++;
		$row = $nomer + 8; // Ditambah 6 karena row 0-5 digunakan untuk header
		$sheet->write($row,0,"$nomer",$format);
		$sheet->write($row,1,"$w[MhswID]",$format);
		$sheet->write($row,2,"$w[NamaMhsw]",$formatmhs);
		$sheet->write($row,3,"$w[Tugas1]",$format);
		$sheet->write($row,4,"$w[Tugas2]",$format);
		$sheet->write($row,5,"$w[Tugas3]",$format);
		$sheet->write($row,6,"$w[Tugas4]",$format);
		$sheet->write($row,7,"$w[Tugas5]",$format);
		$sheet->write($row,8,"$w[Presensi]",$format);
		$sheet->write($row,9,"$w[UTS]",$format);
		$sheet->write($row,10,"$w[UAS]",$format);
		$sheet->write($row,11,"$w[Responsi]",$format);
		$sheet->write($row,12,"$w[NilaiAkhir]",$format);
		$sheet->write($row,13,"$w[GradeNilai]",$format);
	}
	
	$xls->close();
}
?>