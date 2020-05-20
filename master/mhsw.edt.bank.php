<?php
// Author: Emanuel Setio Dewo
// 05 March 2006

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'frmBank' : $_REQUEST['sub'];
$sub();

// *** Main ***
function frmBank() {
  global $datamhsw, $mnux, $pref;
  $ad = ($datamhsw['Autodebet'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>
  <input type=hidden name='submodul' value='$_SESSION[submodul]' />
  <input type=hidden name='sub' value='BankSav' />
  <input type=hidden name='BypassMenu' value='1' />

  <tr><td class=ul colspan=2><b>Autodebet</b></td></tr>
  <tr><td class=ul colspan=2>Autodebet adalah fasilitas keuangan mahasiswa yang meng-enable
    pen-debetan secara otomatis keuangan mahasiswa sehingga mahasiswa tidak perlu
    membayar semua biaya kuliah secara manual ke bank.</td></tr>
  <tr><td class=inp>Autodebet</td><td class=ul><input type=checkbox name='Autodebet' value='Y' $ad></td></tr>
  <tr><td class=inp>Nama Bank</td><td class=ul><input type=text name='NamaBank' value='$datamhsw[NamaBank]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Nomer Rekening</td><td class=ul><input type=text name='NomerRekening' value='$datamhsw[NomerRekening]' size=40 maxlength=50></td></tr>
  <tr><td colspan=2 class=ul align=center><input type=submit name='Simpan' Value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}

function BankSav() {
  $Autodebet = (empty($_REQUEST['Autodebet']))? 'N' : $_REQUEST['Autodebet'];
  $NamaBank = sqling($_REQUEST['NamaBank']);
  $NomerRekening = sqling($_REQUEST['NomerRekening']);
  $s = "update mhsw set Autodebet='$Autodebet',
    NamaBank='$NamaBank', NomerRekening='$NomerRekening'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&submodul=$_SESSION[submodul]", 10);
}

?>
