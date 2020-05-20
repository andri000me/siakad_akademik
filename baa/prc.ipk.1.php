<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Proses IPK Semester", 1);

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
TampilkanJudul("Proses IPK Semester");
$gos = (empty($_REQUEST['gosx']))? 'AmbilData' : $_REQUEST['gosx'];
$gos();

// *** Functions ***
function AmbilData() {
  $s = "select k.KHSID, k.MhswID, m.Nama
    from khs k
      left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where k.KodeID = '".KodeID."'
      and k.TahunID = '$_SESSION[TahunID]'
      and k.ProdiID = '$_SESSION[ProdiID]'
      and k.NA = 'N'
    order by k.MhswID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
  	$_SESSION['PRC_IPK_KHSID_'.$n] = $w['KHSID'];
  	$_SESSION['PRC_IPK_MhswID_'.$n] = $w['MhswID'];
  	$_SESSION['PRC_IPK_Nama_'.$n] = $w['Nama'];
	$n++;
  }
  $_SESSION['PRC_IPK_TahunID'] = $_SESSION['TahunID'];
  $_SESSION['PRC_IPK_ProdiID'] = $_SESSION['ProdiID'];
  $_SESSION['PRC_IPK_JML'] = $n;
  $_SESSION['PRC_IPK_PRC'] = 0;
  // Tampilkan konfirmasi
  echo Konfirmasi("Konfirmasi Proses",
    "Anda akan memproses IPK dari prodi: <b>$_SESSION[ProdiID]</b> Tahun Akd: <b>$_SESSION[TahunID]</b>.<br />
    Jumlah yg akan diproses: <b>$_SESSION[PRC_IPK_JML]</b>.<br />
    Anda yakin akan memprosesnya?
    <hr size=1 color=silver />
    Opsi: <input type=button name='Proses' value='Proses Sekarang'
      onClick=\"window.location='../$_SESSION[mnux].1.php?gosx=Proses'\" />
      <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />");
}
function HitungIPS($TahunID, $MhswID, $KHSID) {
  // IPS menghitung semua nilai walau pun belum di finalisasi.
  $data = GetFields('krs', "NA='N' and Tinggi='*' and KHSID", $KHSID,
    "sum(BobotNilai * SKS)/sum(SKS) as BBT,
    sum(BobotNilai * SKS) as NK,
    sum(SKS) as TotSKS");
  return $data['BBT']+0;
}
function HitungIPK($TahunID, $MhswID, $KHSID) {
  // Hitung IPK
  //and Final='Y' and
  $SesiSkrg = GetaField('khs', 'KHSID', $KHSID, 'Sesi')+0;
  $IPK = GetaField('krs left outer join khs on krs.KHSID=khs.KHSID', "krs.KodeID='".KodeID."' and krs.Tinggi='*' and krs.NA='N' and (khs.Sesi <= $SesiSkrg or krs.KHSID=0) and krs.MhswID",
    $MhswID,
    "sum(krs.BobotNilai * krs.SKS)/sum(krs.SKS)");
  
  return $IPK+0;
}
function Proses() {
  $jml = $_SESSION['PRC_IPK_JML']+0;
  $prc = $_SESSION['PRC_IPK_PRC']+0;
  
  $TahunID = $_SESSION['PRC_IPK_TahunID'];
  $ProdiID = $_SESSION['PRC_IPK_ProdiID'];
  if ($prc < $jml) {
  	// Parameter
  	$KHSID = $_SESSION['PRC_IPK_KHSID_'.$prc]+0;
  	$MhswID = $_SESSION['PRC_IPK_MhswID_'.$prc];
  	$Nama = $_SESSION['PRC_IPK_Nama_'.$prc];
    // Proses
	ResetNilaiTertinggi($MhswID);
	BuatNilaiTertinggi($MhswID);
	
    $ips = HitungIPS($TahunID, $MhswID, $KHSID);
    $ipk = HitungIPK($TahunID, $MhswID, $KHSID);
    $s_ips = "update khs
      set IPS = $ips, IP = $ipk
      where KHSID = '$KHSID' ";
    $r_ips = _query($s_ips);

    // Tampilkan
    $persen = ($jml > 0)? $prc/$jml*100 : 0;
    $sisa = ($jml > 0)? 100-$persen : 0;
    $persen = number_format($persen);
    echo "<p align=center>
    <font size=+1>$persen %</font><br />
    <img src='../img/B1.jpg' width=1 height=20 /><img src='../img/B2.jpg' width=$persen height=20 /><img src='../img/B3.jpg' width=$sisa height=20 /><img src='../img/B1.jpg' width=1 height=20 /><br />
    Memproses: #$prc<br />
    <sup>$MhswID</sup><br />
    <b>$Nama</b><br />
    <h1 align=center>
      IPS: $ips<br />
      IPK: $ipk
    </h1>
    </p>
    <hr size=1 color=silver />
    <p align=center>
      <input type=button name='Batal' value='Batalkan' onClick=\"window.close()\" />
    </p>";

    // Next
    $_SESSION['PRC_IPK_PRC']++;
    // Reload
    $tmr = 10;
    echo <<<SCR
    <script>
    window.onload=setTimeout("window.location='../$_SESSION[mnux].1.php?gosx=Proses'", $tmr);
    </script>
SCR;
  }
  else echo Konfirmasi("Proses Selesai",
    "Proses telah selesai.<br />
    Data yang berhasil diproses: <b>$_SESSION[PRC_IPK_PRC]</b>.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />");
}

function ResetNilaiTertinggi($MhswID) {
  $s = "update krs set Tinggi = '' where MhswID='$MhswID' and KodeID='".KodeID."' ";
  $r = _query($s);
}

function BuatNilaiTertinggi($MhswID) {
  // Ambil semuanya dulu
  $s = "select KRSID, MKKode, BobotNilai, GradeNilai, SKS, Tinggi
    from krs
    where KodeID = '".KodeID."'
      and MhswID = '$MhswID'
    order by MKKode";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $ada = GetFields('krs', "Tinggi='*' and KRSID<>'$w[KRSID]' and MhswID='$MhswID' and MKKode", $w['MKKode'], '*');
    // Jika nilai sekarang lebih tinggi
    if ($w['BobotNilai'] > $ada['BobotNilai']) {
      $s1 = "update krs set Tinggi='*' where KRSID='$w[KRSID]' ";
      $r1 = _query($s1);
      // Cek yg lalu, kalau tinggi, maka reset
      if ($ada['Tinggi'] == '*') {
        $s1a = "update krs set Tinggi='' where KRSID='$ada[KRSID]' ";
        $r1a = _query($s1a);
      }
    }
    // Jika yg lama lebih tinggi, maka ga usah diapa2in
    else {
    }
  }
}

?>
