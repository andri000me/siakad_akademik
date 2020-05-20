<?php
// Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com
// Start Date; 17 Maret 2009

session_start();
// *** Parameters ***
$JadwalRemedialID = $_REQUEST['JRID']+0;
// *** Main ***
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Daftar Remedial Mahasiswa");
TampilkanJudul("Daftar Remedial Mahasiswa");

$gos = (empty($_REQUEST['gos']))? 'EditRemedial' : $_REQUEST['gos'];
$gos($JadwalRemedialID);

// *** Functions ***

function CheckDatesScript()
{	echo "<script>
			function CheckHours(mydoc)
			{	alert(mydoc.form.rem_form.TglTatapMuka1_h.value);
			}
		</script>";
}
function loadJavaScripts()
{	echo "<script>
			function ignoreCheckBoxDisable(chk, chk2, myform)
			{	total = (document.getElementById('JumlahNorm')).value;
				for (i = 1; i <= total; i++)
				{	
					(document.getElementById(chk+i)).disabled = false;
				}
				total2 = (document.getElementById('JumlahRem')).value;
				for (i = 1; i <= total2; i++)
				{	
					(document.getElementById(chk2+i)).disabled = false;
				}
				myform.submit();
			}
		</script>";
}

function EditRemedial($JadwalRemedialID)
{	$MKID = $_REQUEST['MKID']+0;
	if($JadwalRemedialID==0)
	{		
		$tahunstring =(empty($_SESSION['TahunID']))? "" : "and j.TahunID='$_SESSION[TahunID]'";
		$prodistring =(empty($_SESSION['_remedialProdiID']))? "" : "and j.ProdiID='$_SESSION[_remedialProdiID]'";
	
		$s = "select j.MKKode, j.Nama, j.SKS, j.ProdiID, j.TahunID, d.Nama as _Dosen 
				from jadwal j left outer join dosen d on j.DosenID=d.Login and j.KodeID=d.KodeID
				where j.MKID='$MKID' $tahunstring $prodistring and j.KodeID='".KodeID."'";
		$r = _query($s);
		$w = _fetch_array($r);
		$w['TglTatapMuka1'] = date('Y-m-d')." 00:00:00";
		$w['AkhirTglTatapMuka1'] = date('Y-m-d')." 00:00:00";
		$w['TglTatapMuka2'] = date('Y-m-d')." 00:00:00";
		$w['AkhirTglTatapMuka2'] = date('Y-m-d')." 00:00:00";
		$w['TglUjian'] = date('Y-m-d')." 00:00:00";
		$w['AkhirTglUjian'] = date('Y-m-d')." 00:00:00";
		//$gos = 'JadwalRemedialSav';
	}
	else
	{
		$s = "select jd.*, d.Nama as _Dosen 
			from jadwalremedial jd left outer join dosen d on jd.DosenID=d.Login and jd.KodeID=d.KodeID
			where jd.KodeID='".KodeID."' and jd.JadwalRemedialID='$JadwalRemedialID'";
		$r = _query($s);
		$w = _fetch_array($r);
		
		//$gos = 'RemedialSav';
	}
	
	$tglTatap1 = GetDateOption($w['TglTatapMuka1'], 'TglTatapMuka1');
	$tempjamtatap1 = substr($w['TglTatapMuka1'], 11, 5);
	$jamTatap1 = GetTimeOption($tempjamtatap1, 'TglTatapMuka1');
	$tempakhirjamtatap1 = substr($w['AkhirTglTatapMuka1'], 11, 5);
	$akhirjamTatap1 = GetTimeOption($tempakhirjamtatap1, 'AkhirTglTatapMuka1');
	$tglTatap2 = GetDateOption($w['TglTatapMuka2'], 'TglTatapMuka2');
	$tempjamtatap2 = substr($w['TglTatapMuka2'], 11, 5);
	$jamTatap2 = GetTimeOption($tempjamtatap2, 'TglTatapMuka2');
	$tempakhirjamtatap2 = substr($w['AkhirTglTatapMuka2'], 11, 5);
	$akhirjamTatap2 = GetTimeOption($tempakhirjamtatap2, 'AkhirTglTatapMuka2');
	$tglUjian = GetDateOption($w['TglUjian'], 'TglUjian');
	$tempjamUjian = substr($w['TglUjian'], 11, 5);
	$jamUjian = GetTimeOption($tempjamUjian, 'TglUjian');
	$tempakhirjamujian = substr($w['AkhirTglUjian'], 11, 5);
	$akhirjamUjian = GetTimeOption($tempakhirjamujian, 'AkhirTglUjian');
	$optruangtatap1 = GetOption2('ruang', 'RuangID', 'RuangID', $w['RuangIDTatapMuka1'], "KodeID='".KodeID."'", 'RuangID');
	$optruangtatap2 = GetOption2('ruang', 'RuangID', 'RuangID', $w['RuangIDTatapMuka2'], "KodeID='".KodeID."'", 'RuangID');
	$optruangujian = GetOption2('ruang', 'RuangID', 'RuangID', $w['RuangIDUjian'], "KodeID='".KodeID."'", 'RuangID');
	$optdosen = GetOption2('dosen', 'Nama', 'Nama', $w['DosenID'], "KodeID='".KodeID."' and INSTR(ProdiID, '.$w[ProdiID].')>0", 'Login');
	
	CheckDatesScript();
	
	echo "<table class=box cellspacing=1 align=center width=800>
			<form name='rem_form' action='?' method=POST onSubmit=\"return CheckHours(this)\">
				<input type=hidden name='gos' value='JadwalRemedialSav' \>
				<input type=hidden name='JRID' value='$JadwalRemedialID' \>
				<input type=hidden name='MKID' value='$_REQUEST[MKID]' \>
				<input type=hidden name='TahunID' value='$_SESSION[TahunID]' \>
				<input type=hidden name='ProdiID' value='$w[ProdiID]' \>
				<input type=hidden name='RemTahunID' value='$_SESSION[_remedialTahunID]' \>
				
				<tr><td colspan=4><hr color=green size=2></hr></td><tr>	
				<tr><td class=inp>Mata Kuliah:</td>
					<td class=ul1>$w[MKKode] - $w[Nama]</td>
					<td class=inp>SKS</td>
					<td class=ul1>$w[SKS]</td></tr>
				<tr><td class=inp width=100>Thn Akademik:</td>
					<td class=ul1 width=300>$w[TahunID]</td>
					<td class=inp width=100>Program Studi:</td>
					<td class=ul1 width=300>$w[ProdiID] - $w[ProgramID]</td></tr>
				<tr><td colspan=4><hr color=green size=2></hr></td><tr>	
				<tr><td class=inp>Dosen Remedial:</td>
					<td class=ul1><select name='DosenID'>$optdosen</select></td></tr>		
				<tr><td class=inp>Tanggal Kuliah:</td>
					<td class=ul1>$tglTatap1</td>
					<td class=ul1><font color=green>&</font></td>
					<td class=ul1>$tglTatap2</td></tr>
				<tr><td class=inp>Jam Kuliah:</td>
					<td class=ul1>$jamTatap1 - $akhirjamTatap1</td>
					<td class=inp></td>
					<td class=ul1>$jamTatap2 - $akhirjamTatap2</td></tr>
				<tr><td class=inp>Ruang Kuliah:</td>
					<td class=ul1><select name='RuangTatap1'>$optruangtatap1</select></td>
					<td class=inp></td>
					<td class=ul1><select name='RuangTatap2'>$optruangtatap2</select></td></tr>
				<tr><td class=inp>Tanggal Ujian:</td>
					<td class=ul1>$tglUjian</td></tr>
				<tr><td class=inp>Jam Ujian:</td>
					<td class=ul1>$jamUjian - $akhirjamUjian</td></tr>
				<tr><td class=inp>Ruang Ujian:</td>
					<td class=ul1><select name='RuangUjian'>$optruangujian</td></tr>
				<tr><td colspan=4><hr color=green size=2></hr></td><tr>";
	
	if($JadwalRemedialID==0)
	{	echo "		<tr><td colspan=4 align=center><input type=submit name='Simpan' value='Simpan dan Proses Remedial'>
								  <input type=button name='Batal' value='Batal' onClick=\"self.close()\"</td></tr>";
		echo "	</form>
		</table>";
	}
	else
	{	echo "		<tr><td colspan=4 align=center><input type=submit name='Simpan' value='Simpan Jadwal'></td></tr>";
		echo "	</form>
			</table>";
		loadJavaScripts();
		echo "
			<table class=box cellspacing=1 align=center width=800>
			   <form action='?' method=POST>
				<input type=hidden name='gos' value='RemedialSav' \>
				<input type=hidden name='JRID' value='$JadwalRemedialID' \>
				<input type=hidden name='MKID' value='$_REQUEST[MKID]' \>
				<input type=hidden name='TahunID' value='$_SESSION[TahunID]' \>
				<input type=hidden name='ProdiID' value='$_SESSION[_remedialProdiID]' \>
				<input type=hidden name='RemTahunID' value='$_SESSION[_remedialTahunID]' \>
			   <tr><td width=800>
				<table class=box cellspacing=1 align=center border=1 width=650>
				<tr><th class=ttl width=20>#</th>
					<th class=ttl width=80>NIM</th>
					<th class=ttl width>Nama</th>
					<th class=ttl width=50>Tahun</th>
					<th class=ttl width=200>Dosen MK</th>
					<th class=ttl width=30>Grade</th>
					<th class=ttl width=20>Check</th>
				";
	
		$s1 = "select a.MhswID, m.Nama, a.RemedialID, d.Nama as _Dosen, a.TahunID as _Tahun, a.KRSID, a.GradeNilai, r.Final, r.RemedialSet 
								   from krs a 
										left outer join khs b on a.KHSID=b.KHSID and a.KodeID=b.KodeID
										left outer join mhsw m on m.MhswID=a.MhswID and a.KodeID=m.KodeID
										left outer join jadwal j on a.JadwalID=j.JadwalID and a.KodeID=j.KodeID
										left outer join dosen d on j.DosenID=d.Login and a.KodeID=d.KodeID
										left outer join remedial r on a.RemedialID=r.RemedialID
			where (a.GradeNilai='D' or a.GradeNilai='E') and a.TidakLengkap='N' and a.Final='Y' and a.KodeID='".KodeID."' and a.MKID='$MKID'
				and (r.TahunID='$_SESSION[_remedialTahunID]' or r.TahunID is NULL)
				order by a.Nama
			";
		$r1 = _query($s1);
	
		$counting = 0;
		while($w1=_fetch_array($r1))
		{	$counting++;
			$check = (empty($w1['RemedialID']))? '' : 'checked';
			$disabled = ($w1['Final'] == 'Y' or $w1['RemedialSet'] == 'Y')? 'disabled' : '';
			$grade = (empty($w1['GradeNilai']))? '-' : $w1['GradeNilai'];
			echo "<tr><td>$counting.</td>
					<td>$w1[MhswID]</td>
					<td>$w1[Nama]</td>
					<td align=center>$w1[_Tahun]</td>
					<td>$w1[_Dosen]</td>
					<td align=center>$w1[GradeNilai]</td>
					<td align=center><input type=checkbox id='Norm$counting' name='Pilihan[]' value='$w1[KRSID]' $check $disabled>
									 <input type=hidden name='SemuaPilihan[]' value='$w1[KRSID]'></td>
					 </td></tr>";
		}
		$s1 = "select r.MhswID, m.Nama, r.RemedialID, d.Nama as _Dosen, r.TahunID as _Tahun, r.KRSID, r.GradeNilai, 
					r.RemedialLanjutanID, r2.Final, r2.RemedialSet
								   from remedial r
										left outer join krs k on r.KRSID=k.KRSID and r.KodeID=k.KodeID
										left outer join mhsw m on m.MhswID=r.MhswID and r.KodeID=m.KodeID
										left outer join jadwalremedial jd on jd.JadwalRemedialID=r.JadwalRemedialID
										left outer join dosen d on jd.DosenID=d.Login and r.KodeID=d.KodeID
										left outer join remedial r2 on r.RemedialLanjutanID=r2.RemedialID
			where (r.GradeNilai='D' or r.GradeNilai='E') and r.Final='Y' and r.RemedialLanjutanID=0 and r.KodeID='".KodeID."' and k.MKID='$MKID'
				and (r.TahunID!='$_SESSION[_remedialTahunID]')
				order by m.Nama
			";
		$countNORM = $counting;
		$countREM = 0;
		$r1 = _query($s1);
		while($w1=_fetch_array($r1))
		{	$counting++;
			$countREM++;
			$check = ($w1['RemedialLanjutanID']==0)? '' : 'checked';
			$disabled = ($w1['Final'] == 'Y' or $w1['RemedialSet'] == 'Y')? 'disabled' : '';
			$grade = (empty($w1['GradeNilai']))? '-' : $w1[GradeNilai];
			echo "<tr><td>$counting.</td>
					<td>$w1[MhswID]</td>
					<td>$w1[Nama](REM)</td>
					<td align=center>$w1[_Tahun]</td>
					<td>$w1[_Dosen]</td>
					<td align=center>$w1[GradeNilai]</td>
					<td align=center><input type=checkbox id='Rem$countREM' name='PilihanRem[]' value='$w1[RemedialID]' $check $disabled>
									 <input type=hidden name='SemuaPilihanRem[]' value='$w1[RemedialID]'></td>
					 </td></tr>";
		}
		
		echo "		</table></td></tr>";
		echo "		<tr><td colspan=4 align=center><input type=button name='SubmitButton' value='Simpan' onClick=\"ignoreCheckBoxDisable('Norm', 'Rem', this.form)\" />
								  <input type=button name='Batal' value='Batal' onClick=\"self.close()\" />
								  <input type=hidden id='JumlahNorm' name='JumlahNorm' value='$countNORM' >
								  <input type=hidden id='JumlahRem' name='JumlahRem' value='$countREM' >
								  </td></tr></table>";		
	}
}

