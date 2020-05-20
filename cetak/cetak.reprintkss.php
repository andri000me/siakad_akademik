<?php
include "../sisfokampus.php";
$tahun = GetSetVar('tahun');
$mhswid = GetSetVar('mhswid');

TampilkanJudul("Reprint KSS");
FilterReprintKSS();
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

function FilterReprintKSS(){
	global $arrID;
	echo "<p><table class=box cellpadding=4 cellspacing=1>
				<form saction='?' method='post'>
				<input type=hidden name='mnux' value='cetak.reprintkss'>
				<input type=hidden name='gos' value='CekKSS'>
				<tr><th class=ttl colspan=2>$arrID[Nama]</th></tr>
				<tr><td class=inp>Tahun Akademik</td><td class=ul><input type=text name=tahun value='$_SESSION[tahun]'></td></tr>
				<tr><td class=inp>NIM</td><td class=ul><input type=text name=mhswid value='$_SESSION[mhswid]'></td></tr>
				<tr><td class=ul colspan=2><input type=submit Value='Cetak'></td></tr>
				</form></table></p>";
}

function CekKSS(){
	$khs = GetFields('khs', "TahunID = $_SESSION[tahun] and MhswID", $_SESSION['mhswid'], '*');
	$mhsw = GetFields('mhsw', "MhswID", $_SESSION['mhswid'], 'Nama');
	if (strpos("ACT", $khs['StatusMhswID']) === false && $khs['KaliCetak'] <= 0) {
		echo ErrorMsg('Status Mahasiswa Belum Aktif', "Mahasiswa tidak dapat mencetak KSS karena statusnya tidak aktif");
	} else {
		echo Konfirmasi("Cetak KSS", "Mahasiswa dengan <br />NIM : <b>$_SESSION[mhswid]</b><br />Nama : <b>$mhsw[Nama]</b><br /> Telah mencetak KSS sebanyak $khs[KaliCetak].<br />
										<input type=button name='Cetak' value='Cetak KSS' onClick=\"location='cetak/cetak.reprintkss.go.php?khsid=$khs[KHSID]&mhswid=$_SESSION[mhswid]&tahun=$_SESSION[tahun]'\">");
	}
}
/*
function reprint(){
  global $_HeaderPrn, $_lf;
  $mhswid = $_SESSION['mhswid'];
	$tahun = $_SESSION['tahun'];
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join fakultas f on prd.FakultasID=f.FakultasID", 
    'm.MhswID', $mhswid, 
    "m.MhswID, m.Nama, m.TempatLahir, m.TanggalLahir, m.PenasehatAkademik, 
    m.Alamat, m.Kota, m.KodePos, 
    m.ProdiID, m.ProgramID, m.BIPOTID,
    prd.Nama as PRD, prg.Nama as PRG, f.Nama as FAK");
  $khs = GetFields('khs', "TahunID = '$tahun' and MhswID", $mhswid, "*");
	CetakKSS1($tahun, $mhsw, $khs);	
}

function CetakKSS1($tahun, $mhsw, $khs) {
  global $_HeaderPrn, $_lf;
  if ($stm['Nilai'] == 0) {}  
  // Buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(18).chr(27).chr(15));
  //.chr(27).chr(67).chr(18)
  fwrite($f, $_lf.$_lf);
  // Isinya
  $brs = 15;
  $arr = array();
  $div = str_pad('', 154, '-'). $_lf;
  for ($i=0; $i <= $brs; $i++) $arr[$i] = '';
  TuliskanDataUtama($mhsw, $khs, $arr);
  if ($stm['Nilai'] == 0) TuliskanStatusMhsw($mhsw, $khs, $arr, $stm);
  else TuliskanIsiKRS($mhsw, $khs, $arr);
  TuliskanKanan($mhsw, $khs, $arr);
  
  for ($i=0; $i <= $brs; $i++) fwrite($f, $arr[$i].$_lf);
  fwrite($f, chr(27).chr(18).chr(67).chr(66));
  //fwrite($f, chr(12));  
  fclose($f);
  include "dwoprn.php";
  DownloadDWOPRN($nmf);
}
function GagalCetak($mhsw, $khs) {
  echo ErrorMsg("Tidak Dapat Dicetak",
    "Tidak dapat mencetak Kartu Studi Semester (KSS) karena sudah pernah dicetak. <br />
    Sudah pernah dicetak <b>$khs[KaliCetak]</b> kali.
    <hr size=1 color=silver>
    Pilihan: <input type=button name='Tutup' value='Tutup' onClick='javascript:window.close()'>");
}
function TuliskanDataUtama($mhsw, $khs, &$arr) {
  $mrg = str_pad(' ', 15);
  $TGL = FormatTanggal($mhsw['TanggalLahir']);
  $SKRG = date('d-m-Y');
  $thn = GetFields("tahun", "ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and TahunID",
    $khs['TahunID'], "Nama, date_format(TglAkhirKSS, '%d-%m-%Y') as TNIL");
  $rek = GetaField('pejabat', 'JabatanID', 'REKTOR', 'Nama');
  $pa = GetaField('dosen d', "d.Login", $mhsw['PenasehatAkademik'], "concat(d.Nama, ', ', d.Gelar)");
  $arr[0] .= $mrg . str_pad($thn['Nama'], 59);
  $arr[1] .= $mrg . str_pad($mhsw['MhswID'], 59);
  $arr[2] .= $mrg . str_pad($mhsw['Nama'], 59);
  $arr[3] .= $mrg . str_pad($mhsw['TempatLahir'] . ', '. $TGL, 59);
  $arr[4] .= $mrg . str_pad($mhsw['Alamat'] . ' '. $mhsw['KodePos'], 59);
  $arr[5] .= $mrg . str_pad($mhsw['Kota'], 59);
  $arr[6] .= $mrg . str_pad($mhsw['FAK']. '/ '. $mhsw['PRD'], 59);
  $arr[7] .= $mrg . str_pad($pa, 59);
  $arr[8] .= $mrg . str_pad(' ', 30) . str_pad($SKRG, 29);
  $arr[9] .= str_pad(' ', 74);
  $arr[10] .= str_pad('   ' . $thn['TNIL'], 74);
  $arr[11] .= str_pad(' ', 74);
  //$arr[11] .= str_pad(' ', 74); 
  $arr[12] .= str_pad(' ', 74);
  $arr[13] .= str_pad($rek, 65, ' ', STR_PAD_LEFT);
}
function TuliskanStatusMhsw($mhsw, $khs, &$arr, $stm) {
  $arr[6] .= str_pad(strtoupper($stm['Nama']), 61, ' ', STR_PAD_BOTH);
  for ($i = 1; $i < 13; $i++) $arr[$i] .= str_pad(' ', 68, ' ');
}
function TuliskanIsiKRS($mhsw, $khs, &$arr) {
  $s = "select j.MKKode, LEFT(j.Nama, 39) as NM, j.SKS, j.JenisJadwalID, krs.StatusKRSID,
    j.NamaKelas
    from krs krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
    where krs.KHSID='$khs[KHSID]' and j.JenisJadwalID='K' and j.JadwalSer = 0
    order by j.MKKode";
  $r = _query($s);
  $i = 0; $sks = 0;
  while ($w = _fetch_array($r)) {
    $sks += $w['SKS'];
    $stt = ($w['StatusKRSID'] != 'A')? " ($w[StatusKRSID])" : '';
    $w['NM'] .= ($w['JenisJadwalID'] == 'K')? '' : "($w[JenisJadwalID])";
    $arr[$i] .= str_pad($w['MKKode'], 10) .
      str_pad($w['NM'].$stt, 40) .
      str_pad($w['SKS'], 6). 
      str_pad($w['NamaKelas'], 12, ' ');
    $i++;
  }
  for ($j=$i; $j < 13; $j++) $arr[$j] .= str_pad(' ', 68, ' ');
  $arr[13] .= str_pad($sks, 60, ' ', STR_PAD_LEFT);
}
function TuliskanKanan($mhsw, $khs, &$arr) {
  $arr[6] .= $mhsw['MhswID'];
  $arr[7] .= $mhsw['Nama'];
}
*/
?>
