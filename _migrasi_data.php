<?php
// Start  : 08/01/2009

session_start();
include_once "sisfokampus.php";
include_once "dwo.lib.php";
HeaderSisfoKampus("Migrasi Data STIKES Binawan");

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'fnKonfirmasi' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function fnKonfirmasi() {
  echo Konfirmasi("Konfirmasi",
    "Anda akan mengimpor seluruh database kasih bangsa original ke database sisfo kampus.
    <hr size=1 color=silver />
    <input type=button name='btnProses' value='Proses'
    onClick=\"location='?gos=fnProses'\" />");
}
function fnProses() {
  $NamaDatabaseBaru = "binawan";
  $NamaDatabaseLama = "binawankeperawatan";
  $KodeIDBaru = "BINAWAN";
  
  echo "<ol>";
  
  // fnProsesSetJadwalIDdiKRS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);  
  //fnProsesSetKHSdiKRS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru, 0, 10000);
  //fnProsesSetKHSdiKRS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru, 10000, 10000);
  //fnProsesSetKHSdiKRS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru, 20000, 10000);
  //fnProsesSetKHSdiKRS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru, 30000, 10000);
  //fnProsesSetSesidiKHS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  //fnProsesBuatKHSuntukKRSKosong($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  //fnProsesSetKHSdiKRSKosong($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  //fnProsesBuatKurikulumUntukMKKosong($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  //fnProsesSetKurikulumdiMK($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
 
  //$arrTable = array('mhsw', 'khs', 'mk', 'kurikulum', 'jadwal', );
  //foreach($arrTable as $pertable)
  //{	fnProsesGantiProdiDariProdiDikti($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru, $pertable);
  //}
  
  //fnProsesJmlhMhswDiJadwal($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  //fnProsesSetMKIDNamadiJadwal($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  
  // fnProsesSetBIPOTIDdiKHS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  //fnProsesSetTotalSKSJmlhMKdiKHS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  // fnProsesTotalSKSdiMhsw($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  
  //fnMigrasiUpdateMhsw($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  //fnMigrasiUpdateJadwal($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  //fnMigrasiUpdateProfilMhsw($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  fnUpdateLoginDosen($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru);
  
  //fnMkTime();
  
  echo "</ol>";
  echo "<font size=+1>Selesai.</font>";
}
  
function fnProsesSetJadwalIDdiKRS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{
  
  $s = "select * from $NamaDatabaseBaru.krs";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  { 
	$jadwal = GetFields('jadwal', "MKID='$w[MKID]' and TahunID='$w[TahunID]' and ProdiID='$w[ProdiID]' and ProgramID", $w[ProgramID], "JadwalID, DosenID");
	
	$s1 = "update $NamaDatabaseBaru.krs
				JadwalID = '$jadwal[JadwalID]'
		";
	$r1 = _query($s1);
	$n++;
  }
  echo "<li>KRS Mahasiswa yang diproses: $n</li>";
}

function fnProsesJmlhMhswDiJadwal($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{  $s = "select JadwalID from $NamaDatabaseBaru.jadwal";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  {	$_JumlahMhsw = GetaField("$NamaDatabaseBaru.krs", "JadwalID", $w['JadwalID'], "count(KRSID)"); 
  
	$s1 = "update $NamaDatabaseBaru.jadwal set JumlahMhsw='$_JumlahMhsw' where JadwalID='$w[JadwalID]'
		";
	$r1 = _query($s1);
	$n++;
  }
 echo "<li>Updating Jadwal...</li>";
}

function fnProsesSetKHSdiKRS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru, $start, $limit)
{
  $s = "select KRSID, MhswID, TahunID from $NamaDatabaseBaru.krs limit $start, $limit";
  $r = _query($s);
  $n = 0;
  while($w=_fetch_array($r))
  {	$n++;
    $_KHSID = GetaField("$NamaDatabaseBaru.khs", "MhswID='$w[MhswID]' and TahunID", $w['TahunID'], 'KHSID');
	$s1 = "update $NamaDatabaseBaru.krs set KHSID='$_KHSID' where KRSID='$w[KRSID]'";
	$r1 = _query($s1);
  }
  echo "<li>Updating KRS... $n records</li>";
}

