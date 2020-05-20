<?php

session_start();

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Ganti Foto Mhsw");

// *** Main ***
$back = $_REQUEST['back'];
$MhswID = sqling($_REQUEST['MhswID']);
$mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID, '*');
if (empty($mhsw))
  die(ErrorMsg('Error',
    "Mahasiswa dengan NIM: <b>$MhswID</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));


$gos = (empty($_REQUEST['gos']))? 'TampilkanFormulir' : $_REQUEST['gos'];
$gos($MhswID, $mhsw, $back);

// *** functions ***
function TampilkanFormulir($MhswID, $mhsw, $back) {
  $MaxFileSize = 500000;
  TampilkanJudul("Upload Foto Mahasiswa");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='../master/gantifotomhsw.php' enctype='multipart/form-data' method=POST>
  <input type=hidden name='MAX_FILE_SIZE' value='$MaxFileSize' />
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='MhswID' value='$MhswID' />
  <input type=hidden name='back' value='$back' />
  
  <tr><td class=inp>NIM:</td>
      <td class=ul>$MhswID</td></tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul>$mhsw[Nama]</td></tr>
  
  <tr><td class=inp width=100>File Foto</td>
    <td class=ul><input type=file name='foto' size=35></td></tr>
  <tr><td class=ul colspan=2 align=center>
    <input type=submit name='Upload' value='Upload File Foto' />
    <input type=button name='Batal' value='Batal' onClick='window.close()' />
    </td></tr>
  </form></table></p>";
}
function Simpan($MhswID, $mhsw, $back) {
  $upf = $_FILES['foto']['tmp_name'];
  $arrNama = explode('.', $_FILES['foto']['name']);
  $tipe = $_FILES['foto']['type'];
  $arrtipe = explode('/', $tipe);
  $extensi = $arrtipe[1];
  $dest = "foto/" . $MhswID . '.' . $extensi;
  //echo $dest;
  if (move_uploaded_file($upf, "../$dest")) {
    $s = "update mhsw set Foto='$dest' where MhswID='$MhswID' ";
    $r = _query($s);
    TutupScript($back);
  }
  else echo ErrorMsg("Gagal Upload Foto",
    "Tidak dapat meng-upload file foto.<br />
    Periksa file yg di-upload, karena besar file dibatasi cuma: <b>$_REQUEST[MAX_FILE_SIZE]</b> byte.");
  //print_r($_FILES);
}
function TutupScript($back) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='$back';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
