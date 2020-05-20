<?php

session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
  
// *** Parameters ***
$ProdiID 	= GetSetVar('_pustakaProdiID');
$md			= GetSetVar('md');

// Tgl Mulai
$TglMulai_y = GetSetVar('TglMulai_y', date('Y'));
$TglMulai_m = GetSetVar('TglMulai_m', date('m'));
$TglMulai_d = GetSetVar('TglMulai_d', '01');
$_SESSION['TglMulai'] = "$TglMulai_y-$TglMulai_m-$TglMulai_d";
// Tgl Selesai
$TglSelesai_y = GetSetVar('TglSelesai_y', date('Y'));
$TglSelesai_m = GetSetVar('TglSelesai_m', date('m'));
$TglSelesai_d = GetSetVar('TglSelesai_d', date('d'));
$_SESSION['TglSelesai'] = "$TglSelesai_y-$TglSelesai_m-$TglSelesai_d";

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiTgl' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function KonfirmasiTgl() {
  KonfirmasiTanggal("../$_SESSION[mnux].laporan.peminjaman.php", "Cetak");
}

function Cetak() {
	if($_SESSION['md']==1){
	include_once "../header_pdf.php";
	  
	// *** Parameters ***
	  // *** Init PDF
	  $pdf = new PDF();
	  $pdf->SetAutoPageBreak(true, 5);
	  $pdf->SetTitle("Laporan Peminjaman Buku");
	  $pdf->AddPage();
	  $lbr = 190;
	
	  BuatIsinya($_SESSION['_pustakaProdiID'], $pdf, $_SESSION['md']);
	
	  $pdf->Output();
	}
	else {
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=Peminjaman-Pustaka-".$_SESSION['TglMulai']."-".$_SESSION['TglSelesai']);
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Pragma: public");
	echo "<style>td {mso-number-format:'\@';}</style>";
	BuatIsinya($_SESSION['_pustakaProdiID'], $xls,$_SESSION['md']);
	}
}

function BuatIsinya($ProdiID, $p, $md) {
  if($md==1){
  $lbr = 190; $t = 5;
  $prd = (!empty($_SESSION['_pustakaProdiID']) ? GetaField("prodi","ProdiID",$_SESSION['_pustakaProdiID'],"Nama"): "Semua Prodi");
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Laporan Peminjaman Buku Pustaka", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Periode: ".TanggalFormat($_SESSION['TglMulai'])." s/d ".TanggalFormat($_SESSION['TglSelesai']), 0, 1, 'C');
  $p->Cell($lbr, $t, "Program Studi: $prd", 0, 1, 'C');
  $p->Ln(2);
  // Buat header tabel
  $t = 5;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(20, $t, 'Tanggal', 1, 0);
  $p->Cell(25, $t, 'NIM', 1, 0);
  $p->Cell(80, $t, 'Nama', 1, 0);
  $p->Cell(20, $t, 'Pinjam', 1, 0,'C');
  $p->Cell(20, $t, 'Blm kembali', 1, 0,'C');
  $p->Ln($t);
  
  $whr = (!empty($_SESSION['_pustakaProdiID']) ? " and m.ProdiID='$_SESSION[_pustakaProdiID]' ": "");
  $s = "select s.AnggotaID, m.Nama, s.TanggalPinjam, s.SirkulasiID from pustaka_sirkulasi s left outer join mhsw m on m.MhswID = s.AnggotaID
			where
      '$_SESSION[TglMulai]' <= s.TanggalPinjam
      and s.TanggalPinjam <= '$_SESSION[TglSelesai]'
	  $whr
    order by  s.TanggalPinjam, m.ProdiID,m.ProgramID";
  $r = _query($s); $n=0;
   while ($w = _fetch_array($r)) {
	   $jumlahPinjam = GetaField('pustaka_sirkulasi2',"SirkulasiID", $w['SirkulasiID'], "count(Sirkulasi2ID)")+0;
	   $jumlahKmbali = GetaField('pustaka_sirkulasi2',"Status='Pinjam' and SirkulasiID", $w['SirkulasiID'], "count(Sirkulasi2ID)")+0;
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(20, $t, $w['TanggalPinjam'], 'B', 0);
	$p->Cell(25, $t, $w['AnggotaID'], 'B', 0);
	$p->Cell(80, $t, $w['Nama'], 'B', 0);
	$p->Cell(20, $t, $jumlahPinjam, 'B', 0,'C');
	$p->Cell(20, $t, $jumlahKmbali, 'BR', 0,'C');
    
    $p->Ln($t);
	
	$totPinjam += $jumlahPinjam;
	$totKembali += $jumlahKmbali;
   }
   $p->SetFont('Helvetica', 'B', 8);
    $p->Cell(135, $t, 'Total', 'LB', 0, 'R');
	$p->Cell(20, $t, $totPinjam, 'B', 0,'C');
	$p->Cell(20, $t, $totKembali, 'BR', 0,'C');
	$p->Ln($t);
  }
  else {
  $prd = (!empty($_SESSION['_pustakaProdiID']) ? GetaField("prodi","ProdiID",$_SESSION['_pustakaProdiID'],"Nama"): "Semua Prodi");
  echo "<p><strong>Laporan Peminjaman Buku Pustaka</strong><br>Periode: ".TanggalFormat($_SESSION['TglMulai'])." s/d ".TanggalFormat($_SESSION['TglSelesai']);
  echo "<br>Program Studi: $prd</p>";
  // Buat header tabel
    echo "<table>
  <tr><th colspan=5><br />$w[Nama]</th></tr>
  <tr>
  		<th>Nmr</th>
		<th>Tanggal Buku</th>
		<th>NIM</th>
		<th>Nama</th>
		<th>Judul Buku</th>
		<th>Eks</th>
		<th>Status</th>
		</tr>";
  $whr = (!empty($_SESSION['_pustakaProdiID']) ? " and m.ProdiID='$_SESSION[_pustakaProdiID]' ": "");
  $s = "select s.AnggotaID, m.Nama, s.TanggalPinjam, s.SirkulasiID from pustaka_sirkulasi s left outer join mhsw m on m.MhswID = s.AnggotaID
			where
      '$_SESSION[TglMulai]' <= s.TanggalPinjam
      and s.TanggalPinjam <= '$_SESSION[TglSelesai]'
	  $whr
    order by  s.TanggalPinjam, m.ProdiID,m.ProgramID";
  $r = _query($s); $n=0;
   while ($w = _fetch_array($r)) {
	   $jumlahPinjam = GetaField('pustaka_sirkulasi2',"SirkulasiID", $w['SirkulasiID'], "count(Sirkulasi2ID)")+0;
	   $jumlahKmbali = GetaField('pustaka_sirkulasi2',"Status='Pinjam' and SirkulasiID", $w['SirkulasiID'], "count(Sirkulasi2ID)")+0;
	   $s1 = "SELECT s.*,b.Judul from pustaka_sirkulasi2 s left outer join pustaka_bibliografi b on b.BibliografiID=s.BibliografiID where s.SirkulasiID='$w[SirkulasiID]'";
	 $r1 = _query($s1);
	 while($w1 = _fetch_array($r1)){
    $n++;
	 echo "<tr>
			  <td>$n</td>
			  <td>$w[TanggalPinjam]</td>
			  <td>$w[AnggotaID]</td>
			  <td>$w[Nama]</td>
			  <td>$w1[Judul]</td>
			  <td>$w1[Eksemplar]</td>
			  <td>$w1[Status]</td></tr>";
	 }
	$totPinjam += $jumlahPinjam;
	$totKembali += $jumlahKmbali;
   }
   echo "</table>";
  }
}
?>
