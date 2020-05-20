<?php

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');
$HariID = GetSetVar('HariID');
date_default_timezone_set('Asia/Jakarta');

// *** Main ***
?>
  <script type="text/javascript" language="javascript" src="clock.js"></script>
<?php
TampilkanJudul("Presensi Dosen & Mahasiswa");
$gos = (empty($_REQUEST['gos']))? 'DftrJadwal' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeaderPresensi() {
  //$optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['ProdiID'], "KodeID='".KodeID."'", 'ProdiID');
  // Edit: Ilham
  $s = "select DISTINCT(TahunID) from tahun where KodeID='".KodeID."' order by TahunID DESC";
  $r = _query($s);
  $opttahun = "<option value=''></option>";
  while($w = _fetch_array($r)) {  
	  $ck = ($w['TahunID'] == $_SESSION['TahunID'])? "selected" : '';
      $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
  }
  $optprodi = ($_SESSION['_LevelID'] == 100)? 
     GetOption4("prodi", "ProdiID", "concat(ProdiID, ' - ', Nama) as NM", "NM",  $_SESSION['ProdiID'], '.') : 
	 GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);

// =============================================================


//================================
 
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['ProgramID'], "KodeID='".KodeID."'", 'ProgramID');
  $opthari = GetOption2('hari', 'Nama', 'HariID', $_SESSION['HariID'], '', 'HariID');
  $buttons = ($_SESSION['_LevelID'] == 100)? "<input type=button name='CetakDetail' value='Cetak Detail Presensi' onClick=\"javascript:CetakDetail()\" /> <sup>updated: 03/02/2016</sup>" : 
	 "<input type=button name='CetakRekap' value='Cetak Rekap' onClick='javascript:CetakRekap()' />
      <input type=button name='CetakDetail' value='Cetak Detail Presensi' onClick=\"javascript:CetakDetail()\" />
      <input type=button name='CetakPresMhsw' value='Cetak Presensi Mhsw' onClick=\"javascript:CetakDetailMhsw()\" />"; 
  echo "<table class=box cellspacing=0 align=center width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <tr><td class=wrn width=2 rowspan=4></td>
      <td class=inp>Thn Akd.:</td>
      <td class=ul1><select name='TahunID'/>$opttahun</select></td>
      <td class=inp>Program Studi:</td>
      <td class=ul1><select name='ProdiID'>$optprodi</select></td>
      </tr>
  <tr><td class=inp>Hari:</td>
      <td class=ul1><select name='HariID'>$opthari</select></td>
      <td class=inp>Prg Pendidikan:</td>
      <td class=ul1><select name='ProgramID'>$optprg</select>
        <input type=submit name='Tampilkan' value='Tampilkan Jadwal' align=right />
        </td>
      </tr>
  </form>
  <tr><td class=ul colspan=5>
      $buttons
      </td></tr>
  </table>";
