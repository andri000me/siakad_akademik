<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 23 September 2008

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');

// *** Init PDF
$pdf = new FPDF('L', 'mm', 'A5');
$pdf->SetTitle("Daftar Wisudawan - $TahunID");
$lbr = 190;

BuatHeadernya($pdf);
BuatIsinya($pdf);

$pdf->Output();

// *** Functions ***
function BuatHeadernya($p) {
  global $arrID;
  $p->AddPage('L');
  $gel = GetFields('wisuda', "KodeID='".KodeID."' and TahunID", $_SESSION['TahunID'], 
    "*, date_format(TglWisuda, '%d-%m-%Y') as _TglWisuda");
  $t = 8;
  $p->SetFont('Helvetica', 'B', 20);
  $p->Cell($lbr, $t, "Daftar Wisudawan $arrID[Nama]", 0, 1, 'C');
  $p->Cell($lbr, $t, $gel['Nama'], 0, 1, 'C');
  
  $p->Ln(20);
  $p->SetFont('Helvetica', 'BI', 16);
  $p->Cell($lbr, $t, "Selamat Kepada Para Wisudawan/wati Yang Berbahagia", 0, 1, 'C');
  $p->Ln(20);
  $p->SetFont('Helvetica', '', 16);
  $p->Cell($lbr, $t, "Tanggal Wisuda: " . $gel['_TglWisuda'], 0, 1, 'C');
  $p->Ln(4);
}
function BuatIsinya($p) {
  global $lbr;
  $s = "select w.MhswID, w.Judul, w.Predikat, w.Prasyarat, 
    k.IP, m.ProdiID, m.Nama as NamaMhsw, m.Foto, m.Kota,
    a.Nama as _Agama, d.Nama as _DSN, d.Gelar,
    m.TempatLahir, date_format(m.TanggalLahir, '%d-%m-%Y') as _TglLahir,
    m.Agama, m.Alamat, m.StatusSipil, m.NamaAyah, m.PenasehatAkademik,
    ss.Nama as _StatusSipil,
    date_format(m.TanggalLulus, '%d-%m-%Y') as _TanggalLulus
    from wisudawan w
      left outer join mhsw m on w.MhswID = m.MhswID and m.KodeID = '".KodeID."'
	  left outer join khs k on k.MhswID = m.MhswID
      left outer join dosen d on m.PenasehatAkademik = d.Login and d.KodeID = '".KodeID."'
      left outer join agama a on m.Agama = a.Agama
      left outer join statussipil ss on ss.StatusSipil = m.StatusSipil
    where w.KodeID = '".KodeID."'
	and w.TahunID = '$_SESSION[TahunID]'
	   and k.TahunID = '$_SESSION[TahunID]'
    order by m.ProdiID, w.MhswID";
  $r = _query($s); $prd = ';alskdjf;asd'; $kota = 'al;skjdfalsdhfa';
  $t = 6;
  $p->setFillColor(230, 230, 0);
  while ($w = _fetch_array($r)) {
    $p->AddPage('L');
    // Tampilkan fotonya dulu
    $Foto = (empty($w['Foto']))? "img/tux001.jpg" : $w['Foto'];
    $Foto = (file_exists("../".$Foto))? $Foto : "img/tux001.jpg";
    $p->Image("../$Foto", 84, 8, 44);
    
    $p->SetFont('Helvetica', 'BI', 16);
    $p->Ln(44);
    $p->Cell($lbr, $t+2, $w['NamaMhsw'], 0, 1, 'C');
    
    // Datanya
    $a = array();
    $a[] = array('NIM', ':', $w['MhswID']);
    $a[] = array('Tgl & Tempat Lahir', ':', $w['_TglLahir'] . ', ' . $w['TempatLahir']);
    $a[] = array('Asal Kota/Kab.', ':', $w['Kota']);
    $a[] = array('Agama', ':', $w['_Agama']);
    $a[] = array('IPK, Predikat', ':', $w['IP'] . ', ' . $w['Predikat']);
    $a[] = array('Nama Ayah', ':', $w['NamaAyah']);
    $a[] = array('Penasehat Akademik', ':', $w['_DSN'] . ', ' . $w['Gelar']);
    // tampilkan
    foreach ($a as $_a) {
      $p->SetFont('Helvetica', 'I', 12);
      $p->Cell(70, $t, $_a[0], 0, 0, 'R');
      $p->SetFont('Helvetica', 'B', 12);
      $p->Cell(4, $t, $_a[1], 0, 0, 'C');
      $p->Cell(60, $t, $_a[2], 0, 1);
    }
    $p->SetFont('Helvetica', 'I', 12);
    $p->Cell($lbr, $t, 'Judul Skripsi :', 0, 1, 'C');
    $p->SetFont('Helvetica', '', 12);
    $p->MultiCell($lbr, $t, $w['Judul'], 0, 'C'); 
  }
}
function BikinHeadernya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'BI', 8);
  $p->Cell(20, $t, 'NIM', 'B', 0);
  $p->Cell(60, $t, 'Nama Mahasiswa', 'B', 0);
  $p->Cell(12, $t, 'IPK', 'B', 0, 'R');
  $p->Cell(30, $t, 'Predikat', 'B', 0);
  $p->Cell(68, $t, 'Prasyarat', 'B', 0);
  $p->Ln($t);
}
?>
