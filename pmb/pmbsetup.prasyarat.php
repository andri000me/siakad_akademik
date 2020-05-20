<?php
// Author : Irvandy Goutama
// Start  : 5 September 2009
// Email  : irvandygoutama@gmail.com

// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrPrasyarat' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function DftrPrasyarat() {
  $s = "select * from pmbformsyarat where KodeID='".KodeID."'
    order by Urutan ASC";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 align=center width=600>
    <tr><td class=ul1 colspan=5><a href='?mnux=$_SESSION[mnux]&sub=Edit&md=1&id='>Tambah Prasyarat Formulir PMB</a></td></tr>
	<tr>
      <th class=ttl colspan=2>#</th>
      <th class=ttl>Nama</th>
	  <th class=ttl>Script?</th>
      <th class=ttl>Keterangan</th>
      <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
      echo "<tr>
      <td class=ul1 width=10>
        <a href='?mnux=$_SESSION[mnux]&sub=Edit&md=0&id=$w[PMBFormSyaratID]'><img src='img/edit.png' /></a>
        </td>
      <td class=inp width=20>$w[Urutan]</td>
      <td class=ul1 width=250>$w[Nama]</td>
	  <td class=ul1 width=50 align=center><img src='img/$w[AdaScript].gif'></td>
      <td class=ul1>$w[Keterangan]&nbsp;</td>
      <td class=ul1 align=center width=10>
        <img src='img/book$w[NA].gif' />
        </td>
      </tr>";
  }
  echo "</table></p>";
}

function Edit() {
  $md = $_REQUEST['md']+0;
  $id = sqling($_REQUEST['id']);
  // Cek mode edit
  if ($md == 0) {
    $jdl = "Edit Prasyarat Formulir PMB";
    $w = GetFields('pmbformsyarat', 'PMBFormSyaratID', $id, '*');
    $_id = "<input type=hidden name='id' value='$id'>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Prasyarat Formulir PMB";
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
  $adascript = ($w['AdaScript'] == 'Y')? 'checked' : '';
  
  CheckFormScript('Urutan,Nama');
  echo "<p><table class=box cellspacing=1 align=center>
  <form action='?' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='gos' value='prasyarat' />
  <input type=hidden name='sub' value='Simpan' />
  $_id
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Urutan:</td>
      <td class=ul1><input type=text name='Urutan' value='$w[Urutan]' style='text-align:right' size=1 maxlength=5></td>
      </tr>
  <tr><td class=inp>Nama Prasyarat:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Ada Script?</td>
	  <td class=ul1><input type=checkbox name='AdaScript' value='Y' $adascript ></td>
	  </tr>
  <tr><td class=inp>Script:</td>
	  <td class=ul1><input type=text name='Script' value='$w[Script]' size=35/></td>
	  </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul1><textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td>
      </tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
      <td class=ul1>
      <input type=checkbox name='NA' value='Y' $na /> Centang jika tdk aktif.
      </td></tr>
  <tr><td class=ul1 colspan=2>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=prasyarat&sub='\" />
      </td></tr>
  
  </form>
  </table></p>";
}

function Simpan() {
  $md = $_REQUEST['md']+0;
  $id = sqling($_REQUEST['id']);
  $Urutan = $_REQUEST['Urutan'];
  $Nama = sqling($_REQUEST['Nama']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $AdaScript = ($_REQUEST['AdaScript'] == 'Y')? 'Y' : 'N';
  if($AdaScript == 'Y') $Script = $_REQUEST['Script'];
  $NA = ($_REQUEST['NA'] == 'Y')? 'Y' : 'N';
  
  if($md == 0)
  {	$s = "update pmbformsyarat set Nama = '$Nama', Keterangan = '$Keterangan', AdaScript = '$AdaScript', 
				Script = '$Script', Urutan = '$Urutan', NA='$NA' where PMBFormSyaratID = '$id'" ;
	$r = _query($s);
  }
  else if($md == 1){
	
	$s = "insert into pmbformsyarat set KodeID='".KodeID."', Nama = '$Nama', Keterangan = '$Keterangan', AdaScript = '$AdaScript', 
				Script = '$Script', Urutan = '$Urutan', NA='$NA'" ;
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
