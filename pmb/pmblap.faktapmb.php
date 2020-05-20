<?php
session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";


// *** Parameters ***
$Kelipatan = GetSetVar('Kelipatan');
$gel = $_REQUEST['gel'];
$tahun = GetaField('pmbperiod', "PMBPeriodID='$gel' and KodeID", KodeID, 'Tahun');
$prevtahun = $tahun-1;
$nexttahun = $tahun+1;
$urutan = GetaField('pmbperiod', "PMBPeriodID='$gel' and KodeID", KodeID, 'Urutan');

$gos = (empty($_REQUEST['gos']))? 'ParameterSetup' : $_REQUEST['gos'];
$gos($prevtahun, $tahun, $nexttahun, $urutan, $gel);

function ParameterSetup($prevtahun, $tahun, $nexttahun, $urutan, $gel)
{	if(empty($tahun)) echo "<b>Tahun $gel tidak terdaftar. Harap menghubungi Kepala PMB untuk membenarkannya</b>";
	else
	{
		CheckFormScript("Kelipatan");
		echo "<p><table class=bsc cellspacing=1 width=400 border=1 align=center>
				<form action='../$_SESSION[mnux].faktapmb.php' method=POST onSubmit=\"return CheckForm(this)\">
				<input type=hidden name='gos' value='CetakLaporan' />
				<input type=hidden name='gel' value='$gel' />
				
				<tr><td class=inp align=right width=50%>Kelipatan Target Tahun <b>$tahun</b> : </td>
					<td class=ul1><input type=text name='Kelipatan' value='$_SESSION[Kelipatan]' size=3 maxlength=5 \>x dari tahun <b>$prevtahun</b></td>
				</tr>
					<tr><td class=ul1 colspan=2 align=center>
					<input type=submit name='Simpan' value='Buat Laporan' />
					<input type=button name='Batal' value='Batal' onClick='window.close()' />
				</td></tr>
			  </form></table></p>";
	}
}

function CetakLaporan($prevtahun, $tahun, $nexttahun, $urutan, $gel)
{
	include_once "../fpdf.php";
	require_once "../phplot.php";

	$lbr = 190;
	
	$arrStatusAplikan = array(); 
	$s = "select * from statusaplikan where KodeID='".KodeID."' and StatusAplikanID not in ('BLI','USM','UK') order by Urutan ASC";
	$r = _query($s);
	while($w = _fetch_array($r)) $arrStatusAplikan[] = $w['StatusAplikanID'];
	
	$arrGelombang = array();
	getArrayGelombang($arrGelombang, $tahun);
			
	// Buat Graph dan dimasukkan ke file dulu
	$piepath = '../tmp/data_fakta_pmb_pie_graph.png';
	$barpath = '../tmp/data_fakta_pmb_bar_graph.png';
	BuatPieGraph($piepath, $prevtahun, $tahun, $urutan, $gel);
	BuatBarGraph($barpath, $prevtahun, $tahun, $arrStatusAplikan, $urutan, $gel);
	
	// *** Cetak ***
	$pdf = new FPDF('L', 'mm', 'A4');
	$pdf->SetAutoPageBreak(true, 5);
	$pdf->SetTitle("DATA & FAKTA PMB TAHUN AJARAN $tahun/$nexttahun GELOMBANG ".UbahKeRomawiLimit99($urutan));
	$pdf->AddPage('L');
	
	BuatHeaderLap($prevtahun, $tahun, $pdf);
	TampilkanIsinya($prevtahun, $tahun, $arrStatusAplikan, $arrGelombang, $pdf );
	
	TampilkanGraph($piepath, 30, 75, 96, 64, $pdf);
	TampilkanGraph($barpath, 185, 75, 96, 64, $pdf);
	
	TampilkanSumberInformasi(60, 145, $prevtahun, $tahun, $urutan, $gel, $pdf);
	TampilkanRatioPresenter(185, 145, $arrStatusAplikan, $tahun, $urutan, $gel, $pdf);
	
	$pdf->Output();
}


