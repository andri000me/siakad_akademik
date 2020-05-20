<?php
error_reporting(E_ALL);
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
$BIPOTNamaID = GetSetVar('BIPOTNamaID');

// *** Main ***
$gos = (empty($_REQUEST['gosx']))? 'KonfirmasiAkun' : $_REQUEST['gosx'];
$gos();

// *** Functions ***
function KonfirmasiAkun() {
  $optakun = GetOption2('bipotnama', "Nama",
    'Urutan', $_SESSION['BIPOTNamaID'], "KodeID='".KodeID."'", 'BIPOTNamaID');
  $skrg = date('Y-m-d H:i');
  echo <<<ESD
  <h1 align=center>Laporan Pembayaran Per Akun</h1>
  <table class=box cellspacing=1 align=center>
  <form action='../$_SESSION[mnux].bayarakun.php' method=POST>
  <input type=hidden name='gosx' value='Cetak' />
  <input type=hidden name='TahunID' value='$_SESSION[TahunID]' />
  <input type=hidden name='ProdiID' value='$_SESSION[ProdiID]' />
  <input type=hidden name='Sekarang' value='$skrg' />
  
  <tr><td class=inp>Akun:</td>
      <td class=ul>
        <select name='BIPOTNamaID'>$optakun</select>
      </td>
      </tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='btnCetak' value='Cetak' />
      <input type=button name='btnBatal' value='Batal'
        onClick="window.close()" />
      </td></tr>
  </form>
  </table>
ESD;
}
function Cetak() {
  $TahunID = $_SESSION['TahunID'];
  $ProdiID = $_SESSION['ProdiID'];
  $BIPOTNamaID = $_SESSION['BIPOTNamaID'];
    // *** Init PDF
  $pdf = new PDF();
  $pdf->SetTitle("Laporan Pembayaran Per Akun");

  BuatIsinya($TahunID, $ProdiID, $BIPOTNamaID, $pdf);

  $pdf->Output();
}
function BuatIsinya($TahunID, $ProdiID, $BIPOTNamaID, $p) {
  $lbr = 190;
  $whr_prd = ($ProdiID == '')? '' : "and m.ProdiID = '$ProdiID' ";
  $s = "select b2.*, b.MhswID, b.Keterangan, b.RekeningID,
      date_format(b.Tanggal, '%d-%m-%Y') as TGL,
      m.Nama as NamaMhsw, m.ProdiID,
      format(b2.Jumlah, 0) as JML
    from bayarmhsw2 b2
      left outer join bayarmhsw b on b2.BayarMhswID = b.BayarMhswID
      left outer join mhsw m on b.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where b.KodeID = '".KodeID."'
      and b.TahunID = '$TahunID'
      and b2.BIPOTNamaID = '$BIPOTNamaID'
      and b.NA = 'N'
      and b2.NA = 'N'
      $whr_prd
    order by m.ProdiID, b.Tanggal, b.MhswID";
  $r = _query($s);
  $n = 0; $t = 5; $_prd = 'lkasdfjasd;'; $awal = $_prd;
  $ttl = 0;
  while ($w = _fetch_array($r)) {
    if ($_prd != $w['ProdiID']) {
      if ($awal != $w['ProdiID']) BuatFooterTabel($ttl, $p);
      $_prd = $w['ProdiID'];
      BuatHeaderTabel($TahunID, $w['ProdiID'], $BIPOTNamaID, $p);
      $n = 0;
      $ttl = 0;
    }
    $n++;
    $ttl += $w['Jumlah'];
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(20, $t, $w['TGL'], 'B', 0);
    $p->Cell(25, $t, $w['MhswID'], 'B', 0);
    $p->Cell(70, $t, $w['NamaMhsw'], 'B', 0);
    $p->Cell(30, $t, $w['JML'], 'BR', 0, 'R');
    
    $p->Ln($t);
  }
  BuatFooterTabel($ttl, $p);
}
function BuatFooterTabel($ttl, $p) {
  $_ttl = number_format($ttl);
  $t = 5;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(125, $t, 'Total :', 0, 0, 'R');
  $p->Cell(30, $t, $_ttl, 0, 0, 'R');
}
function BuatHeaderTabel($TahunID, $ProdiID, $BIPOTNamaID, $p) {
  $p->AddPage();
  $Nama = GetaField('bipotnama', 'BIPOTNamaID', $BIPOTNamaID, 'Nama');
  $Prodi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');
  // Judul besar
  $lbr = 190; $t = 5;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Laporan Pembayaran $Nama", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Tahun Akademik: $TahunID", 0, 1, 'C');
  $p->Cell($lbr, $t, "Program Studi: $Prodi", 0, 1, 'C');
  $p->Ln(2);
  // Buat header tabel
  $t = 5;
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(20, $t, 'Tanggal', 1, 0);
  $p->Cell(25, $t, 'N.P.M', 1, 0);
  $p->Cell(70, $t, 'Mahasiswa', 1, 0);
  $p->Cell(30, $t, 'Jumlah', 1, 0, 'R');
  $p->Ln($t);
}
?>
