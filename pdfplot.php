<?php
require('mem_image.php');
require('pmb/phplot/phplot.php');

$graph = new PHPlot(500,250);
$graph->SetDataType('linear-linear');

//Specify some data
$data = array(
    array('', 2000,  750),
    array('', 2010, 1700),
    array('', 2015, 2000),
    array('', 2020, 1800),
    array('', 2025, 1300),
    array('', 2030,  400)
);
$graph->SetDataValues($data);

//Specify plotting area details
$graph->SetPlotType('lines');
$graph->SetTitleFontSize('2');
$graph->SetTitle('Social Security trust fund asset estimates, in $ billions');
$graph->SetPlotAreaWorld(2000,0,2035,2000);
$graph->SetPlotBgColor('white');
$graph->SetPlotBorderType('left');
$graph->SetBackgroundColor('white');
$graph->SetDataColors(array('red'),array('black'));

//Define the X axis
$graph->SetXLabel('Year');
$graph->SetHorizTickIncrement('5');
$graph->SetXGridLabelType('default');

//Define the Y axis
$graph->SetVertTickIncrement('500');
$graph->SetPrecisionY('0');
$graph->SetYGridLabelType('right');
$graph->SetLightGridColor('blue');

//Disable image output
$graph->SetPrintImage(0);
//Draw the graph
$graph->DrawGraph();

//$pdf = new MEM_IMAGE();
//$pdf->AddPage();
//$pdf->GDImage($graph->PrintImage(),30,20,140);
//$pdf->Output();
?>
