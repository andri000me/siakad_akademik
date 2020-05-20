<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit BIPOT Mhsw");

// *** Parameters ***
$MhswID = GetSetVar('MhswID');

// *** Main ***
TampilkanJudul("Edit BIPOT Mhsw");
$gos = (empty($_REQUEST['gos']))? "Edit" : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Edit() {
  $mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $_SESSION['MhswID'], '*');
  $optbpt = GetOption2('bipot', 'Tahun', 'Tahun Desc', $mhsw['BIPOTID'],
    "KodeID='".KodeID."' and ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]'",
    'BIPOTID');
  echo <<<ESD
  <table class=bsc width=100%>
  <form action='../$_SESSION[mnux].bipotmhsw.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='MhswID' value='$_SESSION[MhswID]' />
  
  <tr><td class=inp>NIM:</td>
      <td class=ul1><b>$mhsw[MhswID]</b></td>
      </tr>
  <tr><td class=inp>Mahasiswa:</td>
      <td class=ul1><b>$mhsw[Nama]</b></td>
      </tr>
  <tr><td class=inp>Angkatan:</td>
      <td class=ul1><b>$mhsw[TahunID]</b></td>
      </tr>
  <tr><td class=inp>Bipot:</td>
      <td class=ul1>
      <select name='BIPOTID'>$optbpt</select>
      </td></tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick="window.close()" />
      </td></tr>
  
  </form>
  </table>
ESD;
}
function Simpan() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $BIPOTID = $_REQUEST['BIPOTID'];
  // Simpan
  $s = "update mhsw
    set BIPOTID = '$BIPOTID',
        LoginEdit = '$_SESSION[_Login]',
        TanggalEdit = now()
    where KodeID = '".KodeID."' and MhswID = '$MhswID' ";
  $r = _query($s);
  TutupScript($MhswID);
}
function TutupScript($MhswID) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&MhswID=$MhswID';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
