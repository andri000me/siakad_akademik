<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit Pengarang");

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
    $jdl = "Edit Pengarang";
    $w = GetFields('app_pustaka1.mst_author', 'author_id', $id, '*');
    $ro = "";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Pengarang";
    $w = array();
    $ro = '';
  }
  elseif ($md == 3) {
    $jdl = "Hapus Pengarang?";
    $w = GetFields('app_pustaka1.mst_author', 'author_id', $id, '*');
    $ro = "readonly=true disabled=true";
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih detail.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

  echo "<p><table class=box cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].pengarang.edt.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Nama Pengarang:</td>
      <td class=ul1><input type=text name='author_name' value='$w[author_name]' size=30 $ro /></td>
      </tr>
  <tr><td class=inp>Tipe:</td>
      <td class=ul1><input type='radio' name='authority_type' value='p' ".($w['authority_type']=='p' ? "checked":"")." $ro /> Pribadi
	  				<input type='radio' name='authority_type' value='o' ".($w['authority_type']=='o' ? "checked":"")." $ro /> Organisasi
					<input type='radio' name='authority_type' value='c' ".($w['authority_type']=='c' ? "checked":"")." $ro /> Konferensi</td>
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
  $author_id = $_REQUEST['id']+0;
  $author_name = sqling($_REQUEST['author_name']);
  $authority_type = sqling($_REQUEST['authority_type']);
  // Simpan
  if ($md == 0) {
    $s = "update app_pustaka1.mst_author
      set author_name = '$author_name',
          authority_type  = '$authority_type'
      where author_id = '$author_id' ";
    $r = _query($s);
    TutupScript($bck);
  }
  elseif ($md == 1) {
    $s = "insert ignore into app_pustaka1.mst_author
      (author_name,authority_type)
      values
      ('$author_name', '$authority_type')";
    $r = _query($s);
	
    TutupScript($bck);
  }
  elseif ($md == 3) {
    $s = "DELETE FROM app_pustaka1.mst_author
      where author_id='$author_id'";
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