echo <<<SCR
  <script>
  <!--
  function CetakRekap() {
    lnk = "$_SESSION[mnux].rekap.php";
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakDetail() {
    lnk = "$_SESSION[mnux].detail.php";
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakDetailMhsw() {
    lnk = "$_SESSION[mnux].mhsw.php";
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
SCR;
}
function DftrJadwal() {
  TampilkanHeaderPresensi();
  if (empty($_SESSION['TahunID']) || empty($_SESSION['ProdiID']))
    echo Konfirmasi("Tahun Akademik & Program Studi",
      "Masukkan Tahun Akademik & Program Studi terlebih dahulu untuk dapat menampilkan jadwal kuliah.");
  else DftrJadwal1();
}
function DftrJadwal1() {
  // Filtering
  if ($_SESSION['_LevelID']==100) $NIDN = GetaField('dosen', "Login",$_SESSION[_Login],"NIDN");
  $whr_hari = ($_SESSION['HariID'] == '')? '' : "and j.HariID = '$_SESSION[HariID]' ";
  $whr_prg  = ($_SESSION['ProgramID'] == '')? '' : "and j.ProgramID = '$_SESSION[ProgramID]' ";
  $whr_dosen = ($_SESSION['_LevelID'] == 100) ? " and j.DosenID = '$NIDN%' " : "";
if ($_SESSION['_LevelID']==100) {
	
    $s = "select j.*,
      left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
      concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      jj.Nama as _NamaJenisJadwal, jj.Tambahan,
	  mk.TugasAkhir, mk.PraktekKerja, k.Nama AS namaKelas
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
	  left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID
	  left outer join mk mk on mk.MKID=j.MKID and mk.KodeID='".KodeID."' 
	  LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    left outer join jadwaldosen jd on jd.JadwalID=j.JadwalID
    where j.TahunID = '$_SESSION[TahunID]'
      and j.ProdiID = '$_SESSION[ProdiID]'
      and j.KodeID = '".KodeID."'
	  and (j.DosenID = '$_SESSION[_Login]' or jd.DosenID = '$_SESSION[_Login]')
	  $whr_hari
      $whr_prg
      and j.NA = 'N'
      group by j.JadwalID
    order by j.HariID , j.JamMulai, j.JamSelesai";
	}
	else {
	 $s = "select j.*,
      left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
      concat(d.Gelar1, ' ', d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      jj.Nama as _NamaJenisJadwal, jj.Tambahan,
	  mk.TugasAkhir, mk.PraktekKerja, k.Nama AS namaKelas
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
	  left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID
	  left outer join mk mk on mk.MKID=j.MKID and mk.KodeID='".KodeID."' 
	  LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    where j.TahunID = '$_SESSION[TahunID]'
      and j.ProdiID = '$_SESSION[ProdiID]'
      and j.KodeID = '".KodeID."'
	  $whr_hari
      $whr_prg
	  $whr_dosen
      and j.NA = 'N'
    order by j.HariID , j.JamMulai, j.JamSelesai";
	}
	

  $r = _query($s);
  $n = 0; $_hr = 'lasdjfalsjh';
  echo "<table class=box cellspacing=1 align=center width=900>";
  $PrintDaftar = ($_SESSION['_LevelID'] == 100)? '' : 
	"<th class=ttl title='Daftar Presensi Dosen'>DPD</th>
	<th class=ttl title='Daftar Presensi Mahasiswa'>DPM</th>";
  $hdr = "<tr>
    <th class=ttl>#</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Mata Kuliah</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Mhsw</th>
	$PrintDaftar
    <th class=ttl colspan=2>Presensi</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    if ($_hr != $w['HariID']) {
      $_hr = $w['HariID'];
      $hari = GetaField('hari', 'HariID', $_hr, 'Nama');
      echo "<tr><td class=ul colspan=11><font size=+1>$hari</font> <sup>$_hr</sup></td></tr>";
      echo $hdr;
    }
    $n++;
    if ($w['Final'] == 'Y') {
      $c = 'class=nac';
      $edt = "<img src='img/lock.jpg' width=25 title='Sudah difinalisasi. Tidak dapat diubah.' />";
    }
    else {
    $c = 'class=ul';
		if ($_SESSION["_LevelID"]==1 || $_SESSION["_LevelID"]==20 || $_SESSION["_LevelID"]==100 || $_SESSION["_LevelID"]==41 || $_SESSION["_LevelID"]==110) {
      $edt = "<a href='#' onClick=\"location='?mnux=$_SESSION[mnux]&gos=Edit&JadwalID=$w[JadwalID]'\"><img src='img/edit.png' /></a>";
	  }
	  else { 
      $edt = "";
	  }
    }
	$PrintDaftar2 = ($_SESSION['_LevelID'] == 100)? '' : 
	    "<td $c align=center>
        <a href='#' onClick='javascript:CetakDAD($w[JadwalID], $w[SKS])' title='Daftar Presensi Dosen'><img src='img/printer2.gif' /></a>
        </td>
		<td $c align=center>
        <a href='#' onClick='javascript:CetakDHK($w[JadwalID], $w[SKS])' title='Daftar Presensi Mahasiswa'><img src='img/printer2.gif' /></a>
        </td>";
    $TagTambahan = ($w['Tambahan'] == 'Y')? "<b>( $w[_NamaJenisJadwal] )</b>" : "";
	
$jmlMhsw = GetaField ("khs h, bipotmhsw b, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID", "k.JadwalID = '$w[JadwalID]'
	AND h.MhswID=k.MhswID
	AND b.MhswID=k.MhswID
	AND b.TambahanNama like (concat('%',k.MKKode,'%'))
	AND b.Dibayar=(b.Jumlah*b.Besar)
	AND b.TahunID=k.TahunID
	AND h.TahunID=k.TahunID and k.KodeID",KodeID,'count(DISTINCT(k.MhswID))');
	 
	  	//Hitung semua mahasiswa yang mengambil matakuliah ini dan pembayarannya telah mencukupi untuk terdaftar di semester ini.
	
	  
	  //Hitung semua mahasiswa yang mengambil matakuliah ini
	  $s3= "select count(k.MhswID) as MKini from krs k, khs h where
			h.MhswID=k.MhswID And 
			h.TahunID=k.TahunID And 
			k.JadwalID=$w[JadwalID]";
	
	$r3 = _query($s3);
	  while ($w3 = _fetch_array($r3)) {
	  $jMhswKRS=$w3[MKini];
	  }
      $Kehadiran = GetaField('presensi','JadwalID',$w['JadwalID'],"max(Pertemuan)")+0;
    echo "<tr>
      <td class=inp width=15>$n</td>
      <td $c><sup>$w[_JM]</sup>&#8594;<sub>$w[_JS]</sub></td>
      <td $c>$w[MKKode] <sup>$w[Sesi]</sup></td>
      <td $c>$w[Nama] $TagTambahan</td>
      <td $c align=right>$w[SKS]</td>
      <td $c>$w[namaKelas] <sup>$w[ProgramID]</sup></td>
      <td $c>$w[DSN]</td>
      <td $c align=center><font size=4 color=#990000><strong>$jmlMhsw</strong></font>/$jMhswKRS</td>
	  $PrintDaftar2
      <td class=ul1 align=right>$Kehadiran<sub>&times;</sub></td>
      <td class=ul align=center>
        $edt
        </td>
      </tr>";
  }
  echo <<<ESD
  </table>
  <p></p>
  
  <script>
  function CetakDHK(JadwalID, SKS) {
    lnk = "$_SESSION[mnux].dhk.php?JadwalID="+JadwalID;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakDAD(JadwalID, SKS) {
    lnk = "$_SESSION[mnux].dad.php?JadwalID="+JadwalID;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
ESD;
}

function Edit() {
  $JadwalID = GetSetVar('JadwalID');
  $jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join hari hr on j.HariID = hr.HariID
    left outer join hari hruas on hruas.HariID = date_format(j.UASTanggal, '%w')
    left outer join hari hruts on hruts.HariID = date_format(j.UTSTanggal, '%w')
    left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID 
	LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
	", 
    "j.JadwalID", $JadwalID,
    "j.*, concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
    prd.Nama as _PRD, hr.Nama as _HR, hruas.Nama as _HRUAS,hruts.Nama as _HRUTS,j.HariID as _HariID,
    LEFT(j.JamMulai, 5) as _JM, LEFT(j.JamSelesai, 5) as _JS,
    LEFT(j.UASJamMulai, 5) as _JMUAS, LEFT(j.UASJamSelesai, 5) as _JSUAS,
    LEFT(j.UTSJamMulai, 5) as _JMUTS, LEFT(j.UTSJamSelesai, 5) as _JSUTS,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
	jj.Nama as _NamaJenisJadwal, jj.Tambahan, k.Nama AS namaKelas
    ");
  // Cek apakah jadwal valid?
  if (empty($jdwl)) 
    die(ErrorMsg('Error',
      "Jadwal tidak ditemukan.<br />
      Mungkin jadwal sudah dihapus.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" >"));
  // Cek apakah sudah di-finalisasi?
  if ($jdwl['Final'] == 'Y')
    die(ErrorMsg('Error',
      "Jadwal sudah difinalisasi.<br />
      Anda sudah tidak dapat mengubah data ini lagi.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" >"));
  // Jika sudah valid semua, maka tampilkan menu edit yg sebenarnya
 
 Edits($jdwl);
  
}
function Edits($jdwl) {
  PresensiScript();
  TampilkanHeader($jdwl);
  TampilkanPresensi($jdwl);
}
function TampilkanPresensi($jdwl) {
  if($_SESSION['_LevelID'] == 100) {
   $NIDN = GetaField('dosen', "Login",$_SESSION[_Login],"NIDN");
   $s = "Select j.DosenID from jadwal j
    left outer join jadwaldosen jd on jd.JadwalID=j.JadwalID
   where (j.DosenID = '$_SESSION[_Login]' or jd.DosenID = '$_SESSION[_Login]') and j.JadwalID='$jdwl[JadwalID]' group by j.JadwalID";
   $r = _query($s);
   $w = _fetch_array($r);
	if(empty($w[DosenID]))
	   die(ErrorMsg("Anda tidak berhak mengakses data presensi dari Mata Kuliah: <b>$jdwl[Nama], Hari: $jdwl[_HRUAS], Jam: $jdwl[_JM] - $jdwl[_JS]</b>. 
					<br>Bila anda seharusnya berhak mengakses data ini, harap menghubungi Ketua Prodi."));
  }
  $s = "select p.*,
    date_format(p.Tanggal, '%d-%m-%Y') as _Tanggal,
    date_format(p.Tanggal, '%w') as _Hari,
    d.Nama as DSN, d.Gelar,
    h.Nama as _HR,
    left(p.JamMulai, 5) as _JM, left(p.JamSelesai, 5) as _JS,
      (select sum(Nilai)
      from presensimhsw 
      where PresensiID=p.PresensiID) as JmlHadir
    from presensi p
      left outer join hari h on h.HariID = date_format(p.Tanggal, '%w')
      left outer join dosen d on d.Login = p.DosenID and d.KodeID = '".KodeID."'
    where p.JadwalID = '$jdwl[JadwalID]'
    order by p.Pertemuan";
  $r = _query($s);


  echo "<table class=box cellspacing=1 align=center width=800>";
  echo "<tr>
    <td class=ul1 colspan=7 width=800>";
    
 // ===========================================================================================
	// kode penguncian transaksi presensi dimulai disini (hanya untuk selain superuser)
 // ===========================================================================================
 if ($_SESSION[_LevelID] == 100) {   //=== penguncian untuk dosen dibuka sementara
	if ($_SESSION['_LevelID'] != 1 && $_SESSION['_LevelID'] != 110) {    
        $timezone = "Asia/Jakarta";
        //if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
         $hariini=date('w');
         $JadwalID = $jdwl[JadwalID];
         $J = GetFields('jadwal',"JadwalID",$JadwalID,"JamMulai,HariID");
         $sekarang = date("H:i");
        $selisih = _query("Select timediff('$sekarang','$J[JamMulai]') as Selisih, timediff('$sekarang','$J[JamMulai]')*1 as PM");
        
        while ($w0i = _fetch_array($selisih)) {
        $_selisih = $w0i['Selisih'];
        if ($w0i['PM'] < 0) $plusmin = -1;
        else $plusmin = 1;
        }
        
                // memisahkan jam menit dan detik
                // ---------------------------------------------------------
                 list($hours, $mins, $secs) = explode(':', $_selisih);
                 $frmSelisih = ($hours * 3600 ) + ($mins * 60 ) + $secs + 0;
                 $jam = (int)$hours;
                 $menit = (int)$mins;
                 $detik = (int)$secs;
                 // ---------------------------------------------------------
                 
// menentukan plus minus selisih
// ---------------------------------------------------------
 $selisihnya = $frmSelisih * $plusmin;
// ---------------------------------------------------------
 
                // 660 = 10 Menit
                // ---------------------------------------------------------
                // if (($selisihnya > -1800) && ($selisihnya < 1800) && ($J[HariID]==$hariini))
                if ($J[HariID]==$hariini)  {
                     echo "<input type=button name='TambahPresensi' value='Tambah Presensi' 
                      onClick=\"javascript:PrsnEdit(1, $jdwl[JadwalID], 0)\" />";
                }
                else {
                $Jam = str_replace('-','',$jam);
                if ($Jam == 0) $Tepatnya = "$mins";
                elseif ($mins == 0) $Tepatnya = "$Jam";
                else $Tepatnya = "$Jam jam $mins";
                // ---------------------------------------------------------

// buat pesan jika hari yang sama
// ---------------------------------------------------------                
if (($plusmin == -1) And ($J[HariID]==$hariini)) {
$Pesan = "( Waktu Perkuliahan akan dimulai $Tepatnya menit lagi )";
}
if (($plusmin == 1) And ($J[HariID]==$hariini))  { $Pesan = "( Waktu Perkuliahan sudah lewat $Tepatnya menit yang lalu )"; }
// ---------------------------------------------------------

// mulai beraksi
// ---------------------------------------------------------
echo "<table width=100% class=bsc bgcolor='#eee'>
<tr valign='bottom' style=' vertical-align:text-bottom'>
	<td><h2><font color='#990000'><blink>Presensi Dosen Mengajar Akan aktif sesuai jadwal.</blink></font> </h2> </td>
	<td align=right>
<p align='right' style=' background:#FFFFFF; border: #000000 thick 1px;border-radius:6; padding:3px; box-shadow:0 0 2px #888;-moz-box-shadow:0 0 2px #888;-webkit-box-shadow:0 0 2px #888; text-align:center; color:#006699; font-weight:bolder; font-size:28px' id='clock1'></p>
	</td>
  </tr>
<tr>
	<td colspan=2 bgcolor='#CCCCCC' width=100%>
	<font color='blue'><i>Range Waktu Presensi 30 Menit Sebelum dan 30 Menit Sesudah Jam Mulai Perkuliahan [Sesuai Setting Jadwal]</i><br /></font></td></tr>
</table>

<center><b>$Pesan</b></center><br />
<img src='img/gembok.jpg' title='akan aktif sesuai jadwal perkuliahan'>";
}
}
//===========================================================================================================================
// END
//===========================================================================================================================
else {
	 echo "<input type=button name='TambahPresensi' value='Tambah Presensi' 
      onClick=\"javascript:PrsnEdit(1, $jdwl[JadwalID], 0)\" />";
}
} /*filter dosen agar tidak bisa menambahkan pada jam mulai kuliah*/
else{
	// KHUSUS MK Praktikum (Labor) yang 1 SKS di Teknik. Dosen boleh memilih mengisi presensi atau tidak
	$MKPraktikum = GetFields('jadwal j left outer join mk m on m.MKID=j.MKID', "j.JadwalID", $jdwl['JadwalID'],"m.SKSPraktikum, m.SKSTatapMuka");
	if ($MKPraktikum['SKSPraktikum']==1 && $MKPraktikum['SKSTatapMuka']==0){
		echo "<input type=button name='TambahPresensi' value='Tambah Presensi' 
      onClick=\"javascript:PrsnEdit(1, $jdwl[JadwalID], 0)\" />";
	}
}
if ($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==100){
  if ($jdwl['TahunID']=='20171'){
  echo "<input type=button name='TambahPresensi' value='Tambah Presensi' 
      onClick=\"javascript:PrsnEdit(1, $jdwl[JadwalID], 0)\" />";
    }
  echo " <input type=button name='KuliahOnline' value='Kuliah Online'
      onClick=\"javascript:KuliahOnline($jdwl[JadwalID])\" /> ";
}
	echo "<input type=button name='Refresh' value='Refresh'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos=Edit&JadwalID=$jdwl[JadwalID]'\" />
    <input type=button name='Kembali' value='Kembali ke Daftar'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" /> 
    </td></tr>";

  echo "<tr>
    <th class=ttl width=40 colspan=2>#</th>
    <th class=ttl width=120>Tanggal</th>
    <th class=ttl width=60>Jam</th>
    <th class=ttl>Dosen Pemberi Kuliah</th>
    <th class=ttl>Materi</th>
    <th class=ttl width=50>Mhsw<br />Hadir</th>
    <th class=ttl>Daring</th>
    <th class=ttl>Del</th>
    </tr>";
  
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $Jumlah = $w['JmlHadir']+0;
    $EdtMateri = (empty($w['Catatan']))? "<a href='#' onClick='javascript:PrsnMateriEdit($w[JadwalID],$w[PresensiID])'>-- Materi Belum diisi --</a>" : "<a href='#' onClick='javascript:PrsnMateriEdit($w[JadwalID],$w[PresensiID])'>".preg_replace('#(\\\r\\\n)#', "<br />", $w['Catatan'])."</a>";
    $Daring = ($w['KuliahOnline']=='Y')? "<a href='?mnux=jur/kuliahonline&gos=Edit&pid=$w[PresensiID]'>Kuliah<br>Online</a>" : "-";
    echo "<tr>
      <td class=inp width=20>$w[Pertemuan]</td>
      <td class=ul width=10 align=center><a href='#' onClick='javascript:PrsnEdit(0, $w[JadwalID], $w[PresensiID])'><img src='img/edit.png' /></a></td>
      <td class=ul>$w[_HR] <br><sup>$w[_Tanggal]</sup></td>
      <td class=ul align=center><sup>$w[_JM]</sup>&#8594;<sub>$w[_JS]</sub></td>
      <td class=ul>$w[DSN] <sup>$w[Gelar]</sup></td>
      <td class=ul>$EdtMateri&nbsp;</td>
      <td class=ul align=right>
        $Jumlah
        <a href='#' onClick='javascript:PrsnMhswEdit($w[PresensiID])'><img src='img/edit.png' /></a>
        </td>
      <td class=ul align=center>$Daring&nbsp;</td>
      <td class=ul align=right>
        <a href='#' onClick='javascript:PrsDelete($w[PresensiID],$w[JadwalID])'><img src='img/del.gif' /></a>
        </td>
      </tr>";
  }
  
  echo "</table>";
}
function TampilkanHeader($jdwl) {
  $TagTambahan = ($jdwl['Tambahan'] == 'Y')? "<b>( $jdwl[_NamaJenisJadwal] )</b>" : "";
  $JadwalUTS = GetFields("jadwaluts j left outer join hari h on h.HariID = date_format(j.Tanggal, '%w')","JadwalID",$jdwl[JadwalID],"date_format(j.Tanggal,'%d-%m-%Y') TGL, h.Nama as Hari,j.JamMulai,j.JamSelesai");
  $JadwalUAS = GetFields("jadwaluas j left outer join hari h on h.HariID = date_format(j.Tanggal, '%w')","JadwalID",$jdwl[JadwalID],"date_format(j.Tanggal,'%d-%m-%Y') TGL, h.Nama as Hari,j.JamMulai,j.JamSelesai");
  echo "<table class=box cellspacing=0 align=center width=800>
  <tr><td class=inp width=100>Thn Akademik:</td>
      <td class=ul>$jdwl[TahunID]</td>
      <td class=inp width=100>Program Studi:</td>
      <td class=ul>$jdwl[_PRD] <sup>$jdwl[ProdiID]</sup></td>
      </tr>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul>$jdwl[Nama] $TagTambahan<sup>$jdwl[MKKode]</sup></td>
      <td class=inp>Dosen:</td>
      <td class=ul>$jdwl[DSN]</td>
      </tr>
  <tr><td class=inp>SKS:</td>
      <td class=ul>$jdwl[SKS], Peserta: $jdwl[JumlahMhsw] <sup title='Jumlah Mahasiswa'>&#2000;</sup></td>
      <td class=inp>Kelas:</td>
      <td class=ul>$jdwl[namaKelas] <sup>$jdwl[ProgramID]</sup></td>
      </tr>
  <tr><td class=inp rowspan=2>Jadwal Kuliah:</td>
      <td class=ul rowspan=2>".$jdwl['_HR']." <sup>".$jdwl['_JM']."</sup>&#8594;<sub>".$jdwl['_JS']."</sub></td>
      <td class=inp>Jadwal UTS:</td>
      <td class=ul> ".$JadwalUTS["Hari"].", ".$JadwalUTS["TGL"].",<sup>".$JadwalUTS["JamMulai"]."</sup>&#8594;<sub>".$JadwalUTS["JamSelesai"]."</sub></td>
       </tr>
       <tr>
       <td class=inp>Jadwal UAS:</td>
      <td class=ul> ".$JadwalUAS["Hari"].", ".$JadwalUAS["TGL"].",<sup>".$JadwalUAS["JamMulai"]."</sup>&#8594;<sub>".$JadwalUAS["JamSelesai"]."</sub>
      				</td>
                    </tr>
  </table>";
}

function PresensiScript() {
  echo <<<SCR
  <script>
  function KuliahOnline(jid) {
    lnk = "$_SESSION[mnux].kuliahonline.php?jid="+jid;
    win2 = window.open(lnk, "", "width=500, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function PrsnEdit(md, jid, pid) {
    lnk = "$_SESSION[mnux].edit.php?md="+md+"&jid="+jid+"&pid="+pid;
    win2 = window.open(lnk, "", "width=500, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function PrsnMhswEdit(pid) {
    lnk = "$_SESSION[mnux].mhswedit.php?pid="+pid;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function PrsDelete(pid,jid) {
    if(!confirm("Yakin menghapus Presensi ini?")) return false;
    lnk = "$_SESSION[mnux].hapus.php?pid="+pid+"&jid="+jid;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
    function PrsnMateriEdit(jid,pid) {
    lnk = "$_SESSION[mnux].materiedit.php?pid="+pid+"&jid="+jid;
    win2 = window.open(lnk, "", "width=450, height=200,top=300,left=420, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

function GetOption4($table, $key, $Fields, $Label, $Nilai, $Separator, $whr = '', $antar='<br />') {
  $_whr = (empty($whr))? '' : "and $whr";
  $s = "select $key, $Fields
    from $table
    where NA='N' $_whr order by $key";
  $r = _query($s);
  $_arrNilai = explode($Separator, $Nilai);
  $str = '';
  while ($w = _fetch_array($r)) {
    $_ck = (array_search($w[$key], $_arrNilai) === false)? '' : 'selected';
    $str .= "<option value='$w[$key]' $_ck>$w[$Label]</option>";
	//$str .= "<input type=checkbox name='".$key."[]' value='$w[$key]' $_ck> $w[$Label]$antar";
  }
  return $str;
}
?>