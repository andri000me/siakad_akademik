<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 03 Mei 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("KOMPRE");
echo $_Themes;

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = sqling($_REQUEST['id']);
$prd = sqling($_REQUEST['prd']);
// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $prd, $id);

// *** Functions ***
function Edit($md, $prd, $id) {
  if ($md == 0) {
    $jdl = "Edit Komponen";
    $w = GetFields('komprematauji', "KodeID='".KodeID."' and KompreMataUjiID", $id, "*");
  }
  elseif ($md == 1) {
    $jdl = "Tambah Komponen";
    $w = array();
    $w['NA'] = 'N';
  }
  else die(ErrorMsg('Error', "Mode edit tidak dikenali."));
  
  TampilkanJudul($jdl);
  // Parameters
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  echo "<p><table class=bsc cellspacing=1 align=center width=100%>
  <form action='../$_SESSION[mnux].setup.edit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='id' value='$w[KompreMataUjiID]' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='prd' value='$prd' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><td class=inp>Kode KOMPRE:</td>
      <td class=ul1><input type=text name='KodeKompre' value='$w[KodeKompre]'></td>
      </tr>
  <tr><td class=inp>Nama Komponen:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]'
        size=30 maxlength=50 /></td>
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

function Simpan($md, $prd, $id) {
  TutupScript();
  $KodeKompre = sqling($_REQUEST['KodeKompre']);
  $Nama = sqling($_REQUEST['Nama']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : 'Y';
  if ($md == 0) {
	$s = "update komprematauji
      set KodeKompre = '$KodeKompre',
		  Nama = '$Nama',
          Keterangan = '$Keterangan',
          NA = '$NA',
          LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where KodeID = '".KodeID."' and KompreMataUjiID = '$id' ";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux].setup.php');</script>";
  }
  elseif ($md == 1) {
    $ada = GetaField('komprematauji', "KodeKompre='$KodeKompre' and KodeID", KodeID, "KompreMataUjiID");
	if($ada)
	{	echo ErrorMsg("Simpan Gagal", 
			"Kode Mata Uji Komprehensif <b>$KodeKompre</b> telah dipakai. Harap menggunakan kode yang lain.<br>
			<input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" >");
	}
	else
	{
		$s = "insert into komprematauji
		  (KodeKompre, KodeID, Nama, ProdiID, Keterangan, LoginBuat, TanggalBuat, NA)
		  values
		  ('$KodeKompre', '".KodeID."', '$Nama', '$prd', '$Keterangan', '$_SESSION[_Login]', now(), '$NA')";
		$r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux].setup.php');</script>";
	}
  }
  else die(ErrorMsg('Error', "Mode edit tidak ditemukan."));
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup(loc) {
    opener.location='../'+loc;
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}
?>