function BuatHeaderLap($prevtahun, $tahun, $p) {
  global $lbr;
  $t = 3;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, $t, "DATA & FAKTA PMB TA $prevtahun/$tahun", 0, 0, 'C');
  $p->Ln($t);
}
function TampilkanIsinya($prevtahun, $tahun, $arrStatusAplikan, $arrGelombang, $p) {
  $t = 3; $lebar = 9;
  $arrCurTotal = array();
  $arrPrevTotal = array();
  BuatHeaderTabel($arrStatusAplikan, $lebar, $prevtahun, $tahun, $p);
  for ($i = 0; $i < sizeof($arrGelombang); $i++) {
    $p->SetFont('Helvetica', 'B', 6);
    $p->Cell($lebar, $t, UbahKeRomawiLimit99($arrGelombang[$i]), 1, 0, 'C');
	$p->SetFont('Helvetica', '', 6);
	BuatEntry($arrGelombang[$i], $arrStatusAplikan, $arrPrevTotal, $arrCurTotal, $t, $lebar, 
				$prevtahun, $tahun, $p);
    $p->Ln($t);
  }
  BuatTotal($arrStatusAplikan, $arrPrevTotal, $arrCurTotal, $lebar, $tahun, $p);
  
  return $arrTotal;
}
function BuatTotal($arrStatusAplikan, $arrPrevTotal, $arrCurTotal, $lebar, $tahun, $p) {
  $t = 3;
  $p->SetFont('Helvetica', 'B', 6);
  $p->Cell($lebar, $t, '', '', 0);
  foreach ($arrStatusAplikan as $stat) {
    $p->Cell($lebar, $t, $arrPrevTotal[$stat], 1, 0, 'C');
	$p->Cell($lebar, $t, "100%", 1, 0, 'C');
	//$totaltarget = GetaField('pmbperiod', "Tahun", $tahun, 'sum(Target'.$stat.')');
	$totaltarget = $arrPrevTotal[$stat]*$_SESSION['Kelipatan'];	
	$p->Cell($lebar, $t, $totaltarget, 1, 0, 'C');
	$p->Cell($lebar, $t, $arrCurTotal[$stat], 1, 0, 'C');
	$persen = ($totaltarget <= 0)? 0 : number_format($arrCurTotal[$stat]/$totaltarget*100, 1, ",", ".") ; 
	$p->Cell($lebar, $t, $persen.'%', 1, 0, 'C');
  }
  $p->Ln($t);
}
function BuatEntry($Urutan, $arrStatusAplikan, &$arrPrevTotal, &$arrCurTotal, $t, $lebar, $prevtahun, $tahun, $p) {
  $arrTempPrev = array();
  $arrTempCur = array();
  FillArrayPrevPeriod($Urutan, $arrTempPrev, $arrPrevTotal, $arrStatusAplikan, $prevtahun);
  FillArrayCurPeriod($Urutan, $arrTempCur, $arrCurTotal, $arrStatusAplikan, $tahun);
  $p->SetFont('Helvetica', '', 6);
  foreach ($arrStatusAplikan as $stat) {
	$p->Cell($lebar, $t, $arrTempPrev[$stat], 1, 0, 'C'); 
	$p->Cell($lebar, $t, number_format(0, 1, ",", ".").'%', 1, 0, 'C');
	//$target = GetaField('pmbperiod', "Tahun='$tahun' and Urutan", $Urutan, 'Target'.$stat);
	$target = $arrTempPrev[$stat]*$_SESSION['Kelipatan'];
	$p->Cell($lebar, $t, $target, 1, 0, 'C');
	$p->Cell($lebar, $t, $arrTempCur[$stat], 1, 0, 'C');
	$persen = ($target <= 0)? 0 : number_format($arrTempCur[$stat]/$target*100, 1, ",", ".") ; 
	$p->Cell($lebar, $t, $persen.'%', 1, 0, 'C');
  }
 	
}
function BuatHeaderTabel($arrStatusAplikan, $lebar, $prevtahun, $tahun, $p ) {
  $t = 3;
  $p->SetFont('Helvetica', 'B', 6);
  $p->Cell($lebar, $t, "", 'LT', 0);
  foreach ($arrStatusAplikan as $stat) {
	$p->Cell(5*$lebar, $t, $stat, 'LTR', 0, 'C');
  }
  $p->Ln($t);
  $p->Cell($lebar, $t, "GEL", 'L', 0);
  foreach ($arrStatusAplikan as $stat) {
	$p->Cell(2*$lebar, $t, $prevtahun, 'LTR', 0, 'C');
	$p->Cell(3*$lebar, $t, $tahun, 'LTR', 0, 'C');
  }
  $p->Ln($t);
  $p->Cell($lebar, $t, "", 'L', 0);
  foreach ($arrStatusAplikan as $stat) {
	$p->Cell($lebar, $t, 'REA', 'LTR', 0, 'C');
	$p->Cell($lebar, $t, '%', 'LTR', 0, 'C');
	$p->Cell($lebar, $t, 'TGT', 'LTR', 0, 'C');
	$p->Cell($lebar, $t, 'REA', 'LTR', 0, 'C');
	$p->Cell($lebar, $t, '%', 'LTR', 0, 'C');
  }
  $p->Ln($t);
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
function FillArrayPrevPeriod($Urutan, &$arrTempPrev, &$arrPrevTotal, $arrStatusAplikan, $prevtahun)
{	
	foreach ($arrStatusAplikan as $stat) {
		$jml = GetaField("statusaplikanmhsw sam left outer join aplikan a on sam.AplikanID=a.AplikanID
											    left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and p.KodeID='".KodeID."'",
							"p.Tahun='$prevtahun' and p.Urutan='$Urutan' and sam.StatusAplikanID='$stat' and a.KodeID", KodeID,
								'count(sam.StatusAplikanMhswID)+0');
		$arrTempPrev[$stat] = $jml;
		$arrPrevTotal[$stat] += $jml;
	}
}

function FillArrayCurPeriod($Urutan, &$arrTempCur, &$arrCurTotal, $arrStatusAplikan, $tahun)
{	
	foreach ($arrStatusAplikan as $stat) {
		$jml = GetaField("statusaplikanmhsw sam left outer join aplikan a on sam.AplikanID=a.AplikanID
											    left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and p.KodeID='".KodeID."'",
							"p.Tahun='$tahun' and p.Urutan='$Urutan' and sam.StatusAplikanID='$stat' and a.KodeID", KodeID,
								'count(sam.StatusAplikanMhswID)+0');
		$arrTempCur[$stat] = $jml;
		$arrCurTotal[$stat] += $jml;
	}
}

function TampilkanGraph($piepath, $X, $Y, $width, $height, $p)
{	$p->Image($piepath, $X, $Y, $width, $height);
}

function TampilkanSumberInformasi($X, $Y, $prevtahun, $tahun, $urutan, $gel, $p)
{	$t = 3; $lebar = 9;

	$p->SetXY($X, $Y);
	$p->Cell($lebar, $t, 'NO', 1, 0, 'C');
	$p->Cell(6*$lebar, $t, 'S. INFORMASI', 1, 0, 'L');
	$p->Cell($lebar, $t, 'FG', 1, 0, 'C');
	$p->Cell($lebar, $t, 'NFG', 1, 0, 'C');
	$p->Cell($lebar, $t, 'TOT', 1, 0, 'C');
	$p->Ln($t);
	$p->SetX($X);
	
	$s = "select InfoID, Nama from sumberinfo where NA='N' order by Urutan";
	$r = _query($s);
	$count = 0;
	$totfg = 0;
	$totnfg = 0;
	$tottot = 0;
	while($w=_fetch_array($r))
	{	$count++;
		$p->Cell($lebar, $t, $count, 1, 0, 'C');
		$p->Cell(6*$lebar, $t, $w['Nama'], 1, 0, 'L');
		/*$fg = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='$tahun'", 
						"INSTR(concat(',',a.SumberInformasi,','), concat(',',$w[InfoID],','))>0 and (a.TahunLulus='$prevtahun' or a.TahunLulus='$tahun') and a.KodeID", KodeID, 
							"count(a.AplikanID)");*/
		$fg = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='$tahun'", 
							"INSTR(concat(',',a.SumberInformasi,','), concat(',',$w[InfoID],','))>0 and (a.TahunLulus='$prevtahun' or a.TahunLulus='$tahun') and a.PMBPeriodID like '$gel%' and a.KodeID", KodeID, 
							"count(a.AplikanID)");
		$totfg +=$fg;
		$p->Cell($lebar, $t, $fg, 1, 0, 'C');
		/*$nfg = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='$tahun'", 
							"INSTR(a.SumberInformasi, $w[InfoID])>0 and a.TahunLulus < '$prevtahun' and a.KodeID", KodeID, 
							"count(a.AplikanID)"); */
		$nfg = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='$tahun'", 
							"INSTR(a.SumberInformasi, $w[InfoID])>0 and a.TahunLulus < '$prevtahun' and a.PMBPeriodID like '$gel%' and a.KodeID", KodeID, 
							"count(a.AplikanID)");
		$p->Cell($lebar, $t, $nfg, 1, 0, 'C');
		$totnfg +=$nfg;
		$tot = $fg+$nfg;
		$p->Cell($lebar, $t, $tot, 1, 0, 'C');
		$p->Ln($t);
		$p->SetX($X);
		$tottot +=$tot;
	}
	$p->Cell(7*$lebar, $t, '', 0, 0, 'L');
	$p->Cell($lebar, $t, $totfg, 1, 0, 'C');
	$p->Cell($lebar, $t, $totnfg, 1, 0, 'C');
	$p->Cell($lebar, $t, $tottot, 1, 0, 'C');
	$p->Ln($t);
}

