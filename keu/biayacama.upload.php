<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 14 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Proses Biaya Cama");

// *** Parameters ***
$_upJumlah = GetSetVar('_upJumlah');
$_upProses = GetSetVar('_upProses');

// *** Main ***
if ($_upProses <= $_upJumlah) {
  if ($_upProses > 0) JalankanProses($_upJumlah, $_upProses);
}
else Selesai($_upJumlah);

// *** Functions ***
function JalankanProses($_upJumlah, $_upProses) {
  $arr = $_SESSION['_up_'.$_upProses];
  $dat = explode('|', $arr);
  // persentase
  $_sudah = ($_upJumlah > 0)? ($_upProses/$_upJumlah) * 100 : 0;
  $_sisa  = 100 - $_sudah;
  // Parameter
  $BayarMhswID = 'BTN-'.$dat[23];
  $NamaTahun = $dat[0];
  $PMBID = $dat[3];
  $NamaMhsw = $dat[4];
  $ProdiID = $dat[6];
  $NamaProdi = $dat[7];
  $Angkatan = $dat[8];
  $RekeningID = "4201390002572";
  $Jumlah = $dat[10]+0;
  $NamaBIPOT = $dat[11];
  $BuktiSetoran = $dat[23].'|'.$dat[24];
  $Catatan = $dat[27];
  // Prosesnya
  $thn = explode('-', $NamaTahun);
  $Tahun = $thn[0];
  $Semester = (substr($thn[1], 1, 2) == 'GA')? 1 : 2;
  $TahunID = $Tahun.$Semester;
  // Cek, apakah sudah dibayarkan sebelumnya atau belum?
  $ada = GetaField('bayarmhsw', "KodeID='".KodeID."' and BayarMhswID",
    $BayarMhswID, "count(BayarMhswID)")+0;
  if ($ada > 0) {
    echo "<p style='text-align:center;background:red'>Sudah pernah dibayarkan</p>";
  }
  else {
    include_once "../baa/mhswbaru.lib.php";
    // Tambahkan di catatan pembayaran
    $s = "insert into bayarmhsw
      (BayarMhswID, KodeID, TahunID, RekeningID, PMBID, TrxID, PMBMhswID,
      Bank, BuktiSetoran, Tanggal, Jumlah,
      Keterangan, LoginBuat, TanggalBuat, NA)
      values
      ('$BayarMhswID', '".KodeID."', '$TahunID', '$RekeningID', '$PMBID', 1, 0,
      'BTN', '$BuktiSetoran', now(), $Jumlah,
      '$Catatan', '$_SESSION[_Login]', now(), 'N')";
    $r = _query($s);
    die("<pre>$s</pre>");
    // Update summary
    HitungUlangBIPOTPMB($PMBID);
  }
  // Tampilan proses
  $_Jumlah = number_format($Jumlah);
  // Tampilan proses
  echo "
  <p align=center>
  <font size=+1>$_upProses</font> <sup>~$_upJumlah</sup><br />
    <img src='../img/B1.jpg' height=20 width=1 /><img src='../img/B2.jpg' height=20 width=$_sudah /><img src='../img/B3.jpg' height=20 width=$_sisa /><img src='../img/B1.jpg' height=20 width=1 />
    <br />
    Tahun Akd: $TahunID <br />
    PMBID: $PMBID <br />
    Nama: <b>$NamaMhsw</b><br />
    Prodi: <b>$NamaProdi</b> <sup>$ProdiID</sup><br />
    Rekening: <b>$RekeningID</b><br />
    Jumlah: <b>$_Jumlah</b><br />
    Catatan: <b>$Catatan</b><br />
  </p>";
  
  // Next...
  $tmr = 1000;
  $_SESSION['_upProses']++;
  echo <<<SCR
    <script>
    window.onload=setTimeout("window.location='../$_SESSION[mnux].upload.php'", $tmr);
    </script>
SCR;
}

function Selesai($_pmbJumlah) {
  $namafile = basename($_SESSION['_pmbFile']);
  echo "<p align=center>
  Proses telah selesai.<br />
  Sistem telah memproses <font size=+1>$_pmbJumlah</font> data Cama.<br />
  </p>";
  //echo "<script>parent.window.location='../index.php?mnux=$_SESSION[mnux]'</script>";
  
  echo "<p align=center>
  <input type=button name='Kembali' value='Kembali' onClick=\"parent.window.location='../index.php?mnux=$_SESSION[mnux]'\" />
  </p>";
}
?>

