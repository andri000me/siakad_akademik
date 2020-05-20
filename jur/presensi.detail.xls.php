<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../tcpdf/tcpdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');
$HariID = GetSetVar('HariID');
$lbr = 190;

// Init
$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetTitle("Detail Kehadiran Kuliah - $TahunID");
$pdf->AddPage('P');

// *** Main ***
BuatHeader($TahunID, $ProdiID, $pdf);
BuatRekap($TahunID, $ProdiID, $ProgramID, $HariID, $pdf);

$pdf->Output();

// *** Functions ***
function BuatHeader($TahunID, $ProdiID, $p) {
  global $lbr;
  $NamaTahun = NamaTahun($TahunID);
  $NamaProdi = GetaField('prodi', "KodeID = '".KodeID."' and ProdiID", $ProdiID, 'Nama');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, 6, "Rekap Kehadiran Kuliah - $NamaTahun", 0, 1, 'C');
  $p->SetFont('Helvetica', 'I', 10);
  $p->Cell($lbr, 6, "Program Studi $NamaProdi", 0, 1, 'C');
}
function BuatRekap($TahunID, $ProdiID, $ProgramID, $HariID, $p) {
  global $lbr;
  
  $whr_program = ($ProgramID == '')? '' : "and j.ProgramID = '$ProgramID' ";
  $whr_hari = ($HariID == '')? '' : "and j.HariID = '$HariID' ";
  $whr_dosen = ($_SESSION['_LevelID']==100)? "and j.DosenID='$_SESSION[_Login]'":"";

  $s = "select j.*, left(concat(d.Nama, ', ', d.Gelar), 25) as DSN,
      left(j.Nama, 22) as MKNama,
      prd.Nama as _PRD, prg.Nama as _PRG,
      mk.Sesi, h.Nama as _HR,
      left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
      date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
      date_format(j.UASTanggal, '%w') as _UASHari,
      huas.Nama as HRUAS,
      LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai, k.Nama AS namaKelas
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
      left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
      left outer join mk mk on mk.MKID = j.MKID
      left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
      left outer join hari h on h.HariID = j.HariID 
	  LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    where j.NA = 'N'
      and j.TahunID = '$TahunID'
      and j.ProdiID = '$ProdiID'
      $whr_program
      $whr_hari
      $whr_dosen
    order by j.ProgramID DESC, j.HariID, j.JamMulai, j.JamSelesai
    ";
  $r = _query($s);
  $n = 0; $t = 6;

  $prghr = ';lasdkjf;asdf';
  while ($w = _fetch_array($r)) {
    if ($prghr != $w['ProgramID'].$w['HariID']) {
      $prghr = $w['ProgramID'].$w['HariID'];
      
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell($lbr, 10, $w['_HR'] . " -- (". $w['_PRG'] . ")", 'B', 1);
      TampilkanHeaderTabel($p);
      $n = 0;
    }
    $persen = ($w['RencanaKehadiran'] == 0)? 0 : $w['Kehadiran']/$w['RencanaKehadiran']*100;
    $persen = number_format($persen, 2);
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'B', 0);
    $p->Cell(20, $t, $w['MKKode'], 'B', 0);
    $p->Cell(45, $t, $w['MKNama'], 'B', 0);
    $p->Cell(10, $t, $w['SKS'], 'B', 0, 'C');
    $p->Cell(14, $t, $w['namaKelas'], 'B', 0);
    $p->Cell(20, $t, $w['_JM'].'-'.$w['_JS'], 'B', 0);
    $p->Cell(50, $t, $w['DSN'], 'B', 0);
    $p->Cell(15, $t, $w['Kehadiran'] . "/" . $w['RencanaKehadiran'], 'B', 0, 'L');
    $p->Cell(10, $t, $persen, 'B', 0, 'L');
    $p->Ln($t);
    TampilkanDetail($w['JadwalID'], $p);
  }
}
function TampilkanDetail($JadwalID, $p) {
  $s = "select p.*, date_format(p.Tanggal, '%d-%m-%Y') as _TGL,
    left(p.JamMulai, 5) as _JM, left(p.JamSelesai, 5) as _JS,
    concat(d.Nama, ', ', d.Gelar) as _DSN
    from presensi p
      left outer join dosen d on d.Login = p.DosenID and d.KodeID = '".KodeID."'
    where p.JadwalID = '$JadwalID'
    order by p.Pertemuan";
  $r = _query($s);
  
  $t = 5; $mrg = 10;
  $p->SetFont('Helvetica', '', 7);
  $html = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">";
  while ($w = _fetch_array($r)) {
   /* $p->Cell($mrg);
    $p->Cell(7, $t, $w['Pertemuan'], 'B', 0);
    $p->Cell(16, $t, $w['_TGL'], 'B', 0);
    $p->Cell(16, $t, $w['_JM'].'-'.$w['_JS'], 'B', 0);
    $p->Cell(46, $t, $w['_DSN'], 'B', 0);*/
	$Catatan = FixStr2($w['Catatan']);
		$html .="<tr><td width=\"3%\">$w[Pertemuan]</td>
						<td width=\"9%\"> $w[_TGL]</td>
						<td width=\"10%\">$w[_JM]-$w[_JS]</td>
						<td width=\"19%\">$w[_DSN]</td>
						<td width=\"54%\">".$Catatan."</td>
				</tr>
			";
  }
  $html .="</table>";
  $p->writeHTMLCell(190, $t, 20, '', $html, 0, 1, '', true, 'J', false);
}
function TampilkanHeaderTabel($p) {
  $p->SetFont('Helvetica', 'IB', 9);
  $t = 5;
  $p->Cell(10, $t, 'No.', 'B', 0);
  $p->Cell(20, $t, 'Kode', 'B', 0);
  $p->Cell(45, $t, 'Mata Kuliah', 'B', 0);
  $p->Cell(10, $t, 'SKS', 'B', 0);
  $p->Cell(10, $t, 'Kelas', 'B', 0);
  $p->Cell(20, $t, 'Jam Kuliah', 'B', 0);
  $p->Cell(50, $t, 'Dosen Pengasuh', 'B', 0);
  $p->Cell(15, $t, 'Hadir', 'B', 0, 'R');
  $p->Cell(10, $t, 'Persen', 'B', 0, 'C');
  $p->Ln($t);
}

function CetakJadwal($JadwalID, $p) {
  TampilkanHeader($jdwl, $p);
}

function TampilkanHeader($jdwl, $p) {
  $lbr = 190;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, 6, "Rekap Kehadiran Kuliah - $jdwl[TahunID]", 1, 1, 'C');
}
?>
