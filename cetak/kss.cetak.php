<?php
// Author: Emanuel Setio Dewo
// 17 April 2006
// Selamat Ulang Tahun Ibu
session_start();
// *** Buat File ***
include "../sisfokampus.php";
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
include_once "mhswkeu.sav.php";
Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $_HeaderPrn, $_lf;
  $mhswid = $_REQUEST['mhswid'];
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join fakultas f on prd.FakultasID=f.FakultasID", 
    'm.MhswID', $mhswid, 
    "m.MhswID, m.Nama, m.TempatLahir, m.TanggalLahir, m.PenasehatAkademik, 
    m.Alamat as Alamat, m.AlamatAsal as AlamatAsal, m.Kota, m.KotaAsal, m.KodePos, 
    m.ProdiID, m.ProgramID, m.BIPOTID,
    prd.Nama as PRD, prg.Nama as PRG, f.Nama as FAK");
  $tahun = $_REQUEST['tahun'];
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields("khs", 'KHSID', $khsid, '*');
  //if ($khs['Cetak'] == 'N') CetakKSS1($tahun, $mhsw, $khs);
  //else GagalCetak($mhsw, $khs);
  
  // Hapus Data KRS
  HapusKRS($tahun, $mhsw, $khs);
  // Import dari KRSTEMP
  ImportKRS($tahun, $mhsw, $khs);
  CetakKSS1($tahun, $mhsw, $khs);
}
function HapusKRS($tahun, $mhsw, $khs) {
  $s = "delete from krs
    where TahunID='$tahun'
      and KHSID='$khs[KHSID]' ";
  $r = _query($s);
}
function HapusKRSTemp($tahun, $mhsw, $khs) {
  $s = "delete from krstemp
    where TahunID='$tahun'
      and KHSID='$khs[KHSID]' ";
  $r = _query($s);
}

