<?php

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

// *** Parameters ***
$KurikulumID = $_REQUEST['KurikulumID'];
$ProdiID = $_REQUEST['ProdiID'];
// *** Init PDF
$pdf = new PDF();
$pdf->SetTitle("STRUKTUR PROGRAM KURIKULUM");
$lbr = 190;

//BuatHeadernya($TahunID, $ProdiID, $sta, $pdf);
BuatIsinya($KurikulumID, $ProdiID, $pdf);

$pdf->Output();

function BuatIsinya($KurikulumID, $ProdiID, $p){
	$t = 4;
	$jumsesi = GetaField('mk', "KodeID='".KodeID."' and KurikulumID = '$KurikulumID' and ProdiID", $ProdiID, 'MAX(Sesi)');
	$s = "select * from jenismk where ProdiID = '$ProdiID' and NA = 'N' order by Singkatan";
	$q = _query($s);
	
	$p->addPage('P', 'A4');
	BuatHeader($jumsesi, $ProdiID, $p);

	for ($i=1;$i<=$jumsesi;$i++){
		$totalT[$i] = 0;
		$totalP[$i] = 0;
		$totalS[$i] = 0;
	}
	
	$totalTall = 0;
	$totalPall = 0;
	$totalSall = 0;
	
	while ($w = _fetch_array($q)){
	 	$p->SetFont('Helvetica', '', 5);
		$p->Cell(5, $t, "", 1, 0);
		$p->Cell(185, $t, $w[Singkatan], 1, 1);
		$s2 = "select * from mk where ProdiID = '$ProdiID' and KurikulumID = '$KurikulumID' and NA = 'N' and JenisMKID = $w[JenisMKID] order by Nama";
		$q2 = _query($s2);
		$n = 0;
		$lbrSks = (125/$jumsesi)/3;
		for ($i=1;$i<=$jumsesi;$i++){
			$sesiTjum[$i] = 0;
			$sesiPjum[$i] = 0;
			$sesijum[$i] = 0;
		}
		
		$subTjum = 0;
		$subPjum = 0;
		$subjum = 0;
		
		while ($w2 = _fetch_array($q2)){
			$n++;
			$p->Cell(5, $t, $n, 1, 0);
			$p->Cell(30, $t, $w2[Nama], 1, 0);
			$p->Cell(15, $t, $w2[MKKode], 1, 0, 'C');
			$sksTjum = 0;
			$sksPjum = 0;
			$sksjum = 0;
			
			for ($i=1;$i<=$jumsesi;$i++){
				if ($w2[Sesi] == $i){
					$sksT = $w2[SKSTatapMuka];
					$sksP = $w2[SKSPraktikum];
					$sks = $w2[SKS];

					$sksTjum += $sksT;
					$sksPjum += $sksP;
					$sksjum += $sks;
					
					// HITUNG SUB TOTAL SESI
					$sesiTjum[$i] += $sksT;
					$sesiPjum[$i] += $sksP;
					$sesijum[$i] += $sks;
				} else {
					$sksT = "";
					$sksP = "";
					$sks = "";
				}
				$p->Cell($lbrSks, $t, $sksT, 1, 0, 'C');
				$p->Cell($lbrSks, $t, $sksP, 1, 0, 'C');
				$p->Cell($lbrSks, $t, $sks, 1, 0, 'C');
				
			}
			
			$p->Cell(5, $t, $sksTjum, 1, 0, 'C');
			$p->Cell(5, $t, $sksPjum, 1, 0, 'C');
			$p->Cell(5, $t, $sksjum, 1, 0, 'C');
			$p->ln($t);
			
			$subTjum += $sksTjum;
			$subPjum += $sksPjum;
			$subjum += $sksjum;
		}
		
		// BUAT TABEL SUB TOTAL
		$p->SetFont('Helvetica', 'B', 5);
		$p->Cell(5, $t, "", 1, 0);
		$p->Cell(30, $t, "SUB TOTAL", 1, 0);
		$p->Cell(15, $t, "", 1, 0, 'C');
		for ($i=1;$i<=$jumsesi;$i++){
			if ($sesijum[$i] == 0){
				$sesiTjum[$i] = "";
				$sesiPjum[$i] = "";
				$sesijum[$i] = "";
			}
			$p->Cell($lbrSks, $t, $sesiTjum[$i], 1, 0, 'C');
			$p->Cell($lbrSks, $t, $sesiPjum[$i], 1, 0, 'C');
			$p->Cell($lbrSks, $t, $sesijum[$i], 1, 0, 'C');
			
			$totalT[$i] += $sesiTjum[$i]+0;
			$totalP[$i] += $sesiPjum[$i]+0;
			$totalS[$i] += $sesijum[$i]+0;
		}
		$p->Cell(5, $t, $subTjum, 1, 0, 'C');
		$p->Cell(5, $t, $subPjum, 1, 0, 'C');
		$p->Cell(5, $t, $subjum, 1, 0, 'C');
		$p->ln($t);
		
		$totalTall += $subTjum;
		$totalPall += $subPjum;
		$totalSall += $subjum;
	}
	
	// BUAT TABEL TOTAL
	$p->SetFont('Helvetica', 'B', 5);
	$p->SetFillColor(200, 200, 200);
	$p->Cell(5, $t, "", 1, 0, 'L', true);
	$p->Cell(30, $t, "TOTAL", 1, 0, 'L', true);
	$p->Cell(15, $t, "", 1, 0, 'C', 'L', true);
	for ($i=1;$i<=$jumsesi;$i++){
		if ($totalS[$i] == 0){
			$totalT[$i] = "";
			$totalP[$i] = "";
			$totalS[$i] = "";
		}
		$p->Cell($lbrSks, $t, $totalT[$i], 1, 0, 'C', true);
		$p->Cell($lbrSks, $t, $totalP[$i], 1, 0, 'C', true);
		$p->Cell($lbrSks, $t, $totalS[$i], 1, 0, 'C', true);
	}
	$p->Cell(5, $t, $totalTall, 1, 0, 'C', true);
	$p->Cell(5, $t, $totalPall, 1, 0, 'C', true);
	$p->Cell(5, $t, $totalSall, 1, 0, 'C', true);
	$p->ln($t*2);
	
	BuatFooter($p);
}

