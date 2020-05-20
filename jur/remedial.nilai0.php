<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 29 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Jadwal Remedial", 1);

// *** Parameters ***
$id = $_REQUEST['id'];
$jdwlrem = GetFields('jadwalremedial', "JadwalRemedialID='$id' and KodeID", KodeID, "*");

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'NilaiRemedial' : $_REQUEST['gos'];
$gos($jdwlrem);

// *** Functions ***

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
  $TanggalPenting = AmbilTanggalPenting($jdwl['JadwalRemedialID']);
  echo "<table class=box cellspacing=0 align=center width=840>
  <tr><td class=inp width=100>Thn Akademik:</td>
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
      <td class=ul>$jdwl[SKS], Peserta: $jdwl[JumlahMhsw] <sup title='Jumlah Mahasiswa'>&#2000;</sup></td>
      <td class=inp>Kelas:</td>
      <td class=ul>$jdwl[NamaKelas] <sup>$jdwl[ProgramID]</sup></td>
      </tr>
  <tr><td class=inp>Jdwl Kuliah:</td>
	  <td class=ul1>$TanggalPenting</td>
	  </tr>
  $FINAL
  </table>";
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
function NilaiRemedial($jdwl) {
  $s = "select k.*, m.Nama as NamaMhsw
    from krsremedial k
      left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where k.JadwalRemedialID = '$jdwl[JadwalRemedialID]'
      and k.NA = 'N'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  echo "<table class=box cellpadding=0 cellspacing=1 align=center width=840>";

  if ($jdwl['Final'] == 'Y') {
    $frm = '';
    $btnSimpan = '';
    $btnHitungUlang = '';
    $btnFinal = '';
    $btnGagal = '';
  }
  else {
    $frm = "<form action='?' method=POST>";
    $btnSimpan = "<input type=submit name='SimpanSemua' value='Simpan Semua' />";
    $btnHitungUlang = "<input type=button name='Hitung' value='Hitung Nilai' onClick=\"location='?mnux=$_SESSION[mnux]&gos=HitungNilai&BypassMenu=1&_nilaiJadwalID=$jdwl[JadwalID]'\" />";
    $btnFinal = "<input type=button name='Finalisasi' value='Finalisasi' onClick=\"javascript:Finalisasikan($jdwl[JadwalID])\" />";
    //$btnGagal = "<input type=button name='Gagal' value='Gagal Penilaian' onClick=\"javascript:Gagalkan($jdwl[JadwalID])\" />";
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
  echo "$frm
    <input type=hidden name='gos' value='NilaiRemedialSimpan' />
    <input type=hidden name='BypassMenu' value=1 />
    <input type=hidden name='_nilaiJadwalID' value='$jdwl[JadwalID]' />";
  echo "<tr>
    <td class=ul colspan=15>
    $btnSimpan
    <input type=button name='Refresh' value='Refresh' onClick=\"location='?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$jdwl[JadwalID]'\" />
    $btnHitungUlang
    $btnFinal
    $btnGagal
    </td></tr>";
  
  $DetailJudul = GetDetailJudul($jdwl);
  
  echo "<tr>
    <th class=ttl rowspan=2>NIM</th>
    <th class=ttl rowspan=2>Mahasiswa</th>
	<th class=ttl rowspan=2>Presensi
    <th class=ttl>Detail Nilai</th>
    <th class=ttl rowspan=2>Nilai<br />Akhir</th>
    <th class=ttl rowspan=2>Grade<br />&#9889;</th>
    </tr>
    <tr>
    $DetailJudul
    </tr>";
  $wd = "width=30"; $nomer = 0;
  $jml = _num_rows($r);
  while ($w = _fetch_array($r)) {
    $nomer++;
    
	$countPresensi = GetaField('presensiremedial', 'JadwalRemedialID', $w['JadwalRemedialID'], 'count(PresensiRemedialID)');
	$Presensi = ($countPresensi == 0)? 0 : number_format($w['_Presensi']/$countPresensi*100, 0);
    
	$DetailNilai = GetDetailNilai($jdwl, $w);
	echo "<tr>
      <input type=hidden name='krsid[]' value='$w[KRSID]' />
      <input type=hidden name='KRS_$n' value='$w[KRSID]' />
      <td class=inp width=70>$w[MhswID]</td>
      <td class=ul>$w[NamaMhsw]</td>
      <td class=ul align=right>$w[_Presensi] / $countPresensi</td>
      $DetailNilai
	  <td class=ul align=center><b>$w[NilaiAkhir]</b></td>
      <td class=ul align=center><b>$w[GradeNilai] <sup>$w[BobotNilai]</sup></b></td>
      </tr>";
  }
  echo "<input type=hidden name='JumlahMhsw' value='$jml' />";
  echo "</form></table>";
}

function GetDetailJudul($jdwl)
{	$result = '';	
	$ro = '';
	if($jdwl['Final'] == 'Y') $ro = 'readonly=TRUE disabled=TRUE';
	
	if(empty($jdwl['DetailJudul']))
	{	$result = "<th class=ttl align=center $ro><input type=text name='Title[]' value='Presensi' size=3 maxlength=5><br>
													<input type=text name='Persen[]' value='100' size=1 maxlength=5>%</th>";
	}
	else
	{
		$arrTitle = explode('<!!>', $jdwl['DetailJudul']);
		
		foreach($arrTitle as $title)
		{	$arrDetail = explode('<><>', $title);
			$result .= "<th class=ttl align=center $ro><input type=text name='Title[]' value='$arrDetail[0]' size=1 maxlength=5><br>
													<input type=text name='Persen[]' value='$arrDetail[1]' size=1 maxlength=5>%</th>";
		}
	}
	return $result;
}

function GetDetailNilai($jdwl, $krs)
{	$result = '';
	$ro = '';
	if($jdwl['Final'] == 'Y') $ro = 'readonly=TRUE disabled=TRUE';
	
	if(empty($jdwl['DetailJudul']))
	{	$result = "<td class=ul align=center $ro><input type=text name='Nilai[]' value='0' size=1 maxlength=5></td>";
	}
	else
	{	$arrTitle = explode('<!!>', $jdwl['DetailJudul']);
		$length = count($arrTitle);
		
		$arrNilai = explode('<!!>', $krs['DetailNilai']); 
		
		$n = 0;
		foreach($arrNilai as $nilai)
		{	$n++;
			if($n < $length) $result .= "<td class=ul align=center $ro><input type=text name='Nilai[]' value='$nilai' size=1 maxlength=5></td>";
		}
	}
	return $result;
}	

function NilaiRemedialSimpan() {
  $_nilaiJadwalID = $_REQUEST['_nilaiJadwalID'];
  $krsid = array();
  $krsid = $_REQUEST['krsid'];
  foreach ($krsid as $id) {
    $Presensi = $_REQUEST['Presensi_'.$id]+0;
    $Tugas1 = $_REQUEST['Tugas1_'.$id]+0;
    $Tugas2 = $_REQUEST['Tugas2_'.$id]+0;
    $Tugas3 = $_REQUEST['Tugas3_'.$id]+0;
    $Tugas4 = $_REQUEST['Tugas4_'.$id]+0;
    $Tugas5 = $_REQUEST['Tugas5_'.$id]+0;
    $UTS = $_REQUEST['UTS_'.$id]+0;
    $UAS = $_REQUEST['UAS_'.$id]+0;
    // Simpan
    $s = "update krs
      set Presensi = '$Presensi',
          Tugas1 = '$Tugas1', Tugas2 = '$Tugas2', Tugas3 = '$Tugas3',
          Tugas4 = '$Tugas4', Tugas5 = '$Tugas5',
          UTS = '$UTS', UAS = '$UAS',
          TanggalEdit = now(), LoginEdit = '$_SESSION[_Login]'
      where KRSID = $id ";
    $r = _query($s);
    //echo "<pre>$s</pre>";
  }
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
  while ($w = _fetch_array($r)) {
    $nilai = ($w['Tugas1'] * $jdwl['Tugas1']) +
      ($w['Tugas2'] * $jdwl['Tugas2']) +
      ($w['Tugas3'] * $jdwl['Tugas3']) +
      ($w['Tugas4'] * $jdwl['Tugas4']) +
      ($w['Tugas5'] * $jdwl['Tugas5']) +
      ($w['Presensi'] * $jdwl['Presensi']) +
      ($w['UTS'] * $jdwl['UTS']) +
      ($w['UAS'] * $jdwl['UAS'])
      ;
    $nilai = ($nilai / 100) +0;
    if ($jdwl['Responsi'] > 0) {
      $nilai = ($nilai * (100 - $jdwl['Responsi'])/100) +
        ($w['Responsi'] * ($jdwl['Responsi'])/100);
    }
    $ProdiID = GetaField('mhsw', "MhswID", $w['MhswID'], "ProdiID");
    $arrgrade = GetFields('nilai', 
      "KodeID='$_SESSION[KodeID]' and NilaiMin <= $nilai and $nilai <= NilaiMax and ProdiID",
      $ProdiID, "Nama, Bobot");
    // Simpan
    $s1 = "update krs set NilaiAkhir='$nilai', GradeNilai='$arrgrade[Nama]', BobotNilai='$arrgrade[Bobot]'
      where KRSID=$w[KRSID] ";
    $r1 = _query($s1);
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$jadwalid", 100);
}

function AmbilTanggalPenting($jrid)
{	$tanggalpentingstring = GetaField('jadwalremedial', "JadwalRemedialID='$jrid' and KodeID", KodeID, "TanggalPenting");
	$returnstring = '';

	$n = 0;
	if(!empty($tanggalpentingstring))
	{
		$arrTanggalPenting = explode('<|>', $tanggalpentingstring);
		
		foreach($arrTanggalPenting as $tanggal)
		{	$n++;
			$arr = explode('!/!', $tanggal);
			$tgl = $arr[0];
			$returnstring.= "&bull; $tgl - $arr[1]<br>";
		}
	}
	return $returnstring;
}
?>
