<?php
// Author: Emanuel Setio Dewo
// 06 Feb 2006

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";
  
// *** Parameters ***
$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");

$lbr1 = 200;

$pdf = new FPDF();
$pdf->SetTitle("Denah Kursi USM");

$prodi = $_REQUEST['prodi'];
$pmbusm = $_REQUEST['pmbusm'];
$gel = $_REQUEST['gel'];

CetakDenahKursiUSM($_SESSION['_usm_ruang'] , $_SESSION['_usm_prodi'], $_SESSION['_usm_pmbusm'], $gelombang, $pdf);
		
$pdf->Output();

// *** Functions ***

function CetakDenahKursiUSM($ruang, $prodi, $pmbusm, $gel, $p)
{  	
	// Ambil semua prodiusm yang hars di-cetak
	// Bila prodi kosong, print semua prodiusm
	$prodistring = (empty($prodi))? '' : "and INSTR(concat('|', pu.ProdiID, '|'), concat('|', '$prodi', '|'))";
	// Bila PMBUSM kosong, print semua ujian usmnnya
	$pmbusmstring = (empty($pmbusm))? '' : "and pu.PMBUSMID='$pmbusm'" ;
	
	$s = "select DISTINCT(ru.ProdiUSMID) as _ProdiUSMID from ruangusm ru left outer join prodiusm pu on ru.ProdiUSMID=pu.ProdiUSMID
			where ru.KodeID='".KodeID."' and ru.PMBPeriodID='$gel' $prodistring $pmbusmstring";
	$r = _query($s);
	while($w = _fetch_array($r))
	{	
		BuatPerHalaman($w['_ProdiUSMID'], $gel, $p);
	}	
}

function BuatPerHalaman($prodiusmid, $gelombang, $p)
{	

	if(!empty($prodiusmid))
	{
		$s = "select pu1.RuangID, date_format(pu1.TanggalUjian, '%d %M %Y') as _TanggalUjian, 
				LEFT(pu1.JamMulai, 5) as _JM, LEFT(pu1.JamSelesai, 5) as _JS, pu1.PMBUSMID, pu2.Nama 
				from prodiusm pu1 left outer join pmbusm pu2 on pu1.PMBUSMID= pu2.PMBUSMID
				where pu1.ProdiUSMID = '$prodiusmid' and pu1.KodeID='".KodeID."'";
		$r = _query($s);
		while($w = _fetch_array($r))
		{	
			if(!empty($w['RuangID'])) 
			{	$arrRuangID = explode(',', $w['RuangID']);			
				
				foreach($arrRuangID as $RuangID)
				{
					$p->AddPage('P', 'A4');
	
					$p->SetFont('Helvetica', 'B', 14);
					$p->SetFillColor(200, 200, 200);
					HeaderLogo("Denah Kursi USM - Gelombang $gelombang", $p, 'P');
					$p->Ln(13);
					
					$p->SetFont('Helvetica', 'B', 11);
					$t = 3;
					
					$t2 = 2*$t;
					$p->Cell(25, $t2, 'Mata Ujian', 0, 0);
					$p->Cell(3, $t2, ':', 0);
					$p->Cell(2*strlen($w['Nama'])+2, $t2, $w['Nama'], 0, 0, 'L');
					$p->Ln($t2+2);
					$p->Cell(25, $t2, 'Waktu Ujian ', 0, 0);
					$p->Cell(3, $t2, ':', 0);
					$tanggalstring = $w['_TanggalUjian'].' '.$w['_JM'].'-'.$w['_JS'];
					$p->Cell(2*strlen($tanggalstring)+2, $t2, $tanggalstring, 0, 0, 'L');
					$p->Cell(30, $t2, '', 0);
					$p->Cell(15, $t2, 'Ruang', 0, 0);
					$p->Cell(3, $t2, ':', 0);
					$p->Cell(2*strlen($RuangID)+5, $t2, $RuangID, 0, 0, 'C');
					$p->Ln($t2*2);	
			
					// header dari tabel
					$ruang = GetFields('ruang', "RuangID='$RuangID' and KodeID", KodeID, 'KapasitasUjian, KolomUjian');
					$BanyakBaris = ceil($ruang['KapasitasUjian']/$ruang['KolomUjian']);
					$arrSiswa = array(); $arrRuangUSMID = array(); $arrKehadiran = array(); $arrNilaiUSM = array();
					$s1 = "select UrutanDiRuang, PMBID, RuangUSMID, Kehadiran, NilaiUSM from ruangusm where ProdiUSMID='$prodiusmid' and PMBPeriodID='$gelombang' and RuangID='$RuangID' and KodeID='".KodeID."'";
					$r1 = _query($s1);
					while($w1 = _fetch_array($r1))
					{	$arrSiswa[$w1['UrutanDiRuang']] = $w1['PMBID'];
						$arrRuangUSMID[$w1['UrutanDiRuang']] = $w1['RuangUSMID'];
						$arrKehadiran[$w1['UrutanDiRuang']] = $w1['Kehadiran'];
						$arrNilaiUSM[$w1['UrutanDiRuang']] = $w1['NilaiUSM'];
					}
				  // isi Table
				  
				  $p->SetFont('Helvetica', 'B', 9);
				  $t = 6;
				  $n = 0;
					for($i = 1; $i <= $BanyakBaris; $i++)
					{	for($j = 1; $j <= $ruang['KolomUjian']; $j++)
						{	$n++;
							$XStart = $p->GetX();
							$YStart = $p->GetY();
								
							if(empty($arrSiswa[$n]))
							{	$p->Cell(37, ($t*3)-1, '', 1, 0, '', true);
								$p->SetXY($XStart, $YStart);
								$p->Image('../img/kursi.jpg', $XStart+2, $YStart+2, 10);
								$p->Ln($t*2);
								$p->SetX($XStart);
								$p->Cell(37, $t, "  Kolom $j, Baris $i", 0, 0);
								$p->SetXY($XStart+38, $YStart);
								
							}
							else
							{	
								$cama = GetFields('pmb', "PMBID='$arrSiswa[$n]' and KodeID", KodeID, '*');
								$p->Cell(37, $t, $arrSiswa[$n], 0, 0, 'C');
								$p->Ln($t*3/4);
								$p->SetX($XStart);
								$p->Cell(37, $t, $cama['Nama'], 0, 0, 'C');
								$p->SetXY($XStart, $YStart);
								$p->Ln($t*2);
								$p->SetX($XStart);
								$p->Cell(30, $t, "  Kolom $j, Baris $i", 0, 0);
								$p->SetXY($p->GetX(), $p->GetY()-3);
								if($_SESSION['_usm_jenisx'] == 1)
								{	if($arrKehadiran[$n] == 'Y')
										if($arrNilaiUSM[$n] == 0) $p->Cell(5, $t, "", 1, 0, 'C');
										else $p->Cell(5, $t, "$arrNilaiUSM[$n]", 1, 0, 'C');
									else
										$p->Cell(5, $t, "X", 1, 0, 'C');
								}
								else
								{	if($arrKehadiran[$n] == 'Y') $p->Cell(5, $t, "H", 1, 0, 'C');
									else $p->Cell(5, $t, "", 1, 0, 'C');
								}
								$p->SetXY($XStart, $YStart);
								$p->Cell(37, ($t*3)-1, '', 1, 0);
								$p->SetXY($XStart+38, $YStart);
							}
						}
						$p->Ln(3*$t);
					}
				}
			}
	   }
	}
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
