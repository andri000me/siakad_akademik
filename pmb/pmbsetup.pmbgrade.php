<?php
// *** Parameters ***

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrPMBGrade' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function DftrPMBGrade() {
  $s = "select * from pmbgrade where KodeID='".KodeID."'
    order by GradeNilai";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 align=center width=500>
    <form action='?' method=POST>
	<input type=hidden name='md' value='1' />
    <input type=hidden name='gos' value='pmbgrade' />
    <input type=hidden name='sub' value='Edit' />
	<tr><td colspan=5><input type=submit name='Tambah' value='Tambah Grade'></td>
	</tr>
	<tr>
      <th class=ttl colspan=2>Grade</th>
	  <th class=ttl align=center>Nilai Min</th>
	  <th class=ttl align=center>Nilai Max</th>
      <th class=ttl>Keterangan</th>
      <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    echo "<tr>
      <td class=ul1 width=10>
        <a href='?mnux=$_SESSION[mnux]&sub=Edit&md=0&id=$w[GradeNilai]'><img src='img/edit.png' /></a>
        </td>
      <td class=inp width=20>$w[GradeNilai]</td>
	  <td class=ul1 align=center>$w[NilaiUjianMin]</td>
	  <td class=ul1 align=center>$w[NilaiUjianMax]</td>
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
    $jdl = "Edit PMB Grade";
    $w = GetFields('pmbgrade', 'GradeNilai', $id, '*');
    $_id = "<input type=text name='id' value='$id' size=5 maxlength=5>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah PMB Grade";
    $w = array();
	$w['NilaiUjianMin'] = 0.00;
	$w['NilaiUjianMax'] = 0.00;
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
  
  CheckFormScript('id,NilaiUjianMin,NilaiUjianMax');
  
  echo "<p><table class=box cellspacing=1 align=center>
  <form name='pmbgrade' action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='gos' value='pmbgrade' />
  <input type=hidden name='sub' value='Simpan' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Grade ID:</td>
      <td class=ul1>$_id</td>
      </tr>
  <tr><td class=inp>Nilai Minimum:</td>
      <td class=ul1><input type=text name='NilaiUjianMin' value='$w[NilaiUjianMin]' size=2 maxlength=5 /></td>
      </tr>
  <tr><td class=inp>Nilai Maximum:</td>
      <td class=ul1><input type=text name='NilaiUjianMax' value='$w[NilaiUjianMax]' size=2 maxlength=5 /></td>
      </tr>
  <tr><td class=inp>Catatan:</td>
      <td class=ul1><textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td>
      </tr>
  <tr><td class=inp wdith>NA (tidak aktif)?</td>
      <td class=ul1>
      <input type=checkbox name='NA' value='Y' $na /> Centang jika tdk aktif.
      </td></tr>
  <tr><td class=ul1 colspan=2>
      <input type=submit name='Simpan' value='Simpan' onClick=\"return CekNilai()\"/>
      <input type=button name='Batal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=pmbgrade&sub='\" />
      </td></tr>
  
  </form>
  </table></p>
  <script>
	function CekNilai()
	{	if(parseInt(pmbgrade.NilaiUjianMin.value) > parseInt(pmbgrade.NilaiUjianMax.value)) 
		{	alert('Nilai Minimum tidak boleh melebihi Nilai Maksimum'); }
		return parseInt(pmbgrade.NilaiUjianMin.value) <= parseInt(mbgrade.NilaiUjianMax.value);
	}
  </script>";
}

function Simpan()
{	$md = $_REQUEST['md']+0;
	$id = $_REQUEST['id'];
	$NilaiUjianMin = $_REQUEST['NilaiUjianMin']+0;
	$NilaiUjianMax = $_REQUEST['NilaiUjianMax']+0;
	$Keterangan = sqling($_REQUEST['Keterangan']);
	$NA = ($_REQUEST['NA'] == 'Y')? 'Y' : 'N';
	
	if($md == 0)
	{	$s = "update `pmbgrade` set NilaiUjianMin='$NilaiUjianMin', NilaiUjianMax='$NilaiUjianMax', 
								Keterangan='$Keterangan', NA='$NA' where GradeNilai='$id'";
		$r = _query($s);
	}
	else if($md == 1)
	{	$s = "insert `pmbgrade` set GradeNilai='$id', KodeID='".KodeID."', 
									NilaiUjianMin='$NilaiUjianMin', NilaiUjianMax='$NilaiUjianMax',
									Keterangan='$Keterangan', NA='$NA'";
		$r = _query($s);
	}	
	BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=pmbgrade&sub", 10);
}
?>
