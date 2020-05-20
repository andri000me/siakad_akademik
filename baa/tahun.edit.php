<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit Tahun Akademik");

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');
$TahunID = GetSetVar('TahunID');
$md = $_REQUEST['md']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($TahunID, $ProdiID, $ProgramID, $md);
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
function setDatePicker(selector,rangeSelector,stat,next){
	var dt = $('#alt'+selector).val().replace('-',',');
	dt = dt.replace('-',',');
	
	$('#'+selector).datePicker({startDate:'01/01/1990'});
	if (dt != '/'){
		$('#'+selector).datePicker().val(new Date(dt).asString()).trigger('change');
	}
	$('#'+selector).dpSetPosition($.dpConst.POS_TOP, $.dpConst.POS_RIGHT);
	
	if (rangeSelector != ''){
		var dts = $('#alt'+rangeSelector).val().replace('-',',');
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
	  setDatePicker('TglKRSMulai', 'TglKRSSelesai', 'end');
	  setDatePicker('TglKRSSelesai', 'TglKRSMulai', 'start');
	  
	setDatePicker('TglKuliahMulai','TglKuliahSelesai','end','');		
	setDatePicker('TglKuliahSelesai','TglKuliahMulai','start');
	
	  setDatePicker('TglBayarMulai', 'TglBayarSelesai', 'end');
	  setDatePicker('TglBayarSelesai', 'TglBayarMulai', 'start');
	  
	  setDatePicker('TglUTSMulai', 'TglUTSSelesai', 'end');
	  setDatePicker('TglUTSSelesai', 'TglUTSMulai', 'start');
	  
	  setDatePicker('TglUASMulai', 'TglUASSelesai', 'end');
	  setDatePicker('TglUASSelesai', 'TglUASMulai', 'start');
	  
	  setDatePicker('TglCuti', 'TglCuti', '');
	  setDatePicker('TglNilai', 'TglNilai', '');
});
</script>";

function GetDateOption3($value,$name){
	$a = "<input type=hidden name=".$name." id=alt".$name." value=".$value." /><input type=text id=".$name." value='".$value."' readonly=true />";
	return $a;
}


