<?php
// Script tambahan
// Digunakan untuk menghitung SKS yg diambil mhsw

function perskskhusus($mhsw, $khs, $bipot, $ada, $pmbmhswid=1) {
  $Nama = GetaField('bipotnama', 'BIPOTNamaID', $bipot['BIPOTNamaID'], 'Nama');
  // Jumlah SKS yg diambil mhsw. 
  // Hrs diparsing krn dicek apkh mhsw jg mengambil jdwl dgn hrg yg berbeda?
  $s = "select k.TahunID, k.MhswID,
    j.MKKode, j.Nama, j.NamaKelas,
    j.JadwalID, j.SKS, j.SKSAsli, j.BiayaKhusus, j.Biaya
    from krs k
      left outer join jadwal j on k.JadwalID=j.JadwalID
    where k.MhswID='$mhsw[MhswID]' and k.TahunID='$khs[TahunID]'
      and j.BiayaKhusus='Y' ";
  $r = _query($s);
  $totharga = 0; $totsks = 0; $mk = ''; $jml = 0;
  while ($w = _fetch_array($r)) {
    $jml++;
    //echo "$w[MKKode]: $w[Nama] ($w[SKS] SKS), Harga Standar? $w[HargaStandar]:$w[Harga]<br />";
    $totsks += $w['SKSAsli'];
    $totharga += $w['Biaya'];
    $mk .= "$w[MKKode]~$w[Nama]~$w[SKSAsli]~$w[Biaya]\r\n";
  }
  if ($totharga <= 0) { // override harga dengan default jika biayanya = 0
    $bpt = 16; 
    $totharga = GetaField("bipot2", "BipotNamaID = $bpt and BIPOT2ID", $bipot['BIPOT2ID'], "Jumlah");
    $jml = 1;
  }
  $jml = 1; // --> harus selalu 1
  if (empty($ada) && ($totharga > 0)) {
    $s0 = "insert into bipotmhsw
      (PMBID, MhswID, TahunID, KodeID,
      BIPOT2ID, BIPOTNamaID, Nama,
      PMBMhswID, TrxID, Jumlah, Besar, Catatan,
      LoginBuat, TanggalBuat)
      values
      ('$mhsw[PMBID]', '$mhsw[MhswID]', '$khs[TahunID]', '".KodeID."', 
      '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]', '$Nama',
      '$pmbmhswid', '$bipot[TrxID]', $jml, '$totharga', '$mk',
      '$_SESSION[_Login]', now())";
    $r0 = _query($s0);
  }
  else {
    $s0 = "update bipotmhsw 
      set Besar='$totharga',
          BIPOT2ID = '$bipot[BIPOT2ID]',
          BIPOTNamaID = '$bipot[BIPOTNamaID]',
          Nama = '$Nama', 
          Jumlah='$jml',
          PMBMhswID='$pmbmhswid',
          Catatan='$mk',
          LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BIPOTMhswID='$ada' ";
    $r0 = _query($s0);
  }
}

?>