function ImportKRS($tahun, $mhsw, $khs) {
  $s = "select *
    from krstemp
    where TahunID='$tahun'
      and MhswID='$mhsw[MhswID]'
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
  }
}
function CetakKSS1($tahun, $mhsw, $khs) {
  global $_HeaderPrn, $_lf;
  // Cek Status
  $stm = GetFields('statusmhsw', 'StatusMhswID', $khs['StatusMhswID'], '*');
  if ($stm['Nilai'] == 0) {
		//delete KRS mahasiswa di krstemp
		HapusKRSTemp($tahun, $mhsw, $khs);
		//Apakah Mahasiswa sudah punya kewajiban???
		$bipot = GetFields('bipotmhsw', "TrxID = 1 and TahunID = '$tahun' and MhswID", $mhsw['MhswID'], '*, sum(Dibayar) as BYR', 'Group by MhswID');
		//echo "$bipot[MhswID]"; exit;
		if (!empty($bipot['MhswID'])) {
			$Bayar = $bipot['BYR'];
			//Apakah BPS sudah dibayar???
		  $BPSbyr = GetaField('bipotmhsw', "TrxID = 1 and TahunID = '$tahun' and MhswID", $mhsw['MhswID'], 'Dibayar');
			//Delete Keuangan Mahasiswa kecuali hutang, dan Biaya yang sudah dibayar
			$s = "delete 
						from bipotmhsw 
						where MhswID = '$mhsw[MhswID]' and
									TahunID = '$tahun' and 
									TrxID = 1 and 
									BipotNamaID <> 30";
			$r = _query($s);
		}
		if ($BPSbyr <= 0) {
		  //Ambil Jumlah BPS dari Bipot
			$BPS = GetFields('bipot2', "BipotID = $mhsw[BIPOTID] and BipotNamaID", 11, '*');
			//Masukkan Jumlah BPS ke Bipotmhsw
			$s1 = "insert into bipotmhsw(MhswID, TahunID, BIPOT2ID, BIPOTNamaID,
				PMBMhswID, TrxID, Jumlah, Besar, Dibayar, Catatan,
				LoginBuat, TanggalBuat)
				values('$mhsw[MhswID]', '$tahun', '$BPS[BIPOT2ID]', '$BPS[BIPOTNamaID]',
				'1', '$BPS[TrxID]', 1, '$BPS[Jumlah]', '$Bayar', 'Cuti atau Tunggu Ujian',
				'$_SESSION[_Login]', now())";
			$r1 = _query($s1);
			//update jumlah MK dan SKS di KHS
			
			//Hitung ulang Biaya di KHS
			
		} else {
		  $BPS = GetFields('bipot2', "BipotID = $mhsw[BIPOTID] and BipotNamaID", 11, '*');
			//Masukkan Jumlah BPS ke Bipotmhsw
			$s1 = "insert into bipotmhsw(MhswID, TahunID, BIPOT2ID, BIPOTNamaID,
				PMBMhswID, TrxID, Jumlah, Besar, Dibayar, Catatan,
				LoginBuat, TanggalBuat)
				values('$mhsw[MhswID]', '$tahun', '$BPS[BIPOT2ID]', '$BPS[BIPOTNamaID]',
				'1', '$BPS[TrxID]', 1, '$BPS[Jumlah]', '$Bayar', 'Cuti atau Tunggu Ujian',
				'$_SESSION[_Login]', now())";
			$r1 = _query($s1);
		}
		$upkhs = "update khs set JumlahMK='0', TotalSKS='0' where KHSID = '$khs[KHSID]'";
		$up = _query($upkhs);
		HitungBiaya($mhsw, $khs);
  }
  else {
    // Jika belum aktif, maka set status menjadi aktif
    $status = ($khs['StatusMhswID'] != 'A')? ", StatusMhswID='A' " : '';
    // Hitung JumlahMK and JumlahSKS
    $krsmhsw = GetFields("krs k
      left outer join jadwal j on k.JadwalID=j.JadwalID", 
      "j.JenisJadwalID='K' and k.TahunID='$tahun' and k.MhswID", $mhsw['MhswID'], 
      "count(KRSID) as JumlahMK, sum(k.SKS) as JumlahSKS");
    $JumlahMK = $krsmhsw['JumlahMK']+0;
    $JumlahSKS = $krsmhsw['JumlahSKS']+0;
    // Set kalau sudah dicetak
    $s = "update khs 
      set Cetak='Y', JumlahMK='$JumlahMK', TotalSKS='$JumlahSKS',
      KaliCetak=KaliCetak+1 $status
      where KHSID='$khs[KHSID]' ";
    $r = _query($s);
    // Hitung Jumlah MK & KRS
  }
  // Update biaya2
  $sb = "update bipotmhsw set Draft='N' 
    where MhswID='$mhsw[MhswID]' and TahunID='$khs[TahunID]' and Draft='Y' ";
  $rb = _query($sb);
  
  // Buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(18).chr(27).chr(15).chr(27).chr(67).chr(18));
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
  //$rek = GetaField('pejabat', 'JabatanID', 'REKTOR', 'Nama');
  $pa = GetaField('dosen d', "d.Login", $mhsw['PenasehatAkademik'], "concat(d.Nama, ', ', d.Gelar)");
  $Alamat = (empty($mhsw['Alamat'])) ? $mhsw['AlamatAsal'] : $mhsw['Alamat'];
	$Kota = (empty($mhsw['Kota'])) ? $mhsw['KotaAsal'] : $mhsw['Kota'];
	if (strlen($Alamat) > 45) {
		$needle = ' ';
		$piece = $pos = strripos($Alamat, $needle);
		$Alamat1 = substr($Alamat,0,$piece);
		$Alamat2 = strstr($Alamat,substr($Alamat,$piece,$piece));
	} else {
		$Alamat1 = $Alamat;
		$Alamat2 = '';
	}
	$Alamat1 = str_replace(chr(13), ' ', $Alamat1);
  $Alamat1 = str_replace(chr(10), '', $Alamat1);
	$arr[0] .= $mrg . str_pad($thn['Nama'], 59);
  $arr[1] .= $mrg . str_pad($mhsw['MhswID'], 59);
  $arr[2] .= $mrg . str_pad($mhsw['Nama'], 59);
  $arr[3] .= $mrg . str_pad($mhsw['TempatLahir'] . ', '. $TGL, 59);
  $arr[4] .= $mrg . str_pad($Alamat1, 59);
  //$arr[5] .= $mrg . str_pad($Alamat2, 59);
  $arr[5] .= $mrg . str_pad($Kota . ' '. $mhsw['KodePos'], 59);
  $arr[6] .= $mrg . str_pad($mhsw['FAK']. '/ '. $mhsw['PRD'], 59);
  $arr[7] .= $mrg . str_pad($pa, 59);
  $arr[8] .= $mrg . str_pad(' ', 30) . str_pad($SKRG, 29);
  $arr[9] .= str_pad(' ', 74);
  $arr[10] .= str_pad('   ' . $thn['TNIL'], 74);
  $arr[11] .= str_pad(' ', 74);
  $arr[12] .= str_pad(' ', 74); 
  //$arr[13] .= str_pad(' ', 74);
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
?>
