<?php

session_start(); error_reporting(0);
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Jadwal Kuliah", 1);

// *** infrastruktur **
echo <<<SCR
  <script src="../$_SESSION[mnux].edit.script.js"></script>
SCR;

// *** Parameters ***
$_jdwlTahun = GetSetVar('_jdwlTahun');
$_jdwlProdi = GetSetVar('_jdwlProdi');
$_jdwlProg  = GetSetVar('_jdwlProg');

// *** Special Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0;
$Kembali = $_REQUEST['Kembali']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $Kembali);

// *** Functions ***
function Edit($md, $id, $Kembali) {
  if($Kembali == 1)
  {	  if ($md == 0) $jdl = "Edit Jadwal";
	  elseif ($md == 1) $jdl = "Tambah Jadwal";
	  else {
	  }
		$w = array();
		$w['ProgramID'] = $_REQUEST['ProgramID'];
		$w['MKID'] = $_REQUEST['MKID'];
		$w['MKKode'] = $_REQUEST['MKKode'];
		$w['Nama'] = $_REQUEST['MKNama'];
		$w['SKS'] = $_REQUEST['SKS'];
		$w['NamaKelas'] = $_REQUEST['NamaKelas'];
		$w['RencanaKehadiran'] = $_REQUEST['RencanaKehadiran'];
		$w['KehadiranMin'] = $_REQUEST['KehadiranMin'];
		$w['MaxAbsen'] = $_REQUEST['MaxAbsen'];
		$w['TglMulai'] = $_REQUEST['TglMulai'];
		$w['TglSelesai'] = $_REQUEST['TglSelesai'];
		$w['Kapasitas'] = $_REQUEST['Kapasitas'];
		$w['HariID'] = $_REQUEST['HariID'];
		$w['RuangID'] = $_REQUEST['RuangID'];
		$w['BiayaKhusus'] = $_REQUEST['BiayaKhusus'];
		$w['Biaya'] = $_REQUEST['Biaya'];
		$w['NamaBiaya'] = $_REQUEST['NamaBiaya'];
		$w['DosenID'] = $_REQUEST['DosenID'];
		$w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
		$w['JamMulai'] = substr($_REQUEST['JamMulai'], 0, 5);
		$w['JamSelesai'] = substr($_REQUEST['JamSelesai'], 0, 5);
		$w['AdaResponsi'] = $_REQUEST['AdaResponsi'];
        $w['NA'] = $_REQUEST['NA'];
	  }
  else
  {
	  if ($md == 0) {
		$jdl = "Edit Jadwal";
		$w = GetFields('jadwal', 'JadwalID', $id, '*');
		$w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
		$w['JamMulai'] = substr($w['JamMulai'], 0, 5);
		$w['JamSelesai'] = substr($w['JamSelesai'], 0, 5);
	  }
	  elseif ($md == 1) {
		$jdl = "Tambah Jadwal";
		$w = array();
		$w['Dosen'] = '';
		$w['ProgramID'] = $_SESSION['_jdwlProg'];
		$w['RencanaKehadiran'] = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $_SESSION['_jdwlProdi'], 'DefKehadiran');
		$w['MaxAbsen'] = floor(0.3 * $w['RencanaKehadiran']);
		$w['KehadiranMin'] = $w['RencanaKehadiran'];
		$w['TglMulai'] = date('Y-m-d');
		$w['TglSelesai'] = date('Y-m-d');
		$w['HariID'] = date('w');
		$w['AdaResponsi'] = 'N';
		$w['BiayaKhusus'] = 'N';
        $w['NA'] = 'N';
	  }
	  else {
	  }
  }
  
  	$q = "select * from kelas where ProdiID = '".$_SESSION[_jdwlProdi]."' group by Nama order by Nama";
	$m = _query($q);
		if (_num_rows($m) == 0) {
			$optkelas = "";
		} else {
		  $optkelas = "<option value='' $sel>&nbsp;</option>";
			while ($x = _fetch_array($m)){
				$sel = ($w['NamaKelas'] == $x[KelasID])? 'selected=selected' : '';
				$optkelas .= "<option value=$x[KelasID] $sel>$x[Nama]</option>";
			}
		}

