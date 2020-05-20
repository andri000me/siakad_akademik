<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 23 Agustus 2008
//
  include_once "dwo.lib.php";
  include_once "db.mysql.php";
  include_once "connectdb.php";
  include_once "parameter.php";
  include_once "cekparam.php";

if (file_exists("../fpdf.php")) require("../fpdf.php");
else require("fpdf.php");


class PDF extends FPDF {

function Header() {
$JadwalIDs = $_REQUEST['JadwalID']+0;
$ProdiIDs = GetSetVar('ProdiID');
$jdwls = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
    left outer join mk mk on mk.MKID = j.MKID
    left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w') 
	LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    ",
    "j.JadwalID", $JadwalIDs,
    "j.*, concat(d.Nama, ', ', d.Gelar) as DSN, d.NIDN,
    prd.Nama as _PRD, prg.Nama as _PRG,
    mk.Sesi,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
    date_format(j.UASTanggal, '%w') as _UASHari,
    huas.Nama as HRUAS,
    LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai, k.Nama AS namaKelas
    ");

 $TahunIDs = $jdwls['TahunID'];
$thns = GetFields('tahun', "KodeID = '".KodeID."' and ProdiID = '$jdwls[ProdiID]' and ProgramID = '$jdwls[ProgramID]' and TahunID", $TahunIDs, "*");


$t = 5; $lbr = 190;

  // Tampilkan

    $mrg = 35;
    $pjg = 150;
    $identitas = GetFields('identitas', "Kode", KodeID, '*');
    $logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $this->Image($logo, 20, 14, 18);
    $this->SetFont("Times", '', 11);
    $this->Cell($mrg);
    $this->Cell($pjg, 6, $identitas['Yayasan'], 0, 1, 'C');
    $this->SetFont("Times", 'B', 15);
    $this->Cell($mrg);
    $this->Cell($pjg, 7, $identitas['Nama'], 0, 1, 'C');
    $this->SetFont("Times", 'I', 10);
    $this->Cell($mrg);
    //$this->Cell($mrg);
    //$this->Cell($pjg, 5, $identitas['Kota'], 0, 1, 'C');
    $this->SetFont("Times", 'I', 7);
    $this->Cell($mrg);
    $this->Cell(80, 5,
      $identitas['Alamat1']. " Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'].", Website:".$identitas['Website'].", Email:".$identitas['Email'], 0, 1, 'C');

    // Kolom 1
    $this->SetFont('Helvetica', 'I', 7);
    $this->Cell(52, 5, "", 0, 0);
    $this->Cell(4, 5, "", 0, 0, 'C');
    $this->SetFont('Helvetica', 'I', 7);
    $this->Cell(12, 5, "Kode MK: ", 0, 0);
    $this->SetFont('Helvetica', 'B', 7);
    $this->Cell(15, 5, $jdwls['MKKode'], 0, 0);
    $this->SetFont('Helvetica', 'I', 7);
    $this->Cell(8, 5, "Kelas: ", 0, 0,'L');
    $this->SetFont('Helvetica', 'B', 7,'L');
    $this->Cell(17, 5, $jdwls['namaKelas'], 0, 0);
    $this->SetFont('Helvetica', 'I', 7);
    $this->Cell(16, 5, "Nama Dosen: ", 0, 0);
    $this->SetFont('Helvetica', 'B', 7);
    $this->Cell(40, 5, $jdwls['DSN'], 0, 1);
    $this->Cell(1);
    $this->Cell(190, 0, "", 1, 1);
    $this->Ln(1);
  }

}
function BuatHeaderPDF($p, $x=10, $y=5, $w=190) {
  $p->Image("../img/header_image.gif", $x, $y, $w);
  $p->Ln(26);
}
function BuatHeaderPDF0($p, $x=10, $y=5, $w=190) {
  $p->Image("../img/header_image.gif", $x, $y, $w);
}
?>