<?php


include_once "header_pdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$MhswID = GetSetVar('MhswID');
$ProgramID = GetSetVar('_khsProgramID');
$Angkatan = GetSetVar('Angkatan', date('Y'));

// *** Main ***
TampilkanJudul("Cetak Kartu Ujian Tengah Semester Mahasiswa");
$gos = (empty($_REQUEST['gos']))? 'TampilkanHeader' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeader() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
  CheckFormScript('TahunID,ProdiID,Angkatan');
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
  echo <<<SELESAI
  <table class=box cellspacing=1 align=center>
  <form action='$_SESSION[mnux].cetak.php' method=POST target=_blank onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='CetakKHS' />
  <input type=hidden name='BypassMenu' value='1' />
  <tr><td class=wrn width=2 rowspan=3></td>
      <td class=inp>Tahun Akademik:</td>
      <td class=ul><input type=text id='TahunID' name='TahunID' value='$_SESSION[TahunID]' size=5 maxlength=5 /></td>
      <td class=inp>Program Studi:</td>
      <td class=ul><select id='ProdiID' name='ProdiID'>$optprodi</select></td>
	  <td class=inp>Prg. Pendidikan:</td>
      <td class=ul><select name='_khsProgramID'>$optprogram</select></td>
      </tr>
  <tr><td class=inp>Angkatan Mhsw:</td>
      <td class=ul><input type=text name='Angkatan' value='$_SESSION[Angkatan]' size=5 maxlength=5 /> atau:</td>
      <td class=inp>Mahasiswa:</td>
      <td class=ul colspan=2 nowrap>
      <input type=text name='MhswID' value='$_SESSION[MhswID]' size=20 maxlength=50 />
      <input type=submit name='btnCetak' value='Cetak Kartu UTS' /><br />
      *) Kosongkan jika ingin mencetak 1 angkatan
      </td>
      <td>Batas <input type=text name='lfrom'> sampai <input type=text name='lto'><br>
      **) kosongkan bila tidak perlu</td>
      </tr>
  <tr><td colspan=4 align=center><input type=button name='Cetak' value='Cetak Daftar Yang Tidak Bisa UTS' onClick="CetakDaftarTakBisaUjian()">
	  </td>
	  </tr>
  </form>
  </table>
  </p>
  <script>
	function CetakDaftarTakBisaUjian()
	{	
		var thn = document.getElementById('TahunID').value;
		var prd = document.getElementById('ProdiID').value;
		if (thn == '' || prd == ''){
			alert('Tahun Akademik dan Program Studi tidak boleh kosong');
		} else {
		  lnk = '$_SESSION[mnux].takujian.php?TahunID='+thn+'&ProdiID='+prd;
		  win2 = window.open(lnk, '', 'width=600, height=400, scrollbars, status');
		  if (win2.opener == null) childWindow.opener = self;
		}
	}	
  </script>
SELESAI;
}
?>
