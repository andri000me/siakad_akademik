<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$TahunID = GetaField;
$DosenID = $_SESSION['_Login'];
$dsn = GetFields('dosen', "Login='$DosenID' and KodeID", KodeID, "*");

// *** Main ***
$pdf = new PDF();
$pdf->SetTitle("Daftar Mahasiswa Bimbingan yang Aktif");
$pdf->AddPage();

// Buat header dulu
BuatHeader($dsn, $pdf);
// Tampilkan datanya
DaftarMhsw($dsn, $pdf);

$pdf->Output();

// *** Functions ***
function BuatHeader($dsn, $p) {
  $lbr = 190;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Daftar Mahasiswa", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Dosen PA: $dsn[Nama], $dsn[Gelar]", 0, 1, 'C');
  $p->Ln(2);
}
function DaftarMhsw($dsn, $p) {
	$TahunID = GetaField('tahun',"NA='N' AND TahunID not like 'Tran%' AND KodeID",KodeID,"max(TahunID)");
  $s = "select m.MhswID, m.Nama as NamaMhsw, m.TahunID,
    m.ProdiID, p.Nama as _Prodi, m.ProdiID, h.SetujuPA, h.Bayar
    from khs h left outer join mhsw m on h.MhswID=m.MhswID
      left outer join prodi p on p.ProdiID = m.ProdiID and p.KodeID = '".KodeID."'
    where m.KodeID = '".KodeID."'
      and m.PenasehatAkademik = '$dsn[Login]'
      and m.Keluar = 'N'
	  and h.StatusMhswID = 'A'
	  and h.TahunID='$TahunID'
    order by m.TahunID, m.ProdiID, m.MhswID";
  $r = _query($s);
  $n = 0; $t = 5; $_prd = 'lkasjdhfaksdjkhf-19823';
  $lbr = 190;
  while ($w = _fetch_array($r)) {
    if ($_prd != $w['ProdiID']) {
      $_prd = $w['ProdiID'];
      $p->SetFont('Helvetica', 'B', 9);
      $p->Cell($lbr, $t+1, $w['ProdiID'] . ' - ' . $w['_Prodi'], 0, 1);
      TampilkanHeaderTabel($p);
      $n = 0;
    }
    $n++;
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(18, $t, $n, 1, 0);
    $p->Cell(30, $t, $w['MhswID'], 1, 0);
    $p->Cell(100, $t, $w['NamaMhsw'], 1, 0);
    $p->Cell(30, $t, ($w['SetujuPA'] == 'Y')? "Sudah":"Belum Validasi", 1, 0);
    $p->Ln($t);
  }
}
function TampilkanHeaderTabel($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'BI', 9);
  $p->Cell(18, $t, 'Nmr', 1, 0);
  $p->Cell(30, $t, 'NIM/NPM', 1, 0);
  $p->Cell(100, $t, 'Nama Mahasiswa', 1, 0);
  $p->Cell(30, $t, 'Setuju PA', 1, 0);
  $p->Ln($t);
}
?>
