<?php
// Author: Irvandy Goutama
// Start Date: 31 Januari 2009

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../util.lib.php";
  
// *** Parameters ***
$JRID = $_REQUEST['JRID'];

include_once "../fpdf.php";

$lbr = 200;

$pdf = new FPDF();
$pdf->SetTitle("Peserta Remedial");

if($JRID == 0)
{	$prodistring = (empty($_SESSION['_remedialProdiID']))? "" : "and jr.ProdiID='$_SESSION[_remedialProdiID]'";
	$tahunstring = (empty($_SESSION['_remedialTahunID']))? "" : "and jr.TahunID='$_SESSION[_remedialTahunID]'";
	
	$s = "select jr.Nama as _NamaMK, m.Nama as _NamaMhsw, kr.MhswID, o.Nama as _NamaKelas, k.GradeNilai
			from krsremedial kr left outer join jadwalremedial jr on kr.JadwalRemedialID=jr.JadwalRemedialID
				left outer join mhsw m on kr.MhswID=m.MhswID
				left outer join kelas o on m.KelasID=o.KelasID
				left outer join krs k on kr.KRSID=k.KRSID
			where kr.KodeID='".KodeID."' $prodistring $tahunstring 
			order by jr.Nama, o.Nama, m.Nama";
	$r = _query($s);
}
else
{	$s = "select jr.Nama as _NamaMK, m.Nama as _NamaMhsw, kr.MhswID, o.Nama as _NamaKelas, k.GradeNilai
			from krsremedial kr left outer join jadwalremedial jr on kr.JadwalRemedialID=jr.JadwalRemedialID
				left outer join mhsw m on kr.MhswID=m.MhswID
				left outer join kelas o on m.KelasID=o.KelasID
				left outer join krs k on kr.KRSID=k.KRSID
			where kr.KodeID='".KodeID."' and kr.JadwalRemedialID='$JRID'";
	$r = _query($s);
}	

$pdf->AddPage('P', 'A4');

$t = 5; 

$pdf->SetFont('Helvetica', 'B', 7);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(70, $t, 'MATA KULIAH', 1, 0, 'C', true);
$pdf->Cell(20, $t, 'NPM', 1, 0, 'C', true);
$pdf->Cell(70, $t, 'NAMA LENGKAP', 1, 0, 'C', true);
$pdf->Cell(10, $t, 'GRADE', 1, 0, 'C', true);
$pdf->Ln($t);

while($w = _fetch_array($r))
{	$pdf->Cell(70, $t, $w['_NamaMK'], 1, 0, 'C');
	$pdf->Cell(20, $t, $w['MhswID'], 1, 0, 'C');
	$pdf->Cell(70, $t, $w['_NamaMhsw'], 1, 0, 'C');
	$pdf->Cell(10, $t, $w['GradeNilai'], 1, 0, 'C');
	$pdf->Ln($t);
}

$pdf->Output();

?>