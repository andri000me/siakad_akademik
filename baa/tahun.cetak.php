<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 04 Juni 2009

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
$TahunID = $_REQUEST['TahunID'];
$ProdiID = $_REQUEST['ProdiID'];
$ProgramID = $_REQUEST['ProgramID'];
if (empty($TahunID))
  die(ErrorMsg("Error",
    "Tentukan tahun akademik-nya dulu.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));

// *** Changable Parameters ***//
$WarnaKuliah = array(200, 0, 0);
$WarnaKRS = array(0, 200, 0);
$WarnaBayar = array(0, 0, 200);
$WarnaUTS = array(200, 200, 0);
$WarnaUAS = array(0, 200, 200);
$WarnaCuti = array(200, 0 ,200);
$WarnaNilai = array(200, 200, 200);
	  
// *** Main
$thn = NamaTahun($TahunID);
$pdf = new FPDF('P');
$pdf->SetTitle("Kalender Akademik - $thn");
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFillColor(200, 200, 200);

$whr_prd = (empty($ProdiID))? "" : "and t.ProdiID='$ProdiID'";
$whr_prg = (empty($ProgramID))? "" : "and t.ProgramID='$ProgramID'";
$s = "select t.*
    from tahun t
    where t.KodeID = '".KodeID."'
      and t.TahunID = '$TahunID'
	  $whr_prd
	  $whr_prg
    order by t.TahunID";
$r = _query($s);
$n = 0;

while ($w = _fetch_array($r)) 
{	
	$n++;
	$pdf->AddPage('P');
	HeaderLogo("Kalender Akademik - $thn", $pdf, 'P');
	Isinya($w['TahunID'], $w['ProdiID'], $w['ProgramID'], $pdf);
	BarGraphnya($w['TahunID'], $w['ProdiID'], $w['ProgramID'], 85, 30, $pdf);
}
$pdf->Output();

// *** Functions ***
function BarGraphnya($TahunID, $ProdiID, $ProgramID, $X, $Y, $p) {
	global $WarnaKuliah, $WarnaKRS, $WarnaBayar, $WarnaUTS, $WarnaUAS, $WarnaCuti, $WarnaNilai;

	$t = 5;
	$widthPerMonth = 12;
	$heightPerDay = 8;
	$gapPerMonth = 3;
	
	$thn = GetFields('tahun', "TahunID='$TahunID' and ProdiID='$ProdiID' and ProgramID='$ProgramID' and KodeID", KodeID, '*');
	//Buat array yang memuat semua Tanggal yang Penting
	$arrTanggalPenting = array();
	
	PushDatesToArray($thn['TglKuliahMulai'], $thn['TglKuliahSelesai'], $arrTanggalPenting, $WarnaKuliah);
	
    PushDatesToArray($thn['TglKRSMulai'], $thn['TglKRSSelesai'], $arrTanggalPenting, $WarnaKRS);
	
	PushDatesToArray($thn['TglBayarMulai'], $thn['TglBayarSelesai'], $arrTanggalPenting, $WarnaBayar);
	
	PushDatesToArray($thn['TglUTSMulai'], $thn['TglUTSSelesai'], $arrTanggalPenting, $WarnaUTS);
	
	PushDatesToArray($thn['TglUASMulai'], $thn['TglUASSelesai'], $arrTanggalPenting, $WarnaUAS);
	
	$arrTanggalPenting[] = implode('~', array(substr($thn['TglCuti'], 0, 10), $WarnaCuti[0], $WarnaCuti[1], $WarnaCuti[2])); 
    $arrTanggalPenting[] = implode('~', array(substr($thn['TglNilai'], 0, 10), $WarnaNilai[0], $WarnaNilai[1], $WarnaNilai[2]));
	
	asort($arrTanggalPenting);
	$arrTanggalPenting = array_unique($arrTanggalPenting);

	// Buat Bar Graphnya
	$YearMonth = "0000-00";
	$indexOfMonth = 0;
	$maxDay = 0; 
	foreach($arrTanggalPenting as $Tanggal)
	{	$_Tanggal = explode('~', $Tanggal);
		if(substr($_Tanggal[0], 0, 7) != $YearMonth)
		{	if($YearMonth != "0000-00")
			{	$lastDayOfMonth = date('t', strtotime($YearMonth.'-01'));
				while($maxDay <= $lastDayOfMonth)
				{	$p->SetXY($X+($indexOfMonth*($widthPerMonth+$gapPerMonth)), $Y+$maxDay*$heightPerDay);
					$p->Cell($widthPerMonth, $heightPerDay, ($maxDay < 10)? '0'.$maxDay : $maxDay, 1, 0, 'C');
					$maxDay++;
				}
			}
			$indexOfMonth++;
			$p->SetXY($X+($indexOfMonth*($widthPerMonth+$gapPerMonth)), $Y);
			$p->Cell($widthPerMonth, $heightPerDay, UbahKeBulanIndonesiaSingkat(substr($_Tanggal[0], 5, 2)), 1, 0, 'C');
			$YearMonth = substr($_Tanggal[0], 0, 7);
			$maxDay = 1;
		}
		
		while($maxDay < substr($_Tanggal[0], 8, 2)+0)
		{	$p->SetXY($X+($indexOfMonth*($widthPerMonth+$gapPerMonth)), $Y+$maxDay*$heightPerDay);
			$p->Cell($widthPerMonth, $heightPerDay, ($maxDay < 10)? '0'.$maxDay : $maxDay, 1, 0, 'C');
			$maxDay++;
		}
		
		$maxDay = substr($_Tanggal[0], 8, 2)+1;
		$p->SetXY($X+($indexOfMonth*($widthPerMonth+$gapPerMonth)), $Y+(substr($_Tanggal[0], 8, 2)+0)*$heightPerDay);
		$p->SetFillColor($_Tanggal[1], $_Tanggal[2], $_Tanggal[3]); 
		$p->Cell($widthPerMonth, $heightPerDay, substr($_Tanggal[0], 8, 2), 1, 0, 'C', true); 
		$p->SetFillColor(255, 255, 255); 
	}
	
	$lastDayOfMonth = date('t', strtotime($YearMonth.'-01'));
	while($maxDay <= $lastDayOfMonth)
	{	$p->SetXY($X+($indexOfMonth*($widthPerMonth+$gapPerMonth)), $Y+$maxDay*$heightPerDay);
		$p->Cell($widthPerMonth, $heightPerDay, ($maxDay < 10)? '0'.$maxDay : $maxDay, 1, 0, 'C');
		$maxDay++;
	}
}

function PushDatesToArray($start, $end, &$arrTanggalPenting, $Warna)
{	$arrTanggalPenting[] = implode('~', array(date('Y-m-d', strtotime($start)), $Warna[0], $Warna[1], $Warna[2]));
	$arrTanggalPenting[] = implode('~', array(date('Y-m-d', strtotime($end)), $Warna[0], $Warna[1], $Warna[2]));
}
/*{ $dateFrom=strtotime($start);
  $dateTo=strtotime($end);
  
  $arrTanggalPenting[] = date('Y-m-d', $dateFrom); // Masukkan Tanggal Walau Tanggal Akhir mungkin tidak valid.
  
  if ($dateFrom < $dateTo) {
    while ($dateFrom<$dateTo) {
      $dateFrom+=24*60*60; 
      $arrTanggalPenting[] = date('Y-m-d', $dateFrom);
    }
  }
}*/

function BarGraphnya2($TahunID, $ProdiID, $ProgramID, $p) {
	$t = 5;
	
	$thn = GetFields('tahun', "TahunID='$TahunID' and ProdiID='$ProdiID' and ProgramID='$ProgramID' and KodeID", KodeID, '*');
	
	$TanggalAwal = "0000-00-00";
	$TanggalAkhir = "0000-00-00";
	
	$TanggalAwal = ($TanggalAwal > $thn['TglKuliahMulai'])? $thn['TglKuliahMulai'] : $TanggalAwal;
	$TanggalAwal = ($TanggalAwal > $thn['TglKRSMulai'])? $thn['TglKRSMulai'] : $TanggalAwal;
	$TanggalAwal = ($TanggalAwal > $thn['TglBayarMulai'])? $thn['TglBayarMulai'] : $TanggalAwal;
	$TanggalAwal = ($TanggalAwal > $thn['TglUTSMulai'])? $thn['TglUTSMulai'] : $TanggalAwal;
	$TanggalAwal = ($TanggalAwal > $thn['TglUASMulai'])? $thn['TglUASMulai'] : $TanggalAwal;
	$TanggalAwal = ($TanggalAwal > $thn['TglCuti'])? $thn['TglCuti'] : $TanggalAwal;
	$TanggalAwal = ($TanggalAwal > $thn['TglNilai'])? $thn['TglNilai'] : $TanggalAwal;
 
	$TanggalAkhir = ($TanggalAkhir < $thn['TglKuliahSelesai'])? $thn['TglKuliahSelesai'] : $TanggalAkhir;
	$TanggalAkhir = ($TanggalAkhir < $thn['TglKRSSelesai'])? $thn['TglKRSSelesai'] : $TanggalAkhir;
	$TanggalAkhir = ($TanggalAkhir < $thn['TglBayarSelesai'])? $thn['TglBayarSelesai'] : $TanggalAkhir;
	$TanggalAkhir = ($TanggalAkhir < $thn['TglUTSSelesai'])? $thn['TglUTSSelesai'] : $TanggalAkhir;
	$TanggalAkhir = ($TanggalAkhir < $thn['TglUASSelesai'])? $thn['TglUASSelesai'] : $TanggalAkhir;
	$TanggalAkhir = ($TanggalAkhir < $thn['TglCuti'])? $thn['TglCuti'] : $TanggalAkhir;
	$TanggalAkhir = ($TanggalAkhir < $thn['TglNilai'])? $thn['TglNilai'] : $TanggalAkhir;
    
	$width = 275/($TanggalAkhir - $TanggalAwal);
	
}
function Isinya($TahunID, $ProdiID, $ProgramID, $p) {
  global $WarnaKuliah, $WarnaKRS, $WarnaBayar, $WarnaUTS, $WarnaUAS, $WarnaCuti, $WarnaNilai;
  $lbr = 190; $t = 5;
  $thn = GetFields('tahun', "TahunID='$TahunID' and ProdiID='$ProdiID' and ProgramID='$ProgramID' and KodeID", KodeID, '*');
  
  $kolom1 = 60;
  $kolomkosong = 10;
  $kolomperpanjangan = 20;
  $kolom2 = 50;
  $jeda = 4;
  $indent = 'L';
  
  $p->Ln($jeda);
  
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell($kolom1, $t, 'Awal Semester', 'BT', 0, $indent);
  $p->Cell($kolomperpanjangan, $t, '', 'BT', 1, $indent);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->SetFillColor($WarnaKRS[0], $WarnaKRS[1], $WarnaKRS[2]);
  $p->Cell($t, $t, '', 1, 0, '', true);
  $p->Cell($kolom1, $t, 'Tanggal Pengambilan KRS :', 0, 1, $indent);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell($kolomkosong, $t, '', 0, 0, $indent);
  $p->Cell($kolom2, $t, Tanggal($thn['TglKRSMulai']).' ~ '.Tanggal($thn['TglKRSSelesai']), 0, 1, $indent);
  $p->Ln($jeda);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->SetFillColor($WarnaBayar[0], $WarnaBayar[1], $WarnaBayar[2]);
  $p->Cell($t, $t, '', 1, 0, '', true);
  $p->Cell($kolom1, $t, 'Tanggal Pembayaran Kuliah:', 0, 1, $indent);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell($kolomkosong, $t, '', 0, 0, $indent);
  $p->Cell($kolom2, $t, Tanggal($thn['TglBayarMulai']).' ~ '.Tanggal($thn['TglBayarSelesai']), 0, 1, $indent);
  $p->Ln($jeda);
  
  $p->Ln(2*$jeda);
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell($kolom1, $t, 'Perkuliahan', 'BT', 0, $indent);
  $p->Cell($kolomperpanjangan, $t, '', 'BT', 1, $indent);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->SetFillColor($WarnaKuliah[0], $WarnaKuliah[1], $WarnaKuliah[2]);
  $p->Cell($t, $t, '', 1, 0, '', true);
  $p->Cell($kolom1, $t, 'Tanggal Perkuliahan Berjalan :', 0, 1, $indent);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell($kolomkosong, $t, '', 0, 0, $indent);
  $p->Cell($kolom2, $t, Tanggal($thn['TglKuliahMulai']).' ~ '.Tanggal($thn['TglKuliahSelesai']), 0, 1, $indent);
  $p->Ln($jeda);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->SetFillColor($WarnaUTS[0], $WarnaUTS[1], $WarnaUTS[2]);
  $p->Cell($t, $t, '', 1, 0, '', true); 
  $p->Cell($kolom1, $t, 'Tanggal Ujian Tengah Semester:', 0, 1, $indent);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell($kolomkosong, $t, '', 0, 0, $indent);
  $p->Cell($kolom2, $t, Tanggal($thn['TglUTSMulai']).' ~ '.Tanggal($thn['TglUTSSelesai']), 0, 1, $indent);
  $p->Ln($jeda);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->SetFillColor($WarnaUAS[0], $WarnaUAS[1], $WarnaUAS[2]);
  $p->Cell($t, $t, '', 1, 0, '', true);
  $p->Cell($kolom1, $t, 'Tanggal Ujian Akhir Semester :', 0, 1, $indent);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell($kolomkosong, $t, '', 0, 0, $indent);
  $p->Cell($kolom2, $t, Tanggal($thn['TglUASMulai']).' ~ '.Tanggal($thn['TglUASSelesai']), 0, 1, $indent);
  $p->Ln($jeda);
  
  $p->Ln(2*$jeda);
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell($kolom1, $t, 'Akhir Semester', 'BT', 0, $indent);
  $p->Cell($kolomperpanjangan, $t, '', 'BT', 1, $indent);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->SetFillColor($WarnaNilai[0], $WarnaNilai[1], $WarnaNilai[2]);
  $p->Cell($t, $t, '', 1, 0, '', true);
  $p->Cell($kolom1, $t, 'Tanggal Finalisasi Penilaian:', 0, 1, $indent);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell($kolomkosong, $t, '', 0, 0, $indent);
  $p->Cell($kolom2, $t, Tanggal($thn['TglNilai']), 0, 1, $indent);
  $p->Ln($jeda);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->SetFillColor($WarnaCuti[0], $WarnaCuti[1], $WarnaCuti[2]);
  $p->Cell($t, $t, '', 1, 0, '', true);
  $p->Cell($kolom1, $t, 'Tanggal Batas Pengajuan Cuti:', 0, 1, $indent);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell($kolomkosong, $t, '', 0, 0, $indent);
  $p->Cell($kolom2, $t, Tanggal($thn['TglCuti']), 0, 1, $indent);
  $p->Ln($jeda);
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
function Tanggal($string, $format='Y-m-d', $divider='-')
{	$arrBulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	$arrMonth = array();
	$arrMonth['Jan'] = 'Januari';
	$arrMonth['Feb'] = 'Februari';
	$arrMonth['Mar'] = 'Maret';
	$arrMonth['Apr'] = 'April';
	$arrMonth['May'] = 'Mei';
	$arrMonth['Jun'] = 'Juni';
	$arrMonth['Jul'] = 'Juli';
	$arrMonth['Aug'] = 'Agustus';
	$arrMonth['Sep'] = 'September';
	$arrMonth['Oct'] = 'Oktober';
	$arrMonth['Nov'] = 'November';
	$arrMonth['Dec'] = 'Desember';
	
	$pos = 0; $tahun = ''; $bulan = ''; $hari = '';
	$formatArr = explode($divider, $format);
	$stringArr = explode($divider, $string);
	$n = 0;
	foreach($formatArr as $part)
	{	
		if($part == 'Y') 
		{	$tahun = substr($string, $pos, 4);
			$pos += 4+1;
		}
		else if($part == 'y') 
		{	$temptahun = substr($string, $pos, 2);
			$pos += 2+1;
			if($temptahun > 30) $tahun = '19'.$temptahun;
			else $tahun = '20'.$temptahun;
		}
		else if($part == 'm') 
		{	$bulan = $arrBulan[substr($string, $pos, 2)-1];
			$pos += 2+1;
		}
		else if($part == 'M') 
		{	$bulan = $arrMonth[substr($string, $pos, 3)];
			$pos += 3+1;
		}
		else if($part == 'd') 
		{	$hari = substr($string, $pos, 2);
			$pos += 2+1;
		}
	}
	return $hari.' '.$bulan.' '.$tahun;
}

function UbahKeBulanIndonesiaSingkat($integer)
{	$arrBulan= array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des');
	return $arrBulan[$integer-1];
}	
?>
