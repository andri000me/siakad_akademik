<?php

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";


// *** Parameters ***
$gel = $_REQUEST['gel'];
$tahun = substr($gel, 0, 4);
$prevtahun = $tahun-1;
$nexttahun = $tahun+1;
$urutan = substr($gel, 4, 1);

$gos = (empty($_REQUEST['gos']))? 'CetakLaporan' : $_REQUEST['gos'];
$gos($prevtahun, $tahun, $nexttahun, $urutan, $gel);

function CetakLaporan($prevtahun, $tahun, $nexttahun, $urutan, $gel)
{
	include_once "../fpdf.php";
	require_once "../phplot.php";

	$lbr = 190;
	
	// Buat Graph dan dimasukkan ke file dulu
	$piepath = '../tmp/data_fakta_pmb_pie_graph.png';
	BuatPieGraph($piepath, $prevtahun, $tahun, $urutan, $gel);
	
	// *** Cetak ***
	$pdf = new FPDF('P', 'mm', 'A4');
	$pdf->SetTitle("LAPORAN SUMBER INFORMASI PMB TAHUN AJARAN $tahun/$nexttahun GELOMBANG ".UbahKeRomawiLimit99($urutan));
	$pdf->AddPage('P');
	
	BuatHeaderLap($prevtahun, $tahun, $pdf);
	
	TampilkanGraph($piepath, 20, 30, 144, 96, $pdf);
	
	TampilkanSumberInformasi(30, 150, $prevtahun, $tahun, $urutan, $gel, $pdf);
	
	$pdf->Output();
}


function BuatHeaderLap($prevtahun, $tahun, $p) {
  global $lbr;
  $t = 3;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, $t, "LAPORAN SUMBER INFORMASI PMB TAHUN AJARAN $prevtahun/$tahun", 0, 0, 'C');
  $p->Ln($t);
}

function TampilkanGraph($piepath, $X, $Y, $width, $height, $p)
{	$p->Image($piepath, $X, $Y, $width, $height);
}

function TampilkanSumberInformasi($X, $Y, $prevtahun, $tahun, $urutan, $gel, $p)
{	$t = 5; $lebar = 15;

	$p->SetXY($X, $Y);
	$p->Cell($lebar, $t, 'NO', 1, 0, 'C');
	$p->Cell(6*$lebar, $t, 'Sumber Informasi', 1, 0, 'L');
	$p->Cell($lebar, $t, 'FG', 1, 0, 'C');
	$p->Cell($lebar, $t, 'NFG', 1, 0, 'C');
	$p->Cell($lebar, $t, 'TOT', 1, 0, 'C');
	$p->Ln($t);
	$p->SetX($X);
	
	$s = "select InfoID, Nama from sumberinfo order by Urutan desc";
	$r = _query($s);
	$count = 0;
	$totfg = 0;
	$totnfg = 0;
	$tottot = 0;
	while($w=_fetch_array($r))
	{	$count++;
		$p->Cell($lebar, $t, $count, 1, 0, 'C');
		$p->Cell(6*$lebar, $t, $w['Nama'], 1, 0, 'L');
		$fg = GetaField("pmb b left outer join pmbperiod p on b.PMBPeriodID=p.PMBPeriodID and b.KodeID=p.KodeID and LEFT(p.PMBPeriodID, 4)='$tahun'", 
							"INSTR(concat(',',b.SumberInfo,','), concat(',',$w[InfoID],','))>0 and (b.TahunLulus='$prevtahun' or b.TahunLulus='$tahun') and b.KodeID", KodeID, 
							"count(b.PMBID)");
		$totfg +=$fg;
		$p->Cell($lebar, $t, $fg, 1, 0, 'C');
		$nfg = GetaField("pmb b left outer join pmbperiod p on b.PMBPeriodID=p.PMBPeriodID and b.KodeID=p.KodeID and LEFT(p.PMBPeriodID, 4)='$tahun'", 
							"INSTR(b.SumberInfo, $w[InfoID])>0 and b.TahunLulus < '$prevtahun' and b.KodeID", KodeID, 
							"count(b.PMBID)");
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

function BuatPieGraph($filetujuan, $prevtahun, $tahun, $urutan, $gel)
{	$fg = GetaField("pmb b left outer join pmbperiod p on b.PMBPeriodID=p.PMBPeriodID and b.KodeID=p.KodeID and LEFT(p.PMBPeriodID, 4)='$tahun'", 
							"(b.TahunLulus='$prevtahun' or b.TahunLulus='$tahun') and b.KodeID", KodeID, 
							"count(b.PMBID)");
	$nfg = GetaField("pmb b left outer join pmbperiod p on b.PMBPeriodID=p.PMBPeriodID and b.KodeID=p.KodeID and LEFT(p.PMBPeriodID, 4)='$tahun'", 
						"b.TahunLulus < '$prevtahun' and b.KodeID", KodeID, 
						"count(b.PMBID)");

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
		
		$plot->SetTitle("Persentase Calon Mahasiswa Fresh / non Fresh Graduate");
		
		foreach ($data as $row)
		  $plot->SetLegend(implode(': ', $row));
		
		$plot->SetIsInline(true);
		$plot->SetOutputFile($filetujuan);
		$plot->DrawGraph();
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
