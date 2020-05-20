<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 22 September 2008

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
  $id = sqling($_REQUEST['id']);
  
  if ($md == 0) {
    $jdl = "Edit Prasyarat Wisuda";
    $w = GetFields('wisudaprasyarat', "KodeID='".KodeID."' and PrasyaratID",
      $id, '*');
    $PrasyaratID = "<input type=hidden name='id' 
      value='$w[PrasyaratID]' /><b>$w[PrasyaratID]</b>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Prasyarat Wisuda";
    $w = array();
    $PrasyaratID = "<input type=text name='id' size=20 maxlength=50 />";
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  
  // tampilkan
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  TampilkanJudul($jdl);
  echo <<<ESD
  <table class=bsc width=100%>
  <form action='../$_SESSION[mnux].prasyarat.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  
  <tr><td class=inp>Kode Prasyarat:</td>
      <td class=ul>$PrasyaratID</td>
      </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Tidak Aktif? (NA)</td>
      <td class=ul><input type=checkbox name='NA' value='Y' $na /></td>
      </tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='btnSimpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  
  </form>
  </table>
ESD;
}
function Simpan() {
  $md = $_REQUEST['md']+0;
  $id = sqling($_REQUEST['id']);
  $Nama = sqling($_REQUEST['Nama']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // Simpan
  if ($md == 0) {
    $s = "update wisudaprasyarat
      set Nama = '$Nama',
          NA = '$NA',
          LoginEdit = '$_SESSION[_Login]',
          TglEdit = now()
      where PrasyaratID = '$id' and KodeID = '".KodeID."' limit 1";
    $r = _query($s);
    //die($s);
    TutupScript();
  }
  elseif ($md == 1) {
    $ada = GetFields('wisudaprasyarat', "KodeID='".KodeID."' and PrasyaratID",
      $id, '*');
    if (empty($ada)) {
      $s = "insert into wisudaprasyarat
        (PrasyaratID, KodeID, Nama, LoginBuat, TglBuat, NA)
        values
        (upper('$id'), '".KodeID."', '$Nama', '$_SESSION[_Login]', now(), '$NA')";
      $r = _query($s);
      TutupScript();
    }
    else die(ErrorMsg('Error',
      "Kode <b>$id</b> sudah dipakai.<br />
      Gunakan kode prasyarat yang lain!
      <hr size=1 color=silver />
      <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&_tabWisuda=$_SESSION[_tabWisuda]&gos=Prasyarat';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
