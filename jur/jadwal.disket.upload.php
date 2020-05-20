<?php
// Author: Emanuel Setio Dewo
// Email : setio.dewo@gmail.com
// Blog : http://dewo.wordpress.com
// 09 Des 2006
// 

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "FormUpload" : $_REQUEST['gos'];

// *** Main ***
if (!empty($gos)) $gos();

// *** Functions ***
function FormUpload() {
  TampilkanJudul("Upload Disket Nilai");
  CheckFormScript('FileNilai');
  echo "<p><table class=box cellspacing=1>
  <form action='index.php' enctype='multipart/form-data' onSubmit='return CheckForm(this)' method=POST>
  <input type=hidden name='mnux' value='jadwal.disket.upload'>
  <input type=hidden name='gos' value='DISKSAV'>
  
  <tr><th class=ttl colspan=2>File yang Diupload</th></tr>
  <tr><td class=inp1>Nama File Nilai</td>
      <td class=ul><input type=file name='FileNilai' size=40></td></tr>
  <tr><td colspan=2>
      <input type=submit name='Upload' value='Upload & Proses Nilai'>
      </td></tr>
      
  </form></table></p>";
}
function DISKSAV() {
  TampilkanJudul("Upload File Nilai");
  $FileNilai = $_FILES['FileNilai']['tmp_name'];
  $NamaFileNilai = $_FILES['FileNilai']['name'];
  $nmf = HOME_FOLDER  .  DS . "tmp/$NamaFileNilai";
  if (move_uploaded_file($FileNilai, $nmf)) {
    if (file_exists($nmf)) {
    $isi = array();
    $isi = parse_ini_file($nmf, true);
    CekFileNilai($nmf, $isi);
    }
    else echo ErrorMsg("Proses Upload File Nilai Gagal",
      "File Nilai gagal diupload. Coba ulangi sekali lagi.
      <hr size=1>
      Opsi: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>");
  }
  else echo ErrorMsg("Proses Upload File Nilai Gagal",
    "Tidak dapat memproses file Nilai. Periksa file nilai yang diupload.
    <hr size=1>
    Opsi: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>");
}
function CekFileNilai($nmf, $isi) {
  global $_AuthorWebsite;
  TampilkanHeaderFileNilai($isi);
  // Cek apakah file nilai kompatibel?
  if ($isi['TAG']['AUTHOR'] != "EMANUEL SETIO DEWO")
    die(ErrorMsg("FATAL ERROR",
    "File nilai tidak kompatibel atau file ini bukan file nilai.<br />
    Hubungi SIM/PUSKOM atau <a href='$_AuthorWebsite' target=_blank>Sisfo Kampus</a> untuk penjelasan lebih detail.
    <hr size=1>
    Opsi: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>"));
  // Cek apakah sdh difinalisasi?
  $jdwl = GetFields('jadwal', 'JadwalID', $isi['KULIAH']['ID'], "*");
  if (!empty($jdwl)) {
    // Jika belum difinalisasi
    if ($jdwl['Final'] == 'N') {
      echo Konfirmasi("Konfirmasi Proses File Nilai",
        "Anda akan memproses file nilai ini?<br />
        Pemrosesan file nilai ini akan mengganti nilai yg ada dalam sistem.
        <hr>
        Opsi: <a href='?mnux=$_SESSION[mnux]&gos='>Batalkan Pemrosesan</a> |
        <a href='?mnux=$_SESSION[mnux]&gos=DISKPRC&nmf=$nmf'>Proses File Nilai</a>");
    }
    // Jika sdh difinalisasi
    else echo ErrorMsg("Sudah Difinalisasi",
      "Data Kuliah sudah difinalisasi.<br />
      Nilai sudah tidak dapat diubah.
      <hr size=1>
      Opsi: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>");
  }
  else echo ErrorMsg("Tidak Ada Data",
    "Data Kuliah <font size=+1>" . 
    $isi["KULIAH"]["MKKode"] . " - ".
    $isi["KULIAH"]["Nama"] . "</font> (Kelas: ".
    $isi["KULIAH"]["NamaKelas"] . ", Jenis: " .
    $isi["KULIAH"]["JenisJadwalID"] . ") Tidak ditemukan.");
}
function TampilkanHeaderFileNilai($isi) {
  echo "<p><table class=box cellspacing=1>
  <tr><th class=ttl colspan=4>Header File Nilai</th></tr>
  <tr><td class=inp>ID Jadwal</td>
      <td class=ul>" . $isi["KULIAH"]["ID"] . "</td>
      <td class=inp>Program/Program Studi</td>
      <td class=ul>" . $isi['KULIAH']['ProgramID'] . "/" . $isi['KULIAH']['ProdiID'] . "</td></tr>
  <tr><td class=inp>Matakuliah</td>
      <td class=ul>" . $isi['KULIAH']['MKKode'] . " - " . $isi['KULIAH']['Nama'] . "</td>
      <td class=inp>Kelas</td>
      <td class=ul>" . $isi['KULIAH']['NamaKelas'] . " (" . $isi['KULIAH']['JenisJadwalID'] . ")</td></tr>
  <tr><td class=inp>Waktu Kuliah</td>
      <td class=ul>" . $isi['KULIAH']['HR'] . ", " . $isi['KULIAH']['JamMulai'] . " - " . $isi['KULIAH']['JamSelesai'] . "</td>
      <td class=inp>Dosen</td>
      <td class=ul>" . $isi['KULIAH']['DSN'] . "</td></tr>
  </table>";
}
function DISKPRC() {
  $nmf = $_REQUEST['nmf'];
  if (file_exists($nmf)) {
    $isi = parse_ini_file($nmf, true);
    $JadwalID = $isi['KULIAH']['ID'];
    // upload nilai
    $Jumlah = $isi['MHSW']['Jumlah'];
    for ($i = 1; $i <= $Jumlah; $i++) {
      $_det = $isi['MHSW'][$i];
      $det = explode(',', $_det);
      $_MhswID = $det[0];
      $_Tugas1 = $det[2]+0;
      $_Tugas2 = $det[3]+0;
      $_Tugas3 = $det[4]+0;
      $_Tugas4 = $det[5]+0;
      $_Tugas5 = $det[6]+0;
      $_Presensi = $det[7]+0;
      $_UTS = $det[8]+0;
      $_UAS = $det[9]+0;
      $_NilaiAkhir = $det[10]+0;
      $_Responsi = $det[11]+0;
      $_GradeNilai = $det[12];
      $_BobotNilai = $det[13]+0;
      $s = "update krs
        set Tugas1=$_Tugas1, Tugas2=$_Tugas2, Tugas3=$_Tugas3, Tugas4=$_Tugas4, Tugas5=$_Tugas5,
        Presensi=$_Presensi, UTS=$_UTS, UAS=$_UAS, NilaiAkhir=$_NilaiAkhir, Responsi=$_Responsi, 
        GradeNilai='$_GradeNilai', BobotNilai=$_BobotNilai
        where JadwalID='$JadwalID'
          and MhswID='$_MhswID' ";
      //echo "$s<br />";
      $r = _query($s);
    }
    
    // update jadwal
    $TugasMandiri = $isi['BOBOT']['TugasMandiri']+0;
    $Tugas1 = $isi['BOBOT']['Tugas1']+0;
    $Tugas2 = $isi['BOBOT']['Tugas2']+0;
    $Tugas3 = $isi['BOBOT']['Tugas3']+0;
    $Tugas4 = $isi['BOBOT']['Tugas4']+0;
    $Tugas5 = $isi['BOBOT']['Tugas5']+0;
    $Presensi = $isi['BOBOT']['Presensi']+0;
    $UTS = $isi['BOBOT']['UTS']+0;
    $UAS = $isi['BOBOT']['UAS']+0;
    $Responsi = $isi['BOBOT']['Responsi']+0;
    
    $sj = "update jadwal set
      TugasMandiri=$TugasMandiri,
      Tugas1=$Tugas1, Tugas2=$Tugas2, Tugas3=$Tugas3, Tugas4=$Tugas4, Tugas5=$Tugas5,
      Presensi=$Presensi, UTS=$UTS, UAS=$UAS, Responsi=$Responsi
      where JadwalID=$JadwalID";
    $rj = _query($sj);
    $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, "*");
    // Hitung Nilai
    include_once "dosen.nilai.sav.php";
    HitungNilai1($JadwalID, $jdwl);
    echo Konfirmasi("Proses Selesai",
      "Proses upload nilai telah selesai.
      <hr />
      Opsi: <a href='?mnux=$_SESSION[mnux]'>Kembali</a> | 
      <b><a href='?mnux=$_SESSION[mnux]&gos=DISKFINAL&jadwalid=$JadwalID'>Finalisasi Matakuliah ini</a></b>");
    /*// verbose
    echo "<pre>";
    print_r($isi);
    echo "</pre>";
    */
  }
  else echo ErrorMsg("Gagal Proses",
    "File nilai <font size=+1>$nmf</font> tidak ditemukan.");
}
function DISKFINAL() {
  include_once "dosen.nilai.sav.php";
  $jadwalid = $_REQUEST['jadwalid'];
  FinalisasiSav();
  echo Konfirmasi("Proses Finalisasi Selesai",
    "Proses Finalisasi nilai telah selesai.<br />
    Nilai matakuliah ini sudah tidak dapat diubah lagi.
    <hr />
    Opsi: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>");
}
?>
