<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 22 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Finalisasi Mata Kuliah", 1);

// *** Parameters ***
$id = $_REQUEST['id'];
$jdwl = GetFields('jadwalremedial', 'JadwalRemedialID', $id, '*');
if ($jdwl['Final'] == 'Y')
  die(ErrorMsg('Error',
    "Matakuliah sudah difinalisasi."));
    
// *** Main ***
TampilkanJudul("Finalisasi Mata Kuliah");
$gos = (empty($_REQUEST['gos']))? "KonfirmasiFinalisasi" : $_REQUEST['gos'];
$gos($jdwl);

// *** Functions ***
function KonfirmasiFinalisasi($jdwl) {
  echo Konfirmasi("Konfirmasi Finalisasi",
    "<p>Benar Anda akan memfinalisasi mata kuliah remedial ini?<br />
    Setelah difinalisasi, mata kuliah remedial sudah tidak dapat diubah nilainya.</p>
    
    <p>Cek sekali lagi. Lakukan <b>[Hitung Nilai]</b> untuk menghitung semua nilai mahasiswa.
    Baru setelah itu mata kuliah dapat difinalisasi.</p>

    <hr size=1 color=silver />
    Opsi: <input type=button name='Finalisasi' value='Finalisasi'
      onClick=\"location='../$_SESSION[mnux].final.php?id=$jdwl[JadwalRemedialID]&gos=Finalisasi'\" />
      <input type=button name='Batal' value='Batalkan' onClick=\"window.close()\" />");
}
function Finalisasi($jdwl) {
  $id = $_REQUEST['id'];
  // finalisasi jadwal
  $s = "update jadwalremedial
    set Final = 'Y', Gagal = 'N',
        TglEdit = now(), LoginEdit = '$_SESSION[_Login]'
    where JadwalRemedialID = $id";
  $r = _query($s);
  // finalisasi krs
  $s = "update krsremedial
    set Final = 'Y',
        TanggalEdit = now(), LoginEdit = '$_SESSION[_Login]'
    where JadwalRemedialID = $id";
  $r = _query($s);
  
  // Anggap Remedial sudah beres dan bisa di-remedialkan lagi bila diperlukan lagi nanti
  $s = "select KRSID from krsremedial where JadwalRemedialID='$JadwalRemedialID' and KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {
	$s1 = "update krs set SedangRemedial = 'N' where KRSID='$w[KRSID]' and KodeID='".KodeIID."'";
	$r1 = _query($s1);
  }
  // Kembali
  TutupScript($id);
}
function TutupScript($id) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location="../$_SESSION[mnux].nilai?gos=Nilai2&id=$id";
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
