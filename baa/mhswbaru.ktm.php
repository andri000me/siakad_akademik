<?php
session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../fpdf.php";

$pmbid = $_REQUEST['pmbid'];

$pmb = getFields('pmb',"PMBID",$pmbid,'*');

$pdf=new FPDF('P','mm','A4');

$pdf->AddPage();

$identitas = GetFields('identitas', 'Kode', KodeID, '*');
$pdf->Image('../img/logo.jpg',10,10,20);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,6,'',0,0);
$pdf->Cell(75,6,'KARTU TANDA MAHASISWA SEMENTARA',0,1,'C');
$pdf->SetFont('Helvetica','B',15);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(20,15,'',0,0);
$pdf->Cell(75,15,$identitas['Nama'],0,1,'C');
$pdf->SetFont('Courier','B',18);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(5,10,'',0,0);
$pdf->Cell(50,10,$pmb['MhswID'],0,1);
$pdf->SetFont('Arial','',6);
$pdf->Cell(55,4,'NOMOR POKOK MAHASISWA',0,1);
$pdf->SetFont('Courier','B',14);
$pdf->Cell(5,10,'',0,0);
$pdf->Cell(50,10,strtoupper($pmb[Nama]),0,1);
$pdf->SetFont('Arial','',6);
$pdf->Cell(55,4,'NAMA',0,1);
$pdf->SetFont('Courier','B',14);
$pdf->Cell(5,10,'',0,0);
$pdf->Cell(50,10,$pmb[ProdiID],0,1);
$pdf->SetFont('Arial','',6);
$pdf->Cell(55,4,'PROGRAM STUDI',0,1);

$ImagePath = GetaField('mhsw', "MhswID='$pmb[MhswID]' and KodeID", KodeID, "Foto");
if(empty($ImagePath))
{	$pdf->SetXY($pdf->getX()+60,$pdf->getY()-40);
	$pdf->Cell(30,40,'FOTO',1,1,'C');
}
else $pdf->Image('../'.$ImagePath, $pdf->getX()+60,$pdf->getY()-40, 30, 40);

$pdf->SetFont('Arial','',7);
$pdf->SetXY(110,10);
$pdf->Cell(90,5,"* Kartu Mahasiswa ini wajib dibawa untuk setiap kegiatan PMB di Kampus",0,1);
$pdf->SetX(110);
$pdf->Cell(90,5,"* Kartu Mahasiswa ini berlaku selama Mahasiswa aktif atau dalam masa",0,1);
$pdf->SetX(110);
$pdf->Cell(90,5,"  berlaku kartu",0,1);
$pdf->SetX(110);
$pdf->Cell(90,5,"* Apabila kartu Mahasiswa ini hilang agar melapor ke Bag. Akademik dan",0,1);
$pdf->SetX(110);
$pdf->Cell(90,5,"  dikenai biaya pembuatan kartu sebesar Rp 100.000,-",0,1);
$pdf->SetX(110);
$pdf->Cell(90,5,"* Barang siapa menemukan kartu ini dimohon untuk mengembalikan ke",0,1);
$pdf->SetX(110);
$pdf->Cell(90,5,"  alamat di bawah ini",0,1);
$pdf->Ln(5);

$penanggung = GetFields('pejabat', "KodeJabatan='PUKET1' and KodeID", KodeID, 'Nama, Jabatan');
$pdf->SetX(112);
$pdf->Cell(55,4, $identitas['Alamat1'],0,0);
$pdf->Cell(40,4, $penanggung['Jabatan'],0,1,'C');
$pdf->SetX(112);
$pdf->Cell(55,4, $identitas['Kota'].' '.$identitas['KodePos'],0,1);
$pdf->SetX(112);
$pdf->Cell(55,4,"Telp. $identitas[Telepon]",0,1);
$pdf->SetX(112);
$pdf->Cell(55,4,"FAX $identitas[Fax]",0,1);
$pdf->SetX(112);
$pdf->Cell(55,4,"Email : $identitas[Email]",0,0);
$pdf->Cell(40,4, $penanggung['Nama'],0,1,'C');
$pdf->SetX(112);
$pdf->Cell(55,4,"$identitas[Website]",0,1);

//buat border
$pdf->SetXY(5,5);
$pdf->Cell(100,75,'',1,0,'C');
$pdf->Cell(100,75,'',1,1,'C');

$pdf->Output();
?>
