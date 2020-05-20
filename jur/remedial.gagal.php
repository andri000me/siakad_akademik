<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 22 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Gagal Penilaian");

// *** Parameters ***
$id = $_REQUEST['id'];
$jdwl = GetFields('jadwalremedial', 'JadwalRemedialID', $id, '*');
if ($jdwl['Final'] == 'Y')
  die(ErrorMsg('Error',
    "Matakuliah sudah difinalisasi."));
    
// *** Main ***
TampilkanJudul("Gagal Penilaian");
$gos = (empty($_REQUEST['gos']))? "KonfirmasiGagal" : $_REQUEST['gos'];
$gos($jdwl);

// *** Functions ***
function KonfirmasiGagal($jdwl) {
  $optnilai = GetOption2('nilai', "concat(Nama, ' (', Bobot, ')')", 'Bobot desc', '', "ProdiID='$jdwl[ProdiID]' ", "NilaiID");
  echo Konfirmasi("Konfirmasi Gagal Penilaian",
    "<form action='../$_SESSION[mnux].gagal.php' method=POST>
    <input type=hidden name='id' value='$jdwl[JadwalRemedialID]' />
    <input type=hidden name='gos' value='Gagalkan' />
    
    <p>Benar Anda akan menset gagal penilaian pada mata kuliah ini?
    Setelah digagalkan, mata kuliah akan difinalisasi dan sudah tidak dapat diubah nilainya.</p>
    
    <p>Nilai untuk semua mahasiswa:<br />
    <select name='NilaiGagal'>$optnilai</select></p>
    
    <p>Alasan penggagalan:<br />
    <textarea name='CatatanGagal' cols=40 rows=4></textarea></p>

    <hr size=1 color=silver />
    Opsi: <input type=submit name='GagalNilai' value='Gagalkan Penilaian' />
      <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
    </form>");
}
function Gagalkan($jdwl) {
  $id = $_REQUEST['id'];
  $NilaiGagal = $_REQUEST['NilaiGagal'];
  if (empty($NilaiGagal))
    echo ErrorMsg("Nilai Belum Diset",
      "Anda harus mengeset nilai gagal penilaian dosen untuk matakuliah ini.<br>
      Proses Gagal Nilai Dosen tidak dilakukan.");
  else {
    // Gagalkan jadwal
    $Nilai = GetFields('nilai', 'NilaiID', $NilaiGagal, '*');
    $BobotNilai = $Nilai['Bobot'];
    $GradeNilai = $Nilai['Nama'];
    $CatatanGagal = sqling($_REQUEST['CatatanGagal']);
    $s = "update jadwalremedial set Gagal='Y', Final = 'Y', 
      CatatanGagal = '$CatatanGagal', NilaiGagal = '$GradeNilai'
      where JadwalID = '$id' ";
    $r = _query($s);
    // Set semua nilai mahasiswa
    $s1 = "update krsremedial set GradeNilai='$GradeNilai', BobotNilai=$BobotNilai, Final='Y'
      where JadwalRemedialID='$id' and NA = 'N' ";
    $r1 = _query($s1);
	
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
}
function TutupScript($id) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=Nilai2&id=$id';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
