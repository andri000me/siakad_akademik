<?php
// Author: Emanuel Setio Dewo
// 12 May 2006
// www.sisfokampus.net
session_start();
include "../config/config.php";
include "db.mysql.php";
include "connectdb.php";

function init0() {
  $prd = (empty($_SESSION['prodi'])) ? '' : "and khs.ProdiID = '$_SESSION[prodi]'";
	$prid = (empty($_SESSION['prid'])) ? '' : "and khs.ProgramID = '$_SESSION[prid]'";
  echo "<body bgcolor=#EEFFFF>";
  $n = 0;
  $s = "select KHSID, khs.MhswID
    from khs left outer join mhsw m on m.MhswID = khs.MhswID
    where khs.TahunID='$_SESSION[tahun]' and khs.Autodebet=0
		$prd $prid
		and JumlahMK > 0
		and (m.TahunID >= '2002')
    order by MhswID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    echo "$n. $w[MhswID]<br>";
    $_SESSION["AD".$n] = $w['MhswID'].'~'.$w['KHSID'];
    $n++;
  }
  $_SESSION['MaxData'] = $n;
  echo "<hr><p>Proses inisialisasi <b>SELESAI</b>. Lanjutkan ke proses selanjutnya.</p>";
}
function prc0() {
  include_once "dwo.lib.php";
  include_once "parameter.php"; 
  include_once "mhswkeu.sav.php";
  
  $NoRek = GetaField('rekening','Def','Y','RekeningID');
  echo "<body bgcolor=#EEFFFF>";
  $dat = $_SESSION['AD'.$_SESSION['ADPOS']];
  $arr = explode('~', $dat);  
  echo "Processing: #" . $_SESSION['ADPOS'] . " : <b>" . $arr[0] . ' ('.$arr[1]. ")</b><hr>";
  $_REQUEST['mhswid'] = $arr[0];
  $_REQUEST['khsid'] = $arr[1];
  $_REQUEST['pmbmhswid'] = 1;

  PrcBIPOTSesi();
  // Ambil data khs
  $khs = GetFields('khs', 'KHSID', $arr[1], '*');
  $balance = $khs['Biaya'] - $khs['Bayar'] - $khs['Potongan'] + $khs['Tarik'];
  // Apakah sudah ada BPM?
  $bpm = GetFields('bayarmhsw', "TahunID='$khs[TahunID]' and Autodebet=1 and MhswID", $arr[0], "*");
  $strbpm = '';
  if (empty($bpm)) {
    $strbpm = "<font color=blue>Dibuat</font>";
    $bpm['BayarMhswID'] = GetNextBPM();
    $s = "insert into bayarmhsw (BayarMhswID, TahunID, RekeningID,
      MhswID, Autodebet, TrxID, PMBMhswID,
      Tanggal, Jumlah, Keterangan,
      LoginBuat, TanggalBuat)
      values ('$bpm[BayarMhswID]', '$khs[TahunID]', '$NoRek',
      '$khs[MhswID]', 1, 1, 1,
      now(), '$balance', 'Autodebet',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  else {
    $strbpm = "<font color=gray>Diupdate</font>";
    $s = "update bayarmhsw set Jumlah='$balance' where BayarMhswID='$bpm[BayarMhswID]' ";
    $r = _query($s);
  }
  echo "1. Proses keuangan: OK<br />";
  echo "2. Buat BPM: <b>$bpm[BayarMhswID]</b> ~ $strbpm<br />";
  if ($_SESSION['ADPOS'] < $_SESSION['MaxData']-1) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses Keuangan <b>SELESAI</b>. Lanjutkan ke proses selanjutnya.</p>";
	$_SESSION['ADPOS']++;
}

function file0() {
  include_once "dwo.lib.php";
  //HdrExcl(HOME_FOLDER  .  DS . "tmp/cobacsv.csv");
  echo "<body bgcolor=#EEFFFF>";
  $dat = $_SESSION['AD'.$_SESSION['ADPOS']];
  $arr = explode('~', $dat);
  $mhsw = GetFields('mhsw', 'MhswID', $arr[0], "Nama, NamaBank, NomerRekening, ProdiID");
  $khs = GetFields('khs', 'KHSID', $arr[1], "TahunID, Biaya, Potongan, Bayar, Tarik, TotalSKS");
  //$krs  = GetaField('krs left outer join jadwal j on krs.JadwalID = j.JadwalID', "j.JenisJadwalID = 'R' and KHSID", $khs['KHSID'], 'count(krs.KRSID)');
  $krs = GetaField('krstemp k left outer join jadwal j on k.JadwalID=j.JadwalID', 
    "k.TahunID='$khs[TahunID]' and k.MhswID='$arr[0]' and j.JenisJadwalID", 
    'R', "count(*)");
  $balance = $khs['Biaya'] - $khs['Bayar'] - $khs['Potongan'] + $khs['Tarik'];
  $bpm = GetFields('bayarmhsw', "TahunID='$khs[TahunID]' and Autodebet=1 and MhswID", $arr[0], '*');
  $bpmid = $bpm['BayarMhswID'];
  //echo "<h1>&raquo; $khs[TahunID] ($arr[1]) - $bpmid</h1>";
  echo "Processing: #" . $_SESSION['ADPOS'] . " : <b>" . $arr[0] . ' ('.$arr[1] . ")</b><hr>";
  // baca header
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].autodebet.hdr.csv";
  $f = fopen($nmf, 'r');
  $_hdr = fread($f, filesize($nmf)); 
  fclose($f);
  // ekstrak header
  $_arrhdr = explode(chr(13).chr(10), $_hdr);
  $hdr = $_arrhdr[0];
  $arrhdr = explode(';', $hdr);
  $detail = GetDetailBPM($arr[0], $arr[1], $khs['TahunID'], $arrhdr);
  //$jumlahPrak = GetaField()
  $det = implode(';', $detail);
  $fak = substr($mhsw['ProdiID'], 0,1);
  $jur = substr($mhsw['ProdiID'], 1,1);
  $bpmnostrip = str_replace('-','',$bpmid);
  //$nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].autodebet.csv";
  //$f = fopen($nmf, 'a');
  $isi = array(
      $khs['TahunID'],
			$fak,
      $jur,	
      $arr[0], 
      $mhsw['Nama'],
			$balance,
			'',
			$detail[1],
			$detail[2],
			$detail[3],
			$detail[4],
			$detail[5],
			$detail[6],
			$detail[7],
      '0',
      $bpmnostrip,
      $khs['TotalSKS'],
      $krs); 
  $namadbfisi = "autodebet/autodebet-$_SESSION[tahun]-$_SESSION[prodi].dbf";
  $conn=dbase_open($namadbfisi,2);
  dbase_add_record($conn, $isi);
  // hihihi...
  if ($_SESSION['ADPOS'] < $_SESSION['MaxData']-1) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses pembuatan file <b>Berhasil</b>. Silakan download file di:
    <input type=button name='Download' value='Download File' onClick=\"location='downloaddbf.php?fn=$namadbfisi'\">
    </p>";
  $_SESSION['ADPOS']++;
}

