<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit Anggota");

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']; // Jika edit, maka gunakan id ini
$bck = $_REQUEST['bck'];

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);

// *** Functions ***
function Edit($md, $id, $bck) {
  if ($md == 0) {
    $jdl = "Edit Anggota";
    $w = GetFields('pustaka_anggota', 'AnggotaID', $id, '*');
    $ro = "readonly=true disabled=true";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Anggota";
    $w = array();
	$agt=GetaField('pustaka_anggota', "AnggotaID !", '', "MAX(ID)") +1;
	$_agt = str_pad((int)$agt,7,"0",STR_PAD_LEFT);
	$w['AnggotaID']=KodeID."$_agt";
    $ro = '';
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih detail.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

  echo "<p><table class=box cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].anggota.edit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>ID Anggota:</td>
      <td class=ul1><input type=text name='AnggotaID' value='$w[AnggotaID]' size=30 $ro /></td>
      </tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]' size=30  /></td>
      </tr>
  <tr><td class=inp>Handphone:</td>
      <td class=ul1><input type=text name='Handphone' value='$w[Handphone]' size=30  /></td>
      </tr> 
  <tr><td class=inp>Email:</td>
      <td class=ul1><input type=text name='Email' value='$w[Email]' size=30  /></td>
      </tr>
  <tr><td class=inp>Alamat:</td>
      <td class=ul1><input type=text name='Alamat' value='$w[Alamat]' size=50  /></td>
      </tr>
  <tr><td class=inp>Jenis Anggota:</td>
      <td class=ul1><select name='InstitusiID'><option value=''></option>
	  		<option value='DOS'>Dosen</option>
			<option value='KAR'>Karyawan</option>
			<option value='MHL'>Mahasiswa Luar Biasa</option>
			<option value='UMM'>Umum</option></select>
			</td>
      </tr>
  <tr><td class=inp>Nama Institusi:<br /><sub>*) bila bukan mahasiswa internal</sub></td>
      <td class=ul1><input type=text name='NamaInstitusi' value='$w[NamaInstitusi]' size=30  /></td>
      </tr>
  <tr><td class=inp>NA:</td>
      <td class=ul1><input type='radio' name='NA' value='Y' ".($w['NA']=='Y' ? "checked":"")." /> Ya
	  				<input type='radio' name='NA' value='N' ".($w['NA']=='N' ? "checked":"")." /> Tidak</td>
      </tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td></tr>
  
  </form>
  </table></p>";
}
function Simpan($md, $id, $bck) {
  $AnggotaID = $_REQUEST['AnggotaID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Handphone = sqling($_REQUEST['Handhone']);
  $Email = sqling($_REQUEST['Email']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $NamaInstitusi = sqling($_REQUEST['NamaInstitusi']);
  $NA = sqling($_REQUEST['NA']);
  // Simpan
  if ($md == 0) {
    $s = "update pustaka_anggota
      set Nama = '$Nama', Handphone = '$Handphone', Email='$Email', Alamat = '$Alamat', InstitusiID='$_REQUEST[InstitusiID]', NamaInstitusi = '$NamaInstitusi',
          NA  = '$NA'
      where AnggotaID = '$id' ";
    $r = _query($s);

    $type = ($_REQUEST['InstitusiID']=='MHL' || $_REQUEST['InstitusiID']=='UMM') ? "3":"2";
    $s1 = "UPDATE app_pustaka1.member set member_name = '$Nama',member_type_id='$type',inst_name='$NamaInstitusi' 
			where member_id='$AnggotaID'";
	$r1 = _query($s1);

    TutupScript($bck);
  }
  elseif ($md == 1) {
	  $cekID = GetaField("pustaka_anggota", "AnggotaID", $AnggotaID, "Nama");
	  if (!empty($cekID)) { 
		  die(ErrorMsg('Error',
		"Anggota dengan ID $AnggotaID sudah ada, nama anggota yang sudah tersimpan $cekID.
		<hr size=1 color=silver />
		Opsi: <a href=# onclick='window.history.back()'>Kembali</a> | <input type=button name='Tutup' value='Tutup'
		  onClick=\"window.close()\" />"));
	  }
    $s = "insert into pustaka_anggota
      (AnggotaID, Nama, Handphone, Email, Alamat, NamaInstitusi,InstitusiID, NA)
      values
      ('$AnggotaID','$Nama', '$Handphone','$Email','$Alamat', '$NamaInstitusi','$_REQUEST[InstitusiID]','$NA')";
    $r = _query($s);

    $type = ($_REQUEST['InstitusiID']=='MHL' || $_REQUEST['InstitusiID']=='UMM') ? "3":"2";
    $expire = date('Y-m-d', strtotime('+4 months'));
    $s1 = "INSERT INTO app_pustaka1.member (member_id, member_name,member_type_id,member_since_date,register_date,expire_date,mpasswd,input_date,is_pending, inst_name) 
			values ('$AnggotaID','$Nama','$type',now(),now(),'$expire',md5('ubh'),now(),'0', '$NamaInstitusi')
			on duplicate key UPDATE is_pending='$pending', expire_date='$expire', mpasswd=md5('ubh')";
	$r1 = _query($s1);
	
    TutupScript($bck);
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  
}
function TutupScript($BCK) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../?mnux=$BCK&gos=anggota';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
