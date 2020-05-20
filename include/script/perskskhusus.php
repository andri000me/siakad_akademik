<?php
// Script tambahan
// Digunakan untuk menghitung SKS yg diambil mhsw

function perskskhusus($mhsw, $khs, $bipot, $ada, $pmbmhswid=1) {
  // Jumlah SKS yg diambil mhsw. 
  // Hrs diparsing krn dicek apkh mhsw jg mengambil jdwl dgn hrg yg berbeda?
  $s = "select k.TahunID, k.MhswID,
    j.JadwalID, j.SKSAsli, j.HargaStandar, j.Harga
    from krs k
      left outer join jadwal j on k.JadwalID=j.JadwalID
    where k.MhswID='$mhsw[MhswID]' and k.TahunID='$khs[TahunID]'
      and j.HargaStandar='N' ";
  $r = _query($s);
  $totharga = 0; $totsks = 0; $mk = ''; $jml = 0;
  while ($w = _fetch_array($r)) {
    $jml++;
    //echo "$w[MKKode]: $w[Nama] ($w[SKS] SKS), Harga Standar? $w[HargaStandar]:$w[Harga]<br />";
    $totsks += $w['SKSAsli'];
    $totharga += $w['Harga'];
    $mk .= "$w[MKKode] - $w[Nama]: $w[SKSAsli] SKS dgn Harga: $w[Harga] \r\n";
  }
  if ($totharga <= 0){
    //$bpt = 
    $totharga = GetaField("bipot2", "BipotNamaID = 16 and BIPOT2ID", $bipot['BIPOT2ID'], "Jumlah");
    $jml = 1;
  }
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
    $s0 = "update bipotmhsw set Besar='$totharga', Jumlah='$jml',
      PMBMhswID='$pmbmhswid',
      Catatan='Total SKS: $totsks',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BIPOTMhswID='$ada[BIPOTMhswID]' ";
    $r0 = _query($s0);
  }
}

?>
