<?php
// Kostumisasi oleh Arisal Yanuarafi

if (file_exists("../fpdf.php")) require("../fpdf.php");
else require("fpdf.php");

class PDF extends FPDF {
  function Header() {
    $mrg = 35;
    $pjg = 150;
    $identitas = GetFields('identitas', "Kode", KodeID, '*');
    $logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $this->Image($logo, 32, 5, 20);
    $this->SetFont("Helvetica", '', 10);
    $this->Cell($mrg);
    $this->Cell($pjg, 4, $identitas['Yayasan'], 0, 1, 'C');
    $this->SetFont("Helvetica", 'B', 15);
    $this->Cell($mrg);
    $this->Cell($pjg, 5, $identitas['Nama'], 0, 1, 'C');
    $this->SetFont("Helvetica", 'I', 7);
    $this->Cell($mrg);
    $this->Cell($pjg, 4, $identitas['Alamat1'], 0, 1, 'C');
    $this->Cell(1);
	$this->Cell($mrg);
    $this->Cell($pjg, 4, "Website: ".$identitas['Website'].", Email: ".$identitas['Email'], 0, 1, 'C');
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
