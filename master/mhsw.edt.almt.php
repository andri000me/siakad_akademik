<?php
// Author: Emanuel Setio Dewo
// 28 Februari 2006

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'frmAlamat' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function frmAlamat() {
  global $datamhsw, $mnux, $pref;
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>
  <input type=hidden name='sub' value='AlamatSav' />
  <input type=hidden name='submodul' value='almt' />
  <input type=hidden name='BypassMenu' value='1' />

  <tr><td colspan=2 class=ul><b>Alamat Menetap</td></tr>
  <tr><td class=inp width=100>Alamat</td>
      <td class=ul><input type=text name='AlamatAsal' value='$datamhsw[AlamatAsal]' size=50 maxlength=200></td></tr>
  <tr><td class=inp>RT</td>
      <td class=ul><input type=text name='RTAsal' value='$datamhsw[RTAsal]' size=10 maxlength=5>
      RW <input type=text name='RWAsal' value='$datamhsw[RWAsal]' size=10 maxlength=5></td></tr>
  <tr><td class=inp>Kota</td>
      <td class=ul><input type=text name='KotaAsal' value='$datamhsw[KotaAsal]' size=20 maxlength=50></td>
      </tr>
  <tr><td class=inp>Kode Pos</td>
      <td class=ul><input type=text name='KodePosAsal' value='$datamhsw[KodePosAsal]' size=10 maxlength=20></td>
      </tr>
  <tr><td class=inp>Propinsi</td>
      <td class=ul><input type=text name='PropinsiAsal' value='$datamhsw[PropinsiAsal]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Negara</td>
      <td class=ul><input type=text name='NegaraAsal' value='$datamhsw[NegaraAsal]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Telepon</td>
      <td class=ul><input type=text name='TeleponAsal' value='$datamhsw[TeleponAsal]' size=30 maxlength=50></td></tr>
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function AlamatSav() {
  $AlamatAsal = sqling($_REQUEST['AlamatAsal']);
  $RTAsal = sqling($_REQUEST['RTAsal']);
  $RWAsal = sqling($_REQUEST['RWAsal']);
  $KotaAsal = sqling($_REQUEST['KotaAsal']);
  $KodePosAsal = sqling($_REQUEST['KodePosAsal']);
  $PropinsiAsal = sqling($_REQUEST['PropinsiAsal']);
  $NegaraAsal = sqling($_REQUEST['NegaraAsal']);
  $TeleponAsal = sqling($_REQUEST['TeleponAsal']);
  // Simpan
  $s = "update mhsw set AlamatAsal='$AlamatAsal',
    RTAsal='$RTAsal', RWAsal='$RWAsal',
    KotaAsal='$KotaAsal', KodePosAsal='$KodePosAsal',
    PropinsiAsal='$PropinsiAsal', NegaraAsal='$NegaraAsal', TeleponAsal='$TeleponAsal'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&submodul=almt&gos=", 100);
}

?>
