<?php

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("OPK Siswa");

TampilkanJudul("Pembagian Kelas Mahasiswa");
$gos = (empty($_REQUEST['gos']))? "EditKelas" : $_REQUEST['gos'];
$gos();
	
	function EditKelas()
	{	
		$md = $_REQUEST['md']+0;
		$kid = $_REQUEST['kid'];
		if($md == 0) 
		{	$jdl = 'Edit Kelas';
			$w = GetFields('kelas', 'KelasID', $kid, '*');
			$strkid = "
			<tr>
				<td class=inp>Kelas ID: </td>
				<td class=ul1><input type=text name='dkid' value='$kid' size=2 disabled>
							<input type=hidden name='kid' value='$kid'></td>				
			</tr>";
		}
		else if($md == 1)
		{	$jdl = 'Tambah Kelas';
			$w = array();
			$strkid = "";
		}
		else die(ErrorMsg('Error', "Mode edit tidak dikenali. Hubungi Sysadmin untuk informasi lebih lanjut." ));
		
		TampilkanJudul($jdl);
		echo "<br>";
		$optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['ProdiID'], "KodeID='".KodeID."'", 'ProdiID');
		$optprogram = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $w['ProgramID'], "KodeID='".KodeID."'", 'ProgramID'); 
		$opttahun = GetOption2('tahun', "concat(TahunID, ' (', ProdiID, if(ProdiID='','','-'), ProgramID, ')')", 'TahunID', $w['TahunID'], "NA='N'", 'TahunID');
		CheckFormScript("InputNama,InputTahun,InputProdi,InputProgram,InputKapMax");
		echo "<table class=box cellspacing=1 align=center>
				<form action='?' method=POST onSubmit=\"return CheckForm(this)\">
					<input type=hidden name='mnux' value='$_SESSION[mnux]' />
					<input type=hidden name='gos' value='SavKelas' />
					<input type=hidden name='md' value='$md'/>
					<input type=hidden name='kid' value='$kid' />
					
					<tr>
						<td class=inp>Nama Kelas:</td>
						<td class=ul1><input type=text name='InputNama' value='$w[Nama]'></td>
					</tr>
					<tr>
						<td class=inp>Tahun Akademik:</td>
						<td class=ul1><input type=text name='InputTahun' value='$_SESSION[opk_tahun]' size=3 maxlength=10></td>
					</tr>
					<tr>
						<td class=inp>Program Studi:</td>
						<td class=ul1><select name='InputProdi'>$optprodi</select></td>
					</tr>
					<tr>
						<td class=inp>Program:</td>
						<td class=ul1><select name='InputProgram'>$optprogram</select></td>
					</tr>
					<tr>
						<td class=inp>Kapasitas Maksimum Kelas: </td>
						<td class=ul1><input type=text name='InputKapMax' value='$w[KapasitasMaksimum]' size=2 maxlength=4></td>
					</tr>
					<tr>
						<td class=ul1 colspan=2 align=center>
							<input type=submit name='SimpanKelas' value='Simpan Kelas' />
							<input type=reset name='Reset' value='Reset' />
							<input type=button name='Batal' value='Batal'
								onClick=\"self.close();\" /></td>
					</tr>
				</table>
			</form>";
	}
	
	function SavKelas()
	{	$md = $_REQUEST['md']+0;
		$kid = $_REQUEST['kid']+0;
		$InputNama = sqling($_REQUEST['InputNama']);
		$InputTahun = $_REQUEST['InputTahun'];
		$InputProdi = $_REQUEST['InputProdi'];
		$InputProgram = $_REQUEST['InputProgram'];
		$KapasitasMax = $_REQUEST['InputKapMax']+0;
		
		
		if($md == 0)
		{	
			$sj = "update `kelas` set  
					 Nama='$InputNama', 
					 TahunID = '$InputTahun',
					 ProdiID = '$InputProdi', 
					 ProgramID = '$InputProgram',
					 KapasitasMaksimum = '$KapasitasMax',
					 LoginEdit = '$_SESSION[_Login]',
					 TanggalEdit = now() 
					where KelasID = '$kid'";
			$rj = _query($sj);
			
			echo Konfirmasi("Berhasil", "Data berhasil disimpan");
			ClosingScript0();
		}
		else if($md == 1)
		{	
			$si = "insert into `kelas` (Nama, TahunID, ProdiID, ProgramID, KodeID, KapasitasSekarang, KapasitasMaksimum, LoginBuat, TanggalBuat, NA )
				values ( '$InputNama', '$InputTahun', '$InputProdi', '$InputProgram', '".KodeID."', '0', '$KapasitasMax', '$_SESSION[_Login]', now(), 'N')";
			$ri = _query($si);
			echo Konfirmasi("Berhasil", "Data berhasil disimpan");
			ClosingScript1();
		}
	}
	
	function ClosingScript0()
	{	echo "<SCRIPT>
					opener.location='../$_SESSION[mnux].list.php';
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
