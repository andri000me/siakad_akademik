<?php

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";

// *** Parameters ***
$Fee = GetSetVar('Fee');
$gel = $_REQUEST['gel'];

$tahun = substr($gel, 0, 4);
$prevtahun = $tahun-1;

$gos = (empty($_REQUEST['gos']))? 'ParameterSetup' : $_REQUEST['gos'];
$gos($prevtahun, $tahun, $gel);

function ParameterSetup($prevtahun, $tahun, $gel)
{
	CheckFormScript("Fee");
	echo "<p><table class=bsc cellspacing=1 width=400 border=1 align=center>
			<form action='../$_SESSION[mnux].ratiopresenter.php' method=POST onSubmit=\"return CheckForm(this)\">
			<input type=hidden name='gos' value='CetakLaporan' />
			<input type=hidden name='gel' value='$gel' />
			
			<tr><td class=inp align=right width=50%>Fee Presenter tahun <b>$tahun</b> : </td>
				<td class=ul1>Rp <input type=text name='Fee' value='$_SESSION[Fee]' size=5 maxlength=11 \>,-</td>
			</tr>
				<tr><td class=ul1 colspan=2 align=center>
				<input type=submit name='Simpan' value='Buat Laporan' />
				<input type=button name='Batal' value='Batal' onClick='window.close()' />
			</td></tr>
		  </form></table></p>";
}

function CetakLaporan($prevtahun, $tahun, $gel)
{	include_once "../fpdf.php";

	$lbr = 190;
	
	$pdf = new FPDF('P', 'mm', 'A4');
	$pdf->SetTitle("Ratio Presenter PMB - $prevtahun/$tahun");
	$pdf->AddPage('P');
	
	$arrStatusAplikan = array(); 
	$s = "select * from statusaplikan where KodeID='".KodeID."' order by Urutan ASC";
	$r = _query($s);
	while($w = _fetch_array($r)) $arrStatusAplikan[] = $w['StatusAplikanID'];
	
	// Buat Table 1
	BuatHeaderLap($prevtahun, $tahun, 0, $pdf);
	$arrPresenterID = array();
	GetArrayPresenterSortByRatio($arrPresenterID, $tahun);
	TampilkanIsinya($prevtahun, $tahun, $arrStatusAplikan, $arrPresenterID, 0, $pdf );
	
	$pdf->Ln(10);
	BuatHeaderLap($prevtahun, $tahun, 1, $pdf);
	$arrPresenterID = array();
	GetArrayPresenterSortByReg($arrPresenterID, $tahun);
	$arrStat = TampilkanIsinya($prevtahun, $tahun, $arrStatusAplikan, $arrPresenterID, 1, $pdf);
	
	$pdf->Ln(10);
	TampilkanStatistik($arrStat, $tahun, $pdf);
	
	$pdf->Output();
}

// *** Functions ***
function TampilkanStatistik($arrStat, $tahun, $p)
{	$t = 5; $inp = 60; $ul1 = 40;
	$p->SetFont('Helvetica', '', 8);
	$p->Cell($inp, $t, "% Ratio Presenter dari Aplikan s/d daftar :", 0, 0, 'R');
	$p->SetFont('Helvetica', 'B', 8);
	$persen = ($arrStat['APL'] == 0)? 0 : floor(($arrStat['DFT']/$arrStat['APL'])*100);
	$p->Cell($ul1, $t, $persen.'%', 0, 1, 'L');
	$p->SetFont('Helvetica', '', 8);
	$p->Cell($inp, $t, "% Ratio Presenter dari Aplikan s/d registrasi :", 0, 0, 'R');
	$p->SetFont('Helvetica', 'B', 8);
	$persen = ($arrStat['APL'] == 0)? 0 : floor(($arrStat['REG']/$arrStat['APL'])*100);
	$p->Cell($ul1, $t, $persen.'%', 0, 1, 'L');
	$p->SetFont('Helvetica', '', 8);
	$p->Cell($inp, $t, "% Rata-rata Ratio Presenter :", 0, 0, 'R');
	$p->SetFont('Helvetica', 'B', 8);
	$perbandingan = ($arrStat['REG'] == 0)? 0 : number_format($arrStat['APL']/$arrStat['REG'],2, ",", ".");
	$p->Cell($ul1, $t, "1     :     ".$perbandingan, 0, 1, 'L');
	$p->SetFont('Helvetica', '', 8);
	$p->Cell($inp, $t, "Total Aplikan :", 0, 0, 'R');
	$p->SetFont('Helvetica', 'B', 8);
	$p->Cell($ul1, $t, $arrStat['APL'], 0, 1, 'L');
	$p->SetFont('Helvetica', '', 8);
	$p->Cell($inp, $t, "Total Daftar :", 0, 0, 'R');
	$p->SetFont('Helvetica', 'B', 8);
	$p->Cell($ul1, $t, $arrStat['DFT'], 0, 1, 'L');
	$p->SetFont('Helvetica', '', 8);
	$p->Cell($inp, $t, "Total Registrasi :", 0, 0, 'R');
	$p->SetFont('Helvetica', 'B', 8);
	$p->Cell($ul1, $t, $arrStat['REG'], 0, 1, 'L');
	$p->SetFont('Helvetica', '', 8);
	$p->Cell($inp, $t, "Fee Per Presenter :", 0, 0, 'R');
	$p->SetFont('Helvetica', 'B', 8);
	$p->Cell($ul1, $t, "Rp ".number_format($_SESSION['Fee'], 0, ",", "."), 0, 1, 'L');
}

