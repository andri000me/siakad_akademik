<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$Angkatan = GetSetVar('Angkatan', date('Y'));
$ProgramID = GetSetVar('_khsProgramID');
$MhswID = sqling($_REQUEST['MhswID']);
if (!empty($MhswID)) {
  $whr_mhsw = "and h.MhswID = '$MhswID' ";
}
$TahunID = GetaField("tahun", "ProdiID='$ProdiID' AND ProgramID='$ProgramID' AND NA='N' AND KodeID", KodeID, "TahunID");



// Init PDF
$pdf = new PDF();
$pdf->SetTitle("Kartu UAS Mahasiswa");
$pdf->SetFillColor(200, 200, 200);

// *** Main ***
$s = "select distinct(h.KHSID) as KHSID, h.MhswID, m.Nama, h.IP, h.IPS,
      h.TahunID, m.ProgramID, m.ProdiID,
      prd.Nama as _PRD, prg.Nama as _PRG, t.Nama as _THN,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Nama, ', ', d.Gelar)) as _PA
    from khs h
      left outer join prodi prd on prd.ProdiID = h.ProdiID and prd.KodeID = '".KodeID."'
      left outer join tahun t on t.TahunID = h.TahunID and t.ProdiID = h.ProdiID and t.KodeID = '".KodeID."'
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
	  left outer join program prg on prg.ProgramID = m.ProgramID and prg.KodeID = '".KodeID."'
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."'
    where h.TahunID = '$TahunID'
      and h.ProdiID = '$_SESSION[ProdiID]'
	  and m.ProgramID = '$_SESSION[_khsProgramID]'
      $whr_mhsw
    order by h.MhswID limit 1";
$r = _query($s);
  
while ($w = _fetch_array($r)) {
	BuatHeaderKHS($w, $pdf);
  	BuatIsinya($w, $pdf);
}

$pdf->Output();


