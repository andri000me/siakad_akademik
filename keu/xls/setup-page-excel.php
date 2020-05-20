<?php
//include all support file
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
HeaderingExcel('test4.xls');

//make a workbook
$workbook=new Workbook("-");

//make first worksheet
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
//seting zoom size
//75 is 75% zoom size
$worksheet1->set_zoom(75);

//set portrait page
$worksheet1->set_portrait(75);

//set a4 paper size
$worksheet1->set_paper(9);

//set hide gridlines
$worksheet1->hide_gridlines();

//set print area
$worksheet1->print_area(0,0,15,5);

//set page header
$worksheet1->set_header("header",$margin=2);

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
