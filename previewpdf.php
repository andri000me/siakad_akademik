<?php

if (file_exists("../tcpdf/tcpdf.php")) require("../tcpdf/tcpdf.php");
else require("tcpdf/tcpdf.php");

class PDF extends TCPDF {
  function Header() {
	  //set margins
		$this->SetMargins(10, 36, 10);

	//set auto page breaks
	$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $mrg = 35;
    $pjg = 150;
	$this->Ln(8);
    $identitas = GetFields('identitas', "Kode", KodeID, '*');
    $logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $this->Image($logo, 28, 10, 20);
    $this->SetFont("Times", '', 11);
    $this->Cell($mrg);
    $this->Cell($pjg, 6, $identitas['Yayasan'], 0, 1, 'C');
    $this->SetFont("Times", 'B', 15);
    $this->Cell($mrg);
    $this->Cell($pjg, 7, $identitas['Nama'], 0, 1, 'C');
    $this->SetFont("Times", 'I', 10);
    $this->Cell($mrg);
    $this->Cell($pjg, 5, $identitas['Alamat1'], 0, 1, 'C');
    //$this->Cell($mrg);
    //$this->Cell($pjg, 5, $identitas['Alamat2'], 0, 1, 'C');
    //$this->Cell($pjg, 5, $identitas['Kota'], 0, 1, 'C');
    $this->SetFont("Times", 'I', 7);
    $this->Cell($mrg);
    $this->Cell($pjg, 5,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'].", Website:".$identitas['Website'].", Email:".$identitas['Email'], 0, 1, 'C');
    $this->Cell(1);
    $this->Cell(190, 0, "", 0, 1);
	$this->writeHTML("<hr>", true, false, true, false, '');
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
