<?php

session_start();
include_once "../sisfokampus1.php";
include_once "../$_SESSION[mnux].lib.php";

HeaderSisfoKampus("Edit BIPOT");

// *** Parameters ***
$MhswID = sqling($_REQUEST['MhswID']);
$TahunID = sqling($_REQUEST['TahunID']);

$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0; // Jika edit, maka gunakan id ini utk edit biaya mhsw

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($MhswID, $TahunID, $md, $id);

// *** Functions ***
function Edit($MhswID, $TahunID, $md, $id) {
  if ($md == 0) {
    $jdl = "Edit Bipot Mhsw";
    $w = GetFields('bipotmhsw', 'BIPOTMhswID', $id, '*');
    $ro = "readonly=true disabled=true";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Bipot Mhsw";
    $w = array();
    $ro = '';
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih detail.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));
  // Tampilkan formulir
  if (($_SESSION['_LevelID'] == 1) || ($_SESSION['_LevelID'] == 60) || ($_SESSION['_LevelID'] == 20) ) {
    $Dibayar = "<tr>
      <td class=inp>Dibayar:</td>
      <td class=ul><input type=text name='Dibayar' value='$w[Dibayar]' size=20 maxlength=20 /></td>
      </tr>";
  }
  else {
    $Dibayar = "<input type=hidden name='Dibayar' value='$w[Dibayar]' />";
  }
  $optbipotnama = GetOption2('bipotnama', "concat(Nama, ' (', TrxID, ')')", 
    'TrxID, Urutan', $w['BIPOTNamaID'], "KodeID='".KodeID."'", 'BIPOTNamaID');
  echo "<p><table class=box cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].bipotedit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='MhswID' value='$MhswID' />
  <input type=hidden name='TahunID' value='$TahunID' />
  <input type=hidden name='id' value='$id' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>BIPOT:</td>
      <td class=ul1><select name='BIPOTNamaID' $ro>$optbipotnama</select></td>
      </tr>
  <tr><td class=inp>Jumlah:</td>
      <td class=ul1><input type=text name='Jumlah' value='$w[Jumlah]' size=3 maxlength=3 /></td>
      </td>
  <tr><td class=inp>Besar, Rp:</td>
      <td class=ul1><input type=text name='Besar' value='$w[Besar]' size=20 maxlength=20 /></td>
      </tr>
  $Dibayar
  <tr><td class=inp>Tambahan Nama</td>
      <td class=ul1><input type=text name='TambahanNama' value='$w[TambahanNama]' size=30 maxlength=200 /></td>
      </tr>
  <tr><td class=inp>Catatan:</td>
      <td class=ul1>
      <input type=text name='Catatan' value='$w[Catatan]' size=60>
      </td></tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td></tr>
  
  </form>
  </table></p>";
}
function Simpan($MhswID, $TahunID, $md, $id) {
  $BIPOTNamaID = $_REQUEST['BIPOTNamaID']+0;
  $Jumlah = $_REQUEST['Jumlah']+0;
  $Besar  = $_REQUEST['Besar']+0;
  $Dibayar = $_REQUEST['Dibayar']+0;
  $TambahanNama = sqling($_REQUEST['TambahanNama']);
  $Catatan = sqling($_REQUEST['Catatan']);
  // Simpan
  if ($md == 0) {
    $s = "update bipotmhsw
      set Jumlah = '$Jumlah',
          Besar  = '$Besar',
          Dibayar = '$Dibayar',
          TambahanNama = '$TambahanNama',
          Catatan = '$Catatan',
          LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where BIPOTMhswID = '$id' ";
    $r = _query($s);
    HitungUlangBIPOTMhsw($MhswID, $TahunID);
    TutupScript($MhswID, $TahunID);
  }
  elseif ($md == 1) {
    $bn = GetFields('bipotnama', 'BIPOTNamaID', $BIPOTNamaID, '*');
    $s = "insert into bipotmhsw
      (KodeID, PMBMhswID, MhswID, TahunID,
      BIPOT2ID, BIPOTNamaID, Nama, TrxID,
      Jumlah, Besar, Dibayar, Catatan,
      LoginBuat, TanggalBuat)
      values
      ('".KodeID."', 1, '$MhswID', '$TahunID',
      0, $BIPOTNamaID, '$bn[Nama]', $bn[TrxID],
      $Jumlah, $Besar, $Dibayar, '$Catatan',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    HitungUlangBIPOTMhsw($MhswID, $TahunID);
    TutupScript($MhswID, $TahunID);
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  
}
function TutupScript($MhswID, $TahunID) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=&MhswID=$MhswID&TahunID=$TahunID';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
