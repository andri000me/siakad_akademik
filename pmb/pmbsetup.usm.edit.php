<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 03 Mei 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("USM");
echo $_Themes;

// *** Parameters ***
$md = $_REQUEST['md']+0;
$Kode = sqling($_REQUEST['Kode']);
$bck = sqling($_REQUEST['bck']);

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $Kode, $bck);

// *** Functions ***
function Edit($md, $Kode, $bck) {
  if ($md == 0) {
    $jdl = "Edit Komponen";
    $w = GetFields('pmbusm', "KodeID='".KodeID."' and PMBUSMID", $Kode, "*");
    $_usm = "<input type=hidden name='Kode' value='$w[PMBUSMID]' /><b>$w[PMBUSMID]</b>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Komponen";
    $w = array();
    $w['NA'] = 'N';
    $_usm = "<input type=text name='Kode' size=10 maxlength=10 />";
  }
  else die(ErrorMsg('Error', "Mode edit tidak dikenali."));
  
  TampilkanJudul($jdl);
  // Parameters
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  if(empty($w['CaraPenempatan']))$Urut = 'checked';
  else
  {	  $Urut = ($w['CaraPenempatan'] == 'Urut')? 'checked' : '';
	  $Acak = ($w['CaraPenempatan'] == 'Acak')? 'checked' : '';
	  $Manual = ($w['CaraPenempatan'] == 'Manual')? 'checked' : '';
  }
  
  CheckFormScript('Kode,Nama');
  echo "<p><table class=bsc cellspacing=1 align=center width=100%>
  <form action='../$_SESSION[mnux].usm.edit.php' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><td class=inp>Kode USM:</td>
      <td class=ul1>$_usm</td>
      </tr>
  <tr><td class=inp>Nama Ujian:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]'
        size=30 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Cara Penempatan Cama:</td>
	  <td class=ul1><input type=radio name='CaraPenempatan' value='Urut' $Urut>Urut
				<br><input type=radio name='CaraPenempatan' value='Acak' $Acak>Acak
				<br><input type=radio name='CaraPenempatan' value='Manual' $Manual>Manual</td>
	  </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul1>
        <textarea name='Keterangan' cols=30 rows=4>$w[Keterangan]</textarea>
      </td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
      <td class=ul1>
      <input type=checkbox name='NA' value='Y' $na /> *) Beri centang jika tidak aktif
      </td>
      </tr>
  
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td>
      </tr>
  </form>
  </table></p>";
}

function Simpan($md, $Kode, $bck) {
  TutupScript();
  $Nama = sqling($_REQUEST['Nama']);
  $CaraPenempatan = $_REQUEST['CaraPenempatan'];
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : 'Y';
  if ($md == 0) {
    $s = "update pmbusm
      set Nama = '$Nama',
          CaraPenempatan = '$CaraPenempatan',
		  Keterangan = '$Keterangan',
          NA = '$NA',
          LoginEdit = '$_SESSION[_Login]',
          TglEdit = now()
      where KodeID = '".KodeID."' and PMBUSMID = '$Kode' ";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  elseif ($md == 1) {
    $s = "insert into pmbusm
      (PMBUSMID, KodeID, Nama, CaraPenempatan, Keterangan, LoginBuat, TglBuat, NA)
      values
      ('$Kode', '".KodeID."', '$Nama', '$CaraPenempatan', '$Keterangan', '$_SESSION[_Login]', now(), '$NA')";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  else die(ErrorMsg('Error', "Mode edit tidak ditemukan."));
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup(bck) {
    opener.location='../index.php?mnux='+bck;
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}
?>