function TampilkanRatioPresenter($X, $Y, $arrStatusAplikan, $tahun, $urutan, $gel, $p)
{	$t = 3; $lebar = 9;
	$p->SetXY($X, $Y);
	$p->Cell($lebar, $t, 'PRES.', 1, 0, 'C');
	foreach($arrStatusAplikan as $stat)
	{	$p->Cell($lebar, $t, $stat, 1, 0, 'C');
		if($stat == 'DFT') $p->Cell($lebar, $t, $stat.'%', 1, 0, 'C');
		if($stat == 'REG') $p->Cell($lebar, $t, $stat.'%', 1, 0, 'C');
	}
	$p->Ln($t);
	$p->SetX($X);
	
	$s = "select PresenterID from presenter where NA = 'N' and KodeID='".KodeID."' order by Nama";
	$r = _query($s);
	$arrTotal=array();
	while($w = _fetch_array($r))
	{	$arrTempCur = array();
		FillArrayCurPeriodByPresID($w['PresenterID'], $arrTempCur, $arrTotal, $arrStatusAplikan, $tahun);
		$p->Cell($lebar, $t, $w['PresenterID'], 1, 0, 'C');
		foreach ($arrStatusAplikan as $stat) {
			$p->Cell($lebar, $t, $arrTempCur[$stat], 1, 0, 'C');
			
			if($stat == 'DFT')
			{	$persen = ($arrTempCur['APL'] == 0)? 0 : floor(($arrTempCur['DFT']/$arrTempCur['APL'])*100);
				$p->Cell($lebar, $t, $persen.'%', 1 , 0, 'C');
			}
			if($stat == 'REG')
			{	$persen = ($arrTempCur['APL'] == 0)? 0 : floor(($arrTempCur['REG']/$arrTempCur['APL'])*100);
				$p->Cell($lebar, $t, $persen.'%', 1 , 0, 'C');
			}
		}
		$p->Ln($t);
		$p->SetX($X);
	}
	$p->Cell($lebar, $t, '', 'T', 0);
	foreach ($arrStatusAplikan as $stat) {
		$p->Cell($lebar, $t, $arrTotal[$stat], 1, 0, 'C');
		if($stat == 'DFT') 
		{	$persen = ($arrTotal['APL'] == 0)? 0 : floor(($arrTotal['DFT']/$arrTotal['APL'])*100);
			$p->Cell($lebar, $t, $persen.'%', 1 , 0, 'C');
		}
		if($stat == 'REG') 
		{	$persen = ($arrTotal['APL'] == 0)? 0 : floor(($arrTotal['REG']/$arrTotal['APL'])*100);
			$p->Cell($lebar, $t, $persen.'%', 1 , 0, 'C');
		}
	}
	$p->Ln($t);
}

