<?php
// Author : Arisal Yanuarafi
// Email  : arisal.yanuarafi@yahoo.com
// Start  : 4 Januari 2017

session_start();
include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";


// *** Parameters ***
$pid = $_REQUEST['pid'];
$jid = $_REQUEST['jid'];

// *** Main ***

//1. Hapus Presensi Dosen
$s = "DELETE from presensi where PresensiID='$pid'";
$r = _query($s);
//echo $s;
//2. Hapus Presensi Mahasiswa
$s = "DELETE from presensimhsw where PresensiID='$pid'";
$r = _query($s);
//3. Hitung Ulang Presensi Mahasiswa
$s = "SELECT KRSID from krs where JadwalID='$jid'";
$r = _query($s);
while ($w = _fetch_array($r)){
$jml = GetaField('presensimhsw', 'KRSID', $w['KRSID'], "sum(Nilai)")+0;
      $sk = "update krs
        set _Presensi = $jml
        where KRSID = $w[KRSID]";
      $rk = _query($sk);
}
//4. Update Jadwal
$s = "UPDATE jadwal set Kehadiran=(Kehadiran-1) where JadwalID='$jid'";
$r = _query($s);
echo "<html><head><script>
opener.location='../index.php?mnux=$_SESSION[mnux]&gos=Edit&JadwalID=$jid';
    self.close();
	</script></head></html>";