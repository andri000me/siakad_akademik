<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 27 Oktober 2008

session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";
  
// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$Logindsn = GetSetVar('Logindsn');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiTgl' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function KonfirmasiTgl() {
  	$Logindsn = $_SESSION['Logindsn'];
	$prodi = $_SESSION['ProdiID'];
	$whrProdi = empty($_SESSION['ProdiID']) ? '' : "Homebase='$_SESSION[ProdiID]'";
	
	$optstatus = GetOption2('dosen', "concat(Login, ' - ', Nama)", 'Login', $Logindsn, "$whrProdi", "Login");
	
	echo "<table class=box cellspacing=1 align=center border=1>
			<tr><th class=ttl colspan=2>Daftar Riwayat Hidup Dosen</th></tr>			
				<form action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden name='gos' value='' />
				<input type=hidden name='ProdiID' value='$_SESSION[ProdiID]' />
				
					<td class=inp>Cari Dosen: </td>
					<td class=ul1><select name='Logindsn' onChange='this.form.submit()'>$optstatus</select></td>
				</tr>
				<tr>
					<td class=inp colspan=2 align=center>
						<input type=button name='cetak' value='Cetak Laporan' onClick=\"CetakRiwayatDosen('$Logindsn','$prodi')\">
						<input type=button name='Tutup' value='Tutup' onClick='window.close()'>
					</td>
				</tr>
				</form>
			</table>
			
			<script>
					function CetakRiwayatDosen(Logindsn, prodi) {
					lnk = '../$_SESSION[mnux].rwythdpdsn.cetak.php?logindsn='+Logindsn+'&prodi='+prodi;
					//alert(lnk);
					win2 = window.open(lnk, '', 'width=800, height=600, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
				}
			</script><br>
			
			";
		
}

?>
