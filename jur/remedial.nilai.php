<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 29 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Jadwal Remedial", 1);

// *** Parameters ***
$tabNilai = GetSetVar('tabNilai', 'Bobot');
$arrNilai = array("Bobot Penilaian~Bobot~Nilai2",
  "Nilai Mahasiswa~NilaiMhsw~Nilai2"
  );
$id = $_REQUEST['id'];

$jdwlrem = GetFields("jadwalremedial j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    ", 
    "j.JadwalRemedialID", $id,
    "j.*, concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
    prd.Nama as _PRD,
    LEFT(j.JamMulai, 5) as _JM, LEFT(j.JamSelesai, 5) as _JS
    ");
	
// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Nilai2' : $_REQUEST['gos'];
$gos($jdwlrem);

// *** Functions ***

function Nilai2($jdwl) {
  if (!empty($jdwl)) {
    //CekHakAksesJadwal($_SESSION['_nilaiJadwalID']);
    TampilkanTabNilai($jdwl);
    TampilkanHeaderMK($jdwl);
    TampilkanPenilaian($jdwl);
  }
}
function TampilkanTabNilai($jdwl) {
  global $arrNilai;
  echo "<table class=bsc cellspacing=1 align=center>";
  echo "<tr>";
  foreach ($arrNilai as $a) {
    $isi = explode('~', $a);
    $c = ($_SESSION['tabNilai'] == $isi[1])? 'class=menuaktif' : 'class=menuitem';
    echo "<td $c id='tab_$isi[1]'>
      <a href='?&tabNilai=$isi[1]&gos=$isi[2]&id=$jdwl[JadwalRemedialID]'>$isi[0]</a>
      </td>";
  }
  echo "<td class=menuitem>
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" /></td>";
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
  echo "<table class=box cellspacing=0 align=center width=840>
  <tr><td class=inp width=100>Tahun Akademik:</td>
      <td class=ul>$jdwl[TahunID]</td>
      <td class=inp width=100>Program Studi:</td>
      <td class=ul>$jdwl[_PRD] <sup>$jdwl[ProdiID]</sup></td>
      </tr>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul>$jdwl[Nama] <sup>$jdwl[MKKode]</sup></td>
      <td class=inp>Dosen:</td>
      <td class=ul>$jdwl[DSN]</td>
      </tr>
  <tr><td class=inp>SKS:</td>
      <td class=ul>$jdwl[SKS], Peserta: $jdwl[JmlhMhsw] <sup title='Jumlah Mahasiswa'>&#2000;</sup></td>
      <td class=inp>Kelas:</td>
      <td class=ul>$jdwl[NamaKelas] <sup>$jdwl[ProgramID]</sup></td>
      </tr>
  <tr><td class=inp>Jdwl Kuliah:</td>
      <td class=ul>$jdwl[_HR] <sup>$jdwl[_JM]</sup>&#8594;<sub>$jdwl[_JS]</sub>, Presensi: $jdwl[Kehadiran]<sup>&times;</sup></td>
  $FINAL
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
    var jml = parseInt(frm.JumlahBobot.value);
	var ttlbobotlain = 0;
	
	for(var i = 0; i < jml; i++)
	{	var tempbobot = parseFloat(eval("frm.Bobot"+i+".value"));
		if(isNaN(tempbobot)) { tempbobot = 0.00; eval("frm.Bobot"+i+".value = ''"); }
		ttlbobotlain += tempbobot;
	}
	
	var presensi = parseFloat(frm.Presensi.value); 
	var uas = parseFloat(frm.Ujian.value);
	
	if(isNaN(presensi)) {  presensi = 0.00; frm.Presensi.value = ''; }
	if(isNaN(uas)) {	uas = 0.00; frm.Ujian.value = ''; }
	
	var tot = presensi + ttlbobotlain + uas;
    frm.TOT.value = tot;
  }
  function CheckBobot(frm) {
    var jml = parseInt(frm.JumlahBobot.value);
	var ttlbobotlain = 0;
	
	for(var i = 0; i < jml; i++)
	{	var tempbobot = parseFloat(eval("frm.Bobot"+i+".value"));
		if(isNaN(tempbobot)) { tempbobot = 0.00; eval("frm.Bobot"+i+".value = ''"); }
		ttlbobotlain += tempbobot;
	}
	
	var tot = parseFloat(frm.Presensi.value) +
        parseFloat(frm.Ujian.value) +
		ttlbobotlain;
    
    if (tot != 100) alert('Tidak dapat disimpan karena total bobot tidak 100%');
    return tot == 100;
  }
  function HapusBobot(id, brid, urutan, nama)
  {	if(confirm('Anda akan menghapus bobot nilai '+urutan+'. '+nama+'? Perubahan yang telah akan lakukan juga tidak akan disimpan.'))
	window.location='?gos=HapusBobot&id='+id+'&brid='+brid;
  }
  //-->  </script>
SCR;
}
function Bobot($jdwl) {
  $ro = ($jdwl['Final'] == 'Y')? "readonly=true disabled=true" : '';
  CheckPersentaseScript();
  
  $bobotstring = '';
  $s = "select * from bobotremedial where KodeID='".KodeID."' and JadwalRemedialID='$jdwl[JadwalRemedialID]' order by Urutan, Nama";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  {	$Bobot = ($w['Bobot'] == 0.00)? '' : $w['Bobot'];
	$bobotstring .= "<tr><td class=inp><input type=hidden name='BRID$n' value='$w[BobotRemedialID]'>$w[Urutan]. $w[Nama]:</td>
						 <td class=ul1><input type=text name='Bobot$n' value='$Bobot' size=3 maxlength=6 onChange='HitungBobot(bobot)' style='text-align: right;' $ro> %
								<a href='#self' onClick=\"HapusBobot('$jdwl[JadwalRemedialID]', '$w[BobotRemedialID]', '$w[Urutan]', '$w[Nama]')\"><sup>&times;</sup></a></td></tr>
						";
	$n++;
  }
  
  echo "
  <table class=box cellspacing=0 align=center width=840>
  <form action='?' method=POST $ro>
  <input type=hidden name='gos' value='TambahBobot' />
  <input type=hidden name='id' value='$jdwl[JadwalRemedialID]' />
  <input type=hidden name='BypassMenu' value='1' />
	  <tr><td class=inp>Urutan: </td>
	      <td class=ul1><input type=text name='UrutanBaru' value='' size=1 maxlength=10></td>
		  <td class=inp>Nama: </td>
		  <td class=ul1><input type=text name='NamaBaru' value='' size=20 maxlength=50> 
		  <input type=submit name='TambahBobot' value='Tambah Bobot' \"></td>
	  </tr>
  </form>
  </table>";
  
  if($jdwl['Presensi'] == 0.00) $jdwl['Presensi'] = '';
  if($jdwl['Ujian'] == 0.00) $jdwl['Ujian'] = '';
  
  echo "
  <table class=box cellspacing=0 align=center width=840>
  <form name='bobot' action='?' method=POST $ro onSubmit='return CheckBobot(this)'>
  <input type=hidden name='gos' value='BobotSimpan' />
  <input type=hidden name='id' value='$jdwl[JadwalRemedialID]' />
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>Bobot Penilaian</th></tr>
  <tr><<tr><td class=inp width=400>Presensi:</td>
					<td class=ul1><input type=text name='Presensi' value='$jdwl[Presensi]' size=3 maxlength=6 onChange='HitungBobot(bobot)' style='text-align: right;' $ro> %</td></tr>
				$bobotstring
  </tr>
  <input type=hidden id='JumlahBobot' name='JumlahBobot' value='$n'>
  <tr><td class=inp>Ujian Akhir Semester:</td>
      <td class=ul><input type=text name='Ujian' value='$jdwl[Ujian]' size=3 maxlength=6 onChange='HitungBobot(bobot)' style='text-align: right;' $ro /> %</td>
      </tr>
  <tr><td bgcolor=silver colspan=2 height=1></td></tr>
  <tr><td class=inp>TOTAL:</td>
      <td class=ul><input type=text name='TOT' value='$TOT' size=3 maxlength=6 style='text-align: right;' readonly=true /> %</td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan Perubahan' $ro />
      </td></tr>
  </form>
  </table>
  <script>HitungBobot(bobot)</script>";
}
function TambahBobot() {
  $id = $_REQUEST['id']+0;
  $UrutanBaru = $_REQUEST['UrutanBaru'];
  $NamaBaru = $_REQUEST['NamaBaru'];
  
  // Simpan
  //$ada = GetaField('bobotremedial', "JadwalRemedialID='$id' and Nama='$NamaBaru' and KodeID", KodeID, "Nama");
  //if(!empty($ada))
  //echo ErrorMsg("Duplikat Nama", "Terdapat Nama yang sama Jadwal yang Sama. Harap Nama dibedakan");
  
  //else
  //{  
	  $s = "insert bobotremedial
		set KodeID='".KodeID."', JadwalRemedialID='$id', 
			Urutan = '$UrutanBaru', Nama = '$NamaBaru',
			LoginBuat = '$_SESSION[_Login]', TanggalBuat = now()";
	  $r = _query($s);
	  // Kembali
	  BerhasilSimpan("?gos=Nilai2&id=$id", 100);
  //}
}
function HapusBobot() {
  $id = $_REQUEST['id']+0;
  $brid = $_REQUEST['brid']+0;
  
  $s = "delete from bobotremedial where BobotRemedialID='$brid' and KodeID='".KodeID."'";
  $r = _query($s);
  
  BerhasilSimpan("?gos=Nilai2&id=$id", 100);
  }
