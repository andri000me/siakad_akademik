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
  include_once "../fpdf.php";
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
    "Tentukan program studi-nya dulu.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));
	  
// *** Main
$prds = getaField('prodi',"KodeID = '".KodeID."' and ProdiID",$ProdiID,'Nama');
$jjgs = GetaField('jenjang', "JenjangID", GetaField('prodi',"KodeID = '".KodeID."' and ProdiID",$ProdiID,'JenjangID'), "Nama");

$thn = NamaTahun($TahunID);
$pdf = new FPDF('P','mm','A4');
$pdf->SetTitle("Jadwal Ujian Komprehensif $thn");

$s1 = "select DISTINCT(r.KampusID) as _KMP, k.Tanggal as _TGL 
			from kompredosen k 
				left outer join ruang r on k.RuangID=r.RuangID and r.KodeID='".KodeID."'
			where k.KodeID='".KodeID."' 
			order by _KMP, k.Tanggal";
$r1 = _query($s1);
while($w1 = _fetch_array($r1))
{	
	$pdf->AddPage('L');
	$pdf->SetAutoPageBreak(true, 5);
	HeaderLogo("JADWAL UJIAN KOMPREHENSIF - ".strtoupper($thn) , $pdf, 'L', "JENJANG ".$jjgs." PROGRAM STUDI: ".strtoupper($prds));
	BuatHeadernya($w1['_TGL'], $w1['_KMP'], $pdf);
	Isinya($w1['_TGL'], $w1['_KMP'], $pdf);
	BuatFooternya($pdf);
}
$pdf->Output();

// *** Functions ***

function BuatHeadernya($Tanggal, $KampusID, $p) 
{	$t = 6;
	$p->SetFont('Helvetica', 'B', 10);
	$p->Cell(30, $t, "Hari / Tanggal", 0, 0);
	$p->Cell(0, $t, ': '.GetDateInWords($Tanggal), 0, 0);
	$p->Ln($t);
	
	$p->Cell(30, $t, "Tempat", 0, 0);
	$NamaKampus = GetaField('kampus', "KampusID='$KampusID' and KodeID", KodeID, "Nama");
	$p->Cell(0, $t, ': '.$NamaKampus, 0, 0);
	$p->Ln(2*$t);
}

function Isinya($Tanggal, $Kampus, $p) {
  $lbr = 290; $t = 6;
  JudulKolomnya($p);
  $p->SetFont('Helvetica', '', 10);
  
  $PilihanKompre = GetaField('prodi', "ProdiID='$_SESSION[ProdiID]' and KodeID", KodeID, 'PilihanKompre');
  if($PilihanKompre == 'Y') // Setiap mata uji memiliki penguji dan waktu berbeda2
  {
	  $s = "select kd.*, m.MhswID,left(m.Nama, 28) as Mhsw,m.KelasID, d.Nama as _DosenPenguji, kmu.Nama as _NamaMataUji,
			date_format(kd.Tanggal, '%a') as _Hari, LEFT(kd.JamMulai, 5) as _JamUjian, LEFT(kd.JamSelesai, 5) as _JamSelesai, pa.Nama as _DosenPA
			from kompredosen kd 
				left outer join kompre k on kd.KompreID=k.KompreID and k.KodeID='".KodeID."'
				left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
				left outer join dosen pa on pa.Login = m.PenasehatAkademik
				left outer join dosen d on d.Login = kd.DosenID
				left outer join komprematauji kmu on kmu.KompreMataUjiID=kd.KompreMataUjiID and kmu.KodeID='".KodeID."'
			where kd.KodeID = '".KodeID."'
		  and k.Lulus = 'N'
		  and kd.Tanggal = '$Tanggal'
		  and kd.NA = 'N'
		group by k.MhswID
		order by kd.Tanggal";
  }
  else // Setiap siswa hanya dijadwalkan 1 waktu untuk semua mata uji
  {	  $s = "select k.*, m.MhswID,left(m.Nama, 28) as Mhsw,m.KelasID, d.Nama as _DosenPenguji, 
			date_format(k.TanggalUjian, '%a') as _Hari, LEFT(k.JamMulai, 5) as _JamUjian, LEFT(k.JamSelesai, 5) as _JamSelesai, pa.Nama as _DosenPA
			from kompre k 
				left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
				left outer join dosen pa on pa.Login = m.PenasehatAkademik
				left outer join dosen d on d.Login = k.DosenID
			where k.KodeID = '".KodeID."'
		  and k.Lulus = 'N'
		  and k.TanggalUjian = '$Tanggal'
		  and k.NA = 'N'
		group by k.MhswID
		order by k.TanggalUjian";
  }
  $r = _query($s);
  $n = 0;

  $jum = _num_rows($r);
  while ($w = _fetch_array($r)) {
  	$n++;
	$MataUji = ($PilihanKompre == 'Y')? ' ('.$w['_NamaMataUji'].')' : '';
	$p->Cell(8, $t, $n, 1, 0, 'C');
	$p->Cell(30, $t, $w['MhswID'], 1, 0, 'C');
	$p->Cell(60, $t, $w['Mhsw'], 1, 0);
	$p->Cell(50, $t, $w['_DosenPA'], 1, 0);
	$p->Cell(50, $t, $w['_DosenPenguji'].$MataUji, 1, 0);
	$p->Cell(55, $t, $w['_JamUjian'].' - '.$w['_JamSelesai'].' WIB', 1, 0, 'C');
	$p->Cell(20, $t, $w['RuangID'], 1, 0, 'C');
  }
}

