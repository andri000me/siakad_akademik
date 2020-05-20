<?php
// Author: Emanuel Setio Dewo
// Script tambahan untuk BIPOT Mhsw
// BPP Pokok akan dibebankan ke Mhsw jika mhsw sudah mengambil SKS

function bpppokok($mhsw, $khs, $bipot, $ada='', $pmbmhswid=1) {
  // Jumlah mk yg diambil mhsw.
  $jml = GetaField('krstemp', "StatusKRSID='A' and KHSID", $khs['KHSID'], "count(*)");
  //echo "<h1>$jml</h1>";
  if (empty($ada)) {
    // Jika hanya ambil 1 matakuliah, cek apakah dia hanya ambil skripsi?
    if ($jml == 1) {
      $krs = GetFields('krstemp', "StatusKRSID='A' and KHSID", $khs['KHSID'], "*");
      $TA = GetaField("mk mk
        left outer join jenispilihan jp on mk.JenisPilihanID=jp.JenisPilihanID",
        "MKID", $krs['MKID'], "TA");
      if ($TA == 'N') TambahkanBPPPokok($mhsw, $khs, $bipot, $ada, $pmbmhswid);
      //echo $krs['MKID']; exit;
    }
    elseif ($jml > 1) TambahkanBPPPokok($mhsw, $khs, $bipot, $ada, $pmbmhswid);
  }
  else {
    $harga = ($jml > 0)? $bipot['Jumlah'] : 0;
    if ($jml > 0) {
      $s0 = "update bipotmhsw set Jumlah=1, Besar=$harga,
      PMBMhswID='$pmbmhswid',
      Catatan='Total SKS: $totsks',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BIPOTMhswID='$ada[BIPOTMhswID]' ";
      $r0 = _query($s0);
    }
  }
}
function TambahkanBPPPokok($mhsw, $khs, $bipot, $ada='', $pmbmhswid) {
  $s0 = "insert into bipotmhsw (MhswID, PMBID, TahunID, BIPOT2ID, BIPOTNamaID,
      PMBMhswID, TrxID, Jumlah, Besar, Catatan,
      LoginBuat, TanggalBuat)
      values ('$mhsw[MhswID]', '$mhsw[PMBID]', '$khs[TahunID]', '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]',
      '$pmbmhswid', '$bipot[TrxID]', 1, '$bipot[Jumlah]', 'Total Harga: $totsks x $bipot[Jumlah]',
      '$_SESSION[_Login]', now())";
  $r0 = _query($s0);
}
?>
