<?php
//Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Pengkelasan Otomatis");

TampilkanJudul("Pengkelasan Otomatis - OPK Mahasiswa");

$gos = (empty($_REQUEST['gos']))? "OPKOtomatis" : $_REQUEST['gos'];
$gos();

	function loadJavaScript()
	{	echo "<script language='JavaScript'>
				function CheckAll()
				{		
					total = (document.getElementById('TotalKelas')).value;
					var ttl = 0;
					for (i = 1; i <= total; i++)
					{	
						(document.getElementById('Kelas'+i)).checked = true;
						ttl = ttl + Number((document.getElementById('hKelas'+i)).value);
					}
					(document.getElementById('Capacity')).value = ttl;
				}
					
				function UnCheckAll()
				{
					total = (document.getElementById('TotalKelas')).value;
						
					for (i = 1; i <= total; i++)
					{
						(document.getElementById('Kelas'+i)).checked = false;
					}
					(document.getElementById('Capacity')).value = 0;
				}
				
				function AddValue(id)
				{	check = (document.getElementById('Kelas'+id)).checked;
					val = (document.getElementById('hKelas'+id)).value;
					var ttl = 0;
					ttl = Number((document.getElementById('Capacity')).value)
					if(check == true) ttl = ttl+Number(val);
					else ttl = ttl-Number(val);	
					(document.getElementById('Capacity')).value = ttl; 
				}	
			</script>";
	}

	function OPKOtomatis()
	{	loadJavaScript();
	
		$kelaminstring = (empty($_SESSION['kelamin_by']))? 'P, W': $_SESSION['kelamin_by'];
		$s = "select ProdiID from prodi where KodeID='".KodeID."' order by ProdiID";
		$r = _query($s);
		while($w = _fetch_array($r)) { $prodistring .= "$w[ProdiID], "; }
		$prodistring = (empty($_SESSION['prodi']))? substr($prodistring, 0, sizeof($prodistring)-3) : $_SESSION['prodi'];
		
		$s = "select ProgramID from program where KodeID='".KodeID."' order by ProgramID";
		$r = _query($s);
		while($w = _fetch_array($r)) { $programstring .= "$w[ProgramID], "; }
		$programstring = (empty($_SESSION['program']))? substr($programstring, 0, sizeof($programstring)-3) : $_SESSION['program'];
		
		if(!empty($_SESSION['prodi'])) $wherejurusan = "and ProdiID='$_SESSION[prodi]'";
		else $wherejurusan = '';
		
		if(!empty($_SESSION['program'])) $whereprogram = "and ProgramID='$_SESSION[program]'";
		else $whereprogram = '';
		
		if(!empty($_SESSION['opk_tahun_filter'])) $wheretahunakademik = "and TahunID='$_SESSION[opk_tahun_filter]'";
		else $wheretahunakademik = '';
		
		if(!empty($_SESSION['kelamin_by']))
		{	if($_SESSION['kelamin_by']=='W')
			{	$wherefilter .= "and Kelamin='W'"; }
			else
			{	$wherefilter .= "and Kelamin='P'"; }
		}
		
		$wherefilter .= "and NilaiUjian >= '$_SESSION[nilai_dari]' and NilaiUjian <= '$_SESSION[nilai_sampai]'";
		
		$s3="select MhswID from `mhsw` 
				where (KelasID='0' or KelasID='')
				$wheretahunakademik
				$wherejurusan
				$whereprogram
				$wherefilter";
		$r3 = _query($s3);
		$n3 = _num_rows($r3);
		
		echo "<table class=box cellspacing=1 align=center width=700>
					<tr><td class=ul1 colspan=4><b>Anda akan mengelompokkan <font color=red>$n3</font> mahasiswa yang memiliki kriteria:</b></td><tr>
					<tr><td width=50></td><td class=ul1 align=right width=100>Tahun Akademik: </td><td class=ul1 width=30>$_SESSION[opk_tahun_filter]</td><td width=400></td></tr> 
					<tr><td></td><td class=ul1 align=right>Kelamin: </td><td class=ul1>$kelaminstring</td></tr>
					<tr><td></td><td class=ul1 align=right>Program Studi: </td><td class=ul1>$prodistring</td></tr>
					<tr><td></td><td class=ul1 align=right>Program: </td><td class=ul1>$programstring</td></tr>
					<tr><td></td><td class=ul1 align=right>Nilai: </td><td class=ul1>$_SESSION[nilai_dari] - $_SESSION[nilai_sampai]</td></tr>
			  </table>
			  <table class=box cellspacing=1 align=center width=700>
			      <form name='data_kelas' action='?' method=POST>
					<input type=hidden name='mnux' value='$_SESSION[mnux]' />
					<input type=hidden name='gos' value='ApplyOtomatis' />
					<tr><td class=ul1 colspan=4><b>Ke dalam kelas-kelas di Tahun Akademik $_SESSION[opk_tahun] berikut:</b></td></tr>";
					
		$s = "select * from kelas where TahunID='$_SESSION[opk_tahun]' and KodeID='".KodeID."' order by Nama";
		$r = _query($s);
		$n = _num_rows($r);
		$rowlimit=3;
		$currentrow=-1;
		$counting = 1;
		echo "<tr>";
		while($w = _fetch_array($r))
		{	$currentrow++;
			if($currentrow==$rowlimit) { echo "</tr><tr>"; $currentrow=0; }
			$kapleft = $w['KapasitasMaksimum']-$w['KapasitasSekarang'];
			$fontgrey = ($kapleft == 0)? '<font color=grey>' : '';
			$fontendgrey =($kapleft == 0)? '</font>' : '';
			echo "<td class=ul1><input type=checkbox id='Kelas$counting' name='Pilihan[]' value='$w[KelasID]'
					onChange=\"javascript:AddValue($counting)\">$fontgrey $w[Nama] ($w[KapasitasSekarang]/$w[KapasitasMaksimum])$fontendgrey
					<input type=hidden id='hKelas$counting' value='$kapleft'>
					</td>";
			$counting++;
		}
					
		if(empty($_SESSION['kelamin_by'])) $kelaminotheroptions = "<td class=ul1><input type=radio name='CheckKelamin' value='HalfHalf'>50:50</td>";
		if(empty($_SESSION['prodi'])) $prodiotheroptions = "<td class=ul1><input type=radio name='CheckProdi' value='Evenly'>Sama Rata</td>";
		
		echo "		<input type=hidden id='TotalKelas' value='$n'>
					<input type=hidden name='JumlahMahasiswa' value='$n3'>
				</tr>
				<tr><td colspan=4 align=center><b>Total kapasitas:</b> <input type=text id='Capacity' name='Capacity' value='0' size=2 disabled> mahasiswa</td></tr>
				<tr><td colspan=4 align=center><input type=button name='CekSemua' value='Cek Semua' onClick=\"javascript:CheckAll()\" \>
												<input type=button name='ClearSemua' value='Clear Semua' onClick=\"javascript:UnCheckAll()\" \>
					</td></tr>
				
				<tr><td colspan=4>
					<table class=box align=center width=700>
					<tr><td class=ul1 colspan=4><font color=green><b>OPSI UNTUK PENGELOMPOKKAN SECARA OTOMATIS:</b></font></td></tr>
					<tr><td class=inp width=120>KELAMIN:</td>
						<td class=ul1 width=150><input type=radio name='CheckKelamin' value='Random' checked>Random</td>
						$kelaminotheroptions</tr>
					<tr><td class=inp>PRODI/PROGRAM:</td>
						<td class=ul1><input type=radio name='CheckProdi' value='Random' checked>Random</td>
						$prodiotheroptions</tr>
					<tr><td class=inp>NILAI:</td>
						<td class=ul1><input type=radio name='CheckNilai' value='Random' checked>Random</td>
						<td class=ul1><input type=radio name='CheckNilai' value='TopScore'>Nilai Tertinggi</td></tr>
					</table>
				</td></tr>
				
				<tr><td class=ul1 align=center colspan=4>
						<input type=submit name='Otomatis' value='Kelaskan Secara Otomatis' onClick=\"data_kelas.submit()\"/>
						<input type=reset name='Reset' value='Reset' />
						<input type=button name='Batal' value='Batal' onClick=\"self.close();\" />
				</td></tr>
					
				</form>
			</table>
					 ";	
	}

	function ApplyOtomatis()
	{	$Pilihan = $_REQUEST['Pilihan'];
		$JumlahMahasiswa = $_REQUEST['JumlahMahasiswa'];
		$CheckKelamin = $_REQUEST['CheckKelamin'];
		$CheckProdi = $_REQUEST['CheckProdi'];
		$CheckNilai = $_REQUEST['CheckNilai'];

if(!empty($Pilihan))
{		
		// Buat Array untuk Prodi
		$s = "select ProdiID from prodi where KodeID='".KodeID."'";
		$r = _query($s);
		while($w = _fetch_array($r)) $prodilist[] = $w[ProdiID];
		$JumlahProdi = _num_rows($r);
		
		// Buat Array untuk Kelamin
		$kelaminlist = array('P', 'W');
		
		$countMahasiswa = 0;
		foreach($Pilihan as $terpilih)
		{	
			$KapasitasSkrg = GetaField('kelas', "KelasID='$terpilih' and KodeID", KodeID, 'KapasitasSekarang');
			$Kapasitas = GetaField('kelas', "KelasID='$terpilih' and KodeID", KodeID, 'KapasitasMaksimum');
			if($KapasitasSkrg < $Kapasitas)	
			{
				$count = $KapasitasSkrg;
				
				while($count < $Kapasitas)
				{	//echo "$count.";
					
					if($countMahasiswa == $JumlahMahasiswa)
					{	break;
					}
					
					if($count%$JumlahProdi == 0)
					{	$prodilist = ShuffleArray($prodilist, $JumlahProdi, 12);
						$kelaminlist = ShuffleArray($kelaminlist, 2, 5);
					}
					
					$tahunstring = (empty($_SESSION['opk_tahun_filter']))? "" : "and TahunID='$_SESSION[opk_tahun_filter]'";
					$nilaistring = "and NilaiUjian>='$_SESSION[nilai_dari]' and NilaiUjian<='$_SESSION[nilai_sampai]'";
					$kelaminstring = (empty($_SESSION['kelamin_by']))? "" : "and Kelamin='$_SESSION[kelamin_by]'";
					$prodistring = (empty($_SESSION['prodi']))? "" : "and ProdiID='$_SESSION[prodi]'";
					$programstring = (empty($_SESSION['program']))? "" : "and ProgramID='$_SESSION[program]'";
					
					if($CheckNilai == 'TopScore')
					{	$nilaiorder = "order by NilaiUjian DESC"; 
						// Cari nilai Ujian yang Maksimum
						$s = "select MhswID, NilaiUjian	from mhsw
									where KodeID='".KodeID."' and KelasID='0' $kelaminstring $prodistring $nilaistring $tahunstring $nilaiorder 
							  ";
						$r = _query($s);
						$w = _fetch_array($r);
						$nilaistring = "and NilaiUjian='$w[NilaiUjian]'";
					}
					if($CheckKelamin == 'HalfHalf')
					{	$countkelamin = $count%2;
						$kelaminstring = "and Kelamin='$kelaminlist[$countkelamin]'";
					}
					if($CheckProdi == 'Evenly')
					{	$countprodi = $count%$JumlahProdi;
						$prodistring = "and ProdiID='$prodilist[$countprodi]'";
					}
					
					// List semua mahasiswa yang memenuhi kriteria
					$s = "select MhswID
							from mhsw
								where KodeID='".KodeID."' and KelasID='0' $kelaminstring $prodistring $nilaistring $tahunstring $nilaiorder
						  ";
					$r = _query($s);
					$n = _num_rows($r);
					
					if($n!=0)
					{	//select one of the entries at random
						$random = rand(0, $n-1);
						
						$s = "select MhswID
							from mhsw
								where KodeID='".KodeID."' and KelasID='0' $kelaminstring $prodistring $nilaistring $tahunstring $nilaiorder
								limit $random, 1";
						$r = _query($s);
						$w = _fetch_array($r);
						//echo "Try random $random, Put $w[MhswID] to $terpilih<br>";
						
						$ss = "update mhsw set KelasID='$terpilih' where MhswID = '$w[MhswID]'";
						$rr = _query($ss);
						
						$sss = "select MhswID from `mhsw` where KelasID='$terpilih'"; 
						$rrr = _query($sss);
						$nnn = _num_rows($rrr);
		
						$ss = "update kelas set KapasitasSekarang='$nnn' where KelasID='$terpilih'";
						$rr = _query($ss);
						
						$count++;
						$countMahasiswa++;
					}
					else
					{	$count++;
						$Kapasitas++;
					}
					//echo "<br>";
				}
			}
			if($countMahasiswa == $JumlahMahasiswa)break;
		}
}		
		ClosingScript();
	}
	
	function ShuffleArray($arr, $size, $times)
	{	$i = 0;
		while($i < $times)
		{	$rand1 = rand(0, $size-1);
			$rand2 = rand(0, $size-1);
			
			$temp = $arr[$rand1];
			$arr[$rand1] = $arr[$rand2];
			$arr[$rand2] = $temp;
			
			$i++;
		}
		
		return $arr;
	}
	
	function ClosingScript()
	{	echo "<SCRIPT>
					opener.location='../index.php?mnux=$_SESSION[mnux]&gos=entry&sub=';
					self.close();
				</SCRIPT>";
	}
?>