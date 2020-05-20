<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit Terbitan Berkala");

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0; // Jika edit, maka gunakan id ini
$bck = $_REQUEST['bck'];

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);

// *** Functions ***
function Edit($md, $id, $bck) {
  if ($md == 0) {
    $jdl = "Edit Jenis Terbitan Berkala";
    $w = GetFields('pustaka_kalaterbit', 'KalaTerbitID', $id, '*');
    $ro = "readonly=true disabled=true";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Jenis Terbitan Berkala";
    $w = array();
    $ro = '';
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih detail.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

  echo "<p><table class=box cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].kalaterbit.edit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Jenis Terbitan Berkala:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]' size=30 /></td>
      </tr>
  <tr><td class=inp>NA:</td>
      <td class=ul1><input type='radio' name='NA' value='Y' ".($w['NA']=='Y' ? "checked":"")." /> Ya
	  				<input type='radio' name='NA' value='N' ".($w['NA']=='N' ? "checked":"")." /> Tidak</td>
      </tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td></tr>
  
  </form>
  </table></p>";
}
function Simpan($md, $id, $bck) {
  $PenerbitID = $_REQUEST['id']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $NA = sqling($_REQUEST['NA']);
  // Simpan
  if ($md == 0) {
    $s = "update pustaka_kalaterbit
      set Nama = '$Nama',
          NA  = '$NA'
      where KalaTerbitID = '$id' ";
    $r = _query($s);
    TutupScript($bck);
  }
  elseif ($md == 1) {
    $s = "insert into pustaka_kalaterbit
      (Nama,NA)
      values
      ('$Nama', '$NA')";
    $r = _query($s);
	
    TutupScript($bck);
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  
}
function TutupScript($BCK) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../?mnux=$BCK&gos=bibliografi';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
