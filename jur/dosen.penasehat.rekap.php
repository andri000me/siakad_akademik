<?php


session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');
$Tahun   = GetSetVar('Tahun');

// *** Main ***
$pdf = new PDF();
$pdf->SetTitle("Rekap Penasehat Akademik");
$pdf->AddPage();

Headernya($ProdiID, $pdf);
RekapPA($ProdiID, $pdf);

$pdf->Output();

// *** Functions ***
function Headernya($ProdiID, $p) {
  $NamaProdi = GetaField('prodi', "ProdiID = '$ProdiID' and KodeID", KodeID, "Nama");
  $lbr = 180;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Rekap Dosen Penasehat Akademik", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Program Studi: $NamaProdi", 0, 1, 'C');
  $p->Ln(2);
}
function RekapPA($ProdiID, $p) {
  // Buat headernya
  $t = 6;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(20, $t, 'Nmr', 1, 0);
  $p->Cell(40, $t, 'Kode Dosen', 1, 0);
  $p->Cell(100, $t, 'Nama Dosen', 1, 0);
  $p->Cell(20, $t, 'Mhsw', 1, 1, 'R');
  
  $whr = ($_SESSION['ProgramID']=='') ? "" : "and m.ProgramID='$_SESSION[ProgramID]'";
  // Datanya
  $s = "select count(MhswID) as JML,
      m.PenasehatAkademik,
      d.Nama as NamaDosen, d.Gelar
    from mhsw m
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."'
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$ProdiID'
      and m.Keluar = 'N'
	  $whr
    group by m.PenasehatAkademik";
  $r = _query($s);
  $n = 0; $t = 5;
  
  $p->SetFont('Helvetica', '', 9);
  while ($w = _fetch_array($r)) {
    $n++;
    $NamaDosen = (empty($w['NamaDosen']))? 'Belum diset' : $w['NamaDosen'] . ', ' . $w['Gelar'];
    $p->Cell(20, $t, $n, 'LB', 0);
    $p->Cell(40, $t, $w['PenasehatAkademik'], 'B', 0);
    $p->Cell(100, $t, $NamaDosen, 'B', 0);
    $p->Cell(20, $t, $w['JML'], 'BR', 0, 'R');
    $p->Ln($t);
  }
}
?>
