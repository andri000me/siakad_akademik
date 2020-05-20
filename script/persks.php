<?php
// Script tambahan
// Digunakan untuk menghitung SKS yg diambil mhsw

function persks($mhsw, $khs, $bipot, $ada, $pmbmhswid=1) {
  $Nama = GetaField('bipotnama', 'BIPOTNamaID', $bipot['BIPOTNamaID'], 'Nama');
  // Apakah mhsw baru?
  if ($pmbmhswid == 0) {
    $jmlsks = GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], 'DefSKS')+0;
    if ($ada == 0) {
      $s = "insert into bipotmhsw
        (MhswID, PMBID, KodeID,
        TahunID, BIPOT2ID, BIPOTNamaID, Nama,
        PMBMhswID, TrxID, Jumlah, Besar, Catatan,
        LoginBuat, TanggalBuat)
        values
        ('$mhsw[MhswID]', '$mhsw[PMBID]', '".KodeID."',
        '$mhsw[PMBPeriodID]', '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]', '$Nama',
        0, '$bipot[TrxID]', $jmlsks, '$bipot[Jumlah]', 'Total harga: $jmlsks x $bipot[Jumlah]',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
    }
    else {
      $s = "update bipotmhsw
        set Jumlah = '$jmlsks',
            Besar = '$bipot[Jumlah]',
            Catatan = 'Total: $jmlsks x $bipot[Jumlah]',
            LoginEdit = '$_SESSION[_Login]',
            TanggalEdit = now()
        where BIPOTMhswID = '$ada' ";
      $r = _query($s);
    }
  }
  // *** Mhsw Lama ***
  else {
    $jmlsks = GetaField('krs', "TahunID = '$khs[TahunID]' and NA = 'N' and MhswID", $mhsw['MhswID'], "sum(SKS)")+0;
    if ($ada == 0) {
      $s = "insert into bipotmhsw
        (MhswID, PMBID, KodeID,
        TahunID, BIPOT2ID, BIPOTNamaID, Nama,
        PMBMhswID, TrxID, Jumlah, Besar, Catatan,
        LoginBuat, TanggalBuat)
        values
        ('$mhsw[MhswID]', '$mhsw[PMBID]', '".KodeID."',
        '$khs[TahunID]', '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]', '$Nama',
        1, '$bipot[TrxID]', $jmlsks, '$bipot[Jumlah]', 'Total harga: $jmlsks x $bipot[Jumlah]',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
    }
    else {
      $s = "update bipotmhsw
        set Jumlah = '$jmlsks',
            Besar = '$bipot[Jumlah]',
            Catatan = 'Total: $jmlsks x $bipot[Jumlah]',
            LoginEdit = now()
        where BIPOTMhswID = '$ada' ";
      $r = _query($s);
    }
  }
}
function persks_old($mhsw, $khs, $bipot, $ada='', $pmbmhswid=1) {
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
