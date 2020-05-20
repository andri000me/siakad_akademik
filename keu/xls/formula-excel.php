<?php
//menyertakan semua class yang diperlukan
require_once('OLEwriter.php');
require_once('BIFFwriter.php');
require_once('Worksheet.php');
require_once('Workbook.php');

function HeaderingExcel($filename){
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=$filename");
    header("Expires:0");
    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
}

//http headers
HeaderingExcel('test5.xls');

//make a workbook
$workbook=new Workbook("-");

//make first worksheet
$worksheet1= & $workbook->add_worksheet("Sheet 1");

//add calculation
$worksheet1->write_number(1,1,3);
$worksheet1->write_number(1,2,2);
$worksheet1->write_formula(1,3,"= B2+C2");

//multiply calculation
$worksheet1->write_number(2,1,3);
$worksheet1->write_number(2,2,2);
$worksheet1->write_formula(2,3,"= B3*C3");

//div calculation
$worksheet1->write_number(3,1,3);
$worksheet1->write_number(3,2,2);
$worksheet1->write_formula(3,3,"= B4/C4");

//average calculation
$worksheet1->write_number(4,1,3);
$worksheet1->write_number(4,2,2);
$worksheet1->write_formula(4,3,"= AVERAGE(B5:C5)");

//min value
$worksheet1->write_number(5,1,3);
$worksheet1->write_number(5,2,2);
$worksheet1->write_formula(5,3,"= MIN(B6:C6)");

//max value
$worksheet1->write_number(6,1,3);
$worksheet1->write_number(6,2,2);
$worksheet1->write_formula(6,3,"= MAX(B7:C7)");


$workbook->close();
?>
