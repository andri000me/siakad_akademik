<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 10 Sept 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit Status Mhsw");

// *** Parameters ***
$KHSID = GetSetVar('KHSID');
$khs = GetFields('khs', 'KHSID', $KHSID, '*');
if (empty($khs))
  die(ErrorMsg('Error',
    "Data semester tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));

// *** Main ***
TampilkanJudul("Ubah Status Mhsw");
$gos = (empty($_REQUEST['gos']))? 'frmStatus' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function frmStatus() {
  $sta = GetaField('khs', 'KHSID', $_SESSION['KHSID'], 'StatusMhswID');
  $optsta = GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)",
    'StatusMhswID', $sta, '', 'StatusMhswID');
  echo <<<ESD
  <form action='../$_SESSION[mnux].statusmhsw.php' method=POST>
  <input type=hidden name='KHSID' value='$_SESSION[KHSID]' />
  <input type=hidden name='gos' value='Simpan' />
  
  <p align=center>
  Status Mahasiswa:<br />
  <select name='StatusMhswID'>$optsta</select>
  </p>
  <hr size=1 color=silver />
  <p align=center>
  <input type=submit name='Simpan' value='Simpan' />
  <input type=button name='Batal' value='Batal'
    onClick='window.close()' />
  </p>
  </form>
  
ESD;
}
function Simpan() {
  $KHSID = $_REQUEST['KHSID'];
  $StatusMhswID = sqling($_REQUEST['StatusMhswID']);
  // Simpan
  $s = "update khs set StatusMhswID = '$StatusMhswID'
    where KHSID = '$KHSID' ";
  $r = _query($s);
  // Tutup
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