function BuatPieGraph($filetujuan, $prevtahun, $tahun, $urutan, $gel)
{	$fg = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='$tahun'", 
							//"(a.TahunLulus='$prevtahun' or a.TahunLulus='$tahun') and a.KodeID", KodeID, 
							"a.SudahBekerja='N' and a.KodeID", KodeID, 
							"count(a.AplikanID)");
	$nfg = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='$tahun'", 
						//"a.TahunLulus < '$prevtahun' and a.KodeID", KodeID, 
						"a.SudahBekerja='Y' and a.KodeID", KodeID, 
						"count(a.AplikanID)");

	$data = array(
		  array('Fresh Graduate', $fg),
		  array('Non Fresh Graduate', $nfg),
		);
		
		$plot = new PHPlot(800,600);
		//$plot->SetImageBorderType('raised');
		
		$plot->SetPlotType('pie');
		$plot->SetDataType('text-data-single');
		$plot->SetDataValues($data);
		
		$plot->SetDataColors(array('red', 'blue', 'green', 'yellow', 'cyan',
								'magenta', 'brown', 'lavender', 'pink',
								'gray', 'orange'));
		
		
		$plot->setShading(60);
		$plot->SetLabelScalePosition(0.2);
		$plot->SetFont('generic', 5);
		$plot->SetFont('title', 5);
		$plot->SetFont('legend', 5);
		
		$plot->SetTitle("Resume % Aplikan Fresh / non Fresh Graduate");
		
		foreach ($data as $row)
		  $plot->SetLegend(implode(': ', $row));
		
		$plot->SetIsInline(true);
		$plot->SetOutputFile($filetujuan);
		$plot->DrawGraph();
}


