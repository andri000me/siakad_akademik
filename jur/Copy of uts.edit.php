<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 14 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Jadwal Kuliah", 1);

// *** infrastruktur **
echo <<<SCR
  <script src="../$_SESSION[mnux].edit.script.js"></script>
SCR;

// *** Parameters ***
$_utsTahun = GetSetVar('_utsTahun');
$_utsProdi = GetSetVar('_utsProdi');
$_utsProg  = GetSetVar('_utsProg');

// *** Special Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0;
$jutsid = $_REQUEST['jutsid']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $jutsid);

// *** Functions ***
function Edit($md, $id, $jutsid) {
  $w = GetFields('jadwal', 'JadwalID', $id, '*');
  $w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
  $w['_JM'] = substr($w['JamMulai'], 0, 5);
  $w['_JS'] = substr($w['JamSelesai'], 0, 5);
  $prodi = GetFields('prodi', "ProdiID='$_SESSION[_utsProdi]' and KodeID", KodeID, "*");
  $tahun = GetFields('tahun', "TahunID='$_SESSION[_utsTahun]' and ProdiID='$_SESSION[_utsProdi]' and ProgramID='$_SESSION[_utsProg]' and KodeID", KodeID, "*");
  
  if ($md == 0) {
	$jdl = "Edit Jadwal UTS";
	$jadwaluts = GetFields('jadwaluts', 'JadwalUTSID', $jutsid, '*');
	$w['UTSTanggal'] = $jadwaluts['Tanggal'];
	$w['UTSJamMulai'] = substr($jadwaluts['JamMulai'], 0, 5);
	$w['UTSJamSelesai'] = substr($jadwaluts['JamSelesai'], 0, 5);
	$w['UTSDosenID'] = $jadwaluts['DosenID'];
	$w['UTSDosen'] = GetaField('dosen', "Login='$jadwaluts[DosenID]' and KodeID", KodeID, 'Nama');
	$w['UTSRuangID'] = $jadwaluts['RuangID'];
	$w['UTSKapasitas'] = $jadwaluts['Kapasitas'];
	$w['UTSKolomUjian'] = $jadwaluts['KolomUjian'];
	$w['UTSBarisUjian'] = ceil($jadwaluts['Kapasitas'] / $jadwaluts['KolomUjian']);
  }
  elseif ($md == 1) {
	$jdl = "Tambah Jadwal UTS";
	$w['UTSTanggal'] = $tahun['TglUTSMulai'];
	$w['UTSJamMulai'] = '09:00';
	$w['UTSJamSelesai'] = '09:50';
  }
  else {
	die(ErrorMsg("Error", "Mode tidak dikenali")); 
  }
  // Parameters
  JdwlUTSScript();
echo '
<link type="text/css" href="../datepicker2/datePicker.css" rel="stylesheet" />	
<script type="text/javascript" src="../datepicker2/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../datepicker2/date-id.js"></script>
<!--[if IE]>
<script type="text/javascript" src="../datepicker2/jquery.bgiframe.js"></script>
<![endif]-->
<script type="text/javascript" src="../datepicker2/jquery.datePicker.js"></script>
';

$s2 = "select date_format(TglUTSMulai, '%d')+0 as _fromday, date_format(TglUTSMulai, '%m')+0 as _frommonth, date_format(TglUTSMulai, '%Y')+0 as _fromyear,
	date_format(TglUTSSelesai, '%d')+0 as _today, date_format(TglUTSSelesai, '%m')+0 as _tomonth, date_format(TglUTSSelesai, '%Y')+0 as _toyear
	from tahun where NA = 'N' and TahunID = '$_SESSION[_utsTahun]' and ProdiID = '$_SESSION[_utsProdi]' and ProgramID = '$_SESSION[_utsProg]'";
$q2 = _query($s2);
$w2 = (_fetch_array($q2));

$start = $w2[_fromyear].",".($w2[_frommonth]).",".$w2[_fromday];
$end = $w2[_toyear].','.($w2[_tomonth]).','.$w2[_today];


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
	setDatePicker('UTSTanggal','UTSTanggal','');
		var dts = '".$start."';
		if (dts) {
			dts = new Date(dts);
				$('#UTSTanggal').dpSetStartDate(dts.addDays(0).asString());
			}
		var dts = '".$end."';
		if (dts) {
			dts = new Date(dts);
				$('#UTSTanggal').dpSetEndDate(dts.addDays(0).asString());
			}
});
</script>";

