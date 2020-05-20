<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 14 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Proses Biaya Mahasiswa");

// *** Parameters ***
$_byaJumlah = GetSetVar('_byaJumlah');
$_byaProses = GetSetVar('_byaProses');

// *** Main ***
if ($_byaProses <= $_byaJumlah) JalankanProses($_byaJumlah, $_byaProses);
else Selesai($_byaJumlah);

// *** Functions ***
function JalankanProses($_byaJumlah, $_byaProses) {
  $tmr = 1;
  include_once "../keu/biayamhsw.lib.php";
  // Parameters
  $MhswID = $_SESSION['_byaMhswID_'.$_byaProses];
  $Nama = $_SESSION['_byaNama_'.$_byaProses];
  $ProdiID = $_SESSION['_byaProdiID_'.$_byaProses];
  $ProgramID = $_SESSION['_byaProgramID_'.$_byaProses];
  $BIPOTID = $_SESSION['_byaBIPOTID_'.$_byaProses];
  $TahunID = $_SESSION['_byaTahun'];
  // Tampilan proses
  $persen = ($_byaJumlah > 0)? $_byaProses/$_byaJumlah*100: 0;
  $persen = number_format($persen);
  $sisa = 100 - $persen;
  echo "
  <p align=center>
  <b>$_byaProses</b> <sup>~$_byaJumlah</sup><br />
  <img src='../img/B1.jpg' width=1 height=20 /><img src='../img/B2.jpg' width=$persen height=20 /><img src='../img/B3.jpg' width=$sisa height=20 /><img src='../img/B1.jpg' width=1 height=20 /><br />
  <font size=+2>$persen %</font>
  ".
    $_SESSION['_byaPMBID_'.$_byaProses] . "<br />".
    $_SESSION['_byaNama_'.$_byaProses] .
  "</p>";

  if ($BIPOTID == 0) 
    $BIPOTID = SetBIPOTID($mhsw, $ProdiID, $ProgramID);
  
  //ProsesBIPOT2($mhsw['MhswID']);
  //HitungUlangBIPOTPMB($mhsw['MhswID']);
  
  // Ambil BIPOT yg sudah diupdate
  $Total = VirtualBipotMhsw($MhswID, $BIPOTID);
  echo "<p align=center><font size=+2>$Total (Bipot: $BIPOTID)</font></p>";
  if ($BIPOTID > 0 && $Total > 0) {
    $_SESSION['_byaDiproses']++;
    $prodi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');
    $smt = GetaField('semester', 'Semester', $_SESSION['_byaSemester'], 'Nama');
    $smt = substr($smt, 0, 6);
    $smt = str_pad($smt, 6, ' ', STR_PAD_RIGHT);
    // Tuliskan ke file
    $fn = "../" . $_SESSION['_byaFile'];
    $f = fopen($fn, 'a');
    fwrite($f,
      $_SESSION['_byaTahun'] . '-'. $smt . '|'.
      '000000|00000|'.
      str_pad($MhswID, 16, ' ', STR_PAD_RIGHT) . '|'.
      str_pad(substr($Nama, 0, 21), 21, ' ', STR_PAD_RIGHT) . '|' .
      str_pad(' ', 25, ' ', STR_PAD_RIGHT) . '|' .
      str_pad($ProdiID, 10, ' ', STR_PAD_RIGHT) . '|'.
      str_pad(substr($prodi, 0, 25), 25, ' ', STR_PAD_RIGHT) . '|'.
      substr($TahunID, 0, 4) . '|'.
      '00000004201390002572|'.
      str_pad(' ', 20, ' ', STR_PAD_RIGHT) . '|'.
      str_pad($Total, 10, '0', STR_PAD_LEFT) . '|'.
      '0000000000|0000000000|0000000000|0000000000|0000000000|0000000000|0000000000|0000000000|'.
      '01|'.
      $_SESSION['_byaKodeInstitusi'].'|'.
      $_SESSION['_byaKodePembayaran'].'|'.
      str_pad(substr($MhswID, 0, 10), 10, '0', STR_PAD_LEFT) . '|'.
      'F'.
      "\n");
    fclose($f);
  }
  echo "<p align=center>Berhasil diproses: <b>$_SESSION[_byaDiproses]</b></p>";
  // Next...
  $_SESSION['_byaProses']++;
  echo <<<SCR
    <script>
    window.onload=setTimeout("window.location='../$_SESSION[mnux].proses.php'", $tmr);
    </script>
SCR;
}

function Selesai($_mhswJumlah) {
  $namafile = basename($_SESSION['_byaFile']);
  $Diproses = $_SESSION['_byaDiproses']+0;
  // Update header file dulu
  $fn = "../" . $_SESSION['_byaFile'];
  $f = fopen($fn, 'r');
  $length = filesize($fn);
  $length = ($length == 0)? 1 : $length;
  $isi = fread($f, $length);
  fclose($f);
  // Tulis
  $f = fopen($fn, 'w');
  $hdr = str_replace('~JML~', $Diproses, $_SESSION['_byaHeader']);
  fwrite($f, $hdr);
  fwrite($f, $isi);
  fclose($f);
  // Tampilkan
  echo "<p align=center>
  Proses telah selesai.<br />
  Sistem telah memproses <font size=+1>$Diproses dari $_mhswJumlah</font> data Mhsw.<br />
  Silakan download file Bank di:<br />
  <font size=+1><a href='../$_SESSION[_byaFile]'>$namafile</a></font><br />
  (Klik kanan pada link tersebut dan pilih menu 'Save As...' atau 'Save target...' atau 'Save link as...')
  </p>";

  echo "<p align=center>
  <input type=button name='Kembali' value='Kembali' onClick=\"parent.window.location='../index.php?mnux=$_SESSION[mnux]'\" />
  </p>";
}
?>