// *** Functions ***
function BuatFooter($khs, $jml, $sks, $p) {
  global $arrID;
  $MaxSKS = GetaField('maxsks',
    "KodeID='".KodeID."' and NA = 'N'
    and DariIP <= $khs[IPS] and $khs[IPS] <= SampaiIP and ProdiID", 
    $khs['ProdiID'], 'SKS')+0;
  // Pejabat
  $pjbt = GetFields('prodi', "KodeID='".KodeID."' and ProdiID", $khs['ProdiID'], "*");
  // Array Isi
  $tgl = TanggalFormat(date('Y-m-d'));
  $arr = array();
  $arr[] = array('Jumlah Matakuliah yg Diambil', ':', $jml, $arrID['Kota'].', '.$tgl);
  $arr[] = array('Jumlah SKS yg Ditempuh', ':', $sks,$pjbt['Jabatan']);
  $arr[] = array('', '', '', '');
  $arr[] = array('', '', '', 'dto');
  $arr[] = array('~IMG~');
  $arr[] = array('CATATAN:', '', '', $pjbt['Pejabat']);
  //$arr[] = array('1. Kartu ujian ini harus dibawa setiap mengikuti ujian', '', '', 'NIDN: '.$pjbt['NIP']);
  //$arr[] = array('2. Mahasiswa yang tidak dapat memperlihatkan kartu ujian - ');
  //$arr[] = array('   pada saat ujian berlangsung wajib melapor ke Administrasi Fakultas - ');
  //$arr[] = array('   untuk dicetak ulang dan membayar Denda Rp 20.000,-');
  
  
  // Tampilkan
  $p->Ln(2);
  $t = 5;
  $p->SetFont('Helvetica', '', 9);
  foreach ($arr as $a) {
    if ($a[0] == '~IMG~') {
      $fn = "../ttd/$pjbt[KodeJabatan].ttd.gif";
      if (file_exists($fn)) {
        $p->Cell(132);
        $p->Image($fn, null, null, 20);
        $p->Ln(1);
      }
      else $p->Ln($t+2);
    }
    else {
      $p->Cell(50, $t, $a[0], 0, 0);
      $p->Cell(2, $t, $a[1], 0, 0, 'C');
      $p->Cell(30, $t, $a[2], 0, 0);
      $p->Cell(48, $t, '', 0, 0);
      $p->Cell(63, $t, $a[3], 0, 0);
      $p->Ln($t);
    }
  }
}
function BuatIsinya($khs, $p) {
  global $arrHari;
  BuatHeaderDetail($p);
  $s = "select k.MKKode, k.SKS, j.NamaKelas, k.KRSID,
      left(k.Nama, 40) as MKNama,
      d.Gelar1, d.Nama as DSN, d.Gelar as GLR,
      dayofweek(jt.Tanggal) as HRUAS,
      date_format(jt.Tanggal, '%d-%m-%y') as TGLUAS,
	  j.MaxAbsen,j.JadwalID
    from krs k
      left outer join jadwal j on j.JadwalID = k.JadwalID
	  left outer join jadwaluas jt on jt.JadwalID = j.JadwalID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    where k.KHSID = $khs[KHSID]
    order by j.UASTanggal, k.MKKode";
  $r = _query($s);
  $t = 8;
  $n = 0; $l = 'TB'; $_sks = 0;
  $p->SetFont('Helvetica', '', 7);
  $kdmk='';
  while ($w = _fetch_array($r)) {
	 
    $HitungMangkir = GetaField('presensimhsw p 
									left outer join presensi p2 on p.PresensiID=p2.PresensiID
									left outer join jadwal j on j.JadwalID=p.JadwalID
									left outer join jenispresensi jp on p.JenisPresensiID=jp.JenisPresensiID',
						"p.KRSID='$w[KRSID]' and p2.Pertemuan <= (j.RencanaKehadiran/2) and jp.Nilai", 0, "count(p.PresensiID)");
	$J = GetFields("jadwaluas js,uasmhsw u","u.JadwalUASID=js.JadwalUASID and u.MhswID='$khs[MhswID]' and js.JadwalID",$w[JadwalID],'left(js.JamMulai,5) as JAM,js.RuangID as Ruang');

	if ($kdmk != $w['MKKode']) 
		{
		$kdmk=$w['MKKode'];
		
	if($HitungMangkir <= $w['MaxAbsen'])
	{
		$n++;
		$_sks += $w['SKS'];
		$p->SetFont('Helvetica', '', 7);
		$p->Cell(20, $t, $w['MKKode'], $l, 0);
		$p->Cell(65, $t, $w['MKNama'], $l, 0);
		$p->Cell(8, $t, $w['SKS'], $l, 0, 'C');
		$p->Cell(45, $t, $w['Gelar1'] . ' ' . $w['DSN'] . ', '. $w['GLR'], $l, 0);
		$p->Cell(10, $t, $arrHari[$w['HRUAS']-1], $l, 0);
		$p->Cell(20, $t, $w['TGLUAS'].' '.$J['JAM'], $l, 0);
		$p->Cell(15, $t, $J['Ruang'], $l, 0, 'L');
		$p->Cell(10, $t, '...', $l, 0, 'C');
		$p->Ln($t);
	}
	else
	{	$p->SetFont('Helvetica', '', 7);
		$p->Cell(20, $t, $w['MKKode'], $l, 0, '', true);
		$p->Cell(65, $t, $w['MKNama'], $l, 0, '', true);
		$p->Cell(8, $t, $w['SKS'], $l, 0, 'C', true);
		$p->Cell(45, $t, $w['DSN'] . ', '. $w['GLR'], $l, 0, '', true);
		$p->SetFont('Helvetica', 'B', 7);
		$p->Cell(55, $t, "TIDAK MEMENUHI PERSYARATAN", $l, 0, 'C', true);
		$p->Ln($t);
	}
	}
	
  }
  BuatFooter($khs, $n, $_sks, $p);
}
function BuatHeaderDetail($p) {
  $t = 6; $l = 'BT';
  $p->SetFont('Helvetica', 'B', 7);
  $p->Cell(20, $t, 'Kode', $l, 0);
  $p->Cell(65, $t, 'Mata Kuliah', $l, 0);
  $p->Cell(8, $t, 'SKS', $l, 0);
  $p->Cell(45, $t, 'Dosen Pengasuh', $l, 0);
  $p->Cell(10, $t, 'H.UAS', $l, 0);
  $p->Cell(20, $t, 'Tgl/Jam', $l, 0,'C');
  $p->Cell(15, $t, 'Ruang', $l, 0);
  $p->Cell(10, $t, 'P.PWS', $l, 0);
  $p->Ln($t);
}
function BuatHeaderKHS($khs, $p) {
  global $lbr;
  
  $p->AddPage();
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, 8, "Kartu Ujian Akhir Semester", 0, 1, 'C');
  // parameter
  $prodi = $khs['_PRD'];
  $prg   = $khs['_PRG'];
  $thn   = $khs['_THN'];
  
  $data = array();
  $data[] = array('Nama', ':', $khs['Nama'], 'Tahun Akademik', ':', $thn);
  $data[] = array('NPM', ':', $khs['MhswID'], 'Program Studi', ':', $prodi);
  $data[] = array('Dosen PA', ':', $khs['_PA'], 'Prg Pendidikan', ':', $prg);
  // Tampilkan
  foreach ($data as $d) {
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(20, 5, $d[0], 0, 0);
    $p->Cell(4, 5, $d[1], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(78, 5, $d[2], 0, 0);
    
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(26, 5, $d[3], 0, 0);
    $p->Cell(4, 5, $d[4], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(50, 5, $d[5], 0, 1);
  }
  $p->Ln(2);
}
?>
