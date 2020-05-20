<?php

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Init PDF
$pdf = new PDF();
$pdf->SetTitle("Rekapitulasi Jumlah Mahasiswa Per Angkatan");
$pdf->AddPage();
$lbr = 190;

BuatIsinya($pdf);

$pdf->Output();

// *** Functions ***
function BuatHeadernya($klmn, $stt, $p) {
  global $lbr;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Rekapitulasi Jumlah Mahasiswa Per Angkatan", 0, 1, 'C');
  $p->Ln($t);
  
  $t = 5;
  $p->SetFont('Helvetica', 'BI', 9);
  
  // Baris 1
  $p->Cell(15, $t, 'Tahun', 'LTR', 0, 'C');
  $p->Cell(13, $t, 'Total', 'LTR', 0, 'R');
  $p->Cell(13*sizeof($klmn), $t, 'Kelamin', 1, 0, 'C');
  $p->Cell(13*sizeof($stt), $t, 'Status Mhsw', 1, 0, 'C');
  $p->Ln($t);
  
  // Baris 2
  $p->Cell(15, $t, 'Angktn', 'LBR', 0, 'C');
  $p->Cell(13, $t, 'Mhsw', 'LBR', 0, 'R');
  // Kelamin
  foreach ($klmn as $k) {
    $p->Cell(13, $t, $k, 1, 0, 'R');
  }
  // Status
  foreach ($stt as $_stt) {
    $p->Cell(13, $t, $_stt, 1, 0, 'R');
  }
  
  $p->Ln($t);
}
function BuatIsinya($p) {
  $t = 6;
  $arrAngkatan = GetArrayAngkatan($arrJml);
  $arrKelamin = GetArrayKelamin($arrJmlKelamin);
  $arrStatus = GetArrayStatus($arrJmlStatus);
  BuatHeadernya($arrKelamin, $arrStatus, $p);
  
  $total = 0;
  $det = array();
  for ($i = 0; $i < sizeof($arrAngkatan); $i++) {
    $angk = $arrAngkatan[$i];
    // Jumlah
    $p->SetFont('Helvetica', 'B', 10);
    $p->Cell(15, $t, $arrAngkatan[$i], 'B', 0);
    
    $p->SetFont('Helvetica', '', 10);
    $jml = number_format($arrJml[$arrAngkatan[$i]]);
    $p->Cell(13, $t, $jml, 'B', 0, 'R');
    $total += $arrJml[$arrAngkatan[$i]];
    
    
    // Kelamin
    foreach ($arrKelamin as $klmn) {
      $jmlkel = $arrJmlKelamin[$arrAngkatan[$i]][$klmn];
      $_jmlkel = ($jmlkel == 0)? '-' : number_format($jmlkel);
      $p->Cell(13, $t, $_jmlkel, 'B', 0, 'R');
      $det['kelamin_'.$klmn] += $jmlkel;
    }
    // Status
    foreach ($arrStatus as $stt) {
      $jmlstt = $arrJmlStatus[$arrAngkatan[$i]][$stt];
      $_jmlstt = ($jmlstt == 0)? '-' : number_format($jmlstt);
      $p->Cell(13, $t, $_jmlstt, 'B', 0, 'R');
      $det['status_'.$stt] += $jmlstt;
    }
    
    $p->Ln($t);
  }
  
  $p->SetFont('Helvetica', 'B', 10);
  // Menampilkan total
  $p->Cell(15, $t, "TOTAL :", 0, 0, 'R');
  $p->Cell(13, $t, number_format($total), 0, 0, 'R');
  // Total Kelamin
  foreach ($arrKelamin as $klmn) {
    $jml = $det['kelamin_'.$klmn];
    $_jml = number_format($jml);
    $p->Cell(13, $t, $_jml, 0, 0, 'R');
  }
  // Status
  foreach ($arrStatus as $stt) {
    $jml = $det['status_'.$stt];
    $_jml = number_format($jml);
    $p->Cell(13, $t, $_jml, 0, 0, 'R');
  }
  $p->Ln($t);
}
function GetArrayAngkatan(&$arrJml) {
	$whr = (!empty($_SESSION['ProdiID']))? " where ProdiID='$_SESSION[ProdiID]' " : '';
  $s = "select m.TahunID, count(m.MhswID) as JML
    from mhsw m $whr
    group by m.TahunID
    order by m.TahunID desc";
  $r = _query($s);
  $arr = array();
  while ($w = _fetch_array($r)) {
    $arr[] = $w['TahunID'];
    $arrJml[$w['TahunID']] = $w['JML'];
  }
  return $arr;
}
function GetArrayKelamin(&$arrJmlKelamin) {
	$whr = (!empty($_SESSION['ProdiID']))? " where ProdiID='$_SESSION[ProdiID]' " : '';
  $s = "select m.TahunID, count(m.MhswID) as JML, m.Kelamin
    from mhsw m $whr
    group by m.Kelamin, m.TahunID";
  $r = _query($s);
  $arr = array();
  while ($w = _fetch_array($r)) {
    if (array_search($w['Kelamin'], $arr) === false) $arr[] = $w['Kelamin'];
    $arrJmlKelamin[$w['TahunID']][$w['Kelamin']] = $w['JML'];
    
  }
  return $arr;
}
function GetArrayStatus(&$arrJmlStatus) {
	$whr = (!empty($_SESSION['ProdiID']))? " where ProdiID='$_SESSION[ProdiID]' " : '';
  $s = "select m.TahunID, count(m.MhswID) as JML, m.StatusMhswID
    from mhsw m $whr
    group by m.StatusMhswID, m.TahunID";
  $r = _query($s);
  $arr = array();
  while ($w = _fetch_array($r)) {
    if (array_search($w['StatusMhswID'], $arr) === false) $arr[] = $w['StatusMhswID'];
    $arrJmlStatus[$w['TahunID']][$w['StatusMhswID']] = $w['JML'];
  }
  // *** Jika ada yg tidak beres dgn status mhsw
  if (sizeof($arr) <= 1) {
    $arr = array();
    $s = "select StatusMhswID
      from statusmhsw
      where NA = 'N'
      order by StatusMhswID";
    $r = _query($s);
    while ($w = _fetch_array($r)) {
      $arr[] = $w['StatusMhswID'];
    }
  }
  return $arr;
}
?>
