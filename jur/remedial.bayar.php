<?php
// Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com
// Start Date; 17 Maret 2009

session_start();
// *** Parameters ***

// *** Main ***
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Proses Biaya Remedial");
TampilkanJudul("Proses Biaya Remedial");

$gos = (empty($_REQUEST['gos']))? 'TampilkanBayarRemedial' : $_REQUEST['gos'];
$gos();

// *** Functions ***

function TampilkanBayarRemedial()
{	$prodiopt = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['_remedialProdiID'], "KodeID='".KodeID."'", 'ProdiID');
	echo "<table class=box cellspacing=1 align=center width=400>
			<form name='rem_form' action='?' method=POST onSubmit=\"\">
				<input type=hidden name='gos' value='ProsesBiayaRemedial' \>
				<input type=hidden name='TahunID' value='$_SESSION[_remedialTahunID]' \>
				<input type=hidden name='ProdiID' value='$_SESSION[_remedialProdiID]' \>
				
				<tr><td class=inp>Thn Akademik:</td>
					<td class=ul1><input type=text name='_remedialTahunID' value='$_SESSION[_remedialTahunID]' size=4 maxlength=5 /></td></tr>
			    <tr><td class=inp>Program Studi:</td>
					<td class=ul1><select name='_remedialProdiID'>$prodiopt</select\"></td></tr>
				<tr><td colspan=2 align=center><input type=submit name='Proses' value='Proses'>
										<input type=button name='Cetak' value='Cetak Kartu Remedial' onClick=\"\">
										<input type=button name='Batal' value='Batal' onClick=\"self.close()\"</td></tr>
			</form>
		</table>";	
}

function ProsesBiayaRemedial()
{	/*echo Konfirmasi("Konfirmasi Finalisasi Pembayaran Remedial",
    "<p>Benar Anda akan memfinalisasi jadwal remedial tahun ini?<br />
    Setelah difinalisasi, mata kuliah remedial sudah tidak dapat diubah jadwal dan pesertanya.</p>
    
    <p>Cek sekali lagi. Anda tidak dapat mengubah jadwal lagi setelah ini
    Baru setelah itu mata kuliah dapat difinalisasi.</p>

    <hr size=1 color=silver />
    Opsi: <input type=button name='Finalisasi' value='Finalisasi Jadwal Remedial'
      onClick=\"location='../$_SESSION[mnux].bayar.php?gos=Finalisasi'\" />
      <input type=button name='Batal' value='Batalkan' onClick=\"window.close()\" />");*/
}
?>