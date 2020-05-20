<?php
// Author: Emanuel Setio Dewo
// 19 May 2006 (Pengganti prc.ipk.batch.x.php
// http://www.sisfokampus.net

session_start();
include "../sisfokampus.php";
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

function PRC2() {
  echo "<body bgcolor=#EEFFFF>";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  
  $pos = $_SESSION['IPK'.$prodi.'POS'];
  $max = $_SESSION['IPK'.$prodi];
  $mhswid = $_SESSION['IPK-MhswID'.$prodi.$pos];
  $khsid = $_SESSION['IPK-KHSID'.$prodi.$pos];
  
  // proses
  if (!empty($mhswid)) {
    echo "<p><b>$pos/$max</b><br />
      &raquo; $khsid &raquo; <font size=+2>$mhswid</font></p>";
    $ipk = HitungIPK($mhswid, $tahun);
    $ips = HitungIPS($mhswid, $khsid, $ipk, $tahun);
    echo "<p>Tahun: $tahun<br />
      IPS: $ips<br />
      IPK: $ipk</p>";
  }
  // refresh page
  if ($_SESSION['IPK'.$prodi.'POS'] < $_SESSION['IPK'.$prodi]) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    // update data tahun
    $st = "update tahun set ProsesIPK=ProsesIPK+1
      where TahunID='$tahun' and ProgramID='$prid' and ProdiID='$prodi'";
    $rt = _query($st);
    echo "<p>Proses IPK sudah <font size=+2>SELESAI</font></p>";
  }
  $_SESSION['IPK'.$prodi.'POS']++;
}
function PRCMUNDUR() {
  echo "<body bgcolor=#EEFFFF>";
  $_SESSION['HM-POS']++;
  $pos = $_SESSION['HM-POS'];
  $max = $_SESSION['HM-JML'];
  $mhswid = $_SESSION['HM-MhswID-'.$pos];
  $tahun1 = $_SESSION['HM-tahun1'];
  echo "Processing: <font size=+1>$mhswid</font><hr size=1 color=silver>";
  // Ambil data KHS Mhsw
  $s = "select KHSID, TahunID
    from khs
    where MhswID='$mhswid'
      and TahunID >= '$tahun1'
    order by TahunID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $ipk = HitungIPK($mhswid, $w['TahunID']);
    $ips = HitungIPS($mhswid, $w['KHSID'], $ipk, $w['TahunID']);
    echo "Tahun: $w[TahunID], IPS: $ips, IPK: $ipk <br />";
  }
  // refresh page
  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<p>Proses Hitung IPK Mundur telah <font size=+1>Selesai</font></p>";
}
function HitungIPS($mhswid, $khsid, $ipk, $tahun) {
  //$khs = GetFields('khs', 'KHSID', $khsid, "TahunID, MhswID");
  //$ips = GetaField('krs', "StatusKRSID='A' and KHSID", $khsid,
  //  "sum(SKS * BobotNilai)/sum(SKS)");
  $_ips = GetFields('krs left join jadwal j on krs.JadwalID = j.JadwalID', "(j.JenisJadwalID is null or j.JenisJadwalID <> 'R') and StatusKRSID='A' and (GradeNilai<>'-' or GradeNilai <> '' and not GradeNilai is NULL) and krs.Final = 'Y' and krs.MhswID='$mhswid' and krs.TahunID", $tahun,
    "sum(krs.SKS * BobotNilai)/sum(krs.SKS) as IPS, sum(krs.SKS) as SKS");
  $ips = $_ips['IPS']+0;
  $sks = $_ips['SKS']+0;
  $s = "update khs set IPS=$ips, IP=$ipk, TotalSKS=$sks where KHSID='$khsid' ";
  $r = _query($s);
  return $ips;
}
function HitungIPK($mhswid, $tahun) {
  // Hapus tabel KRSPRC
  $_sx = "delete from krsprc where MhswID='$mhswid' ";
  $_rx = _query($_sx);
  // LOOP 1: Nilai Tertinggi
  $s = "select k.KRSID, k.KHSID, k.MhswID, k.MKID, k.TahunID, 
    k.MKKode, k.GradeNilai, k.BobotNilai, k.StatusKRSID, k.SKS, mk.MKSetara
    from krs k
      left outer join mk mk on k.MKID=mk.MKID
      left outer join jadwal j on j.JadwalID = k.JadwalID
      left outer join nilai n on k.GradeNilai=n.Nama
    where k.MhswID='$mhswid' 
      and k.StatusKRSID='A' 
      and (GradeNilai <> '-' or GradeNilai <> '' and not GradeNilai is NULL)
      and k.Final = 'Y'
      and k.TahunID <= '$tahun' 
      and n.Lulus = 'Y'
      and (j.JenisJadwalID <> 'R' or j.JenisJadwalID is NULL)
    order by k.MKKode asc, k.BobotNilai desc";
  $r = _query($s);
  $n = 0; $mk = '';
  while ($w = _fetch_array($r)) {
    if ($mk != $w['MKKode']) {
      $mk = $w['MKKode'];
      InsertKRSPRC($w);
    }
  }
  // LOOP 2: Matakuliah Setara
  $s = "select *
    from krsprc
    where MhswID='$mhswid' and MKSetara <> ''
    order by BobotNilai desc";
  $r = _query($s);
  $reject = '';
  while ($w = _fetch_array($r)) {
    if (strpos($reject, $w['MKKode']) === false) {
      $reject .= $w['MKSetara'];
    }
    else {
      // hapus
      $sdel = "delete from krsprc where KRSPRCID='$w[KRSPRCID]' ";
      $rdel = _query($sdel);
    }
  }
  $arr = GetFields('krsprc', "GradeNilai not in ('', '-') and MhswID", $mhswid, 
    "sum(SKS) as TSKS, sum(SKS * BobotNilai) as KXN");
  $arr['TSKS'] += 0;
  $_ipk = ($arr['TSKS'] == 0)? '0' : $arr['KXN'] / $arr['TSKS']; 
  $si = "update mhsw set IPK=$_ipk, TotalSKS=$arr[TSKS] where MhswID='$mhswid'";
  $ri = _query($si);
  return $_ipk;
}
function InsertKRSPRC($w) {
  $s = "insert into krsprc
    (KRSID, KHSID, MhswID, TahunID, 
    MKID, MKKode, SKS, GradeNilai, BobotNilai,
    StatusKRSID, MKSetara)
    values ('$w[KRSID]', '$w[KHSID]', '$w[MhswID]', '$w[TahunID]',
    '$w[MKID]', '$w[MKKode]', '$w[SKS]', '$w[GradeNilai]', '$w[BobotNilai]',
    '$w[StatusKRSID]', '$w[MKSetara]')";
  $r = _query($s);
}

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();
include_once "disconnectdb.php";

?>
