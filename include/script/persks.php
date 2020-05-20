<?php
// Script tambahan
// Digunakan untuk menghitung SKS yg diambil mhsw

function persks($mhsw, $khs, $bipot, $ada='', $pmbmhswid=1) {
  // Jumlah SKS yg diambil mhsw.
  $jmlsks = 0;
  $whr = "MhswID='$mhsw[MhswID]'";
  if ($khs['Sesi'] == 1) {
    $jmlsks = GetaField("prodi", "ProdiID", $mhsw['ProdiID'], "DefSKS")+0; 
    $mhsw['MhswID'] = $mhsw['PMBID'];  
    $whr = "PMBID='$mhsw[PMBID]'";
    //var_dump($ada);
  }  
  else {
    //$TabelKRS = ($_REQUEST['DariKRS'] == 0)? "krs" : "krstemp"; // Apakah diakses dari modul KRS?
    if ($_REQUEST['DariKRS'] > 0)
      $jmlsks = GetaField("krstemp k
        left outer join jadwal j on k.JadwalID=j.JadwalID",
        "k.MhswID='$mhsw[MhswID]' and k.TahunID='$khs[TahunID]' and j.JenisJadwalID='K' and j.JadwalSer=0 and k.NA='N' and StatusKRSID='A' and j.HargaStandar", 'Y',
        "sum(k.SKS)")+0;
    else {
      $jmlsks = GetaField("krs k
        left outer join jadwal j on k.JadwalID=j.JadwalID",
        "k.MhswID='$mhsw[MhswID]' and k.TahunID='$khs[TahunID]' and j.JenisJadwalID='K' and j.JadwalSer=0 and k.NA='N' and StatusKRSID='A' and j.HargaStandar", 'Y',
        "sum(k.SKS)")+0;
      if ($jmlsks == 0)
        $jmlsks = GetaField("krstemp k
        left outer join jadwal j on k.JadwalID=j.JadwalID",
        "k.MhswID='$mhsw[MhswID]' and k.TahunID='$khs[TahunID]' and j.JenisJadwalID='K' and j.JadwalSer=0 and k.NA='N' and StatusKRSID='A' and j.HargaStandar", 'Y',
        "sum(k.SKS)")+0;
    }
      
  }
  if (empty($ada)) {
    $s0 = "insert into bipotmhsw (MhswID, PMBID, TahunID, BIPOT2ID, BIPOTNamaID,
      PMBMhswID, TrxID, Jumlah, Besar, Catatan,
      LoginBuat, TanggalBuat)
      values ('$mhsw[MhswID]', '$mhsw[PMBID]', '$khs[TahunID]', '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]',
      '$pmbmhswid', '$bipot[TrxID]', '$jmlsks', '$bipot[Jumlah]', 'Total Harga: $jmlsks x $bipot[Jumlah]',
      '$_SESSION[_Login]', now())";
    $r0 = _query($s0);
  }
  else {
    $s0 = "update bipotmhsw set Jumlah='$jmlsks', Besar='$bipot[Jumlah]',
      PMBMhswID='$pmbmhswid',
      Catatan='Total: $jmlsks SKS',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where TahunID='$khs[TahunID]' and $whr and BIPOTNamaID='$bipot[BIPOTNamaID]' ";
    //echo "<pre>$s0</pre>";
    $r0 = _query($s0);
  }
}

?>
