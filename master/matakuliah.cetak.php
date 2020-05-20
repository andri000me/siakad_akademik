<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb2.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$kurid = GetSetVar('k');
$kur = GetFields('kurikulum', 'KurikulumID', $kurid, '*');
if (empty($kur))
  die(ErrorMsg('Error',
    "Kurikulum tidak ditemukan.<br />
    Hubungi sysadmin utk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));

$pdf = new TCPDF();
$pdf->SetTitle("Matakuliah");
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'B', 14);

CetakHeadernya($kur, $pdf);
CetakMatakuliahnya($kur, $pdf);

$pdf->Output();

// *** Functions ***
function CetakHeadernya($kur, $p) {
  $lbr = 190; $t = 6;
  $_prd = GetaField('prodi', "ProdiID='$kur[ProdiID]' and KodeID", KodeID, 'Nama');
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Daftar Matakuliah Kurikulum: $kur[Nama]", 0, 1, 'C');
  $p->Cell($lbr, $t, "Program Studi: $_prd", 0, 1, 'C');
  $p->Ln(3);
}
function CetakMatakuliahnya($kur, $p) {
  $s = "select mk.*
    from mk
    where mk.KurikulumID = $kur[KurikulumID] and Sesi > 0
    order by mk.Sesi, mk.MKKode";
  $r = _query($s);
  
  $n = 0; $t = 6; $ss = -25;
  $ttl = 0;
  while ($w = _fetch_array($r)) {
    if ($ss != $w['Sesi']) {
      if ($ss != -25) {
        $p->SetFont('Helvetica', '', 10);
        $p->Cell(180, $t, 'Jumlah SKS: ', 'LB', 0, 'R');
        $p->Cell(10, $t, $ttl, 'BR', 1, 'R');
        $ttl = 0;
      } 
      $ss = $w['Sesi'];
      $p->SetFont('Helvetica', 'B', 10);
      $p->Ln(2);
      $p->Cell(190, $t+2, $kur['Sesi']. " : " . $w['Sesi'], 1, 1);
      BuatHeaderTabel($p);
    }
    $n++;
	$Pras='';$Prasyarat='';$_Pras='';
	$Prasyarat = GetaField("mkpra", "MKID", $w['MKID'], "MKPra");
	$Pras = GetaField("mk", "NA='N' AND MKKode", $Prasyarat, "Nama");
	$_Pras = $Pras;
    $ttl += $w['SKS'];
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(10, $t, $n, 1, 0);
    $p->Cell(30, $t, $w['MKKode'], 1, 0);
    $p->Cell(130, $t, $w['Nama'], 1, 0);
    $p->Cell(10, $t, $w['SKS'], 1, 0);
    $p->Cell(10, $t, '', 1, 0, 'R');
    
    $p->Ln($t);
  }
}
function BuatHeaderTabel($p) {
  $t = 5;
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell(10, $t, 'Nmr.', 1, 0);
  $p->Cell(30, $t, 'Kode MK', 1, 0);
  $p->Cell(130, $t, 'Nama Matakuliah', 1, 0);
  $p->Cell(10, $t, 'SKS', 1, 0);
  $p->Cell(10, $t, 'Nilai', 1, 0, 'R');
  $p->Ln($t);
}
?>
