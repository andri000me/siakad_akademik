<?php
// Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com
// Start Date; 17 Maret 2009

session_start();
// *** Parameters ***

// *** Main ***

$gos = (empty($_REQUEST['gos']))? 'DaftarGagal' : $_REQUEST['gos'];
$gos();

// *** Functions ***

function DaftarGagal()
{	include_once "../sisfokampus1.php";
	HeaderSisfoKampus("Laporan Daftar Gagal Remedial Mahasiswa");
	TampilkanJudul("Laporan Daftar Gagal Remedial Mahasiswa");

	$tahunstring =(empty($_SESSION['_remedialTahunID']))? "" : "and r.TahunID='$_SESSION[_remedialTahunID]'";
	$prodistring =(empty($_SESSION['_remedialProdiID']))? "" : "and mk.ProdiID='$_SESSION[_remedialProdiID]'";
	
	$s = "select r.MhswID, r.TahunID, m.Nama, jd.MKKode, jd.Nama as _MKNama, jd.SKS, r.GradeNilai, r.BobotNilai
			from krsremedial r left outer join mhsw m on r.MhswID=m.MhswID and r.KodeID=m.KodeID
							left outer join jadwalremedial jd on r.JadwalRemedialID=jd.JadwalRemedialID and r.KodeID=jd.KodeID
							left outer join mk on mk.MKID=jd.MKID and mk.KodeID=r.KodeID
			where r.Final='Y' and (r.GradeNilai = 'D' or r.GradeNilai = 'E') $tahunstring $prodistring and r.KodeID='".KodeID."' 
			order by m.MhswID";
	$r = _query($s);
	$n = _num_rows($r);
	
	echo "<table class=box cellspacing=1 align=center width=700>
			<form name='rem_form' action='?' method=POST onSubmit=\"\">
				<input type=hidden name='gos' value='CetakLaporanRemedialMahasiswa' \>
				<input type=hidden name='TahunID' value='$_SESSION[_remedialTahunID]' \>
				<input type=hidden name='ProdiID' value='$_SESSION[_remedialProdiID]' \>
				
				<tr><td colspan=10 align=center>
									<input type=button name='Batal' value='Tutup' onClick=\"self.close()\" ></td></tr>
				<tr><th class=ttl width=20>#</th>
					<th class=ttl width=80>NIM</th>
					<th class=ttl width=200>Nama</th>
					<th class=ttl width=>Mata Kuliah</th>
					<th class=ttl width=40>SKS</th>
					<th class=ttl width=40>Grade</th>
					<th class=ttl width=40>Bobot</th></tr>";
	$count = 0;
	$curID ='1knckosjkdfo2skdkj';
	while($w = _fetch_array($r))
	{	
		$MhswID = ''; $Nama = ''; $counting = ''; $gantibaris = '';
		if($curID != $w['MhswID'])
		{	$MhswID =  $w['MhswID'];
			$Nama = $w['Nama'];
			$curID = $MhswID;
			$count++;
			$counting = $count.".";
			$gantibaris = ($count != 1)? '<tr><td colspan=10><hr color=green size=1></hr></td></tr>' : '';
		}
		echo "	$gantibaris
				<tr><td class=ul1 align=center>$counting</td>
					<td class=ul1>$MhswID</td>
					<td class=ul1>$Nama</td>
					<td class=ul1>$w[_MKNama]<sup>$w[MKKode]</sup></td>
					<td class=ul1 align=center>$w[SKS]</td>
					<td class=ul1>$w[GradeNilai]</td>
					<td class=ul1>$w[BobotNilai]</td>
					";
	}
				
		echo "	</form>
		</table></p>";
	
}