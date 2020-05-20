<?php
//if ($_SESSION['_LevelID']==120) { die('belum masa cetak lhs'); }
include_once "header_pdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('_khsProgramID');
$MhswID = GetSetVar('MhswID');
$Angkatan = GetSetVar('Angkatan', date('Y'));

// *** Main ***
TampilkanJudul("Cetak Kartu Hasil Studi Mahasiswa");
$gos = (empty($_REQUEST['gos']))? 'TampilkanHeader' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeader() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
  CheckFormScript('TahunID,ProdiID,Angkatan');
  $Sekarang = date('Y-m-d-H-i');
if ($_SESSION['_LevelID']==120){
	// baris ini dibuat untuk mengalihkan mahasiswa untuk melihat nilai, sementara.
	/*echo "<script>window.location='$_SESSION[SID]krs'</script>"; */
		$s="select DISTINCT(TahunID) as TahunID from khs where MhswID='".$_SESSION['_Login']."' order by TahunID DESC";
		$r=_query($s);
		$optThn = "<option value=''></option>";
		while ($w=_fetch_array($r)) {
			$_TahunID = $_SESSION["_krsTahunID"];
		if ($w['TahunID']==$_TahunID) {
		$optThn .="<option value='".$w['TahunID']."' selected>".$w['TahunID']."</option>";
		}
		else{
		$optThn .="<option value='".$w['TahunID']."'>".$w['TahunID']."</option>";
		}
		}
      echo "<table class=box cellspacing=1 align=center>
  <form action='".$_SESSION['mnux'].".cetak.php' method=POST target=_blank onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='CetakKHS' />
  <input type=hidden name='BypassMenu' value='1' />
  <input type=hidden name='Sekarang' value='".$Sekarang."' />
  <tr><td class=wrn width=2 rowspan=3></td>
      <td class=inp>Tahun Akademik:</td>
      <td class=ul><select name='TahunID'>".$optThn."</select></td>
      <input type=hidden name='ProdiID' value='".$_SESSION['_ProdiID']."' />
      <input type=hidden name='Angkatan' value='".$_SESSION['Angkatan']."' size=5 maxlength=5 />
      <input type=hidden name='MhswID' value='".$_SESSION['_Login']."' size=20 maxlength=50 />
<td><input type=submit name='Cetak' value='Cetak KHS' /></td>
      
      </td>
      </tr>
  </form>
  </table>
  </p>";
  }
  else {
  $s="select ProgramID,Nama from program where NA='N'";
		$r=_query($s);
		$optprogram = "<option value=''></option>";
		while ($w=_fetch_array($r)) {
		if ($w[ProgramID]==$_SESSION['_khsProgramID']) {
		$optprogram .="<option value='$w[ProgramID]' selected>$w[Nama]</option>";
		}
		else{
		$optprogram .="<option value='$w[ProgramID]'>$w[Nama]</option>";
		}
		}
  echo "<table class=box cellspacing=1 align=center>
  <form action='".$_SESSION['mnux'].".cetak.php' method=POST target=_blank onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='CetakKHS' />
  <input type=hidden name='BypassMenu' value='1' />
  <input type=hidden name='Sekarang' value='$Sekarang' />
  <tr><td class=wrn width=2 rowspan=3></td>
      <td class=inp>Tahun Akademik:</td>
      <td class=ul><input type=text name='TahunID' value='".$_SESSION['TahunID']."' size=5 maxlength=20 /></td>
      <td class=inp>Program Studi:</td>
      <td class=ul><select name='ProdiID'>".$optprodi."</select></td>
	  <td class=inp>Prg. Pendidikan:</td>
      <td class=ul><select name='_khsProgramID'>".$optprogram."</select></td>
      </tr>
  <tr><td class=inp>Angkatan Mhsw:</td>
      <td class=ul><input type=text name='Angkatan' value='".$_SESSION['Angkatan']."' size=5 maxlength=5 /> atau:</td>
      <td class=inp>Mahasiswa:</td>
      <td class=ul colspan=3 nowrap>
<input type=text name='MhswID' value='".$_SESSION['MhswID']."' size=20 maxlength=50 />
<input type=submit name='Cetak' value='Cetak KHS' /><br />
      *) Kosongkan jika ingin mencetak 1 angkatan
      </td>
      </tr>
  </form>
  </table>
  </p>";
  }
}
?>