function HitungBalance($MhswID, $TahunID){
  $Biaya = GetaField("bipotmhsw", "TahunID = '$TahunID' and TrxID = '1' and MhswID", $MhswID, "sum(Jumlah*Besar)");
  $Potongan = GetaField("bipotmhsw", "TahunID = '$TahunID' and TrxID = '-1' and MhswID", $MhswID, "sum(Jumlah*Besar)");
  
  $Balance = $Biaya - $Potongan;
  
  Return $Balance;
}

function GetDetailBPM($mhswid, $khsid, $thn, $arrhdr) {
  $hdr = array();
  for ($i=6; $i < sizeof($arrhdr); $i++) {
    $apa = explode('(',  $arrhdr[$i]);
    $apa[1] = str_replace(')', '', $apa[1]);
    $hdr[] = $apa[1];
  }
  $arr = array();
  for ($i = 0; $i < sizeof($hdr); $i++) $arr[$i] = 0;
  $s = "select bm.BIPOTNamaID, bm.Jumlah, bm.Besar, bm.Dibayar 
    from bipotmhsw bm
    where bm.TahunID='$thn' and bm.MhswID='$mhswid' and bm.TrxID=1
    order by bm.BIPOTNamaID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $val = $w['BIPOTNamaID'];
    $key = array_search($val, $hdr);
    $arr[$key] = $w['Jumlah'] * $w['Besar'] - $w['Dibayar']; 
  }
  //var_dump($hdr);
 // exit;
  return $arr;
}

function UpBipotMhsw($updtbpt, $mhswid, $tahun){
	$arrhdr = explode(';', $updtbpt);
  $hdr = array();
  for ($i=0; $i < sizeof($arrhdr); $i++) {
    $apa = explode('(',  $arrhdr[$i]);
    $apa[1] = str_replace(')', '', $apa[1]);
    $hdr[] = $apa[1];
		$jml[] = $apa[0];
  }
  $arr = array();
  for ($i = 0; $i < sizeof($hdr); $i++){
	$s = "update bipotmhsw set Dibayar='$jml[$i]'
				where MhswID = $mhswid
				and TahunID = $tahun
      	and BipotNamaID=$hdr[$i]";
	
	$r = _query($s);
	}
}

function HapusKRS($tahun, $mhsw, $khs) {
  $s = "delete from krs
    where TahunID='$tahun'
      and KHSID='$khs' ";
  $r = _query($s);
}

