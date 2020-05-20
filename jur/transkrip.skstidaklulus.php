<?php
// Author : Irvandy Gouttama
// Email  : irvandygoutama@gmail.com
// Start  : 02 Juni 2008

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
$MhswID=$_REQUEST['MhswID'];
if (empty($MhswID))
  die(ErrorMsg("Error",
    "Tentukan Mahasiswa ID-nya dulu.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));

// *** Main
$pdf = new FPDF('P');
$pdf->SetTitle("Daftar SKS Tidak Lulus Mahasiswa");
$pdf->AddPage('P');
HeaderLogo("Daftar SKS Tidak Lulus Mahasiswa", $pdf, 'P');
$pdf->SetFont('Helvetica', 'B', 14);

Isinya($MhswID, $pdf);

$pdf->Output();

// *** Functions ***
function Isinya($MhswID, $p) {
  $lbr = 190; $t = 5;
  BuatHeadernya($MhswID, $p);
  JudulKolomnya($p);
  $p->SetFont('Helvetica', '', 9);
  $s = "select k.*
    from krs k
      left outer join khs h on h.KHSID = k.KHSID and h.KodeID = '".KodeID."'
      left outer join nilai n on k.GradeNilai=n.Nama and h.ProdiID=n.ProdiID and n.KodeID = '".KodeID."'
	where k.MhswID = '$MhswID'
		and n.Lulus = 'N'
    order by k.TahunID, k.MKKode";
  $r = _query($s);
  $n = 0;
  $_thn = "02n4lajwnrfvnaw34";
  while ($w = _fetch_array($r)) {
	if($_thn != $w['TahunID'])
	{	$p->Ln($t);
		$p->SetFont('Helvetica', 'B', 10);
		$p->Cell(0, $t, $w['TahunID'], 'B', 1);
		$n = 0;
		$p->SetFont('Helvetica', '', 9);
		$_thn = $w['TahunID'];
	}
	$n++;
	
	$p->Cell(8, $t, $n, 0, 0);
    $p->Cell(25, $t, $w['MKKode'], 0, 0, 'C');
	$p->Cell(100, $t, $w['Nama'], 0, 0);
	$p->Cell(15, $t, $w['SKS'], 0, 0, 'C');
	$p->Cell(15, $t, $w['GradeNilai'], 0, 0, 'C');
	$p->Cell(15, $t, $w['BobotNilai'], 0, 0, 'C');
	$p->Ln($t);
  }
}
function JudulKolomnya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(8, $t, 'No.', 1, 0);
  $p->Cell(25, $t, 'Kode MK', 1, 0, 'C');
  $p->Cell(100, $t, 'Nama Mata Kuliah', 1, 0);
  $p->Cell(15, $t, 'SKS', 1, 0, 'C');
  $p->Cell(15, $t, 'Grade', 1, 0, 'C');
  $p->Cell(15, $t, 'Bobot', 1, 0, 'C');
  $p->Ln($t);
}
function BuatHeadernya($MhswID, $p)
{ $mhsw = GetFields("mhsw m left outer join dosen d on m.PenasehatAkademik=d.Login and d.KodeID='".KodeID."'", 
					"m.MhswID='$MhswID' and m.KodeID", KodeID, 'm.MhswID, m.Nama, d.Nama as DSN, d.Gelar, m.ProgramID, m.ProdiID');
  $arr[] = array('NIM', ':', $mhsw['MhswID']);
  $arr[] = array('Nama', ':', $mhsw['Nama'], 'Program', ':', $mhsw['ProgramID'].' - '.GetaField('program', "ProgramID='$mhsw[ProgramID]' and KodeID", KodeID, 'Nama'));
  $Dosen = (empty($mhsw['DSN']))? "(Belum diset)" : $mhsw['DSN'].', '.$mhsw['Gelar'];
  $arr[] = array('Dosen Pembimbing', ':', $Dosen, 'Program Studi', ':', $mhsw['ProdiID'].' - '.GetaField('prodi', "ProdiID='$mhsw[ProdiID]' and KodeID", KodeID, 'Nama'));

  // Tampilkan
  $p->SetFont('Helvetica', '', 10);
  $t = 5;
  foreach ($arr as $a) {
    // Kolom 1
    $p->SetFont('Helvetica', 'I', 10);
    $p->Cell(35, $t, $a[0], 0, 0);
    $p->Cell(4, $t, $a[1], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 10);
    $p->Cell(70, $t, $a[2], 0, 0);
	
	// Kolom 2
	$p->SetFont('Helvetica', 'I', 10);
    $p->Cell(35, $t, $a[3], 0, 0);
    $p->Cell(4, $t, $a[4], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 10);
    $p->Cell(70, $t, $a[5], 0, 0);
	$p->Ln($t);
  }
  $p->Ln(2);
  $p->Cell(0, $t, '', 'T', 1);
}

function HeaderLogo($jdl, $p, $orientation='P')
{	$pjg = 110;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 12, 8, 18);
	$p->SetY(5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 5, $identitas['Yayasan'], 0, 1, 'C');
    $p->SetFont("Helvetica", 'B', 10);
    $p->Cell($pjg, 7, $identitas['Nama'], 0, 0, 'C');
    
	//Judul
	if($orientation == 'L')
	{
		$p->SetFont("Helvetica", 'B', 16);
		$p->Cell(20, 7, '', 0, 0);
		$p->Cell($pjg, 7, $jdl, 0, 1, 'C');
	}
	else
	{	$p->SetFont("Helvetica", 'B', 12);
		$p->Cell(80, 7, $jdl, 0, 1, 'R');
	}
	
    $p->SetFont("Helvetica", 'I', 6);
	$p->Cell($pjg, 3,
      $identitas['Alamat1'], 0, 1, 'C');
    $p->Cell($pjg, 3,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'], 0, 1, 'C');
    $p->Ln(3);
	if($orientation == 'L') $length = 275;
	else $length = 190;
    $p->Cell($length, 0, '', 1, 1);
    $p->Ln(2);
}

?>