function BuatBarGraph($filetujuan, $prevtahun, $tahun, $arrStatusAplikan, $urutan, $gel)
{	$arrPrevTotal = array();
	$arrCurTotal = array();
	FillArrayPeriod($arrPrevTotal, $arrStatusAplikan, $prevtahun, $gel);
	FillArrayPeriod($arrCurTotal, $arrStatusAplikan, $tahun, $gel);
	
	$maxPrevHeight = 0;
	$maxCurHeight = 0;
	foreach($arrStatusAplikan as $stat)
	{	$data[] = array($stat, $arrPrevTotal[$stat], $arrCurTotal[$stat]);
		$maxPrevHeight = ($maxPrevHeight < $arrPrevTotal[$stat])? $arrPrevTotal[$stat] : $maxPrevHeight;
		$maxCurHeight = ($maxCurHeight < $arrCurTotal[$stat])? $arrCurTotal[$stat] : $maxCurHeight;
	}
	
	$plot = new PHPlot(800, 600);
	//$plot->SetImageBorderType('raised');
	
	$plot->SetFont('y_label', 5);
	$plot->SetFont('x_label', 5);
	$plot->SetFont('title', 5);
	$plot->SetFont('legend', 5);
	$plot->setShading(10);
	
	$plot->SetPlotType('bars');
	$plot->SetDataType('text-data');
	$plot->SetDataValues($data);
	
	$plot->SetTitle('GRAFIK & DATA PMB GEL SISIPAN');
	$plot->SetLegend(array($prevtahun, $tahun));

	$plot->SetXTickLabelPos('none');
	$plot->SetXTickPos('none');
	
	$maxHeight = ($maxPrevHeight < $maxCurHeight)? $maxCurHeight : $maxPrevHeight;
	$increment = ($maxHeight <= 50)? 5 : (($maxHeight <=100)? 10 : (($maxHeight <= 500)? 50 : 100)) ; 
	
	$plot->SetYTickIncrement($increment);
	$plot->SetYDataLabelPos('plotin');
	
	$plot->SetIsInline(true);
	$plot->SetOutputFile($filetujuan);
	$plot->DrawGraph();
}

function FillArrayCurPeriodByPresID($PresenterID, &$arrTempCur, &$arrTotal, $arrStatusAplikan, $tahun)
{	
	foreach ($arrStatusAplikan as $stat) {
		$jml = GetaField("statusaplikanmhsw sam left outer join aplikan a on sam.AplikanID=a.AplikanID
											    left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and p.KodeID='".KodeID."'",
							    "a.PresenterID='$PresenterID' and p.Tahun='$tahun' and sam.StatusAplikanID='$stat' and a.KodeID", KodeID,
								'count(sam.StatusAplikanMhswID)+0');
		$arrTempCur[$stat] = $jml;
		$arrCurTotal[$stat] += $jml;
	}
}

function FillArrayPeriod(&$arrTempCur, $arrStatusAplikan, $tahun, $gel)
{	
	foreach ($arrStatusAplikan as $stat) {
		$jml = GetaField("statusaplikanmhsw sam left outer join aplikan a on sam.AplikanID=a.AplikanID
											    left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and p.KodeID='".KodeID."'",
							"p.Tahun='$tahun' and sam.StatusAplikanID='$stat' and a.KodeID", KodeID,
								'count(sam.StatusAplikanMhswID)+0');
		$arrTempCur[$stat] = $jml;
		$arrTotal[$stat] += $jml;
	}
}

function UbahKeRomawiLimit99($integer)
{	if($integer<0) return $integer;
	$arrRomanOnes = array('0', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX');
	if($integer<10) return $arrRomanOnes[$integer]; 
	else
	{	if($integer<100)
		{	$arrRomanTens = array('X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC');
			$integertens = ceil($integer/10);
			$integerones = $integer%10;
			if($integerones == 0) return $arrRomanTens[$integertens];
			else return $arrRomanTens[$integertens].$arrRomanOnes[$integerones];
		}
		else
		{	return $integer;
		}
	}
}
?>
