<?php
// Author : Emanuel Setio Dewo
// Start  : 5 Agustus 2008
// Email  : setio.dewo@gmail.com

// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrStawal' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function DftrStawal() {
  $s = "select * from statusawal
    order by StatusAwalID";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 align=center width=500>
    <tr>
      <th class=ttl colspan=2>Kode</th>
      <th class=ttl>Status</th>
      <th class=ttl>Catatan</th>
      <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    echo "<tr>
      <td class=ul1 width=10>
        <a href='?mnux=$_SESSION[mnux]&sub=Edit&md=0&id=$w[StatusAwalID]'><img src='img/edit.png' /></a>
        </td>
      <td class=inp width=20>$w[StatusAwalID]</td>
      <td class=ul1 width=100>$w[Nama]</td>
      <td class=ul1>$w[Catatan]&nbsp;</td>
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
    $jdl = "Edit Status Awal Masuk";
    $w = GetFields('statusawal', 'StatusAwalID', $id, '*');
    $_id = "<input type=hidden name='id' value='$id'><b>$id</b>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Status Awal Masuk";
    $w = array();
    $w['NA'] = 'N';
    $_id = "<input type=text name='id' size=5 maxlength=5 />";
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Kembali' value='Kembali' onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />"));
  
  // Tampilkan formulir
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 align=center>
  <form action='?' method=POST>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='gos' value='stawal' />
  <input type=hidden name='sub' value='Simpan' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode:</td>
      <td class=ul1>$_id</td>
      </tr>
  <tr><td class=inp>Nama Status:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50 /></td>
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
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=stawal&sub='\" />
      </td></tr>
  
  </form>
  </table></p>";
}

function Simpan() {
  $md = $_REQUEST['md']+0;
  $id = sqling($_REQUEST['id']);
  $Nama = sqling($_REQUEST['Nama']);
  $BeliFormulir = ($_REQUEST['BeliFormulir'] == 'Y')? 'Y' : 'N';
  $JalurKhusus = ($_REQUEST['JalurKhusus'] == 'Y')? 'Y' : 'N';
  $TanpaTest = ($_REQUEST['TanpaTest'] == 'Y')? 'Y' : 'N';
  $Catatan = sqling($_REQUEST['Catatan']);
  $NA = ($_REQUEST['NA'] == 'Y')? 'Y' : 'N';
  
  if($md == 0)
  {	$s = "update statusawal set Nama = '$Nama', Catatan = '$Catatan', NA='$NA' where StatusawalID = '$id'" ;
	$r = _query($s);
  }
  else if($md == 1){
	$s = "insert into statusawal set id='$id', Nama = '$Nama', Catatan = '$Catatan', NA='$NA'" ;
	//$s = "insert into statusawal set Nama = '$Nama', BeliFormulir='$BeliFormulir', 
	//		JalurKhusus = '$JalurKhusus', TanpaTest = '$TanpaTest', Catatan = '$Catatan', NA='$NA'";
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
