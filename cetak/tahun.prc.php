<?php
// Author: Emanuel Setio Dewo
// 19 May 2006
// www.sisfokampus.net

session_start();
include "../sisfokampus.php";
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

function NextSesiMhsw($mhswid) {
  $s = "select max(Sesi) as SS from khs where MhswID='$mhswid' ";
  $r = _query($s);
  $w = _fetch_array($r);
  return $w['SS']+1;
}
function PRC() {
  echo "<body bgcolor=#EEFFFF>";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  $pss = $_SESSION['THN'.$prodi.'POS'];
  $mhswid = $_SESSION['THN'.$prodi.$pss];
  // Jika ada data
  if (!empty($mhswid)) {
    echo "<p>#<font size=+2>".$pss . "</font> &raquo; <b>$tahun</b> &raquo; " .$_SESSION['THN'.$prodi.$pss]."</p><hr>";
    $sdh = GetFields('khs', "MhswID='$mhswid' and TahunID", $tahun, "KHSID, MhswID");
    if (empty($sdh)) {
      $def = GetaField('statusmhsw', 'Def', 'Y', 'StatusMhswID');
      $sesi = NextSesiMhsw($mhswid);
      $mhsw = GetFields('mhsw', "MhswID", $mhswid, "BIPOTID,ProgramID,ProdiID"); 
      $sp = "insert into khs (TahunID, KodeID, ProgramID, ProdiID,
        MhswID, StatusMhswID, Sesi, BIPOTID,
        LoginBuat, TanggalBuat)
        values ('$tahun', '$_SESSION[KodeID]', '$mhsw[ProgramID]', '$mhsw[ProdiID]',
        '$mhswid', '$def', '$sesi', '$mhsw[BIPOTID]',
        '$_SESSION[_Login]', now()  )";
      //echo "<pre>$sp</pre>";
      $rp = _query($sp);
      echo "<p><font color=green>DIPROSES</font></p>";
    }
    else {
      echo "<p><font color=gray>Sudah pernah diproses</font></p>";
    }
  }
  // refresh page
  if ($_SESSION['THN'.$prodi.'POS'] < $_SESSION['THN'.$prodi]) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    // update data tahun
    $st = "update tahun set ProsesBuka=ProsesBuka+1
      where TahunID='$tahun' and ProgramID='$prid' and ProdiID='$prodi'";
    $rt = _query($st);
    echo "<p>Proses buka TAHUN akademik <b>$tahun</b> sudah <font size=+2>SELESAI</font></p>";
  }

  $_SESSION['THN'.$prodi.'POS']++;
}
function PRCBIPOT() {
  include_once "mhswkeu.sav.php";
  
  $tahun = $_SESSION['BPT-TAHUN'];
  $prid = $_SESSION['BPT-PRID'];
  $prodi = $_SESSION['BPT-PRODI'];
  $jml = $_SESSION['BPT-JML'];
  $pos = $_SESSION['BPT-POS']++;
  $prs = ($jml > 0)? number_format($pos/$jml*100) : '0';
  echo "<p>$pos/$jml : <font size=+2>$prs%</font></p>";
  // Proses BIPOT
  $_REQUEST['khsid'] = $_SESSION['BPT-KHSID-'.$pos];
  $_REQUEST['mhswid'] = $_SESSION['BPT-MHSWID-'.$pos];
  $_REQUEST['pmbmhswid'] = 1;
  PrcBIPOTSesi();
  
  // Refresh page
  if ($_SESSION['BPT-POS'] <= $_SESSION['BPT-JML']) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    echo "<p>Proses BIPOT tahun <b>$tahun</b> sudah <font size=+2>SELESAI</font></p>";
  }
}
function PRCTUTUP() {
  echo "<body bgcolor=#EEFFFF>";
  $tahun = $_SESSION['tahun'];
  $tahun1 = $_SESSION['tahun1'];
  $prodi = $_SESSION['prodi'];
  $prid = $_SESSION['prid'];
  $pss = $_SESSION['Tutup-Pos-'.$prodi];
  $mhswid = $_SESSION['Tutup-MhswID-'.$prodi.$pss];
  $khsid = $_SESSION['Tutup-KHSID-'.$prodi.$pss];
  $max = $_SESSION['Tutup-Max-'.$prodi];
if ($khsid != 0 and !empty($mhswid)) {
  // proses
  $persen = ($max == 0)? '0' : number_format($pss/$max * 100);
  echo "Proses &raquo; <b>$pss/$max</b> &raquo; $mhswid ($khsid)</p>
  <hr>
  <p><font size=+2>$persen</font> %</p>";
  // Jika yang ditutup adalah Semester Pendek
  $SP = GetaField('tahun', "TahunID", $tahun, 'SP');
  if ($SP == 'Y') {}
  else {
  // Hitung
  $bia = GetaField('bipotmhsw', "TahunID='$tahun' and TrxID=1 and MhswID", $mhswid, "sum(Jumlah*Besar)")+0;
  $pot = GetaField('bipotmhsw', "TahunID='$tahun' and TrxID=-1 and MhswID", $mhswid, "sum(Jumlah*Besar)")+0;
  $byr = GetaField('bayarmhsw', "TahunID='$tahun' and TrxID=1 and MhswID", $mhswid, "sum(Jumlah)")+0;
  $trk = GetaField('bayarmhsw', "TahunID='$tahun' and TrxID=-1 and MhswID", $mhswid, "sum(Jumlah)")+0;
  echo "<p>$bia, $pot, $byr, $trk &raquo; Next: $tahun1</p>";
  $bal = $bia + $trk - $pot - $byr;
  $jmldenda = 0;
  if ($bal > 0) {
    // Apakah kena denda?
    if ($_SESSION['Denda2']+0 > 0) {
      //$denda = GetFields('bipotmhsw', "TahunID='$tahun' and MhswID='$mhswid' and BIPOTNamaID", $_SESSION['accDenda2'], '*');
      //if (empty($denda)) {
        $NamaD = GetaField('bipotnama', 'BIPOTNamaID', $_SESSION['accDenda2'], 'Nama');
        $jmldenda = ($bal * $_SESSION['Denda2']/100)+0;
        //$s0 = "insert into bipotmhsw
        //(PMBID, MhswID, TahunID, BIPOTNamaID, Nama,
        //TrxID, Jumlah, Besar, Dibayar, Catatan,
        //LoginBuat, TanggalBuat)
        //values
        //(1, '$mhswid', '$tahun1', $_SESSION[accDenda2], '$NamaD',
        //1, 1, $jmldenda, 0, 'TUTUP TAHUN $_SESSION[Denda2]%',
        //'$_SESSION[_Login]', now())";
        //$r0 = _query($s0);
      }
    
    // Transfer Hutang ke Smt berikutnya
    $sdh = GetFields('bipotmhsw', "TahunID='$tahun1' and MhswID='$mhswid' and BIPOTNamaID", $_SESSION['HutangNext'], "*");
    if (empty($sdh)) {
      $Nama = GetaField('bipotnama', 'BIPOTNamaID', $_SESSION['HutangPrev'], 'Nama');
      
      $s0 = "insert into bipotmhsw
        (PMBID, MhswID, TahunID, BIPOTNamaID, Nama,
        TrxID, Jumlah, Besar, Dibayar, Catatan,
        LoginBuat, TanggalBuat)
        values
        (1, '$mhswid', '$tahun1', $_SESSION[accDenda2], '$NamaD',
        1, 1, $jmldenda, 0, 'TUTUP TAHUN $_SESSION[Denda2]%',
        '$_SESSION[_Login]', now())";
      $r0 = _query($s0);
      
      $s = "insert into bipotmhsw
        (PMBMhswID, MhswID, TahunID, BIPOTNamaID, Nama,
        TrxID, Jumlah, Besar, Dibayar, Catatan,
        LoginBuat, TanggalBuat)
        values
        (1, '$mhswid', '$tahun1', $_SESSION[HutangNext], '$Nama',
        1, 1, $bal, 0, 'TUTUP TAHUN',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
    }
    // Bayarkan hutang di smt sebelumnya
    //$lns = GetFields('bipotmhsw', "TahunID='$tahun' and MhswID='$mhswid' and BIPOTNamaID", $_SESSION['HutangPrev'], "*");
    //if (empty($lns)) {
      //$Nama = GetaField('bipotnama', 'BIPOTNamaID', $_SESSION['HutangNext'], 'Nama');
      //$s = "insert into bipotmhsw
      //  (PMBMhswID, MhswID, TahunID, BIPOTNamaID, Nama,
      //  TrxID, Jumlah, Besar, Dibayar, Catatan,
      //  LoginBuat, TanggalBuat)
      //  values
      //  (1, '$mhswid', '$tahun', $_SESSION[HutangPrev], '$Nama',
      //  -1, 1, 0, $bal+$jmldenda, 'TUTUP TAHUN',
      //  '$_SESSION[_Login]', now())";
      //$r = _query($s);
    //}
    // Hitung Total
    include "mhswkeu.lib.php";
    HitungBiayaBayarMhsw($mhswid, $khsid);
  }
  elseif ($bal < 0) {
    // Transfer Deposit
    $sdh = GetFields('bipotmhsw', "TahunID='$tahun1' and MhswID='$mhswid' and BIPOTNamaID", $_SESSION['DepositNext'], "*");
    if (empty($sdh)) {
    $Nama = GetaField('bipotnama', 'BIPOTNamaID', $_SESSION['DepositPrev'], 'Nama');
    $bal = -1 * $bal;
      $s = "insert into bipotmhsw
        (PMBMhswID, MhswID, TahunID, BIPOTNamaID, Nama,
        TrxID, Jumlah, Besar, Dibayar, Catatan,
        LoginBuat, TanggalBuat)
        values
        (1, '$mhswid', '$tahun1', $_SESSION[DepositPrev], '$Nama',
        -1, 1, $bal, 0, 'TUTUP TAHUN',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
    }
  }
  }
  // Tutup KHS
  $s = "update khs set Tutup='Y' where KHSID=$khsid";
  $r = _query($s);
}  
  // refresh page
  if ($_SESSION['Tutup-Pos-'.$prodi] < $_SESSION['Tutup-Max-'.$prodi]) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    // update tahun
    $st = "update tahun set ProsesTutup=ProsesTutup+1
      where ProgramID='$prid'
      and ProdiID='$prodi'
      and TahunID='$tahun' ";
    $rt = _query($st);
    echo "<p>Proses Tutup Tahun <b>$tahun</b> <font size=+2>SELESAI</font></p>";
  }
  $_SESSION['Tutup-Pos-'.$prodi]++;

}

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

include_once "disconnectdb.php";
?>
