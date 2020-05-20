<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 22 Agustus 2008

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
$TahunID = GetSetVar('_jdwlTahun');
$ProdiID = GetSetVar('_jdwlProdi');
$ProgramID = GetSetVar('_jdwlProg');
$_jdwlHari  = GetSetVar('_jdwlHari');
$_jdwlKelas = GetSetVar('_jdwlKelas');
$_jdwlSemester = GetSetVar('_jdwlSemester');

$thn = GetFields('tahun', "TahunID='$TahunID' and KodeID='".KodeID."' and ProdiID='$ProdiID' and ProgramID", $ProgramID, "*");

$lbr = 280;

$pdf = new FPDF('L');
$pdf->SetTitle("Formulir Rencana Studi - $TahunID");
$pdf->SetAutoPageBreak(true, 5);
$pdf->AddPage('L');
$pdf->SetFont('Helvetica', 'B', 14);
HeaderLogo("Formulir Rencana Studi", $pdf, 'L');
// Buat header dulu
BuatHeader($thn, $pdf);
// Tampilkan datanya
AmbilJadwal($thn, $pdf);
// Buat footer
BuatFooter($thn, $pdf);

$pdf->Output();

// *** Functions ***
function BuatFooter($thn, $p) {
  global $arrID;
  $mrg = 200;
  $t = 5;
  // Tanda tangan
  $p->Ln(4);
  $p->Cell($mrg);
  $p->Cell(60, $t, $arrID['Kota'] . ", ________________", 0, 1);
  $p->Ln(15);

  $p->Cell(60, $t, 'Penasehat Akademik : _________________', 0, 0);
  $p->Cell(140, $t, '', 0, 0);
  $p->Cell(60, $t, 'Mahasiswa : _________________', 0, 1);
  $p->Cell($mrg);
  $p->Cell(60, $t, ' NIM  :  _________________', 0, 1);
}
function AmbilJadwal($thn, $p) {
  // Buat headernya dulu
  $p->SetFont('Helvetica', 'B', 9);
  $t = 6;
  
  $p->Cell(10, $t, 'Ambil', 1, 0, 'C');
  $p->Cell(8, $t, 'No', 1, 0);
  $p->Cell(20, $t, 'Kode MK', 1, 0);
  $p->Cell(75, $t, 'Matakuliah', 1, 0);
  $p->Cell(9, $t, 'SKS', 1, 0);
  $p->Cell(55, $t, 'Dosen Pengajar', 1, 0);
  $p->Cell(14, $t, 'Hari', 1, 0);
  $p->Cell(22, $t, 'Jam', 1, 0);
  $p->Cell(30, $t, 'Kelas', 1, 0, 'C');
  $p->Cell(30, $t, 'Ruangan', 1, 0);
  $p->Ln($t);

  // Parameters
  $whr_hari = ($_SESSION['_jdwlHari'] == '')? '' : "and j.HariID = '$_SESSION[_jdwlHari]' ";
  $whr_kelas = ($_SESSION['_jdwlKelas'] == '')? '' : "and j.NamaKelas = '$_SESSION[_jdwlKelas]' ";
  $whr_smt  = ($_SESSION['_jdwlSemester'] == '')? '' : "and mk.Sesi = '$_SESSION[_jdwlSemester]' ";
  // Ambil Isinya
  $s = "select k.Nama as NamaKelasID, j.*,
      j.Nama as MK,
      h.Nama as HR, 
      LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Nama, ', ', d.Gelar)) as DSN,
      date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
      date_format(j.UASTanggal, '%w') as _UASHari,
      huas.Nama as HRUAS,
 	  if (j.JadwalRefID != 0,'(LAB)','') as _lab,
     LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai
    from kelas k, jadwal j
      left outer join hari h on h.HariID = j.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
      left outer join mk on mk.MKID = j.MKID
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$_SESSION[_jdwlTahun]'
	  and k.KelasID=j.NamaKelas
      and j.ProdiID = '$_SESSION[_jdwlProdi]'
      and j.ProgramID = '$_SESSION[_jdwlProg]'
      $whr_hari $whr_kelas $whr_smt
    order by j.Nama, j.HariID";
  $r = _query($s);
  //die("<pre>$s</pre>");
  $n = 0; $_h = 'akjsdfh'; $_p = 'la;skdjfadshg';
  $t = 6;

  if(_num_rows($r) > 0)
  {
	  while ($w = _fetch_array($r)) {
		$n++;
		$hr = $w['HR'];
		$p->SetFont('Helvetica', '', 8);
		$p->Cell(10, $t, '', 1, 0);
		$p->Cell(8, $t, $n, 1, 0, 'R');
		$p->Cell(20, $t, $w['MKKode'], 1);
		$p->Cell(75, $t, $w['MK'].' '.$w[_lab], 1);
		$p->Cell(9, $t, $w['SKS'], 1, 0, 'C');
		$p->Cell(55, $t, $w['DSN'], 1);
		$p->Cell(14, $t, $hr, 1);
		$p->Cell(22, $t, $w['JM'] . ' - ' . $w['JS'], 1);
		$p->Cell(30, $t, $w['NamaKelasID'], 1, 0, 'C');
		$p->Cell(30, $t, $w['RuangID'], 1, 0);
		
		$p->SetXY(13, $p->GetY()+1);
		$p->Cell(4, 4, '', 1, 0);
		$p->SetXY(10, $p->GetY()-1);
		
		$p->Ln($t);
	  }
   }
   else
   {	$p->Cell(100, $t, 'TAHUN TIDAK DITEMUKAN! Harap menghubungi KaBaa untuk mensetup tahun akademik ini', 0, 1);
   }
}
function BuatHeader($thn, $p) {
  $p->SetFont('Helvetica', 'B', 10);
  
  $prodi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $thn['ProdiID'], 'Nama');
  $prg   = GetaField('program', "KodeID='".KodeID."' and ProgramID", $thn['ProgramID'], 'Nama');
  //Header
  $p->Cell(90, 6, "Thn Akd.: " . $thn['Nama'], 0, 0);
  $p->Cell(90, 6, "Prg Studi: " . $prodi, 0, 0);
  $p->Cell(90, 6, "Prg Pendidikan: " . $prg, 0, 0);
  $p->Ln(6);
  // Filter
  $hari = ($_SESSION['_jdwlHari'] == '')? '(Semua)' : GetaField('hari', 'HariID', $_SESSION['_jdwlHari'], 'Nama');
  $kelas = ($_SESSION['_jdwlKelas'] == '')? '(Semua)' : $_SESSION['_jdwlKelas'];
  $smt = ($_SESSION['_jdwlSemester'] == '')? '(Semua)' : $_SESSION['_jdwlSemester'];
  $p->Cell(90, 6, "Hari: ". $hari, 0, 0);
  $p->Cell(90, 6, "Kelas: $kelas", 0, 0);
  $p->Cell(90, 6, "Semester: $smt", 0, 0);
  $p->Ln(6);
  
  $p->Ln(2);
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
