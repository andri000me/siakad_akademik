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
$Angkatan = GetSetVar('Angkatan');

// Tgl Mulai
/*
$TglMulai_y = GetSetVar('TglMulai_y', date('Y'));
$TglMulai_m = GetSetVar('TglMulai_m', date('m'));
$TglMulai_d = GetSetVar('TglMulai_d', date('d'));
$_SESSION['TglMulai'] = "$TglMulai_y-$TglMulai_m-$TglMulai_d";
// Tgl Selesai
$TglSelesai_y = GetSetVar('TglSelesai_y', date('Y'));
$TglSelesai_m = GetSetVar('TglSelesai_m', date('m'));
$TglSelesai_d = GetSetVar('TglSelesai_d', date('d'));
$_SESSION['TglSelesai'] = "$TglSelesai_y-$TglSelesai_m-$TglSelesai_d";
*/
// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'PilihProdi' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function PilihProdi() {
	$optProdi = "";
	$optTahun = "";
	$s = "select * from prodi where NA = 'N' order by Nama";
	$q = _query($s);
	while ($w = _fetch_array($q)){
		$optProdi .= "<option value=$w[ProdiID]>$w[Nama]</option>";
	}
	
	$now = date('Y');
	$startY = $now - 10;
	for ($i=$startY;$i<=$now;$i++){
		$optTahun .= "<option value=$i>$i</option>";
	}
	CheckFormScript("Angkatan");
	echo '
		<link rel="stylesheet" type="text/css" href="../themes/default/index.css" />
		<form action="" method=POST onSubmit="return CheckForm(this)">
		<input type=hidden name=gos value=Cetak />
		<table class=box width=400 cellpadding=1 align=center>
		<tr>
			<th class=ttl colspan=3 align=center>Laporan Pembayaran Uang Kuliah</th>
		</tr>
		<tr>
			<td class=wrn width=1>&nbsp;</td>
			<td class=inp width=150>Program Studi : </td>
			<td class=ul>
				<select id=prodi name=ProdiID>
						'.$optProdi.'
				</select>
			</td>
		</tr>
		<tr>
			<td class=wrn>&nbsp;</td>
			<td class=inp>Angkatan Mahasiswa : </td>
			<td class=ul><input type=text name=Angkatan size=4 maxlength=4 />
			</td>
		</tr>
		<tr>
			<td class=wrn>&nbsp;</td>
			<td class=inp>Tahun Akademik : </td>
			<td class=ul>
				<select name=TahunID>
						'.$optTahun.'
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=3 align=center><input class=buttons type=submit value=Cetak /></td>
		</tr>
		</table>
	';
}

function Cetak() {
  // *** Init PDF
  $pdf = new FPDF('L','mm','A4');
  $pdf->SetTitle("Pembayaran Uang Kuliah per tahun");
  $pdf->AddPage();
  $lbr = 290;

  BuatIsinya($_SESSION['TahunID'], $_SESSION['ProdiID'], $_SESSION['Angkatan'], $pdf);

  $pdf->Output();
}