echo '
<link type="text/css" href="../datepicker2/datePicker.css" rel="stylesheet" />	
<script type="text/javascript" src="../datepicker2/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../datepicker2/date-id.js"></script>
<!--[if IE]>
<script type="text/javascript" src="../datepicker2/jquery.bgiframe.js"></script>
<![endif]-->
<script type="text/javascript" src="../datepicker2/jquery.datePicker.js"></script>
';

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
	setDatePicker('TglMulai','TglSelesai','end');
	setDatePicker('TglSelesai','TglMulai','start');
		
});
</script>";
function GetDateOption3($value,$name){
	$a = "<input type=hidden name=".$name." id=alt".$name." value=".$value." /><input type=text id=".$name." value=".$value." readonly=true />";
	return $a;
}

  // Parameters
  JdwlScript();
  $prodi = GetFields('prodi', "KodeID='".KodeID."' and ProdiID", $_SESSION['_jdwlProdi'], '*');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID',
    $w['ProgramID'], "KodeID='".KodeID."'", 'ProgramID');
  $opthari = GetOption2('hari', "Nama", 'HariID', $w['HariID'], '', 'HariID');
  //$opttglmulai = GetDateOption3($w['TglMulai'], 'TglMulai');
  //$opttglselesai = GetDateOption3($w['TglSelesai'], 'TglSelesai');
  $opttglmulai = GetDateOption2($w['TglMulai'], 'TglMulai', "AmbilHari('TglMulai', 'frmJadwal')");
  $opttglselesai = GetDateOption2($w['TglSelesai'], 'TglSelesai', "");
  $optJamMulai = GetTimeOption($w['JamMulai'], 'JamMulai');
  $optJamSelesai = GetTimeOption($w['JamSelesai'], 'JamSelesai');
  $ck_adaresponsi = ($w['AdaResponsi'] == 'Y')? 'checked' : '';
  $ck_biayakhusus = ($w['BiayaKhusus'] == 'Y')? 'checked' : '';
  $ck_NA = ($w['NA'] == 'Y')? 'checked' : '';
  // Tampilkan
  CheckFormScript("ProgramID,HariID,DosenID,MKID");
  TampilkanJudul($jdl);
  
  $ck_krs = GetaField('krs',"JadwalID",$id,"COUNT(KRSID)")+0;
  $ro = ($ck_krs>0 && $id>0) ? "readonly=true disabled":"";
  
$s2 = "select date_format(TglKuliahMulai, '%d')+0 as _fromday, date_format(TglKuliahMulai, '%m')+0 as _frommonth, date_format(TglKuliahMulai, '%Y')+0 as _fromyear,
	date_format(TglKuliahSelesai, '%d')+0 as _today, date_format(TglKuliahSelesai, '%m')+0 as _tomonth, date_format(TglKuliahSelesai, '%Y')+0 as _toyear
	from tahun where NA = 'N' and TahunID = '$_SESSION[_jdwlTahun]' and ProdiID = '$_SESSION[_jdwlProdi]' and ProgramID = '$_SESSION[_jdwlProg]'";
$q2 = _query($s2);
$w2 = (_fetch_array($q2));

$start = $w2[_fromyear]."/".($w2[_frommonth])."/".$w2[_fromday];
$end = $w2[_toyear].'/'.($w2[_tomonth]).'/'.$w2[_today];

echo '
  <script>
  	function cekJdwl(start,end){
	
		var kuliahMulai = getDateTime("TglMulai");
		var kuliahSelesai = getDateTime("TglSelesai");
				
		var RkuliahMulai = Date.parse(start);
		var RkuliahSelesai = Date.parse(end);
		
		
		
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
		
		var kapasitas = document.forms[0].Kapasitas.value;
		
		var errmsg = "";
		
		if (kuliahMulai < RkuliahMulai || kuliahMulai > RkuliahSelesai || kuliahSelesai < RkuliahMulai || kuliahSelesai > RkuliahSelesai){
			errmsg += "Tanggal kuliah harus berada pada masa kuliah sesuai Tahun Akademik\\n"
		}
		
		if (fromJam >= toJam){
			errmsg += "Jam kuliah mulai harus lebih awal dari jam kuliah selesai\\n"
		}
		if (kapasitas == 0){
			errmsg += "Kapasitas tidak boleh bernilai 0\\n"
		}
		if (errmsg != ""){
			alert (errmsg);
			return false;
		}
	}
  </script>';

