<?php

session_start();

include_once "../sisfokampus1.php";
include_once "../jur/krs.lib.php";

HeaderSisfoKampus("KRS Mahasiswa", 1);

// *** Parameters ***

$mhswid = GetSetVar('mhswid');
//if ($_SESSION['_LevelID']==120) die(ErrorMsg("Tolak", "Mahasiswa selain BP 2014  bisa mengisi KRS."));

$khsid = GetSetVar('khsid');
$_krsKelasID = GetSetVar('_krsKelasID');
$_krsSemester  = GetSetVar('_krsSemester');
$khs = GetFields('khs', "KHSID", $khsid, '*');
if ($_SESSION['_LevelID'] == '120') {
	$sudahVerifikasiTagihan = GetaField('khs', "KHSID", $khsid, 'KonfirmasiAktif');
	
		if ($khs['SetujuPA']=='Y') {
			die(errormsg('Tidak bisa isi KRS','KRS Anda sudah disetujui. Tidak bisa merubah KRS.'));
		}
	$cekAkses = GetaField('khs', "MhswID='$mhswid' AND KHSID", $khsid, 'MhswID');
		if (!$cekAkses) die(errormsg('Bukan Anda','Anda mencoba mengakses akun yang lain'));
}
// *** Main ***
TampilkanJudul("Daftar Matakuliah Yang Ditawarkan");
$gos = (empty($_REQUEST['gos']))? 'DftrJadwal' : $_REQUEST['gos'];
$gos($mhswid, $khsid);


