<?php
session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

// *** Parameters ***
$gel = $_REQUEST['gel'];
$tahun = substr($gel, 0, 4);
$prevtahun = ($tahun-1);

$lbr = 190;

// *** Cetak ***
$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetTitle("Statistik Aplikan - $prevtahun/$tahun");
$pdf->AddPage('L');

//BuatHeaderLap($prevtahun, $tahun, $pdf);
TampilkanIsinya($prevtahun, $tahun, $pdf);

$pdf->Output();

// *** Functions ***
function BuatHeaderLap($prevtahun, $tahun, $p) {
  global $lbr;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Statistik Aplikan - $prevtahun/$tahun", 0, 1, 'C');
  $p->Ln(4);
}
function TampilkanIsinya($prevtahun, $tahun, $p) {
  $t = 6; $lebar = 8;
  // Ambil Prodinya
  $arrStatusAplikan = array('APL', 'BLI', 'DFT', 'USM', 'LLS', 'REG');
  $arrRevStatusAplikan = array('REG', 'LLS', 'USM', 'DFT', 'BLI', 'APL');
  GetArrayPresenter($arrPresenterID, $arrPresenter);
  BuatHeaderTabel($arrStatusAplikan, $lebar, $p, $prevtahun, $tahun);
  
  
  for ($i = 0; $i < sizeof($arrPresenterID); $i++) {
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(10, $t, $arrPresenterID[$i], 'B', 0);
    //$p->Cell(60, $t, $arrPresenter[$i], 'B', 0);
    $p->Cell($lebar, $t, '', 'B', 0);
	BuatEntry($arrPresenterID[$i], $arrStatusAplikan, $arrRevStatusAplikan, $t, $lebar, $p, $arrPrevPeriod, $arrCurPeriod, $prevtahun, $tahun);
    $p->Ln($t);
  }
  //BuatTotal($arrPeriode, $arrJumlah, $lebar, $p);
}
function BuatTotal($arrPeriode, $arrJumlah, $lebar, $p) {
  $t = 6;
  $p->SetFont('Helvetica', 'BI', 11);
  $p->Cell(70, $t, 'Total :', '', 0, 'R');
  foreach ($arrPeriode as $per) {
    $p->Cell($lebar, $t, $arrJumlah[$per], '', 0, 'R');
  }
}
function BuatEntry($PresenterID, $arrStatusAplikan, $arrRevStatusAplikan, $t, $lebar, $p, &$arrPrevPeriod, &$arrCurPeriod, $prevtahun, $tahun) {
  $arrTempPrev = array();
  $arrTempCur = array();
  FillArrayPrevPeriod($PresenterID, $arrTempPrev, $arrRevStatusAplikan, $prevtahun);
  FillArrayCurPeriod($PresenterID, $arrTempCur, $arrRevStatusAplikan, $tahun);
  
  foreach ($arrStatusAplikan as $stat) {
	$p->Cell($lebar, $t, '', '', 0);
	
	$p->Cell($lebar, $t, $arrTempPrev[$stat], 'B', 0, 'R');
	
	$p->Cell($lebar, $t, 'V', 'B', 0, 'R');
	
	$p->Cell($lebar, $t, $arrTempCur[$stat], 'B', 0, 'R');
	
	$p->Cell($lebar, $t, '%', 'B', 0, 'R');
  }
}
function BuatHeaderTabel($arrStatusAplikan, $lebar, $p, $prevtahun, $tahun) {
  $t = 7;
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell(10, $t, "Pres.", 'BT', 0);
  foreach ($arrStatusAplikan as $stat) {
	$p->Cell(5*$lebar, $t, $stat, 'BT', 0, 'R');
  }
  $p->Ln($t);
  $p->Cell(10, $t, "", '', 0);
  $p->Cell($lebar, $t, '', '', 0);
  foreach ($arrStatusAplikan as $stat) {
   	$p->Cell($lebar, $t, '', '', 0);
	$p->Cell($lebar, $t, substr($prevtahun, 2, 4), 'BT', 0, 'R');
	$p->Cell($lebar, $t, 'EXP', 'BT', 0, 'R');
	$p->Cell($lebar, $t, substr($tahun, 2, 4), 'BT', 0, 'R');
	$p->Cell($lebar, $t, '%', 'BT', 0, 'R');
  }
  $p->Ln($t);
}
function GetArrayPresenter(&$arrPresenterID, &$arrPresenter) {
  $s = "select p.PresenterID, p.Nama
    from presenter p
    where p.KodeID = '".KodeID."'
    and p.NA = 'N'
    order by p.PresenterID";
  $r = _query($s);
  $arrPresenterID = array();
  $arrPresenter = array();
  while ($w = _fetch_array($r)) {
    $arrPresenterID[] = $w['PresenterID'];
    $arrPresenter[] = $w['Nama'];
  }
}

function FillArrayPrevPeriod($PresenterID, &$arrTempPrev, $arrRevStatusAplikan, $prevtahun)
{	
	foreach ($arrRevStatusAplikan as $stat) {
		$prevjml = GetaField('aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID',
		  "a.PresenterID='$PresenterID' and p.Tahun='$prevtahun' and left(a.StatusAplikan, 3)='$stat' and a.KodeID", KodeID,
		  "count(a.AplikanID)")+0;
		$arrTempPrev[$stat] += $prevjml;
	}
	foreach ($arrRevStatusAplikan as $stat) {
		
		
		$tempprev = $arrTempPrev[$stat]; 
	}
}
function FillArrayCurPeriod($PresenterID, &$arrTempCur, $arrRevStatusAplikan, $tahun)
{	
	foreach ($arrRevStatusAplikan as $stat) {
		$jml = GetaField('aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID',
		  "a.PresenterID='$PresenterID' and p.Tahun='$tahun' and left(a.StatusAplikan, 3)='$stat' and a.KodeID", KodeID,
		  "count(a.AplikanID)")+0;
		$arrTempCur[$stat] += $jml;
	}
	foreach ($arrRevStatusAplikan as $stat) {
		
	}
}
?>