function BuatHeader($jum, $ProdiID, $p){
  global $lbr;
  $NamaProdi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');
  $t = 6;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, $t, "STRUKTUR PROGRAM KURIKULUM JURUSAN ".strtoupper($NamaProdi), 0, 1, 'C');
  $p->ln($t);
  $t = 3;
  $p->SetFont('Helvetica', 'B', 5);
  $p->Cell(5, $t*3, "No", 1, 0, 'C');
  $p->Cell(30, $t*3, "MATA KULIAH", 1, 0, 'C');
  $p->Cell(15, $t*3, "KODE MK", 1, 0, 'C');
  $p->Cell(125, $t, "SEMESTER", 1, 0, 'C');
  $p->Cell(15, $t*2, "JUMLAH", 1, 1, 'C');
  //buat table sks untuk jumlah
  $p->setXY($p->getX()+175, $p->getY());
	$p->Cell(5, $t, "T", 1, 0, 'C');
	$p->Cell(5, $t, "P", 1, 0, 'C');
	$p->Cell(5, $t, "SKS", 1, 0, 'C');
  $p->ln($t);
  //BUAT TABEL SESI
  $p->setXY($p->getX()+50, $p->getY()-$t*2);
  $lb = 125/$jum;
  for ($i=1;$i<=$jum;$i++){
	$p->Cell($lb, $t, $i, 1, 0, 'C');
  }
  $p->ln($t);
  //BUAT TABEL SKS
  $p->setXY($p->getX()+50, $p->getY());
  $lb2 = $lb/3;
  for ($i=1;$i<=$jum;$i++){
	$p->Cell($lb2, $t, "T", 1, 0, 'C');
	$p->Cell($lb2, $t, "P", 1, 0, 'C');
	$p->Cell($lb2, $t, "SKS", 1, 0, 'C');
  }
  $p->ln($t);
}

function BuatFooter($p){
  $t = 3;
  $p->SetFont('Helvetica', 'B', 6);
  $p->Cell(100, $t, "Keterangan :", 0, 1);
  $p->SetFont('Helvetica', '', 6);
  $p->Cell(100, $t, "1. Satu jam perkuliahan efektif : 45 menit", 0, 1);
  $p->Cell(100, $t, "2. Perkuliahan efektif tiap hari : 8 jam", 0, 1);
  $p->Cell(100, $t, "3. Tiap minggu perkuliahan efektif : 38 jam", 0, 1);
  $p->Cell(100, $t, "4. Jumlah minggu perkuliahan efektif persemester : 19 minggu", 0, 1);
  $p->Cell(100, $t, "5. Jumlah jam perkuliahan efektif persemester : 722 jam", 0, 1);
  $p->Cell(100, $t, "6. Praktek industri dilaksanakan pada semester VI : 10 minggu (380 jam)", 0, 1);
  $p->Cell(100, $t, "7. Proyek spesialisasi (TA) pada semester VI : 9 minggu (342 jam)", 0, 1);
  
  $JabatanPudir1 = GetaField('pejabat', "NA = 'N' and Urutan", 2, 'Jabatan');
  $NamaPudir1 = GetaField('pejabat', "NA = 'N' and Urutan", 2, 'Nama');
  $NIPPudir1 = GetaField('pejabat', "NA = 'N' and Urutan", 2, 'NIP');
  
  $p->setXY($p->getX()+140, $p->getY()-$t*7);
  $p->Cell(50, $t, "Padang,", 0, 1);
  $p->setXY($p->getX()+140, $p->getY());
  $p->Cell(50, $t, $JabatanPudir1, 0, 1);
  $p->setXY($p->getX()+140, $p->getY());
  $p->Cell(50, $t*4, "", 0, 1);
  $p->setXY($p->getX()+140, $p->getY());
  $p->Cell(50, $t, $NamaPudir1, 0, 1);
  $p->setXY($p->getX()+140, $p->getY());
  $p->Cell(50, $t, "NIP. ".$NIPPudir1, 0, 1);
}
