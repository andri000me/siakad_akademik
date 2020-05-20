<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";
  
// *** Parameters ***
	$JadwalID = sqling($_REQUEST['JadwalID']);
	
	$jdwl = GetFields("jadwal j
		left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
		left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
		left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
		left outer join mk mk on mk.MKID = j.MKID
		left outer join kelas k on k.KelasID = j.NamaKelas
		left outer join hari hr on hr.HariID=j.HariID
		left outer join jadwaluts jut on jut.JadwalID = j.JadwalID
		left outer join jadwaluas jua on jua.JadwalID = j.JadwalID
			left outer join hari huts on huts.HariID = date_format(jut.Tanggal, '%w')
		  left outer join hari huas on huas.HariID = date_format(jua.Tanggal, '%w')
		",
		"j.JadwalID", $JadwalID,
		"k.Nama as _NamaKelas, j.*, concat(d.Gelar1, ' ', d.Nama, ', ', d.Gelar) as DSN, d.NIDN,
		prd.Nama as _PRD, prg.Nama as _PRG, mk.Sesi, mk.PerSKS,
		date_format(jua.Tanggal, '%d-%m-%Y') as _UASTanggal,
		date_format(jut.Tanggal, '%d-%m-%Y') as _UTSTanggal,
		date_format(jut.Tanggal, '%w') as _UTSHari,
		date_format(jua.Tanggal, '%w') as _UASHari,
		huts.Nama as HRUTS,
		huas.Nama as HRUAS, mk.MKKode,
		hr.Nama as HariKuliah,
		j.JamMulai, j.JamSelesai,
		LEFT(jut.JamMulai, 5) as _UTSJamMulai, LEFT(jut.JamSelesai, 5) as _UTSJamSelesai,
		LEFT(jua.JamMulai, 5) as _UASJamMulai, LEFT(jua.JamSelesai, 5) as _UASJamSelesai
		");

if (empty($jdwl))
  die(ErrorMsg("Error",
    "Data jadwal tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

$Kolom = GetaField('jadwal', "KodeID='".KodeID."' and JadwalID", $_REQUEST['JadwalID'], 'RencanaKehadiran');
$lbr = 280;
// *** Functions ***//
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetTitle("Absensi UTS - $jdwl[Nama] - $TahunID");

if ($jdwl['ProgramID']=='M' || $jdwl['PerSKS']=='N'){
	$s = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID";
}
else {
$s = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, bipotmhsw b, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND b.MhswID=k.MhswID
	AND b.TambahanNama like (concat('%',k.MKKode,'%'))
	AND b.Dibayar=(b.Jumlah*b.Besar)
	AND b.TahunID=k.TahunID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID";
}
$r = _query($s);
$n = _num_rows($r);

$no =0;
$maxentryperpage = 20;
$maxentryoflastpage = 10;
$pages = floor($n/$maxentryperpage);
$lastpageentry = $n%$maxentryperpage;
if($lastpageentry == 0)
{	$pages -= 1;
    $lastpageentry = $maxentryperpage;
}
$totalpage = $pages;
if($lastpageentry > $maxentryoflastpage) $totalpage += 2;
else $totalpage += 1;

// Buat semua halaman tanpa footer
for($i = 0; $i< $pages; $i++)
{ 	$start = $i*$maxentryperpage;
	if ($jdwl['ProgramID']=='M' || $jdwl['PerSKS']=='N'){
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
	limit $start, $maxentryperpage";
	}
	else {
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
   from khs h, bipotmhsw b, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND b.MhswID=k.MhswID
	AND b.TambahanNama like (concat('%',k.MKKode,'%'))
	AND b.Dibayar=(b.Jumlah*b.Besar)
	AND b.TahunID=k.TahunID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
		limit $start, $maxentryperpage";
	}
	$r1 = _query($s1);
	
	$pdf->AddPage('P');
	$pdf->SetAutoPageBreak(true, 3);
	// Buat header dulu
	BuatHeader($jdwl, $Kolom, $pdf);
	// Tampilkan datanya
	AmbilDetail($jdwl, $r1, $start, $Kolom, $pdf);
}

//Buat halaman terakhir dengan footer
$start = $i*$maxentryperpage;

if($lastpageentry > $maxentryoflastpage)
{	
	if ($jdwl['ProgramID']=='M' || $jdwl['PerSKS']=='N'){
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
	limit $start, $maxentryperpage";
	}
	else {
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, bipotmhsw b, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND b.MhswID=k.MhswID
	AND b.TambahanNama like (concat('%',k.MKKode,'%'))
	AND b.Dibayar=(b.Jumlah*b.Besar)
	AND b.TahunID=k.TahunID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
		limit $start, $maxentryperpage";
	}
	$r1 = _query($s1);
	$pdf->AddPage('P');
	$pdf->SetAutoPageBreak(true, 5);
	// Tampilkan datanya
	AmbilDetail($jdwl, $r1, $start, $Kolom, $pdf);
	
	$pdf->AddPage('P');
	$pdf->SetAutoPageBreak(true, 4);
	// Buat footer
	BuatFooter($jdwl, $i+2, $totalpage, $pdf);
}
else
{	
	if ($jdwl['ProgramID']=='M' || $jdwl['PerSKS']=='N'){
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
	limit $start, $maxentryperpage";
	}
	else {
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, bipotmhsw b, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND b.MhswID=k.MhswID
	AND b.TambahanNama like (concat('%',k.MKKode,'%'))
	AND b.Dibayar=(b.Jumlah*b.Besar)
	AND b.TahunID=k.TahunID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
		limit $start, $maxentryperpage";
	}
	$r1 = _query($s1);
	
	$pdf->AddPage('P');
	$pdf->SetAutoPageBreak(true, 5);
	// Tampilkan datanya
	AmbilDetail($jdwl, $r1, $start, $Kolom, $pdf);
	// Buat footer
	BuatFooter($jdwl, $pdf);
}

$pdf->Output();

function BuatFooter($jdwl, $p) {
  global $arrID;
  $t = 5;
  $p->SetFont('Helvetica', '', 7);
  $p->Cell(180, $t, "Catatan: Bagi mahasiswa yang namanya tidak terproses, silakan konfirmasi ke Administrasi Fakultas", 0 , 'L');
  $p->Ln(6);
  $p->Cell(41);
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(41, $t, "Nama", 0 , 'L');
  $p->Cell(56, $t, "Tanda Tangan", 0 , 'L');
  $p->Cell(60, $t, $arrID['Kota'] . ", " . TanggalFormat(date('Y-m-d')), 0, 1);
  $p->Cell(138);
  $p->Cell(60, $t, "Dosen Pengasuh,", 0 , 1);
  $p->Ln(13);

  
  $p->Cell(28, $t, "Pengawas:", 0 , 'L');
  $p->Cell(46, $t, "1. ____________________", 0 , 'L');
  $p->Cell(64, $t, "_______________________", 0 , 'L');
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(60, $t, $jdwl['DSN'], 0, 1);
  $p->Cell(138);
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(60, $t, 'NIDN: ' . $jdwl['NIDN'], 0, 1);
  $p->Cell(28);
  $p->Cell(46, $t, "2. ____________________", 0 , 'L');
  $p->Cell(60, $t, "_______________________", 0 , 'L');
  $p->Ln(8);
  $p->Cell(1);
  $p->Cell(190, 0, "", 1, 1);
  $p->SetFont('Helvetica', 'B', 8);


}
function AmbilDetail($jdwl, $r, $start, $Kolom, $p) {
	$t =  10;
  BuatHeaderTabel($p);
  $n = 0;
  $p->SetFont('Helvetica', '', 9);
  while ($w = _fetch_array($r)) {
    $n++;
    $p->Cell(12);
    $p->Cell(10, $t, $n, 'LTBR', 0,'C');
    $p->Cell(32, $t, $w['MhswID'], 'TBR', 0);
    $p->Cell(55, $t, $w['Nama'], 'TBR', 0);
    $p->Cell(15, $t, '', 'TBR', 0, 'C');
    $p->Cell(20, $t, '', 'TBR', 0, 'C');
    $p->Cell(35, $t, $n, 'TBR', 0, 'L');
    $p->Ln($t);
  }
}

function BuatHeaderTabel($p) {
  $t = 4;
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(12);
  $p->Cell(10, $t, 'No.', 'LTR', 0, 'C');
  $p->Cell(32, $t, 'Nomer BP', 'TR', 0);
  $p->Cell(55, $t, 'Nama Mahasiswa', 'TR', 0);
  $p->Cell(15, $t, 'Nilai', 'TR', 0, 'C');
  $p->Cell(20, $t, 'Jumlah', 'TR', 0, 'C');
  $p->Cell(35, $t, 'Tanda Tangan', 'TR', 0, 'C');
  $p->Ln($t);
  $p->Cell(12);
  $p->Cell(10, $t, ' ', 'LBR', 0);
  $p->Cell(32, $t, '', 'BR', 0);
  $p->Cell(55, $t, '', 'BR', 0);
  $p->Cell(15, $t, '(Angka)', 'BR', 0, 'C');
  $p->Cell(20, $t, 'Kehadiran', 'BR', 0, 'C');
  $p->Cell(35, $t, ' ', 'BR', 0, 'C');
  $p->Ln($t);
}
function BuatHeader($jdwl, $thn, $p) {
  $t = 5; $lbr = 180;
  $p->AddPage('P');

  $arr = array();
  $arr[] = array('Mata Kuliah', ':', $jdwl['MKKode'] . '   ' . $jdwl['Nama'], 
    'Dosen Pengasuh', ':', $jdwl['DSN']);
  $arr[] = array('Kelas / Thn Akd', ':', $jdwl['namaKelas'] . ' / ' . $thn['Nama'],
    'Program Studi', ':', $jdwl['_PRD'] . ' ('. $jdwl['_PRG'].')');
  $arr[] = array('Semester / SKS', ':', $jdwl['Sesi'] . ' / ' . $jdwl['SKS'],
    'Hari / Tgl Ujian', ':', $jdwl['HRUAS'] . 
    ' / ' . $jdwl['_UASTanggal'] .
    ' / ' . $jdwl['_UASJamMulai'] . ' - ' . $jdwl['_UASJamSelesai']);
  // Tampilkan
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, 8, 'Daftar Hadir dan Nilai Ujian Tengah Semester', 0, 1, 'C');
  $p->SetFont('Helvetica', '', 7);
  foreach ($arr as $a) {
    // Kolom 1
    $p->SetFont('Helvetica', 'I', 7);
	///*$p->Cell(12, $t, '', 0, 0);
//    $p->Cell(25, $t, $a[0], 0, 0);
//    $p->Cell(4, $t, $a[1], 0, 0, 'C');*/
    $p->SetFont('Helvetica', 'B', 7);
    //$p->Cell(70, $t, $a[2], 0, 0);
	$p->writeHTMLCell(25, $t, 12, '', $a[1], 0, 0, '', true, 'L', false);
	$p->writeHTMLCell(1, $t, 3, '', $a[1], 0, 0, '', true, 'L', false);
	$p->writeHTMLCell(70, $t, 30, '', $a[2], 0, 0, '', true, 'L', false);
    // Kolom 2
    $p->SetFont('Helvetica', 'I', 7);
    $p->Cell(25, $t, $a[3], 0, 0);
    $p->Cell(4, $t, $a[4], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 7);
    $p->Cell(70, $t, $a[5], 0, 0);
    $p->Ln($t);
  }
  $p->Ln(4);
}