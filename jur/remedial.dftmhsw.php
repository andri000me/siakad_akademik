<?php
// Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com
// Start Date; 17 Maret 2009

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Daftar Remedial Mahasiswa");
TampilkanJudul("Daftar Remedial Mahasiswa");

// *** Parameters ***
$JadwalRemedialID = $_REQUEST['id']+0;
$jdwlrem = GetFields('jadwalremedial', "JadwalRemedialID='$JadwalRemedialID' and KodeID", KodeID, "*");
$_rem_filter_grade = GetSetVar('_rem_filter_grade', 'D');
$_rem_filter_tahun = GetSetVar('_rem_filter_tahun');
$_rem_filter_mkkode = GetSetVar('_rem_filter_mkkode', $jdwlrem['MKID']);
//echo "GRADE: $_rem_filter_grade, TAHUN: $_rem_filter_tahun, MKKODE: $_rem_filter_mkkode<br>";

// *** Main ***

$gos = (empty($_REQUEST['gos']))? 'EditRemedial' : $_REQUEST['gos'];
$gos($jdwlrem);

// *** Functions ***

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

function EditRemedial($jdwlrem)
{	$opt_filter_grade = GetOption2('nilai', "Nama", 'Nama', $_SESSION['_rem_filter_grade'], "KodeID='".KodeID."' and BisaRemedial='Y' and ProdiID='$jdwlrem[ProdiID]'", 'Nama', 0, 0);
	echo AmbilHeader($jdwlrem);
	
	$filter_tahun =(empty($_SESSION['_rem_filter_tahun']))? "" : "and a.TahunID = '$_SESSION[_rem_filter_tahun]'";
	$filter_grade =(empty($_SESSION['_rem_filter_grade']))? "" : "and a.GradeNilai = '$_SESSION[_rem_filter_grade]'";
	$filter_mkkode = (empty($_SESSION['_rem_filter_mkkode']))? "" : "and a.MKKode = '$_SESSION[_rem_filter_mkkode]'";
	
	$s1 = "select a.MhswID, m.Nama as _Mhsw, d.Nama as _Dosen, a.TahunID as _Tahun, a.KRSID, a.GradeNilai , a.SedangRemedial, a.MKKode, a.Nama,
				kr.KRSRemedialID, kr.Final, k.KHSID as _KHSIDTahunIni
				from krs a
					left outer join mhsw m on a.MhswID=m.MhswID and m.KodeID='".KodeID."'
					left outer join statusmhsw sm on sm.StatusMhswID=m.StatusMhswID and sm.KodeID='".KodeID."'
					left outer join dosen d on a.DosenID=d.Login and d.KodeID='".KodeID."'
					left outer join khs k on k.TahunID='$jdwlrem[TahunID]' and k.MhswID=a.MhswID and k.KodeID='".KodeID."' 
					left outer join krsremedial kr on kr.KRSID=a.KRSID and kr.TahunID='$jdwlrem[TahunID]' and kr.KodeID='".KodeID."'
		where a.KodeID='".KodeID."'
			and a.Final = 'Y'
			and sm.Keluar = 'N'
			$filter_tahun
			$filter_grade
			$filter_mkkode
			order by a.MKKode, m.Nama
		";
	$r1 = _query($s1);

	loadJavaScripts();	

	echo "
		<table class=box cellspacing=1 align=center width=800>
		   <form action='?' method=POST>
			<input type=hidden name='gos' value='RemedialSav' \>
			<input type=hidden name='id' value='$jdwlrem[JadwalRemedialID]' \>
			<table class=box cellspacing=1 align=center border=1 width=650>
			
			";
	$counting = 0;
	$currentMKKode = '938w5y39nbvwp9ent';
		
	while($w1 = _fetch_array($r1))
	{
		if($currentMKKode != $w1['MKKode'])
		{	echo "<br>
				  <tr><th class=ttl colspan=7>$w1[MKKode] - $w1[Nama]</th></tr>";
			echo "<tr><th class=ttl width=20>#</th>
				<th class=ttl width=80>NIM</th>
				<th class=ttl width>Nama</th>
				<th class=ttl width=50>Tahun</th>
				<th class=ttl width=200>Dosen MK</th>
				<th class=ttl width=30>Grade</th>
				<th class=ttl width=20>Check</th>";					
			$currentMKKode = $w1['MKKode'];
		}
		$counting++;
		$class = ($w1['_KHSIDTahunIni']+0 == 0)? 'class=wrn' : '';
		$check = (empty($w1['KRSRemedialID']))? '' : 'checked';
		$grade = (empty($w1['GradeNilai']))? '-' : $w1['GradeNilai'];
		$pilihancheck = ($w1['Final'] == 'Y' || $w1['_KHSIDTahunIni']+0 == 0)? 
							"<input type=checkbox name='Unused[]' value='$w1[KRSID]' $check readonly=true disabled=true>" : 
							"<input type=checkbox name='Pilihan[]' value='$w1[KRSID]' $check>" ;
		echo "<tr><td align=right $class>$counting.</td>
				<td $class>$w1[MhswID]</td>
				<td $class>$w1[_Mhsw]</td>
				<td $class align=center>$w1[_Tahun]</td>
				<td $class>$w1[_Dosen]</td>
				<td $class align=center>$w1[GradeNilai]</td>
				<td $class align=center>$pilihancheck
					<input type=hidden name='SemuaPilihan[]' value='$w1[KRSID]~$w1[MhswID]~$w1[SedangRemedial]'></td>
			</tr>";
	}
	echo "<tr><td class=ul1 colspan=7 align=center><input type=submit name='Simpan' value='Simpan yang Dicentang'></td></tr>
			</table>";
}

