<?php


session_start();
include_once "../sisfokampus1.php";
error_reporting(0);
//ini_set('display_errors', 1);

HeaderSisfoKampus("Presensi", 1);

// *** Parameters ***
$jenis = $_REQUEST['jenis'];
$jid = $_REQUEST['jid']+0;
$pid = $_REQUEST['pid']+0;
$md = $_REQUEST['md']+0;

 // ===========================================================================================
	// kode penguncian transaksi presensi dimulai disini (selain superuser)
 // ===========================================================================================
if ($_SESSION['_LevelID'] != 1 && $_SESSION['_LevelID'] != 110 && $_SESSION['_LevelID'] != 100) {
    $timezone = "Asia/Jakarta";
	if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
	 $hariini=date('w');
	 $tanggal=date('Y-m-d');
     $JadwalID = $jid;
     $J = GetFields('jadwal',"JadwalID",$JadwalID,"JamMulai,HariID, ProdiID");
     $sekarang = date("H:i");
  	$selisih = _query("Select timediff('$sekarang','$J[JamMulai]') as Selisih, timediff('$sekarang','$J[JamMulai]')*1 as PM");
    
    while ($w0i = _fetch_array($selisih)) {
    $_selisih = $w0i['Selisih'];
    if ($w0i['PM'] < 0) $plusmin = -1;
    else $plusmin = 1;
    }
	

 list($hours, $mins, $secs) = explode(':', $_selisih);
 $frmSelisih = ($hours * 3600 ) + ($mins * 60 ) + $secs + 0;
 $jam = (int)$hours;
 $menit = (int)$mins;
 $selisihnya = $frmSelisih * $plusmin;
 $cekPresensi = GetaField('presensi',"Tanggal like '$tanggal' And JadwalID",$jid,'count(JadwalID)');
if (empty($cekPresensi)) {
  // ===
  //if (($selisihnya > -660) && ($selisihnya < 1260) && ($J[HariID]==$hariini))  
if ($J[HariID]==$hariini)  {

// *** Main ***
$rem = ($jenis=='remedial')? 'Rem' : '';
$jdl = ($md == 0)? "Edit Presensi ".$rem : "Tambah Presensi ".$rem;
TampilkanJudul($jdl);
$gos = (empty($_REQUEST['gos']))? 'Edit'.$rem : $_REQUEST['gos'];
$gos($md, $jid, $pid);
}
else {
die(ErrorMsg('Error',
      "Tidak bisa melanjutkan.<br />
	  bukan waktu entri presensi..."));
	  }
}
else {
die(ErrorMsg('Error',
      "Tidak bisa melanjutkan.<br />
	  hanya bisa melakukan 1 kali presensi pada hari yang sama..."));
	  }
}
else {
$rem = ($jenis=='remedial')? 'Rem' : '';
$jdl = ($md == 0)? "Edit Presensi ".$rem : "Tambah Presensi ".$rem;
TampilkanJudul($jdl);
$gos = (empty($_REQUEST['gos']))? 'Edit'.$rem : $_REQUEST['gos'];
$gos($md, $jid, $pid);
}
// *** Functions ***
function Edit($md, $jid, $pid) {
  PresensiScript();
echo '
<link type="text/css" href="../datepicker2/datePicker.css" rel="stylesheet" />	
<script type="text/javascript" src="../datepicker2/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../datepicker2/date-id.js"></script>
<!--[if IE]>
<script type="text/javascript" src="../datepicker2/jquery.bgiframe.js"></script>
<![endif]-->
<script type="text/javascript" src="../datepicker2/jquery.datePicker.js"></script>
';

$s2 = "select date_format(TglKuliahMulai, '%d')+0 as _fromday, date_format(TglKuliahMulai, '%m')+0 as _frommonth, date_format(TglKuliahMulai, '%Y')+0 as _fromyear,
	date_format(TglKuliahSelesai, '%d')+0 as _today, date_format(TglKuliahSelesai, '%m')+0 as _tomonth, date_format(TglKuliahSelesai, '%Y')+0 as _toyear
	from tahun where NA = 'N' order by TahunID DESC limit 1";
$q2 = _query($s2);
$w2 = (_fetch_array($q2));

$start = $w2[_fromyear].",".($w2[_frommonth]).",".$w2[_fromday];
$end = $w2[_toyear].','.($w2[_tomonth]).','.$w2[_today];
//var_dump($start.$end);

echo "
<script>
function setDatePicker(selector,rangeSelector,stat){
	var dt = $('#alt'+selector).val().replace('-',',');
	dt = dt.replace('-',',');
	
	$('#'+selector).datePicker({startDate:'01/01/1990'});
	$('#'+selector).datePicker().val(new Date(dt).asString()).trigger('change');
	$('#'+selector).dpSetPosition($.dpConst.POS_TOP, $.dpConst.POS_RIGHT);
	
	if (rangeSelector != ''){
		var dts = $('#'+rangeSelector).val().replace('-',',');
		dts = dts.replace('-',',');
		
		if (dts) {
			dts = new Date(dts);
			
			if (stat == 'end'){
				$('#'+selector).dpSetEndDate(dts.addDays(-1).asString());
			} else if (stat == 'start'){
				$('#'+selector).dpSetStartDate(dts.addDays(1).asString());
			}
			
		}
		
		// bind to event
		$('#'+rangeSelector).bind(
			'dpClosed',
			function(e, selectedDates)
			{
				var year = selectedDates[0].getFullYear();
				var mon = selectedDates[0].getMonth()+1;
				var day = selectedDates[0].getDate();
				var realvalue = year+'-'+mon+'-'+day;
				$('#alt'+rangeSelector).val(realvalue);
				
				var d = selectedDates[0];
				if (d) {
					d = new Date(d);
					if (stat == 'end'){
						$('#'+selector).dpSetEndDate(d.addDays(-1).asString());
					} else if (stat == 'start'){
						$('#'+selector).dpSetStartDate(d.addDays(1).asString());
					}
				}
			}
		)
	}	
}

$(function()
{
	Date.format = 'dd mmmm yyyy';
	setDatePicker('Tanggal','Tanggal','');
		var dts = '".$start."';
		if (dts) {
			dts = new Date(dts);
				$('#Tanggal').dpSetStartDate(dts.addDays(0).asString());
			}
		var dts = '".$end."';
		if (dts) {
			dts = new Date(dts);
				$('#Tanggal').dpSetEndDate(dts.addDays(0).asString());
			}
});
</script>";

function GetDateOption3($value,$name){
	$a = "<input type=hidden name=".$name." id=alt".$name." value=".$value." /><input type=text id=".$name." value=".$value." readonly=true />";
	return $a;
}


  $jdwl = GetFields('jadwal', 'JadwalID', $jid, '*');
  if ($md == 0) {
    $w = GetFields('presensi', 'PresensiID', $pid, "*");
    $w['JamMulai'] = substr($w['JamMulai'], 0, 5);
    $w['JamSelesai'] = substr($w['JamSelesai'], 0, 5);
  }
  elseif ($md == 1) {
    $w = array();
    $w['Pertemuan'] = GetaField('presensi', "JadwalID", $jid, "max(Pertemuan)")+1;
    $w['JamMulai'] = substr($jdwl['JamMulai'], 0, 5);
    $w['JamSelesai'] = substr($jdwl['JamSelesai'], 0, 5);
	// Script lama yang dinonaktifkan
	//------------------------------------------------------------------------------------------------------
    //$w['Tanggal'] = date('Y-m-d', strtotime( '+'.(($w['Pertemuan']-1)).'day', strtotime(date('d M Y'))));
	//------------------------------------------------------------------------------------------------------
	$w['Tanggal']=date('Y-m-d',strtotime(date('d M Y')));
    $w['DosenID'] = $jdwl['DosenID'];
  }
  else Die(ErrorMsg('Error',
    "Mode edit: <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Parameter
  $opttgl = GetDateOption3($w['Tanggal'], 'Tanggal');
  $optJamMulai = GetTimeOption($w['JamMulai'], 'JamMulai');
  $optJamSelesai = GetTimeOption($w['JamSelesai'], 'JamSelesai');
  $optdsn = GetDosenJadwal($jdwl, $w['DosenID']);
  $arrHari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
  $hr = $arrHari[date('w', strtotime($w['Tanggal']))+0];
  
  // Tampilkan
  CheckFormScript('DosenID');
echo '
  <script>
  	function cekJdwl(){
	
		var fromHour = document.forms[0].JamMulai_h.value;
		var fromMinutes = document.forms[0].JamMulai_n.value;

		var toHour = document.forms[0].JamSelesai_h.value;
		var toMinutes = document.forms[0].JamSelesai_n.value;
		
		var d4 = new Date();
		d4.setHours(fromHour);
		d4.setMinutes(fromMinutes);
		
		var fromJam = d4.getTime();
		
		var d5 = new Date();
		d5.setHours(toHour);
		d5.setMinutes(toMinutes);
		
		var toJam = d5.getTime();
		
		
		var errmsg = "";
		
		if (fromJam >= toJam){
			errmsg += $w[Tanggal]."Jam kuliah mulai harus lebih awal dari jam kuliah selesai\\n"
		}
		if (errmsg != ""){
			alert (errmsg);
			return false;
		}
	}
  </script>';

  echo "<table class=box cellspacing=1 width=100%>
  <form name='frmPresensi' action='../$_SESSION[mnux].edit.php' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='jid' value='$jid' />
  <input type=hidden name='pid' value='$pid' />
  
  <tr><td class=inp>Pertemuan Ke:</td>
      <td class=ul><input type=text name='Pertemuan' value='$w[Pertemuan]' size=4 maxlength=5 /></td>
      </tr>
  <tr><td class=inp>Tanggal:</td>
      <td class=ul>$opttgl</td>
      </tr>
  <tr><td class=inp>Jam Kuliah:</td>
      <td class=ul>
        $optJamMulai &#8594;
        $optJamSelesai
      </td></tr>
  <tr><td class=inp>Pemberi Kuliah:</td>
      <td class=ul><select name='DosenID'>$optdsn</select></td>
      </tr>
  <tr><td class=inp>Materi:</td>
      <td class=ul>
        <textarea name='Catatan' cols=40 rows=6>".nl2br($w['Catatan'])."</textarea>
      </td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' onclick='return cekJdwl()' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  </table>";
}
function EditRem($md, $jid, $pid) {
  PresensiScript();
  $jdwl = GetFields('jadwalremedial', 'JadwalRemedialID', $jid, '*');
  if ($md == 0) {
    $w = GetFields('presensi', 'PresensiID', $pid, "*, date_format(Tanggal, '%w') as HR");
  $w['JamMulai'] = $_REQUEST[JamMulai];
  $w['JamSelesai'] = $_REQUEST[JamSelesai];
	$hr = $w[HR];
  }
  elseif ($md == 1) {
    $w = array();
    $w['Pertemuan'] = GetaField('presensi', "JadwalRemedialID", $jid, "max(Pertemuan)")+1;
  $w['JamMulai'] = $_REQUEST[JamMulai];
  $w['JamSelesai'] = $_REQUEST[JamSelesai];
    $w['Tanggal'] = date('Y-m-d');
    $w['DosenID'] = $jdwl['DosenID'];
  }
  else Die(ErrorMsg('Error',
    "Mode edit: <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Parameter
  $opttgl = GetDateOption($w['Tanggal'], 'Tanggal');
  $optdsn = GetDosenJadwal($jdwl, $w['DosenID']);
  // Tampilkan
  CheckFormScript('DosenID,JamMulai,JamSelesai');
  echo "<table class=box cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].edit.php' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='SimpanRemedial' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='jid' value='$jid' />
  <input type=hidden name='pid' value='$pid' />
  
  <tr><td class=inp>Pertemuan Ke:</td>
      <td class=ul><input type=text name='Pertemuan' value='$w[Pertemuan]' size=4 maxlength=5 /></td>
      </tr>
  <tr><td class=inp>Tanggal:</td>
      <td class=ul>$opttgl</td>
      </tr>
  <tr><td class=inp>Jam Kuliah:</td>
      <td class=ul>
        <input type=text name='JamMulai' value='$w[JamMulai]' size=5 maxlength=5 />
        &#8594;
        <input type=text name='JamSelesai' value='$w[JamSelesai]' size=5 maxlength=5 /> $hr
      </td></tr>
  <tr><td class=inp>Pemberi Kuliah:</td>
      <td class=ul><select name='DosenID'>$optdsn</select></td>
      </tr>
  <tr><td class=inp>Materi:</td>
      <td class=ul>
        <textarea name='Catatan' cols=50 rows=6>$w[Catatan]</textarea>
      </td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  </table>";
}
function Simpan($md, $jid, $pid) {
  $Pertemuan = $_REQUEST['Pertemuan']+0;
  $Tanggal = "$_REQUEST[Tanggal]";
  $DosenID = $_REQUEST['DosenID'];
  $JamMulai = "$_REQUEST[JamMulai_h]:$_REQUEST[JamMulai_n]";
  $JamSelesai = "$_REQUEST[JamSelesai_h]:$_REQUEST[JamSelesai_n]";
  $Catatan = sqling($_REQUEST['Catatan']);
  // Simpan
  if ($md == 0) {
    $s = "update presensi
      set Pertemuan = '$Pertemuan',
          Tanggal = '$Tanggal',
          JamMulai = '$JamMulai', JamSelesai = '$JamSelesai',
          Catatan = '$Catatan',
		  DosenID='$DosenID',
          LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
      where PresensiID = '$pid' ";
    $r = _query($s);
    HitungPresensi($jid);
    TutupScript($jid);
  }
  elseif ($md == 1) {
    $jdwl = GetFields('jadwal', 'JadwalID', $jid, '*');
    $s = "insert into presensi
      (TahunID, JadwalID, Pertemuan, DosenID,
      Tanggal, JamMulai, JamSelesai, Catatan,
      LoginBuat, TanggalBuat)
      values
      ('$jdwl[TahunID]', $jid, $Pertemuan, '$DosenID',
      '$Tanggal', '$JamMulai', '$JamSelesai', '$Catatan',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    $pid = GetLastID();
    HitungPresensi($jid);
    TutupScript($jid);
  }
  else die(ErrorMsg('Error', "Ada kesalahan. Mode edit tidak dikenali."));
}
function SimpanRemedial($md, $jid, $pid) {
  $Pertemuan = $_REQUEST['Pertemuan']+0;
  $Tanggal = "$_REQUEST[Tanggal]";
  $DosenID = $_REQUEST['DosenID'];
  $w['JamMulai'] = "$_REQUEST[JamMulai_h]:$_REQUEST[JamMulai_n]";
  $w['JamSelesai'] = "$_REQUEST[JamSelesai_h]:$_REQUEST[JamSelesai_n]";
  $Catatan = sqling($_REQUEST['Catatan']);
  // Simpan
  if ($md == 0) {
    $s = "update presensi
      set Pertemuan = '$Pertemuan',
          Tanggal = '$Tanggal',
          JamMulai = '$JamMulai', JamSelesai = '$JamSelesai',
          Catatan = '$Catatan',
          LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
      where PresensiID = '$pid' ";
    $r = _query($s);
    HitungPresensi($jid);
    TutupScript($jid);
  }
  elseif ($md == 1) {
    $jdwl = GetFields('jadwalremedial', 'JadwalRemedialID', $jid, '*');
    $s = "insert into presensi
      (TahunID, JadwalRemedialID, Pertemuan, DosenID,
      Tanggal, JamMulai, JamSelesai, Catatan,
      LoginBuat, TanggalBuat)
      values
      ('$jdwl[TahunID]', $jid, $Pertemuan, '$DosenID',
      '$Tanggal', '$JamMulai', '$JamSelesai', '$Catatan',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    $pid = GetLastID();
    HitungPresensiRem($jid);
    TutupScriptRem($jid);
  }
  else die(ErrorMsg('Error', "Ada kesalahan. Mode edit tidak dikenali."));
}
function HitungPresensi($jid) {
  // Hitung kehadiran
  $jml = GetaField('presensi', "JadwalID", $jid, "count(PresensiID)")+0;
  // update jadwal
  $s = "update jadwal
    set Kehadiran = '$jml'
    where JadwalID = '$jid' ";
  $r = _query($s);
}
function HitungPresensiRem($jid) {
  // Hitung kehadiran
  $jml = GetaField('presensi', "JadwalRemedialID", $jid, "count(PresensiID)")+0;
  // update jadwal
  $s = "update jadwalremedial
    set Kehadiran = '$jml'
    where JadwalRemedialID = '$jid' ";
  $r = _query($s);
}
function GetDosenJadwal($jdwl, $def='') {
  $Nama = GetaField('dosen', "KodeID='".KodeID."' and Login", $jdwl['DosenID'], "concat(Nama, ', ', Gelar)");
  $arr = array();
  $arr[] = '';
  $arr[] = "$jdwl[DosenID]~$Nama";
  // Ambil dosen2 lain
  $s = "select jd.DosenID, concat(d.Nama, ', ', d.Gelar) as _Nama
    from jadwaldosen jd
      left outer join dosen d on d.Login = jd.DosenID and d.KodeID = '".KodeID."'
    where jd.JadwalID = '$jdwl[JadwalID]'
    order by d.Nama";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arr[] = "$w[DosenID]~$w[_Nama]";
  }
  // buat menjadi opsi
  $opt = '';
  foreach ($arr as $a) {
    $isi = explode('~', $a);
    $sel = ($def == $isi[0])? 'selected' : '';
    $opt .= "<option value='$isi[0]' $sel>$isi[1]</option>";
  }
  return $opt;
}
function GetDateOption2($dt, $nm='dt',$loc='') {
  $arr = Explode('-', $dt);
  $_dy = GetNumberOption(1, 31, $arr[2]);
  $_mo = GetMonthOption($arr[1]);
  $_yr = GetNumberOption(1930, Date('Y')+2, $arr[0]);
  return "<select name='".$nm."_d' onChange=\"$loc\">$_dy</select>
    <select name='".$nm."_m' onChange=\"$loc\">$_mo</select>
    <select name='".$nm."_y' onChange=\"$loc\">$_yr</select>";
}
function TutupScript($jid) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=Edit&JadwalID=$jid';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
function TutupScriptRem($jid) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=EditRem&JadwalRemedialID=$jid';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
function PresensiScript()
{ echo " 
  <script>
  function AmbilHari()
  {	  var theDate = new Date();
	  var hariArray = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
		theDate.setFullYear(Number(frmPresensi.Tanggal_y.value), Number(frmPresensi.Tanggal_m.value)-1, Number(frmPresensi.Tanggal_d.value));
		
		frmPresensi.HariPresensi.value = hariArray[theDate.getDay()];
  }
  </script>";
}

?>