function GetDateOption3($value,$name){
	$a = "<input type=hidden name=".$name." id=alt".$name." value=".$value." /><input type=text id=".$name." value=".$value." readonly=true />";
	return $a;
}

  //$opttgluts = GetDateOption3($w['UTSTanggal'], 'UTSTanggal');  
  $opttgluts = GetDateOption($w['UTSTanggal'], 'UTSTanggal');
  $optJamMulai = GetTimeOption($w['UTSJamMulai'], 'UTSJamMulai');
  $optJamSelesai = GetTimeOption($w['UTSJamSelesai'], 'UTSJamSelesai');
  $NamaHari = GetaField('hari', 'HariID', $w['HariID'], 'Nama');
  // Tampilkan
  CheckFormScript("UTSRuangID");
  
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
			errmsg += "Jam ujian mulai harus lebih awal dari jam kuliah selesai\\n"
		}
		if (errmsg != ""){
			alert (errmsg);
			return false;
		}
	}
  </script>';
  
  TampilkanJudul($jdl);
  echo <<<END 
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmJadwalUTS' action='../$_SESSION[mnux].edit.php' method=POST onSubmit=return CheckForm(this)>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='TahunID' value='$_SESSION[_utsTahun]' />
  <input type=hidden name='ProdiID' value='$_SESSION[_utsProdi]' />
  <input type=hidden name='ProgramID' value='$_SESSION[_utsProg]' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='jutsid' value='$jutsid' />
  
  <tr><td class=inp>Program Studi:</td>
      <td class=ul1><b>$prodi[Nama]</b> <sup>($_SESSION[_utsProdi])</sup></td>
      <td class=inp>Program:</td>
      <td class=ul1>$w[ProgramID]</td>
      </tr>
  <tr><td class=inp>Tanggal Mulai Kuliah:</td>
      <td class=ul1>$w[KuliahTanggal] <sup>$NamaHari</sup></td>
	  <td class=inp>Jam Kuliah:</td>
      <td class=ul1>
        $w[_JM] &#8594; $w[_JS]
        </td>
      </tr>
  <tr><td class=inp>Ruang:</td>
      <td class=ul1>$w[RuangID]</td>
      <td class=inp>Kapasitas:</td>
      <td class=ul1>$w[Kapasitas]<sub>orang</sub></td>
      </tr>

  <tr><td class=inp>Matakuliah:</td>
      <td class=ul1 colspan=3 nowrap>$w[Nama] <sup>$w[MKKode]</sup></td>
      </tr>
  <tr><td class=inp>Dosen Pengajar:</td>
      <td class=ul1>$w[Dosen] <sup>$w[DosenID]</sup></td>
      <td class=inp>Kelas:</td>
      <td class=ul1>$w[NamaKelas]</td>
      </tr>
  <tr><td colspan=4><hr color=silver size=3></td></tr>
  <tr><td class=inp>Tanggal UTS:</td>
      <td class=ul1 nowrap>$opttgluts</td>
      <td class=inp>Jam UTS:</td>
      <td class=ul1 nowrap>
        $optJamMulai &#8594;
        $optJamSelesai
      </td></tr>
	  
  <tr><td class=inp>Ruang UTS:</td>
      <td class=ul1>
        <input type=text name='UTSRuangID' value='$w[UTSRuangID]' size=10 maxlength=50 
          onKeyUp=javascript:CariRuang('$_SESSION[_utsProdi]', 'frmJadwalUTS','$w[Kapasitas]') />
        &raquo;
      <a href='#'
        onClick="javascript:CariRuang('$_SESSION[_utsProdi]', 'frmJadwalUTS','$w[Kapasitas]')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwalUTS.UTSRuangID.value=''">Reset</a>
        </td>
      <td class=inp>Kapasitas:</td>
      <td class=ul1>
        <input type=text name='UTSKapasitas' value='$w[UTSKapasitas]' size=4 maxlength=5 />
        <sub>orang</sub>
        </td>
      </tr>
  <tr><td class=inp>Kolom Ujian:</td>
	  <td class=ul1><input type=text name='UTSKolomUjian' value='$w[UTSKolomUjian]' onChange="HitungBaris('frmJadwalUTS')" size=1 maxlength=2 />
	  <td class=inp>Baris Ujian:</td>
	  <td class=ul1><input type=text name='UTSBarisUjian' value='$w[UTSBarisUjian]' size=1 maxlength=2 disabled />
  </tr>
  
  <tr><td class=inp>Dosen Pengawas:</td>
      <td class=ul1 colspan=3 nowrap>
      <input type=text name='UTSDosenID' value='$w[DosenID]' size=10 maxlength=50 readonly='TRUE' />
      <input type=text name='UTSDosen' value='$w[Dosen]' size=30 maxlength=50 readonly='TRUE' onKeyUp="javascript:CariDosen('$_SESSION[_utsProdi]', 'frmJadwalUTS')" />
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[_utsProdi]', 'frmJadwalUTS')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwalUTS.UTSDosenID.value='';frmJadwalUTS.UTSDosen.value=''">Reset</a>
      </div>
      </td>
      </tr>
	  
	  
<tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' onclick='return cekJdwl()' />
      <input type=button name='Batal' value='Batal' onClick=window.close() />
      </td></tr>
  </form>
  </table>
  
  <div class='box0' id='cariruang'></div>
  <div class='box0' id='caridosen'></div>
END;
}

function Simpan($md, $id, $jutsid) {
  $w['UTSTanggal'] = "$_REQUEST[UTSTanggal_y]-$_REQUEST[UTSTanggal_m]-$_REQUEST[UTSTanggal_d]";
  $w['UTSJamMulai'] = "$_REQUEST[UTSJamMulai_h]:$_REQUEST[UTSJamMulai_n]";
  $w['UTSJamSelesai'] = "$_REQUEST[UTSJamSelesai_h]:$_REQUEST[UTSJamSelesai_n]";
  $w['UTSRuangID'] = $_REQUEST['UTSRuangID'];
  $w['UTSKapasitas'] = $_REQUEST['UTSKapasitas'];
  $w['UTSKolomUjian'] = $_REQUEST['UTSKolomUjian'];
  $w['UTSDosenID'] = $_REQUEST['UTSDosenID2'];
  $w['UTSRuangID2'] = $_REQUEST['UTSRuangID2'];
  $w['UTSKapasitas2'] = $_REQUEST['UTSKapasitas2'];
  $w['UTSKolomUjian2'] = $_REQUEST['UTSKolomUjian2'];
  $w['UTSDosenID2'] = $_REQUEST['UTSDosenID2'];
  $w['JadwalID'] = $id;
  $w['TahunID'] = $_REQUEST['TahunID'];
  $w['ProdiID'] = $_REQUEST['ProdiID'];
  $w['ProgramID'] = $_REQUEST['ProgramID'];
  
  // *** Cek semuanya dulu ***
   $cekada = GetFields('tahun', "NA='N' and TahunID='$w[TahunID]' 
            and ProdiID = '$w[ProdiID]' 
            and left(TglUTSMulai,10)<= '$w[UTSTanggal]' and 
            left(TglUTSSelesai,10)>= '$w[UTSTanggal]' and ProgramID","$w[ProgramID]",
            "*");
  $cek = GetFields('tahun', "NA='N' and TahunID='$w[TahunID]' 
            and ProdiID = '$w[ProdiID]' and ProgramID","$w[ProgramID]",
            "date_format(TglUTSMulai,'%d %M %Y') as Mulai, date_format(TglUTSSelesai,'%d %M %Y') as Selesai");
  
  if(empty($cekada)){
    die(ErrorMsg('Kesalahan Tanggal', 
      "Tanggal yang anda setting tidak sesuai dengan Tanggal penjadwalan UTS,<br/>
       yaitu dari Tanggal : <b>$cek[Mulai]</b> sampai dengan <b>$cek[Selesai]</b>.
      <hr size=1 color=silver />
      <p align=center>
      <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
  }
  
  $oke = '';
  if (!empty($w['UTSRuangID'])) $oke .= CekRuang($w, $jutsid);
  //$oke .= CekTanggal($w, $jutsid);
  
  // Ambil data MK
  $mk = GetFields('mk', "MKID", $w['MKID'], "Nama,MKKode,KurikulumID,SKS,Sesi");
  // Jika semuanya baik2 saja
  if (empty($oke)) {
    // Jika mode=edit
    if ($md == 0) {
      $s = "update jadwaluts
        set RuangID = '$w[UTSRuangID]',
			DosenID = '$w[UTSDosenID]',
            Kapasitas = '$w[UTSKapasitas]',
            Tanggal = '$w[UTSTanggal]',
            JamMulai = '$w[UTSJamMulai]', JamSelesai = '$w[UTSJamSelesai]',
			KolomUjian = '$w[UTSKolomUjian]',
			TanggalEdit = now(),
            LoginEdit = '$_SESSION[_Login]'
        where JadwalUTSID = '$jutsid' ";
      $r = _query($s);
      
	  DaftarkanMhswKeRuangUTS($w, $jutsid);
	  
	  TutupScript();
    }
    elseif ($md == 1) {
      $s = "insert into jadwaluts
        (KodeID, TahunID, JadwalID, DosenID, 
        Tanggal, JamMulai, JamSelesai,
		Kapasitas, RuangID, KolomUjian, TanggalBuat, LoginBuat)
        values
        ('".KodeID."', '$w[TahunID]', '$w[JadwalID]', '$w[UTSDosenID]',
        '$w[UTSTanggal]', '$w[UTSJamMulai]', '$w[UTSJamSelesai]',
		'$w[UTSKapasitas]', '$w[UTSRuangID]', '$w[UTSKolomUjian]', now(), '$_SESSION[_Login]')";
      $r = _query($s);
	  
	  $JadwalUTSID1 = mysql_insert_id();
	  
	  $s = "insert into jadwaluts
        (KodeID, TahunID, JadwalID, DosenID, 
        Tanggal, JamMulai, JamSelesai,
		Kapasitas, RuangID, KolomUjian, TanggalBuat, LoginBuat)
        values
        ('".KodeID."', '$w[TahunID]', '$w[JadwalID]', '$w[UTSDosenID2]',
        '$w[UTSTanggal]', '$w[UTSJamMulai]', '$w[UTSJamSelesai]',
		'$w[UTSKapasitas2]', '$w[UTSRuangID2]', '$w[UTSKolomUjian2]', now(), '$_SESSION[_Login]')";
      $r = _query($s);
	  
	  $JadwalUTSID2 = mysql_insert_id();
	  
	  DaftarkanMhswKeRuangUTS($w, $JadwalUTSID1, $JadwalUTSID2);
	  
      TutupScript();
    }
  }
  // Jika ada yg salah
  else {
	die(ErrorMsg('Ada Kesalahan', 
      "Berikut adalah pesan kesalahannya: 
      <ol>$oke</ol>
      <hr size=1 color=silver />
      <p align=center>
      
	  <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
  }
}
function CekRuang($w, $JadwalUTSID) {
  
  $s = "select j.JadwalUTSID, j.JamMulai, j.JamSelesai, j.DosenID, 
    d.Nama as NamaDosen, j.JadwalID, j2.Nama, j2.MKKode, j2.ProdiID, j2.ProgramID, 
	p.Nama as _PRG, pr.Nama as _PRD, date_format(j.Tanggal, '%d-%m-%y') as _Tanggal 
    from jadwaluts j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join jadwal j2 on j.JadwalID = j2.JadwalID and j2.KodeID = '".KodeID."'
	  left outer join program p on p.ProgramID = j2.ProgramID and p.KodeID = '".KodeID."'
      left outer join prodi pr on pr.ProdiID = j2.ProdiID and pr.KodeID = '".KodeID."'
    where j.TahunID = '$w[TahunID]'
      and j.RuangID = '$w[UTSRuangID]'
      and j.Tanggal = '$w[UTSTanggal]'
      and (('$w[UTSJamMulai]:00' <= j.JamMulai and j.JamMulai <= '$w[UTSJamSelesai]:59')
      or  ('$w[UTSJamMulai]:00' <= j.JamSelesai and j.JamSelesai <= '$w[UTSJamSelesai]:59'))
      and j.NA = 'N'
	  and j.KodeID='".KodeID."'
      and j.JadwalUTSID <> '$JadwalUTSID' ";
  //die("<pre>$s</pre>");
  $r = _query($s);
  $a = '';
  while ($w = _fetch_array($r)) {
    $a .= "<li>
      <b>Jadwal UTS bentrok dengan</b>:<br />
      <table class=bsc width=400>
      <tr><td class=inp width=100>Matakuliah:</td>
          <td class=ul1>$w[Nama] <sup>($w[MKKode])</td>
          </tr>
	  <tr><td class=inp>Tanggal:</td>
          <td class=ul1>$w[_Tanggal]</td>
          </tr>
      <tr><td class=inp>Jam:</td>
          <td class=ul1>$w[JamMulai] &minus; $w[JamSelesai]</td>
          </tr>
      <tr><td class=inp>Dosen:</td>
          <td class=ul1>$w[NamaDosen]</td>
          </tr>
      <tr><td class=inp>Program Studi:</td>
          <td class=ul1>$w[_PRD] <sup>($w[ProdiID])</sup></td>
          </tr>
      <tr><td class=inp>Prg Pendidikan:</td>
          <td class=ul1>$w[_PRG] <sup>($w[ProgramID])</sup></td>
          </tr>
      </table>
      </li>";
  }
  return $a;
}

function CekTanggal($w, $jutsid)
{ $uts = GetFields('tahun', "TahunID='$w[TahunID]' and ProgramID='$w[ProgramID]' and ProdiID='$w[ProdiID]' and KodeID", KodeID, "LEFT(TglUTSMulai, 10) as _TglUTSMulai, LEFT(TglUTSSelesai, 10) as _TglUTSSelesai");

  $a = '';
  if ($uts['_TglUTSMulai'] > $w['UTSTanggal'] or $w['UTSTanggal'] > $uts['_TglUTSSelesai'])
  {
    $a .= "<li>
      <b>Tanggal UTS ".$w[UTSTanggal]." berada di luar Tanggal UTS yang direncanakan</b>:<br />
		 Rentang waktu yang disediakan: ".$uts['_TglUTSMulai']." s/d ".$uts['_TglUTSSelesai']."<br />
      ";
  }
  return $a;
}
function DaftarkanMhswKeRuangUTS($w, $JadwalUTSID1, $JadwalUTSID2)
{	//echo "JADWALUTSID=$JadwalUTSID1 ,JADWALID=$w[JadwalID]<br>";
	if($JadwalUTSID1+0 != 0)
    {   
		$limit1= $w['UTSKapasitas']+0;
		$s6 = "select MhswID from krs where JadwalID='$w[JadwalID]' and KodeID='".KodeID."' limit 0,$limit1";
		$batas = $w['UTSKapasitas']+0;
		$r2 = _query($s6);
		$n2 = _num_rows($r2);
		echo "JUMLAH: $n2<br>";
		while($w2 = _fetch_array($r2))
		{   //Cek apakah mahasiswa sudah terdaftar
			$ada = GetaField('utsmhsw', "MhswID='$w2[MhswID]' and JadwalUTSID='$JadwalUTSID1' and KodeID", KodeID, "UTSMhswID");
			if(empty($ada))
			{	$s1 = "insert into utsmhsw
				(KodeID, MhswID, JadwalUTSID, TahunID, RuangID, TanggalBuat, LoginBuat)
				values
				('".KodeID."', '$w2[MhswID]', '$JadwalUTSID1', '$w[TahunID]', '$w[UTSRuangID]', now(), '$_SESSION[_Login]')";
				$r1 = _query($s1);
			}
		}
		$limit2= $w['UTSKapasitas2']+0;
		$s4 = "select MhswID from krs where JadwalID='$w[JadwalID]' and KodeID='".KodeID."' limit $batas, $limit2";
		$r4 = _query($s4);
		$n4 = _num_rows($r4);
		echo "JUMLAH: $n2<br>";
		while($w4 = _fetch_array($r4))
		{   //Cek apakah mahasiswa sudah terdaftar
			$ada = GetaField('utsmhsw', "MhswID='$w4[MhswID]' and JadwalUTSID='$JadwalUTSID2' and KodeID", KodeID, "UTSMhswID");
			if(empty($ada))
			{	$s3 = "insert into utsmhsw
				(KodeID, MhswID, JadwalUTSID, TahunID, RuangID, TanggalBuat, LoginBuat)
				values
				('".KodeID."', '$w4[MhswID]', '$JadwalUTSID2', '$w[TahunID]', '$w[UTSRuangID2]', now(), '$_SESSION[_Login]')";
				$r3 = _query($s3);
			}
		}
	}
	else
	die(ErrorMsg("Error", "Tidak ditemukan Jadwal UTS yang dimaksud. Harap menghubungi yang berwenang."));
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
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
function JdwlUTSScript() {
  echo <<<SCR
  <script>
  function toggleBox(szDivID, iState) // 1 visible, 0 hidden
  {
    if(document.layers)	   //NN4+
    {
       document.layers[szDivID].visibility = iState ? "show" : "hide";
    }
    else if(document.getElementById)	  //gecko(NN6) + IE 5+
    {
        var obj = document.getElementById(szDivID);
        obj.style.visibility = iState ? "visible" : "hidden";
    }
    else if(document.all)	// IE 4
    {
        document.all[szDivID].style.visibility = iState ? "visible" : "hidden";
    }
  }
  function CariRuang(ProdiID, frm, kapasitasR) {
    if (eval(frm + ".UTSRuangID.value != ''")) {
      eval(frm + ".UTSRuangID.focus()");
      showRuang(ProdiID, frm, eval(frm +".UTSRuangID.value"), 'cariruang', kapasitasR);
      toggleBox('cariruang', 1);
    }
  }
  
    function CariRuang2(ProdiID, frm, kapasitasR) {
    if (eval(frm + ".UTSRuangID2.value != ''")) {
      eval(frm + ".UTSRuangID2.focus()");
      showRuang(ProdiID, frm, eval(frm +".UTSRuangID2.value"), 'cariruang', kapasitasR);
      toggleBox('cariruang', 1);
    }
  }
  
  function CariDosen(ProdiID, frm) {
    if (eval(frm + ".UTSDosen2.value != ''")) {
      eval(frm + ".UTSDosen2.focus()");
      showDosen(ProdiID, frm, eval(frm +".UTSDosen2.value"), 'caridosen');
      toggleBox('caridosen', 1);
    }
  }
  function HitungBaris(frm)
  {  var kapasitas, kolom;
	 if(eval(frm + ".UTSKapasitas.value == ''")) kapasitas = 0;
	 else kapasitas = parseInt(eval(frm + ".UTSKapasitas.value"));
	 if(eval(frm + ".UTSKolomUjian.value != ''") && eval(frm + ".UTSKolomUjian.value != 0")) 
	 {	kolom = parseInt(eval(frm + ".UTSKolomUjian.value"));
		if(kolom != 0)
		{	baris = Math.ceil(kapasitas/kolom);
			eval(frm + ".UTSBarisUjian.value = " + baris);
		}
	 }	 
	 else 
	 {	eval(frm + ".UTSKolomUjian.value = 1");
		eval(frm + ".UTSBarisUjian.value = " + kapasitas);
	 } 
  }
  </script>
SCR;
}
?>

</BODY>
</HTML>