function AmbilHeader($jdwlrem)
{	// Buat Isi Dropdown untuk filtering tahun
	$MKSetara = GetaField('mk', "MKKode='$jdwlrem[MKKode]' and KodeID", KodeID, "MKSetara");
	$arrMKSetara = explode('.', $jdwlrem['MKKode'].((empty($MKSetara))? "" : substr($MKSetara, 1,  strlen($MKSetara)-2)));
	
	$filter_mk = "and (a.MKKode='".implode("' or a.MKKode='", $arrMKSetara)."')";
	$arrTahun = array();
	$s = "select DISTINCT(a.TahunID) as _Tahun 
			from krs a
				left outer join mhsw m on a.MhswID=m.MhswID and m.KodeID='".KodeID."'
				left outer join statusmhsw sm on sm.StatusMhswID=m.StatusMhswID
				left outer join jadwal j on a.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
				left outer join nilai n on j.ProdiID=n.ProdiID and n.BisaRemedial='Y'
				left outer join khs k on k.TahunID='$jdwlrem[TahunID]' and k.MhswID=a.MhswID and k.KodeID='".KodeID."' 
			where a.KodeID='".KodeID."'
				and sm.Keluar = 'N'
				and n.Lulus = 'N'
				and a.Final = 'Y'
				and a.TahunID < '$jdwlrem[TahunID]'
				and a.KodeID='".KodeID."'
				$filter_mk
				";
	$r = _query($s);
	while($w = _fetch_array($r)) $arrTahun[] = $w['_Tahun']; 
	sort($arrTahun);
	$opt_filter_tahun = GetOptionFromArray($arrTahun, $_SESSION['_rem_filter_tahun'], 0);
	
	// Buat Isi Dropdown untuk filtering grade yang bisa melakukan remedial
	$opt_filter_grade = GetOption2('nilai', "Nama", 'Nama', $_SESSION['_rem_filter_grade'], "KodeID='".KodeID."' and BisaRemedial='Y' and ProdiID='$jdwlrem[ProdiID]'", 'Nama', 0, 0);
	
	// Buat Isi Dropdown untuk filtering mata kuliah dan mata kuliah yang setara yang bisa diremedialkan secara bersamaan
	$opt_filter_mkkode = GetOptionFromArray($arrMKSetara, $_SESSION['_rem_filter_mkkode'], 0);
	
	return "<table class=box cellspacing=1 align=center width=800>
			<form action='?' method=POST>
				<input type=hidden name='gos' value='EditRemedial' \>
				<input type=hidden name='id' value='$jdwlrem[JadwalRemedialID]' \>
			
				<tr><td colspan=4><hr color=green size=2></hr></td><tr>	
				<tr><td class=inp>Mata Kuliah:</td>
					<td class=ul1>$jdwlrem[MKKode] - $jdwlrem[Nama]</td>
					<td class=inp>SKS</td>
					<td class=ul1>$jdwlrem[SKS]</td></tr>
				<tr><td class=inp width=100>Thn Akademik:</td>
					<td class=ul1 width=300>$jdwlrem[TahunID]</td>
					<td class=inp width=100>Program Studi:</td>
					<td class=ul1 width=300>$jdwlrem[ProdiID] - $jdwlrem[ProgramID]</td></tr>
				<tr><td colspan=4><hr color=green size=2></hr></td></tr>
				<tr><td class=inp>Filter Tahun:</td>
					<td class=ul1><select name='_rem_filter_tahun'>$opt_filter_tahun</select></td>
					<td class=inp>Filter Grade:</td>
					<td class=ul1><select name='_rem_filter_grade'>$opt_filter_grade</select></td>
					</tr>
				<tr><td class=inp>Filter Mata Kuliah:</td>
					<td class=ul1><select name='_rem_filter_mkkode'>$opt_filter_mkkode</select></td>
					<td class=ul1 colspan=2><input type=submit name='Submit' value='Lakukan Filter'>
											<input type=button name='Tutup' value='Tutup' onClick=\"window.close()\"></td>
					</tr>
				</form>
		</table>";
/*
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
		<tr><td colspan=4><hr color=green size=2></hr></td><tr>
	</table>";
*/
}

