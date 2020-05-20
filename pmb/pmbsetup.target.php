<?php
// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrPMBTarget' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function DftrPMBTarget() {
  $_PMBPeriodID = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
  $s = "select p.*,r.Nama from pmbtarget p left outer join prodi r on r.ProdiID=p.ProdiID where p.KodeID='".KodeID."' and p.PMBPeriodID='$_PMBPeriodID'
    order by p.ProdiID";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 align=center width=500>
    <form action='?' method=POST>
	<input type=hidden name='md' value='1' />
    <input type=hidden name='gos' value='target' />
    <input type=hidden name='sub' value='Edit' />
	<tr><td colspan=5><input type=submit name='Tambah' value='Tambah Target'></td>
	</tr>
	<tr>
      <th class=ttl colspan=2>Prodi</th>
	  <th class=ttl align=center>Target</th>
      <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    echo "<tr>
      <td class=ul1 width=10>
        <a href='?mnux=$_SESSION[mnux]&sub=Edit&md=0&id=$w[PMBTargetID]&ProdiID=$w[ProdiID]'><img src='img/edit.png' /></a>
        </td>
      <td class=inp width=400>$w[ProdiID] - $w[Nama]</td>
	  <td class=ul1 align=center>$w[Target]</td>
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
  $optprd = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
  // Cek mode edit
  if ($md == 0) {
    $jdl = "Edit PMB Target";
    $w = GetFields('pmbtarget', 'PMBTargetID', $id, '*');
    $_id = "<input type=text name='Target' value='$w[Target]' size=5 maxlength=5>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah PMB Target";
    $w = array();
	$w['Target'] = 0;
    $w['NA'] = 'N';
    $_id = "<input type=text name='Target' size=5 maxlength=5 />";
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Kembali' value='Kembali' onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />"));
  
  // Tampilkan formulir
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  
  CheckFormScript('Target,ProdiID');
  
  echo "<p><table class=box cellspacing=1 align=center>
  <form name='pmbgrade' action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='gos' value='target' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='sub' value='Simpan' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>ProdiID:</td>
      <td class=ul1><select name='ProdiID'>$optprd</select></td>
      </tr>
  <tr><td class=inp>Target:</td>
      <td class=ul1>$_id</td>
      </tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
      <td class=ul1>
      <input type=checkbox name='NA' value='Y' $na /> Centang jika tdk aktif.
      </td></tr>
  <tr><td class=ul1 colspan=2>
      <input type=submit name='Simpan' value='Simpan'/>
      <input type=button name='Batal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=target&sub='\" />
      </td></tr>
  
  </form>
  </table></p>";
}

function Simpan()
{	$md = $_REQUEST['md']+0;
	$id = $_REQUEST['id'];
	$Target = $_REQUEST['Target']+0;
	$ProdiID = $_REQUEST['ProdiID'];
	$NA = ($_REQUEST['NA'] == 'Y')? 'Y' : 'N';
	$_PMBPeriodID = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');

	if($md == 0)
	{	$s = "update `pmbtarget` set Target='$Target', ProdiID='$ProdiID', PMBPeriodID = '$_PMBPeriodID',
								NA='$NA', LoginEdit='$_SESSION[_Login]', TanggalEdit=now() where PMBTargetID='$id'";
		$r = _query($s);
	}
	else if($md == 1)
	{	
    $s = "insert `pmbtarget` set KodeID='".KodeID."', 
									Target='$Target', ProdiID='$ProdiID', PMBPeriodID = '$_PMBPeriodID',
                  LoginBuat='$_SESSION[_Login]', TanggalBuat=now(),
									NA='$NA'";
		$r = _query($s);
	}	
	BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=target&sub", 10);
}
?>