function BuatFooternya($p)
{ $t= 6;
  $identitas = GetFields('identitas', 'Kode', KodeID, '*');
  $p->SetFont('Helvetica', 'B', 10);
  $p->Ln($t);
  $p->Ln($t);
	$p->Cell(175);
	$p->Cell(50, $t, $identitas['Kota'].', '.GetDateInWords(date('Y-m-d')), 0);
	$p->Ln($t);
	
	$BAAK = GetFields('pejabat', "KodeJabatan='KABAA' and KodeID", KodeID, "*");
	$Ketua = GetFields('pejabat', "KodeJabatan='KETUA' and KodeID", KodeID, "*");
	$p->Cell(175, $t, $BAAK['Jabatan'], 0, 0);
	$p->Cell(50, $t, $Ketua['Jabatan'], 0, 0);
	$p->Ln($t);
	$p->Ln($t);
	$p->Ln($t);
	$p->Ln($t);
	
	$p->Cell(175, $t, $BAAK['Nama'], 0, 0);
	$p->Cell(50, $t, $Ketua['Nama'], 0, 0);
}
function JudulKolomnya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(8, $t, 'No.', 1, 0, 'C');
  $p->Cell(30, $t, 'N I M', 1, 0, 'C');
  $p->Cell(60, $t, 'Mahasiswa', 1, 0, 'C');
  $p->Cell(50, $t, 'Pembimbing', 1, 0, 'C');
  $p->Cell(50, $t, 'Penguji', 1, 0, 'C');
  $p->Cell(55, $t, 'Waktu', 1, 0, 'C');
  $p->Cell(20, $t, 'Ruang', 1, 0, 'C');
  $p->Ln($t);
}
function HeaderLogo($jdl, $p, $orientation='P', $jdl2='')
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
	$p->SetFont("Helvetica", 'B', 16);
	$p->Cell(20, 7, '', 0, 0);
    $p->Cell($pjg, 7, $jdl, 0, 1, 'C');
	
    $p->SetFont("Helvetica", 'I', 6);
	$p->Cell($pjg, 3,$identitas['Alamat1'], 0, 0, 'C');
	$p->SetFont("Helvetica", 'B', 16);
	$p->Cell(20);
	$p->Cell($pjg, 7, $jdl2, 0, 1, 'C');
	$p->SetFont("Helvetica", 'I', 6);
    $p->Cell($pjg, 3,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'], 0, 1, 'C');
    $p->Ln(3);
	if($orientation == 'L') $length = 275;
	else $length = 190;
    $p->Cell($length, 0, '', 1, 1);
    $p->Ln(2);
}
?>
