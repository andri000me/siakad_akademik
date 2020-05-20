<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 08 Sept 2008

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";
  include_once "../util.lib.php";
  
// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

if (empty($TahunID))
  die(ErrorMsg("Error",
    "Tentukan tahun akademik-nya dulu.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));
if (empty($ProdiID))
  die(ErrorMsg("Error",
    "Tentukan Program Studi-nya dulu.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));

// *** Main
$prds = getaField('prodi',"KodeID = '".KodeID."' and ProdiID",$ProdiID,'Nama');

$thn = NamaTahun($TahunID);
$pdf = new PDF('P','mm','A4');
$pdf->SetTitle("Jadwal Ujian Sidang Komprehensif Tahun $thn");
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell($t, 9, "Jadwal Ujian Sidang Komprehensif - $thn", 0, 1, 'C');
$pdf->Cell($t, 9, "Program Studi $prds", 0, 1, 'C');
Isinya($pdf);

$pdf->Output();

// *** Functions ***

function Isinya($p) {
  $lbr = 290; $t = 5;
  JudulKolomnya($p);
  $p->SetFont('Helvetica', '', 8);
  $s = "select k.*, m.MhswID,left(m.Nama, 28) as Mhsw,m.KelasID
  		from kompre k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	where k.KodeID = '".KodeID."'
      and k.TahunID = '$_SESSION[TahunID]'
	  and k.Lulus = 'N'
	  and k.NA = 'N'
    Group by k.MhswID";
  $r = _query($s);
  $n = 0;

  $jum = _num_rows($r);
  while ($w = _fetch_array($r)) {
  	$hari = AmbilHari($w[_Hari]);
    $n++;
	$ss = "select k.*, LEFT(k.JamMulai, 5) as JM, LEFT(k.JamSelesai, 5) as JS, d.Nama as _Dosen
		from kompredosen k left outer join dosen d on k.DosenID=d.Login 
		where k.KompreID = '$w[KompreID]' and k.NA = 'N'
		order by k.Tanggal, k.JamMulai, k.JamSelesai";
	$qq = _query($ss);
	$nn = _num_rows($qq);
	$tt = $nn*$t;
	$p->Cell(5, $tt, '', '', 0);
    $p->Cell(8, $tt, $n, 1, 0);
    $p->Cell(20, $tt, $w['MhswID'], 1, 0);
    $p->Cell(60, $tt, $w['Mhsw'], 1, 0);
	while ($ww = _fetch_array($qq)){
		$p->Cell(40, $t, $ww['_Dosen'], 1, 0);
		$p->Cell(55, $t, GetDateInWords($ww['Tanggal'])." / ".$ww['JM']." - ".$ww['JS']." / ".$ww['RuangID'], 1, 1);
		$p->Cell(93);
	}
	//$p->Cell(55, $t, '', 'T', 0);
    $p->SetX(10);
  }
  
  
  $bulan = AmbilBulan(date('m'));
  $p->Ln($t);
	$identitas = GetFields('identitas', 'Kode', KodeID, "*");
	$pejabat = GetFields('pejabat', "KodeJabatan='KABAA' and KodeID", KodeID, 'Nama, Jabatan');
	$p->Cell(5, $t, '', '', 0);
	$p->Cell(50, $t, $identitas['Kota'].', '.date('d')." ".$bulan." ".date('Y'), '', 0, 'C');
	$p->Ln($t*4);
	$p->Cell(50, $t, $pejabat[Nama], 0, 0, 'C');
	$p->Ln($t);
	$p->Cell(50, $t, $pejabat[Jabatan], 0, 0, 'C');
	

}
function JudulKolomnya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(5, $t, '', '', 0);
  $p->Cell(8, $t, 'No.', 1, 0);
  $p->Cell(20, $t, 'N I M', 1, 0);
  $p->Cell(60, $t, 'Mahasiswa', 1, 0);
  $p->Cell(40, $t, 'Dosen', 1, 0);
  $p->Cell(55, $t, 'Hari/Tanggal/Waktu/Ruang', 1, 0);
  $p->Ln($t);
}

function Footer($p){
  $t = 6;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, '', '', 0);
  $p->Cell(50, $t, 'BEKASI, ', 'BT', 0);
  $p->Cell(20, $t, 'N I M', 'BT', 0);
}

function AmbilBulan($integer)
{	$arrBulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
						'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	return $arrBulan[$integer+0];
}


function AmbilHari($string)
{	$arrHari['Mon'] = 'Senin';
	$arrHari['Tue'] = 'Selasa';
	$arrHari['Wed'] = 'Rabu';
	$arrHari['Thu'] = 'Kamis';
	$arrHari['Fri'] = 'Jumat';
	$arrHari['Sat'] = 'Sabtu';
	$arrHari['Sun'] = 'Minggu';
	
	return $arrHari[$string];
}
?>
