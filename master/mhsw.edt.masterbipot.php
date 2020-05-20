<?php

function BPTEDT() {
	global $datamhsw, $mnux, $pref;
  //$mhswid = $_REQUEST['mhswid'];
	$mhswid = $datamhsw['MhswID'];
  $w = GetFields('mhsw', 'MhswID', $mhswid, '*');
  $bipotid = GetOption2("bipot", "concat(Tahun, ' - ', Nama, ' - ', Def)", 'Tahun',
    $w['BIPOTID'], "ProgramID='$w[ProgramID]' and ProdiID='$w[ProdiID]'", 'BIPOTID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='mhswid' value='$mhswid'>
  <input type=hidden name='slntx' value='BPTSAV'>
	<input type=hidden name='slnt' value='mhsw.edt.masterbipot'>
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>

  <tr><th class=ttl colspan=2>Edit Master Biaya & Potongan Mhsw</th></tr>
  <tr><td class=inp1>NPM</td><td class=ul>$w[MhswID]</td></tr>
  <tr><td class=inp1>Nama</td><td class=ul>$w[Nama]</td></tr>
  <tr><td class=inp1>Program</td><td class=ul>$w[ProgramID]</td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul>$w[ProdiID]</td></tr>
  <tr><td class=inp1>Biaya dan Potongan</td><td class=ul><select name='BIPOTID'>$bipotid</select></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhsw.edt.masterbipot'\"></td></tr>
  </form></table></p>";
}
function BPTSAV() {
  $s = "update mhsw set BIPOTID='$_REQUEST[BIPOTID]'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
  //CariMhsw1();
}

?>