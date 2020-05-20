<?php
// Script tambahan
// Digunakan untuk menghitung SKS yg diambil mhsw

function bps2002($mhsw, $khs, $bipot, $ada, $pmbmhswid=1) {
  if ($khs['JumlahMK'] <= 1) {
    // Cek apakah matakuliah skripsi/tesis?
    if ($_REQUEST['DariKRS'] > 0) $krs = GetFields('krstemp', "KHSID", $khs['KHSID'], "*");
    else  $krs = GetFields('krs', "KHSID", $khs['KHSID'], "*");
    $ta = GetaField("mk m left outer join jenispilihan jp on m.JenisPilihanID=jp.JenisPilihanID", 
      "MKID", $krs['MKID'], "jp.TA");
    if ($ta == 'N') InsertBPS($mhsw, $khs, $bipot, $ada, $pmbmhswid);
    //else ResetBPS($mhsw, $khs, $bipot, $ada, $pmbmhswid);
  }
  elseif ($khs['JumlahMK'] > 1) InsertBPS($mhsw, $khs, $bipot, $ada, $pmbmhswid);
}
function ResetBPS($mhsw, $khs, $bipot, $ada, $pmbmhswid) {
  if (!empty($ada)) {
    $s0 = "update bipotmhsw set Besar=0 where BIPOTMhswID='$ada[BIPOTMhswID]' ";
    $r0 = _query($s0);
  }
}
function InsertBPS($mhsw, $khs, $bipot, $ada, $pmbmhswid=1) {
  if (empty($ada)) {
    $s0 = "insert into bipotmhsw(MhswID, TahunID, BIPOT2ID, BIPOTNamaID,
      PMBMhswID, TrxID, Jumlah, Besar, Catatan,
      LoginBuat, TanggalBuat)
      values('$mhsw[MhswID]', '$khs[TahunID]', '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]',
      '$pmbmhswid', '$bipot[TrxID]', 1, '$bipot[Besar]', '$mk',
      '$_SESSION[_Login]', now())";
    $r0 = _query($s0);
  }
  else {
    $s0 = "update bipotmhsw set Besar='$bipot[Besar]',
      PMBMhswID='$pmbmhswid',
      Catatan='Total SKS: $totsks',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BIPOTMhswID='$ada[BIPOTMhswID]' ";
    $r0 = _query($s0);
  }
}

?>
