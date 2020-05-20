<?php
// Arisal Yanuarafi for ITP 17 September 2014
session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";

if($_REQUEST['md']==1){
include_once "../header_pdf.php";
  
// *** Parameters ***
  // *** Init PDF
  $pdf = new PDF();
  $pdf->SetAutoPageBreak(true, 5);
  $pdf->SetTitle("Laporan Buku Pustaka");
  $pdf->AddPage();
  $lbr = 190;

  BuatIsinya($_SESSION['_pustakaProdiID'], $pdf, $_REQUEST['md']);

  $pdf->Output();
}
else {
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=Pustaka-Laporan-Buku-".$_SESSION['TglMulai']."-".$_SESSION['TglSelesai']);
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
echo "<style>td {mso-number-format:'\@';}</style>";
BuatIsinya($_SESSION['_pustakaProdiID'], $xls,$_REQUEST['md']);
}


function BuatIsinya($ProdiID, $p, $md) {
  if ($md=='1') {
  $lbr = 190; $t = 5;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Laporan Buku Pustaka", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, TanggalFormat(date('Y-m-d')), 0, 1, 'C');
  $p->Ln(2);
  
  $s = "SELECT * from pustaka_klasifikasi where NA='N'";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
  // Buat header tabel
  $t = 5;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(10, $t, '', '', 0);
  $p->Cell(90, $t, $w['Nama'], '', 0);
  $p->Ln($t);
  $p->Cell(10, $t, '', '', 0);
  $p->Cell(10, $t, 'Nmr', 'TLBR', 0);
  $p->Cell(100, $t, 'Judul Buku', 'TBR', 0, 'C');
  $p->Cell(20, $t, 'Eksemplar', 'TBR', 0, 'C');
  $p->Cell(20, $t, 'Sirkulasi', 'TBR', 0,'C');
  $p->Cell(20, $t, 'Keluar', 'TBR', 0,'C');
  $p->Ln($t);
  
  $s1 = "SELECT * from pustaka_bibliografi where Klasifikasi='".$w['KlasifikasiID']."'";
  $r1 = _query($s1); $n=0;$sirkulasi=0;$keluar=0; $totS=0; $totK=0;$totStok=0;
  while ($w1 = _fetch_array($r1)) {
    $n++;
	$sirkulasi = GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"b.BibliografiID='$w1[BibliografiID]' and b.Klasifikasi", $w['KlasifikasiID'],"Count(Sirkulasi2ID)");
	$keluar = GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"p.Status='Pinjam' and b.BibliografiID='$w1[BibliografiID]' and b.Klasifikasi", $w['KlasifikasiID'],"Count(Sirkulasi2ID)");
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, '', '', 0);
	  $p->Cell(10, $t, $n, 'LBR', 0);
	  $p->Cell(100, $t, $w1['Judul'], 'BR', 0);
	  $p->Cell(20, $t, $w1['JumlahStok'], 'BR', 0, 'C');
	  $p->Cell(20, $t, $sirkulasi, 'TBR', 0,'C');
	  $p->Cell(20, $t, $keluar, 'TBR', 0,'C');
	  $p->Ln($t);
		$totS += $sirkulasi; $totK += $keluar; $totStok += $w1['JumlahStok'];
   }
   $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(10, $t, '', '', 0);
	  $p->Cell(110, $t, 'Total', 'LBR', 0,'C');
	  $p->Cell(20, $t, $totStok, 'BR', 0, 'C');
	  $p->Cell(20, $t, $totS, 'TBR', 0,'C');
	  $p->Cell(20, $t, $totK, 'TBR', 0,'C');
	  $p->Ln($t*2);

  }
  }
  else {
  echo "<h3>Laporan Buku Pustaka</h3>";
  echo "<p>".TanggalFormat(date('Y-m-d'))."</p>";
  
  $s = "SELECT * from pustaka_klasifikasi where NA='N'";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
  // Buat header tabel
  echo "<table>
  <tr><th colspan=5><br />$w[Nama]</th></tr>
  <tr>
  		<th>Nmr</th>
		<th>Judul Buku</th>
		<th>Eksemplar</th>
		<th>Sirkulasi</th>
		<th>Keluar</th>
		</tr>";
 
  $s1 = "SELECT * from pustaka_bibliografi where Klasifikasi='".$w['KlasifikasiID']."'";
  $r1 = _query($s1); $n=0;$sirkulasi=0;$keluar=0; $totS=0; $totK=0;$totStok=0;
  while ($w1 = _fetch_array($r1)) {
    $n++;
	$sirkulasi = GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"b.BibliografiID='$w1[BibliografiID]' and b.Klasifikasi", $w['KlasifikasiID'],"Count(Sirkulasi2ID)");
	$keluar = GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"p.Status='Pinjam' and b.BibliografiID='$w1[BibliografiID]' and b.Klasifikasi", $w['KlasifikasiID'],"Count(Sirkulasi2ID)");
	  echo "<tr>
			  <td>$n</td>
			  <td>$w1[Judul]</td>
			  <td>$w1[JumlahStok]</td>
			  <td>$sirkulasi</td>
			  <td>$keluar</td></tr>";
		$totS += $sirkulasi; $totK += $keluar; $totStok += $w1['JumlahStok'];
   }
	echo "<tr>
			  <td colspan=2 align=center>Total</td>
			  <td>$totStok</td>
			  <td>$totS</td>
			  <td>$totK</td></tr>";
  }
  echo "</table>";
  }
  
}
?>
