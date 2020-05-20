<?php
session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../fpdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Init PDF
$pdf = new FPDF();
$pdf->SetTitle("Daftar Dosen Yang Belum Entry Nilai UAS");
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFillColor(200, 200, 200);


$pdf->AddPage();
$tahunstring = (empty($TahunID))? '' : "Tahun $TahunID";
HeaderLogo("Dosen yang Belum Entry UAS $tahunstring", $pdf, 'P');
BuatHeaderTable($TahunID, $ProdiID, $pdf);
$lbr = 190;

BuatIsinya($TahunID, $ProdiID, $pdf);

$pdf->Output();

// *** Functions ***
function BuatIsinya($TahunID, $ProdiID, $p) {
  $whr_prodi = (empty($ProdiID))? '' : "and j.ProdiID = '$ProdiID' ";
  $whr_tahun = (empty($TahunID))? '' : "and j.TahunID = '$TahunID'";
  $s = "select j.JadwalID, j.MKKode, j.Nama, j.JumlahMhsw, d.Login, p.Nama as Program,
        d.Nama as DSN
    from jadwal j 
      left outer join dosen d on j.DosenID=d.Login 
	  left outer join program p on p.ProgramID=j.ProgramID
	  left outer join mk m on m.MKID = j.MKID
    where j.KodeID='".KodeID."'
		and (m.TugasAkhir='N' AND m.PraktekKerja='N')
	  $whr_tahun
    order by d.Nama";
  $r = _query($s); $n = 0;
  $t = 5; $ttl = 0; $_dosenid = 'alskdjfalsdhguairgsofjhjg9e8rgjpsofjg';
  
  while ($w = _fetch_array($r)) {
  if($_dosenid != $w['Login'])
	{	$_dosenid = $w['Login'];}
	
	$BelumIsi = GetaField('krs', "JadwalID='$w[JadwalID]' and NilaiAkhir = 0 and KodeID", KodeID,  "count(KRSID)");
	$bagidua = $w['JumlahMhsw']/2;
	if ($BelumIsi > $bagidua && $bagidua > 0){
	$n++;
    $ttlbelumisi += $BelumIsi;
	$ttljmlhmhsw += $w['JumlahMhsw'];
    $p->SetFont('Helvetica', '', 7);
    $p->Cell(10, $t, $n, 'LB', 0);
    $p->Cell(45, $t, $w['DSN'], 'B', 0);
    $p->Cell(25, $t, $w['MKKode'], 'B', 0);
	$p->Cell(10, $t, $w['Program'], 'B', 0);
    $p->Cell(60, $t, $w['Nama'], 'B', 0);
	$p->Cell(10, $t, $BelumIsi, 'B', 0, 'C');
	$p->Cell(10, $t, $w['JumlahMhsw'], 'B', 0, 'C');
	$Persentase = ($w['JumlahMhsw'] <= 0)? 0 : ($BelumIsi/$w['JumlahMhsw']*100);
	$p->Cell(20, $t, number_format($Persentase, 2).' %', 1, 0, 'R', ($Persentase > 0)? true : false) ;
    $p->Ln($t); 
	}
  }
  $_ttl = number_format($ttl+0);
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, 1,  ' ', 1, 1);
  $p->Cell(130, $t, 'TOTAL :', 0, 0, 'R');
  $p->Cell(20, $t, ($n <= 0)? 0.00 : number_format($ttlbelumisi/$n, 2).' %', 0, 0, 'R');
  $p->Cell(20, $t, ($n <= 0)? 0.00 : number_format($ttljmlhmhsw/$n, 2).' %', 0, 0, 'R');
  $p->Cell(20, $t, ($ttljmlhmhsw <= 0)? 0.00 : number_format($ttlbelumisi/$ttljmlhmhsw*100, 2).' %', 0, 0, 'R'); 
  $p->Ln($t+2);
}
function BuatHeadertable($TahunID, $ProdiID, $p) {
  global $lbr;
  $t = 5;
  $p->SetFont('Helvetica', 'BI', 8);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(45, $t, 'Dosen', 1, 0);
  $p->Cell(25, $t, 'Kode MK', 1, 0);
  $p->Cell(10, $t, 'Prog', 1, 0);
  $p->Cell(60, $t, 'Nama', 1, 0);
  $p->Cell(10, $t, 'Belum', 1, 0, 'C');
  $p->Cell(10, $t, 'Mhsw', 1, 0, 'C');
  $p->Cell(20, $t, 'Persen', 1, 0, 'R');
  $p->Ln($t);
}

function HeaderLogo($jdl, $p, $orientation='P')
{	$pjg = 110;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 12, 8, 18);
	$p->SetY(5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 5, $identitas['Yayasan'], 0, 1, 'C');
    $p->SetFont("Helvetica", 'B', 10);
    $p->Cell($pjg, 7, $identitas['Nama'], 0, 0, 'C');
    
	//Judul
	if($orientation == 'L')
	{
		$p->SetFont("Helvetica", 'B', 16);
		$p->Cell(20, 7, '', 0, 0);
		$p->Cell($pjg, 7, $jdl, 0, 1, 'C');
	}
	else
	{	$p->SetFont("Helvetica", 'B', 12);
		$p->Cell(80, 7, $jdl, 0, 1, 'R');
	}
	
    $p->SetFont("Helvetica", 'I', 6);
	$p->Cell($pjg, 3,
      $identitas['Alamat1'], 0, 1, 'C');
    $p->Cell($pjg, 3,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'], 0, 1, 'C');
    $p->Ln(3);
	if($orientation == 'L') $length = 275;
	else $length = 190;
    $p->Cell($length, 0, '', 1, 1);
    $p->Ln(2);
}

?>