// *** Functions ***
function TampilkanFilterProgram($khs) {
  $_SESSION['_krsProgramID'] = $khs['ProgramID'];
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_krsProgramID, "KodeID='".KodeID."'", 'ProgramID');
  $thn = GetaField('mhsw',"MhswID",$khs[MhswID],'left(TahunID,4)');
  // filter kelas
  $s = "Select DISTINCT(k.KelasID),k.Nama from kelas k, jadwal j where k.KelasID=j.NamaKelas AND j.ProdiID='$khs[ProdiID]' AND j.ProgramID='$khs[ProgramID]' AND j.TahunID='$khs[TahunID]' order by k.Nama";
  $r = _query($s);
  $optkelas = "<option value=''></option>";
  while ($w = _fetch_array($r)) {
  if ($_SESSION['_krsKelasID']==$w['KelasID']) {
  $optkelas .= "<option value='$w[KelasID]' Selected>$w[Nama]</option>";
  }
  else $optkelas .= "<option value='$w[KelasID]'>$w[Nama]</option>";
  }
  //filter Semester
    $s = "Select DISTINCT(m.Sesi) from mk m, jadwal j where m.MKID=j.MKID AND j.ProdiID='$khs[ProdiID]' AND j.ProgramID='$khs[ProgramID]' AND j.TahunID='$khs[TahunID]' order by m.Sesi";
  $r = _query($s);
  $optSesi = "<option value=''></option>";
  while ($w = _fetch_array($r)) {
  if ($_SESSION['_krsSemester']==$w['Sesi']) {
  $optSesi .= "<option value='$w[Sesi]' Selected>$w[Sesi]</option>";
  }
  else $optSesi .= "<option value='$w[Sesi]'>$w[Sesi]</option>";
  }
  echo "<table class=bsc cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].ambil.php' method=POST name='frmFilterProgram'>
  <input type=hidden name='gos' value='' />
  <input type=hidden name='mhswid' value='$khs[MhswID]' />
  <input type=hidden name='khsid' value='$khs[KHSID]' />
  
  <tr><td class=inp width=10>Filter:</td>
      <td class=ul1 nowrap>
      Program Pendidikan:
      <input type=text name='_prog' value='$_SESSION[_krsProgramID]' disabled size=3>
	  <input type=hidden name='_krsProgramID' value='$_SESSION[_krsProgramID]'> &nbsp;
      Kelas: 
      <select Name='_krsKelasID' onChange='this.form.submit()'>$optkelas</select>
      Semester:
      <Select name='_krsSemester' onChange='this.form.submit()'>$optSesi</select>
      <input type=submit name='Filter' value='Filter' />
      <input type=button name='Tutup' value='Tutup' onClick='window.close()' />
      </td>
      </tr>
  
  </form>
  </table>";
}
function TampilkanWarning($psn) {
  echo "<table class=box cellspacing=1 width=100%>
  <tr><th class=wrn>$psn</th></tr>
  </table>";
}
function AmbilDaftarKRS($mhswid, $khsid) {
  $s = "select JadwalID
    from krs
    where KHSID = '$khsid'
    order by JadwalID";
  $r = _query($s);
  $j = array();
  while ($w = _fetch_array($r)) {
    $j[] = $w['JadwalID'];
  }
  $jid = implode(',', $j);
  $jid = (empty($jid))? '' : "and not (j.JadwalID in ($jid))";
  return $jid;
}
function AmbilJenisJadwal()
{ $s = "select * from jenisjadwal where Tambahan='N' and NA='N'";
  $r = _query($s);
  $jj = array();
  while($w = _fetch_array($r))
  {	$jj[] = "'".$w['JenisJadwalID']."'";
  }
  $jjid = implode(',', $jj);
  $jjid = (empty($jjid))? '' : "and j.JenisJadwalID in ($jjid)";
  return $jjid;
}
function DftrJadwal($mhswid, $khsid) {
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  TampilkanFilterProgram($khs);
  PilihLabKRSScript();
  
  // filtering the listing
  $whr_prg = ($_SESSION['_krsProgramID'] == '')? '' : "and j.ProgramID = '$_SESSION[_krsProgramID]' ";
  $whr_kls = ($_SESSION['_krsKelasID'] == '')? '' : "and j.NamaKelas like '$_SESSION[_krsKelasID]%' ";
  $whr_smt = ($_SESSION['_krsSemester'] == '')?  '' : "and mk.Sesi = '$_SESSION[_krsSemester]' ";
  $whr_jenisjadwal = AmbilJenisJadwal();
  $whr_krs = AmbilDaftarKRS($mhswid, $khsid);
  
  $s = "select j.JadwalID, j.MKID, j.MKKode, j.Nama, j.SKS, j.HariID, j.AdaResponsi,
      LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
      j.DosenID, concat(d.Gelar1, ' ',d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      j.RuangID, j.NamaKelas, j.ProgramID, j.JumlahMhsw, j.Kapasitas, kl.Nama as NamaKelas, mk.Sesi
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join mk on mk.MKID = j.MKID
      left outer join kelas kl on kl.KelasID = j.NamaKelas
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$khs[TahunID]'
      and j.ProdiID = '$khs[ProdiID]'
      and j.NA = 'N'
      $whr_prg
      $whr_krs
      $whr_kls
      $whr_smt
	  $whr_jenisjadwal
    
    order by j.HariID, j.JamMulai, j.NamaKelas";
  $r = _query($s); $n = 0;
  // Jika tidak ada yg ditawarkan:
  if (_num_rows($r) == 0) die(TampilkanWarning("Tidak ada matakuliah yang dijadwalkan.
    <hr size=1 color=white />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Tampilkan
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<form action='../$_SESSION[mnux].ambil.php' method=POST>
    <input type=hidden name='gos' value='Ambil' />
    <input type=hidden name='mhswid' value='$mhswid' />
    <input type=hidden name='khsid' value='$khsid' />";
  $hdr = "<tr>
    <th class=ttl colspan=2>Ambil</th>
    <th class=ttl width=80>Kode <sup title='Semester'>Smt</sup></th>
    <th class=ttl>Matakuliah <sup title='SKS MK'>SKS</sup></th>
    <th class=ttl width=200>Dosen</th>
	<th class=ttl width=80>Kelas <sup>Program</sup></th>
    <th class=ttl width=70>Jam<br />Kuliah</th>
	<th class=ttl width=80>Ruang</th>
	<th class=ttl width=50>Jmlh.<br>Siswa</th>
    <th class=ttl width=50>Kap.</th>
	</tr>";

  $hr = -32;
  $btn = "<input type=submit name='Simpan' value='Ambil Yg Dicentang' />
    <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />";
  while ($w = _fetch_array($r)) {
  	$mkMhsw = GetFields('krs', "MhswID='$khs[MhswID]' and MKKode='$w[MKKode]' and KodeID", KodeID, 'MKKode, MAX(BobotNilai), GradeNilai, TahunID');
  	$sdhpernah = (empty($mkMhsw['MKKode']) ? "":"<br><font color=red>Pernah diambil $mkMhsw[TahunID], nilai: ".$mkMhsw['GradeNilai']."</font>");
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      $_hr = GetaField('hari', 'HariID', $hr, 'Nama');
      $btn1 = ($hr > 1)? $btn : '';
      echo "<tr>
        <td class=ul1 colspan=3>
        <b>$_hr</b> <sup>$hr</sup>
        </td>
        <td class=ul1 colspan=5>$btn1</td>
        </tr>";
      echo $hdr;
    }
    $n++;
	$checkboxjadwal = ($w['JumlahMhsw'] < $w['Kapasitas'])?
		"<input type=checkbox id='JdwlRes$w[JadwalID]' name='jid[]' value='$w[JadwalID]' onChange=\"ChooseJadwal('$w[JadwalID]')\"/>" :
		"<img src='../img/N.gif' title='Kapasitas lokal sudah penuh'>";
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul1 width=5 align=center>
        $checkboxjadwal
        </td>
      
      <td class=ul1>$w[MKKode]<sup>$w[Sesi]</sup></td>
      <td class=ul1>$w[Nama] <sup>$w[SKS]</sup>$sdhpernah</td>
      <td class=ul1>$w[DSN]&nbsp;</td>
	  <td class=ul1>$w[NamaKelas] <sup>$w[ProgramID]</sup></td>
	  <td class=ul1><sup>$w[JM]</sup>&minus;<sub>$w[JS]</sub></td>
	  <td class=ul1 align=center>$w[RuangID]&nbsp;</td>
	  <td class=ul1 align=right>$w[JumlahMhsw]&nbsp;</td>
	  <td class=ul1 align=right>$w[Kapasitas]&nbsp;</td>
	  </tr>";
	
	if($w['AdaResponsi'] == 'Y')
	{	$s1 = "select jr.JadwalID, jr.JadwalRefID, LEFT(jr.JamMulai, 5) as JM, LEFT(jr.JamSelesai, 5) as JS, 
					jr.RuangID, jr.JumlahMhsw, jr.Kapasitas, h.Nama as _NamaHari, jr.JenisJadwalID, jj.Nama as _NamaJenisJadwal
				from jadwal jr left outer join hari h on jr.HariID=h.HariID
								left outer join jenisjadwal jj on jj.JenisJadwalID=jr.JenisJadwalID
				where jr.JadwalRefID='$w[JadwalID]' and jr.JumlahMhsw < jr.Kapasitas and jr.KodeID='".KodeID."'
				order by jj.JenisJadwalID, jr.HariID, jr.JamMulai, jr.JamSelesai";
		$r1 = _query($s1);
		$totallab = _num_rows($r1);
		if($totallab == 0)
		{	echo "<tr
					  <td></td>
					  <td class=inp>>></td>
					  <td class=nac colspan=6 align=center><b>Belum ada jadwal Lab yang dibuat</b></td>
					  </tr>";
		}
		else
		{	$nx = 0; // Counting the number of each type of extra 
			$jx = 'K'; // Storing the last type assessed
			$typex = 0; // Counting the number of types of extra
			$n1 = 0; 
			while($w1 = _fetch_array($r1))
			{	if($jx != $w1['JenisJadwalID']) 
				{	if($jx != 'K') echo "<input type=hidden id='JdwlResCount$w[JadwalID]of$jx' name='JdwlResCount$w[JadwalID]of$jx' value='$nx'>";
					$nx = 0;
					$typex++;
					echo "<input type=hidden id='JdwlResType$w[JadwalID]of$typex' name='JdwlResType$w[JadwalID]of$typex' value='$w1[JenisJadwalID]'>";
					$jx = $w1['JenisJadwalID'];
				}
				$nx++;
				$n1++;
				$class='cnaY';
				echo "<tr>
				  <td></td>
				  <td class=inp>>></td>
				  <td class=$class width=5 align=right>
					<input type=checkbox id='JdwlRes$w[JadwalID]of$w1[JenisJadwalID]of$nx' name='jresid[]' value='$w[JadwalID]~$w1[JadwalID]' onChange=\"ChooseLab('$w[JadwalID]', '$w1[JenisJadwalID]', '$nx')\"/>
					</td>
				  <td class=$class align=left><b>$w1[_NamaJenisJadwal] #$nx</b></td>
				  <td class=$class colspan=2><b>Hari:</b> $w1[_NamaHari]</td>
				  <td class=$class><sup>$w1[JM]</sup>&minus;<sub>$w1[JS]</sub></td>
				  <td class=$class align=center>$w1[RuangID]&nbsp;</td>
				  <td class=$class align=right>$w1[JumlahMhsw]&nbsp;</td>
				  <td class=$class align=right>$w1[Kapasitas]&nbsp;</td>
				  </tr>";
			}
			echo "<input type=hidden id='JdwlResCount$w[JadwalID]of$jx' name='JdwlResCount$w[JadwalID]of$jx' value='$nx'>";
			echo "<input type=hidden id='JdwlResCountType$w[JadwalID]' name='JdwlResCountType$w[JadwalID]' value='$typex'>";
		}
	}
  }
  echo "<tr><td class=ul1 colspan=3>&nbsp</td><td class=ul1 colspan=5>$btn</td></tr>";
  echo "</form></table>";
  echo "<p align=center>Mata kuliah yang sudah diambil tidak ditampilkan lagi di sini.</p>";
}

function Ambil($mhswid, $khsid) {
  $jid = array();
  $jid = $_REQUEST['jid'];
  $jresid = $_REQUEST['jresid'];
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  $cekprasyarat = ($khs['Ignored']=='Y')? "N" : GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $khs['ProdiID'], 'CekPrasyarat');
  if (empty($jid)) {
    echo ErrorMsg('Error',
      "Anda belum mencentang matakuliah yang akan diambil.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Kembali' value='Kembali'
        onClick=\"location='../$_SESSION[mnux].ambil.php?mhswid=$mhswid&khsid=$khsid'\" />
        <input type=button name='Tutup' value='Tutup'
        onClick=\"window.close()\" />");
  }
  else {
    TutupScript($mhswid, $khsid);

    // Buat array pesan	
    $arrPesan = array();
    $_psn = '';
    foreach ($jid as $j) {
      $oke = true;
      $jdwl = GetFields('jadwal', 'JadwalID', $j, '*');
      // Cek prasyarat
      if ($cekprasyarat == 'Y') $oke = CheckPrasyarat($khs, $jdwl, $_psn);
      // Cek apakah ada bentrok?
      if ($oke) $oke = CheckKRSMhsw($khs, $jdwl, $_psn);
	  /*if($jdwl['AdaResponsi'] == 'Y')
	  {	$jdwlresponsi = GetFields("jadwal jr left outer join jadwal j on j.JadwalRefID=jr.JadwalID and j.KodeID='".KodeID."'" , 'JadwalID', $arrResponsi[$j], 'jr.*, j.Nama, j.MKKode, j.SKS');
		if ($oke) $oke = CheckResponsiMhsw($khs, $jdwlresponsi, $_psn); 
	  }*/
      if ($oke) $oke = CheckKapasitas($jdwl, $_psn);
      if ($oke) SimpanKRSMhsw($khs, $jdwl);
      else $arrPesan[] = $_psn;
    }
    HitungUlangKRS($khsid);
    echo "<script>
      opener.location='../index.php?mnux=$_SESSION[mnux]&gos=&mhswid=$mhswid&khsid=$khsid';
      </script>";

    // Jika ada Error, tampilkan pesan errornya
    if (!empty($arrPesan)) {
     $p = implode(' ', $arrPesan);
     echo ErrorMsg('Error',
       "Ada KRS yang gagal diambil. Berikut adalah pesan kesalahannya:
       <ol>$p</ol>
       <hr size=1 color=silver />
       Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"javascript:ttutup()\" />
         <input type=button name='Kembali' value='Kembali' onClick=\"location='../$_SESSION[mnux].ambil.php'\" />");
    }
    else
	{	
		// Sampai sini, penyimpanan data krs telah selesai. 
	    //echo "KRS untuk Jadwal Kuliah Utama Telah berhasil disimpan.<br>";
		
			// Sekarang, cek dan simpan data kelas tambahan (responsi/lab/tutorial)
			// Buat array yang memuat semua jadwal responsi
			$arrPesan = array();
			// Bila ada jadwal kelas tambahan yang dipilih....
			if(!empty($jresid))
			{	
				foreach($jresid as $j)
				{	$a = explode('~', $j);
					
					$oke = true;
					  $jdwl = GetFields('jadwal', 'JadwalID', $a[1], '*');
					  // Tidak usah Cek prasyarat karena sudah dicek sebelumnya
					  // Cek apakah ada bentrok?
					  if ($oke) $oke = CheckKRSMhsw($khs, $jdwl, $_psn);
					  if ($oke) $oke = CheckKapasitas($jdwl, $_psn);
					  if ($oke) SimpanKRSMhsw($khs, $jdwl);
					  else $arrPesan[] = $_psn;
				}
			}
			
			if (!empty($arrPesan)) {
			 $p = implode(' ', $arrPesan);
			 echo ErrorMsg('Error',
			   "Ada KRS Tambahan yang gagal diambil. Berikut adalah pesan kesalahannya:
			   <ol>$p</ol>
			   <hr size=1 color=silver />
			   Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"javascript:ttutup()\" />
				 <input type=button name='Kembali' value='Kembali' onClick=\"location='../$_SESSION[mnux].ambil.php'\" />");
			}
		echo "<script>ttutup()</script>";
	}
  }
}
function CheckKapasitas($jdwl, &$_psn) {
  $hsl = true;
  if ($jdwl['Kapasitas'] > 0) {
    if ($jdwl['Kapasitas'] - $jdwl['JumlahMhsw'] <= 0) {
      $_psn .= "<li>Kapasitas kelas matakuliah ini terlampaui.
        Anda tidak bisa masuk ke kelas ini.<br />
        <sup>Saran: Ambillah kelas paralelnya. Hubungi staf Prodi jika semua kelas penuh.</sup>
        <table class=bsc cellspacing=1 width=100%>
        <tr><td class=inp width=80>Kapasitas:</td>
            <td class=ul1>$jdwl[Kapasitas] orang</td>
            </tr>
        <tr><td class=inp>Terisi:</td>
            <td class=ul1>$jdwl[JumlahMhsw] orang</td>
            </tr>
        </table>
        </li>";
      $hsl = false;
    }
  }
  return $hsl;
}
function CheckPrasyarat($khs, $jdwl, &$_psn) {
  $s = "select mkpra.*, mk.Nama, mk.SKS
    from mkpra
      left outer join mk on mk.MKID = mkpra.PraID
    where mkpra.MKID = '$jdwl[MKID]' ";
  $r = _query($s);
  $hsl = true;
  while ($w = _fetch_array($r)) {
    $sdh = GetFields('krs k', 
      "k.KodeID='".KodeID."' and k.NA='N' and k.MhswID='$khs[MhswID]' 
			and k.BobotNilai=(select max(k2.BobotNilai) from krs k2 where k2.KodeID='".KodeID."' and k2.NA='N' and k2.MhswID='$khs[MhswID]' and k2.MKKode='$w[MKPra]' group by k2.MKKode) and k.MKKode", 
		$w['MKPra'], 'k.*');
	
	$alternatif = GetFields('krs k', 
      "k.KodeID='".KodeID."' and k.NA='N' AND LOCATE(concat('.',k.MKKode,'.'),'$w[MKAlternatif]') 
			and k.BobotNilai=(select max(k2.BobotNilai) from krs k2 where k2.KodeID='".KodeID."' and k2.NA='N' and k2.MhswID='$khs[MhswID]' and LOCATE(concat('.',k2.MKKode,'.'),'$w[MKAlternatif]') group by k2.MhswID) and k.MhswID", $khs['MhswID'], 'k.*');
			

    // Jika belum diambil
    if (empty($sdh) && empty($alternatif)) {
      $_psn .= "<li>Anda belum mengambil mata kuliah prasyaratnya.<br />
        <sup>Saran: Ambillah dulu matakuliah prasyaratnya.</sup><br />
        Berikut adalah matakuliah prasayaratnya:
        <table class=bsc cellspacing=1 width=100%>
        <tr><td class=inp>MK yg Gagal:</td>
            <td class=ul1>$jdwl[MKKode] &minus; $jdwl[Nama] <sup>($jdwl[SKS] SKS)</sup></td>
            </tr>
        <tr><td class=inp>MK Prasyarat:</td>
            <td class=ul1>$w[MKPra] &minus; $w[Nama] <sup>($w[SKS] SKS)</sup></td>
            </tr>
		<tr><td class=inp>MK Alternatif:</td>
            <td class=ul1>$w[MKAlternatif] &minus; $w[Nama] <sup>($w[SKS] SKS)</sup></td>
            </tr>
        </table>
        </li>";
      $hsl = false;
    }
    // Jika sudah diambil, cek nilainya
    else {
		if ($alternatif['BobotNilai'] < $w['Bobot'] && $sdh['BobotNilai'] < $w['Bobot']) {
				$_psn .= "<li>Nilai MK prasyarat Anda tidak memadai.<br />
				<sup>Saran: Penuhilah prasyarat MK ini.</sup><br />
				Berikut adalah prasyarat MK ini:
				<table class=bsc cellspacing=1 width=100%>
				<tr><td class=inp>MK yg Gagal:</td>
					<td class=ul1>$jdwl[MKKode] &minus; $jdwl[Nama] <sup>($jdwl[SKS] SKS)</sup></td>
					</tr>
				<tr><td class=inp>Nilai Anda:</td>
					<td class=ul1>$alternatif[GradeNilai] ($alternatif[BobotNilai])</td>
					</tr>
				<tr><td class=inp>MK Prasyarat:</td>
					<td class=ul1>$w[MKPra] &minus; $w[Nama] <sup>($w[SKS] SKS)</sup></td>
					</tr>
				<tr><td class=inp>Nilai Minimal:</td>
					<td class=ul1>$w[Nilai] ($w[Bobot])</td>
					</tr>
				</table>
				</li>";        
				$hsl = false;
			  }
			  
		}
    
  }
  return $hsl;
}
function CheckKRSMhsw($khs, $jdwl, &$_psn) {
  
  // Apakah bentrok jadwalnya?
  $b = GetFields("krs k
    left outer join jadwal j on j.JadwalID = k.JadwalID
	left outer join hari h on j.HariID = h.HariID",
    "k.TahunID = '$khs[TahunID]'
    and j.HariID = '$jdwl[HariID]'
     and (('$jdwl[JamMulai]:00' <= j.JamMulai and j.JamSelesai <= '$jdwl[JamSelesai]:00')
	or  ('$jdwl[JamMulai]:00' >= j.JamMulai and j.JamSelesai >= '$jdwl[JamMulai]:00'))
	and k.NA = 'N' and k.StatusKRSID='A' and k.MhswID",$khs['MhswID'],
    "k.*, j.MKID, j.MKKode, j.Nama, j.RuangID,
    j.NamaKelas, j.ProgramID,
    LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
    j.SKS, j.AdaResponsi,
    h.Nama as HR");
  $_SKS = $jdwl['SKS'];
  $jumambil=$khs['SKS']+$_SKS;

  if( $jumambil > $khs['MaxSKS'])
  {
  echo"
  <script>
  alert('Batas Pengambilan Sks Anda Tidak Mencukupi')
  top.location='krs.ambil.php?mhswid=$_REQUEST[mhswid]&khsid=$_REQUEST[khsid]';
  </script>";
  die(errorMsg("Error", "Batas Pengambilan KRS tidak mencukupi"));
  }
  else{
  	if (empty($b)) {
    return TRUE;
  }
  else {
    $responsistring = ($b['AdaResponsi'] == 'Y')? "<tr><td class=inp>Jam Lab:</td><td class=ul1>$b[_JamMulaiLab] &minus; $b[_JamSelesaiLab]</td></tr>" : "";	
	$cekjenisjadwal = GetaField('jenisjadwal', "JenisJadwalID='$jdwl[JenisJadwalID]' and NA='N' and Tambahan", 'Y', 'Nama');
	if(!empty($cekjenisjadwal)) $TandaResponsi = "<b>( ".strtoupper($cekjenisjadwal)." )</b>";
	$_psn .= "<li>Bentrok dengan matakuliah yang telah Anda ambil.<br />
      <sup>Saran: Ambillah MK paralelnya yang tidak bentrok dgn jadwal Anda.</sup><br />
      Berikut adalah MK yg bentrok:
      <table class=bsc cellspacing=1 width=100%>
      <tr><td class=ul1>MK Gagal:</td>
          <td class=ul1>$jdwl[MKKode] &minus; $jdwl[Nama] <sup>($jdwl[SKS] SKS) $TandaResponsi </sup></td>
          </tr>
      <tr><td class=ul1><sup>&#8883; MK Bentrok:</sup></td>
          <td class=ul1>$b[MKKode] &minus; $b[Nama] <sup>($b[SKS] SKS)</sup></td>
          </tr>
      <tr><td class=ul1><sup>&#8883; Jam:</sup></td>
          <td class=ul1>$b[HR], $b[JM]&minus;$b[JS]</td>
          </tr>
      $responsistring
	  <tr><td class=ul1><sup>&#8883; Kelas:</sup></td>
          <td class=ul1>$b[KelasID] <sup>($b[ProgramID])</sup></td>
          </tr>
      <tr><td class=ul1><sup>&#8883; Ruang:</sup></td>
          <td class=ul1>$b[RuangID]&nbsp;$b[ProgramID]</td>
          </tr>
      </table></li>";
    return FALSE;
  }
}
}
function SimpanKRSMhsw($khs, $jdwl) {
  //$cek = GetaField('jenisjadwal', "JenisJadwalID", $jdwl['JenisJadwalID'], 'Tambahan');
  //if($cek == 'Y') $_SKS = 0;
  // else 
  $_SKS = $jdwl['SKS'];
  $jumambil=$khs[SKS]+$_SKS;
  $cek = GetaField('krs', "MhswID='$khs[MhswID]' and TahunID='$jdwl[TahunID]' and MKID",$jdwl['MKID'],"MKID");
  if($jumambil > $khs[MaxSKS] || !empty($cek))
  {
  echo"
  <script>
  alert('Batas Pengambilan Sks Anda Tidak Mencukupi atau matakuliah sudah ada di dalam KRS Anda!')
  </script>";
  }else{
  $s = "insert into krs
    (KodeID, KHSID, MhswID, TahunID, JadwalID, 
    MKID, MKKode, Nama, SKS,
    LoginEdit, TanggalEdit)
    values
    ('".KodeID."', '$khs[KHSID]', '$khs[MhswID]', '$khs[TahunID]', '$jdwl[JadwalID]',
    '$jdwl[MKID]', '$jdwl[MKKode]', '$jdwl[Nama]', '$_SKS',
    '$_SESSION[_Login]', now())";
  $r = _query($s);
  }
  HitungPeserta($jdwl['JadwalID']);
}
function PilihLabKRSScript()
{	echo <<< SCR
		<script>
			function ChooseLab(mkid, type, target)
			{	count = document.getElementById('JdwlResCount'+mkid+'of'+type).value;
				if(document.getElementById('JdwlRes'+mkid).checked)
				{
					for(i = 1; i <= count; i++)
					{	document.getElementById('JdwlRes'+mkid+'of'+type+'of'+i).checked = false;
					}
					document.getElementById('JdwlRes'+mkid+'of'+type+'of'+target).checked = true;
				}
				else
				{	for(i = 1; i <= count; i++)
					{	document.getElementById('JdwlRes'+mkid+'of'+type+'of'+i).checked = false;
					}
				}
			}
			function ChooseJadwal(mkid)
			{	countType = document.getElementById('JdwlResCountType'+mkid).value;
				for(t = 1; t <= countType; t++)
				{	oneType = document.getElementById('JdwlResType'+mkid+'of'+t).value;
					
					count = document.getElementById('JdwlResCount'+mkid+'of'+oneType).value;
					if(document.getElementById('JdwlRes'+mkid).checked)
					{	if(count > 0)
						{	
							for(i = 1; i <= count; i++)
							{	document.getElementById('JdwlRes'+mkid+'of'+oneType+'of'+i).checked = false;	
							}
						}
						document.getElementById('JdwlRes'+mkid+'of'+oneType+'of'+1).checked = true;
					}
					else
					{	for(i = 1; i <= count; i++)
							document.getElementById('JdwlRes'+mkid+'of'+oneType+'of'+i).checked = false;	
					}
				}
			}
		</script>
SCR;
}
function TutupScript($mhswid, $khsid) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=&mhswid=$mhswid&khsid=$khsid';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}

?>