function JadwalRemedialSav()
{	$TglTatapMuka1 = "$_REQUEST[TglTatapMuka1_y]-$_REQUEST[TglTatapMuka1_m]-$_REQUEST[TglTatapMuka1_d]  $_REQUEST[TglTatapMuka1_h]:$_REQUEST[TglTatapMuka1_n]";
	$AkhirTglTatapMuka1 = "$_REQUEST[TglTatapMuka1_y]-$_REQUEST[TglTatapMuka1_m]-$_REQUEST[TglTatapMuka1_d]  $_REQUEST[AkhirTglTatapMuka1_h]:$_REQUEST[AkhirTglTatapMuka1_n]";
	$TglTatapMuka2 = "$_REQUEST[TglTatapMuka2_y]-$_REQUEST[TglTatapMuka2_m]-$_REQUEST[TglTatapMuka2_d]  $_REQUEST[TglTatapMuka2_h]:$_REQUEST[TglTatapMuka2_n]";
	$AkhirTglTatapMuka2 = "$_REQUEST[TglTatapMuka2_y]-$_REQUEST[TglTatapMuka2_m]-$_REQUEST[TglTatapMuka2_d]  $_REQUEST[AkhirTglTatapMuka2_h]:$_REQUEST[AkhirTglTatapMuka2_n]";
	$TglUjian = "$_REQUEST[TglUjian_y]-$_REQUEST[TglUjian_m]-$_REQUEST[TglUjian_d]  $_REQUEST[TglUjian_h]:$_REQUEST[TglUjian_n]";
	$AkhirTglUjian = "$_REQUEST[TglUjian_y]-$_REQUEST[TglUjian_m]-$_REQUEST[TglUjian_d]  $_REQUEST[AkhirTglUjian_h]:$_REQUEST[AkhirTglUjian_n]";
	$RuangTatap1 = $_REQUEST['RuangTatap1'];
	$RuangTatap2 = $_REQUEST['RuangTatap2'];
	$RuangUjian = $_REQUEST['RuangUjian'];
	$DosenID = $_REQUEST['DosenID'];
	$JadwalRemedialID = $_REQUEST['JRID']+0;
	$MKID = $_REQUEST['MKID']+0;
	$TahunID = $_REQUEST['TahunID'];
	$ProdiID = $_REQUEST['ProdiID'];
	$RemTahunID = $_REQUEST['RemTahunID'];
	
	$error = CheckTanggalRemedial($TglTatapMuka1, $AkhirTglTatapMuka1,
								$TglTatapMuka2, $AkhirTglTatapMuka2,
								$TglUjian, $AkhirTglUjian);
	
	if(!empty($error))
	{	$a = "Terjadi kesalahan pemasukan data Jadwal Remedial:<br><br>";
		foreach($error as $msg)
		{	$a .= '&bull; '.$msg.'<br>';
		}
		$a .= "<br><input type=button name='Kembali' value='Kembali' onClick=\"location='../$_SESSION[mnux].jdwl.php?MKID=$MKID&JRID=$JadwalRemedialID'\" ";
		die(ErrorMsg("Kesalahan Pemasukan Data", $a));
	}
	
	$s1 = "select k.MKKode, k.Nama, k.SKS, j.ProgramID, j.NamaKelas 
			from krs k left outer join jadwal j on k.KodeID=j.KodeID and k.JadwalID=j.JadwalID  
				where k.MKID='$MKID' and k.KodeID='".KodeID."'";
	$r1 = _query($s1);
	$w1 = _fetch_array($r1);
	
	if(empty($JadwalRemedialID))
	{
		$s = "insert into `jadwalremedial`
				set KodeID='".KodeID."', TahunID='$RemTahunID', ProdiID='$ProdiID', DosenID='$DosenID', 
					MKID='$MKID', MKKode='$w1[MKKode]', Nama='$w1[Nama]', SKS='$w1[SKS]', ProgramID='$w1[ProgramID]', NamaKelas='$w1[NamaKelas]',
					TglTatapMuka1='$TglTatapMuka1', TglTatapMuka2='$TglTatapMuka2', TglUjian='$TglUjian',
					AkhirTglTatapMuka1='$AkhirTglTatapMuka1', AkhirTglTatapMuka2='$AkhirTglTatapMuka2', AkhirTglUjian='$AkhirTglUjian',
					RuangIDTatapMuka1='$RuangTatap1', RuangIDTatapMuka2='$RuangTatap2', RuangIDUjian='$RuangUjian',
					LoginBuat='$_SESSION[_Login]', TanggalBuat=now()";
		$r = _query($s);
		
		$JadwalRemedialID= GetaField('jadwalremedial', "TahunID='$RemTahunID' and ProdiID='$ProdiID' and MKID='$MKID' and KodeID", KodeID, 'JadwalRemedialID');
	}
	else
	{	$s = "update `jadwalremedial`
				set DosenID='$DosenID', ProdiID='$ProdiID', 
					TglTatapMuka1='$TglTatapMuka1', TglTatapMuka2='$TglTatapMuka2', TglUjian='$TglUjian',
					AkhirTglTatapMuka1='$AkhirTglTatapMuka1', AkhirTglTatapMuka2='$AkhirTglTatapMuka2', AkhirTglUjian='$AkhirTglUjian',
					RuangIDTatapMuka1='$RuangTatap1', RuangIDTatapMuka2='$RuangTatap2', RuangIDUjian='$RuangUjian',
					LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
				where JadwalRemedialID='$JadwalRemedialID' and KodeID='".KodeID."'";
		$r = _query($s);
	}	
	RefreshScript($MKID, $JadwalRemedialID);
}

