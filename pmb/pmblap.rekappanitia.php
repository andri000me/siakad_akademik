<?php

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";

// *** Parameters ***
$Fee = GetSetVar('Fee');
$gel = $_REQUEST['gel'];
$tahun = GetaField('pmbperiod', "PMBPeriodID='$gel' and KodeID", KodeID, 'Tahun');
$prevtahun = ($tahun-1);

$gos = (empty($_REQUEST['gos']))? 'CetakLaporan' : $_REQUEST['gos'];
$gos($prevtahun, $tahun, $gel);

function CetakLaporan($prevtahun, $tahun, $gel)
{	include_once "../fpdf.php";

	$lbr = 190;
	
	$pdf = new FPDF('L', 'mm', 'A4');
	$pdf->SetTitle("Ratio Presenter PMB - $prevtahun/$tahun");
	$pdf->AddPage('L');
	$arrGelombang = array();
	getArrayGelombang($arrGelombang, $tahun);
	$arrPejabatID = array();
	GetArrayPejabat($arrPejabatID, $tahun);
	
	// Buat Table 1
	BuatHeaderLap($prevtahun, $tahun, $pdf);
	TampilkanIsinya($prevtahun, $tahun, $arrGelombang, $arrPejabatID, $pdf );
	
	$pdf->Output();
}

