<?php

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
    $jdl = "Edit Mata Uji";
    $w = GetFields('matrimatauji', "KodeID='".KodeID."' and MatriMataUjiID", $id, "*");
    $_matauji = "<input type=hidden name='id' value='$w[MatriMataUjiID]' /><b>$w[MatriMataUjiID]</b>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Mata Uji";
    $w = array();
    $w['NA'] = 'N';
    $_matauji = "<input type=text name='id' size=10 maxlength=10 />";
  }
  else die(ErrorMsg('Error', "Mode edit tidak dikenali."));
  
  TampilkanJudul($jdl);
  // Parameters
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  echo "<p><table class=bsc cellspacing=1 align=center width=100%>
  <form action='../$_SESSION[mnux].setupmatauji.edt.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><td class=inp>Kode Mata Uji Matrikulasi:</td>
      <td class=ul1>$_matauji</td>
      </tr>
  <tr><td class=inp>Nama Ujian:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]'
        size=30 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul1>
        <textarea name='Keterangan' cols=30 rows=4>$w[Keterangan]</textarea>
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
  $Nama = sqling($_REQUEST['Nama']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : 'Y';
  if ($md == 0) {
    $s = "update matrimatauji
      set Nama = '$Nama',
          Keterangan = '$Keterangan',
          NA = '$NA',
          LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where KodeID = '".KodeID."' and MatriMataUjiID = '$id' ";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  elseif ($md == 1) {
    $s = "insert into matrimatauji
      (MatriMataUjiID, KodeID, Nama, Keterangan, LoginBuat, TanggalBuat, NA)
      values
      ('$id', '".KodeID."', '$Nama', '$Keterangan', '$_SESSION[_Login]', now(), '$NA')";
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
