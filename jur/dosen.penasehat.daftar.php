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
$Tahun	 = GetSetVar('Tahun');

// *** Main ***
$pdf = new PDF();
$pdf->SetTitle("Daftar Mahasiswa Penasehat Akademik");


RekapPA($ProdiID, $pdf, $Tahun);

$pdf->Output();

// *** Functions ***
function Headernya($ProdiID, $p) {
  $NamaProdi = GetaField('prodi', "ProdiID = '$ProdiID' and KodeID", KodeID, "Nama");
  $lbr = 180;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Daftar Dosen Penasehat Akademik - Mahasiswa", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Program Studi: $NamaProdi", 0, 1, 'C');
  $p->Ln(2);
}
function RekapPA($ProdiID, $p, $Tahun) {
  // Datanya
  $whr = ($_SESSION['ProgramID']=='') ? "" : "and m.ProgramID='$_SESSION[ProgramID]'";
  $s = "select m.MhswID, m.Nama as NamaMhsw,
      m.PenasehatAkademik, m.ProdiID,
      d.Nama as NamaDosen, d.Gelar
    from khs h,mhsw m
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."'
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$ProdiID'
      and m.Keluar = 'N'
	  and m.MhswID=h.MhswID
	  $whr
	  and h.TahunID='$Tahun'
    order by m.PenasehatAkademik,m.MhswID";
  $r = _query($s);
  $n = 0; $t = 5; $dsn = 'lkajdsfpakjdfas';
  $lbr = 190;
  
  while ($w = _fetch_array($r)) {
    if ($dsn != $w['PenasehatAkademik'].$w['NamaDosen']) {
      $NamaDosen = (empty($w['NamaDosen']))? 'Belum diset' : $w['NamaDosen'] . ', ' . $w['Gelar'];
      $dsn = $w['PenasehatAkademik'].$w['NamaDosen'];
      
	  $p->AddPage();
	  Headernya($ProdiID, $p);
	  $p->Ln(2);
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell($lbr, $t+2, $NamaDosen, 0, 1);
      HeaderTabel($p);
      $n = 0;
    }
    $n++;
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(20, $t, $n, 'LB', 0);
    $p->Cell(40, $t, $w['MhswID'], 'B', 0);
    $p->Cell(100, $t, ucfirst(strtolower($w['NamaMhsw'])), 'B', 0);
    $p->Cell(20, $t, $w['ProdiID'], 'BR', 0, 'R');
    $p->Ln($t);
  }
}
function HeaderTabel($p) {
  // Buat headernya
  $t = 6;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(20, $t, 'Nmr', 1, 0);
  $p->Cell(40, $t, 'NIM/NPM', 1, 0);
  $p->Cell(100, $t, 'Nama Mhsw', 1, 0);
  $p->Cell(20, $t, 'Prodi', 1, 1, 'R');
}
?>
