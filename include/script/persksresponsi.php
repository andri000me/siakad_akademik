<?php
// Author: Emanuel Setio Dewo
// 28 November 2006
// www.sisfokampus.net
// email: setio.dewo@gmail.com

// Script tambahan
// Digunakan untuk menghitung praktikum$ yg diambil mhsw

function perskskhusus($mhsw, $khs, $bipot, $ada, $pmbmhswid=1) {
  // Jumlah SKS yg diambil mhsw. 
  // Hrs diparsing krn dicek apkh mhsw jg mengambil jdwl dgn hrg yg berbeda?
  $TabelKRS = ($_REQUEST['DariKRS'] == 0)? "krs" : "krstemp"; // Apakah diakses dari modul KRS?
  $s = "select k.TahunID, k.MhswID,
    j.JadwalID, j.SKSAsli, j.HargaStandar, j.Harga, j.JenisJadwalID
    from $TabelKRS k
      left outer join jadwal j on k.JadwalID=j.JadwalID
    where k.MhswID='$mhsw[MhswID]' and k.TahunID='$khs[TahunID]'
      and j.JenisJadwalID='R' ";
  $r = _query($s);
  $totharga = 0; $totsks = 0; $mk = ''; $jml = 0;
  while ($w = _fetch_array($r)) {
    $jml++;
    //echo "$w[MKKode]: $w[Nama] ($w[SKS] SKS), Harga Standar? $w[HargaStandar]:$w[Harga]<br />";
    $totsks += $w['SKS'];
    $totharga += $w['Harga'];
    $mk .= "$w[MKKode] - $w[Nama]: $w[SKSAsli] SKS dgn Harga: $w[Harga] \r\n";
  }
  if (empty($ada) && ($totharga > 0)) {
    $s0 = "insert into bipotmhsw(MhswID, TahunID, BIPOT2ID, BIPOTNamaID,
      PMBMhswID, TrxID, Jumlah, Besar, Catatan,
      LoginBuat, TanggalBuat)
      values('$mhsw[MhswID]', '$khs[TahunID]', '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]',
      '$pmbmhswid', '$bipot[TrxID]', $totsks, '$bipot[Jumlah]', '$mk',
      '$_SESSION[_Login]', now())";
    $r0 = _query($s0);
  }
  else {
    $s0 = "update bipotmhsw set Jumlah='$totsks', Besar='$bipot[Jumlah]',
      PMBMhswID='$pmbmhswid',
      Catatan='$mk',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BIPOTMhswID='$ada[BIPOTMhswID]' ";
    $r0 = _query($s0);
  }
}
?>
