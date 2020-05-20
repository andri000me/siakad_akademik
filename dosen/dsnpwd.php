<?php

// *** Parameters ***
if ($_SESSION['_LevelID'] == 1 || $_SESSION['_LevelID'] == 43 || $_SESSION['_LevelID'] == 40 || $_SESSION['_LevelID'] == 56 || $_SESSION['_LevelID'] == 20) {
  $_DosenID = GetSetVar('_DosenID');
}
elseif ($_SESSION['_LevelID'] == 100) {
  $_DosenID = $_SESSION['_Login'];
}
else die(ErrorMsg('Error',
  "Anda tidak berhak menjalankan modul ini."));

// *** Main ***
TampilkanJudul("Ubah Password Dosen");
$gos = (empty($_REQUEST['gos']))? 'frmPwd' : $_REQUEST['gos'];
$gos($_DosenID);

// *** Functions ***
function frmPwd($_DosenID) {
  if ($_SESSION['_LevelID'] == 1 || $_SESSION['_LevelID'] == 43 || $_SESSION['_LevelID'] == 40 || $_SESSION['_LevelID'] == 56 || $_SESSION['_LevelID'] == 20) {
    $ro = '';
  }
  else {
    $ro = "readonly=true";
  }
  $dsn = GetFields('dosen', "KodeID='".KodeID."' and Login", $_DosenID,
    "Login, Nama, ProdiID, `Password`");
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmPwd' action='?' method=POST onSubmit="return CheckPassword(frmPwd)">
  <input type=hidden name='gos' value='SimpanPwd' />
  
  <tr><td class=inp width=80>Kode Login:</td>
      <td class=ul width=80>
        <input type=text name='_DosenID' value='$_DosenID' size=20 maxlength=50 $ro />
        </td>
      <td class=inp width=80>Nama Dosen:</td>
      <td class=ul><b>$dsn[Nama]</b>&nbsp;</td>
      </tr>
      
  <tr><td class=inp valign=top>Pwd Baru:</td>
      <td class=ul valign=top>
        <input type=password name='PWD1' size=11 maxlength=10 /><br />
        *) Max. 10 Karakter
      </td>
      <td class=inp valign=top>Pwd Baru:</td>
      <td class=ul valign=top>
        <input type=password name='PWD2' size=20 maxlength=10 /><br />
        *) tuliskan password baru sekali lagi
      </td>
      </tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan Password Baru' />
      </td>
      </tr>
  
  </form>
  </table>
  
  <script>
  function CheckPassword(frm) {
    var pesan = "";
    if (frm.PWD1.value == '' || frm.PWD2.value == '')
      pesan += "Password tidak boleh kosong. \\n";
    if (frm.PWD1.value.length < 4)
      pesan += "Password harus lebih dari 4 karakter. \\n";
    if (frm.PWD1.value != frm.PWD2.value)
      pesan += "Ketikkan password baru 2 kali dengan benar. \\n";
    if (pesan != "") alert(pesan);
    return pesan == "";
  }
  </script>
ESD;
}
function SimpanPwd($_DosenID) {
  $_DosenID = sqling($_REQUEST['_DosenID']);
  $PWD1 = sqling($_REQUEST['PWD1']);
  $PWD2 = sqling($_REQUEST['PWD2']);
  $s = "update dosen
    set `Password`=md5('$PWD1'),
        LevelID = '100' , LoginEdit = '$_SESSION[_Login]',TanggalEdit = now()
    where KodeID = '".KodeID."'
      and Login = '$_DosenID' ";
  $r = _query($s);
  //die("<pre>$s</pre>");
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=&_DosenID=$_DosenID", 1); 
}
?>
