<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 14 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Proses Biaya Cama");

// *** Parameters ***
$_pmbJumlah = GetSetVar('_pmbJumlah');
$_pmbProses = GetSetVar('_pmbProses');

// *** Main ***
if ($_pmbProses <= $_pmbJumlah) JalankanProses($_pmbJumlah, $_pmbProses);
else Selesai($_pmbJumlah);

// *** Functions ***
function JalankanProses($_pmbJumlah, $_pmbProses) {
  $tmr = 1;
  include_once "../baa/mhswbaru.lib.php";

  // Tampilan proses
  echo "
  <p align=center>
  <font size=+1>$_pmbProses</font> <sup>~$_pmbJumlah</sup><br />".
    $_SESSION['_pmbPMBID_'.$_pmbProses] . "<br />".
    $_SESSION['_pmbNama_'.$_pmbProses] .
  "</p>";
  // Ambil data Cama
  $pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $_SESSION['_pmbPMBID_'.$_pmbProses], "*");
  if ($pmb['BIPOTID'] == 0) 
    SetBIPOTID($pmb);
  ProsesBIPOT2($pmb['PMBID']);
  HitungUlangBIPOTPMB($pmb['PMBID']);
  
  // Ambil BIPOT yg sudah diupdate
  $pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $_SESSION['_pmbPMBID_'.$_pmbProses], "*");
  $prodi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $pmb['ProdiID'], 'Nama');
  $smt = GetaField('semester', 'Semester', $_SESSION['_pmbSemester'], 'Nama');
  $smt = substr($smt, 0, 6);
  $smt = str_pad($smt, 6, ' ', STR_PAD_RIGHT);
  // Tuliskan ke file
  $fn = "../" . $_SESSION['_pmbFile'];
  $f = fopen($fn, 'a');
  fwrite($f,
    $_SESSION['_pmbTahun'] . '-'. $smt . '|'.
    '000000|00000|'.
    str_pad($pmb['PMBID'], 16, ' ', STR_PAD_RIGHT) . '|'.
    str_pad(substr($pmb['Nama'], 0, 21), 21, ' ', STR_PAD_RIGHT) . '|' .
    str_pad(' ', 25, ' ', STR_PAD_RIGHT) . '|' .
    str_pad($pmb['ProdiID'], 10, ' ', STR_PAD_RIGHT) . '|'.
    str_pad(substr($prodi, 0, 25), 25, ' ', STR_PAD_RIGHT) . '|'.
    substr($pmb['PMBPeriodID'], 0, 4) . '|'.
    '00000004201390002572|'.
    str_pad(' ', 20, ' ', STR_PAD_RIGHT) . '|'.
    str_pad($pmb['TotalBiaya']-$pmb['TotalBayar'], 10, '0', STR_PAD_LEFT) . '|'.
    '0000000000|0000000000|0000000000|0000000000|0000000000|0000000000|0000000000|0000000000|'.
    '01|'.
    $_SESSION['_pmbKodeInstitusi'].'|'.
    $_SESSION['_pmbKodePembayaran'].'|'.
    str_pad(substr($pmb['PMBID'], 0, 10), 10, '0', STR_PAD_LEFT) . '|'.
    'F'.
    "\n"
    );
  
  fclose($f);
  
  // Next...
  $_SESSION['_pmbProses']++;
  echo <<<SCR
    <script>
    window.onload=setTimeout("window.location='../$_SESSION[mnux].proses.php'", $tmr);
    </script>
SCR;
}

function Selesai($_pmbJumlah) {
  $namafile = basename($_SESSION['_pmbFile']);
  echo "<p align=center>
  Proses telah selesai.<br />
  Sistem telah memproses <font size=+1>$_pmbJumlah</font> data Cama.<br />
  Silakan download file Bank di:<br />
  <font size=+1><a href='../$_SESSION[_pmbFile]'>$namafile</a></font><br />
  (Klik kanan pada link tersebut dan pilih menu 'Save As...' atau 'Save target...' atau 'Save link as...')
  </p>";
  //echo "<script>parent.window.location='../index.php?mnux=$_SESSION[mnux]'</script>";
  
  echo "<p align=center>
  <input type=button name='Kembali' value='Kembali' onClick=\"parent.window.location='../index.php?mnux=$_SESSION[mnux]'\" />
  </p>";
}
?>

