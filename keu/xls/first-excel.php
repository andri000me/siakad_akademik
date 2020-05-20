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
HeaderingExcel('test.xls');

//membuat workbook
$workbook=new Workbook("-");

//membuat worksheet pertama
$worksheet1= & $workbook->add_worksheet("Sheet 1");
$worksheet1->set_column(1,1,40);
$worksheet1->set_row(1,20);
$worksheet1->write_string(1,1,"Test Generate Excel");
$workbook->close();
?>
