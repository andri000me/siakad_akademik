<?php
// Author: Emanuel Setio Dewo
// 15 March 2006

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'frmPT' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function CariPTScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function caript(frm){
    lnk = "cari/cariperguruantinggi.php?PerguruanTinggiID="+frm.AsalPT.value+"&Cari="+frm.NamaPT.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}
function frmPT() {
  global $datamhsw, $mnux, $pref;
  CariPTScript();
  $NamaPT = GetaField('perguruantinggi', 'PerguruanTinggiID', $datamhsw['AsalPT'], "concat(Nama, ', ', Kota)");
  $lulus = ($datamhsw['LulusAsalPT'] == 'Y')? 'checked' : '';
  $TglLulusAsalPT = GetDateOption($datamhsw['TglLulusAsalPT'], 'TL');
  $optjur = GetOption2('prodidikti', "concat(ProdiDiktiID, ' - ', Nama)", 'Nama', $datamhsw['ProdiAsalPT'], '', 'ProdiDiktiID');
  // Edit: Ilham
  // Line: 44, 55
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' name='data' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]' />
  <input type=hidden name='submodul' value='$_SESSION[submodul]' />
  <input type=hidden name='sub' value='PTSav' />
  <input type=hidden name='BypassMenu' value='1' />

  <tr><td colspan=2 class=ul><b>Perguruan Tinggi Asal Mahasiswa<sup> (khusus mahasiswa transfer)</sup></td></tr>

  <tr><td class=inp rowspan=2>Perguruan Tinggi</td><td class=ul><input type=text name='AsalPT' value='$datamhsw[AsalPT]' size=10 maxlength=50></td></tr>
    <tr><td class=ul><input type=text name='NamaPT' value='$NamaPT' size=50 maxlength=50> <a href='javascript:caript(data)'>Cari</a><br />
  					Isikan bagian dari Nama Asal Perguruan Tinggi, beri tanda baca koma [ , ] lalu isi Nama Kota Perguruan Tinggi.<br />
                    <b>Contoh: Politeknik, Padang</b><br /><br />
                    </td></tr>
  <tr><td class=inp>Jurusan</td><td class=ul><select name='ProdiAsalPT'>$optjur</select></td></tr>
  <tr><td class=inp>Lulus?</td><td class=ul><input type=checkbox name='LulusAsalPT' value='Y' $lulus>
    <hr size=1 color=silver />
    Lulus tahun: $TglLulusAsalPT</td></tr>
  <tr><td class=inp>Nilai IPK</td><td class=ul><input type=text name='IPKAsalPT' value='$datamhsw[IPKAsalPT]' size=5 maxlength=5></td></tr>
  <tr><td class=ul colspan=2 align=center><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function PTSav() {
  $AsalPT = $_REQUEST['AsalPT'];
  $ProdiAsalPT = $_REQUEST['ProdiAsalPT']; // Edit: Ilham
  $LulusAsalPT = (empty($_REQUEST['LulusAsalPT']))? 'N' : $_REQUEST['LulusAsalPT'];
  $TglLulusAsalPT = "$_REQUEST[TL_y]-$_REQUEST[TL_m]-$_REQUEST[TL_d]";
  echo $TglLulusAsalPT;
  $IPKAsalPT = $_REQUEST['IPKAsalPT'];
  $s = "update mhsw set AsalPT='$AsalPT', ProdiAsalPT='$ProdiAsalPT', LulusAsalPT='$LulusAsalPT', 
    TglLulusAsalPT='$TglLulusAsalPT', IPKAsalPT='$IPKAsalPT'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&submodul=$_SESSION[submodul]", 100);
}
?>
