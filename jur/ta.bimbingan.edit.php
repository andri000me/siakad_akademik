<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Daftar Bimbingan Skripsi/TA", 1);

if ($_SESSION['_LevelID']==100) {
	$cek = GetFields('ta',"TAID", $_REQUEST['TAID'],"Pembimbing,Pembimbing2");
	if ($cek['Pembimbing']!=$_SESSION['_Login'] && $cek['Pembimbing2']!=$_SESSION['_Login']) {
		die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi SysAdmin untuk informasi lebih lanjut."));
	}
}

// *** Parameters ***
// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'TampilkanFormBimbingan' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function TampilkanFormBimbingan() {
  if ($_REQUEST[md] == 0) {
    $jdl = "Edit Kegiatan Bimbingan Skripsi/TA";
    $w = GetFields('tabimbingan', "BimbinganID = '".$_REQUEST[id]."' and TAID", $_REQUEST[TAID], 
		"TglBimbingan, date_format(TglBimbingan,'%d-%M-%Y') as _TglBimbingan, Catatan, BimbinganID");
	$optTgl = getDateOption($w[TglBimbingan], 'TglBimbingan');
  }
  elseif ($_REQUEST[md] == 1) {
    $jdl = "Input Kegiatan Bimbingan Skripsi/TA";
	$optTgl = getDateOption(date('Y-m-d'), 'TglBimbingan');
  }
  else die(ErrorMsg('Error',
    "Terjadi kesalahan.<br />
    Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
	
	TampilkanJudul($jdl);
	$TglMulai = GetaField('ta',"TAID",$_REQUEST['TAID'],"TglMulai");
	$TglSelesai = GetaField('ta',"TAID",$_REQUEST['TAID'],"TglSelesai");
	$_TglMulai = GetaField('ta',"TAID",$_REQUEST['TAID'],"date_format(TglMulai,'%d %M %Y')");
	$_TglSelesai = GetaField('ta',"TAID",$_REQUEST['TAID'],"date_format(TglSelesai,'%d %M %Y')");
	
	$pb = GetFields('ta',"TAID",$_REQUEST['TAID'],"Pembimbing,Pembimbing2");
	$dosen1 = GetaField('dosen',"Login",$pb['Pembimbing'],"Nama");
	$dosen2 = GetaField('dosen',"Login",$pb['Pembimbing2'],"Nama");
	
echo <<<SCR
<form name='data' action='../$_SESSION[mnux].bimbingan.edit.php' method=POST onsubmit='return cekForm()'>
  <table class=bsc cellspacing=1 width=100%>
	<input type=hidden name='md' value='$_REQUEST[md]' />
	<input type=hidden name='sub' value='Simpan' />
	<input type=hidden name='TAID' value='$_REQUEST[TAID]' />
	<input type=hidden name='id' value='$_REQUEST[id]' />
	<input type=hidden id='TglMulai' name='TglMulai' value='$TglMulai' />
	<input type=hidden id='TglSelesai' name='TglSelesai' value='$TglSelesai' />
	<tr>
		<td class=inp width=150>Batas Waktu Bimbingan : </td>
		<td><sup>$_TglMulai s/d $_TglSelesai</sup></td>
	</tr>
	<tr>
		<td class=inp width=150>Tanggal Bimbingan : </td>
		<td>$optTgl</td>
	</tr>
	<tr>
		<td class=inp width=150>Dosen Pembimbing : </td>
		<td><select name=Tipe><option value=1>1. $dosen1</option><option value=2>2. $dosen2</option></select></td>
	</tr>
	<tr>
		<td class=inp width=150>Catatan : </td>
		<td><textarea id=Catatan name=Catatan cols=30 rows=4 >$w[Catatan]</textarea></td>
	</tr>
	<tr>
		<td colspan=2 align=center><input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' /></td>
	</tr>      
  </table>
</form>

<script>
function cekForm(){
	var errMsg = '';
	TglBimbingan = data.TglBimbingan_y.value+'-'+data.TglBimbingan_m.value+'-'+data.TglBimbingan_d.value;
	TglMulai = document.getElementById('TglMulai').value;
	TglSelesai = document.getElementById('TglSelesai').value;
	
	if (TglBimbingan < TglMulai || TglBimbingan > TglSelesai){
		errMsg += "Tanggal bimbingan berada diluar batas waktu yg ditentukan"+String.fromCharCode(10)+String.fromCharCode(10);
	}
	
	if (document.getElementById('Catatan').value == ''){
		errMsg += 'Catatan harus diisi';
	}
	if (errMsg != ''){
		alert(errMsg);
		return false;
	} else {
		return true;
	}	
}

</script>
SCR;
}

function Simpan(){
	
	$TglBimbingan = "$_REQUEST[TglBimbingan_y]-$_REQUEST[TglBimbingan_m]-$_REQUEST[TglBimbingan_d]";
	
	if ($_REQUEST[md] == 0){
		$s = "update tabimbingan set TglBimbingan = '".$TglBimbingan."', Catatan = '".$_REQUEST[Catatan]."', Tipe= '".$_REQUEST[Tipe]."'
				where BimbinganID = '".$_REQUEST[id]."' and TAID = '".$_REQUEST[TAID]."'";
		$q = _query($s);
	}
	if ($_REQUEST[md] == 1){
		$s = "insert into tabimbingan (TAID,TglBimbingan,Catatan, Tipe, LoginBuat,TanggalBuat)
				values('".$_REQUEST[TAID]."','".$TglBimbingan."','".$_REQUEST[Catatan]."','".$_REQUEST[Tipe]."','".$_SESSION[_Login]."',now())";
		$q = _query($s);
	}
  echo <<<ESD
  <script>
  opener.location = "../$_SESSION[mnux].bimbingan.php?TAID=$_REQUEST[TAID]&ref=1";
  self.close();
  </script>
ESD;
}