echo <<<END
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmJadwal' action='../$_SESSION[mnux].edit.php' method=POST onSubmit="return CheckForm(this)">
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='TahunID' value='$_SESSION[_jdwlTahun]' />
  <input type=hidden name='ProdiID' value='$_SESSION[_jdwlProdi]' />
  <input type=hidden name='id' value='$id' />
  
  <tr><td class=inp>Program Studi:</td>
      <td class=ul1><b>$prodi[Nama]</b> <sup>($_SESSION[_jdwlProdi])</sup></td>
      <td class=inp>Program:</td>
      <td class=ul1><select name='ProgramID'>$optprg</select></td>
      </tr>
  <!--<tr><td class=inp>Tanggal Mulai Kuliah:</td>
      <td class=ul1>$opttglmulai</td>
	  <td class=inp>Tanggal Selesai Kuliah:</td>
	  <td class=ul1>$opttglselesai</td>
	  </tr>-->
  <tr><td class=inp>Hari:</td>
      <td class=ul1><select name='HariID' >$opthari</select></td>
      <td class=inp>Jam Kuliah:</td>
      <td class=ul1>
        $optJamMulai &#8594;
        $optJamSelesai
        </td>
      </tr>
  <tr><td class=inp>Ruang:</td>
      <td class=ul1>
        <input type=text name='RuangID' value='$w[RuangID]' size=10 maxlength=50 
          onKeyUp="javascript:CariRuang('$_SESSION[_jdwlProdi]', 'frmJadwal','$_SESSION[_jdwlTahun]')" />
        &raquo;
      <a href='#'
        onClick="javascript:CariRuang('$_SESSION[_jdwlProdi]', 'frmJadwal','$_SESSION[_jdwlTahun]')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwal.RuangID.value=''">Reset</a>
        </td>
      <td class=inp>Kapasitas:</td>
      <td class=ul1>
        <input type=hidden name='Kapasitas' value='$w[Kapasitas]' size=4 maxlength=5 />
        <input type=text name='Kapasitas2' value='$w[Kapasitas]' size=4 maxlength=5  />
        <sub>orang</sub>
        </td>
      </tr>

  <tr><td class=inp>Matakuliah:</td>
      <td class=ul1 colspan=3 nowrap>
      <input type=hidden name='MKID' value='$w[MKID]' />
      <input type=text name='MKKode' value='$w[MKKode]' size=10 maxlength=50 />
      <input type=text name='MKNama' value='$w[Nama]' size=30 maxlength=50 onKeyUp="javascript:CariMK('$_SESSION[_jdwlProdi]', 'frmJadwal')" />
      <input type=hidden name='SKS' value='$w[SKS]' size=3 maxlength=3>
      <input type=text name='SKS2' value='$w[SKS]' size=3 maxlength=3 disabled="disabled"> <sub>SKS</sub>
	  <br><b><font color=green size=0.9em>Ada Responsi/Lab? </font></b><input type=checkbox name='AdaResponsi' value='Y' $ck_adaresponsi>
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariMK('$_SESSION[_jdwlProdi]', 'frmJadwal')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwal.MKID.value='';frmJadwal.MKKode.value='';frmJadwal.MKNama.value='';frmJadwal.SKS.value=0">Reset</a>
      </div>
      </td>
      </tr>

  <tr><td class=inp>Dosen Pengampu:</td>
      <td class=ul1 colspan=3 nowrap>
      <input type=text name='DosenID' value='$w[DosenID]' size=10 maxlength=50 />
      <input type=text name='Dosen' value='$w[Dosen]' size=30 maxlength=50 onKeyUp="javascript:CariDosen('$_SESSION[_jdwlProdi]', 'frmJadwal')" />
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[_jdwlProdi]', 'frmJadwal')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwal.DosenID.value='';frmJadwal.Dosen.value=''">Reset</a>
      </div>
      </td>
      </tr>

  <tr><td class=inp>Kelas:</td>
      <td class=ul1><select name='NamaKelas'>$optkelas</select></td>
      </tr>
  <tr><td class=inp>Rencana Kehadiran Dosen:</td>
      <td class=ul1><input type=text name='RencanaKehadiran' value='$w[RencanaKehadiran]' size=4 maxlength=4 /></td>
      <td class=inp>Maksimum Absen:</td>
      <td class=ul1><input type=text name='MaxAbsen' value='$w[MaxAbsen]' size=4 maxlength=4 /></td>
      </tr>
  <tr><td class=inp>Ada Biaya Khusus?</td>
      <td class=ul1>
        <input type=checkbox name='BiayaKhusus' value='Y' $ck_biayakhusus /> &raquo;
        Biaya:
        <input type=text name='Biaya' value='$w[Biaya]' size=10 maxlength=20 />
      </td>
	  <td class=inp>Nama Biaya:</td>
	  <td class=ul1><input type=text name='NamaBiaya' value='$w[NamaBiaya]' size=30 maxlength=100></td>
      </tr>
  <tr>
  		<td class=inp>Disembunyikan?</td>
        <td class=ul1 colspan=3>
      <input type=checkbox name='NA' value='Y' $ck_NA /> <sup>*) Bila dicentang, Mahasiswa tidak dapat melihat jadwal ini pada saat mengisi KRS</sup>
      </td></tr>
  
  <tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' onclick="return cekJdwl('$start','$end')" />
      <input type=button name='Batal' value='Batal' onClick="window.close()" />
      </td></tr>
  </form>
  </table>

  <div class='box0' id='caridosen'></div>
  <div class='box0' id='carimk'></div>
  <div class='box0' id='cariruang'></div>