function BuatIsinya($TahunID, $ProdiID, $Angkatan, $p) {
  global $lbr;
  $n = 0; $t = 6;
  BuatHeadernya($TahunID, $ProdiID, $Angkatan, $p);
  $JumSesi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'JumlahSesi');
  $MaxSesi = GetaField('mk', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'MAX(Sesi)');
  $s = "select k.MhswID as _MhswID, k.Sesi, m.Nama as _NamaMhsw from khs k left outer join mhsw m on m.MhswID = k.MhswID
  		where k.ProdiID = '$ProdiID' and m.TahunID like '$Angkatan%' and k.TahunID like '$TahunID%' group by k.MhswID";
		
  $q = _query($s);
  $totalJumlah = 0;
  $totalSisa = 0;
  while ($w = _fetch_array($q)){
  		$n++;
		$p->SetFont('Helvetica', '', 8);
		$p->Cell(10, $t, $n, 1, 0, 'R');
		$p->Cell(60, $t, $w[_NamaMhsw], 1, 0, 'L');
		$p->Cell(30, $t, $w[_MhswID], 1, 0, 'C');
		
		$js = $JumSesi;
		
		$CekSesi = (($TahunID - $Angkatan)*$js)+$JumSesi;
		if ($CekSesi > $MaxSesi){
			$JumSesi -= ($CekSesi - $MaxSesi);
			if ($JumSesi <= 0){
				$JumSesi = 1;
			}
		}
		$jumlahBiaya = 0;
		$jumlahBayar = 0;
		for ($i=1;$i<=$JumSesi;$i++){
			$sesi = (($TahunID - $Angkatan)*$js)+$i;
			if ($sesi > $MaxSesi){
				$sesi = $MaxSesi;
			}
			$s2 = "select * from khs where MhswID = '$w[_MhswID]' and Sesi = '$i' and TahunID like '$TahunID%' and ProdiID = '$ProdiID'";
			$q2 = _query($s2);
			$w2 = _fetch_array($q2);
			
			$jumlahBayar += $w2[Bayar];
			$bayar = ($w2[Bayar] == 0)? '-' : number_format($w2[Bayar],0,'.',',');
			$p->Cell(90/$JumSesi, $t, $bayar, 1, 0, 'R');
		}
		$jumlahBiaya = GetFields('bipotmhsw', "KodeID='".KodeID."' and TahunID like '$TahunID%' and TrxID = '1' and MhswID", $w[_MhswID], 'SUM(Besar) as _jumlah');
		$jumlahPotongan = GetFields('bipotmhsw', "KodeID='".KodeID."' and TahunID like '$TahunID%' and TrxID = '-1' and MhswID", $w[_MhswID], 'SUM(Besar) as _jumlah');
		$jumlah = $jumlahBiaya[_jumlah] - $jumlahPotongan[_jumlah];
		$sisa = (($jumlah - $jumlahBayar) > 0)? number_format($jumlah - $jumlahBayar,0,'.',',') : 'Lunas';
		
		//$p->Cell(30, $t, '', 1, 0, 'R');
		$p->Cell(30, $t, number_format($jumlahBayar,0,'.',','), 1, 0, 'R');
		$p->Cell(30, $t, $sisa, 1, 1, 'R');	
		
		$totalJumlah += $jumlahBayar;	
		$totalSisa += ($jumlah - $jumlahBayar);	
  }
  	
	// buat total
	$lb = 190;
	$p->SetFont('Helvetica', 'B', 8);
	$p->Cell($lb, $t, '', 0, 0);
	$p->Cell(30, $t, number_format($totalJumlah,0,'.',','), 1, 0, 'R');
	$p->Cell(30, $t, number_format($totalSisa,0,'.',','), 1, 1, 'R');
}
function BuatHeadernya($TahunID, $ProdiID, $Angkatan, $p) {
  global $lbr;
  $t = 6;
  $NamaProdi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');
  $p->SetFont('Helvetica', 'B', 12);
  $p->SetFillColor(200, 200, 200);
  $p->Cell($lbr, $t, "PEMBAYARAN UANG KULIAH DAN UANG SUMBANGAN PENDIDIKAN", 0, 1, 'C');
  $p->Cell($lbr, $t, "JURUSAN ".strtoupper($NamaProdi)." ANGKATAN $Angkatan", 0, 1, 'C');
  $p->Cell($lbr, $t, "TAHUN AKADEMIK $TahunID", 0, 1, 'C');  
  $p->Ln(2);

  $NamaTagihan = GetaField('bipotnama', "Urutan", 1, 'Nama');  
  $NamaSesi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'NamaSesi');
  $JumSesi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'JumlahSesi');
  $MaxSesi = GetaField('mk', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'MAX(Sesi)');
  
  $t = 6;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t*2, 'NO', 1, 0, 'C', true);
  $p->Cell(60, $t*2, 'NAMA MAHASISWA', 1, 0, 'C', true);
  $p->Cell(30, $t*2, 'N I M', 1, 0, 'C', true);
  $p->Cell(90, $t, strtoupper($NamaTagihan), 1, 0, 'C', true);
  //$p->Cell(30, $t*2, 'DANA BPP', 1, 0, 'C', true);
  $p->Cell(30, $t*2, 'JUMLAH', 1, 0, 'C', true);
  $p->Cell(30, $t*2, 'SISA', 1, 1, 'C', true);
  
  $p->setXY($p->getX()+100, $p->getY()-$t);
  $js = $JumSesi;
  $CekSesi = (($TahunID - $Angkatan)*$js)+$JumSesi;
  if ($CekSesi > $MaxSesi){
  	$JumSesi -= ($CekSesi - $MaxSesi);
	if ($JumSesi <= 0){
		$JumSesi = 1;
	}
  }
  for ($i=1;$i<=$JumSesi;$i++){
  	$sesi = (($TahunID - $Angkatan)*$js)+$i;
	if ($sesi > $MaxSesi){
		$sesi = $MaxSesi;
	}
  	$p->Cell(90/$JumSesi, $t, $NamaSesi.' '.$sesi, 1, 0, 'C', true);
  }
  $p->Ln($t);

}
?>
