<?php
// Author: Emanuel Setio Dewo
// 06 April 2006
session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
CetakSurat();
include_once "disconnectdb.php";

function CetakSurat() {
  global $_HeaderPrn, $_lf;
  // data periode
  $TM = FormatTanggal($_REQUEST['TglDUMulai']);
  $TS = FormatTanggal($_REQUEST['TglDUSelesai']);
  $BY = FormatTanggal($_REQUEST['TglBayar']);
  $TT = FormatTanggal($_REQUEST['TglTangan']);
  
  // Ambil template
  $namatemplate = "template/$_REQUEST[tahunpssb].PSSB.txt";
  $ft = fopen($namatemplate, 'r');
  $tpl = fread($ft, filesize($namatemplate));
  fclose($ft);
  
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";  
  // Ambil semua parameter
  $pssbid = array();
  $pssbid = $_REQUEST['pssbid'];
  if (!empty($pssbid)) {
    $f = fopen($nmf, 'w');
	/*fwrite($f, 
      chr(27) . chr(38) . chr(107) . chr(50) . chr(83). // condensed
      chr(27) . chr(38) . chr(108) . chr(54) . chr(68). // 6 lines per inches
      chr(27) . chr(40) . chr(115) . chr(51) . chr(66)); // bold & 66 baris*/
    fwrite($f, chr(27).chr(15));
    fwrite($f, chr(27).chr(108).chr(5));
    // Buat surat beberapa sekaligus
    for ($i = 0; $i < sizeof($pssbid); $i++) {
      $pssb = GetFields("pssb p
        left outer join program prg on p.ProgramID=prg.ProgramID
        left outer join prodi prd on p.ProdiID=prd.ProdiID
        left outer join fakultas f on prd.FakultasID=f.FakultasID
        left outer join asalsekolah a on p.AsalSekolah=a.SekolahID", 
        'PSSBID', $pssbid[$i], 
        "p.*, prd.Nama as PRD, prg.Nama as PRG, f.Nama as FAK,
        a.Nama as AsalSekolah, a.JenisSekolahID as jensek");
      
      // Ambil template
      $_t = $tpl. chr(12);
      $_t = str_replace('~PSSBID~', $pssb['PSSBID'], $_t);
      $_t = str_replace('~Nama~', $pssb['Nama'], $_t);
      $_t = str_replace('~AsalSekolah~', $pssb['AsalSekolah'], $_t);
      $_t = str_replace('~FAK~', $pssb['FAK'], $_t);
      $_t = str_replace('~PRD~', $pssb['PRD'], $_t);
      $_t = str_replace('~TglMulai~', $TM, $_t);
      $_t = str_replace('~TglSelesai~', $TS, $_t);
      $_t = str_replace('~BayarSelesai~', $BY, $_t);
      $_t = str_replace('~Tanggal~', $TT, $_t);
      // Ambil BIPOT default untuk program studi ybs
      $bipotid = GetaField('bipot', "Def='Y' and ProgramID='$pssb[ProgramID]' and ProdiID", 
        $pssb['ProdiID'], 'BIPOTID');
      // BIPOT
      $tot = 0;
      $bipot2 = GetBipot2($bipotid, $tot);
	  //$jensek = Getafield('asalsekolah', "sekolahID", $pssb['sekolahID'], 'JenisSekolahID');
      $_t = str_replace('~BIPOT~', $bipot2, $_t);
      // Diskon
	    $cttDiskon = ($pssb['Diskon'] > 0)? GetDiskon($bipotid, $pssb['Diskon'], $tot, $satu, $dua, $tiga) : Nodiskon($bipotid, $tot, $satu, $dua, $tiga);
		if ($pssb['jensek']=='PENABUR') {
	$catat = "Jika Anda membayar lunas s.d. ".$BY. " akan mendapat rabat 5% dari SPP yang wajib dibayar, asal sekolah anda dari ".$_lf."   BPK Penabur sehingga mendapat tambahan rabat sebesar 5% lagi"; }
    elseif ($pssb['jensek']=='WAKIL') {
	$catat = "Jika Anda membayar lunas s.d. ".$BY. " akan mendapat rabat 10% dari SPP yang wajib dibayar, asal sekolah anda dari".$_lf."   Perwakilan sehingga mendapat tambahan rabat sebesar 5% lagi"; }
	else {
    $catat = "Jika Anda membayar lunas s.d. ".$BY. " akan mendapat rabat 5% dari SPP yang wajib dibayar"; }
	$_t = str_replace('~LUNAS~', $catat, $_t);
	    $_t = str_replace('~DISKON~', $cttDiskon, $_t);
	    // Total
	    $_t = str_replace('~TOTALBIAYA~', number_format($tot), $_t);
		//50% SPP
		$_t = str_replace('~TAHAP1~', number_format($satu), $_t);
        //30% SPP
		$_t = str_replace('~TAHAP2~', number_format($dua), $_t);
		//20% SPP
		$_t = str_replace('~TAHAP3~', number_format($tiga), $_t);
      // Tuliskan ke file  
      fwrite($f, $_t);
    }
    fclose($f);
    if (empty($_REQUEST['prn'])) {
      TampilkanFileDWOPRN($nmf, '');
    }
    else {
      include_once "dwoprn.php";
      DownloadDWOPRN($nmf);
    }
  }
}
function GetDiskon($bipotid, $Diskon, &$tot, &$satu, &$dua, &$tiga) {
  global $_lf;
  $s = "select b2.*, bn.Nama, bn.DefJumlah, bn.DefBesar, bn.Diskon
    from bipot2 b2
      left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
    where b2.BIPOTID='$bipotid' and b2.SaatID=1
      and INSTR(b2.StatusAwalID, '.S.')>0
      and bn.Diskon='Y'
    order by b2.Prioritas";
  $r = _query($s); $total = 0;
  $a = "Berdasarkan hasil rapat Tim PSB/Jalur Raport, Anda diberikan potongan SPP sebesar ".$Diskon."%, ".$_lf."   sehingga Total Pembayaran SPP dan Biaya Pendidikan lainnya ";
  while ($w = _fetch_array($r)) {
    $jml = ($w['DefJumlah'] == 0)? 1 : $w['DefJumlah'];
    //$bsr = ($w['Diskon'] == 'Y')? $w['Jumlah']-($w['Jumlah'] * $Diskon/100) : 0;
    $total += ($jml * $w['Jumlah'] * $Diskon/100);
    //fwrite($f, "$jml x $bsr".$_lf);
    //$a .= $w['Jumlah'] . ' x '. $bsr . ' ' . (($w['Diskon']=='Y')? 'DISKON' : '').$_lf;
  }
  $tot = $tot - $total;
  $sppx = GetaField('bipot2', "bipotid='$bipotid' AND NA='N' AND BIPOTNamaID", '3', 'jumlah');
  //$spp = $total/($Diskon/100);
  $satu = ($sppx-($sppx * ($Diskon/100)))*0.5;
  $dua = ($tot - $satu)*0.6;
  $tiga = ($tot - $satu)*0.4;  
  //$tiga = $tot-($satu+$dua); 
  return $a .
    "adalah: Rp. ".number_format($tot);
}
function Nodiskon($bipotid, &$tot, &$satu, &$dua, &$tiga) {
	$sppx = GetaField('bipot2', "bipotid='$bipotid' AND NA='N' AND BIPOTNamaID", '3', 'jumlah');
	  $satu = $sppx * 0.5;
	  $dua = ($tot - $satu)*0.6;
      $tiga = ($tot - $satu)*0.4;
}
function GetBipot2($bipotid, &$total) {
  global $_lf;
  $s0 = "select b2.*, bn.Nama, bn.DefJumlah, bn.DefBesar, bn.Diskon
    from bipot2 b2
    left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
    where b2.BIPOTID='$bipotid' and b2.SaatID=1
      and INSTR(b2.StatusAwalID, '.S.')>0
      and b2.NA='N'
    order by b2.Prioritas";
  $r0 = _query($s0);
  $thn = substr($w['PMBID'], 0, 4);
  $a = ''; $n = 0; $total = 0;
  while ($w0 = _fetch_array($r0)) {
    $n++;
    $a .= InsertBIPOT($n, $w0, $tot, $bipotid);
    $total += $tot;
  }
  $strtotal = str_pad(' ', 57, ' '). str_pad('-', 15, '-').$_lf;
  $strtotal .= str_pad('Total :', 57, ' ', STR_PAD_LEFT) .
    str_pad(number_format($total), 15, ' ', STR_PAD_LEFT);
  return $a . $strtotal;
}
function InsertBIPOT($n, $w, &$tot, $bipotid) {
  global $_lf;
  $a = str_pad($n, 5, ' ', STR_PAD_LEFT) .'. ';
  $a .= str_pad($w['Nama'], 30, ' ');
  if ($w['DefJumlah'] > 1) {
    // Jika BPP SKS
    if ($w['BIPOTNamaID'] == 5) {
      $detbipot = GetFields('bipot', "BIPOTID", $bipotid, "*");
      $_prd = $detbipot['ProdiID'];
      $w['DefJumlah'] = GetaField('prodi', 'ProdiID', $_prd, "DefSKS");
    }
    $det = $w['DefJumlah']." x ".number_format($w['Jumlah']);
    $jml = $w['DefJumlah'] * $w['Jumlah'];
    $a .= str_pad($det, 15, ' ', STR_PAD_LEFT);
  }
  elseif ($w['BIPOTNamaID'] == 6){
	  $det2 = '2' . " x " . number_format($w['Jumlah']);
	  $jml = 2 * $w['Jumlah'];
	  $a .= str_pad($det2, 15, ' ', STR_PAD_LEFT);
  }
  else {
    $a .= str_pad(' ', 15, ' ');
    $jml = $w['Jumlah'];
  }
  $tot = $jml;
  $a .= str_pad(number_format($jml), 20, ' ', STR_PAD_LEFT);
  return $a . $_lf;
}
?>
