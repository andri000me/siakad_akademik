<?php
// Arisal Yanuarafi 17 September 2014
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
header("Content-Disposition:attachment;filename=Pengunjung-Pustaka-".$_SESSION['TglMulai']."-".$_SESSION['TglSelesai']);
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
  $p->Cell($lbr, $t, "Rekapitulasi Buku Pustaka", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, TanggalFormat(date('Y-m-d')), 0, 1, 'C');
  $p->Ln(2);
  // Buat header tabel
  $t = 5;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, '', '', 0);
  $p->Cell(10, $t, '', 'LTR', 0);
  $p->Cell(80, $t, '', 'TR', 0, 'C');
  $p->Cell(20, $t, 'Jumlah', 'TR', 0, 'C');
  $p->Cell(20, $t, 'Jumlah', 'TR', 0, 'C');
  $p->Cell(40, $t, 'Status', 'RT', 0,'C');
  $p->Ln($t);
  $p->Cell(10, $t, '', '', 0);
  $p->Cell(10, $t, 'Nmr', 'LBR', 0);
  $p->Cell(80, $t, 'Kategori', 'BR', 0, 'C');
  $p->Cell(20, $t, 'Judul Buku', 'BR', 0, 'C');
  $p->Cell(20, $t, 'Eksemplar', 'BR', 0, 'C');
  $p->Cell(20, $t, 'Sirkulasi', 'TBR', 0,'C');
  $p->Cell(20, $t, 'Keluar', 'TBR', 0,'C');
  $p->Ln($t);
  
  $s = "SELECT * from pustaka_klasifikasi";
  $r = _query($s); $n=0;
  while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, '', '', 0);
	  $p->Cell(10, $t, $n, 'LBR', 0);
	  $p->Cell(80, $t, $w['Nama'], 'BR', 0);
	  $p->Cell(20, $t, GetaField('pustaka_bibliografi',"Klasifikasi", $w['KlasifikasiID'],"Count(BibliografiID)"), 'BR', 0, 'C');
	  $p->Cell(20, $t, GetaField('pustaka_bibliografi',"Klasifikasi", $w['KlasifikasiID'],"SUM(JumlahStok)"), 'BR', 0, 'C');
	  $p->Cell(20, $t, GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"b.Klasifikasi", $w['KlasifikasiID'],"Count(Sirkulasi2ID)"), 'TBR', 0,'C');
	  $p->Cell(20, $t, GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"p.Status='Pinjam' and b.Klasifikasi", $w['KlasifikasiID'],"Count(Sirkulasi2ID)"), 'TBR', 0,'C');
	  $p->Ln($t);
	
	$totDenda += $w['Denda'];
   }
   $p->SetFont('Helvetica', 'B', 8);
    $p->Cell(10, $t, '', '', 0);
	  $p->Cell(90, $t, 'Total', 'LBR', 0);
	  $p->Cell(20, $t, GetaField('pustaka_bibliografi',"BibliografiID >", 1,"Count(BibliografiID)"), 'BR', 0, 'C');
	  $p->Cell(20, $t, GetaField('pustaka_bibliografi',"BibliografiID >", 1,"SUM(JumlahStok)"), 'BR', 0, 'C');
	  $p->Cell(20, $t, GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"b.BibliografiID >", 1,"Count(Sirkulasi2ID)"), 'TBR', 0,'C');
	  $p->Cell(20, $t, GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"p.Status='Pinjam' and b.BibliografiID >", 1,"Count(Sirkulasi2ID)"), 'TBR', 0,'C');
	  $p->Ln($t);
}
else {
	echo "<h3>Laporan Buku Pustaka</h3>";
  	echo "<p>".TanggalFormat(date('Y-m-d'))."</p>";
  // Buat header tabel
  echo "<table width=1000>
  <tr><th colspan=5><br />$w[Nama]</th></tr>
  <tr>
  		<th>Nmr</th>
		<th>Kategori</th>
		<th>Jumlah<br>Judul Buku</th>
		<th>Jumlah<br>Eksemplar</th>
		<th>Sirkulasi</th>
		<th>Keluar</th>
		</tr>";
 
  $s = "SELECT * from pustaka_klasifikasi";
  $r = _query($s); $n=0;
  while ($w = _fetch_array($r)) {
    $n++;
      echo "<tr>
  		<td>$n</td>
		<td>$w[Nama]</td>
		<td>".GetaField('pustaka_bibliografi',"Klasifikasi", $w['KlasifikasiID'],"Count(BibliografiID)")."</td>
		<td>".GetaField('pustaka_bibliografi',"Klasifikasi", $w['KlasifikasiID'],"SUM(JumlahStok)")."</td>
		<td>".GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"b.Klasifikasi", $w['KlasifikasiID'],"Count(Sirkulasi2ID)")."</td>
		<td>".GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"p.Status='Pinjam' and b.Klasifikasi", $w['KlasifikasiID'],"Count(Sirkulasi2ID)")."</td>
		</tr>";
   }
    echo "<tr>
  		<td colspan=2 align=center>TOTAL</td>
		<td>".GetaField('pustaka_bibliografi',"BibliografiID >", 1,"Count(BibliografiID)")."</td>
		<td>".GetaField('pustaka_bibliografi',"BibliografiID >", 1,"SUM(JumlahStok)")."</td>
		<td>".GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"b.BibliografiID >", 1,"Count(Sirkulasi2ID)")."</td>
		<td>".GetaField('pustaka_sirkulasi2 p left outer join pustaka_bibliografi b on b.BibliografiID=p.BibliografiID',
	  				"p.Status='Pinjam' and b.BibliografiID >", 1,"Count(Sirkulasi2ID)")."</td>
		</tr></table>";
	}
}
?>