function BuatHeaderLap($prevtahun, $tahun, $jenistable, $p) {
  global $lbr;
  $t = 5;
  $p->SetFont('Helvetica', 'B', 11);
  $p->Cell($lbr, $t, "Ratio Presenter PMB - $prevtahun/$tahun", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 8);
  if($jenistable== 0) $p->Cell($lbr, $t, "Sort by Ratio Presenter", 0, 1, 'C');
  else if($jenistable==1) $p->Cell($lbr, $t, "Sort by Jumlah Mahasiswa yang Registrasi", 0, 1, 'C');
  
  $p->Ln(4);
}
function TampilkanIsinya($prevtahun, $tahun, $arrStatusAplikan, $arrPresenterID, $jenistable, $p) {
  $t = 5; $lebar = 8;
  BuatHeaderTabel($arrStatusAplikan, $lebar, $prevtahun, $tahun, $jenistable, $p);
  
  $arrCurPeriod=array();
  $arrTotal=array();
  for ($i = 0; $i < sizeof($arrPresenterID); $i++) {
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(8, $t, $arrPresenterID[$i], 'B', 0);
    $p->Cell($lebar, $t, '', 'B', 0);
	BuatEntry($arrPresenterID[$i], $arrStatusAplikan, $t, $lebar, $arrCurPeriod, $arrTotal, $prevtahun, $tahun, $jenistable, $p);
    $p->Ln($t);
  }
  BuatTotal($arrStatusAplikan, $arrTotal, $lebar, $jenistable, $p);
  
  return $arrTotal;
}
function BuatTotal($arrStatusAplikan, $arrTotal, $lebar, $jenistable, $p) {
  $p->Ln(1);
  $t = 6;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(18, $t, '', 'T', 0);
  foreach ($arrStatusAplikan as $per) {
    $p->Cell($lebar, $t, $arrTotal[$per], 'T', 0, 'C');
	$p->Cell(8, $t, '', 'T', 0);
  }
  $p->Cell(4*$lebar+14, $t, '', 'T', 0);
  if($jenistable == 1)
  {	$feetotal = number_format($arrTotal['REG']*$_SESSION['Fee'], 0, ",", ".");
	$p->Cell(2, $t, 'Rp', 'T', 0, 'L');
	$p->Cell(3*$lebar-2, $t, $feetotal, 'T', 0, 'R');
  }
  
  $p->Ln($t);
}
function BuatEntry($PresenterID, $arrStatusAplikan, $t, $lebar, &$arrCurPeriod, &$arrTotal, $prevtahun, $tahun, $jenistable, $p) {
  $arrTempCur = array();
  FillArrayCurPeriod($PresenterID, $arrTempCur, $arrTotal, $arrStatusAplikan, $tahun);
  $p->SetFont('Helvetica', '', 8);
  foreach ($arrStatusAplikan as $stat) {
	$p->Cell($lebar, $t, $arrTempCur[$stat], 'B', 0, 'R');
	$p->Cell(8, $t, '', 'B', 0);
  }
  $persen = ($arrTempCur['APL'] == 0)? 0 : floor(($arrTempCur['REG']/$arrTempCur['APL'])*100);
  $_persen = $persen."%";
  $p->Cell($lebar, $t, $_persen, 'B', 0, 'R');
  $p->Cell(8, $t, "", 'B', 0);
  $perbandingan = ($arrTempCur['REG'] == 0)? 0 : number_format($arrTempCur['APL']/$arrTempCur['REG'], 2, ",", ".");
  $_perbandingan = "1     :     ".$perbandingan;
  $p->Cell(3*$lebar, $t, $_perbandingan, 'B', 0, 'C');
  $p->Cell(8, $t, '', 'B', 0);
  if($jenistable == 1)
  {	$fee = $arrTempCur['REG']*$_SESSION['Fee'];
	$fee = ($fee <= 0)? '-' : number_format($fee, 0, ",", ".");
	$p->Cell(2, $t, 'Rp', 'B', 0, 'L');
	$p->Cell(3*$lebar-2, $t, $fee, 'B', 0, 'R');
  }
}
function BuatHeaderTabel($arrStatusAplikan, $lebar, $prevtahun, $tahun, $jenistable, $p ) {
  $t = 5;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(8, $t, "Pres.", 'BT', 0);
  $p->Cell(8, $t, "", 'BT', 0);
  foreach ($arrStatusAplikan as $stat) {
	$p->Cell($lebar, $t, $stat, 'BT', 0, 'C');
    $p->Cell(8, $t, "", 'BT', 0);
  }
  $p->Cell($lebar, $t, '%', 'BT', 0, 'C');
  $p->Cell(8, $t, "", 'BT', 0);
  $p->Cell(3*$lebar, $t, 'Perbandingan', 'BT', 0, 'C');
  $p->Cell(8, $t, "", 'BT', 0);
  if($jenistable == 1)
  {	 $p->Cell(3*$lebar, $t, 'Jml. Fee Presenter', 'BT', 0, 'C');
  }
  $p->Ln($t);
}
function GetArrayPresenterSortByRatio(&$arrPresenterID, $tahun) {
  $s = "select p.PresenterID, p.Nama, 
		(count(a.StatusAplikanID)/(select count(a2.StatusAplikanID) 
								from presenter p2 left outer join aplikan a2 on p2.PresenterID=a2.PresenterID
												  left outer join pmbperiod pp2 on a2.PMBPeriodID=pp2.PMBPeriodID and pp2.Tahun='$tahun'
								where p2.KodeID = '".KodeID."' and p2.NA = 'N' and p2.PresenterID=p.PresenterID
								group by p2.PresenterID)) as _countPercentage
		from presenter p left outer join aplikan a on p.PresenterID=a.PresenterID and a.StatusAplikanID='REG'
						 left outer join pmbperiod pp on a.PMBPeriodID=pp.PMBPeriodID and pp.Tahun='$tahun'
		where p.KodeID = '".KodeID."'
		and p.NA = 'N'
		group by p.PresenterID
		order by _countPercentage DESC, p.PresenterID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arrPresenterID[] = $w['PresenterID'];
  }
}
function GetArrayPresenterSortByReg(&$arrPresenterID, $tahun) {
  $s = "select p.PresenterID, p.Nama, 
		count(a.StatusAplikanID) as _countREG
    from presenter p left outer join aplikan a on p.PresenterID=a.PresenterID and a.StatusAplikanID='REG'
					 left outer join pmbperiod pp on a.PMBPeriodID=pp.PMBPeriodID and pp.Tahun = '$tahun'
	where p.KodeID = '".KodeID."'
    and p.NA = 'N'
	group by p.PresenterID
    order by _countREG DESC, p.PresenterID";
  $r = _query($s);
  $arrPresenterID = array();
  $arrPresenter = array();
  while ($w = _fetch_array($r)) {
    $arrPresenterID[] = $w['PresenterID'];
    $arrPresenter[] = $w['Nama'];
  }
}
function FillArrayCurPeriod($PresenterID, &$arrTempCur, &$arrTotal, $arrStatusAplikan, $tahun)
{	
	foreach ($arrStatusAplikan as $stat) {
		$jml = GetaField("statusaplikanmhsw sam left outer join aplikan a on sam.AplikanID=a.AplikanID
											    left outer join pmbperiod p on a.PMBPeriodID=p.PMBPeriodID and p.KodeID='".KodeID."'",
		  "a.PresenterID='$PresenterID' and p.Tahun='$tahun' and sam.StatusAplikanID='$stat' and sam.KodeID", KodeID,
		  "count(sam.StatusAplikanMhswID)")+0;
		//echo "$PresenterID - $tahun -  $stat : $jml<br>";
		$arrTempCur[$stat] = $jml;
		$arrTotal[$stat] += $jml;
	}
}

?>
