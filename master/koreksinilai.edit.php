<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Koreksi Nilai");

// *** Parameters ***
$KRSID = $_REQUEST['KRSID'];
$krs = GetFields('krs', 'KRSID', $KRSID, '*');
if (empty($krs))
  die(ErrorMsg('Error',
    "Data nilai mahasiswa tidak ditemukan.
    Hubungi Sistem Administrator untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
else if($krs['Final'] == 'N')
	die(ErrorMsg('Gagal',
    "Nilai mahasiswa ini <b>belum final</b>. Anda masih dapat menggantinya dari modul penilaian mata kuliah tersebut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
else if($krs['NA'] == 'Y')
	die(ErrorMsg('Gagal',
    "Nilai mahasiswa ini terdeteksi berasal dari mata kuliah yang telah <b>di-drop</b>. Anda tidak dapat menggantinya lagi.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
	
// *** Main ***
TampilkanJudul("Koreksi Nilai");
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($KRSID, $krs);

// *** Functions ***
function Edit($KRSID, $krs) {
  $ProdiID = GetaField('mhsw', "KodeID='".KodeID."' and MhswID", $krs['MhswID'], 'ProdiID');
  $optnilai = GetOption2('nilai', "Nama", 'Bobot desc',
    $BobotNilai, "KodeID='".KodeID."' and ProdiID='$ProdiID'", 'Bobot');
  $opttgl = GetDateOption(date('Y-m-d'), 'Tanggal');
  CheckFormScript('Nilai,SK,Perihal,Pejabat,Jabatan');
  echo <<<ESD
  <table class=bsc cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].edit.php' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='KRSID' value='$KRSID' />
  <input type=hidden name='MhswID' value='$krs[MhswID]' />
  <input type=hidden name='GradeNilaiLama' value='$krs[GradeNilai]' />
  <input type=hidden name='BobotNilaiLama' value='$krs[BobotNilai]' />
  <input type=hidden name='gos' value='Simpan' />
  <tr><td class=inp>Kode:</td>
      <td class=ul>$krs[MKKode]</td>
      <td class=inp>Matakuliah:</td>
      <td class=ul>$krs[Nama]</td>
      </tr>
  <tr><td class=inp>SKS:</td>
      <td class=ul>$krs[SKS]</td>
      <td class=inp>Tahun Akd:</td>
      <td class=ul>$krs[TahunID]</td>
      </tr>
  <tr><th class=ttl colspan=4>Nilai Asli:</th></tr>
  <tr><td class=inp>Nilai Akhir:</td>
      <td class=ul>$krs[NilaiAkhir]</td>
      <td class=inp>Grade:</td>
      <td class=ul>$krs[GradeNilai] <sup>$krs[BobotNilai]</sup></td>
      </tr>
  <tr><th class=ttl colspan=4>Ubah Menjadi:</td>
  <tr><td class=inp>Nilai:</td>
      <td class=ul colspan=3>
        <select name='Nilai'>$optnilai
        <option value='Y'>Non-Aktifkan</option></select>
      </td></tr>
  <tr><td class=inp>No. SK:</td>
      <td class=ul colspan=3>
      <input type=text name='SK' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Perihal:</td>
      <td class=ul colspan=3>
      <input type=text name='Perihal' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Tgl. SK:</td>
      <td class=ul colspan=3>
      $opttgl
      </td></tr>
  <tr><td class=inp>Pejabat:</td>
      <td class=ul colspan=3>
      <input type=text name='Pejabat' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Jabatan:</td>
      <td class=ul colspan=3>
      <input type=text name='Jabatan' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul colspan=3>
      <textarea name='Keterangan' cols=30 rows=2></textarea>
      </td></tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick='window.close()' />
      </td></tr>
  </form>
  </table>
ESD;
}
function Simpan($KRSID, $krs) {
  $KRSID = $_REQUEST['KRSID'];
  $Nilai = $_REQUEST['Nilai'];
  $SK = sqling($_REQUEST['SK']);
  $Perihal = sqling($_REQUEST['Perihal']);
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Pejabat = sqling($_REQUEST['Pejabat']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  // Nilai yg lama
  $GradeNilaiLama = sqling($_REQUEST['GradeNilaiLama']);
  $BobotNilaiLama = sqling($_REQUEST['BobotNilaiLama']);
  // Cek
  if ($Nilai =='N'){
  	$s1 = "update krs
    set NA='Y',
        LoginEdit = '$_SESSION[_Login]',
        TanggalEdit = now()
    where KRSID = '$KRSID' ";
  	$r1 = _query($s1);

  	// Simpan history
  	$s = "insert into koreksinilai
    (Tanggal, TahunID, SK, Perihal,
    KRSID, MhswID, MKID,
    GradeLama, GradeNilai,
    Pejabat, Jabatan, Keterangan,
    LoginBuat, TglBuat, NA,Modul)
    values
    ('$Tanggal', '$krs[TahunID]', '$SK', '$Perihal',
    '$KRSID', '$krs[MhswID]', '$krs[MKID]',
    '$GradeNilaiLama', '$nil[Nama]',
    '$Pejabat', '$Jabatan', '$Keterangan',
    '$_SESSION[_Login]', now(), 'N','KoreksiNilaiMhsw')";
  	$r = _query($s);
  }else{
  $nil = GetFields('nilai', 'NilaiID', $Nilai, '*');
  if ($GradeNilaiLama == $nil['Nama'])
    die(ErrorMsg('Error',
      "Anda tidak boleh mengisikan nilai yg sama dengan yg lama.<br />
      Koreksi nilai hanya berlaku untuk perubahan nilai.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Batal' value='Batal'
        onClick='window.close()' />"));
  // Simpan history
  $s = "insert into koreksinilai
    (Tanggal, TahunID, SK, Perihal,
    KRSID, MhswID, MKID,
    GradeLama, GradeNilai,
    Pejabat, Jabatan, Keterangan,
    LoginBuat, TglBuat, NA,Modul)
    values
    ('$Tanggal', '$krs[TahunID]', '$SK', '$Perihal',
    '$KRSID', '$krs[MhswID]', '$krs[MKID]',
    '$GradeNilaiLama', '$nil[Nama]',
    '$Pejabat', '$Jabatan', '$Keterangan',
    '$_SESSION[_Login]', now(), 'N','KoreksiNilaiMhsw')";
  $r = _query($s);
  // Ubat nilai
  $s1 = "update krs
    set GradeNilai = '$nil[Nama]',
        BobotNilai = '$nil[Bobot]',
        LoginEdit = '$_SESSION[_Login]',
        TanggalEdit = now()
    where KRSID = '$KRSID' ";
  $r1 = _query($s1);
}
  TutupScript();
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
