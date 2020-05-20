<?php

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
//$ProdiID = GetSetVar('ProdiID');
$KelasID = GetSetVar('KelasID');
// *** Init PDF
$pdf = new PDF();
$pdf->SetTitle("PEMBAGIAN KELAS");
$lbr = 190;

$ProdiID = GetFields('prodi p left outer join kelas k on k.ProdiID = p.ProdiID', "p.NA = 'N' and k.KelasID", $KelasID, 'p.ProdiID');

//BuatHeadernya($TahunID, $ProdiID, $sta, $pdf);
BuatIsinya($TahunID, $ProdiID[ProdiID], $KelasID, $pdf);

$pdf->Output();

function BuatIsinya($TahunID, $ProdiID, $KelasID, $p){
	$s = "select m.*, k.Nama as _NamaKelas, p.Nama as _NamaProdi, pr.Nama as _NamaProgram, st.Nama as _NamaStatus
			from mhsw m left outer join kelas k on m.KelasID = k.KelasID
			left outer join prodi p on k.ProdiID = p.ProdiID left outer join program pr on k.ProgramID = pr.ProgramID
			left outer join statusmhsw st on m.StatusMhswID = st.StatusMhswID
			where k.ProdiID = '$ProdiID' and k.TahunID = $TahunID and k.KelasID = '$KelasID'
			order by m.MhswID";
	$q = _query($s);
	
	$numrec = 1;
	$n = 0; $t = 5;
	while ($w = _fetch_array($q)){
		if ($numrec == 41) $numrec = 1;
		if ($numrec == 1){
		  $NamaProdi = $w[_NamaProdi];
		  $NamaProgram = $w[_NamaProgram];
		  $NamaKelas = $w[_NamaKelas];
		  $p->AddPage();
		  BuatHeader($TahunID, $NamaProdi, $NamaProgram, $NamaKelas, $p);
		}
		$n++;
		$p->SetFont('Helvetica', '', 9);
		$p->Cell(5, $t, $n, 0, 0, 'R'); 
		$p->Cell(3, $t, '', 0, 0, 'R'); 
		$p->Cell(65, $t, $w['Nama'], 0, 0);
		$p->Cell(25, $t, $w['Kelamin'], 0, 0, 'C');
		$p->Cell(25, $t, $w['MhswID'], 0, 0, 'C');
		$p->Cell(25, $t, $w['TahunID'], 0, 0, 'C');
		$p->Cell(20, $t, $w['_NamaStatus'], 0, 0, 'C');
		$p->Ln($t);
		$numrec++;
	}		
}

function BuatHeader($TahunID, $NamaProdi, $NamaProgram, $NamaKelas, $p){
  global $lbr;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "PEMBAGIAN KELAS PROGRAM STUDI ".strtoupper($NamaProdi), 0, 1, 'C');
  $p->Cell($lbr, $t, "TAHUN AKADEMIK $TahunID", 0, 1, 'C');
  $p->Ln($t+1);
  // Header tabel
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(50, $t, "Kelas : $NamaKelas", 0, 0);
  $p->Ln($t+2);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(5, $t, 'NO', 'B', 0, 'C');
  $p->Cell(3, $t, '', 'B', 0, 'C');
  $p->Cell(65, $t, 'NAMA', 'B', 0, 'C');
  $p->Cell(25, $t, 'JENIS KELAMIN', 'B', 0, 'C');
  $p->Cell(25, $t, 'NO.BP/NIM', 'B', 0, 'C');
  $p->Cell(25, $t, 'ANGKATAN', 'B', 0, 'C');
  $p->Cell(20, $t, 'STATUS', 'B', 0, 'C');
  $p->Ln($t+3);
}