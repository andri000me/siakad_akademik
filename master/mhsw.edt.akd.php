<?php

// *** Main ***
if ($_SESSION['_LevelID']==120) {
	include_once "mhsw.edt.pri.php";
	$sub = 'frmPribadi';
  	$sub();
  }
  else {
	$sub = (empty($_REQUEST['sub']))? 'frmAkademik' : $_REQUEST['sub'];
	$sub();
}


// *** Functions ***
function frmAkademik() {
  global $datamhsw;
  $optdsn = GetOption2('dosen', "concat(Nama, ', ', Gelar)", 'Nama', $datamhsw['PenasehatAkademik'], 
    "", 'Login');
  $optsta = GetOption2('statusawal', "concat(StatusAwalID, ' - ', Nama)", 'Nama', $datamhsw['StatusAwalID'], '', 'StatusAwalID');
  $optsm = GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)", 'Nama', $datamhsw['StatusMhswID'], '', 'StatusMhswID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'Nama', $datamhsw['ProgramID'], "KodeID='".KodeID."'", 'ProgramID');
  $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'Nama', $datamhsw['ProdiID'], "KodeID='".KodeID."'", 'ProdiID');
  $syarat = TampilkanPMBSyarat($datamhsw);
  $arrLengkap = array('Y'=>'Lengkap', 'N'=>'Tidak Lengkap');
  $strLengkap = $arrLengkap[$datamhsw['SyaratLengkap']];
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='submodul' value='$_SESSION[submodul]' />
  <input type=hidden name='sub' value='AkademikSav' />
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]' />
  <input type=hidden name='BypassMenu' value='1' />

  <tr><td colspan=2 class=ul><b>Data Akademik Mahasiswa</td></tr>
  ".($_SESSION['_LevelID']=='1' || $_SESSION['_LevelID']=='40' || $_SESSION['_LevelID']=='42' ?
  "<tr><td class=inp>Program</td>
      <td class=ul><select name='ProgramID'>$optprg</select></td></tr>
  <tr><td class=inp>Program Studi</td>
      <td class=ul><SELECT name='ProdiID'>$optprd</SELECT><b>$datamhsw[PRD]</b></td></tr>
  <tr><td class=inp>Tahun Masuk</td>
      <td class=ul><input type=text name='TahunID' value='$datamhsw[TahunID]' size=5 maxlength=5 />
      </td></tr>
  <tr><td class=inp>Status Awal</td>
      <td class=ul><select name='StatusAwalID'>$optsta</select></td></tr>
   <tr><td class=inp>Status Mahasiswa</td>
      <td class=ul><select name='StatusMhswID'>$optsm</select></td></tr>":"")."
  <tr><td class=inp>Penasehat Akademik</td>
      <td class=ul><select name='PenasehatAkademik'>$optdsn</select></td></tr>
  <tr><td class=inp>Batas Studi</td>
      <td class=ul><input type=text name='BatasStudi' value='$datamhsw[BatasStudi]' size=10 maxlength=8></td></tr>

  <tr><td colspan=2 class=ul><b>Kelengkapan/Persyaratan</b></td></tr>
  <tr><td class=inp rowspan=2>Syarat-syarat</td><td class=ul><img src='img/$datamhsw[SyaratLengkap].gif'> $strLengkap</td></tr>
  <tr><td class=ul>$syarat</td></tr>

  <tr><td class=ul colspan=2 align=center><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form>
  </table></p>";
}
function AkademikSav() {
  // Cek Kelengkapan
  $_syarat = array();
  $_syarat = $_REQUEST['PMBSyaratID'];
  $syarat = (empty($_syarat))? '' : '.' . implode('.', $_syarat) .'.';
  // Cek Kelengkapan
  $mhsw = GetFields('mhsw', 'MhswID', $_REQUEST['mhswid'], 'StatusAwalID, ProdiID, Syarat, SyaratLengkap');
  $s = "select PMBSyaratID, Nama
    from pmbsyarat
    where NA='N' and KodeID='$_SESSION[KodeID]'
      and INSTR(StatusAwalID, '.$mhsw[StatusAwalID].') >0
      and INSTR(ProdiID, '.$mhsw[ProdiID].') >0
    order by PMBSyaratID";
  $r = _query($s);
  $lkp = True;
  if (!empty($_syarat)) {
    while ($w = _fetch_array($r)) {
      if (array_search($w['PMBSyaratID'], $_syarat) === false)
      $lkp = false;
    }
  } else $lkp = false;
  $Lengkap = ($lkp == true)? 'Y' : 'N';
  // Simpan
  $TahunID = sqling($_REQUEST['TahunID']);
  $s = "update mhsw 
    set PenasehatAkademik='$_REQUEST[PenasehatAkademik]', ProgramID='$_REQUEST[ProgramID]',ProdiID='$_REQUEST[ProdiID]',
        TahunID = '$TahunID',
        StatusAwalID='$_REQUEST[StatusAwalID]', StatusMhswID='$_REQUEST[StatusMhswID]',
        Syarat='$syarat', SyaratLengkap='$Lengkap', BatasStudi='$_REQUEST[BatasStudi]'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&submodul=$_SESSION[submodul]", 10);
}
?>
