<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 22 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Gagal Penilaian");

// *** Parameters ***
$id = $_REQUEST['id'];
$jdwl = GetFields('jadwal', 'JadwalID', $id, '*');
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
    <input type=hidden name='id' value='$jdwl[JadwalID]' />
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
  $jadwalid = $_REQUEST['id'];
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
    $s = "update jadwal set Gagal='Y', Final = 'Y', 
      CatatanGagal = '$CatatanGagal', NilaiGagal = '$GradeNilai'
      where JadwalID = '$jadwalid' ";
    $r = _query($s);
    // Set semua nilai mahasiswa
    $s1 = "update krs set GradeNilai='$GradeNilai', BobotNilai=$BobotNilai, Final='Y'
      where JadwalID='$jadwalid' and NA = 'N' ";
    $r1 = _query($s1);
	
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
    TutupScript($jadwalid);
  }
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
