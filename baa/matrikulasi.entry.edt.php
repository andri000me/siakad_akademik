<?php
//Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Matrikulasi Siswa");

TampilkanJudul("OPK Mahasiswa");
$gos = (empty($_REQUEST['gos']))? "EditKelompok" : $_REQUEST['gos'];
$gos();
	
	function EditKelompok()
	{	
		$md = $_REQUEST['md']+0;
		$kid = $_REQUEST['kid'];
		if($md == 0) 
		{	$jdl = 'Edit Kelompok';
			$w = GetFields('matrikulasi', 'MatriID', $kid, '*');
			$strkid = "
			<tr>
				<td class=inp>Kelompok ID: </td>
				<td class=ul1><input type=text name='dkid' value='$kid' size=2 disabled>
							<input type=hidden name='kid' value='$kid'></td>				
			</tr>";
		}
		else if($md == 1)
		{	$jdl = 'Tambah Kelompok';
			$w = array();
			$strkid = "";
		}
		else die(ErrorMsg('Error', "Mode edit tidak dikenali. Hubungi Sysadmin untuk informasi lebih lanjut." ));
		
		TampilkanJudul($jdl);
		echo "<br>";
		CheckFormScript("InputNama,InputTahun,InputKapMax");
		echo "<table class=box cellspacing=1 align=center>
				<form action='?' method=POST onSubmit=\"return CheckForm(this)\">
					<input type=hidden name='mnux' value='$_SESSION[mnux]' />
					<input type=hidden name='gos' value='SavKelompok' />
					<input type=hidden name='md' value='$md'/>
					<input type=hidden name='kid' value='$kid' />
					
					<tr>
						<td class=inp>Nama Kelompok:</td>
						<td class=ul1><input type=text name='InputNama' value='$w[Nama]'></td>
					</tr>
					<tr>
						<td class=inp>Tahun Akademik:</td>
						<td class=ul1><input type=text name='InputTahun' value='$_SESSION[matri_tahun]' size=3 maxlength=10></td>
					</tr>
					<tr>
						<td class=inp>Kapasitas Maksimum Kelompok: </td>
						<td class=ul1><input type=text name='InputKapMax' value='$w[KapasitasMaksimum]' size=2 maxlength=4></td>
					</tr>
					<tr>
						<td class=ul1 colspan=2 align=center>
							<input type=submit name='SimpanKelompok' value='Simpan Kelompok' />
							<input type=reset name='Reset' value='Reset' />
							<input type=button name='Batal' value='Batal'
								onClick=\"self.close();\" /></td>
					</tr>
				</table>
			</form>";
	}
	
	function SavKelompok()
	{	$md = $_REQUEST['md']+0;
		$kid = $_REQUEST['kid']+0;
		$InputNama = sqling($_REQUEST['InputNama']);
		$InputTahun = $_REQUEST['InputTahun'];
		$KapasitasMax = $_REQUEST['InputKapMax']+0;
		
		
		if($md == 0)
		{	
			$sj = "update `matrikulasi` set  
									 Nama='$InputNama', 
									 TahunID = '$InputTahun',
									 KapasitasMaksimum = '$KapasitasMax',
									 LoginEdit = '$_SESSION[_Login]',
									 TanggalEdit = now()
						where MatriID = '$kid'";
			$rj = _query($sj);
			
			echo Konfirmasi("Berhasil", "Data berhasil disimpan");
			ClosingScript0();
		}
		else if($md == 1)
		{	
			$si = "insert into `matrikulasi` (Nama, TahunID, KodeID, KapasitasSekarang, KapasitasMaksimum, LoginBuat, TanggalBuat, NA )
				values ( '$InputNama', '$InputTahun', '".KodeID."', '0', '$KapasitasMax', '$_SESSION[_Login]', now(), 'N')";
			$ri = _query($si);
			echo Konfirmasi("Berhasil", "Data berhasil disimpan");
			ClosingScript1();
		}
	}
	
	function ClosingScript0()
	{	echo "<SCRIPT>
					opener.location='../$_SESSION[mnux].entry.list.php';
					self.close();
				</SCRIPT>";
	}
	function ClosingScript1()
	{	echo "<SCRIPT>
					opener.location='../index.php?mnux=$_SESSION[mnux]&gos=entry&sub=';
					self.close();
				</SCRIPT>";
	}
?>
