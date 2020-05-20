<?php
// Costumized by Arisal Yanuarafi
// Start 	: 15 Desember 2011


// Untuk menghitung jumlah peserta kuliah
function HitungPeserta($jdwlid) {
  $jml = GetaField('krs', "StatusKRSID='A' and JadwalID", $jdwlid, "count(KRSID)")+0;
  // Simpan
  $s = "update jadwal set JumlahMhsw = '$jml' where JadwalID = '$jdwlid' ";
  $r = _query($s);
}

// Menghitung ulang jumlah SKS dan total SKS yang diambil oleh Mhs
function HitungUlangKRS($mhswid, $tahunid) {
  $khs = GetFields('khs', 'MhswID='.$mhswid.' and TahunID', $tahunid, '*');
  $sks = GetaField('krs',
    "TahunID = '$khs[TahunID]' and NA = 'N' and MhswID", $khs['MhswID'],
    "sum(SKS)")+0;
  $totalsks = GetaField('khs', "Sesi <= $khs[Sesi] and MhswID", $khs['MhswID'], "sum(SKS)")+0;
  $s = "update khs
    set SKS = '$sks',
		TotalSKS = '$totalsks'
    where TahunID = '$tahunid'
	AND MhswID= '$mhswid' ";
  $r = _query($s);
  
  
  
}


?>
