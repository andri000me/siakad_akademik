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
HeaderingExcel('test2.xls');

//membuat workbook
$workbook=new Workbook("-");

//membuat worksheet pertama
$fBold=& $workbook->add_format();
$fBold->set_bold();

$fItalic=& $workbook->add_format();
$fItalic->set_italic();

$fUnderline=& $workbook->add_format();
$fUnderline->set_underline(1);

$fUnderline2=& $workbook->add_format();
$fUnderline2->set_underline(2);

$fBesar=& $workbook->add_format();
$fBesar->set_size(50);

$fUnderlineItalic=& $workbook->add_format();
$fUnderlineItalic->set_underline(1);
$fUnderlineItalic->set_italic();

$fBoldItalic=& $workbook->add_format();
$fBoldItalic->set_bold();
$fBoldItalic->set_italic();

$worksheet1= & $workbook->add_worksheet("Sheet 1");
$worksheet1->set_column(1,1,40);
$worksheet1->set_row(1,20);
$worksheet1->set_row(11,50);
$worksheet1->write_string(1,1,"Test Generate Bold Font Excel",$fBold);
$worksheet1->write_string(3,1,"Test Generate Italic Font Excel",$fItalic);
$worksheet1->write_string(5,1,"Test Generate Underline Font Excel",$fUnderline);
$worksheet1->write_string(7,1,"Test Generate Double Underline Font Excel",$fUnderline2);
$worksheet1->write_string(9,1,"Test Generate Underline Italic Font Excel",$fUnderlineItalic);
$worksheet1->write_string(11,1,"Test Generate Big Font Excel",$fBesar);
$worksheet1->write_string(13,1,"Test Generate Bold Italic Font Excel",$fBoldItalic);
$workbook->close();
?>
