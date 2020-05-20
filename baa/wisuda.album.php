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
$pdf->AddPage('P');
$lbr = 70;

//BuatHeadernya($pdf);
BuatIsinya($pdf);

$pdf->Output();

// *** Functions ***
function BuatHeadernya($p) {
  global $lbr;
  $gel = GetFields('wisuda', "KodeID='".KodeID."' and TahunID", $_SESSION['TahunID'], 
    "*, date_format(TglWisuda, '%d-%m-%Y') as _TglWisuda");
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Daftar Wisudawan " . $gel['Nama'], 0, 1, 'C');
  $p->SetFont('Helvetica', '', 12);
  $p->Cell($lbr, $t, "Tanggal Wisuda: " . $gel['_TglWisuda'], 0, 1, 'C');
  $p->Ln(4);
}
function BuatIsinya($p) {
  $lbr = 120;
  $s = "select w.MhswID, w.Judul, w.Predikat, w.Prasyarat, 
    k.IP, m.ProdiID, Kapital(m.Nama) as NamaMhsw, m.Foto, Kapital(m.Kota) as Kota,
    a.Nama as _Agama, Kapital(d.Nama) as _DSN, d.Gelar,
    Kapital(m.TempatLahir) as TempatLahir, date_format(m.TanggalLahir, '%d-%m-%Y') as _TglLahir,
    m.Agama, Kapital(m.Alamat) as Alamat, m.StatusSipil, Kapital(m.NamaAyah) as NamaAyah, m.PenasehatAkademik,
    ss.Nama as _StatusSipil,
    date_format(w.TglSidang, '%d-%m-%Y') as _TanggalLulus
    from wisudawan w
      left outer join mhsw m on w.MhswID = m.MhswID and m.KodeID = '".KodeID."'
	  left outer join khs k on k.MhswID = m.MhswID
      left outer join dosen d on m.PenasehatAkademik = d.Login and d.KodeID = '".KodeID."'
      left outer join agama a on m.Agama = a.Agama
      left outer join statussipil ss on ss.StatusSipil = m.StatusSipil
    where w.KodeID = '".KodeID."'
      and w.TahunID = '$_SESSION[TahunID]'
	   and k.TahunID = '$_SESSION[TahunID]'
	   and m.ProdiID = '$_SESSION[ProdiID]'
    order by m.ProdiID, w.MhswID";
  $r = _query($s); $prd = ';alskdjf;asd';
  $t = 3;
  $mrg = 8;
  $y = 0; $n = 0;
  while ($w = _fetch_array($r)) {
    if ($y >= 4) {
      $y = 0;
      $p->AddPage('P');
    }
    $n++;
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
      $NamaProdi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $prd, 'Nama');
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell($lbr, $t, "Program Studi: " . $NamaProdi, 'B', 1);
      $p->Ln(3);
      //BikinHeadernya($p);
    }
    $dsn = (empty($w['_DSN']))? 'Belum diset' : $w['_DSN'] . ', '. $w['Gelar'];
    
    $p->SetFont('Helvetica', 'BI', 11);
    $p->Cell(50, 3, $n, 0, 1);
    
    $a = array();
    $a[] = array('Nama', ':', $w['NamaMhsw']);
    $a[] = array('Tgl & Tempat Lahir', ':', $w['_TglLahir'] . ', '. $w['TempatLahir']);
    $a[] = array('NIM / Asal Kota/Kab.', ':', $w['MhswID'] . ' / ' . $w['Kota']);
    $a[] = array('Agama', ':', $w['_Agama']);
    $a[] = array('Alamat', ':', $w['Alamat']);
    $a[] = array('Status Sipil', ':', $w['_StatusSipil']);
    $a[] = array('Tgl Lulus Skripsi', ':', $w['_TanggalLulus']);
    $a[] = array('IPK / Predikat', ':', $w['IP'] . ' / ' . $w['Predikat']);
    $a[] = array('Dosen PA / Ayah', ':', $dsn . ' / ' . $w['NamaAyah']);
    
    foreach ($a as $_a) {
      $p->SetFont('Helvetica', 'I', 7);
      //$p->Cell($mrg);
      $p->Cell(28, $t, $_a[0], 0, 0);
      $p->SetFont('Helvetica', '', 7);
      $p->Cell(3, $t, $_a[1], 0, 0, 'C');
      $p->Cell(54, $t, $_a[2], 0, 0);
      $p->Ln($t);
    }
    $p->SetFont('Helvetica', 'I', 7);
    $p->Cell(40, $t, 'Judul Skripsi:', 0, 1);
    //function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
    $p->SetFont('Helvetica', '', 7);
    $p->MultiCell(120, $t, $w['Judul'], 0);
    // Buat gambarnya
    $Foto = (empty($w['Foto']))? "img/tux001.jpg" : $w['Foto'];
    $Foto = (file_exists("../".$Foto))? $Foto : "img/tux001.jpg";
    $p->Image("../$Foto", 100, $y*37+20, 18);
    $p->Ln($t);
    $y++; 
  }
}
function BikinHeadernya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'BI', 8);
  $p->Cell(20, $t, 'NIM', 'B', 0);
  $p->Cell(60, $t, 'Nama Mahasiswa', 'B', 0);

  $p->Ln($t);
}
?>
