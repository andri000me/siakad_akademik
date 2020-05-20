<?php
// Author: SIAKAD TEAM
// 29 Jan 2006, Imlek

// *** Function ***
function TampilkanFormPref() {
  EditUserPassword($_SESSION['_TabelUser'], $_SESSION['_Login'], 'usrpref', 'gos', 'SimpanPwd');
  if ($_SESSION['_LevelID'] != 120)
    EditUserProfile($_SESSION['_TabelUser'], $_SESSION['_Login'], 'usrpref', 'gos', 'SimpanPref');
}
function SimpanPref() {
  SaveUserProfile();
  TampilkanFormPref();
}
function SimpanPwd() {
  SaveUserPassword();
  TampilkanFormPref();
}
function EditUserPassword($tbl, $lgn, $mnux, $gos, $gosval) {
  // JavaScript
  /*
      var pjg = frm.PWD1.value.length;
    if (pjg != 6) alert('Panjang password harus 6 karakter');
    var hsl = false;
    hsl = pjg == 6;
    if (hsl) {
      hsl = frm.PWD1.value == frm.PWD2.value;
      if (!hsl) alert('Password dan Konfirmasi Password tidak sama.');
    }
    return hsl;
  */
  echo "
  <SCRIPT LANGUAGE=\"JavaScript1.2\">
  <!--
  function CheckPWD(form) {
    strs = \"\";
    if (form.PWD1.value == \"\") strs += \"Password tidak boleh KOSONG.\\n\";
    if (form.PWD1.value != form.PWD2.value) strs += \"Password harus sama dengan Password Konfirmasi.\\n\";
    var pjg = form.PWD1.value.length;
    if (pjg < 6) strs += \"Minimal Panjang password harus 6 karakter.\\n\";
    if (strs != \"\") alert(strs);
    return strs == \"\";
  }
  -->
  </SCRIPT>\n";

  // Tuliskan formulir
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckPWD(this);\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$gos' value='$gosval'>
  <input type=hidden name='TabelUser' value='$tbl'>
  <input type=hidden name='LoginUser' value='$lgn'>
  
  <tr><th class=ttl colspan=2>Edit Password</th></tr>
  <tr><td class=inp1>Password Baru</td><td class=ul><input type=password name='PWD1' size=20 maxlength=20></td></tr>
  <tr><td class=inp1>Konfirm Password Baru</td><td class=ul><input type=password name='PWD2' size=20 maxlength=20></td></tr>
  <tr><td colspan=2><input type=submit name='Submit' value='Simpan'>
    <input type=reset value='Reset'></td></tr>
  </form></table></p>";
}
function SaveUserPassword() {
  $tbl = $_REQUEST['TabelUser'];
  $lgn = $_REQUEST['LoginUser'];
  $PWD = $_REQUEST['PWD1'];
  $s = "update $tbl set `Password`=LEFT(PASSWORD('$PWD'), 10) where Login='$lgn' ";
  $r = _query($s);
  echo "<script>alert('Password telah berhasil diubah.');</script>";
}

// *** Parameter ***
$gos = (empty($_REQUEST['gos']))? 'TampilkanFormPref' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Edit Preference");
$gos();
?>
