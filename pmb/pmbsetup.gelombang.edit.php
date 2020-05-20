<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Gelombang");
echo $_Themes;

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = sqling($_REQUEST['id']);
$bck = sqling($_REQUEST['bck']);

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);
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
				$('#'+selector).dpSetEndDate(dts.addDays(0).asString());
			} else if (stat == 'start'){
				$('#'+selector).dpSetStartDate(dts.addDays(0).asString());
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
						$('#'+selector).dpSetEndDate(d.addDays(0).asString());
					} else if (stat == 'start'){
						$('#'+selector).dpSetStartDate(d.addDays(0).asString());
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
	  setDatePicker('UjianMulai', 'UjianSelesai', 'end');
	  setDatePicker('UjianSelesai', 'UjianMulai', 'start');
	  setDatePicker('WawancaraMulai', 'WawancaraSelesai', 'end');
	  setDatePicker('WawancaraSelesai', 'WawancaraMulai', 'start');
	  setDatePicker('BayarMulai', 'BayarSelesai', 'end');
	  setDatePicker('BayarSelesai', 'BayarMulai', 'start');
});
</script>";

function GetDateOption3($value,$name){
	$a = "<input type=hidden name=".$name." id=alt".$name." value=".$value." /><input type=text id=".$name." value=".$value." readonly=true />";
	return $a;
}

// *** Functions ***
function Edit($md, $id, $bck) {
  if ($md == 0) {
    $jdl = "Edit Gelombang";
    $w = GetFields('pmbperiod', "KodeID='".KodeID."' and PMBPeriodID", $id, "*");
    $_id = "<input type=hidden name='id' value='$id' /><b>$id</b>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Gelombang";
    $w = array();
	$w['Tahun'] = date('Y');
    $w['TglMulai'] = date('Y-m-d');
    $w['TglSelesai'] = date('Y-m-d');
    $w['UjianMulai'] = date('Y-m-d');
    $w['UjianSelesai'] = date('Y-m-d');
	$w['WawancaraMulai'] = date('Y-m-d');
    $w['WawancaraSelesai'] = date('Y-m-d');
	$w['BayarMulai'] = date('Y-m-d');
    $w['BayarSelesai'] = date('Y-m-d');
    $w['NA'] = 'N';
    $_id = "<input type=text name='id' value='$id' size=10 maxlength=50 />";
  }
  else die(ErrorMsg('Error', "Mode edit tidak dikenali."));
  
  TampilkanJudul($jdl);
  // Parameters
  $arrTahun = array();
  for($i = date('Y'); $i >= 2000; $i--) { $arrTahun[] = $i; }
  $opttahun = GetOptionsFromData($arrTahun, $w['Tahun'], 1);
  $TglMulai = GetDateOption3($w['TglMulai'], 'TglMulai');
  $TglSelesai = GetDateOption3($w['TglSelesai'], 'TglSelesai');
  $UjianMulai = GetDateOption3($w['UjianMulai'], 'UjianMulai');
  $UjianSelesai = GetDateOption3($w['UjianSelesai'], 'UjianSelesai');
  $WawancaraMulai = GetDateOption3($w['WawancaraMulai'], 'WawancaraMulai');
  $WawancaraSelesai = GetDateOption3($w['WawancaraSelesai'], 'WawancaraSelesai');
  $BayarMulai = GetDateOption3($w['BayarMulai'], 'BayarMulai');
  $BayarSelesai = GetDateOption3($w['BayarSelesai'], 'BayarSelesai');
  $BiayaTesKesehatan = $w['BiayaTesKesehatan'];
  
  CheckFormScript("id,Nama,Tahun,Urutan");
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  $arrDigitNoAplikan = array(3, 4, 5);
  $arrDigitNoPMB = array(3, 4, 5);
  $optdigitnoaplikan = GetDigitOption($arrDigitNoAplikan, $w['DigitNoAplikan']);
  $optdigitnopmb = GetDigitOption($arrDigitNoPMB, $w['DigitNoPMB']);
  // Tampilkan
  echo "<p><table class=bsc cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].gelombang.edit.php' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><td class=inp nowrap>Kode Gelombang:</td>
      <td class=ul1>$_id</td>
      </tr>
  <tr><td class=inp nowrap>Nama Gelombang:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50 /></td>
      </tr>
  <tr><td class=inp nowrap>Tahun Gelombang:</td>
      <td class=ul1><select name='Tahun'>$opttahun</select></td>
      </tr>
  <tr><td class=inp nowrap>Urutan Gelombang:</td>
	  <td class=ul1><input type=text name='Urutan' value='$w[Urutan]' size=3 maxlength=5 /></td>
	  </tr>
  <tr><td class=inp nowrap>Format No. Aplikan:</td>
	  <td class=ul1><input type=text name='FormatNoAplikan' value='$w[FormatNoAplikan]' size=6>
				    <select name='DigitNoAplikan'>$optdigitnoaplikan</select></td></tr>
  <tr><td class=inp nowrap>Format No. Pendaftaran:</td>
	  <td class=ul1><input type=text name='FormatNoPMB' value='$w[FormatNoPMB]' size=6>
					<select name='DigitNoPMB'>$optdigitnopmb</select></td></tr>
  <tr><td class=inp nowrap>Mulai Tgl:</td>
      <td class=ul1>$TglMulai</td>
      </tr>
  <tr><td class=inp nowrap>Selesai Tgl:</td>
      <td class=ul1>$TglSelesai</td>
      </tr>
  <tr><td class=inp nowrap>Mulai USM:</td>
      <td class=ul1>$UjianMulai</td>
      </tr>
  <tr><td class=inp nowrap>Selesai USM:</td>
      <td class=ul1>$UjianSelesai</td>
      </tr>
  <tr><td class=inp nowrap>Mulai Wawancara:</td>
	  <td class=ul1>$WawancaraMulai</td>
	  </tr>
  <tr><td class=inp nowrap>Selesai Wawancara:</td>
	  <td class=ul1>$WawancaraSelesai</td>
	  </tr>
  <tr><td class=inp nowrap>Mulai Registrasi Ulang:</td>
      <td class=ul1>$BayarMulai</td>
      </tr>
  <tr><td class=inp nowrap>Selesai Registrasi Ulang:</td>
      <td class=ul1>$BayarSelesai</td>
      </tr>
  <tr><td class=inp nowrap>Biaya Tes Kesehatan:</td>
      <td class=ul1><input type='text' name='BiayaTesKesehatan' value='$BiayaTesKesehatan' size=7 maxlength=6></td>
      </tr>
  <tr><td class=inp>Tidak Aktif?</td>
      <td class=ul1>
        <input type=checkbox name='NA' value='Y' $NA /> *) Beri centang jika tidak aktif
      </td></tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  </form>
  </table></p>";
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup(bck) {
    opener.location='../index.php?mnux=$_SESSION[mnux]';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}

