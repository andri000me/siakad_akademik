<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 23 Agustus 2008


// Untuk menghitung jumlah peserta kuliah
function HitungPeserta($jdwlid) {
  $jml = GetaField('krs', "StatusKRSID='A' and JadwalID", $jdwlid, "count(KRSID)")+0;
  // Simpan
  $s = "update jadwal set JumlahMhsw = '$jml' where JadwalID = '$jdwlid' ";
  $r = _query($s);
}

// Menghitung ulang jumlah SKS dan total SKS yang diambil oleh Mhs
function HitungUlangKRS($khsid) {
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  $sks = GetaField('krs',
    "TahunID = '$khs[TahunID]' and NA = 'N' and MhswID", $khs['MhswID'],
    "sum(SKS)")+0;
  $totalsks = GetaField('khs', "Sesi <= $khs[Sesi] and MhswID", $khs['MhswID'], "sum(SKS)")+0;
  $s = "update khs
    set SKS = '$sks',
		TotalSKS = '$totalsks'
    where KHSID = '$khsid' ";
  $r = _query($s);
  
  
  
}


?>