function BobotSimpan() {
  $id = $_REQUEST['id']+0;
  $JumlahBobot = $_REQUEST['JumlahBobot']+0;
  for($i=0; $i < $JumlahBobot; $i++)
  {	$BRID = $_REQUEST['BRID'.$i];
	$Bobot = $_REQUEST['Bobot'.$i];
	$s = "update bobotremedial set Bobot = '$Bobot' where BobotRemedialID='$BRID' and KodeID='".KodeID."'";
	$r = _query($s);
  }
  $Presensi = $_REQUEST['Presensi']+0;
  $Ujian = $_REQUEST['Ujian']+0;
  // Simpan
  $s = "update jadwalremedial
    set Presensi = '$Presensi', 
        Ujian = '$Ujian',
        LoginEdit = '$_SESSION[_Login]', TglEdit = now()
    where JadwalRemedialID = '$id' ";
  $r = _query($s);
  // Kembali
  BerhasilSimpan("?gos=Nilai2&id=$id", 100);
}
function NilaiMhsw($jdwl) {
  $s = "select k.*, m.Nama as NamaMhsw
    from krsremedial k
      left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where k.JadwalRemedialID = '$jdwl[JadwalRemedialID]'
      and k.NA = 'N'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  echo "<table class=box cellpadding=0 cellspacing=1 align=center width=840>";

  $s1 = "select * from bobotremedial where JadwalRemedialID='$jdwl[JadwalRemedialID]' and KodeID='".KodeID."' order by Urutan, Nama";
  $r1 = _query($s1);
  $DetailJudul = ''; $arrBobot = array(); $n1 = 0; 
  while($w1 = _fetch_array($r1))
  {	$DetailJudul .= "<th class=ttl>$w1[Nama]</br>$w1[Bobot]%</th>";
	$arrBobot[] = $w1['BobotRemedialID'];
	$n1++;
  }
  $DetailBobot = implode('<-!->', $arrBobot);
  $DetailJudul .= "<input type=hidden name='DetailBobot' value='$DetailBobot'>";
  
  if ($jdwl['Final'] == 'Y') {
    $ro = 'readonly=TRUE disabled=TRUE';
    $btnSimpan = '';
    $btnHitungUlang = '';
    $btnFinal = '';
    $btnGagal = '';
  }
  else {
    $ro = '';
    $btnSimpan = "<input type=submit name='SimpanSemua' value='Simpan Semua' />";
    $btnHitungUlang = "<input type=button name='Hitung' value='Hitung Nilai' onClick=\"location='?gos=HitungNilai&BypassMenu=1&id=$jdwl[JadwalRemedialID]'\" />";
    $btnFinal = "<input type=button name='Finalisasi' value='Finalisasi' onClick=\"javascript:Finalisasikan($jdwl[JadwalRemedialID])\" />";
    $btnGagal = "<input type=button name='Gagal' value='Gagal Penilaian' onClick=\"javascript:Gagalkan($jdwl[JadwalRemedialID])\" />";
    // Javascript
    echo <<<SCR
    <script>
    <!--
    function Finalisasikan(id) {
      lnk = "../$_SESSION[mnux].final.php?id="+id;
      win2 = window.open(lnk, "", "width=400, height=400, scrollbars, status");
    }
    function Gagalkan(id) {
      lnk = "../$_SESSION[mnux].gagal.php?id="+id;
      win2 = window.open(lnk, "", "width=400, height=440, scrollbars, status");
    }
    //-->
    </script>
SCR;
  }
  echo "<form action='?' method=POST>
    <input type=hidden name='gos' value='NilaiMhswSimpan' />
    <input type=hidden name='BypassMenu' value=1 />
    <input type=hidden name='id' value='$jdwl[JadwalRemedialID]' />";
  echo "<tr>
    <td class=ul colspan=15>
    $btnSimpan
    <input type=button name='Refresh' value='Refresh' onClick=\"location='?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$jdwl[JadwalID]'\" />
    $btnHitungUlang
    $btnFinal
    $btnGagal
    </td></tr>";
  echo "<tr>
    <th class=ttl>NIM</th>
    <th class=ttl>Mahasiswa</th>
    <th class=ttl title='Presensi Mahasiswa'>&sum;<br />PRS</th>
    <th class=ttl title='Nilai Presensi Mhsw'>PRS<br />$jdwl[Presensi]%</th>
    $DetailJudul
    <th class=ttl>Ujian<br />$jdwl[Ujian]%</th>
    <th class=ttl>Nilai<br />Akhir</th>
    <th class=ttl>Grade<br />&#9889;</th>
    </tr>";
	
  $wd = "width=30"; $nomer = 0;
  $jml = _num_rows($r);
  $jmlbobotlain = count($arrBobot);
  while ($w = _fetch_array($r)) {
    $nomer++;
    $_pr = $nomer;
    $_ua = $nomer + $jml * (1+$jmlbobotlain);
    $n = $w['KRSRemedialID'];

	$countPresensi = GetaField('presensi', 'JadwalID', $w['JadwalID'], 'count(PresensiID)');
	$Presensi = ($countPresensi == 0)? 0 : number_format($w['_Presensi']/$countPresensi*100, 0);
	
	$arrDetailNilai = AmbilArrayNilai($w['DetailNilai']);
	$Penilaian = "";
	$nb = 0;
	foreach($arrBobot as $b)
	{	$nb++;
		$ix = $nomer + $jml * $nb;
		$Penilaian .= "<td class=ul align=center><input type=text style='text-align:right' name='Nilai_".$b."_".$n."' value='".$arrDetailNilai[$b]."' size=2 maxlength=5 tabindex=$ix $ro></td>";
	}
	echo "<tr>
      <input type=hidden name='krsid[]' value='$w[KRSRemedialID]' />
      <input type=hidden name='KRS_$n' value='$w[KRSRemedialID]' />
      <input type=hidden name='DetailNilai_$n' value='$w[DetailNilai]' />
	  <td class=inp width=70>$w[MhswID]</td>
      <td class=ul>$w[NamaMhsw]</td>
      <td class=ul align=right>$w[_Presensi]<sup>&times;</sup></td>
      <td class=ul $wd align=center><input type=text style='text-align:right' name='Presensi_$n' value='$Presensi' size=2 maxlength=5 tabindex=$_pr readonly=true /></td>
      $Penilaian
	  <td class=ul $wd align=center><input type=text style='text-align:right' name='Ujian_$n' value='$w[Ujian]' size=2 maxlength=5 tabindex=$_ua $ro /></td>
	  <td class=ul align=center><b>$w[NilaiAkhir]</b></td>
      <td class=ul align=center><b>$w[GradeNilai] <sup>$w[BobotNilai]</sup></b></td>
	  </tr>";
  }
  echo "<input type=hidden name='JumlahMhsw' value='$jml' />";
  echo "</form></table>";
}
function AmbilArrayNilai($string)
{	$arrResult = array();
	if(!empty($string))
	{	$arr = explode('~!~', $string);
		foreach($arr as $a)
		{	$arrDetail = explode('!~!', $a);
			$arrResult[$arrDetail[0]] = $arrDetail[1];
		}
	}
	return $arrResult;
}

