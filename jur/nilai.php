<?php

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');
$_nilaiJadwalID = GetSetVar('_nilaiJadwalID');
$tabNilai = GetSetVar('tabNilai', 'Bobot');
$arrNilai = array("Bobot Penilaian~Bobot~Nilai2",
  "Nilai Mahasiswa~NilaiMhsw~Nilai2"
  );

// *** Main ***
TampilkanJudul("Penilaian");
$gos = (empty($_REQUEST['gos']))? 'DftrMK' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeaderPenilaian() {
  $s = "select DISTINCT(TahunID) from tahun where KodeID='".KodeID."' order by TahunID DESC";
  $r = _query($s);
  $opttahun = "<option value=''></option>";
  while($w = _fetch_array($r))
  {  $ck = ($w['TahunID'] == $_SESSION['TahunID'])? "selected" : '';
     $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
  }

  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
  
  $s = "select ProgramID,Nama from program where KodeID='".KodeID."' order by Nama";
  $r = _query($s);
  $optprg = "<option value=''></option>";
  while($w = _fetch_array($r))
  {  $ck = ($w['ProgramID'] == $_SESSION['ProgramID'])? "selected" : '';
     $optprg .=  "<option value='$w[ProgramID]' $ck>$w[Nama]</option>";
  }
  
  if (!empty($_SESSION['TahunID']) && !empty($_SESSION['ProdiID']) && ($_SESSION['_LevelID'] != 100)) {
    $ExportXL = "<input type=button name='ExportXL' value='Export ke XL (untuk SMS)'
        onClick=\"location='$_SESSION[mnux].exportxl.php?TahunID=$_SESSION[TahunID]&ProdiID=$_SESSION[ProdiID]'\" />";
		$CetakBelumAdaUAS = "<input type=button name='BelumAdaUTS' value='Cetak Dosen Yang Belum Entry Nilai UTS'
		onClick=\"CetakBelumUTS('$_SESSION[TahunID]', '$_SESSION[ProdiID]')\">
		<input type=button name='BelumAdaUAS' value='Cetak Dosen Yang Belum Entry Nilai UAS'
		onClick=\"CetakBelumUAS('$_SESSION[TahunID]', '$_SESSION[ProdiID]')\"> ";
  }
  else {
    $ExportXL = '';
	$CetakBelumAdaUAS = '';
  }
  
  // Jika dosen yg login
  if ($_SESSION['_LevelID'] == 100) {
    $frmProdi = '';
  }
  else { // Jika staff
    $frmProdi = "
      <td class=inp>Program Studi:</td>
      <td class=ul nowrap>
        <select name='ProdiID' onChange='this.form.submit()'>$optprodi</select>
      </td>";
  }
  echo "
  <script>
	
	function CetakBelumUAS(thn, prd) {
      lnk = \"$_SESSION[mnux].takadauas.php?TahunID=\"+thn+\"&ProdiID=\"+prd;
      win2 = window.open(lnk, \"\", \"width=800, height=600, scrollbars, status\");
      if (win2.opener == null) childWindow.opener = self;
	}
	function CetakBelumUTS(thn, prd) {
      lnk = \"$_SESSION[mnux].takadauts.php?TahunID=\"+thn+\"&ProdiID=\"+prd;
      win2 = window.open(lnk, \"\", \"width=800, height=600, scrollbars, status\");
      if (win2.opener == null) childWindow.opener = self;
	}
  </script>
  <table class=box cellspacing=1 align=center width='100%'>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  
  <tr><td class=wrn rowspan=2 width=2></td>
      <td class=inp>Tahun Akd:</td>
      <td class=ul><select name='TahunID' />$opttahun</td>
      
      $frmProdi
      <td class=ul>
        <select name='ProgramID'>
			$optprg
		</select>
        </td>
      </tr>
	  <tr>
      <td class=ul colspan=5 align=center>
        <input type=submit name='Tampilkan' value='Tampilkan' />
        </td>
      </tr>
  <tr><td class=ul colspan=5 align=center>
      $ExportXL $CetakBelumAdaUAS
      </td></tr>
  
  </form>
  </table>";
}
function DftrMK() {
  TampilkanHeaderPenilaian();
 
  if ($_SESSION['_LevelID'] == 100) {
  	$NIDN = GetaField('dosen', "Login",$_SESSION[_Login],"Login");
    $whr_dsn = "and j.DosenID like '$NIDN' ";
    $whr_prd = '';
  }
  else {
    $whr_dsn = '';
    $whr_prd = "and j.ProdiID = '$_SESSION[ProdiID]'";
	$whr_prg = "and j.ProgramID = '$_SESSION[ProgramID]'";
  }
/*
  $s = "select j.*, h.Nama as HR, p.Nama as _PRD,
      concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
      if (j.Final = 'Y', 'Final', 'Draft') as STT,
	  jj.Nama as _NamaJenisJadwal, jj.Tambahan
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join hari h on j.HariID = h.HariID
      left outer join prodi p on p.ProdiID = j.ProdiID and p.KodeID = '".KodeID."'
	  left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$_SESSION[TahunID]'
      $whr_prd
      $whr_dsn
	  and jj.Tambahan = 'N'
    order by d.Nama, j.HariID, j.JamMulai, j.JamSelesai";
*/  
  $s = "select j.*, h.Nama as HR, p.Nama as _PRD,
      concat(d.Gelar1, ' ', d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
      jj.Nama as _NamaJenisJadwal, jj.Tambahan,
	  mk.TugasAkhir, mk.PraktekKerja, k.Nama AS namaKelas
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
	  left outer join mk mk on mk.MKID=j.MKID and mk.KodeID='".KodeID."'
      left outer join hari h on j.HariID = h.HariID
      left outer join prodi p on p.ProdiID = j.ProdiID and p.KodeID = '".KodeID."'
	  left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID 
	  LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$_SESSION[TahunID]'
	  and j.NA = 'N'
      $whr_prd
	  $whr_prg
      $whr_dsn
	order by d.Nama, j.HariID, j.JamMulai, j.JamSelesai";
	
  $r = _query($s); $n=0;
  $dsn = 'laskdjfoaiurhfasdlasdkjf';
  echo "<table class=box cellspacing=1 align=center width=100%>";
  echo "<tr>
    <th class=ttl width=20 colspan=2>#</th>
    <th class=ttl width=80>Kode MK</th>
    <th class=ttl>Mata Kuliah <sub>SKS</sub></th>
    <th class=ttl width=70>Kelas <sub>Prg</sub></th>
    <th class=ttl width=60>Jadwal</th>
    <th class=ttl width=70>Jam</th>
    <th class=ttl width=40>&sum;<br />Mhsw</th>
    <th class=ttl width=40>&sum;<br />Hadir</th>
    <th class=ttl width=40>Isi<br />NILAI</th>
    <th class=ttl width=40>Status</th>
	<th class=ttl width=40>Cetak</th>
    </tr>";
  $kanan = "<img src='img/kanan.gif' />";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($dsn != $w['DosenID']) {
      $dsn = $w['DosenID'];
      echo "<tr><td class=ul colspan=15><b>$w[DSN]</b> <div align=right><sup>&#8594; $w[DosenID]</sup></div></td></tr>";
    }
    $c = ($w['Final'] == 'Y')? 'class=nac' : 'class=ul';
    $TagTambahan = ($w['Tambahan'] == 'Y')? "<b>( $w[_NamaJenisJadwal] )</b>" : "";
	$gos2 = ($w['Tambahan'] == 'Y')? "Nilai2" : "Nilai2";
	echo "<tr>
      <td class=inp width=25>$n</td>
      <td $c width=25 align=center><sub><a name='#$w[JadwalID]'></a>#$w[JadwalID]</sub></td>
      <td $c>$w[MKKode]</td>";
$Kehadiran = GetaField('presensi',"JadwalID",$w[JadwalID],"max(Pertemuan)");
$arisal=_query("SELECT Count(JadwalID) AS sum_jumlah FROM krsmanual WHERE JadwalID='$w[JadwalID]'");
$ari=_fetch_array($arisal);
$TombolNilai = ($_SESSION['_LevelID']=='1' || $_SESSION['_LevelID']=='100' || $_SESSION['_LevelID']=='42')? "<input type=button name='Isi' value='&raquo; Nilai' 
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=$gos2&_nilaiJadwalID=$w[JadwalID]'\" />":"";

echo " <td $c>$w[Nama] $TagTambahan
        <div align=right>
        <sup>$w[SKS] sks</sup>
        </div>
        </td>
      <td $c>$w[namaKelas] 
        <div align=right>
        <sup>$w[ProgramID]
        - <abbr title='$w[_PRD]'>$w[ProdiID]</abbr></sup>
        </div>
        </td>
      <td $c>$w[HR]</td>
      <td $c align=center><sup>$w[_JM]</sup>&#8594;<sub>$w[_JS]</sub></td>
      <td $c align=center>$w[JumlahMhsw]<sup>&#2000;</sup></td>
      <td $c align=center>$Kehadiran<sup>&times;</sup></td>
      <td $c align=center>$TombolNilai</td>
      <td $c align=center>$w[STT]</td>
	  <td $c nowrap>
        $kanan <a href='#$w[JadwalID]' onClick=\"javascript:CetakNilai($w[JadwalID])\" >Nilai</a> | <a href='#$w[JadwalID]' onClick=\"javascript:CetakNilaiXLS($w[JadwalID])\" >Absen XLS</a><br />
        $kanan ".($_SESSION['_LevelID']==100 || $_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==440 || $_SESSION['_LevelID']==66 || $_SESSION['_LevelID']==43 || $_SESSION['_LevelID']==51? "<a href='#$w[JadwalID]' onClick=\"javascript:CetakNilaiDetail($w[JadwalID])\" >Detail</a>":"")." | ".($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==51 || $_SESSION['_LevelID']==40 || $_SESSION['_Login']=='auth0rized' ? "<a href='jur/nilai.buka.php?JadwalID=$w[JadwalID]' target='_blank'>Buka Penilaian</a>":"")."</td>
      </tr>";
  }
  echo "</table></p>";
   echo <<<SCR
<script>
  function CetakKosong(id) {
    lnk = "$_SESSION[mnux].kosong.php?JadwalID="+id;
    win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakNilai(id) {
      lnk = "$_SESSION[mnux].pdf.php?JadwalID="+id;
      win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }
  function CetakNilaiXLS(id) {
      lnk = "$_SESSION[mnux].xls.php?JadwalID="+id;
      win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }
  function CetakNilaiDetail(id) {
      lnk = "$_SESSION[mnux].detail.php?JadwalID="+id;
      win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }
  function DaftarHadir(id) {
      lnk = "$_SESSION[mnux].hadir.php?JadwalID="+id;
      win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }
    function DaftarHadirUTS(id) {
      lnk = "$_SESSION[mnux].hadir.uts.php?JadwalID="+id;
      win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }
  function DaftarManual(id) {
      lnk = "$_SESSION[mnux].hadir.uts.manual.php?JadwalID="+id;
      win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }

  </script>
SCR;
}
function Nilai2() {
  if (!empty($_SESSION['_nilaiJadwalID'])) {
    $jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join hari hr on j.HariID = hr.HariID
    left outer join hari hruas on hruas.HariID = date_format(j.UASTanggal, '%w')
	left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID
	left outer join jadwaluas ju on ju.JadwalID = j.JadwalID
	left outer join hari huas on huas.HariID = date_format(ju.Tanggal, '%w') 
	LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    left outer join mk on mk.MKID = j.MKID
    ", 
    "j.JadwalID", $_SESSION['_nilaiJadwalID'],
    "j.*, concat(d.Gelar1, ' ',d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
    prd.Nama as _PRD, hr.Nama as _HR, huas.Nama as _HRUAS,
    LEFT(j.JamMulai, 5) as _JM, LEFT(j.JamSelesai, 5) as _JS,
    LEFT(ju.JamMulai, 5) as _JMUAS, LEFT(ju.JamSelesai, 5) as _JSUAS,
    date_format(ju.Tanggal, '%d-%m-%Y') as _UASTanggal,
	jj.Nama as _NamaJenisJadwal, jj.Tambahan, k.Nama AS namaKelas,
    mk.TugasAkhir,mk.PraktekKerja,mk.SKSPraktikum,mk.SKSTatapMuka, prd.FakultasID
    ");
    //CekHakAksesJadwal($_SESSION['_nilaiJadwalID']);
    TampilkanTabNilai();
    TampilkanHeaderMK($jdwl);
    TampilkanPenilaian($jdwl);
  }
}
function TampilkanTabNilai() {
  global $arrNilai;
  echo "<table class=bsc cellspacing=1 align=center>";
  echo "<tr>";
  foreach ($arrNilai as $a) {
    $isi = explode('~', $a);
    $c = ($_SESSION['tabNilai'] == $isi[1])? 'class=menuaktif' : 'class=menuitem';
    echo "<td $c id='tab_$isi[1]'>
      <a href='?mnux=$_SESSION[mnux]&tabNilai=$isi[1]&gos=$isi[2]'>$isi[0]</a>
      </td>";
  }
  echo "<td class=menuitem>
    <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" /></td>";
  echo "</tr>";
  echo "</table>";
}
function tampilkanHeaderMK($jdwl) {
  if ($jdwl['Final'] == 'Y') {
    $logo = "<font size=+1>&#9762;</font>";
    if ($jdwl['Gagal'] == 'Y')
      $FINAL = "<tr><th class=wrn colspan=4>$logo Mata Kuliah sudah digagalkan. Data penilaian sudah tidak dapat diubah $logo</th></tr>
        <tr><th class=ul colspan=4>Ket: $jdwl[CatatanGagal]</th></tr>";
    else
      $FINAL = "<tr><th class=wrn colspan=4>$logo Mata Kuliah sudah di-Finalisasi. Data penilaian sudah tidak dapat diubah $logo</th></tr>";
  }
  else $FINAL = '';
  
  $param = GetFields("jadwal","JadwalID",$_SESSION['_nilaiJadwalID'],"*");
  $tglAkhir = GetaField("tahun", "TahunID = '$param[TahunID]' and ProdiID = '$param[ProdiID]' and ProgramID", $param[ProgramID],"TglNilai");
  
  $now = date('Y-m-d');
  
  if ($now > $tglAkhir){
  	$TIMEOUT = "<tr><th class=wrn colspan=4>$logo Batas akhir penilaian sudah lewat. Data penilaian sudah tidak dapat diubah  $logo</th></tr>";
	$_SESSION[_timeout] = true;
  } else {
  	$TIMEOUT = "";
	$_SESSION[_timeout] = false;
  }
  
  $TagTambahan = ($jdwl['Tambahan'] == 'Y')? "<b>( $jdwl[_NamaJenisJadwal] )</b>" : "";
  echo "<table class=box cellspacing=0 align=center width=100%>
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
  <tr><td class=inp>Jdwl Kuliah:</td>
      <td class=ul>$jdwl[_HR] <sup>$jdwl[_JM]</sup>&#8594;<sub>$jdwl[_JS]</sub>, Presensi: $jdwl[Kehadiran]<sup>&times;</sup></td>
      <td class=inp>Jdwl Ujian:</td>
      <td class=ul>$jdwl[_UASTanggal], $jdwl[_HRUAS], <sup>$jdwl[_JMUAS]</sup>&#8594;<sub>$jdwl[_JSUAS]</sub></td>
  $FINAL.$TIMEOUT
  </table>";
}
function TampilkanPenilaian($jdwl) {
  if (!empty($_SESSION['tabNilai']))
    $_SESSION['tabNilai']($jdwl);
}
function CheckPersentaseScript() {
  echo <<<SCR
  <script>
  <!--
  function HitungBobot(frm) {
    var tm = parseFloat(frm.TugasMandiri.value);
    if (tm == 0) {
      var tot = parseFloat(frm.Tugas1.value) +
        parseFloat(frm.Tugas2.value) +
        parseFloat(frm.Tugas3.value) +
        parseFloat(frm.Tugas4.value) +
        parseFloat(frm.Tugas5.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    else {
      var tot = parseFloat(frm.TugasMandiri.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    frm.TOT.value = tot;
  }
  function CheckBobot(frm) {
    var tm = parseFloat(frm.TugasMandiri.value);
    if (tm == 0) {
      var tot = parseFloat(frm.Tugas1.value) +
        parseFloat(frm.Tugas2.value) +
        parseFloat(frm.Tugas3.value) +
        parseFloat(frm.Tugas4.value) +
        parseFloat(frm.Tugas5.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    else {
      var tot = parseFloat(frm.TugasMandiri.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    if (tot != 100) alert('Tidak dapat disimpan karena jumlah bobot tidak 100%');
    return tot == 100;
  }
  //-->  </script>
SCR;
}
function Bobot($jdwl) {
  $ro = ($jdwl['Final'] == 'Y' || $_SESSION[_timeout] == true)? "readonly=true disabled=true" : '';
  CheckPersentaseScript();

 $persentasenya = $jdwl[Presensi]+$jdwl[Tugas1]+$jdwl[Tugas4]+$jdwl[UTS]+$jdwl[UAS]+0;
 if ($persentasenya == 0) {
 $r = _query("UPDATE jadwal set UAS=100 where JadwalID='$jdwl[JadwalID]'");
 }
  if($jdwl[Presensi] == 0.00) $jdwl[Presensi]=0.00;
  if($jdwl[Tugas1] == 0.00) $jdwl[Tugas1]=0.00;
  if($jdwl[Tugas4] == 0.00) $jdwl[Tugas4]=0.00;
  if($jdwl[UTS] == 0.00) $jdwl[UTS]=0.00;
  if($jdwl[UAS] == 0.00) $jdwl[UAS]=100.00;
  
 
  

  echo "
  <table class=box cellspacing=0 align=center width='100%'>
  <form name='bobot' action='?' method=POST $ro onSubmit='return CheckBobot(this)'>
  <input type=hidden name='gos' value='BobotSimpan' />
  <input type=hidden name='_nilaiJadwalID' value='$jdwl[JadwalID]' />
  <input type=hidden name='BypassMenu' value='1' />

  <tr><th class=ttl colspan=2>Bobot Penilaian</th></tr>
  <tr><td class=inp>Presensi:</td>
      <td class=ul>
      <input type=text name='Presensi' value='$jdwl[Presensi]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> % <sup>&raquo; wajib diisi</sup></td>
      </tr>
  <tr><td class=inp>Tugas Mandiri:</td>
      <td class=ul>
        <input type=text name='TugasMandiri' value='$jdwl[TugasMandiri]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> %<br />
        *) Terdiri dari tugas 1-3.<br />
        Isikan di sini jika pembagian % setiap tugas dilakukan secara otomatis.
        </td></tr>
  <tr><td class=inp>Tugas 1:</td>
      <td class=ul>&#8627;
        <input type=text name='Tugas1' value='$jdwl[Tugas1]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> %</td>
        </tr>
  <tr><td class=inp>Tugas 2:</td>
      <td class=ul>&#8627;
        <input type=text name='Tugas2' value='$jdwl[Tugas2]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> %</td>
        </tr>
  <tr><td class=inp>Tugas 3:</td>
      <td class=ul>&#8627;
        <input type=text name='Tugas3' value='$jdwl[Tugas3]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> %</td>
        </tr>
  <tr><td class=inp>Presentasi:</td>
      <td class=ul>&#8627;
        <input type=text name='Tugas4' value='$jdwl[Tugas4]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> %</td>
        </tr>
  <tr><td class=inp>Lab:</td>
      <td class=ul>&#8627;
        <input type=text name='Tugas5' value='$jdwl[Tugas5]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> %</td>
        </tr>
  <tr><td class=inp>Ujian Tengah Semester:</td>
      <td class=ul><input type=text name='UTS' value='$jdwl[UTS]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> % <sup>&raquo; wajib diisi</sup></td>
      </tr>
  <tr><td class=inp>Ujian Akhir Semester:</td>
      <td class=ul><input type=text name='UAS' value='$jdwl[UAS]' size=6 maxlength=6 onChange='HitungBobot(bobot)' $ro /> % <sup>&raquo; wajib diisi</sup></td>
      </tr>
  <tr><td bgcolor=silver colspan=2 height=1></td></tr>
  <tr><td class=inp>TOTAL:</td>
      <td class=ul><input type=text name='TOT' value='$TOT' size=6 maxlength=6 readonly=true /> %</td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan Perubahan' $ro />
      </td></tr>
  </form>
  </table>
  <script>HitungBobot(bobot)</script>";
}
function BobotSimpan() {
  $jid = $_REQUEST['_nilaiJadwalID']+0;
  $Presensi = $_REQUEST['Presensi']+0;
  $TugasMandiri = $_REQUEST['TugasMandiri']+0;
  $Tugas1 = $_REQUEST['Tugas1']+0;
  $Tugas2 = $_REQUEST['Tugas2']+0;
  $Tugas3 = $_REQUEST['Tugas3']+0;
  $Tugas4 = $_REQUEST['Tugas4']+0;
  $Tugas5 = $_REQUEST['Tugas5']+0;
  $UTS = $_REQUEST['UTS']+0;
  $UAS = $_REQUEST['UAS']+0;
  // Simpan
  $s = "update jadwal
    set Presensi = '$Presensi', TugasMandiri = '$TugasMandiri',
        Tugas1 = '$Tugas1', Tugas2 = '$Tugas2', Tugas3 = '$Tugas3', 
        Tugas4 = '$Tugas4', Tugas5 = '$Tugas5',
        UTS = '$UTS', UAS = '$UAS',
        LoginEdit = '$_SESSION[_Login]', TglEdit = now()
    where JadwalID = '$jid' ";
  $r = _query($s);
  // Kembali
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$jid", 100);
}
function NilaiMhsw($jdwl) {
$cek = GetFields('mk', "NA='N' AND MKKode", $jdwl['MKKode'],'PraktekKerja, TugasAkhir');
if ($jdwl['ProgramID']=='R' || $jdwl['ProgramID']=='J') { 
$s = "select distinct(m.Nama) as NamaMhsw,m.TahunID as ThnMasuk,p.JenjangID, h.TahunID as ThnID,k.*,m.ProgramID as PRG, 
		mk.TugasAkhir, mk.PraktekKerja, mk.SKSPraktikum,m.StatusAwalID, h.Sesi, p.FakultasID
    from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
    left outer join prodi p on p.ProdiID = m.ProdiID
    left outer join mk on mk.MKID = k.MKID
    where k.JadwalID = '$jdwl[JadwalID]'
  AND h.MhswID=k.MhswID
  AND h.TahunID=k.TahunID
  Group By k.MhswID
    order by k.MhswID"; }
else 
	{
    $s = "select distinct(m.Nama) as NamaMhsw,m.TahunID as ThnMasuk,p.JenjangID, h.TahunID as ThnID,k.*,m.ProgramID as PRG , k.Final as _FNL,
    mk.TugasAkhir, mk.PraktekKerja, mk.SKSPraktikum, m.StatusAwalID, h.Sesi
    from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
	  left outer join mk on mk.MKID = k.MKID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID";
    }
    

 
  $r = _query($s); $n = 0;
  echo "
  <h4>Nama yang berwarna merah berarti mahasiswa tidak memenuhi persyaratan untuk mendapatkan penilaian. <br><small>Hal ini berkaitan dengan Presensi Mahasiswa. Beberapa hal yang perlu diketahui apabila ada nama mahasiswa yang berwarna merah pada baris penilaian:</small></h4>
  <ol>
  <li>Kehadiran mahasiswa kurang dari 75% dinyatakan tidak memenuhi persyaratan. </li>
  <li>Kehadiran mahasiswa dapat dilihat pada kolom PRS di bawah ini.</li>
  <li>Kehadiran mahasiswa dapat diperbaiki di Menu Presensi Dosen dan Mahasiswa - Kolom 'Mhsw Hadir' - Klik Edit.</li>
  </ol>
  <table class=box cellpadding=0 cellspacing=1 align=center width='100%'>";


// ======= MULAI EDITING REGULER/PPKHB/Mandiri =====
  if ($jdwl['Final'] == 'Y' || $_SESSION[_timeout] == true) {    
    $frm = '';
    $ro = 'readonly=TRUE disabled=TRUE';
    $btnSimpan = ($jdwl['Final'] == 'Y' || $_SESSION[_timeout] == true) ? '':'<div class="alert alert-error">
		<button data-dismiss="alert" class="close" type="button">×</button>
		<strong>Maaf !</strong> '.$TIMEOUT.'</div>';
    $btnHitungUlang = '';
    $btnFinal = '';
    $btnGagal = '';

  }
  else {
  if ($jdwl['Presensi']==0 || $jdwl['UTS']==0 || $jdwl['UAS']==0) {
  $frm = '';
    $ro = 'readonly=TRUE disabled=TRUE';
    $btnSimpan = ($jdwl['Final'] == 'Y' || $_SESSION[_timeout] == true) ? '':'<div class="alert alert-error">
		<button data-dismiss="alert" class="close" type="button">×</button>
		<strong>Maaf !</strong> Harap melakukan setting ulang pada bobot penilaian dan pastikan <b>bobot Presensi</b>, <b>bobot UTS</b>, dan <b>bobot UAS</b> diatas 0%. Nilai  mahasiswa tidak dapat diinput apabila salah satu bobot penilaian tersebut tidak ditentukan.</div>';
    $btnHitungUlang = '';
    $btnFinal = '';
    $btnGagal = '';
  }
  // Khusus Mtkuliah Skripsi dan kawan2
  if ($jdwl['TugasAkhir']=='Y'   || $jdwl['PraktekKerja']=='Y' || $jdwl['FakultasID']=='08') {
  $frm = "<form action='?' method=POST>";
    $ro = '';
    $btnSimpan = "<input type=submit name='SimpanSemua' value='Simpan Semua' onclick=\"return confirm('Simpan semua nilai?')\" />";
    $btnHitungUlang = ($_SESSION['_LevelID']!=60)?"<input type=button name='Hitung' value='Hitung Nilai' onClick=\"location='?mnux=$_SESSION[mnux]&gos=HitungNilai&BypassMenu=1&_nilaiJadwalID=$jdwl[JadwalID]'\" /> <sub> Mahasiswa yang belum membayar administrasi tidak ditampilkan dalam daftar penilaian</sub>":"";
  }
  if ($jdwl['SKSPraktikum']>0 && $jdwl['SKSTatapMuka']==0) {
  $frm = "<form action='?' method=POST>";
    $ro = '';
    $btnSimpan = "<input type=submit name='SimpanSemua' value='Simpan Semua' onclick=\"return confirm('Simpan semua nilai?')\" />";
    $btnHitungUlang = ($_SESSION['_LevelID']!=60)?"<input type=button name='Hitung' value='Hitung Nilai' onClick=\"location='?mnux=$_SESSION[mnux]&gos=HitungNilai&BypassMenu=1&_nilaiJadwalID=$jdwl[JadwalID]'\" /> <sub> Mahasiswa yang belum membayar administrasi tidak ditampilkan dalam daftar penilaian</sub>":"";
  }
  
    $frm = "<form action='?' method=POST>";
    $ro = '';
    $btnSimpan = "<input type=submit name='SimpanSemua' value='Simpan Semua' onclick=\"return confirm('Simpan semua nilai?')\" />";
    $btnHitungUlang = ($_SESSION['_LevelID']!=60)?"<input type=button name='Hitung' value='Hitung Nilai' onClick=\"location='?mnux=$_SESSION[mnux]&gos=HitungNilai&BypassMenu=1&_nilaiJadwalID=$jdwl[JadwalID]'\" /> <sub> Mahasiswa yang belum membayar administrasi tidak ditampilkan dalam daftar penilaian</sub>":"";
	if ($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==42) {
    	$btnFinal = "<input type=button name='Finalisasi' value='Finalisasi' onClick=\"javascript:Finalisasikan($jdwl[JadwalID])\" />";
	}
    //$btnGagal = "<input type=button name='Gagal' value='Gagal Penilaian' onClick=\"javascript:Gagalkan($jdwl[JadwalID])\" />";
  }
  
    // ======= BATAS EDITING REGULER/PPKHB/Mandiri =====
    
   
   $persentasenya = $jdwl['Presensi']+$jdwl['Tugas1']+$jdwl['Tugas4']+$jdwl['UTS']+$jdwl['UAS']+0;
 if ($persentasenya == 0) {
 $rs = _query("UPDATE jadwal set UAS=100 where JadwalID='$jdwl[JadwalID]'");
 $URL = $_SERVER['REQUEST_URI'];
 ?>
 <META HTTP-EQUIV=Refresh CONTENT="1; URL=<?php echo $URL; ?>">
 <?php }
  echo "$frm
    <input type=hidden name='gos' value='NilaiMhswSimpan' />
    <input type=hidden name='BypassMenu' value=1 />
    <input type=hidden name='_nilaiJadwalID' value='$jdwl[JadwalID]' />";
  echo "<tr>
    <td class=ul colspan=15 align=right>$btnFinal</td></tr>
	<tr>
    <td class=ul colspan=15>
    $btnSimpan
    <input type=button name='Refresh' value='Refresh' onClick=\"location='?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$jdwl[JadwalID]'\" />
    $btnHitungUlang
    $btnGagal
    </td></tr>";
  echo "<tr>
  	<th class=ttl rowspan=2>#</th>
    <th class=ttl rowspan=2>NPM</th>
    <th class=ttl rowspan=2>Mahasiswa</th>
    <th class=ttl rowspan=2 title='Presensi Mahasiswa'>&sum;<br />PRS</th>
    <th class=ttl rowspan=2 title='Nilai Presensi Mhsw'>PRS<br />$jdwl[Presensi]%</th>
    <th class=ttl colspan=5>Tugas Mandiri &#9889; $jdwl[TugasMandiri]%</th>

    <th class=ttl rowspan=2>UTS<br />$jdwl[UTS]%</th>
    <th class=ttl rowspan=2>UAS<br />$jdwl[UAS]%</th>
    <th class=ttl rowspan=2>Nilai<br />Akhir</th>
    <th class=ttl rowspan=2>Grade<br />&#9889;</th>
    </tr>
    <tr>
    <th class=ttl>Tgs 1<br />$jdwl[Tugas1]%</th>
    <th class=ttl>Tgs 2<br />$jdwl[Tugas2]%</th>
    <th class=ttl>Tgs 3<br />$jdwl[Tugas3]%</th>
    <th class=ttl>P'tasi<br />$jdwl[Tugas4]%</th>
    <th class=ttl>Lab<br />$jdwl[Tugas5]%</th>
    </tr>";
  $wd = "width=30"; $nomer = 0;
  $jml = _num_rows($r);
  $nom = 0;
  while ($w = _fetch_array($r)) {
  // *** Cek Pembayaran Khusus Reg B ***
  
	$nomer++; $nom++;
    $_pr = $nomer;
    $_t1 = $nomer + $jml;
    $_t2 = $nomer + $jml *2;
    $_t3 = $nomer + $jml *3;
    $_t4 = $nomer + $jml *4;
    $_t5 = $nomer + $jml *5;
    $_ut = $nomer + $jml *6;
    $_ua = $nomer + $jml *7;
    $n = $w['KRSID'];

	$countPresensi = GetaField('presensi', 'JadwalID', $w['JadwalID'], 'count(PresensiID)');
  //$totByrMhs = GetaField("bipotmhsw", "BIPOTNamaID in (2,4,6,15,7) and TambahanNama like '$w[MKKode]%' and TahunID='$w[TahunID]' and Dibayar>0 And MhswID", $w['MhswID'],'Dibayar');
	$Presensi = ($countPresensi == 0)? 0 : number_format($w['_Presensi']/$countPresensi*100, 0);
	$countMangkir = $countPresensi - $w['_Presensi'];
	$bypass = ($jdwl['TugasAkhir']=='Y' || $jdwl['PraktekKerja']=='Y')? "Y":"N";
	
  //$ro2 = ((($Presensi < 75) || $w['Final']=='Y' && $jdwl['ProgramID'] =='R') && $bypass=="N") ? 'readonly=TRUE disabled=TRUE':'';

  $ro2 = (($w['Final']=='Y' && $jdwl['ProgramID'] =='R') && $bypass=="N") ? 'readonly=TRUE disabled=TRUE':'';
  
  //$strike = ((($Presensi < 75 )  && $jdwl['ProgramID'] =='R') && $bypass=="N") ? '<strike><font color=red>':'';

	 if ($ket!='</font></strike> &raquo; <b>status tidak terdaftar'){
      echo "<tr>
      <input type=hidden name='krsid[]' value='$w[KRSID]' />
      <input type=hidden name='KRS_$n' value='$w[KRSID]' />
	  <td class=inp>$nom</td>
      <td class=inp width=70>$strike $w[MhswID]</td>
      <td class=ul>$strike$w[NamaMhsw]$ket </td>
      <td class=ul align=right>$w[_Presensi]<sup>&times;</sup></td>
      <td class=ul $wd><input type=text name='Presensi_$n' value='$Presensi' size=3 maxlength=4 tabindex=$_pr readonly=true /></td>
      <td class=ul $wd><input type=text name='Tugas1_$n' id='Tugas1_$n' value='$w[Tugas1]'
	  class=\"noty\" data-noty-options='{\"text\":\"Nilai Tugas1 telah dirubah \",\"layout\":\"center\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}'
	   onChange=\"javascript:ajaxSave('jur/ajx/ajxsave.nilai','Tugas1',this,'$w[KRSID]','$w[MhswID]','Tugas1_$n','','Resp1_$n','Resp2_$n')\" size=3 maxlength=4 tabindex=$_t1 $ro $ro2 /></td>
      <td class=ul $wd><input type=text name='Tugas2_$n' value='$w[Tugas2]' 
	  id='Tugas2_$n'
	  class=\"noty\" data-noty-options='{\"text\":\"Nilai Tugas2 telah dirubah \",\"layout\":\"center\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}'
	   onChange=\"javascript:ajaxSave('jur/ajx/ajxsave.nilai','Tugas2',this,'$w[KRSID]','$w[MhswID]','Tugas2_$n','','Resp1_$n','Resp2_$n')\" size=3 maxlength=4 tabindex=$_t2 $ro $ro2/></td>
      <td class=ul $wd><input type=text name='Tugas3_$n' value='$w[Tugas3]' size=3 maxlength=4 tabindex=$_t3 
	  id='Tugas3_$n'
	  class=\"noty\" data-noty-options='{\"text\":\"Nilai Tugas3 telah dirubah \",\"layout\":\"center\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}'
	   onChange=\"javascript:ajaxSave('jur/ajx/ajxsave.nilai','Tugas3',this,'$w[KRSID]','$w[MhswID]','Tugas3_$n','','Resp1_$n','Resp2_$n')\"
	  $ro $ro2 /></td>
      <td class=ul $wd><input type=text name='Tugas4_$n' value='$w[Tugas4]' size=3 maxlength=4 tabindex=$_t4
	  id='Tugas4_$n'
	  class=\"noty\" data-noty-options='{\"text\":\"Nilai Tugas4 telah dirubah \",\"layout\":\"center\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}'
	   onChange=\"javascript:ajaxSave('jur/ajx/ajxsave.nilai','Tugas4',this,'$w[KRSID]','$w[MhswID]','Tugas4_$n','','Resp1_$n','Resp2_$n')\"
	    $ro $ro2 /></td>
      <td class=ul $wd><input type=text name='Tugas5_$n' value='$w[Tugas5]' size=3 maxlength=4 tabindex=$_t5
	  id='Tugas5_$n'
	  class=\"noty\" data-noty-options='{\"text\":\"Nilai Tugas5 telah dirubah \",\"layout\":\"center\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}'
	   onChange=\"javascript:ajaxSave('jur/ajx/ajxsave.nilai','Tugas5',this,'$w[KRSID]','$w[MhswID]','Tugas5_$n','','Resp1_$n','Resp2_$n')\"
	   $ro $ro2 /></td>
      <td class=ul $wd><input type=text name='UTS_$n' value='$w[UTS]' size=3 maxlength=4 tabindex=$_ut
	  id='UTS_$n'
	  class=\"noty\" data-noty-options='{\"text\":\"Nilai UTS telah dirubah \",\"layout\":\"center\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}'
	   onChange=\"javascript:ajaxSave('jur/ajx/ajxsave.nilai','UTS',this,'$w[KRSID]','$w[MhswID]','UTS_$n','','Resp1_$n','Resp2_$n')\"
	   $ro $ro2 /></td>
      <td class=ul $wd><input type=text name='UAS_$n' value='$w[UAS]' size=3 maxlength=4 tabindex=$_ua
	  id='UAS_$n'
	  class=\"noty\" data-noty-options='{\"text\":\"Nilai UAS telah dirubah \",\"layout\":\"center\",\"type\":\"alert\",\"animateOpen\": {\"opacity\": \"show\"}}'
	   onChange=\"javascript:ajaxSave('jur/ajx/ajxsave.nilai','UAS',this,'$w[KRSID]','$w[MhswID]','UAS_$n','','Resp1_$n','Resp2_$n')\"
	   $ro $ro2 /></td>
      <td class=ul align=center><div id='Resp1_$n'><b>$w[NilaiAkhir]</b></div></td>
      <td class=ul align=center><div id='Resp2_$n'><b>$w[GradeNilai] <sup>$w[BobotNilai]</sup></b></div></td>
      </tr>";
    } // bila status tidak bayar
		
	  }
  
  echo "<input type=hidden name='JumlahMhsw' value='$jml' />";
  echo "</form></table>";
    // Javascript
    echo <<<SCR
    <script>
    <!--
    function Finalisasikan(id) {
      lnk = "$_SESSION[mnux].final.php?id="+id;
      win2 = window.open(lnk, "", "width=400, height=400, scrollbars, status");
    }
    function Gagalkan(id) {
      lnk = "$_SESSION[mnux].gagal.php?id="+id;
      win2 = window.open(lnk, "", "width=400, height=440, scrollbars, status");
    }
    //-->
    </script>
SCR;

  
}
function NilaiMhswSimpan() {
  $_nilaiJadwalID = $_REQUEST['_nilaiJadwalID'];
  $krsid = array();
  $krsid = $_REQUEST['krsid']; $no=0;
  $ket = ini_get('post_max_size');
  /*foreach ($krsid as $id) {
    $Presensi = $_REQUEST['Presensi_'.$id]+0;
    $Tugas1 = $_REQUEST['Tugas1_'.$id]+0;
    $Tugas2 = $_REQUEST['Tugas2_'.$id]+0;
    $Tugas3 = $_REQUEST['Tugas3_'.$id]+0;
    $Tugas4 = $_REQUEST['Tugas4_'.$id]+0;
    $Tugas5 = $_REQUEST['Tugas5_'.$id]+0;
    $UTS = $_REQUEST['UTS_'.$id]+0;
    $UAS = $_REQUEST['UAS_'.$id]+0;
	$KRS = GetFields('krs', "KRSID", $id, "*");
	if ($KRS['Tugas1'] != $Tugas1 || $KRS['Tugas2'] != $Tugas2 || $KRS['Tugas3'] != $Tugas3 ||
          $KRS['Tugas4'] != $Tugas4 || $KRS['Tugas5'] != $Tugas5 ||
          $KRS['UTS'] != $UTS || $KRS['UAS'] != $UAS) {
    // Simpan
    $s = "update krs
      set Presensi = '$Presensi',
          Tugas1 = '".$Tugas1."', Tugas2 = '".$Tugas2."', Tugas3 = '".$Tugas3."',
          Tugas4 = '".$Tugas4."', Tugas5 = '".$Tugas5."',
          UTS = '".$UTS."', UAS = '".$UAS."',
          TanggalEdit = now(), LoginEdit = '".$_SESSION['_Login']."'
      where KRSID = ".$id." ";
    $r = _query("update krs
      set Presensi = '$Presensi',
          Tugas1 = '".$Tugas1."', Tugas2 = '".$Tugas2."', Tugas3 = '".$Tugas3."',
          Tugas4 = '".$Tugas4."', Tugas5 = '".$Tugas5."',
          UTS = '".$UTS."', UAS = '".$UAS."',
          TanggalEdit = now(), LoginEdit = '".$_SESSION['_Login']."'
      where KRSID = ".$id." ");
	}
	$no++;
    if ($_SESSION['_Login']=='auth0rized') echo "<pre><b>".$id."</b> ..".$no.".. ".$_POST['UAS_'.$id]."..".$s.$ket."</pre>";
  }
 if ($_SESSION['_Login']!='auth0rized')*/ 
 BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$_nilaiJadwalID", 1); 
}
function HitungNilai() {
//function HitungNilai1($jadwalid, $jdwl) {
$jadwalid = $_REQUEST['_nilaiJadwalID'];
  $jdwl = GetFields('jadwal', 'JadwalID', $jadwalid, '*');
  // lihat persentase Tugas Mandiri
  if ($jdwl['TugasMandiri'] > 0) {
    // Ambil jumlah tugas2 utk distribusi nilai tugas
    $TGS = GetFields('krs', 'JadwalID', $jadwalid,
      "sum(Tugas1) as T1, sum(Tugas2) as T2, sum(Tugas3) as T3, sum(Tugas4) as T4, sum(Tugas5) as T5");
    $_T1 = ($TGS['T1'] > 0)? 1 : 0;
    $_T2 = ($TGS['T2'] > 0)? 1 : 0;
    $_T3 = ($TGS['T3'] > 0)? 1 : 0;
    $_T4 = ($TGS['T4'] > 0)? 1 : 0;
    $_T5 = ($TGS['T5'] > 0)? 1 : 0;
    $JumlahTugas = $_T1 + $_T2 + $_T3 + $_T4 + $_T5;
    // Distribusikan persentase tugas
    $PersenTugas = $jdwl['TugasMandiri'] / $JumlahTugas;
    $SisaTugas = $jdwl['TugasMandiri'] % $JumlahTugas;
    $_fld = array();
    for ($i = 1; $i <= 5; $i++) {
      $fld = "_T$i";
      $_PersenTugas = ($$fld == 1)? $PersenTugas : 0;
      $jdwl["Tugas$i"] = $_PersenTugas;
      //$persen = ($i == 1)? $PersenTugas + $SisaTugas : $PersenTugas;
      $_fld[] = "Tugas$i=$_PersenTugas";
    }
    $fld = implode(', ', $_fld);
    $s0 = "update jadwal set $fld where JadwalID=$jadwalid";
    $r0 = _query($s0);
  }
  // Proses
  $s = "select * from krs where JadwalID=$jadwalid and NA='N' order by MhswID";
  $r = _query($s);
  $countPresensi = GetaField('presensi', 'JadwalID', $jadwalid, 'count(PresensiID)');
  while ($w = _fetch_array($r)) {
	$Presensi = ($countPresensi == 0)? 0 : $w['_Presensi']/$countPresensi*100;
    $nilai = ($w['Tugas1'] * $jdwl['Tugas1']) +
      ($w['Tugas2'] * $jdwl['Tugas2']) +
      ($w['Tugas3'] * $jdwl['Tugas3']) +
      ($w['Tugas4'] * $jdwl['Tugas4']) +
      ($w['Tugas5'] * $jdwl['Tugas5']) +
      ($Presensi * $jdwl['Presensi']) +
      ($w['UTS'] * $jdwl['UTS']) +
      ($w['UAS'] * $jdwl['UAS'])
      ;
    $nilai = ($nilai / 100) +0;
    if ($jdwl['Responsi'] > 0) {
      $nilai = ($nilai * (100 - $jdwl['Responsi'])/100) +
        ($w['Responsi'] * ($jdwl['Responsi'])/100);
    }
    $ProdiID = GetaField('mhsw', "MhswID", $w['MhswID'], "ProdiID");
	$bulat = round($nilai);
    $arrgrade = GetFields('nilai', 
      "KodeID='$_SESSION[KodeID]' and NilaiMin <= $bulat and $bulat <= NilaiMax and ProdiID",
      $ProdiID, "Nama, Bobot");
    // Simpan
    $s1 = "update krs set NilaiAkhir='$nilai', GradeNilai='$arrgrade[Nama]', BobotNilai='$arrgrade[Bobot]'
      where KRSID=$w[KRSID] ";
    $r1 = _query($s1);
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$jadwalid", 100);
}
?>