function RemedialSav($jdwlrem)
{	$Pilihan = $_REQUEST['Pilihan'];
	$SemuaPilihan = $_REQUEST['SemuaPilihan'];
	
	$MhswGagalDaftarRemedial = array();
	$MhswBerhasilDaftarRemedial = array();
	$MhswBerhasilHapusRemedial = array();
	
	foreach($Pilihan as $p)
	{
	}
	
	if(!empty($SemuaPilihan))
	{	$arrPilihanSaja = array();

		foreach($SemuaPilihan as $pilih)
		{	
			$arr = explode('~', $pilih);
			if($arr[2] == 'Y')
			{	if(!in_array($arr[0], $Pilihan))
				{	$s = "delete from krsremedial where JadwalRemedialID='$jdwlrem[JadwalRemedialID]' and MhswID='$arr[1]' and KodeID='".KodeID."'";
					$r = _query($s);
					$s = "update krs set SedangRemedial = 'N' where KRSID='$arr[0]' and KodeID='".KodeID."'";
					$r = _query($s);
					$MhswBerhasilHapusRemedial[] = "Mahasiswa dengan NIM $arr[1] berhasil dihapus dari jadwal remedial";
				}
			}
			else
			{	if(in_array($arr[0], $Pilihan))
				{	$khs = GetFields('khs', "MhswID='$arr[1]' and TahunID='$_SESSION[_remTahun]' and KodeID", KodeID, "KHSID, MhswID, TahunID");
					if(empty($khs))
					{	$MhswGagalDaftarRemedial[] = "Mahasiswa dengan NIM $arr[1] tidak terdaftar di tahun akademik $_SESSION[_remTahun].";		
					}
					else
					{	
						$s = "insert into krsremedial
								(KodeID, KHSID, KRSID, MhswID, TahunID, JadwalRemedialID, 
								MKID, MKKode, Nama, SKS,
								LoginBuat, TanggalBuat)
								values
								('".KodeID."', '$khs[KHSID]', '$arr[0]', '$khs[MhswID]', '$khs[TahunID]', '$jdwlrem[JadwalRemedialID]',
								'$jdwlrem[MKID]', '$jdwlrem[MKKode]', '$jdwlrem[Nama]', '$jdwlrem[SKS]',
								'$_SESSION[_Login]', now())";
						$r = _query($s);
						$s = "update krs set SedangRemedial = 'Y' where KRSID='$arr[0]' and KodeID='".KodeID."'";
						$r = _query($s);
						$MhswBerhasilDaftarRemedial[] = "Mahasiswa dengan NIM $khs[MhswID] berhasil daftar remedial";
					}
				}
			}
		}
		$jml = GetaField('krsremedial', "JadwalRemedialID='$jdwlrem[JadwalRemedialID]' and KodeID", KodeID, "count(KRSRemedialID)");
		$s = "update jadwalremedial set JmlhMhsw = '$jml' where JadwalRemedialID='$jdwlrem[JadwalRemedialID]' and KodeID='".KodeID."'";
		$r = _query($s);
	}
	
	if(!empty($MhswGagalDaftarRemedial))
	{	$daftarmhswgagal = '<br>&bull; '.implode('<br>&bull; ', $MhswGagalDaftarRemedial);
		$daftarmhswberhasil = (empty($MhswBerhasilDaftarRemedial))? 
			"<b>Tidak ada yang didaftarkan</b>" :
			'Mahasiswa yang berhasil didaftarkan: <br>&bull; '.implode('<br>&bull; ', $MhswBerhasilDaftarRemedial);
		$daftarmhswberhasilhapus = (empty($MhswBerhasilHapusRemedial))? 
			"<b>Tidak ada yang dihapus</b>" :
			'Mahasiswa yang berhasil dihapus: <br>&bull; '.implode('<br>&bull; ', $MhswBerhasilHapusRemedial);
		echo ErrorMsg("Kegagalan Pendaftaran", 
			"Terdapat mahasiswa yang tidak dapat didaftarkan untuk remedial: $daftarmhswgagal
			<br>
			<br>$daftarmhswberhasil
			<br>
			<br>$daftarmhswberhasilhapus
			<br><input type=button name='Tutup' value='Tutup' onClick=\"opener.location='../index.php?mnux=$_SESSION[mnux]'; window.close()\" >");
	}
	else
	{	TutupScript();
	}
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

function GetOptionFromArray($arr, $default, $blank=1)
{	$result = '';

	if($blank == 1) $result = "<option value=''></option>";
	foreach($arr as $a)
	{	if($default == $a) $selected = 'selected';
		else $selected = '';
		
		$result .= "<option value='$a' $selected>$a</option>";
	}
	return $result;
}

?>