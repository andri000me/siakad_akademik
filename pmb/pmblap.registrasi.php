<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
$gel = $_REQUEST['gel'];
$gels = GetFields('pmbperiod', "KodeID='".KodeID."' and PMBPeriodID", $gel, "*");
$tahun = GetaField('pmbperiod', "PMBPeriodID='$gel' and KodeID", KodeID, 'Tahun');
$prevtahun = $tahun-1;
$nexttahun = $tahun+1;

$lbr = 190;

// *** Cetak ***

$s = "select DISTINCT(b.PMBID) 
	  from statusaplikanmhsw sam left outer join aplikan a on sam.AplikanID=a.AplikanID
				 left outer join pmb b on a.PMBID=b.PMBID 
				 left outer join pmbperiod pp on b.PMBPeriodID=pp.PMBPeriodID
	  where b.KodeID = '".KodeID."'
			and a.StatusAplikanID='REG'
			and pp.Tahun='$tahun'";
$r = _query($s);
$n = _num_rows($r);

if($n > 0)
{	$pdf = new FPDF('L', 'mm', 'A4');
	$pdf->SetTitle("Data Registrasi");

	$limitperpage = 54;
	$currentpage = 0;
	
	while($currentpage*$limitperpage < $n)
	{	$pdf->AddPage('L');
		BuatHeaderLap($gel, $gels, $tahun, $nexttahun, $pdf);
		TampilkanIsinya($currentpage, $limitperpage, $gel, $gels, $tahun, $nexttahun, $pdf);
		$currentpage++;
	}
	$pdf->Output();
}
else
{	echo "<div align=center><font size=8><b>Tidak ada data yang dapat dicetak</b></font></div>";
}



// *** Functions ***
function TampilkanHeader($p) {
  $t = 4;
  $p->SetFont('Helvetica', 'B', 6);
  $p->Cell(7, $t, 'NO', 1, 0, 'C');
  $p->Cell(20, $t, 'NPM', 1, 0, 'C');
  $p->Cell(50, $t, 'NAMA', 1, 0, 'C');
  $p->Cell(20, $t, 'KELAS', 1, 0, 'C');
  $p->Cell(70, $t, 'ASAL SMA/SMK', 1, 0, 'C');
  $p->Cell(12, $t, 'LULUS', 1, 0, 'C');
  $p->Cell(10, $t, 'GEL', 1, 0, 'C');
  $p->Cell(10, $t, 'KODE', 1, 0, 'C');
  $p->Cell(70, $t, 'SUMBER INFORMASI', 1, 0, 'C');
  $p->Cell(10, $t, 'PRES.', 1, 1, 'C');
}
function TampilkanIsinya($page, $limit, $gel, $gels, $tahun, $nexttahun, $p) {
  $pagelimit = $page*$limit;
  $s = "select DISTINCT(a.AplikanID), m.MhswID, a.Nama, k.Nama as NamaKelas, 
	  if(aa.Nama like '_%', aa.Nama, 
			if(pt.Nama like '_%', pt.Nama, b.AsalSekolah)) as NamaSekolah,
	  b.TahunLulus, pp.Urutan, a.SumberInformasi, a.PresenterID,
	  a.StatusMundur as Mundur
	  from 
		statusaplikanmhsw sam left outer join aplikan a on sam.AplikanID=a.AplikanID
				 left outer join pmb b on a.PMBID=b.PMBID 
				 left outer join asalsekolah aa on b.AsalSekolah=aa.SekolahID
				 left outer join perguruantinggi pt on b.AsalSekolah=pt.PerguruanTinggiID
				 left outer join mhsw m on b.PMBID=m.PMBID
				 left outer join kelas k on k.KelasID=m.KelasID
				 left outer join pmbperiod pp on a.PMBPeriodID=pp.PMBPeriodID
	  where b.KodeID = '".KodeID."'
			and sam.StatusAplikanID='REG'
			and pp.Tahun='$tahun'
	  order by a.PresenterID, m.MhswID
	  limit $pagelimit, $limit";

	$r = _query($s);
  $n = 0; $t = 3;

  TampilkanHeader($p);
  while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 6);
    $p->Cell(7, $t, $n, 'LB', 0, 'C');
	$p->Cell(20, $t, $w['MhswID'], 1, 0, 'C');
	$p->SetFillColor(255, 255, 255);
	if($w['Mundur'] == 'N') $p->SetFillColor(255, 0, 0);
	$p->Cell(50, $t, $w['Nama'], 1, 0, 'L', true);
	$p->Cell(20, $t, $w['NamaKelas'], 1, 0, 'C');
	$p->Cell(70, $t, $w['NamaSekolah'], 1, 0, 'C');
	$p->Cell(12, $t, $w['TahunLulus'], 1, 0, 'C');
	$p->Cell(10, $t, $w['Urutan'], 1, 0, 'C');
	$p->Cell(10, $t, $w['SumberInformasi'], 1, 0, 'C');
	$arrSumberInfo = explode(',', $w['SumberInformasi']);
	$NamaSumberInfo = GetaField('sumberinfo', 'InfoID', $arrSumberInfo[0], 'Nama');
	$p->Cell(70, $t, $NamaSumberInfo, 1, 0, 'L');
	$p->Cell(10, $t, $w['PresenterID'], 1, 0, 'C');
    $p->Ln($t);
  }
}
function BuatHeaderLap($gel, $gels, $tahun, $nexttahun, $p) {
$name = GetaField('identitas', 'Kode',KodeID,'Nama');

  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(280, 4, "DATA REGISTRASI ".$tahun."-".$nexttahun, 0, 1, 'C');
  $p->Cell(280, 4, "$name", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(280, 4, "Sort by Presenter", 0, 1, 'C');
}

?>
