<?php
// Author : Wisnu
// Email  : -
// Start  : 25 Maret 2009

session_start();

	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	include_once "../header_pdf.php";
	
	include ("../jpgraph/jpgraph.php");
	include ("../jpgraph/jpgraph_line.php");
	include ("../jpgraph/jpgraph_pie.php");
	include ("../jpgraph/jpgraph_pie3d.php");
	
$Tahun1 = getSetVar('Tahun1');
$Tahun2 = getSetVar('Tahun2');

if (empty($Tahun1) || empty($Tahun2))
  die(ErrorMsg("Error",
    "Masukan Periode Tahun dahulu.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));

function LineGraph($w,$h,$title,$data1,$data2,$datax,$output){
	// Create the graph. These two calls are always required
	$graph = new Graph($w,$h,"auto");    
	$graph->SetScale("textlin");
	$graph->SetMarginColor('white');
	$graph->SetFrame(true);
	// Adjust the margin
	$graph->img->SetMargin(40,100,20,40);
	$graph->SetShadow(false);
	
	// Create the linear plot
	
	$lineplot=new LinePlot($data1);
	$lineplot->SetWeight(2);
	$lineplot->SetColor("blue");
	$lineplot->mark->SetType(MARK_DIAMOND);
	$lineplot->mark->SetWidth(5);
	$lineplot->mark->SetFillColor('blue');
	$lineplot->value->SetMargin(-20);
	$lineplot->value->show();
	$lineplot->value->SetColor('blue');
	$lineplot->value->SetFormat('%0.0f');
	$lineplot->SetLegend ($_SESSION[Tahun1]); 
	
	$lineplot2 =new LinePlot($data2);
	$lineplot2->SetColor("green"); 
	$lineplot2->SetWeight(2);  
	$lineplot2->mark->SetType(MARK_FILLEDCIRCLE);
	$lineplot2->mark->SetWidth(3);
	$lineplot2->mark->SetFillColor('green');
	$lineplot2->value->show();
	$lineplot2->value->SetColor('darkgreen');
	$lineplot2->value->SetFormat('%0.0f');
	$lineplot2->SetLegend($_SESSION[Tahun2]);
	
	// Add the plot to the graph
	$graph->Add($lineplot);
	$graph->xaxis->SetTickLabels($datax);
	$graph->title->Set($title);
	$graph->xaxis->title->Set("");
	$graph->yaxis->title->Set("");
	
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
	
	$graph->Add( $lineplot2); 
	$graph->legend->SetShadow(false); 
	$graph->legend->SetFillColor('white'); 
	$graph->legend->SetPos(0.01,0.88,'right','center');
	// Display the graph
	$graph->Stroke($output);
}


function PieChart($w,$h,$title,$data,$dataL,$output){	
	$graph = new PieGraph($w,$h,"auto");
	$graph->SetFrame(false);
	$graph->SetShadow(false);
	$graph->title->Set($title);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	
	$p1 = new PiePlot3D($data);
	$p1->SetAngle(20);
	$p1->SetSize(0.5);
	$p1->SetCenter(0.45);
	$p1->SetLegends($dataL);
	
	$graph->Add($p1);
	$graph->Stroke($output);
}

/// gambar 1 ////
$Judul = "GRAFIK APLIKAN STMIK & AKADEMI BINA INSANI \n DATA TAHUN $_SESSION[Tahun1] DAN $_SESSION[Tahun2]";
$file = "Chart/Aplikan.png";
$s = "select * from pmbperiod where PMBPeriodID like '".$_SESSION[Tahun1]."%' and KodeID = '".KodeID."'";
$q = _query($s);
$datax = array();
$datax[0] = "";
$ydata = array();
$ydata[0] = "-";
$i = 1;
while($x = mysql_fetch_array($q)){
	$ss = getFields("aplikan","KodeID = '".KodeID."' and PMBPeriodID","$x[PMBPeriodID]","count(AplikanID) as _count");
	$datax[$i] = "GEL ".substr($x[PMBPeriodID],4,1);
	$ydata[$i] = $ss[_count];
	$i++;
}

$s = "select * from pmbperiod where PMBPeriodID like '".$_SESSION[Tahun2]."%' and KodeID = '".KodeID."'";
$q = _query($s);
$ydata2 = array();
$ydata2[0] = "-";
$i = 1;
while($x = mysql_fetch_array($q)){
	$ss = getFields("aplikan","KodeID = '".KodeID."' and PMBPeriodID","$x[PMBPeriodID]","count(AplikanID) as _count");
	$ydata2[$i] = $ss[_count];
	$i++;
}

LineGraph(700,250,$Judul,$ydata,$ydata2,$datax,$file);


/// gambar 2 ////
$Judul = "GRAFIK CALON MAHASISWA YANG MENDAFTAR DI STMIK & AKADEMI BINA INSANI \n DATA TAHUN $_SESSION[Tahun1] DAN $_SESSION[Tahun2]";
$file = "Chart/AplikanDft.png";
$s = "select * from pmbperiod where PMBPeriodID like '".$_SESSION[Tahun1]."%' and KodeID = '".KodeID."'";
$q = _query($s);
$datax = array();
$datax[0] = "";
$ydata = array();
$ydata[0] = "-";
$i = 1;
while($x = mysql_fetch_array($q)){
	$ss = getFields("aplikan","KodeID = '".KodeID."'  and (StatusAplikan = 'DFT_Y' or StatusAplikan = 'USM_Y'
			 or StatusAplikan = 'LLS_Y' or StatusAplikan = 'REG_Y') and PMBPeriodID","$x[PMBPeriodID]","count(AplikanID) as _count");
	$datax[$i] = "GEL ".substr($x[PMBPeriodID],4,1);
	$ydata[$i] = $ss[_count];
	$i++;
}

$s = "select * from pmbperiod where PMBPeriodID like '".$_SESSION[Tahun2]."%' and KodeID = '".KodeID."'";
$q = _query($s);
$ydata2 = array();
$ydata2[0] = "-";
$i = 1;
while($x = mysql_fetch_array($q)){
	$ss = getFields("aplikan","KodeID = '".KodeID."'  and (StatusAplikan = 'DFT_Y' or StatusAplikan = 'USM_Y'
			 or StatusAplikan = 'LLS_Y' or StatusAplikan = 'REG_Y') and PMBPeriodID","$x[PMBPeriodID]","count(AplikanID) as _count");
	$ydata2[$i] = $ss[_count];
	$i++;
}

LineGraph(700,250,$Judul,$ydata,$ydata2,$datax,$file);


/// gambar 3 ////
$Judul = "GRAFIK CALON MAHASISWA YANG REGISTRASI DI STMIK & AKADEMI BINA INSANI \n DATA TAHUN $_SESSION[Tahun1] DAN $_SESSION[Tahun2]";
$file = "Chart/AplikanReg.png";
$s = "select * from pmbperiod where PMBPeriodID like '".$_SESSION[Tahun1]."%' and KodeID = '".KodeID."'";
$q = _query($s);
$datax = array();
$datax[0] = "";
$ydata = array();
$ydata[0] = "-";
$i = 1;
while($x = mysql_fetch_array($q)){
	$ss = getFields("aplikan","KodeID = '".KodeID."' and StatusAplikan = 'REG_Y' and PMBPeriodID","$x[PMBPeriodID]","count(AplikanID) as _count");
	$datax[$i] = "GEL ".substr($x[PMBPeriodID],4,1);
	$ydata[$i] = $ss[_count];
	$i++;
}

$s = "select * from pmbperiod where PMBPeriodID like '".$_SESSION[Tahun2]."%' and KodeID = '".KodeID."'";
$q = _query($s);
$ydata2 = array();
$ydata2[0] = "-";
$i = 1;
while($x = mysql_fetch_array($q)){
	$ss = getFields("aplikan","KodeID = '".KodeID."' and StatusAplikan = 'REG_Y' and PMBPeriodID","$x[PMBPeriodID]","count(AplikanID) as _count");
	$ydata2[$i] = $ss[_count];
	$i++;
}

LineGraph(700,250,$Judul,$ydata,$ydata2,$datax,$file);



//// Pie Chart //////
$Judul = "KOMPOSISI CALON MAHASISWA YANG REGISTRASI $_SESSION[Tahun1] / $_SESSION[Tahun2]";
$file = "Chart/AplikanFresh.png";

$Tahun1a = $_SESSION[Tahun1] - 1 ;
$Tahun2a = $_SESSION[Tahun2] - 1 ;

$FG1 = getaField('aplikan',"SumberInformasi like '%".$w[InfoID]."%' and PMBPeriodID like '".$_SESSION[Tahun1]."%' and TahunLulus >= PMBPeriodID-1 and NA",'N','count(AplikanID)');
$NFG1 = getaField('aplikan',"SumberInformasi like '%".$w[InfoID]."%' and PMBPeriodID like '".$_SESSION[Tahun1]."%' and TahunLulus < PMBPeriodID-1 and NA",'N','count(AplikanID)');

$FG2 = getaField('aplikan',"SumberInformasi like '%".$w[InfoID]."%' and PMBPeriodID like '".$_SESSION[Tahun2]."%' and TahunLulus >= PMBPeriodID-1 and NA",'N','count(AplikanID)');
$NFG2 = getaField('aplikan',"SumberInformasi like '%".$w[InfoID]."%' and PMBPeriodID like '".$_SESSION[Tahun2]."%' and TahunLulus < PMBPeriodID-1 and NA",'N','count(AplikanID)');

//$FG1 = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='".$_SESSION[Tahun1]."'", 
//							"INSTR(concat(',',a.SumberInformasi,','), concat(',',$w[InfoID],','))>0 and (a.TahunLulus='$Tahun1a' or a.TahunLulus='".$_SESSION[Tahun1]."') and a.KodeID", KodeID, 
//							"count(a.AplikanID)");
//$FG2 = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='".$_SESSION[Tahun2]."'", 
//							"INSTR(concat(',',a.SumberInformasi,','), concat(',',$w[InfoID],','))>0 and (a.TahunLulus='$Tahun2a' or a.TahunLulus='".$_SESSION[Tahun2]."') and a.KodeID", KodeID, 
//							"count(a.AplikanID)");
//$NFG1 = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='".$_SESSION[Tahun1]."'", 
//							"INSTR(a.SumberInformasi, $w[InfoID])>0 and a.TahunLulus < '$Tahun1a' and a.KodeID", KodeID, 
//							"count(a.AplikanID)");
//$NFG2 = GetaField("aplikan a left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and a.KodeID=p.KodeID and p.Tahun='".$_SESSION[Tahun2]."'", 
//							"INSTR(a.SumberInformasi, $w[InfoID])>0 and a.TahunLulus < '$Tahun2a' and a.KodeID", KodeID, 
//							"count(a.AplikanID)");
						
if (!empty($FG1) || !empty($FG2)){
	$Fresh = $FG1 + $FG2;
}
if (!empty($NFG1) || !empty($NFG2)){
	$NFresh = $NFG1 + $NFG2;
}

$data = array($Fresh,$NFresh);
$dataL = array('Fresh Graduate','Non Fresh Graduate');
if ($Fresh != '' || $NFresh != ''){
PieChart(400,300,$Judul,$data,$dataL,$file);
}



///// CREATE PDF /////
require('fpdf.php');
$pdf=new FPDF('L','mm','A4');

$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Image('Chart/Aplikan.png',10,10,150);
$pdf->Image('Chart/AplikanDft.png',10,75,150);
$pdf->Image('Chart/AplikanReg.png',10,140,150);

$pdf->SetX(180);
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(100,3,"MATRIK KELAS s.d ".date('d/m/Y h:i'),0,1);
$pdf->SetX(180);
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(20,3,"PROGRAM",'TLR',0,'C');
$pdf->Cell(80,3,"WAKTU KULIAH",1,1,'C');
$pdf->SetX(180);
$pdf->Cell(20,3,"STUDI",'BLR',0,'C');
$pdf->Cell(30,3,"PAGI",1,0,'C');
$pdf->Cell(30,3,"MLM",1,0,'C');
$pdf->Cell(20,3,"TOT",1,1,'C');

$s = "select * from prodi where KodeID = '".KodeID."' and NA = 'N'";
$q = _query($s);
$pagiT = 0;
$malamT= 0;
$totalT = 0;
while ($w = _fetch_array($q)){
	$pagi = getaField('mhsw',"KodeID = '".KodeID."' and ProdiID = '".$w[ProdiID]."' and ProgramID","PGI","count(MhswID)");
	$malam = getaField('mhsw',"KodeID = '".KodeID."' and ProdiID = '".$w[ProdiID]."' and ProgramID","MLM","count(MhswID)");
	$total = $pagi + $malam;
	$pdf->SetX(180);
	$pdf->SetFont('Helvetica', '', 6);
	$pdf->Cell(20,3,$w[ProdiID],1,0,'C');
	$pdf->Cell(30,3,$pagi,1,0,'C');
	$pdf->Cell(30,3,$malam,1,0,'C');
	$pdf->Cell(20,3,$total,1,1,'C');
	$pagiT += $pagi;
	$malamT += $malam;
	$totalT += $total; 
}	
	$pdf->SetX(180);
	$pdf->SetFont('Helvetica', '', 6);
	$pdf->Cell(20,3,"TOTAL",1,0,'C');
	$pdf->Cell(30,3,$pagiT,1,0,'C');
	$pdf->Cell(30,3,$malamT,1,0,'C');
	$pdf->Cell(20,3,$totalT,1,1,'C');
	
if ($Fresh != '' || $NFresh != ''){
	$pdf->Image('Chart/AplikanFresh.png',190,50,100);
}
$pdf->Ln(80);

$t = 4;
$pdf->SetX(180);
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(100,5,"DATA ALASAN APLIKAN DATANG KE BINA INSANI",0,1);

$pdf->SetX(180);
$pdf->Cell(10,$t,"NO",1,0,'C');
$pdf->Cell(60,$t,"Sumber Informasi",1,0,'C');
$pdf->Cell(10,$t,"FG",1,0,'C');
$pdf->Cell(10,$t,"NFG",1,0,'C');
$pdf->Cell(10,$t,"TOT",1,1,'C');

$s = "select * from sumberinfo where NA = 'N'";
$q = _query($s);
$n = 0;
$FGT = 0;
$NFGT = 0;
$TOTT = 0;
while ($w = _fetch_array($q)){
	$n++;
	$FG = getaField('aplikan',"SumberInformasi like '%".$w[InfoID]."%' and PMBPeriodID like '".$_SESSION[Tahun2]."%' and TahunLulus >= PMBPeriodID-1 and NA",'N','count(AplikanID)');
	$NFG = getaField('aplikan',"SumberInformasi like '%".$w[InfoID]."%' and PMBPeriodID like '".$_SESSION[Tahun2]."%' and TahunLulus < PMBPeriodID-1 and NA",'N','count(AplikanID)');
	$TOT = $FG + $NFG;
	$pdf->SetX(180);
	$pdf->SetFont('Helvetica', '', 8);
	$pdf->Cell(10,$t,$n,1,0,'C');
	$pdf->Cell(60,$t,$w[Nama],1,0);
	$pdf->Cell(10,$t,$FG,1,0,'C');
	$pdf->Cell(10,$t,$NFG,1,0,'C');
	$pdf->Cell(10,$t,$TOT,1,1,'C');
	$FGT += $FG;
	$NFGT += $NFG;
	$TOTT += $TOT;
}
	$pdf->SetX(180);
	$pdf->SetFont('Helvetica', '', 8);
	$pdf->Cell(10,$t,'',1,0,'C');
	$pdf->Cell(60,$t,"TOTAL",1,0);
	$pdf->Cell(10,$t,$FGT,1,0,'C');
	$pdf->Cell(10,$t,$NFGT,1,0,'C');
	$pdf->Cell(10,$t,$TOTT,1,1,'C');





$pdf->Output();
?>
