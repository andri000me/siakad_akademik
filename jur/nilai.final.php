<?php


session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Finalisasi Mata Kuliah", 1);

// *** Parameters ***
$id = $_REQUEST['id'];
$jdwl = GetFields('jadwal', 'JadwalID', $id, '*');
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
    "<p>Benar Anda akan memfinalisasi mata kuliah ini?<br />
    Setelah difinalisasi, mata kuliah sudah tidak dapat diubah nilainya.</p>
    
    <p>Cek sekali lagi. Lakukan <b>[Hitung Nilai]</b> untuk menghitung semua nilai mahasiswa.
    Baru setelah itu mata kuliah dapat difinalisasi.</p>

    <hr size=1 color=silver />
    Opsi: <input type=button name='Finalisasi' value='Finalisasi'
      onClick=\"location='../$_SESSION[mnux].final.php?id=$jdwl[JadwalID]&gos=Finalisasi'\" />
      <input type=button name='Batal' value='Batalkan' onClick=\"window.close()\" />");
}
function Finalisasi($jdwl) {
  $id = $_REQUEST['id'];
  // finalisasi jadwal
  $s = "update jadwal 
    set Final = 'Y', Gagal = 'N',
        TglEdit = now(), LoginEdit = '$_SESSION[_Login]'
    where JadwalID = $id";
  $r = _query($s);
  // finalisasi krs
  $s = "update krs
    set Final = 'Y',
        TanggalEdit = now(), LoginEdit = '$_SESSION[_Login]'
    where JadwalID = $id";
  $r = _query($s);
  
  // finalisasi jadwal uts
  $s = "update jadwaluts set Final = 'Y'
	where JadwalID = $id";
  $r = _query($s);
  $s = "update jadwaluas set Final = 'Y'
	where JadwalID = $id";
  $r = _query($s);
  
  // finalisasi jadwal responsi/lab/tutorial tambahan
  $s = "select JadwalID from jadwal where JadwalRefID = '$id' and KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {	$s1 = "update jadwal set Final = 'Y', Gagal = 'N',
			TglEdit=now(), LoginEdit = '$_SESSION[_Login]'
			where JadwalID='$w[JadwalID]'";
	$r1 = _query($s1);
	
	$s1 = "update krs
    set Final = 'Y',
        TanggalEdit = now(), LoginEdit = '$_SESSION[_Login]'
    where JadwalID = '$w[JadwalID]'";
	$r1 = _query($s1);
  }
  
  // Kembali
  TutupScript($id);
}
function TutupScript($id) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$id';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
