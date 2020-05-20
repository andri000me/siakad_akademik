<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$khsid = $_REQUEST['khsid'];
$tahunid=$_REQUEST['tahunid'];
$khs = GetFields("khs", "KHSID", $khsid, "*");
if (empty($khs))
  die(ErrorMsg("Error",
    "Data mahasiswa tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

$mhsw = GetFields("mhsw m
  left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."' ",
  "m.KodeID='".KodeID."' and m.MhswID", $khs['MhswID'],
  "m.MhswID, m.Nama, m.PenasehatAkademik, m.StatusAwalID, m.StatusMhswID,
  m.TotalSKS,
  if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Gelar1, ' ',d.Nama, ', ', d.Gelar)) as PA");

$lbr = 190;

$pdf = new PDF();
$pdf->SetTitle("Kartu Rencana Studi");
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell($lbr, 9, "Kartu Rencana Studi", 0, 1, 'C');

// Buat header dulu
BuatHeader($khs, $mhsw, $pdf);
// Tampilkan datanya
AmbilKRS($khs, $mhsw, $pdf);
// Buat footer
$pdf->Cell($lbr, 1, '', 1, 1);
BuatFooter($khs, $mhsw, $pdf);

$pdf->Output();

// *** Functions ***
function BuatFooter($khs, $mhsw, $p) {
  global $arrID;
  $t = 6;
  // Yang diambil
  $p->Cell(98, $t, "Jumlah SKS yang diambil / Beban SKS:", 'LB', 0, 'R');
  $p->Cell(10, $t, GetaField("krs","MhswID='$khs[MhswID]' and NA='N' AND TahunID",$khs['TahunID'],"sum(SKS)")." / ".$khs['MaxSKS'], 'B', 0, 'C');
  $p->Cell(82, $t, ' ', 'BR', 1);
  // Yang sudah ditempuh
  //$p->Cell(98, $t, "Jumlah SKS yang telah ditempuh:", 'LB', 0, 'R');
 // $p->Cell(10, $t, $khs['TotalSKS'], 'B', 0, 'C');
 // $p->Cell(82, $t, ' ', 'BR', 1);
  // Tanda tangan
  $pjbt = GetFields('pejabat', "KodeID='".KodeID."' and KodeJabatan", 'PUKET1', "*");
  $p->Ln(4);
  $p->Cell(10);
  $p->Cell(50, $t, $arrID['Kota'] . ", " . date('d M Y'), 0, 1);
  $p->SetFont('Helvetica', '', 8);
  $p->Cell(10);
  $p->Cell(50, $t, "Mengetahui,", 0, 0);
  $p->Cell(60);
  $p->Cell(50, $t, "Mahasiswa," , 0, 1);
  
  $fn = "../ttd/$pjbt[KodeJabatan].ttd.gif";
  if (file_exists($fn)) {
    $p->Cell(22);
    $p->Image($fn, null, null, 20);
    $p->Ln(2);
  }
  else $p->Ln(15);

  $p->Cell(10);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(50, $t, $mhsw['PA'], 0, 0);
  $p->Cell(60);
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(50, $t, $mhsw['Nama'], 0, 1);
  
  $p->Cell(10);
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(50, 2, 'Pembimbing Akademik', 0 , 0);
  $p->Ln(8);

  $p->Cell(170, 9, "Disahkan BAAK,", 0, 1, 'C');
  $p->Ln(24);
  $p->Cell(1);
  $p->Cell(170, 9, "(___________________________)", 0, 1, 'C');
  $p->Ln(4);
 
  $p->Ln(30);
  $p->Cell(200, 9, "_____________________________________Tanggal Cetak Krs : ".date('d M Y'), 0, 1, 'C');
  $p->Ln(50);

  $p->Cell(1);
  $p->SetFont('Helvetica', '', 9);
  $PesanLogin = GetFields('pesanlogin', 'PesanID', '1', '*');
  $p->Cell(170, 9, $PesanLogin['KRS'], 0, 0);
}
function AmbilKRS($khs, $mhsw, $p) {
  // Buat headernya dulu
  $p->SetFont('Helvetica', 'B', 9);
  $t = 6;
  
  $p->Cell(8, $t, 'No', 1, 0);
  //$p->Cell(14, $t, 'Hari', 1, 0);
  $p->Cell(15, $t, 'Kode MK', 1, 0, 'C');
  $p->Cell(65, $t, 'Matakuliah', 1, 0);
  $p->Cell(10, $t, 'SKS', 1, 0, 'C');
  $p->Cell(40, $t, 'Dosen Pengajar', 1, 0);
  $p->Cell(22, $t, 'Jam', 1, 0, 'C');
  $p->Cell(30, $t, 'Ruang/Kelas', 1, 1, 'C');

  // Ambil Isinya
  $s = "select kl.Nama as NMKelas,k.KRSID, k.Nama, k.MKID, k.MKKode, m.SKS, j.NamaKelas, j.RuangID, j.JadwalID,
      LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
      h.Nama as HR, j.DosenID,
      left(k.Nama, 40) as MK,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', left(concat(d.Gelar1, ' ', d.Nama, ', ', d.Gelar), 25)) as DSN,
	  jj.Nama as _NamaJenisJadwal, jj.Tambahan
    from krs k
      left outer join jadwal j on j.JadwalID = k.JadwalID
	  left outer join mk m on m.MKID = j.MKID
      left outer join hari h on h.HariID = j.HariID
	  left outer join kelas kl on kl.KelasID = j.NamaKelas
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
	  left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
    where k.KHSID = $khs[KHSID]
    order by j.HariID, j.JamMulai ";
  $r = _query($s);
  $n = 0; $_h = 'akjsdfh';
  $t = 6;
  $p->SetFont('Helvetica', '', 8);
  while ($w = _fetch_array($r)) {
    $n++;
    if ($_h != $w['HR']) {
      $_h = $w['HR'];
	  $hr = $w['HR'];
	  
	  $p->SetFont('Helvetica', 'B', 8);
	  $p->Cell(190, $t, $w['HR'], 'LBR', 1, 'L');
	  $p->SetFont('Helvetica', '', 7);
    } //else $hr = '-';
    //function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
    $p->Cell(8, $t, $n, 'LB', 0, 'R');
    //$p->Cell(14, $t, $hr, 'B');
    //$update = _query("UPDATE krs set SKS = '$w[SKS]' where JadwalID='$w[JadwalID]' and JadwalID != '' and JadwalID is not NULL");
    $p->Cell(15, $t, $w['MKKode'], 'B', 0, 'C');
    $TagTambahan = ($w['Tambahan'] == 'Y')? "( $w[_NamaJenisJadwal] )" : "";
	$p->Cell(65, $t, $w['MK'].' '.$TagTambahan, 'B');
    $p->Cell(10, $t, $w['SKS'], 'B', 0, 'C');
    $p->Cell(40, $t, $w['DSN'], 'B');
    $p->Cell(22, $t, $w['JM'] . ' - ' . $w['JS'], 'B', 0, 'C');
	$p->Cell(30, $t, $w['RuangID'].'/'.$w['NMKelas'], 'BR', 1, 'C');
  }
}
function BuatHeader($khs, $mhsw, $p) {
  $prodi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $khs['ProdiID'], 'Nama');
  $prg   = GetaField('program', "KodeID='".KodeID."' and ProgramID", $khs['ProgramID'], 'Nama');
  $thnakd = mysql_query("select t.Nama
    from khs k,tahun t
        where k.KHSID = $khs[KHSID] and k.TahunID=t.TahunID");
	while ($thns = mysql_fetch_array($thnakd)) {
	$thn=$thns[Nama];
	}
  $data = array();
  $data[] = array('Nama', ':', $mhsw['Nama'], 'Tahun Akademik', ':', $thn);
  $data[] = array('NPM', ':', $mhsw['MhswID'], 'Program Studi', ':', $prodi);
  $data[] = array('Dosen PA', ':', $mhsw['PA'], 'Prg Pendidikan', ':', $prg);
  
  foreach ($data as $d) {
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(24, 5, $d[0], 0, 0);
    $p->Cell(4, 5, $d[1], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(74, 5, $d[2], 0, 0);
    
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(26, 5, $d[3], 0, 0);
    $p->Cell(4, 5, $d[4], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(50, 5, $d[5], 0, 1);
  }
  $p->Ln(2);
}
?>