// *** Functions ***
function BuatHeaderLap($prevtahun, $tahun, $p) {
  global $lbr;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "REKAPITULASI KEHADIRAN PMB $tahun", 0, 1, 'C');
  $p->Ln($t);
}
function TampilkanIsinya($prevtahun, $tahun, $arrGelombang, $arrPejabatID, $p) {
  $t = 6; $lebar = 19;
  BuatHeaderTabel($arrPejabatID, $arrGelombang, $lebar, $prevtahun, $tahun,  $p);
  
  $count = 0;
  for ($i = 0; $i < sizeof($arrPejabatID); $i++) {
    $count++;
	$p->SetFont('Helvetica', '', 8);
	$p->Cell(8, $t, $count, 1, 0, 'C');
    $namapejabat = GetaField('pejabat', 'PejabatID', $arrPejabatID[$i], 'NamaInisial');
	$p->Cell($lebar, $t, $namapejabat, 1, 0, 'C');
	BuatEntry($arrPejabatID[$i], $t, $lebar, $arrGelombang, $prevtahun, $tahun, $p);
    $p->Ln($t);
  }
}
function BuatEntry($PejabatID, $t, $lebar, $arrGelombang, $prevtahun, $tahun,  $p) {
  $arrHadirWawancara = array();
  $arrHadirSupporting = array();
  FillArrayCurPeriod($PejabatID, $arrGelombang, $arrHadirWawancara, $arrHadirSupporting, $arrTotal, $tahun);
  
  $p->SetFont('Helvetica', '', 8);
 
  $countWawancara = 0;
  $countSupporting = 0;
  $p->SetFillColor(255, 255, 255);
  foreach ($arrGelombang as $stat) {
	$isiBlok = '';
	if($arrHadirWawancara[$stat] > 0) 
	{	$countWawancara++;
		$isiBlok = 'WAW';
	}
	else if($arrHadirSupporting[$stat] > 0)
	{	$countSupporting++;
		$isiBlok = (empty($IsiBlok))? 'SUP' : $IsiBlok;
	}
	
	if($isiBlok == 'WAW')	$p->SetFillColor(255, 0, 0);	
	else if($isiBlok == 'SUP') $p->SetFillColor(0, 0, 255);
	$p->Cell($lebar, $t, '', 1, 0, 'C', true);
	$p->SetFillColor(255, 255, 255);
  }
  
  $p->Cell($lebar+4, $t, $countWawancara, 1, 0, 'C');
  $p->Cell($lebar+4, $t, $countSupporting, 1, 0, 'C');
  $p->Cell($lebar+4, $t, $countWawancara+$countSupporting, 1, 0, 'C');
}
function BuatHeaderTabel($arrPejabatID, $arrGelombang, $lebar, $prevtahun, $tahun, $p ) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 8);
  
  // Baris 1
  $p->Cell(8, $t, "", 'LT', 0);
  $p->Cell($lebar, $t, '', 'LTR', 0);
  $p->Cell(sizeof($arrGelombang)*$lebar, $t, 'TGL UJIAN', 'T', 0, 'C');
  $p->Cell($lebar+4, $t, "", 'LT', 0);
  $p->Cell($lebar+4, $t, "", 'LT', 0);
  $p->Cell($lebar+4, $t, "TOTAL", 'LTR', 0, 'C');
  $p->Ln($t);
  
  // Baris 2
  $p->Cell(8, $t, "NO", 'L', 0);
  $p->Cell($lebar, $t, 'NAMA', 'LR', 0, 'C');
  foreach ($arrGelombang as $stat) {
	$p->Cell($lebar, $t, UbahKeRomawiLimit99($stat), 1, 0, 'C');
  }
  $p->Cell($lebar+4, $t, "WAWANCARA", 'L', 0,'C');
  $p->Cell($lebar+4, $t, "SUPPORTING", 'L', 0, 'C');
  $p->Cell($lebar+4, $t, "SEMUA", 'LR', 0, 'C');
  $p->Ln($t);
  
  //Baris 3
  $p->Cell(8, $t, "", 'L', 0);
  $p->Cell($lebar, $t, '', 'LR', 0);
  foreach ($arrGelombang as $stat) {
	$tglujian = GetaField('pmbperiod', "Tahun='$tahun' and Urutan='$stat' and KodeID", KodeID, "date_format(UjianMulai, '%d-%b-%Y')"); 
	$p->Cell($lebar, $t, $tglujian, 1, 0, 'C');
  }
  $p->Cell($lebar+4, $t, "", 'L', 0);
  $p->Cell($lebar+4, $t, "", 'L', 0);
  $p->Cell($lebar+4, $t, "KEHADIRAN", 'LR', 0, 'C');
  $p->Ln($t);
}
function GetArrayPejabat(&$arrPejabatID, $tahun) {
  $s = "select distinct(p.PejabatID), 
			count(w.WawancaraID)+count(w2.WawancaraID) as _count1, 
			count(pp.TechnicalSupportID) as _count2
		from pejabat p left outer join wawancara w on p.PejabatID=w.Pewawancara and p.KodeID=w.KodeID
					   left outer join wawancara w2 on p.PejabatID=w2.Pewawancara2 and p.KodeID=w2.KodeID
					   left outer join pmbperiod pp on INSTR(concat('~',pp.TechnicalSupportID,'~'),concat('~',p.PejabatID,'~'))>0 and p.KodeID=pp.KodeID
		where p.KodeID = '".KodeID."'
		and p.NA = 'N'
		group by p.PejabatID
		order by _count1 DESC, _count2 DESC";
  //$s = "select PejabatID from pejabat where KodeID='".KodeID."' and NA='N'";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arrPejabatID[] = $w['PejabatID'];
  }
}
function FillArrayCurPeriod($PejabatID, $arrGelombang, &$arrHadirWawancara, &$arrHadirSupporting, &$arrTotal, $tahun)
{	
	foreach ($arrGelombang as $stat) {
		$jml = GetaField("wawancara w left outer join pmbperiod pp on w.PMBPeriodID=pp.PMBPeriodID and w.KodeID=pp.KodeID",
		  "(w.Pewawancara='$PejabatID' or w.Pewawancara2='$PejabatID') and pp.Tahun='$tahun' and pp.Urutan='$stat' and w.KodeID", KodeID,
		  "count(w.WawancaraID)")+0;
		$arrHadirWawancara[$stat] = $jml;
	}
	foreach ($arrGelombang as $stat) {
		$jml = GetaField("pmbperiod pp", "INSTR(concat('~',pp.TechnicalSupportID,'~'),concat('~',$PejabatID,'~'))>0 and pp.Tahun='$tahun' and pp.Urutan='$stat' and pp.KodeID", KodeID, 'count(pp.PMBPeriodID)')+0;
		$arrHadirSupporting[$stat] = $jml;
	}
}
function GetArrayGelombang(&$arrGelombang, $tahun) {
  $s = "select p.Urutan  
		from pmbperiod p 
		where p.KodeID = '".KodeID."'
		and p.Tahun = '$tahun'
		order by p.Urutan";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arrGelombang[] = $w['Urutan'];
  }
}
function UbahKeRomawiLimit99($integer)
{	$arrRomanOnes = array('I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX');
	if($integer<10) return $arrRomanOnes[$integer-1]; 
	else
	{	if($integer<100)
		{	$arrRomanTens = array('X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC');
			$integertens = floor($integer/10);
			return $arrRomanTens[$intergertens-1].$arrRomanOnes[$integer-1];
		}
		else
		{	return 'FAIL';
		}
	}
}
?>