END;
}
function JdwlScript() {
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
  function CariDosen(ProdiID, frm) {
    if (eval(frm + ".Dosen.value != ''")) {
      eval(frm + ".Dosen.focus()");
      showDosen(ProdiID, frm, eval(frm +".Dosen.value"), 'caridosen');
      toggleBox('caridosen', 1);
    }
  }
  function CariMK(ProdiID, frm) {
    if (eval(frm + ".MKNama.value != ''")) {
      eval(frm + ".MKNama.focus()");
      showMK(ProdiID, frm, eval(frm +".MKNama.value"), 'carimk');
      toggleBox('carimk', 1);
    }
  }
  function CariRuang(ProdiID, frm, tahun) {
    if (eval(frm + ".RuangID.value != ''")) {
      eval(frm + ".RuangID.focus()");
      showRuang(ProdiID, frm, eval(frm +".RuangID.value"), eval(frm +".HariID.value"),eval(frm +".JamMulai_h.value"), eval(frm +".JamMulai_n.value"), eval(frm +".JamSelesai_h.value"),eval(frm +".JamSelesai_n.value"), tahun, 'cariruang');
      toggleBox('cariruang', 1);
    }
  }
  function AmbilHari(name, frm)
  {	  var theDate = new Date();
		
		theDate.setFullYear(Number(eval(frm+'.'+name+'_y.value')), Number(eval(frm+'.'+name+'_m.value'))-1, Number(eval(frm+'.'+name+'_d.value')));

		frmJadwal.HariID.value = theDate.getDay();
  }
  </script>
SCR;
}
function GabungkanScript($md, $id, $w) {
  echo <<<ESD
  <script>
  function Gabung() {
    window.location="../$_SESSION[mnux].edit.php?gos=Simpan&md=$md&id=$id&TahunID=$w[TahunID]"+
      "&ProgramID=$w[ProgramID]&ProdiID=$w[ProdiID]&HariID=$w[HariID]&JamMulai=$w[JamMulai]&JamSelesai=$w[JamSelesai]"+
      "&RuangID=$w[RuangID]&Kapasitas=$w[Kapasitas]&MKID=$w[MKID]&MKKode=$w[MKKode]&MKNama=$w[MKNama]&SKS=$w[SKS]&DosenID=$w[DosenID]"+
      "&NamaKelas=$w[NamaKelas]&RencanaKehadiran=$w[RencanaKehadiran]"+
      "&KehadiranMin=$w[KehadiranMin]&MaxAbsen=$w[MaxAbsen]"+
      "&BiayaKhusus=$w[BiayaKhusus]&Biaya=$w[Biaya]&NamaBiaya=$w[NamaBiaya]&TglMulai=$w[TglMulai]&TglSelesai=$w[TglSelesai]"+
      "&_Gabungkan=721222";
  }
  function Kembali() {
    window.location="../$_SESSION[mnux].edit.php?gos=Edit&md=$md&id=$id&TahunID=$w[TahunID]&Kembali=1"+
      "&ProgramID=$w[ProgramID]&ProdiID=$w[ProdiID]&HariID=$w[HariID]&JamMulai=$w[JamMulai]&JamSelesai=$w[JamSelesai]"+
      "&RuangID=$w[RuangID]&Kapasitas=$w[Kapasitas]&MKID=$w[MKID]&MKKode=$w[MKKode]&MKNama=$w[MKNama]&SKS=$w[SKS]&DosenID=$w[DosenID]"+
      "&NamaKelas=$w[NamaKelas]&RencanaKehadiran=$w[RencanaKehadiran]"+
      "&KehadiranMin=$w[KehadiranMin]&MaxAbsen=$w[MaxAbsen]"+
      "&BiayaKhusus=$w[BiayaKhusus]&Biaya=$w[Biaya]&NamaBiaya=$w[NamaBiaya]&TglMulai=$w[TglMulai]&TglSelesai=$w[TglSelesai]";
  }
  </script>
ESD;
}
function Simpan($md, $id, $Kembali) {
  $_Gabungkan = $_REQUEST['_Gabungkan']+0;
  $w = array();
  $w['TahunID'] = sqling($_REQUEST['TahunID']);
  $w['ProgramID'] = sqling($_REQUEST['ProgramID']);
  $w['ProdiID'] = sqling($_REQUEST['ProdiID']);
  $w['HariID'] = $_REQUEST['HariID'];
  $w['JamMulai'] = "$_REQUEST[JamMulai_h]:$_REQUEST[JamMulai_n]";
  $w['JamSelesai'] = "$_REQUEST[JamSelesai_h]:$_REQUEST[JamSelesai_n]";
  $w['RuangID'] = sqling($_REQUEST['RuangID']);
  $w['Kapasitas'] = $_REQUEST['Kapasitas2']+0;
  $w['MKID'] = $_REQUEST['MKID'];
  $w['MKKode'] = $_REQUEST['MKKode'];
  $w['MKNama'] = $_REQUEST['MKNama'];
  $w['SKS'] = $_REQUEST['SKS']+0;
  $w['DosenID'] = $_REQUEST['DosenID'];
  $w['NamaKelas'] = sqling($_REQUEST['NamaKelas']);
  $w['RencanaKehadiran'] = $_REQUEST['RencanaKehadiran']+0;
  $w['KehadiranMin'] = $_REQUEST['KehadiranMin']+0;
  $w['MaxAbsen'] = $_REQUEST['MaxAbsen']+0;
  if ($_Gabungkan == 721222) {
    $w['TglMulai'] = sqling($_REQUEST['TglMulai']);
	$w['TglSelesai'] = sqling($_REQUEST['TglSelesai']);
  }
  else {
	$w['TglMulai'] = "$_REQUEST[TglMulai_y]-$_REQUEST[TglMulai_m]-$_REQUEST[TglMulai_d]";
	$w['TglSelesai'] = "$_REQUEST[TglSelesai_y]-$_REQUEST[TglSelesai_m]-$_REQUEST[TglSelesai_d]";
  }
  if ($_REQUEST['BiayaKhusus'] == 'Y') {
    $w['BiayaKhusus'] = 'Y';
    $w['Biaya'] = $_REQUEST['Biaya']+0;
	$w['NamaBiaya'] = $_REQUEST['NamaBiaya'];
  }
  else {
    $w['BiayaKhusus'] = 'N';
    $w['Biaya'] = 0;
	$w['NamaBiaya'] = '';
  }
  
  $w['AdaResponsi'] = ($_REQUEST['AdaResponsi'] == 'Y')? 'Y' : 'N';
  $w['NA'] = ($_REQUEST['NA'] == 'Y')? 'Y' : 'N';
  
  // Validasi
  if (empty($w['MKID']))
    die(ErrorMsg('Error',
      "Matakuliah belum dipilih.<br />
      Ambil matakuliah di fasiltas pencarian untuk melengkapi data matakuliah.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // *** parameters ***
  $DosenAda = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
  if (empty($DosenAda))
    die(ErrorMsg('Error',
      "Dosen dengan kode: <b>$w[DosenID]</b> tidak ditemukan.<br />
      Masukkan Dosen dengan fasilitas pencarian.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  
  // *** Cek semuanya dulu ***
  $oke = '';
  $Fakultas = GetaField('prodi',"ProdiID", $w['ProdiID'],"FakultasID");
  if ($Fakultas != '08') {
  if (!empty($w['RuangID'])) $oke .= CekRuang($w, $id);
  $oke .= CekDosen($w, $id);
  $oke .= CekGroup($w, $id);
  }
  // Ambil data MK
  $mk = GetFields('mk', "MKID", $w['MKID'], "Nama,MKKode,KurikulumID,SKS,Sesi");
  // Jika semuanya baik2 saja
  if (empty($oke) || $_Gabungkan == 721222) {
    // Jika mode=edit
    if ($md == 0) {
      $s = "update jadwal
        set ProgramID = '$w[ProgramID]',
            MKID = '$w[MKID]',
            MKKode = '$mk[MKKode]',
            Nama = upper('$mk[Nama]'),
            NamaKelas = upper('$w[NamaKelas]'),
            HariID = '$w[HariID]',
            JamMulai = '$w[JamMulai]',
            JamSelesai = '$w[JamSelesai]',
            SKS = '$w[SKS]',
            SKSAsli = '$mk[SKS]',
            DosenID = '$w[DosenID]',
            RencanaKehadiran = '$w[RencanaKehadiran]',
            KehadiranMin = '$w[KehadiranMin]',
			       MaxAbsen = '$w[MaxAbsen]',
            RuangID = '$w[RuangID]',
            Kapasitas = '$w[Kapasitas]',
            TglMulai = '$w[TglMulai]',
			TglSelesai = '$w[TglSelesai]',
			AdaResponsi = '$w[AdaResponsi]',
			BiayaKhusus = '$w[BiayaKhusus]',
            Biaya = '$w[Biaya]',
            NA = '$w[NA]',
			NamaBiaya = '$w[NamaBiaya]',
            TglEdit = now(),
            LoginEdit = '$_SESSION[_Login]'
        where JadwalID = '$id' and JadwalID > 0 ";
      $r = _query($s);

      $s = "update krs
        set 
            MKID = '$w[MKID]',
            MKKode = '$mk[MKKode]',
            Nama = upper('$mk[Nama]'),
            SKS = '$w[SKS]',
            TanggalEdit = now(),
            LoginEdit = '$_SESSION[_Login]'
        where JadwalID = '$id' and JadwalID > 0 ";
      $r = _query($s);
	  
	  // Tambahan untuk update kuliah tambahan lab/responsi/tutorial
	  $s = "update jadwal
        set ProgramID = '$w[ProgramID]',
            MKID = '$w[MKID]',
            MKKode = '$mk[MKKode]',
            Nama = upper('$mk[Nama]'),
            NamaKelas = upper('$w[NamaKelas]'),
            SKS = '$w[SKS]',
            SKSAsli = '$mk[SKS]',
            TglEdit = now(),
            LoginEdit = '$_SESSION[_Login]'
        where JadwalRefID = '$id' ";
      //$r = _query($s);
      TutupScript();
    }
    elseif ($md == 1) {
      $s = "insert into jadwal
        (KodeID, TahunID, ProdiID, ProgramID,
        NamaKelas, MKID, MKKode, Nama, TglMulai, TglSelesai,
        HariID, JamMulai, JamSelesai, SKSAsli, SKS,
        DosenID, RencanaKehadiran, KehadiranMin, MaxAbsen, 
        AdaResponsi, BiayaKhusus, Biaya, NamaBiaya, 
        Kapasitas, RuangID, TglBuat, LoginBuat,NA)
        values
        ('".KodeID."', '$w[TahunID]', '$w[ProdiID]', '$w[ProgramID]',
        upper('$w[NamaKelas]'), '$w[MKID]', '$mk[MKKode]', upper('$mk[Nama]'), '$w[TglMulai]', '$w[TglSelesai]',
        '$w[HariID]', '$w[JamMulai]', '$w[JamSelesai]', '$mk[SKS]', '$w[SKS]',
        '$w[DosenID]', '$w[RencanaKehadiran]', '$w[KehadiranMin]', '$w[MaxAbsen]',
        '$w[AdaResponsi]', '$w[BiayaKhusus]', '$w[Biaya]', '$w[NamaBiaya]',
        '$w[Kapasitas]', '$w[RuangID]', now(), '$_SESSION[_Login]','$w[NA]')";
      $r = _query($s);
      TutupScript();
    }
  }
  // Jika ada yg salah
  else {
    GabungkanScript($md, $id, $w);
    die(ErrorMsg('Ada Kesalahan', 
      "Berikut adalah pesan kesalahannya: 
      <ol>$oke</ol>
      <hr size=1 color=silver />
      <p align=center>
      <input type=button name='Kembali' value='Kembali' onClick=\"javascript:Kembali()\" />
	  <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
	    //    <input type=button name='Paksakan' value='Gabungkan Jadwal'
       // onClick='javascript:Gabung()' />

  }
}
function CekRuang($w, $JadwalID) {
  $s = "select j.JadwalID, j.MKKode, j.Nama, j.JamMulai, j.JamSelesai, j.DosenID, j.SKS,
    j.ProdiID, j.ProgramID,
    d.Nama as NamaDosen, j.JenisJadwalID,
    p.Nama as _PRG, pr.Nama as _PRD
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join program p on p.ProgramID = j.ProgramID and p.KodeID = '".KodeID."'
      left outer join prodi pr on pr.ProdiID = j.ProdiID and pr.KodeID = '".KodeID."'
    where j.TahunID = '$w[TahunID]'
      and j.RuangID = '$w[RuangID]'
      and j.HariID = '$w[HariID]'
      and (('$w[JamMulai]:00' <= j.JamMulai and j.JamMulai <= '$w[JamSelesai]:59')
      or  ('$w[JamMulai]:00' <= j.JamSelesai and j.JamSelesai <= '$w[JamSelesai]:59'))
      and j.KodeID='".KodeID."'
	  and j.NA = 'N'
      and j.JadwalID <> '$JadwalID' ";
  //die("<pre>$s</pre>");
  $r = _query($s);
  $a = '';
  while ($w = _fetch_array($r)) {
    $a .= "<li>
      <b>Jadwal Ruang Kelas bentrok dengan</b>:<br />
      <table class=bsc width=400>
      <tr><td class=inp width=100>Matakuliah:</td>
          <td class=ul1>$w[Nama] <sup>($w[MKKode])</td>
          </tr>
      <tr><td class=inp>Jenis Kuliah:</td>
          <td class=ul1>$w[JenisJadwalID]&nbsp;</td>
          </tr>
      <tr><td class=inp>Jam:</td>
          <td class=ul1>$w[JamMulai] &minus; $w[JamSelesai]</td>
          </tr>
      <tr><td class=inp>Dosen:</td>
          <td class=ul1>$w[NamaDosen]</td>
          </tr>
      <tr><td class=inp>SKS:</td>
          <td class=ul1>$w[SKS]&nbsp;</td>
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
function CekGroup($w, $JadwalID) {
  $s = "select j.JadwalID, j.MKKode, j.Nama, j.JamMulai, j.JamSelesai, j.DosenID, j.SKS,
    j.ProdiID, j.ProgramID,
    k.Nama as NamaKelas, j.JenisJadwalID,
    p.Nama as _PRG, pr.Nama as _PRD
    from jadwal j
      left outer join kelas k on k.KelasID = j.NamaKelas and k.KodeID = '".KodeID."'
      left outer join program p on p.ProgramID = j.ProgramID and p.KodeID = '".KodeID."'
      left outer join prodi pr on pr.ProdiID = j.ProdiID and pr.KodeID = '".KodeID."'
    where j.TahunID = '$w[TahunID]'
      and j.NamaKelas = '$w[NamaKelas]'
      and j.HariID = '$w[HariID]'
      and (('$w[JamMulai]:00' <= j.JamMulai and j.JamMulai <= '$w[JamSelesai]:59')
      or  ('$w[JamMulai]:00' <= j.JamSelesai and j.JamSelesai <= '$w[JamSelesai]:59'))
      and j.KodeID='".KodeID."'
	  and j.NA = 'N'
      and j.ProgramID='R'
      and j.JadwalID <> '$JadwalID' ";
  //die("<pre>$s</pre>");
  $r = _query($s);
  $a = '';
  while ($w = _fetch_array($r)) {
    $a .= "<li>
      <b>Jadwal Kelas bentrok dengan</b>:<br />
      <table class=bsc width=400>
      <tr><td class=inp width=100>Matakuliah:</td>
          <td class=ul1>$w[Nama] <sup>($w[MKKode])</td>
          </tr>
      <tr><td class=inp>Jenis Kuliah:</td>
          <td class=ul1>$w[JenisJadwalID]&nbsp;</td>
          </tr>
      <tr><td class=inp>Jam:</td>
          <td class=ul1>$w[JamMulai] &minus; $w[JamSelesai]</td>
          </tr>
      <tr><td class=inp>Kelas:</td>
          <td class=ul1>$w[NamaKelas]</td>
          </tr>
      <tr><td class=inp>SKS:</td>
          <td class=ul1>$w[SKS]&nbsp;</td>
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
/*
function CekJadwalLab($w, $JadwalID) {
  $s = "select j.JadwalID, j.MKKode, j.Nama, jr.JamMulai, jr.JamSelesai, j.DosenID, j.SKS,
    j.ProdiID, j.ProgramID,
    d.Nama as NamaDosen, j.JenisJadwalID,
    p.Nama as _PRG, pr.Nama as _PRD
    from jadwalresponsi jr
	  left outer join jadwal j on jr.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join program p on p.ProgramID = j.ProgramID and p.KodeID = '".KodeID."'
      left outer join prodi pr on pr.ProdiID = j.ProdiID and pr.KodeID = '".KodeID."'
    where j.TahunID = '$w[TahunID]'
      and jr.RuangID = '$w[RuangID]'
      and jr.HariID = '$w[HariID]'
	  and jr.KodeID = '".KodeID."'
      and (('$w[JamMulai]:00' <= jr.JamMulai and jr.JamMulai <= '$w[JamSelesai]:59')
      or  ('$w[JamMulai]:00' <= jr.JamSelesai and jr.JamSelesai <= '$w[JamSelesai]:59'))
      and jr.NA = 'N'";
  //die("<pre>$s</pre>");
  $r = _query($s);
  $a = '';
  while ($w = _fetch_array($r)) {
    $a .= "<li>
      <b>Jadwal Ruang Kelas bentrok dengan Jadwal Lab</b>:<br />
      <table class=bsc width=400>
      <tr><td class=inp width=100>Matakuliah:</td>
          <td class=ul1>$w[Nama] <sup>($w[MKKode])</td>
          </tr>
      <tr><td class=inp>Jenis Kuliah:</td>
          <td class=ul1>$w[JenisJadwalID]&nbsp;</td>
          </tr>
      <tr><td class=inp>Jam:</td>
          <td class=ul1>$w[JamMulai] &minus; $w[JamSelesai]</td>
          </tr>
      <tr><td class=inp>Dosen:</td>
          <td class=ul1>$w[NamaDosen]</td>
          </tr>
      <tr><td class=inp>SKS:</td>
          <td class=ul1>$w[SKS]&nbsp;</td>
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
*/
function CekDosen($w, $JadwalID) {
  $s = "select j.JadwalID, j.MKKode, j.Nama, j.JamMulai, j.JamSelesai, j.DosenID, j.SKS,
    j.ProdiID, j.ProgramID,
    d.Nama as NamaDosen, j.JenisJadwalID,
    p.Nama as _PRG, pr.Nama as _PRD
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join program p on p.ProgramID = j.ProgramID and p.KodeID = '".KodeID."'
      left outer join prodi pr on pr.ProdiID = j.ProdiID and pr.KodeID = '".KodeID."'
    where j.TahunID = '$w[TahunID]'
      and j.DosenID = '$w[DosenID]'
      and j.HariID = '$w[HariID]'
      and (('$w[JamMulai]:00' <= j.JamMulai and j.JamMulai <= '$w[JamSelesai]:00')
      or  ('$w[JamMulai]:00' <= j.JamSelesai and j.JamSelesai <= '$w[JamSelesai]:00'))
      and j.NA = 'N'
      and j.JadwalID <> '$JadwalID' ";
  $r = _query($s);
  //die("<pre>$s</pre>");
  $a = '';
  while ($w = _fetch_array($r)) {
    $a .= "<li>
      <b>Jadwal Dosen bentrok dengan</b>:
      <table class=bsc width=400>
      <tr><td class=inp width=80>Matakuliah:</td>
          <td class=ul1>$w[Nama] <sup>($w[MKKode])</td>
          </tr>
      <tr><td class=inp>Jenis Kuliah:</td>
          <td class=ul1>$w[JenisJadwalID]&nbsp;</td>
          </tr>
      <tr><td class=inp>Jam:</td>
          <td class=ul1>$w[JamMulai] &minus; $w[JamSelesai]</td>
          </tr>
      <tr><td class=inp>Dosen:</td>
          <td class=ul1>$w[NamaDosen]</td>
          </tr>
      <tr><td class=inp>SKS:</td>
          <td class=ul1>$w[SKS]&nbsp;</td>
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
?>

</BODY>
</HTML>