function DisablePeriod() {
  $s = "update pmbperiod set NA = 'Y' where KodeID = '". KodeID ."' ";
  $r = _query($s);
}

function Simpan($md, $id, $bck) {
  TutupScript($bck);
  $Nama = sqling($_REQUEST['Nama']);
  $Tahun = $_REQUEST['Tahun'];
  $Urutan = $_REQUEST['Urutan'];
  $FormatNoAplikan = $_REQUEST['FormatNoAplikan'];
  $DigitNoAplikan = $_REQUEST['DigitNoAplikan']+0;
  $FormatNoPMB = $_REQUEST['FormatNoPMB'];
  $DigitNoPMB = $_REQUEST['DigitNoPMB']+0;
  $TglMulai = "$_REQUEST[TglMulai]";
  $TglSelesai = "$_REQUEST[TglSelesai]";
  $UjianMulai = "$_REQUEST[UjianMulai]";
  $UjianSelesai = "$_REQUEST[UjianSelesai]";
  $WawancaraMulai = "$_REQUEST[WawancaraMulai]";
  $WawancaraSelesai = "$_REQUEST[WawancaraSelesai]";
  $BayarMulai = "$_REQUEST[BayarMulai]";
  $BayarSelesai = "$_REQUEST[BayarSelesai]";
  $BiayaTesKesehatan = sqling($_REQUEST['BiayaTesKesehatan']);
  $NA = ($_REQUEST['NA'] == 'Y')? 'Y' : 'N';
  
    if ($md == 0) {
		if ($NA == 'N') DisablePeriod();
		$s = "update pmbperiod
		  set Nama = '$Nama',
			  Tahun = '$Tahun',
			  Urutan = '$Urutan',
			  FormatNoAplikan = '$FormatNoAplikan',
			  DigitNoAplikan = '$DigitNoAplikan',
			  FormatNoPMB = '$FormatNoPMB',
			  DigitNoPMB = '$DigitNoPMB',
			  TglMulai = '$TglMulai',
			  TglSelesai = '$TglSelesai',
			  UjianMulai = '$UjianMulai',
			  UjianSelesai = '$UjianSelesai',
			  WawancaraMulai = '$WawancaraMulai',
			  WawancaraSelesai = '$WawancaraSelesai',
			  BayarMulai = '$BayarMulai',
              BiayaTesKesehatan = '$BiayaTesKesehatan',
			  BayarSelesai = '$BayarSelesai',
			  NA = '$NA',
			  LoginEdit = '$_SESSION[_Login]',
			  TanggalEdit = now()
		  where KodeID = '".KodeID."' and PMBPeriodID = '$id' ";
		$r = _query($s);
		echo "<script>ttutup('$bck')</script>";
	  }
	  elseif ($md == 1) {
		// Cek kode periode-nya dulu
		$ada = GetFields('pmbperiod', "KodeID='".KodeID."' and PMBPeriodID", $id, '*');
		if (empty($ada)) {
		  if ($NA == 'N') DisablePeriod();
		  
		  $ada2 = GetaField('pmbperiod', "Tahun='$Tahun' and Urutan", $Urutan, 'PMBPeriodID');
		  if(empty($ada2))
		  {
	
			  $s = "insert into pmbperiod
				(PMBPeriodID, KodeID, Nama, Tahun, Urutan, FormatNoAplikan, DigitNoAplikan, FormatNoPMB, DigitNoPMB, 
				TglMulai, TglSelesai,
				UjianMulai, UjianSelesai,
				WawancaraMulai, WawancaraSelesai,
				BayarMulai, BayarSelesai,
				NA, LoginBuat, TanggalBuat)
				values
				('$id', '".KodeID."', '$Nama', '$Tahun', '$Urutan', '$FormatNoAplikan', '$DigitNoAplikan', '$FormatNoPMB', '$DigitNoPMB',
				'$TglMulai', '$TglSelesai',
				'$UjianMulai', '$UjianSelesai',
				'$WawancaraMulai', '$WawancaraSelesai',
				'$BayarMulai', '$BayarSelesai',
				'$NA', '$_SESSION[_Login]', now())";
			  $r = _query($s);
			  echo "<script>ttutup('$bck')</script>";
		  }
		  else
		  {	  die(ErrorMsg('Error', "Gelombang untuk Tahun <b>$Tahun</b> dan Urutan <b>$Urutan</b> telah digunakan.<br />
					Kode yang digunakan adalah: $ada. </br>
			  <hr size=1 color=silver />
			  Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
		  }
		}
		else die(ErrorMsg('Error', "Kode Gelombang <b>$id</b> telah digunakan.<br />
		  Coba gunakan kode gelombang yang lain.
		  <hr size=1 color=silver />
		  Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
	  }
	  else die(ErrorMsg('Error', "Terjadi kesalahan mode edit.<br />Mode edit <b>$md</b> tidak dikenali."));
  
}

function GetOptionsFromData($arr, $default, $blank=0)
{	$a = '';

	if($blank==0)
	{	$a.= "<option value=''></option>";
	}
	foreach($arr as $index)
	{	$selected = ($index == $default)? 'selected' : '';
		$a.= "<option value='$index' $selected>$index</option>";
	}

	return $a;
}
function GetDigitOption($arr, $default)
{	$a = '';
	foreach($arr as $index)
	{	$selected = ($index == $default)? 'selected' : '';
		$a.= "<option value='$index' $selected>($index Digit Angka)</option>";
	}
	return $a;
}
?>
