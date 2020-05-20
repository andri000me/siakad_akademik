<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 26 April 2009

// *** Parameters ***
if ($_SESSION['_LevelID'] == 1) {
  $PMBID = GetSetVar('_pmbLoginPMBID');
}
elseif ($_SESSION['_LevelID'] == 33) {
  $PMBID = $_SESSION['_Login'];
}
else die(ErrorMsg('Error',
  "Anda tidak berhak menjalankan modul ini."));

// *** Main ***
TampilkanJudul("Ubah Password Calon Mahasiswa");
$gos = (empty($_REQUEST['gos']))? 'frmPwd' : $_REQUEST['gos'];
$gos($PMBID);

// *** Functions ***
function frmPwd($PMBID) {
  if ($_SESSION['_LevelID'] == 1) {
    $_PMBID = "<input type=text name='PMBID' value='$PMBID' size=20 maxlength=50 />"; 
  }
  else {
    $_PMBID = "<input type=hidden name='PMBID' value='$PMBID' /><b>$PMBID</b>";
  }
  
  $pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $PMBID,
    "PMBID, Nama, ProdiID, Password, PasswordBaru, Hint");
  
  if($pmb['PasswordBaru'] == 'Y')
  {  $Hint = "<tr>
			<td class=ul1 colspan=2><b>Masukkan Pertanyaan Sekuritas Jika Password Hilang:</b></td>
		</tr>
		<tr>
			<td class=inp>Hint:</td>
			<td class=ul1><input type=text name='Hint' maxlength=255></td>
		</tr>
		<tr>
			<td class=inp>Jawaban:</td>
			<td class=ul1><input type=text name='HintAnswer' maxlength=50></td>
		</tr>";
	  $onSubmit = "return CheckPasswordAndHint(frmPwd)";
  
  /* Tambahan Pembatasan Password. Request untuk tidak dipakai di Kasih Bangsa
	var ada = 'N';
	ada = 'N';
	for (var i = 0; i < frm.PasswordBaru1.value.length; i++) {
		if (UpperChar.indexOf(frm.PasswordBaru1.value.charAt(i)) != -1)
		{	ada = 'Y';
			break;
		}
	}
	if (ada == 'N')
	  pesan += \"Password harus mengandung minimal 1 huruf kapital (contoh: A, B, ..)\\n\";
	  
	ada = 'N';
	for (var i = 0; i < frm.PasswordBaru1.value.length; i++) {
		if (LowerChar.indexOf(frm.PasswordBaru1.value.charAt(i)) != -1)
		{	ada = 'Y';
			break;
		}
	}
	if (ada == 'N')
	  pesan += \"Password harus mengandung minimal 1 huruf tidak kapital (contoh: a, b, ..)\\n\";
	
	ada = 'N';
	for (var i = 0; i < frm.PasswordBaru1.value.length; i++) {
		if (IntegerChar.indexOf(frm.PasswordBaru1.value.charAt(i)) != -1)
		{	ada = 'Y';
			break;
		}
	}
	if (ada == 'N')
	  pesan += \"Password harus mengandung minimal 1 angka (contoh: a, b, ..)\\n\";
  */
	  $CheckScript = "<script>
						  function CheckPasswordAndHint(frm) {
							var pesan = \"\";
							var UpperChar = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
							var LowerChar = 'abcdefghijklmnopqrstuvwxyz';
							var IntegerChar = '01233456789';
							
							if (frm.PasswordBaru1.value == '' || frm.PasswordBaru2.value == '')
							  pesan += \"Password tidak boleh kosong. \\n\";
							if (frm.PasswordBaru1.value.length < 6)
							  pesan += \"Password harus lebih dari 6 karakter. \\n\";
							if (frm.PasswordBaru1.value != frm.PasswordBaru2.value)
							  pesan += \"Ketikkan password baru yang sama 2 kali. \\n\";
							
							if (frm.Hint.value == '')
							  pesan += \"Hint tidak boleh kosong. \\n\";
							if (frm.HintAnswer.value == '')
							  pesan += \"Jawaban Hint tidak boleh kosong. \\n\";
							if (pesan != \"\") alert(pesan);
							
							return pesan == \"\";
						  }
						  </script>";
  }
  else
  {	 $Hint = "<tr>
			<td class=ul1 colspan=2><b>Masukkan Jawaban Pertanyaan Sekuritas:</b></td>
		</tr>
		<tr><tr>
		<td class=inp valign=top>Hint:</td>
		<td class=ul valign=top>$pmb[Hint]</td>
	  </tr>
	  <tr>
		<td class=inp valign=top>Jawaban:</td>
		<td class=ul valign=top><input type=text name='HintAnswer'></td>
	  </tr>";
	  $onSubmit = "return CheckPassword(frmPwd)";
	  $CheckScript = "<script>
						  function CheckPassword(frm) {
							var pesan = \"\";
							if (frm.PasswordBaru1.value == '' || frm.PasswordBaru2.value == '')
							  pesan += \"Password tidak boleh kosong. \\n\";
							if (frm.PasswordBaru1.value.length < 10)
							  pesan += \"Password harus lebih dari 10 karakter. \\n\";
							if (frm.PasswordBaru1.value != frm.PasswordBaru2.value)
							  pesan += \"Ketikkan password baru yang sama 2 kali. \\n\";
							if (pesan != \"\") alert(pesan);
							return pesan == \"\";
						  }
						  </script>";
  }
  
  
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmPwd' action='?mnux=$_SESSION[mnux]' method=POST onSubmit="$onSubmit">
  <input type=hidden name='gos' value='SimpanPwd' />
  
  <tr><td class=inp width=80>No. PMB:</td>
      <td class=ul width=80>$_PMBID</td>
  </tr>
  <tr>
      <td class=inp width=80>Nama PMB:</td>
      <td class=ul><b>$pmb[Nama]</b>&nbsp;</td>
      </tr>
  <tr><td class=inp valign=top>Password Baru:</td>
      <td class=ul valign=top>
        <input type=password name='PasswordBaru1' size=20 maxlength=16 />
      </td>
  </tr>
  </tr>
      <td class=inp valign=top>Password Baru:</td>
      <td class=ul valign=top>
        <input type=password name='PasswordBaru2' size=20 maxlength=16/><br />
        *) tuliskan password baru sekali lagi
      </td>
  </tr>
  $Hint
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan Password Baru' />
      </td>
  </tr>
  
  </form>
  </table>
  $CheckScript
ESD;
}
function SimpanPwd($_MhswID) {
  $PMBID = sqling($_REQUEST['PMBID']);
  $Hint = $_REQUEST['Hint'];
  $HintAnswer = $_REQUEST['HintAnswer'];
  $PasswordBaru1 = sqling($_REQUEST['PasswordBaru1']);
  $PasswordBaru2 = sqling($_REQUEST['PasswordBaru2']);
  
  if(GetaField('pmb', "PMBID='$PMBID' and KodeID", KodeID, 'PasswordBaru') == 'Y')
  {	  $s = "update pmb 
			set Password=LEFT(PASSWORD('$PasswordBaru1'), 10), PasswordBaru='N',
				Hint='$Hint', HintAnswer='$HintAnswer'
			where KodeID = '".KodeID."'
			  and PMBID = '$PMBID' ";
	  $r = _query($s);	  
	  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1000); 
  }
  else
  {
	  if(GetaField('pmb', "PMBID='$PMBID' and KodeID", KodeID, 'HintAnswer') == $HintAnswer)
	  {
		  $s = "update pmb
			set Password=LEFT(PASSWORD('$PasswordBaru1'), 10), PasswordBaru='N' 
			where KodeID = '".KodeID."'
			  and PMBID = '$PMBID' ";
		  $r = _query($s);
		  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1000); 
	  }
	  else
	  {	  echo ErrorMsg("Gagal", 
				"Jawaban dari pertanyaan sekuritas tidak sesuai. <br>
				<br>
				<a href='#' onClick=\"location='?mnux=$_SESSION[mnux]'\" >Coba Lagi</a> &bull <a href='#' onClick=\"location=''\">Logout</a> ");
	  }
  }
}
?>
