<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Profile Wisudawan", 1);

// *** infrastruktur **
echo <<<SCR
  <script src="../$_SESSION[mnux].profile.script.js"></script>
SCR;

// *** Parameters ***
$MhswID = sqling($_REQUEST['MhswID']);
$mhsw = GetFields('mhsw m left outer join wisudawan w on m.MhswID=w.MhswID', "m.KodeID='".KodeID."' and m.MhswID", $MhswID, 'm.*,w.TanggalLahirFinal,w.NomerIjazah,w.NomerTranskrip,w.NomerSeri,w.PIN');
if (empty($mhsw)) 
  die(ErrorMsg('Error',
    "Mahasiswa dengan NIM: <b>$MhswID</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Profile' : $_REQUEST['gos'];
$gos($MhswID, $mhsw);

// *** Functions ***
function Profile($MhswID, $mhsw) {
  TampilkanJudul("Edit Profile Wisudawan");
  $tgllahir = GetDateOption($mhsw['TanggalLahir'], 'TGL');
  $optagm = GetOption2('agama', "concat(Agama, ' - ', Nama)", 'Agama', $mhsw['Agama'], '', 'Agama');
  $NamaDosen = (empty($mhsw['PenasehatAkademik']))? '' : GetaField('dosen', "KodeID='".KodeID."' and Login", $mhsw['PenasehatAkademik'], 'Nama');
  CheckFormScript('MhswID,Agama,TempatLahir,DosenID,Kota,NamaAyah');
  $TanggalLahir = TanggalFormat($mhsw[TanggalLahir]);
  $pknya = GetaField('program_kekhususan', "pk_status=2 and MhswID", $MhswID,"pk_idpilihan");
  $optpk = GetOption2('program_kekhususan', "pk_namapilihan", 'pk_idpilihan', $pknya, 'pk_status=2', 'pk_idpilihan','1',1,'pk_idpilihan');
	$tahunpk = GetNumberOption(2006,date('Y'));

  $optkonsentrasi = GetOption2('konsentrasi', "Nama", 'KonsentrasiID', $mhsw['KonsentrasiID'], "ProdiID='$mhsw[ProdiID]'", 'KonsentrasiID');
	$tahunpk = GetNumberOption(2006,date('Y'));
  $PK =  ($mhsw['ProdiID']=='IH') ? "<tr><td class=inp>Program Kekhususan:</td>
      <td class=ul>
      <select name='PK'>$optpk</select> Tahun: <select name='tahunpk'><option value=''></option>$tahunpk</select>
      </td></tr>":"";
  echo <<<ESD
  <table class=bsc cellspacing=1 width=100%>
  
  <form name='frmp' action='../$_SESSION[mnux].profile.php' method=POST 
    onSubmit="return CheckForm(this)">
  <input type=hidden name='MhswID' value='$MhswID' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td class=inp>NIM:</td>
      <td class=ul><b>$MhswID</b></td>
      </tr>
  <tr><td class=inp>Nama Mahasiswa:</td>
      <td class=ul><input type=text name='Nama' value='$mhsw[Nama]'
        size=40 maxlength=250 /></td>
      </tr>
  <tr><td class=inp>Tanggal Lahir:</td>
      <td class=ul>$TanggalLahir<br /><input type=text name='TanggalLahirFinal' value='$mhsw[TanggalLahirFinal]'
        size=20 maxlength=255 /><sup>*) Isikan jika format di Ijazah berbeda</sup></td>
      </tr>
  <tr><td class=inp>Tempat Lahir:</td>
      <td class=ul>
      <input type=text name='TempatLahir' value='$mhsw[TempatLahir]'
        size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Agama:</td>
      <td class=ul><select name='Agama'>$optagm</select></td>
      </tr>
  <tr><td class=inp>Alamat:</td>
      <td class=ul>
      <input type=text name='Alamat' value='$mhsw[Alamat]'
        size=40 maxlength=100 />
      </td></tr>
  <tr><td class=inp>Kota/Kabupaten:</td>
      <td class=ul>
      <input type=text name='Kota' value='$mhsw[Kota]'
        size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Nama Ayah:</td>
      <td class=ul>
      <input type=text name='NamaAyah' value='$mhsw[NamaAyah]'
        size=40 maxlength=50 />
      </td></tr>
		$PK
   <tr><td class=inp>Konsentrasi (Pascasarjana):</td>
      <td class=ul>
      <select name='KonsentrasiID'>$optkonsentrasi</select>
      </td></tr>
   <tr><td class=inp>No. Transkrip:</td>
      <td class=ul>
      <input type=text name='NomerTranskrip' value='$mhsw[NomerTranskrip]'
        size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>NIRL:</td>
      <td class=ul>
      <input type=text name='NomerSeri' value='$mhsw[NomerSeri]'
        size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>PIN:</td>
      <td class=ul>
      <input type=text name='PIN' value='$mhsw[PIN]'
        size=40 maxlength=50 />
      </td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  </form>
  </table>
  
  <div class='box0' id='caridosen'></div>
  
  <script>
  <!--
  function toggleBox(szDivID, iState) // 1 visible, 0 hidden
  {
    if(document.layers)	   //NN4+
    {
       document.layers[szDivID].visibility = iState ? "show" : "hide";
    }
    else if(document.getElementById)	  //gecko(NN6) + IE 5+
    {
        var obj = document.getElementById(szDivID);
        obj.style.visibility = iState ? "visible" : "hidden";
    }
    else if(document.all)	// IE 4
    {
        document.all[szDivID].style.visibility = iState ? "visible" : "hidden";
    }
  }
  function CariDosen(ProdiID, frm) {
    //alert(document.getElementByName(frm));
    if (eval(frm + ".Dosen.value != ''")) {
      eval(frm + ".Dosen.focus()");
      showDosen(ProdiID, frm, eval(frm +".Dosen.value"), 'caridosen');
      toggleBox('caridosen', 1);
    }
  }
  //-->
  </script>
ESD;
}
function Simpan($MhswID, $mhsw) {
  $TanggalLahirFinal = "$_REQUEST[TanggalLahirFinal]";
  $TempatLahir = sqling($_REQUEST['TempatLahir']);
  $Nama = $_REQUEST['Nama'];
  $Agama = sqling($_REQUEST['Agama']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $NamaAyah = sqling($_REQUEST['NamaAyah']);
  $NomerTranskrip = sqling($_REQUEST['NomerTranskrip']);
  $NomerIjazah = sqling($_REQUEST['NomerIjazah']);
  $NomerSeri = sqling($_REQUEST['NomerSeri']);
  $PIN = sqling($_REQUEST['PIN']);
  $KonsentrasiID = sqling($_REQUEST['KonsentrasiID']);
  if (isset($_POST['PK']) and !empty($_POST['PK'])) {
  	$PK = $_POST['PK'];
  	$namapk = GetaField('program_kekhususan', 'pk_idpilihan', $PK, "pk_namapilihan");
    $sql = _query("delete from program_kekhususan where MhswID='$MhswID' ");
  	$sql = _query("insert into program_kekhususan (MhswID,pk_idpilihan,pk_namapilihan,pk_jurusan,pk_semester,pk_tahun,pk_status)
    				values ('$MhswID','$PK', '$namapk', '1', '1', '$_POST[tahunpk]', '2') ");
  }
  
  // Simpan
  $s = 'update mhsw
    set TempatLahir = "'.$TempatLahir.'",
    	Nama = "'.$Nama.'",
        Agama = "'.$Agama.'",
        Alamat = "'.$Alamat.'",
        Kota = "'.$Kota.'",
        NamaAyah = "'.$NamaAyah.'",
        KonsentrasiID = "'.$KonsentrasiID.'"
    where KodeID = "'.KodeID.'"
      and MhswID = "'.$MhswID.'"';
  $r = _query($s);
  //echo $s;
  $s = "update wisudawan
    set TanggalLahirFinal = '$TanggalLahirFinal',
    	NomerIjazah	= '$NomerIjazah',
        NomerTranskrip	= '$NomerTranskrip',
        PIN  = '$PIN',
        NomerSeri	= '$NomerSeri'
    where MhswID = '$MhswID' ";
  $r = _query($s);
  TutupScript();
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&_tabWisuda=$_SESSION[_tabWisuda]&gos=Daftar';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>

</BODY>
</HTML>
