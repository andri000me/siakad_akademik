<?php
// Kostumisasi oleh Arisal Yanuarafi

if (file_exists("../tcpdf/tcpdf.php")) require("../tcpdf/tcpdf.php");
else require("tcpdf/tcpdf.php");

class PDF extends TCPDF {
  function Header() {
    $mrg = 35;
    $pjg = 150;
    
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