function fnProsesSetSesidiKHS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{ $s = "select MhswID from $NamaDatabaseBaru.mhsw";
  $r = _query($s);
  $n = 0;
  while($w=_fetch_array($r))
  {	$s1 = "select TahunID, MhswID, Sesi from $NamaDatabaseBaru.khs where MhswID='$w[MhswID]' order by TahunID";
	$r1 = _query($s1);
	
	while($w1 = _fetch_array($r1))
	{  $n++;
	   $_Sesi = GetaField("$NamaDatabaseBaru.khs", "MhswID", $w['MhswID'], "max(Sesi)")+1;
	   $s2 = "update $NamaDatabaseBaru.khs set Sesi='$_Sesi' where MhswID='$w1[MhswID]' and TahunID='$w1[TahunID]'";
	   $r2 = _query($s2);
	}
  }
  
  echo "<li>Updating KHS... $n records</li>";  
}

function fnProsesBuatKHSuntukKRSKosong($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{	$s = "select DISTINCT(MhswID), TahunID from $NamaDatabaseBaru.krs where KHSID=0";
  $r = _query($s);
  $n = 0;
  while($w=_fetch_array($r))
  {	$n++;
    $mhsw = GetFields("$NamaDatabaseBaru.mhsw", 'MhswID', $w['MhswID'], '*'); 
	$_Sesi = GetaField("$NamaDatabaseBaru.khs", "MhswID", $w['MhswID'], "count(KHSID)")+1;
	
	$s1 = "insert into $NamaDatabaseBaru.khs set 
				KodeID='$KodeIDBaru',
				TahunID='$w[TahunID]',
				ProgramID='$mhsw[ProgramID]',
				ProdiID='$mhsw[ProdiID]',
				MhswID='$w[MhswID]',
				StatusMhswID='A',
				Sesi='$_Sesi',
				MaxSKS='23'
		";
	$r1 = _query($s1);
  }
  echo "<li>Inserting KHS... $n records</li>";
}

function fnProsesSetKHSdiKRSKosong($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru, $start, $limit)
{
  $s = "select KRSID, MhswID, TahunID from $NamaDatabaseBaru.krs where KHSID=0";
  $r = _query($s);
  $n = 0;
  while($w=_fetch_array($r))
  {	$n++;
    $_KHSID = GetaField("$NamaDatabaseBaru.khs", "MhswID='$w[MhswID]' and TahunID", $w['TahunID'], 'KHSID');
	$s1 = "update $NamaDatabaseBaru.krs set KHSID='$_KHSID' where KRSID='$w[KRSID]'";
	$r1 = _query($s1);
  }
  echo "<li>Updating KRS... $n records</li>";
}

function fnProsesSetBIPOTIDdiKHS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{
  $s = "select ProgramID, ProdiID, KHSID from $NamaDatabaseBaru.khs";
  $r = _query($s);
  while($w=_fetch_array($r))
  {	$_BIPOTID = GetaField("$NamaDatabaseBaru.bipot", "ProdiID='$w[ProdiID]' and ProgramID", $w['ProgramID'], "BIPOTID"); 
	$_SKS = GetaField("$NamaDatabaseBaru.krs", "KHSID", $w['KHSID'], "sum(SKS)"); 
	
	$s1 = "update $NamaDatabaseBaru.khs set BIPOTID='$_BIPOTID', SKS='$_SKS' where KHSID='$w[KHSID]'";
	$r1 = _query($s1);
  }
  echo "<li>Updating KHS...</li>";
}

function fnProsesSetTotalSKSJmlhMKdiKHS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{
  $s = "select ProgramID, ProdiID, KHSID, MhswID, Sesi from $NamaDatabaseBaru.khs";
  $r = _query($s);
  while($w=_fetch_array($r))
  {	$_TotalSKS = GetaField("$NamaDatabaseBaru.khs", "Sesi <= $w[Sesi] and MhswID", $w['MhswID'], "sum(SKS)");
	$_JumlahMK = GetaField("$NamaDatabaseBaru.krs left outer join $NamaDatabaseBaru.khs on krs.KHSID=khs.KHSID",
							"khs.Sesi <= $w[Sesi] and khs.MhswID", $w['MhswID'], "count(distinct(krs.MKID))");
	
	$s1 = "update $NamaDatabaseBaru.khs set TotalSKS='$_TotalSKS', JumlahMK='$_JumlahMK' where KHSID='$w[KHSID]'";
	$r1 = _query($s1);
  }
  echo "<li>Updating KHS...</li>";
}

function fnProsesTotalSKSdiMhsw($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{ $s = "select MhswID from $NamaDatabaseBaru.mhsw";
  $r = _query($s);
  while($w=_fetch_array($r))
  {	$_TotalSKS = GetaField("$NamaDatabaseBaru.khs", "MhswID", $w['MhswID'], "sum(SKS)");
	
	$s1 = "update $NamaDatabaseBaru.mhsw set TotalSKS='$_TotalSKS' where MhswID='$w[MhswID]'";
	$r1 = _query($s1);
  }
  echo "<li>Updating KHS...</li>";
}

function fnProsesBuatKurikulumUntukMKKosong($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{ $s = "select DISTINCT(TempTahunID), ProdiID from $NamaDatabaseBaru.mk";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  {	$n++;
	$s1 = "insert into $NamaDatabaseBaru.kurikulum
			set KurikulumKode = '$w[TempTahunID]',
				Nama = 'Kurikulum $w[TempTahunID]',
				KodeID = '$KodeIDBaru', 
				ProdiID= '$w[ProdiID]', 
				Sesi = 'Semester'
		  ";
    $r1 = _query($s1);
  }
  
  echo "<li>Inserting Kurikulum... $n records</li>";
}

function fnProsesSetKurikulumdiMK($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{ $s = "select MKID, MKKode, TempTahunID, ProdiID from $NamaDatabaseBaru.mk";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  {	$n++;
	//echo "$w[TempTahunID], $w[ProdiID] ~~~~ ";
	$_KurikulumID = GetaField("$NamaDatabaseBaru.kurikulum", "KurikulumKode='$w[TempTahunID]' and ProdiID", $w['ProdiID'], 'KurikulumID');
	//echo "KURIKULUMID = $_KurikulumID";
	$s1 = "update $NamaDatabaseBaru.mk
			set KurikulumID='$_KurikulumID' where MKID='$w[MKID]'
		  ";
    $r1 = _query($s1);
	//echo "$s1</br>";
  }
  
  echo "<li>Updating Kurikulum... $n records</li>";
}	

function fnProsesGantiProdiDariProdiDikti($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru, $table)
{	$arrProdi = array('FT', 'GZ', 'K3', 'IK', 'NS');
	$arrProdiDikti = array('11301', '13211', '13341', '14201', '14901');
    
	$n=0;
	foreach($arrProdi as $perprodi)
	{	$s = "update $NamaDatabaseBaru.$table set ProdiID='$perprodi' where ProdiID='$arrProdiDikti[$n]'";
		$r = _query($s);
		$n += _numrows($r);
    }
	
	echo "<li>Updating $table... $n records</li>";
}

function fnProsesSetMKIDNamadiJadwal($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{	$s = "select JadwalID, MKKode, TahunID from $NamaDatabaseBaru.jadwal";
    $r = _query($s);
	$n = 0;
	while($w = _fetch_array($r))
	{	$n++;
		$mk = GetFields("$NamaDatabaseBaru.mk mk left outer join $NamaDatabaseBaru.kurikulum k on mk.KurikulumID=k.KurikulumID", "k.KurikulumKode='$w[TahunID]' and mk.MKKode", $w['MKKode'], "mk.MKID, mk.Nama, mk.SKS");
		
		$s1 = "update $NamaDatabaseBaru.jadwal set MKID='$mk[MKID]', Nama='$mk[Nama]', SKS='$mk[SKS]' where JadwalID='$w[JadwalID]'";
		$r1 = _query($s1);
	}
	echo "<li>Updating KHS... $n records</li>";
}

function fnProsesBIPOTIDdiMhsw($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{
  $s = "select ProgramID, ProdiID, MhswID from $NamaDatabaseBaru.mhsw";
  $r = _query($s);
  while($w=_fetch_array($r))
  {	$_BIPOTID = GetaField("$NamaDatabaseBaru.bipot", "ProdiID='$w[ProdiID]' and ProgramID", $w['ProgramID'], "BIPOTID"); 
	$s1 = "update $NamaDatabaseBaru.mhsw set BIPOTID='$_BIPOTID' where MhswID='$w[MhswID]'";
	$r1 = _query($s1);
  }
  echo "<li>Updating Mahasiswa...</li>";
  
  $s = "select DISTINCT(MhswID) as _MhswID, JadwalID, count(PresensiMhswID) as _countPresensi from $NamaDatabaseBaru.presensimhsw group by MhswID, JadwalID";
  $r = _query($s);
  while($w = _fetch_array($r))
  {	$s1 = "update $NamaDatabaseBaru.krs set _Presensi='$w[_countPresensi]' where MhswID='$w[_MhswID]' and JadwalID='$w[JadwalID]'";
    $r1 = _query($s1);	
  }  
  echo "<li>Updating KRS...</li>";
}
  // ######################################################
  
function fnProsesPresensiEkstra($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{  // Update Jumlah Kehadiran Dosen
  $s = "select JadwalID from $NamaDatabaseBaru.jadwal";
  $r = _query($s);
  while($w = _fetch_array($r))
  {
	$JmlPertemuan = GetaField('presensi', 'JadwalID', $w['JadwalID'], "count(PresensiID)")+0;
	$s1 = "update $NamaDatabaseBaru.jadwal set Kehadiran = '$JmlPertemuan' where JadwalID='$w[JadwalID]'";
	$r1 = _query($s1);
  }
  echo "<li>Updating Kehadiran Dosen...</li>";

  // update Catatan/Isi Presensi

  $s = "select * from $NamaDatabaseLama.absen_dosen_sba";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  {	$_TahunID = substr($w['TAHUN'], 0, 4).(($w['SEMESTER'] == 'GANJIL')? '1' : '2');
	$_ProdiID = (substr($w['KODE_KURIKULUM'], 0, 4) == '0101')? 'AK' : 'MA';
	$_ProgramID = ($w['KELAS'] == 'MALAM')? 'MLM' : 'PGI';
	
	$_JadwalID = GetaField("$NamaDatabaseBaru.jadwal", "MKKode='$w[KODE_MK]' and ProdiID='$_ProdiID' and ProgramID", $_ProgramID, "JadwalID");
	$_Catatan = mysql_real_escape_string($w['BAHAN_MK']);
	
	$s1 = "update $NamaDatabaseBaru.presensi 
			set Catatan='$_Catatan'
			where JadwalID='$_JadwalID' and Tanggal='$w[TANGGAL]'
		";
	$r1 = _query($s1);
    
	$n++;
  }
  
  $s = "select * from $NamaDatabaseLama.absen_dosen";
  $r = _query($s);
  while($w = _fetch_array($r))
  {	$_TahunID = substr($w['TAHUN'], 0, 4).(($w['SEMESTER'] == 'GANJIL')? '1' : '2');
	$_ProdiID = (substr($w['KODE_KURIKULUM'], 0, 4) == '0101')? 'AK' : 'MA';
	$_ProgramID = ($w['KELAS'] == 'MALAM')? 'MLM' : 'PGI';
	
	$_JadwalID = GetaField("$NamaDatabaseBaru.jadwal", "MKKode='$w[KODE_MK]' and ProdiID='$_ProdiID' and ProgramID", $_ProgramID, "JadwalID");
	$_Catatan = mysql_real_escape_string($w['BAHAN_MK']);
	
	$s1 = "update $NamaDatabaseBaru.presensi 
			set Catatan='$_Catatan'
			where JadwalID='$_JadwalID' and Tanggal='$w[TANGGAL]'
		";
	$r1 = _query($s1);
    
	$n++;
  }
  
  echo "<li>Updating isi pengajaran dosen: $n</li>";
}

  // ####################################################

 

function fnProsesKehadiranSKS($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{	$s = "select JadwalID, SKS from $NamaDatabaseBaru.jadwal where SKS!=0"; 
	$r = _query($s);
	while($w = _fetch_array($r))
	{	$_MaxAbsen = 0; $_RencanaKehadiran = 0; $_KehadiranMin = 0;
		if($w['SKS'] == 2)
		{	$_MaxAbsen = 4;
			$_RencanaKehadiran = 14;
			$_KehadiranMin = 10;
		}
		else if($w['SKS'] == 3)
		{	$_MaxAbsen = 4;
			$_RencanaKehadiran = 16;
			$_KehadiranMin = 12;
		}
		else if($w['SKS'] == 4)
		{	$_MaxAbsen = 8;
			$_RencanaKehadiran = 28;
			$_KehadiranMin = 20;
		}
		else
		{	$_MaxAbsen = 0;
			$_RencanaKehadiran = 0;
			$_KehadiranMin = 0;
		}
		
		$s1 = "update $NamaDatabaseBaru.jadwal set MaxAbsen='$_MaxAbsen', RencanaKehadiran='$_RencanaKehadiran', KehadiranMin='$_KehadiranMin' where JadwalID='$w[JadwalID]'";
		$r1 = _query($s1);
	}
}

function fnMigrasiUpdateMhsw($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{	$s = "select ts.*, tsm.StatusMhswID from $NamaDatabaseLama.tblstudent ts 
				left outer join $NamaDatabaseLama.tblstatusmhs tsm on ts.statusMhsID=tsm.statusMhsID
				left outer join $NamaDatabaseLama.tblprogram tp on tp.programID=ts.programID
				left outer join $NamaDatabaseLama.tblsubprogram tsp on tsp.subprogramID=ts.subProgramID";
	$r = _query($s);
	$unprocessed = '';
	while($w = _fetch_array($r))
	{	$ada= GetaField('mhsw', "MhswID", $w['NIM'], 'MhswID');
		if(empty($ada)) $unprocessed .= $w['NIM'].'<br>';
		else
		{	$_ProgramID = '';
			if($w['programID'] == 1) $_ProgramID = '11';
			else if($w['programID'] == 2)
			{	if($w['subprogramID'] == 4) $_ProgramID='21';
				else if($w['subprogramID'] == 5)  $_ProgramID='22';
				else $_ProgramID='21';
			}
			else if($w['programID'] == 3) $_ProgramID = 'C';
			else if($w['programID'] == 4) $_ProgramID = 'D';
			else if($w['programID'] == 5) $_ProgramID = '41';
			else $_ProgramID='';
			
			$s1 = "update $NamaDatabaseBaru.mhsw 
					set ProgramID='$_ProgramID', 
						TempProgramID='$w[programID]',
						TempSubProgramID='$w[subProgramID]',
						SemesterAwal='$w[semesterMasuk]',
						StatusMhswID='$w[StatusMhswID]',
					where MhswID='$w[NIM]'";
			$r1 = _query($s1);
		}
	}
	if(!empty($unprocessed)) echo "<b>NIM MAHASISWA TIDAK DITEMUKAN PADA</b>:<br>".$unprocessed;
}

function fnMigrasiUpdateProfilMhsw($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{	$s = "select tp.*, ts.NIM, tprov.province as _Propinsi1, tprov2.province as _Propinsi2 
			from $NamaDatabaseLama.tblpersonal tp left outer join $NamaDatabaseLama.tblstudent ts on tp.personID=ts.personID
				left outer join $NamaDatabaseLama.tblprovince tprov on tp.provinceID1 = tprov.provinceID
				left outer join $NamaDatabaseLama.tblprovince tprov2 on tp.provinceID2 = tprov2.provinceID";
	$r = _query($s);
	$unprocessed = ''; $n = 0; $nx = 0;
	while($w = _fetch_array($r))
	{	$ada= GetaField('mhsw', "MhswID", $w['NIM'], 'MhswID');
		if(empty($ada)) 
		{	$unprocessed .= $w['NIM'].'/'.$w['personID'].'<br>';
			$nx++;
		}
		else
		{	$n++;
			$_Kelamin = '';
			if($w['sex'] == 'Female') $_Kelamin = 'P';
			else if($w['sex'] == 'Male') $_Kelamin = 'W';
			else $_Kelamin = '';
		
			$_Agama = '';
			if($w['religion'] == 'KRISTEN' or $w['religion'] == 'Kisten Protestan' or $w['religion'] == 'Kristen Protestan' 
						or $w['religion'] == 'Protestant' or $w['religion'] == 'Kristen Prostestan' or $w['religion'] == 'Protestan'
						or $w['religion'] == 'ADVENT' or $w['religion'] == 'Kristen Advent' or $w['religion'] == 'Kristen Prostestan'
						or $w['religion'] == 'Christian') 
						$_Agama = 'KR';
			else if($w['religion'] == 'ISLAM' or $w['religion'] == 'Moeslem' or $w['religion'] == 'Moslem' or $w['religion'] == 'Isalm'
						or $w['religion'] == 'Muslim' or $w['religion'] == 'Moskem' or $w['religion'] == 'lalam' or $w['religion'] == 'Banyumas') 
						$_Agama = 'I';
			else if($w['religion'] == 'KATHOLIK' or $w['religion'] == 'Katolik' or $w['religion'] == 'Kristen Katolik' 
						or $w['religion'] == 'Chatolic') $_Agama = 'K';
			else if($w['religion'] == 'Hindu') $_Agama = 'H';
			else if($w['religion'] == 'Budha') $_Agama = 'B';
			else $_Agama = 'L';
			
			$_StatusSipil = '';
			if($w['maritalstatus'] == 'married') $_StatusSipil = 'M';
			else $_StatusSipil = 'B';
			
			$_Alamat = str_replace('\'', '', $w['address1a'].((!empty($w['address1b']))? ' '.$w['address1b'] : ''));
			$_AlamatAsal = str_replace('\'', '', $w['address2a'].((!empty($w['address2b']))? ' '.$w['address2b'] : ''));
			
			$s1 = "update $NamaDatabaseBaru.mhsw 
					set Kelamin = '$_Kelamin',
						Kebangsaan = '$w[citizenship]',
						Agama = '$_Agama',
						StatusSipil = '$_StatusSipil',
						Alamat = '$_Alamat',
						Kota = '$w[cityID1]',
						KodePos = '$w[postcode1]',
						Propinsi = '$w[_Propinsi1]',
						Telepon = '$w[phone1]',
						Telephone = '$w[phone1]',
						Handphone = '$w[mobile1]',
						AlamatAsal = '$_AlamatAsal',
						KotaAsal = '$w[cityID2]',
						KodePosAsal = '$w[postcode2]',
						PropinsiAsal = '$w[_Propinsi2]',
						TeleponAsal = '$w[phone2]',
						AnakKe = '$w[anak_ke]',
						JumlahSaudara = '$w[siblings]'
					where MhswID='$w[NIM]'";
			$r1 = _query($s1);
		}
	}
	if(!empty($unprocessed)) echo "<b>NIM MAHASISWA TIDAK DITEMUKAN PADA</b>:<br>".$unprocessed;
	echo "<li>Updating Profil Mahasiwa: $n records berhasil, $nx records gagal</li>";
}

function fnMigrasiUpdateJadwal($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{	$s = "select * from $NamaDatabaseLama.tbljadwalkuliah";
	$r = _query($s);
	$n = 0; $nx =0;
	while($w = _fetch_array($r))
	{	$arrMulai = getDate($w['JadwalMKJamStart']);
		$JM = $arrMulai['hours'].':'.$arrMulai['minutes'].":00";
		$TM = $arrMulai['year'].'-'.$arrMulai['mon'].'-'.$arrMulai['mday'];
		$arrSelesai = getDate($w['JadwalMKJamEnd']);
		$JS = $arrSelesai['hours'].':'.$arrSelesai['minutes'].":00";
		$TS = $arrSelesai['year'].'-'.$arrSelesai['mon'].'-'.$arrSelesai['mday'];
		$_arrRuangID=explode('-', $w['ruanganID']);
		
		$_ProgramID = ''; $_TahunID = '';
		if($w['programID'] == 1) 
		{	$_ProgramID = '11';
			$_TahunID=substr($w['jadwalMKTahunAkademik'], 0, 4).'1';
		}
		else if($w['programID'] == 2)
		{	if($w['subprogramID'] == 4)
			{	$_ProgramID='21';
				$_TahunID =substr($w['jadwalMKTahunAkademik'], 0, 4).'1';
			}
			else if($w['subprogramID'] == 5)
			{	$_ProgramID='22';
				$_TahunID =substr($w['jadwalMKTahunAkademik'], 0, 4).'2';
			}
			else 
			{	$_ProgramID='21';
				$_TahunID =substr($w['jadwalMKTahunAkademik'], 0, 4).'1';
			}
		}
		else if($w['programID'] == 3)
		{	$_ProgramID = 'C';
			$_TahunID =substr($w['jadwalMKTahunAkademik'], 0, 4).'1';
		}
		else if($w['programID'] == 4) 
		{	$_ProgramID = 'D';
			$_TahunID =substr($w['jadwalMKTahunAkademik'], 0, 4).'1';
		}
		else if($w['programID'] == 5) 
		{	$_ProgramID = '41';
			$_TahunID =substr($w['jadwalMKTahunAkademik'], 0, 4).'1';
		}
		else $_ProgramID='';
		
		$_AdaResponsi = ($w['jadwalJenis'] == 'lab')? 'Y' : 'N';
		$_AdaTutorial = ($w['jadwalJenis'] == 'tut')? 'Y' : 'N';
		
		$ada = GetaField('jadwal', "MKKode='$w[courseID]' and TahunID", $_TahunID, 'JadwalID');
		
		$uprocessed = '';
		if(empty($ada)) 
		{	$unprocessed .= $w['courseID'].' + '.$_TahunID.'<br>';
			$nx++;
		}
		else
		{	$n++;
			$s1 = "update $NamaDatabaseBaru.jadwal
					set JamMulai = '$JM',
						JamSelesai = '$JS',
						TanggalMulai = '$TM',
						TanggalSelesai = '$TS',
						KuliahTanggal = '$TM',
						HariID='$w[jadwalMKHariID]',
						RuangID='$w[ruanganID]',
						ProgramID='$_ProgramID',
						AdaResponsi='$_AdaResponsi',
						AdaTutorial='$_AdaTutorial',
						
					where MKKode='$w[courseID]' and TahunID='$_TahunID'";
		}
	}
	if(!empty($unprocessed)) echo "<b>Jadwal YAND TIDAK DITEMUKAN</b>:<br>".$unprocessed;
	echo "<li>Updating Jadwal: $n records berhasil
			<br> $nx records gagal</li>";
}

function fnUpdateLoginDosen($NamaDatabaseLama, $NamaDatabaseBaru, $KodeIDBaru)
{	//$s = "update $NamaDatabaseBaru.dosen set Login=NIDN";
	//$r = _query($s);
	$s = "select * from $NamaDatabaseBaru.dosen order by Nama";
	$r = _query($s);
	$n = 0;
	while($w = _fetch_array($r))
	{	$n++;
		$indexLastName = strrpos($w['Nama'], ' ');
		$LastName = substr($w['Nama'], $indexLastName);
		//echo "$n. $LastName<br>";
		$indexFirstName = strpos($w['Nama'], ' ');
		if($indexFirstName == 0) $FirstName = substr($w['Nama'], 0);
		else $FirstName = substr($w['Nama'], 0, $indexFirstName);
		//echo "$n. $FirstName<br>";
		
		
		$_Login = '';
		$_Login = trim(substr($FirstName, 0, 1)).trim(substr($LastName, 0, 8));
		//echo "$n. ".$_Login."</br>";
		$cek = GetaField("$NamaDatabaseBaru.dosen", 'Login', $_Login, 'Login');
		$count = 1;
		if(!empty($cek))
		{	while(!empty($cek))
			{	$count++;
				echo strlen($_Login).'<br>';
				$_tempLogin = (strlen(trim($_Login)) >= 8)? substr($_Login, 0, 7).$count : trim($_Login).$count;
				
				$cek = GetaField("$NamaDatabaseBaru.dosen", 'Login', $_tempLogin, 'Login');
				
				if(empty($cek))
				{	$_Login = trim($_tempLogin);
					break;
				}
			}
		}
	
		$s1 = "update $NamaDatabaseBaru.dosen 
				set Login='$_Login'
				where NIDN='$w[NIDN]'";
		$r1 = _query($s1);
		
		echo "Updating Login... ".($count-1)."    $w[Nama]   ----->   $_Login<br>";
	}
	
}

function fnMkTime()
{	$theint = 1111727400;
    //$theint = mktime(10, 20, 30, 4, -31, 2008);
	echo "The INT is: $theint";
	$thedate2 = getDate($theint);
	$thedate = $thedate2['year'].'-'.$thedate2['mon'].'-'.$thedate2['mday'].' '.$thedate2['hours'].':'.$thedate2['minutes'].':'.$thedate2['seconds'];
	echo "The DATE is: $thedate";
}

function HitungUlangBIPOTMhsw($MhswID, $TahunID) {
  // Hitung Total BIPOT & Pembayaran
  $biaya = GetaField("bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID = b2.BIPOT2ID",
      "bm.PMBMhswID = 1 and bm.KodeID = 'KASIH'
      and bm.NA = 'N'
      and bm.TrxID = 1
      and bm.TahunID = '$TahunID' and bm.MhswID", $MhswID,
      "sum(bm.Jumlah * bm.Besar)")+0;
  $potongan = GetaField("bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID = b2.BIPOT2ID",
      "bm.PMBMhswID = 1 and bm.KodeID = 'KASIH'
      and bm.NA = 'N'
      and bm.TrxID = -1
      and bm.TahunID = '$TahunID' and bm.MhswID", $MhswID,
      "sum(bm.Jumlah * bm.Besar)")+0;
  $bayar = GetaField('bayarmhsw',
      "PMBMhswID = 1 and KodeID = 'KASIH'
      and NA = 'N'
      and TrxID = 1
      and TahunID = '$TahunID' and MhswID", $MhswID,
      "sum(Jumlah)")+0;
  $tarik = GetaField('bayarmhsw',
      "PMBMhswID = 1 and KodeID = 'KASIH'
      and NA = 'N'
      and TrxID = -1
      and TahunID = '$TahunID' and MhswID", $MhswID,
      "sum(Jumlah)")+0;
  // Update data PMB
  $s = "update khs
    set Biaya = $biaya, Potongan = $potongan,
        Bayar = $bayar, Tarik = $tarik
    where KodeID = 'KASIH'
      and MhswID = '$MhswID' 
      and TahunID = '$TahunID'
    limit 1";
  $r = _query($s);
 
  //echo "BIAYA: $biaya, BAYAR: $bayar, TARIK: $tarik, POTONGAN: $potongan<br>";
  $jml = $biaya - $bayar + $tarik - $potongan;
  return $jml;
}
?>
