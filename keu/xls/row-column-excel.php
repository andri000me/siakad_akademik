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
HeaderingExcel('test3.xls');

//membuat workbook
$workbook=new Workbook("-");

//membuat worksheet pertama
$fJudul=& $workbook->add_format();
$fJudul->set_bold();
$fJudul->set_pattern(1);
$fJudul->set_fg_color($color='red');

$fBesar=& $workbook->add_format();
$fBesar->set_size(20);

$fKutipan=& $workbook->add_format();
$fKutipan->set_align('right');
$fKutipan->set_italic();
$fKutipan->set_size(8);
$fKutipan->set_color('blue');

$fList=& $workbook->add_format();
$fList->set_border(1);


$worksheet1= & $workbook->add_worksheet("Sheet 1");
$worksheet1->set_column(1,1,40);
$worksheet1->set_row(1,20);
$worksheet1->set_row(2,50);
$worksheet1->merge_cells(2,1,2,5);

$worksheet1->write_string(1,1,"Learning Excel",$fJudul);
$worksheet1->write_string(2,1,"This is big",$fBesar);
$worksheet1->write_string(3,1,"\"This is note\"",$fKutipan);
$worksheet1->write_string(5,1,"First Row",$fList);
$worksheet1->write_string(6,1,"Second Row",$fList);
$worksheet1->write_string(7,1,"Third Row",$fList);
$workbook->close();
?>
