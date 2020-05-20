<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 03 Mei 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("USM");
echo $_Themes;

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = sqling($_REQUEST['id']);
$bck = sqling($_REQUEST['bck']);

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);

// *** Functions ***
function Edit($md, $id, $bck) {
  if ($md == 0) {
    $jdl = "Edit Presenter";
    $w = GetFields('presenter', "KodeID='".KodeID."' and PresenterID", $id, "*");
	$ro = "readonly=true";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Presenter";
    $w = array();
    $w['NA'] = 'N';
	$ro = '';
  }
  else die(ErrorMsg('Error', "Mode edit tidak dikenali."));
  
  TampilkanJudul($jdl);
  // Parameters
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("id,Nama");
  echo "<p><table class=bsc cellspacing=1 align=center width=100%>
  <form action='../$_SESSION[mnux].presenter.edit.php' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='idlama' value='$w[PresenterID]' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><td class=inp>Kode:</td>
      <td class=ul1><input type=text name='id' value='$w[PresenterID]' size=5></td>
      </tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]' size=40>
      </td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
      <td class=ul1>
      <input type=checkbox name='NA' value='Y' $na /> *) Beri centang jika tidak aktif
      </td>
      </tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td>
      </tr>
  </form>
  </table></p>";
}

function Simpan($md, $id, $bck) {
  TutupScript();
  $idlama = $_REQUEST['idlama'];
  $Nama = sqling($_REQUEST['Nama']);
  $NA = (empty($_REQUEST['NA']))? 'N' : 'Y';
  
  if($id != $idlama)
  {	$ada = GetFields('presenter', "KodeID='".KodeID."' and PresenterID", $id, '*');
    if (!empty($ada))
      die(ErrorMsg('Error', "<br />Presenter dengan kode <b>$id</b> sudah ada.<br />
        Gunakan kode yang lain.
        <hr size=1 color=silver />
        <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  }	
  
  if ($md == 0) {
    $s = "update presenter
      set Nama = '$Nama',
          PresenterID = '$id',
          NA = '$NA',
          LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where KodeID = '".KodeID."' and PresenterID = '$idlama' ";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  elseif ($md == 1) {
  
  // Cek ID-nya dulu
    $s = "insert into presenter
      (PresenterID, KodeID, Nama, LoginBuat, TanggalBuat, NA)
      values
      ('$id', '".KodeID."', '$Nama', '$_SESSION[_Login]', now(), '$NA')";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  else die(ErrorMsg('Error', "Mode edit tidak ditemukan."));
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup(bck) {
    opener.location='../index.php?mnux='+bck;
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}

?>
