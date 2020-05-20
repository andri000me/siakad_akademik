<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit Penerbit");

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
    $jdl = "Edit Penerbit";
    $w = GetFields('app_pustaka1.mst_publisher', 'publisher_id', $id, '*');
    $ro = "";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Penerbit";
    $w = array();
    $ro = '';
  }
  elseif ($md == 3) {
    $jdl = "Hapus Penerbit";
    $w = GetFields('app_pustaka1.mst_publisher', 'publisher_id', $id, '*');
    $ro = "readonly=true disabled=true";
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih detail.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

  echo "<p><table class=box cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].penerbit.edit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Nama Penerbit:</td>
      <td class=ul1><input type=text name='publisher_name' value='$w[publisher_name]' size=40 $ro /></td>
      </tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Proses' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td></tr>
  
  </form>
  </table></p>";
}
function Simpan($md, $id, $bck) {
  $publisher_id = $_REQUEST['id']+0;
  $publisher_name = sqling($_REQUEST['publisher_name']);
  // Simpan
  if ($md == 0) {
    $s = "update app_pustaka1.mst_publisher
      set publisher_name = '$publisher_name'
      where publisher_id = '$publisher_id' ";
    $r = _query($s);
    TutupScript($bck);
  }
  elseif ($md == 1) {
    $s = "insert ignore into app_pustaka1.mst_publisher
      (publisher_name,input_date)
      values
      ('$publisher_name', now())";
    $r = _query($s);
	
    TutupScript($bck);
  }
  if ($md == 3) {
    $s = "DELETE FROM app_pustaka1.mst_publisher
       where publisher_id = '$publisher_id' ";
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
