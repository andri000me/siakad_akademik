<?php
// Script tambahan
// Digunakan untuk menghitung SKS yg diambil mhsw

function perpraktikum($mhsw, $khs, $bipot, $ada, $pmbmhswid=1) {
  // Jumlah Matakuliah praktikum/responsi yg diambil mhsw. 
  $jml = GetaField('krstemp k left outer join jadwal j on k.JadwalID=j.JadwalID', 
    "k.TahunID='$khs[TahunID]' and k.MhswID='$mhsw[MhswID]' and j.JenisJadwalID", 
    'R', "count(*)") *2;
  if (($jml == 0) and (empty($mhsw['MhswID']))) $jml = 2;
  $totharga = $bipot['Jumlah'];
  if (empty($ada) && ($totharga > 0)) {
    $s0 = "insert into bipotmhsw(PMBID, MhswID, TahunID, BIPOT2ID, BIPOTNamaID,
      PMBMhswID, TrxID, Jumlah, Besar, Catatan,
      LoginBuat, TanggalBuat)
      values('$mhsw[PMBID]', '$mhsw[MhswID]', '$khs[TahunID]', '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]',
      '$pmbmhswid', '$bipot[TrxID]', $jml, '$totharga', '$mk',
      '$_SESSION[_Login]', now())";
    $r0 = _query($s0);
  }
  else {
    $s0 = "update bipotmhsw set Jumlah=$jml, Besar='$totharga',
      PMBMhswID='$pmbmhswid',
      Catatan='Total SKS: $totsks',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BIPOTMhswID='$ada[BIPOTMhswID]' ";
    $r0 = _query($s0);
  }
}

?>