function ImportKRS($tahun, $mhswid, $khs) {
  $s = "select *
    from krstemp
    where TahunID='$tahun'
      and MhswID='$mhswid'
      and NA='N' ";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $s1 = "insert into krs
      (KHSID, MhswID, TahunID, JadwalID,
      MKID, MKKode, SKS,
      HargaStandar, Harga, Bayar,
      StatusKRSID,
      Dispensasi, DispensasiOleh, TanggalDispensasi, CatatanDispensasi,
      Catatan, CatatanError,
      LoginBuat, TanggalBuat,
      LoginEdit, TanggalEdit, NA)
      values
      ('$w[KHSID]', '$w[MhswID]', '$w[TahunID]', '$w[JadwalID]',
      '$w[MKID]', '$w[MKKode]', '$w[SKS]',
      '$w[HargaStandar]', '$w[Harga]', '$w[Bayar]',
      '$w[StatusKRSID]',
      '$w[Dispensasi]', '$w[DispensasiOleh]', '$w[TanggalDispensasi]', '$w[CatatanDispensasi]',
      '$w[Catatan]', '$w[CatatanError]',
      '$w[LoginBuat]', '$w[TanggalBuat]',
      '$w[LoginEdit]', '$w[TanggalEdit]', '$w[NA]')";
    $r1 = _query($s1);
		
		$s2 = "update jadwal set JumlahMhsw=JumlahMhsw+1 where jadwalid=$w[JadwalID]";
		$r2 = _query($s2);
  }
}

function SetKHSAktif($khsid, $mhswid, $tahun){
	//$khsid = $_REQUEST['khsid'];
	$khs = GetFields('khs', 'KHSID', $khsid, '*');
	$stm = GetFields('statusmhsw', 'StatusMhswID', $khs['StatusMhswID'], '*');
  if ($stm['Nilai'] == 0) {}
	else {
		$status = ($khs['StatusMhswID'] != 'A')? ", StatusMhswID='A' " : '';
    // Set kalau sudah dicetak
    $s = "update khs set Cetak='Y', KaliCetak=KaliCetak+1 $status
      where KHSID='$khsid'";
    $r = _query($s);
	}
	$sb = "update bipotmhsw set Draft='N' 
    where MhswID='$mhswid' and TahunID='$tahun' and Draft='Y' ";
  $rb = _query($sb);
	
	HapusKRS($tahun, $mhswid, $khsid);
	ImportKRS($tahun, $mhswid, $khsid);
}

function aplod0() {
  echo "<body bgcolor=#EEFFFF>";
  include_once "dwo.lib.php";
  include_once "parameter.php"; 
  include_once "mhswkeu.sav.php";
  $dat = $_SESSION['ADUP'.$_SESSION['ADUPPOS']];
	$upd = $_SESSION['BPT'.$_SESSION['ADUPPOS']];
  //echo "<p><b>$dat</b></p>";
  $arr = explode('~', $dat);
  $mhswid = $arr[0];
  $khsid = $arr[1];
  $bpmid = $arr[2];
  $bayar = $arr[3];
	$tahun = $arr[4];
	//var_dump($arr); exit;
  echo "#$_SESSION[ADUPPOS] &raquo; <font size=4>" . $arr[0] . "</font><hr />";
  $bpm = GetFields('bayarmhsw', 'BayarMhswID', $bpmid, '*');
  echo "$bpm[BayarMhswID] - $bpm[Autodebet]";
  if ($bpm['Proses'] == 0) {
    $_REQUEST['mhswid'] = $mhswid;
    $_REQUEST['pmbid'] = '';
    $_REQUEST['pmbmhswid'] = 1;
    $_REQUEST['khsid'] = $khsid;
    $_REQUEST['RekeningID'] = 'Autodebet';
    $_REQUEST['BuktiSetoran'] = 'Autodebet';
    $_REQUEST['bpmid'] = $bpmid;
    $_REQUEST['Jumlah'] = $bayar;
    $_REQUEST['Keterangan'] = 'Autodebet, tgl: ' . date('Y-m-d');
    $_REQUEST['Tanggal_y'] = date('Y');
    $_REQUEST['Tanggal_m'] = date('m');
    $_REQUEST['Tanggal_d'] = date('d');
    $_REQUEST['md'] = 0;
    //var_dump($_REQUEST['mhswid']); exit;
		BayarSavAuto(0);
		UpBipotMhsw($upd, $mhswid, $tahun);
		SetKHSAktif($khsid, $mhswid, $tahun);
    $stt = "<font color=navy>Dibayar: <b>Autodebet</b></font>";
  }
  else {
		UpBipotMhsw($upd, $mhswid, $tahun);
		SetKHSAktif($khsid, $mhswid, $tahun);
    $stt = "<font color=gray>Updated</b>";
  }
  $_bayar = number_format($bayar);
  echo "<p><table cellspacing=1>
    <tr><td>NPM</td><td>: $mhswid</td></tr> 
    <tr><td>KHS</td><td>: $khsid</td></tr> 
    <tr><td>BPM</td><td>: $bpmid</td></tr> 
    <tr><td>Bayar</td><td>: Rp. $_bayar</td></tr>
    <tr><td>Status</td><td>: $stt</td></tr>
    </table></p>";
  if ($_SESSION['ADUPPOS'] < $_SESSION['ADUPPOSX']) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses upload sudah <b>SELESAI</b>.</p>";
  $_SESSION['ADUPPOS']++;
}

if (!empty($_REQUEST['WZRD'])) $_REQUEST['WZRD']();

include_once "disconnectdb.php";
?>