function RemedialSav()
{	$JadwalRemedialID = $_REQUEST['JRID'];
	$Pilihan = $_REQUEST['Pilihan'];
	$PilihanRem = $_REQUEST['PilihanRem'];
	$SemuaPilihan = $_REQUEST['SemuaPilihan'];
	$SemuaPilihanRem = $_REQUEST['SemuaPilihanRem'];
	$TahunID = $_REQUEST['TahunID'];
	$ProdiID = $_REQUEST['ProdiID'];
	$RemTahunID = $_REQUEST['RemTahunID'];
	
	if(!empty($Pilihan))
	{	$count = 0;
		foreach($Pilihan as $terpilih)
		{	
			$temprid = $SemuaPilihan[$count];
			while($temprid != $terpilih)  
			{	$RemID = GetaField('krs', 'KRSID', $SemuaPilihan[$count], 'RemedialID');
				if(!empty($RemID))
				{	
					$s = "delete from remedial where RemedialID='$RemID' and KodeID='".KodeID."'";
					$r = _query($s);
					$s = "update krs set RemedialID=NULL where KRSID='$SemuaPilihan[$count]' and KodeID='".KodeID."'";
					$r = _query($s);
				}
				$count++;
				$temprid = $SemuaPilihan[$count];
			}
			
			$RemID = GetFields('krs', 'KRSID', $SemuaPilihan[$count], 'RemedialID,KRSID,MhswID');
			if(empty($RemID['RemedialID']))
			{	
				$s = "insert into `remedial`
					set KodeID='".KodeID."', KRSID='$RemID[KRSID]', MhswID='$RemID[MhswID]', TahunID='$RemTahunID', 
						JadwalRemedialID='$JadwalRemedialID', LoginBuat='$_SESSION[_Login]', TanggalBuat=now()";
				$r = _query($s);
				
				// Set RemedialID di krs agar kita bisa lebih cepat mengetahui apakah suatu krs sedang mengalami remedial
				$RemedialID = GetaField('remedial', "KRSID='$RemID[KRSID]' and MhswID='$RemID[MhswID]' and TahunID='$RemTahunID' 
							and JadwalRemedialID='$JadwalRemedialID' and KodeID", KodeID, 'RemedialID'); 
				$s = "update krs set RemedialID='$RemedialID' where KRSID='$terpilih' and KodeID='".KodeID."'";
				$r = _query($s);
			}
			$count++;
		}
		while(!empty($SemuaPilihan[$count]))  
		{	$RemID = GetaField('krs', 'KRSID', $SemuaPilihan[$count], 'RemedialID');
			if(!empty($RemID))
			{	
				$s = "delete from remedial where RemedialID='$RemID' and KodeID='".KodeID."'";
				$r = _query($s);
				$s = "update krs set RemedialID=NULL where KRSID='$SemuaPilihan[$count]' and KodeID='".KodeID."'";
				$r = _query($s);
			}
			$count++;
		}
	}
	else
	{	
		foreach($SemuaPilihan as $terpilih)
		{	$RemID = GetaField('krs', 'KRSID', $terpilih, 'RemedialID');
			echo "REMID $count: !$RemID!<br";
			if(!empty($RemID))
			{	
				$s = "delete from remedial where RemedialID='$RemID' and KodeID='".KodeID."'";
				$r = _query($s);
				$s = "update krs set RemedialID=NULL where KRSID='$terpilih' and KodeID='".KodeID."'";
				$r = _query($s);
			}
		}
	}
	
	if(!empty($PilihanRem))
	{	$count = 0;
		foreach($PilihanRem as $terpilih)
		{	$temprid = $SemuaPilihanRem[$count];
			while($temprid != $terpilih)  
			{	$RemID = GetaField('remedial', 'RemedialID', $SemuaPilihanRem[$count], 'RemedialLanjutanID');
				if(!empty($RemID))
				{	
					$s = "delete from remedial where RemedialID='$RemID' and KodeID='".KodeID."'";
					$r = _query($s);
					$s = "update remedial set RemedialLanjutanID=NULL where RemedialID='$SemuaPilihanRem[$count]' and KodeID='".KodeID."'";
					$r = _query($s);
				}
				$count++;
				$temprid = $SemuaPilihanRem[$count];
			}
			
			$RemID = GetFields('remedial', 'RemedialID', $SemuaPilihanRem[$count], 'RemedialLanjutanID,KRSID,MhswID');
			if(empty($RemID['RemedialLanjutanID']))
			{	
				$s = "insert into `remedial`
					set KodeID='".KodeID."', KRSID='$RemID[KRSID]', MhswID='$RemID[MhswID]', TahunID='$RemTahunID', RemedialRefID='$SemuaPilihanRem[$count]',
						JadwalRemedialID='$JadwalRemedialID', LoginBuat='$_SESSION[_Login]', TanggalBuat=now()";
				$r = _query($s);
				
				// Set RemedialID di krs agar kita bisa lebih cepat mengetahui apakah suatu remedial sedang mengalami remedial
				$RemedialLanjutanID = GetaField('remedial', "KRSID='$RemID[KRSID]' and MhswID='$RemID[MhswID]' and TahunID='$RemTahunID' 
							and RemedialRefID='$SemuaPilihanRem[$count]' and JadwalRemedialID='$JadwalRemedialID' and KodeID", KodeID, 'RemedialID'); 
				$s = "update remedial set RemedialLanjutanID='$RemedialLanjutanID' where RemedialID='$terpilih' and KodeID='".KodeID."'";
				$r = _query($s);
			}
			$count++;
		}
		while(!empty($SemuaPilihanRem[$count]))  
		{	$RemID = GetaField('remedial', 'RemedialID', $SemuaPilihanRem[$count], 'RemedialLanjutanID');
			if(!empty($RemID))
			{	
				$s = "delete from remedial where RemedialID='$RemID' and KodeID='".KodeID."'";
				$r = _query($s);
				$s = "update remedial set RemedialLanjutanID=NULL where RemedialID='$SemuaPilihanRem[$count]' and KodeID='".KodeID."'";
				$r = _query($s);
			}
			$count++;
		}
	}
	else
	{	if(!empty($SemuaPilihanRem))
		{
			foreach($SemuaPilihanRem as $terpilih)
			{	$RemID = GetaField('remedial', 'RemedialID', $terpilih, 'RemedialLanjutanID');
				if(!empty($RemID))
				{	
					$s = "delete from remedial where RemedialID='$RemID' and KodeID='".KodeID."'";
					$r = _query($s);
					$s = "update remedial set RemedialLanjutanID=NULL where RemedialID='$terpilih' and KodeID='".KodeID."'";
					$r = _query($s);
				}
			}
		}
	}
	
	TutupScript();
}

function CheckTanggalRemedial($tgl1, $tgl1a, $tgl2, $tgl2a, $tglu, $tglua)
{	$error = array();
	if($tgl1a < $tgl1) $error[] = 'Jam Tatap Muka 1 : <b>'.substr($tgl1, 11, 6).'</b> harus sebelum Jam Akhir : <b>'.substr($tgl1a, 11, 6).'</b>.';
	if($tgl2a < $tgl2) $error[] = 'Jam Tatap Muka 2 : <b>'.substr($tgl2, 11, 6).'</b> harus sebelum Jam Akhir : <b>'.substr($tgl2a, 11, 6).'</b>.';
	if($tglua < $tglu) $error[] = 'Jam Ujian : <b>'.substr($tglu, 11, 6).'</b> harus sebelum Jam Akhir : <b>'.substr($tglua, 11, 6).'</b>.';
	
	if($tgl2 < $tgl1) $error[] = 'Tanggal Tatap Muka 1: <b>'.substr($tgl1, 0, 11).'</b> harus sebelum Tanggal Tatap Muka 2 : <b>'.substr($tgl2, 0, 11).'</b>';
	if($tglu < $tgl1) $error[] = 'Tanggal Tatap Muka 1: <b>'.substr($tgl1, 0, 11).'</b> harus sebelum Tanggal Ujian : <b>'.substr($tglu, 0, 11).'</b>';
	if($tglu < $tgl2) $error[] = 'Tanggal Tatap Muka 2: <b>'.substr($tgl2, 0, 11).'</b> harus sebelum Tanggal Ujian : <b>'.substr($tglu, 0, 11).'</b>';
	
	return $error;
}

function RefreshScript($MKID, $JadwalRemedialID) {
echo <<<SCR
<SCRIPT>
  function rrefresh() {
    opener.location='../index.php?mnux=$_SESSION[mnux]';
    self.location='../$_SESSION[mnux].jdwl.php?MKID=$MKID&JRID=$JadwalRemedialID';
    return true;
  }
  rrefresh();
</SCRIPT>
SCR;
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}

?>