// *** Functions ***
function Edit($TahunID, $ProdiID, $ProgramID, $md) {
  if ($md == 0) {
    $jdl = "Edit Tahun Akademik";
    $w = GetFields('tahun', "ProdiID='$ProdiID' and ProgramID='$ProgramID' and TahunID",
      $TahunID, '*');
    
    $_prdnm = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $w['ProdiID'], 'Nama');
    $_prgnm = GetaField('program', "KodeID='".KodeID."' and ProgramID", $w['ProgramID'], 'Nama');
    
    $_prd = "<input type=hidden name='ProdiID' value='$ProdiID' /><sup>$ProdiID</sup> $_prdnm";
    $_prg = "<input type=hidden name='ProgramID' value='$ProgramID' /><sup>$ProgramID</sup> $_prgnm";
    $_thn = "<input type=hidden name='TahunID' value='$w[TahunID]'><b>$w[TahunID]</b>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Tahun Akademik";
    $w = array();
    $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $ProdiID, "KodeID='".KodeID."'", 'ProdiID');
    $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $ProgramID, "KodeID='".KodeID."'", 'ProgramID');
    
    $_prd = "<select id='ProdiID' name='ProdiID'>$optprd</select>";
    $_prg = "<select id='ProgramID' name='ProgramID'>$optprg</select>";
    $_thn = "<input type=text id='TahunID' name='TahunID' size=10 maxlength=10 />";
    $dt = "";
    $w['TglKuliahMulai'] = $dt;
    $w['TglKuliahSelesai'] = $dt;
    $w['TglKRSMulai'] = $dt;
    $w['TglKRSSelesai'] = $dt;
    $w['TglBayarMulai'] = $dt;
    $w['TglBayarSelesai'] = $dt;
    $w['TglUTSMulai'] = $dt;
    $w['TglUTSSelesai'] = $dt;
    $w['TglUASMulai'] = $dt;
    $w['TglUASSelesai'] = $dt;
	$w['TglCuti'] = $dt;
    $w['TglNilai'] = $dt;
    $w['SP'] = 'N';
	$w['NA'] = 'N';
  }
  else die(ErrorMsg('Error', "Mode edit: $md tidak dikenali oleh sistem"));
  // Parameters
  $optkuliahmulai = GetDateOption3($w['TglKuliahMulai'], 'TglKuliahMulai');
  $optkuliahselesai = GetDateOption3($w['TglKuliahSelesai'], 'TglKuliahSelesai');
  $optkrsmulai = GetDateOption3($w['TglKRSMulai'], 'TglKRSMulai');
  $optkrsselesai = GetDateOption3($w['TglKRSSelesai'], 'TglKRSSelesai');
  $optbayarmulai = GetDateOption3($w['TglBayarMulai'], 'TglBayarMulai');
  $optbayarselesai = GetDateOption3($w['TglBayarSelesai'], 'TglBayarSelesai');
  $optutsmulai = GetDateOption3($w['TglUTSMulai'], 'TglUTSMulai');
  $optutsselesai = GetDateOption3($w['TglUTSSelesai'], 'TglUTSSelesai');
  $optuasmulai = GetDateOption3($w['TglUASMulai'], 'TglUASMulai');
  $optuasselesai = GetDateOption3($w['TglUASSelesai'], 'TglUASSelesai');
  $optcuti = GetDateOption3($w['TglCuti'], 'TglCuti');
  $optnilai = GetDateOption3($w['TglNilai'], 'TglNilai');
  $_sp = ($w['SP'] == 'Y')? 'checked' : '';
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  
  // Tampilkan form
  echo "
  	<script>
	function getDateTime(ob){
		var curDate = document.getElementById('alt'+ob).value;
		curDate = curDate.replace('-','/');
		curDate = curDate.replace('-','/');
		var period = Date.parse(curDate);
		return period;
	}
		function CheckKalender(){
			var ProdiID = $('#ProdiID').val();
			var ProgramID = $('#ProgramID').val();
			var TahunID = $('#TahunID').val();
			var Nama = $('#Nama').val();
			var TglKuliahMulai = $('#altTglKuliahMulai').val();
			var TglKuliahSelesai = $('#altTglKuliahSelesai').val();
			var TglKRSMulai = $('#altTglKRSMulai').val();
			var TglKRSSelesai = $('#altTglKRSSelesai').val();
			var TglBayarMulai = $('#altTglBayarMulai').val();
			var TglBayarSelesai = $('#altTglBayarSelesai').val();
			var TglUTSMulai = $('#altTglUTSMulai').val();
			var TglUTSSelesai = $('#altTglUTSSelesai').val();
			var TglUASMulai = $('#altTglUASMulai').val();
			var TglUASSelesai = $('#altTglUASSelesai').val();
			var TglCuti = $('#altTglCuti').val();
			var TglNilai = $('#altTglNilai').val();
			
			var kuliahMulai = getDateTime('TglKuliahMulai');
			var kuliahSelesai = getDateTime('TglKuliahSelesai');
			
			var KRSMulai = getDateTime('TglKRSMulai');
			var KRSSelesai = getDateTime('TglKRSSelesai');
			
			var bayarMulai = getDateTime('TglBayarMulai');
			var bayarSelesai = getDateTime('TglBayarSelesai');
			
			var UTSMulai = getDateTime('TglUTSMulai');
			var UTSSelesai = getDateTime('TglUTSSelesai');
			
			var UASMulai = getDateTime('TglUASMulai');
			var UASSelesai = getDateTime('TglUASSelesai');

			var errmsg = '';
			if (ProdiID == ''){
				errmsg += 'Program Studi tidak boleh kosong \\n';
			}
			if (ProgramID == ''){
				errmsg += 'Program Pendidikan tidak boleh kosong \\n';
			}
			if (TahunID == ''){
				errmsg += 'Kode Tahun tidak boleh kosong \\n';
			}
			if (Nama == ''){
				errmsg += 'Nama Tahun tidak boleh kosong \\n';
			}
			if (TglKRSMulai == '/'){
				errmsg += 'Mulai KRS tidak boleh kosong \\n';
			}
			if (TglKRSSelesai == '/'){
				errmsg += 'Selesai KRS tidak boleh kosong \\n';
			}
			if (TglKuliahMulai == '/'){
				errmsg += 'Mulai Kuliah tidak boleh kosong \\n';
			}
			if (TglKuliahSelesai == '/'){
				errmsg += 'Selesai Kuliah tidak boleh kosong \\n';
			}
			if (TglBayarMulai == '/'){
				errmsg += 'Mulai Bayar tidak boleh kosong \\n';
			}
			if (TglBayarSelesai == '/'){
				errmsg += 'Selesai Bayar tidak boleh kosong \\n';
			}
			if (TglUTSMulai == '/'){
				errmsg += 'Mulai UTS tidak boleh kosong \\n';
			}
			if (TglUTSSelesai == '/'){
				errmsg += 'Selesai UTS tidak boleh kosong \\n';
			}
			if (TglUASMulai == '/'){
				errmsg += 'Mulai UAS tidak boleh kosong \\n';
			}
			if (TglUASSelesai == '/'){
				errmsg += 'Selesai UAS tidak boleh kosong \\n';
			}
			if (TglNilai == '/'){
				errmsg += 'Penilaian tidak boleh kosong \\n';
			}
			if (TglCuti == '/'){
				errmsg += 'Pengajuan Cuti tidak boleh kosong \\n';
			}
			
			if (kuliahMulai < KRSSelesai){
				errmsg += 'Mulai Kuliah harus dilakukan setelah masa pengisian KRS \\n';
			}
			if ((UTSMulai < kuliahMulai || UTSMulai > kuliahSelesai) || (UTSSelesai < kuliahMulai || UTSSelesai > kuliahSelesai)){
				errmsg += 'UTS harus berada dalam masa perkuliahan \\n';
			}
			if ((UASMulai < kuliahMulai || UASMulai > kuliahSelesai) || (UASSelesai < kuliahMulai || UASSelesai > kuliahSelesai)){
				errmsg += 'UAS harus berada dalam masa perkuliahan \\n';
			}
			
			if (errmsg != ''){
				alert (errmsg);
				return false;
			}
		}
	</script>
  ";
  echo "<p><table class=bsc cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].edit.php' method=POST onSubmit='return CheckKalender()'>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><th class=ttl colspan=4>$jdl</th></tr>
  <tr><td class=inp>Prg. Studi:</td>
      <td class=ul1>$_prd</td>
      <td class=inp>Prg. Pendidikan:</td>
      <td class=ul1>$_prg</td>
      </tr>
  <tr><td class=inp>Kode Tahun:</td>
      <td class=ul1>$_thn</td>
      <td class=inp>Nama Tahun:</td>
      <td class=ul1><input type=text id='Nama' name='Nama' value='$w[Nama]' size=25 maxlength=50 /></td>
      </tr>
  
  <tr><th class=ttl colspan=4>Detail Kalendar</th></tr>
  <tr><td class=inp>Mulai KRS:</td>
      <td class=ul1>$optkrsmulai</td>
      <td class=inp>Selesai:</td>
      <td class=ul1>$optkrsselesai</td>
      </tr>
  <tr><td class=inp>Mulai Kuliah:</td>
      <td class=ul1 nowrap>$optkuliahmulai</td>
      <td class=inp>Tgl Selesai:</td>
      <td class=ul1 nowrap>$optkuliahselesai</td>
      </tr>
  <tr><td class=inp>Mulai Bayar:</td>
      <td class=ul1>$optbayarmulai</td>
      <td class=inp>Selesai:</td>
      <td class=ul1>$optbayarselesai</td>
      </tr>
  <tr><td class=inp>Mulai UTS:</td>
      <td class=ul1>$optutsmulai</td>
      <td class=inp>Selesai:</td>
      <td class=ul1>$optutsselesai</td>
      </tr>
  <tr><td class=inp>Mulai UAS:</td>
      <td class=ul1>$optuasmulai</td>
      <td class=inp>Selesai:</td>
      <td class=ul1>$optuasselesai</td>
      </tr>
  <tr><td class=inp>Penilaian:</td>
      <td class=ul1>$optnilai</td>
      <td class=inp>Pengajuan Cuti:</td>
      <td class=ul1>$optcuti</td>
      </tr>
  
  <tr><td class=inp>Catatan:</td>
      <td class=ul1 colspan=3><textarea name='Catatan' cols=70 rows=2>$w[Catatan]</textarea></td>
      </tr>
  <tr><td class=inp>Semester Pendek (SP)</td>
      <td class=ul1 colspan=3>
      <input type=checkbox name='SP' value='Y' $_sp /> *) Beri centang jika termasuk semester pendek.
      </td>
      </tr>
  <tr><td class=inp>Tidak Aktif? (NA)</td>
      <td class=ul1 colspan=3>
      <input type=checkbox name='NA' value='Y' $_na /> *) Beri centang jika tidak aktif.
      </td>
      </tr>
  
  <tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
      </td></tr>
  
  </form>
  </table></p>";
}
function Simpan($TahunID, $ProdiID, $ProgramID, $md) {
  $Nama = sqling($_REQUEST['Nama']);
  $TglKuliahMulai = "$_REQUEST[TglKuliahMulai]";
  $TglKuliahSelesai = "$_REQUEST[TglKuliahSelesai]";
  $TglKRSMulai = "$_REQUEST[TglKRSMulai]";
  $TglKRSSelesai = "$_REQUEST[TglKRSSelesai]";
  $TglBayarMulai = "$_REQUEST[TglBayarMulai]";
  $TglBayarSelesai = "$_REQUEST[TglBayarSelesai]";
  $TglUTSMulai = "$_REQUEST[TglUTSMulai]";
  $TglUTSSelesai = "$_REQUEST[TglUTSSelesai]";
  $TglUASMulai = "$_REQUEST[TglUASMulai]";
  $TglUASSelesai = "$_REQUEST[TglUASSelesai]";
  $TglNilai = "$_REQUEST[TglNilai]";
  $TglCuti = "$_REQUEST[TglCuti]";
  $Catatan = sqling($_REQUEST['Catatan']);
  $SP = (empty($_REQUEST['SP']))? 'N' : 'Y';
  $NA = (empty($_REQUEST['NA']))? 'N' : 'Y';
  
  if ($md == 0) {
    $s = "update tahun
      set Nama = '$Nama',
          TglKuliahMulai = '$TglKuliahMulai', TglKuliahSelesai = '$TglKuliahSelesai',
          TglKRSMulai = '$TglKRSMulai', TglKRSSelesai = '$TglKRSSelesai',
          TglBayarMulai = '$TglBayarMulai', TglBayarSelesai = '$TglBayarSelesai',
          TglUTSMulai = '$TglUTSMulai', TglUTSSelesai = '$TglUTSSelesai',
          TglUASMulai = '$TglUASMulai', TglUASSelesai = '$TglUASSelesai',
          TglCuti = '$TglCuti', TglNilai = '$TglNilai', Catatan = '$Catatan',
          LoginEdit = '$_SESSION[_Login]', TglEdit = now(),
          SP = '$SP', NA = '$NA'
      where TahunID = '$TahunID' AND ProdiID='$ProdiID' AND ProgramID='$ProgramID' and KodeID='".KodeID."'";
    $r = _query($s);
    if ($NA == 'N') NA_Tahun($TahunID, $ProdiID, $ProgramID);
    TutupScript();
  }
  elseif ($md == 1) {
    // Cek dulu kodenya.
    $ada = GetFields('tahun', "KodeID='".KodeID."' AND ProdiID='$ProdiID' AND ProgramID='$ProgramID' and TahunID",
      $TahunID, "*");
    if ($ada)
      die(ErrorMsg('Error',
        "Tahun akademik dengan kode: <b>$TahunID</b> sudah ada untuk prodi/program ini. 
        Gunakan kode tahun akademik yang lain.<br />
        Hubungi Sysadmin untuk informasi lebih lanjut.
        <hr size=1 color=silver />
        <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
    else {
      $s = "insert into tahun
        (TahunID, KodeID, ProdiID, ProgramID,
        Nama, TglKuliahMulai, TglKuliahSelesai,
        TglKRSMulai, TglKRSSelesai,
        TglBayarMulai, TglBayarSelesai,
        TglUTSMulai, TglUTSSelesai,
        TglUASMulai, TglUASSelesai,
        TglCuti, TglNilai, Catatan,
        LoginBuat, TglBuat, SP, NA)
        values
        ('$TahunID', '".KodeID."', '$ProdiID', '$ProgramID',
        '$Nama', '$TglKuliahMulai', '$TglKuliahSelesai',
        '$TglKRSMulai', '$TglKRSSelesai',
        '$TglBayarMulai', '$TglBayarSelesai',
        '$TglUTSMulai', '$TglUTSSelesai',
        '$TglUASMulai', '$TglUASSelesai',
        '$TglCuti', '$TglNilai', '$Catatan',
        '$_SESSION[_Login]', now(), '$SP', '$NA')";
      $r = _query($s);
    if ($NA == 'N') NA_Tahun($TahunID, $ProdiID, $ProgramID);
      TutupScript();
    }
  }
  else die(ErrorMsg('Error',
    "Mode edit: $md tidak dikenali sistem."));
}
function NA_Tahun($TahunID, $ProdiID, $ProgramID) {
  $s = "update tahun
    set NA = 'Y'
    where KodeID = '".KodeID."'
      and ProdiID = '$ProdiID'
      and ProgramID = '$ProgramID'
      and TahunID <> '$TahunID' ";
  $r = _query($s);
}
function TutupScript($pmbid) {
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