function BuatDetailNilai($arr)
{	
	if(!empty($arr))
	{	$tempArr = array();
		foreach($arr as $key=>$a)
		{	$tempArr[] = implode('!~!', array($key, $a));
			echo implode('!~!', array($key, $a));
		}
		echo "COUNT: ".count($tempArr);
		$result = '';
		foreach($tempArr as $ta)
		{	$result .= (empty($result))? $ta : "~!~".$ta;
		}
		
		return $result;
	}
	else
	{	return '';
	}
}

function NilaiMhswSimpan($jdwl) {
  $krsid = array();
  $krsid = $_REQUEST['krsid'];
  $arrBobot = explode('<-!->', $_REQUEST['DetailBobot']);
  
  foreach ($krsid as $id) {
    $Presensi = $_REQUEST['Presensi_'.$id]+0;
    $DetailNilai = $_REQUEST['DetailNilai_'.$id];
	$arrDetailNilai = AmbilArrayNilai($DetailNilai);
	
	foreach($arrBobot as $a)
	{	$Nilai = $_REQUEST['Nilai_'.$a.'_'.$id]+0;
		$arrDetailNilai[$a] = $Nilai;
	}
	
	$DetailNilaiFinal = BuatDetailNilai($arrDetailNilai);	
	
	$Ujian = $_REQUEST['Ujian_'.$id]+0;
    // Simpan
    $s = "update krsremedial
      set Presensi = '$Presensi',
          DetailNilai = '$DetailNilaiFinal', 
		  Ujian = '$Ujian',
          TanggalEdit = now(), LoginEdit = '$_SESSION[_Login]'
      where KRSRemedialID = '$id' ";
    $r = _query($s);
    //echo "<pre>$s</pre>";
  }
  BerhasilSimpan("?gos=Nilai2&id=$jdwl[JadwalRemedialID]", 1);
}
function HitungNilai($jdwl) {
  // Proses
  
  $s = "select * from krsremedial where JadwalRemedialID='$jdwl[JadwalRemedialID]' and NA='N' and KodeID='".KodeID."' order by MhswID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arrDetailNilai = AmbilArrayNilai($w['DetailNilai']);
	$s1 = "select * from bobotremedial where JadwalRemedialID='$jdwl[JadwalRemedialID]' and KodeID='".KodeID."' order by Urutan, Nama";
    $r1 = _query($s1);
	$totalBobotLain = 0;
	while($w1 = _fetch_array($r1))
	{	$totalBobotLain += $arrDetailNilai[$w1['BobotRemedialID']] * $w1['Bobot'];
	}
	
	$nilai = 
      $totalBobotLain + 
	  ($w['Presensi'] * $jdwl['Presensi']) +
      ($w['Ujian'] * $jdwl['Ujian'])
      ;
    $nilai = ($nilai / 100) +0;
    
    $ProdiID = GetaField('mhsw', "MhswID", $w['MhswID'], "ProdiID");
    $arrgrade = GetFields('nilai', 
      "KodeID='$_SESSION[KodeID]' and NilaiMin <= $nilai and $nilai <= NilaiMax and ProdiID",
      $ProdiID, "Nama, Bobot");
    // Simpan
    $s2 = "update krsremedial set NilaiAkhir='$nilai', GradeNilai='$arrgrade[Nama]', BobotNilai='$arrgrade[Bobot]'
      where KRSRemedialID=$w[KRSRemedialID] ";
    $r2 = _query($s2);
  }
  BerhasilSimpan("?gos=Nilai2&id=$jdwl[JadwalRemedialID]", 100);
}
?>
