<?php
//Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Cari Nama Parsial");

TampilkanJudul("PMB Form Jual Mahasiswa - Gelombang $_REQUEST[gel]");

$temp_nama = $_REQUEST['n'];
$gos = (empty($_REQUEST['gos']))? "CariNama" : $_REQUEST['gos'];
$gos();

// *** Functions ***

function CariNama()
{	TampilkanJudul("List Nama yang Cocok");
		
	$s = "select * from `aplikan` where AplikanID LIKE '%$_REQUEST[n]%' or Nama LIKE '%$_REQUEST[n]%' ";
	//echo "Select: $s";
	$r = _query($s);
	$n = _num_rows($r);
	
	echo "<SCRIPT>
			function kembalikan(aplikanid, formulirid, programid, namalengkap, kelamin, tempatlahir, tanggallahir, alamat, kota, 
								propinsi, kodepos, rt, rw, telepon, handphone, email, asalsekolah, namasekolah,
								tahunlulus, jurusansekolah, nilaisekolah, pilihan1, pilihan2, pilihan3, jumlahpilihan){
			creator.frmisi.AplikanID.value = aplikanid;
			creator.frmisi.PMBFormulirID.value = formulirid;
			creator.frmisi.ProgramID.value = programid;
			creator.frmisi.Nama.value = namalengkap;
			creator.frmisi.Kelamin.value = kelamin; 
			creator.frmisi.TempatLahir.value = tempatlahir;
			creator.frmisi.TanggalLahir_y.value = tanggallahir.substring(0, 4);
			creator.frmisi.TanggalLahir_m.value = tanggallahir.substring(5, 7);
			creator.frmisi.TanggalLahir_d.value = tanggallahir.substring(8, 10);
			creator.frmisi.Alamat.value = alamat;
			creator.frmisi.Propinsi.value = propinsi;
			creator.frmisi.Kota.value = kota;
			creator.frmisi.KodePos.value = kodepos;
			creator.frmisi.RT.value = rt;
			creator.frmisi.RW.value = rw;
			creator.frmisi.Telepon.value = telepon;
			creator.frmisi.Handphone.value = handphone;
			creator.frmisi.Email.value = email;
			creator.frmisi.AsalSekolah.value = asalsekolah;
			creator.frmisi.SavAsalSekolah.value = asalsekolah;
			creator.frmisi.NamaSekolah.value = namasekolah;
			creator.frmisi.TahunLulus.value = tahunlulus;
			creator.frmisi.JurusanSekolah.value = jurusansekolah;
			creator.frmisi.NilaiSekolah.value = nilaisekolah;
			
			jmlpil = parseInt(jumlahpilihan);
			if(jmlpil <= 1)
			{	creator.frmisi.Pilihan1.value = pilihan1;
				creator.frmisi.Pilihan2.value = '';
				creator.frmisi.Pilihan2.disabled = true;
				creator.frmisi.Pilihan3.value = '';
				creator.frmisi.Pilihan3.disabled = true;
			}
			else if(jmlpil <=2)
			{	creator.frmisi.Pilihan1.value = pilihan1;
				creator.frmisi.Pilihan2.disabled = false;
				creator.frmisi.Pilihan2.value = pilihan2;
				creator.frmisi.Pilihan3.value = '';
				creator.frmisi.Pilihan3.disabled = true;
			}
			else
			{	creator.frmisi.Pilihan1.value = pilihan1;
				creator.frmisi.Pilihan2.value = pilihan2;
				creator.frmisi.Pilihan3.value = pilihan3;
				creator.frmisi.Pilihan2.disabled = false;
				creator.frmisi.Pilihan3.disabled = false;
			}
			
			window.close();
			}
		</SCRIPT>";
	
	echo "<p><table class=box cellspacing=1 align=center>
			<form action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]'/>
				<input type=hidden name='gos' value='SelesaiCari' />
				<input type=hidden name='id' value='$_REQUEST[id]' />
				<input type=hidden name='gel' value='$_REQUEST[gel]' />
				";
	echo "<tr>
			<td class=ul1 colspan=8><sub>Nomor/Nama yang dicari: $_REQUEST[n]</sub></td>
		  </tr>";
	
	echo "<tr>
			<th class=ttl>Pilih</th>
			<th class=ttl>No. Aplikan</th>
			<th class=ttl>Nama</th>
			<th class=ttl>Kelamin</th>
			<th class=ttl>Tempat Lahir</th>
			<th class=ttl>Tgl. Lahir</th>
			<th class=ttl>Alamat</th>
			<th class=ttl>Kota</th>
			<th class=ttl>Kode Pos</th>
		</tr>";
	while($w=_fetch_array($r))
	{	if($w['PMBFormulirID']=='' or !isset($w['PMBFormulirID']) or $w['PMBFormulirID']==NULL)
		{	echo "<tr bgcolor='#00FFFF'>
				<td class=ul1 align=center>^</td>
				";
		}
		else if($w['PMBID']=='' or !isset($w['PMBID']) )
		{	
			$arrayPilihan = explode(',', $w['ProdiID']);
			$arrayGabung = array();
			foreach($arrayPilihan as $pilih) {  if(!empty($pilih))  $arrayGabung[] = $pilih; } 
			
			$NamaSekolah = GetaField('asalsekolah', 'SekolahID', $w['AsalSekolah'], 'Nama');
			if(empty($NamaSekolah) or $NamaSekolah=='') { $NamaSekolah = GetaField('perguruantinggi', 'PerguruanTinggiID', $w['AsalSekolah'], 'Nama');	}
		    
			$JumlahPilihan = (empty($w['PMBFormulirID']))? 3 : GetaField('pmbformulir', "PMBFormulirID='$w[PMBFormulirID]' and KodeID", KodeID, 'JumlahPilihan');
			
			echo "<tr>
				<td class=ul1><input type=checkbox name='pilihan[]' value='$w[AplikanID]' 
					onChange='javascript:kembalikan(\"$w[AplikanID]\",
													\"$w[PMBFormulirID]\",
													\"$w[ProgramID]\",
													\"$w[Nama]\",
													\"$w[Kelamin]\",
													\"$w[TempatLahir]\",
													\"$w[TanggalLahir]\",
													\"$w[Alamat]\",
													\"$w[Kota]\",
													\"$w[Propinsi]\",
													\"$w[KodePos]\",
													\"$w[RT]\",
													\"$w[RW]\",
													\"$w[Telepon]\",
													\"$w[Handphone]\",
													\"$w[Email]\",
													\"$w[AsalSekolah]\",
													\"$NamaSekolah\",
													\"$w[TahunLulus]\",
													\"$w[JurusanSekolah]\",
													\"$w[NilaiSekolah]\",
													\"$arrayGabung[0]\",
													\"$arrayGabung[1]\",
													\"$arrayGabung[2]\",
													\"$JumlahPilihan\")' /></td>
				";
		}
		else
		{
			echo "<tr bgcolor='#D3D3D3'>
				<td class=ul1 align=center>&times</td>
				";
		}
		echo "	<td class=ul1>$w[AplikanID]</td>
				<td class=ul1>$w[Nama]</td>
				<td class=ul1>$w[Kelamin]</td>
				<td class=ul1>$w[TempatLahir]</td>
				<td class=ul1>$w[TanggalLahir]</td>
				<td class=ul1>$w[Alamat]</td>
				<td class=ul1>$w[Kota]</td>
				<td class=ul1>$w[KodePos]</td>
			</tr>";
		
	}
	
	echo "<tr>
				<td colspan=8 align=left><sub> &times menandakan bahwa aplikan ini sudah mendaftar</sub></td>
			</tr>
			<tr>
				<td colspan=8 align=left><sub> ^ menandakan bahwa aplikan ini belum membeli formulir</sub></td>
		  </tr>
		  <tr>
			<td class=ul1 colspan=8 align=center><input type=button name='Batal' value='Batal'
				onClick=\"window.close()\" /> 
			</td>
			</tr>";
	
	echo "</form>
		</table></p>";
}

?>