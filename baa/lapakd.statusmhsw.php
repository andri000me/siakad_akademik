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
$sta = GetSetVar('sta');

// *** Init PDF
$pdf = new PDF();
$pdf->SetTitle("Laporan Status Mahasiswa Per Prodi");
$lbr = 190;

//BuatHeadernya($TahunID, $ProdiID, $sta, $pdf);
BuatIsinya($TahunID, $ProdiID, $sta, $pdf);

$pdf->Output();

// *** Functions ***
function BuatHeadernya($TahunID, $ProdiID, $sta, $p) {
  global $lbr;
  $status = GetaField('statusmhsw', 'StatusMhswID', $sta, 'Nama');
  $t = 7;
  $p->AddPage();
  
  $_prodi = GetaField('prodi', "ProdiID = '$ProdiID' and KodeID", KodeID, "Nama");
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Daftar Mahasiswa Berstatus " . $status . " -- $TahunID", 0, 1, 'C');
  $p->Cell($lbr, $t, " Program Studi: " . $_prodi, 0, 1, 'C');
  $p->Ln(4);
  
  $t = 7;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(15, $t, 'Nmr.', 1, 0);
  $p->Cell(28, $t, 'N P M', 1, 0);
  $p->Cell(65, $t, 'Nama Mahasiswa', 1, 0);
  $p->Cell(20, $t, 'Angkatan', 1, 0);
  $p->Cell(60, $t, 'Penasehat Akademik', 1, 0);
  $p->Ln($t);
}
function BuatIsinya($TahunID, $ProdiID, $sta, $p) {
  $whr_prodi = ($ProdiID == '')? '' : "and h.ProdiID = '$ProdiID' ";
  $s = "select h.MhswID, h.StatusMhswID, m.Nama,
      h.ProdiID, h.ProgramID, m.TahunID as ANGK,
      d.Nama as PA, d.Gelar
    from khs h
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."'
    where h.KodeID = '".KodeID."'
      and h.StatusMhswID = '$sta'
      $whr_prodi
	  and h.TahunID = '$_SESSION[TahunID]'
    group by h.MhswID
	order by h.ProdiID, h.MhswID
	";
  $r = _query($s);
  $t = 6; $n = 0;
  $_prodi = ';alskdjfa;lsdjf;laksdjf;asldkjfa';
  
  while ($w = _fetch_array($r)) {
    if ($_prodi != $w['ProdiID']) {
      $n = 0;
      $_prodi = $w['ProdiID'];
      // Tampilkan
      BuatHeadernya($TahunID, $_prodi, $sta, $p);
    }
    $PA = (empty($w['PA']))? '(Belum diset)' : $w['PA'] . ", " . $w['Gelar'];
    $n++;
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(15, $t, $n, 'LB', 0);
    $p->Cell(28, $t, $w['MhswID'], 'B', 0);
    $p->Cell(65, $t, ucwords(strtolower($w['Nama'])), 'B', 0);
    $p->Cell(20, $t, $w['ANGK'], 'B', 0);
    $p->Cell(60, $t, $PA, 'BR', 0);
    $p->Ln($t);
  }
}
?>
