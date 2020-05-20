<?php

// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrSumberInfo' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function DftrSumberInfo() {
  
  $s = "select * from sumberinfo where KodeID='".KodeID."'
    order by Urutan ASC";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 align=center width=500>
    <form action='?' method=POST>
    <input type=hidden name='md' value='1' />
    <input type=hidden name='gos' value='sumberinfo' />
    <input type=hidden name='sub' value='Edit' />
	<tr><td colspan=5><input type=submit name='Tambah' value='Tambah Sumber Informasi'></td>
	</tr>
	<tr>
      <th class=ttl colspan=2>#</th>
      <th class=ttl>Nama</th>
      <th class=ttl>Catatan</th>
      <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    echo "<tr>
      <td class=ul1 width=10>
        <a href='?mnux=$_SESSION[mnux]&sub=Edit&md=0&id=$w[InfoID]'><img src='img/edit.png' /></a>
        </td>
	  <td class=inp width=10>$w[Urutan]</td>
      <td class=ul1 width=250>$w[Nama]</td>
      <td class=ul1>$w[Catatan]&nbsp;</td>
      <td class=ul1 align=center width=10>
        <img src='img/book$w[NA].gif' />
        </td>
      </tr>";
  }
  echo "</form>
	</table></p>";
}

function Edit() {
  $md = $_REQUEST['md']+0;
  $id = sqling($_REQUEST['id']);
  // Cek mode edit
  if ($md == 0) {
    $jdl = "Edit Sumber Informasi";
    $w = GetFields('sumberinfo', "InfoID='$id' and KodeID", KodeID, '*');
    $_id = "<input type=hidden name='id' value='$id'>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Sumber Informasi";
    $w = array();
    $w['NA'] = 'N';
    $_id = "";
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Kembali' value='Kembali' onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />"));
  
  // Tampilkan formulir
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript('Urutan,Nama');
  echo "<p><table class=box cellspacing=1 align=center>
  <form action='?' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='gos' value='sumberinfo' />
  <input type=hidden name='sub' value='Simpan' />
  $_id
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Urutan:</td>
	  <td class=ul1><input type=text name='Urutan' value='$w[Urutan]' style='text-align:right' size=1 maxlength=5></td></tr>
  <tr><td class=inp>Sumber Informasi:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]' size=35 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Catatan:</td>
      <td class=ul1><textarea name='Catatan' cols=30 rows=3>$w[Catatan]</textarea></td>
      </tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
      <td class=ul1>
      <input type=checkbox name='NA' value='Y' $na /> Centang jika tdk aktif.
      </td></tr>
  <tr><td class=ul1 colspan=2>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=sumberinfo&sub='\" />
      </td></tr>
  
  </form>
  </table></p>";
}

function Simpan() {
  $md = $_REQUEST['md']+0;
  $id = sqling($_REQUEST['id']);
  $Urutan = sqling($_REQUEST['Urutan'])+0;
  $Nama = sqling($_REQUEST['Nama']);
  $Catatan = sqling($_REQUEST['Catatan']);
  $NA = ($_REQUEST['NA'] == 'Y')? 'Y' : 'N';
  
  if($md == 0)
  {	$s = "update sumberinfo set Urutan='$Urutan', Nama = '$Nama', Catatan = '$Catatan', NA='$NA' where InfoID = '$id' and KodeID='".KodeID."'" ;
	$r = _query($s);
  }
  else if($md == 1){
	$s = "insert into sumberinfo set KodeID='".KodeID."', Urutan='$Urutan', Nama = '$Nama', Catatan = '$Catatan', NA='$NA'" ;
	$r = _query($s);
  }
  
  echo "<SCRIPT>
		  ttutup('$_SESSION[mnux]');
		  
		  function ttutup(bck) {
			location='index.php?mnux='+bck;
			return false;
		  }
		</SCRIPT>";
}
?>
