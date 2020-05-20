<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Setup Wisuda");

// *** Parameters ***

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Edit() {
  $md = $_REQUEST['md']+0;
  $id = $_REQUEST['id']+0;
  if ($md == 0) {
    $jdl = "Edit Gelombang Wisuda";
    $w = GetFields('wisuda', 'WisudaID', $id, '*');
  }
  elseif ($md == 1) {
    $jdl = "Tambah Gelombang Wisuda";
    $w = array();
    $w['TglMulai'] = date('Y-m-d');
    $w['TglSelesai'] = date('Y-m-d');
    $w['TglWisuda'] = date('Y-m-d');
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));
  // Tampilkan
  TampilkanJudul($jdl);
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $TglMulai = GetDateOption($w['TglMulai'], 'TglMulai');
  $TglSelesai = GetDateOption($w['TglSelesai'], 'TglSelesai');
  $TglWisuda = GetDateOption($w['TglWisuda'], 'TglWisuda');
  echo <<<ESD
  <table class=bsc cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].edit.php' method=POST>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td class=inp>Tahun/Gelombang:</td>
      <td class=ul><input type=text name='TahunID' value='$w[TahunID]' size=5 maxlength=5 /></td>
      </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Mulai Pendaftaran:</td>
      <td class=ul>$TglMulai</td>
      </tr>
  <tr><td class=inp>Akhir Pendaftaran:</td>
      <td class=ul>$TglSelesai</td>
      </tr>
  <tr><td class=inp>Tanggal Wisuda:</td>
      <td class=ul>$TglWisuda</td>
      </tr>
  <tr><td class=inp>Format Periode:</td>
      <td class=ul><input type=text size=10 name='PeriodeID' maxlength=15 value='$w[PeriodeID]' /> <sup> &raquo; Untuk Penomoran di Ijazah</sup></td>
      </tr>
  <tr><td class=inp>Tidak Aktif? (NA)</td>
      <td class=ul><input type=checkbox name='NA' value='Y' $na /> 
        <font color=red> *) Centang bila tidak aktif.<br />
        Hanya ada 1 gelombang yg aktif.</td>
      </tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='btnSimpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick="window.close()" />
      </td></tr>
  </form>
  </table>
ESD;
}

function Simpan() {
  if ($_SESSION['_LevelID']!='1' && $_SESSION['_LevelID']!='40'){die('Anda tidak berhak merubah status Periode Wisuda.');}
  $md = $_REQUEST['md']+0;
  $id = $_REQUEST['id']+0;
  $TahunID = sqling($_REQUEST['TahunID']);
  $Nama = sqling($_REQUEST['Nama']);
  $TglMulai = "$_REQUEST[TglMulai_y]-$_REQUEST[TglMulai_m]-$_REQUEST[TglMulai_d]";
  $TglSelesai = "$_REQUEST[TglSelesai_y]-$_REQUEST[TglSelesai_m]-$_REQUEST[TglSelesai_d]";
  $TglWisuda = "$_REQUEST[TglWisuda_y]-$_REQUEST[TglWisuda_m]-$_REQUEST[TglWisuda_d]";
  $NA = (!empty($_REQUEST['NA']))? 'Y' : 'N';
  
  if ($md == 0) {
    $sdh = GetFields('wisuda', "KodeID='".KodeID."' and WisudaID <> $id and TahunID",
      $TahunID, '*');
    if (empty($sdh)) {
      $s = "update wisuda
        set TahunID = '$TahunID',
            Nama = '$Nama',
            TglMulai = '$TglMulai',
            TglSelesai = '$TglSelesai',
            TglWisuda = '$TglWisuda',
            LoginEdit = '$_SESSION[_Login]',
            TglEdit = now(),
            NA='$NA'
        where WisudaID = '$id' ";
      $r = _query($s);
      if ($NA == 'N') Hanya($id);
      TutupScript();
    }
    else die(ErrorMsg('Error',
      "Anda tidak boleh mengganti kode Tahun dengan <b>$TahunID</b> karena sudah dipakai.<br />
      Gunakan kode Tahun yang lain.
      <hr size=1 color=silver />
      <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  }
  elseif ($md == 1) {
    $sdh = GetFields('wisuda', "KodeID='".KodeID."' and TahunID", $TahunID, '*');
    if (empty($sdh)) {
      $s = "insert into wisuda
        (KodeID, TahunID, Nama,
        TglMulai, TglSelesai, TglWisuda, Jumlah,
        LoginBuat, TglBuat, NA)
        values
        ('".KodeID."', '$TahunID', '$Nama',
        '$TglMulai', '$TglSelesai', '$TglWisuda', 0,
        '$_SESSION[_Login]', now(), '$NA')";
      $r = _query($s);
      $id = GetLastID();
      if ($NA == 'N') Hanya($id);
      TutupScript();
    }
    else die(ErrorMsg('Error',
      "Kode tahun <b>$TahunID</b> sudah dipakai.<br />
      Anda tidak boleh menggunakan kode ini lagi.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
}
function Hanya($id) {
  $s = "update wisuda set NA = 'Y' where KodeID='".KodeID."' and WisudaID <> $id ";
  $r = _query($s);
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&_tabWisuda=$_SESSION[_tabWisuda]&gos=Setup';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}

?>
