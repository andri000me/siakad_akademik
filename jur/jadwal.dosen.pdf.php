<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');

//die("$TahunID &raquo; $ProdiID &raquo; $ProgramID");

$lbr = 195;

$pdf = new PDF();
$pdf->SetTitle("Jadwal Mengajar Dosen - $TahunID");

// Buat header dulu
//BuatHeader($TahunID, $ProdiID, $pdf);
// Tampilkan datanya
AmbilJadwal($TahunID, $ProdiID, $pdf);
// Buat footer
BuatFooter($pdf);

$pdf->Output();

// *** Functions ***
function BuatFooter($p) {
  global $arrID;
  $mrg = 130;
  $t = 6;
  // Tanda tangan
  $strProdiID = '.'.$_SESSION[_jdwlProdi].'.';
  $pjbt = GetFields('pejabat', "LOCATE('$strProdiID',KodeJabatan) and KodeID",KodeID, "*");
  $p->Ln(4);
  $p->Cell($mrg);
  $p->Cell(60, $t, $arrID['Kota'] . ", " . date('d F Y'), 0, 1);
  $p->Cell($mrg);
  $p->Cell(60, $t, $pjbt['Jabatan'], 0 , 1);
  $p->Ln(10);

  $p->Cell($mrg);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(60, $t, $pjbt['Nama'], 0, 1);
  $p->Cell($mrg);
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(60, $t, 'NIDN : ' . $pjbt['NIP'], 0, 1);
}
function HeaderTabel($p) {
  $p->SetFont('Helvetica', 'B', 9);
  $t = 6;
  $p->Cell(8, $t, 'No', 'LBT', 0);
  $p->Cell(12, $t, 'Hari', 'BT', 0);
  $p->Cell(18, $t, 'Jam', 'BT', 0);

  $p->Cell(18, $t, 'Kode MK', 'BT', 0);
  $p->Cell(70, $t, 'Matakuliah', 'BT', 0);
  $p->Cell(8, $t, 'SKS', 'BT', 0);
  $p->Cell(25, $t, 'Kelas', 'BT', 0, 'C');
  $p->Cell(25, $t, 'Ruang', 'BT', 0);
  $p->Cell(11, $t, 'UAS', 'BTR', 0);
  $p->Ln($t);
}
function AmbilJadwal($TahunID, $ProdiID, $p) {
  global $lbr;
  $NamaTahun = NamaTahun($TahunID);
  // Ambil Isinya
  $s = "select k.Nama as NamaKelasID, j.*,
      j.Nama as MK,
      h.Nama as HR, 
      LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Gelar1, ' ', d.Nama, ', ', d.Gelar)) as DSN,
      date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
      date_format(j.UASTanggal, '%w') as _UASHari,
      huas.Nama as HRUAS, prg.Nama as _PRG,
	  if (j.JadwalRefID != 0,'(LAB)','') as _lab,
      LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai
    from jadwal j
      left outer join hari h on h.HariID = j.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
      left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
	  left outer join kelas k on k.KelasID=j.NamaKelas
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$TahunID'
      and j.ProdiID = '$ProdiID'
    order by d.Nama, j.ProgramID, j.HariID, j.JamMulai";
  $r = _query($s);
  //die("<pre>$s</pre>");
  $n = 0; $_h = 'akjsdfh'; $_d = 'la;skdjfadshg'; $_prg = ';laskdjfl';
  $t = 6;

  while ($w = _fetch_array($r)) {
    if ($_d != $w['DSN']) {

      if ($n > 0) BuatFooter($p);
      $p->AddPage();
      $p->SetFont('Helvetica', '', 12);
      $p->Cell($lbr, 8, "Jadwal Mengajar Dosen - Semester $NamaTahun", 0, 1, 'C');
      $_d = $w['DSN'];
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell($lbr, 8, $_d, 0, 1, 'C');
      // Reset
      $n = 0;
      $_prg = ';ladskfj;asdl';
    }
    $n++;
    if ($_prg != $w['_PRG']) {
      $_prg = $w['_PRG'];
	  $p->SetFont('Helvetica', 'B', 10);
      $p->Cell($lbr, 8, $w['_PRG'], 1, 1);
      HeaderTabel($p);
    }

    $p->SetFont('Helvetica', '', 7);
    $p->Cell(8, $t, $n, 'LB', 0, 'C');
    $p->Cell(12, $t, $w['HR'], 'B');
    $p->Cell(18, $t, $w['JM'] . ' - ' . $w['JS'], 'B');
    $p->Cell(18, $t, $w['MKKode'], 'B');
    $p->Cell(70, $t, $w['MK'].' '.$w[_lab], 'B');
    $p->Cell(8, $t, $w['SKS'], 'B', 0, 'C');
    $p->Cell(25, $t, $w['NamaKelasID'], 'B', 0, 'C');
    $p->Cell(15, $t, $w['RuangID'], 'B', 0, 'C');
    $p->Cell(5, $t, $w['HRUAS'], 'B', 0);
    $p->Cell(16, $t, $w['_UASTanggal'], 'BR', 0);
    $p->Ln($t);
  }
}
function BuatHeader($TahunID, $ProdiID, $p) {
  $NamaTahun = NamaTahun($TahunID);
  $p->SetFont('Helvetica', 'B', 10);
  
  $prodi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $thn['ProdiID'], 'Nama');
  $prg   = GetaField('program', "KodeID='".KodeID."' and ProgramID", $thn['ProgramID'], 'Nama');

  $p->Cell(90, 6, "Thn Akd.: " . $thn['Nama'], 0, 0);
  $p->Cell(90, 6, "Prg Studi: " . $prodi, 0, 0);
  $p->Cell(90, 6, "Prg Pendidikan: " . $prg, 0, 1);
  
  $p->Ln(2);
}
?>
