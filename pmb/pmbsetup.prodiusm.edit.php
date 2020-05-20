<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 09 Juli 2009

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Prodi - USM");

// *** Parameters ***
$prd = sqling($_REQUEST['prd']);
$id = sqling($_REQUEST['id']);
$md = $_REQUEST['md']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $prd, $id);
echo '
<link type="text/css" href="../datepicker2/datePicker.css" rel="stylesheet" />	
<script type="text/javascript" src="../datepicker2/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../datepicker2/date-id.js"></script>
<!--[if IE]>
<script type="text/javascript" src="../datepicker2/jquery.bgiframe.js"></script>
<![endif]-->
<script type="text/javascript" src="../datepicker2/jquery.datePicker.js"></script>
';

  $s2 = "select date_format(UjianMulai, '%d')+0 as _fromday, date_format(UjianMulai, '%m')+0 as _frommonth, date_format(UjianMulai, '%Y')+0 as _fromyear,
  		date_format(UjianSelesai, '%d')+0 as _today, date_format(UjianSelesai, '%m')+0 as _tomonth, date_format(UjianSelesai, '%Y')+0 as _toyear, PMBPeriodID
		from pmbperiod where NA = 'N'";
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
	setDatePicker('TanggalUjian','TanggalUjian','');
		var dts = '".$start."';
		if (dts) {
			dts = new Date(dts);
				$('#TanggalUjian').dpSetStartDate(dts.addDays(0).asString());
			}
		var dts = '".$end."';
		if (dts) {
			dts = new Date(dts);
				$('#TanggalUjian').dpSetEndDate(dts.addDays(0).asString());
			}
			
});
</script>";


function GetDateOption3($value,$name){
	$a = "<input type=hidden name=".$name." id=alt".$name." value=".$value." /><input type=text id=".$name." value=".$value." readonly=true />";
	return $a;
}

// *** Functions ***
function Edit($md, $prd, $id) {
  
  if ($md == 0) {
    $jdl = "Edit USM";
    $w = GetFields('prodiusm', 'ProdiUSMID', $id, '*');
  }
  elseif ($md == 1) {
    $jdl = "Tambah USM";
    $w = array();
    $w['Urutan'] = GetaField('prodiusm', "KodeID='".KodeID."' and ProdiID", $prd, "max(Urutan)")+1;
    $w['TanggalUjian'] = date('Y-m-d');
	$w['JamMulai'] = '08:00';
	$w['JamSelesai'] = '09:00';
    $w['JumlahSoal'] = 0;
  }
  else die(ErrorMsg('Error',
    "Terjadi kesalahan.<br />
    Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Tampilkan
  TampilkanJudul($jdl);

  $s1 = "select r.RuangID, r.KapasitasUjian, k.Nama as NamaKampus 
			from ruang r left outer join kampus k on r.KampusID=k.KampusID
			where r.UntukUSM = 'Y' and r.ProdiID like '%$prd%'
			order by r.KampusID, r.RuangID";
  $r1 = _query($s1);
  
  if(!empty($_SESSION['prodi']))
  
  $gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
  
  $ruangcheckboxes = "";
  $curKampus = '';
  $counting = 0;
  $counting3 = 0;
  $ruangarray = explode(",",$w['RuangID']); 
  while($w1=_fetch_array($r1))
  {	 	if(empty($cur_kampus)) 
		{ 	$cur_kampus = $w1['NamaKampus']; 
			$ruangcheckboxes .= "
								<tr><td class=inp>Lokasi/Ruang Uji:</td>
									 <td colspan=3><b>$w1[NamaKampus]</b></td>
								</tr>
								<tr><td></td>"; 
		}
		else if($cur_kampus != $w1['NamaKampus'])
		{	$cur_kampus = $w1['NamaKampus'];
			$ruangcheckboxes .= "</tr><tr><td></td>
									 <td colspan=3><b>$w1[NamaKampus]</b></td>
							    </tr>
								<tr><td></td>"; 
			$counting3 = 0;					
		}
		
		if(in_array($w1['RuangID'], $ruangarray)) { $ruangcheck = 'checked'; }
		else { 	$ruangcheck = ''; }
		if($counting3 < 3)
		{
			$ruangcheckboxes .= "<td><input type=checkbox id='ruang$counting' name='Ruang[$counting]' value='$w1[RuangID]' $ruangcheck>$w1[RuangID] ($w1[KapasitasUjian])</td>";
		}
		else
		{	
			$counting3 = 0;
			$ruangcheckboxes .= "</tr><tr><td></td><td><input type=checkbox id='ruang$counting' name='Ruang[$counting]' value='$w1[RuangID]' $ruangcheck>$w1[RuangID] ($w1[KapasitasUjian])</td>";
		}
		$counting++; $counting3++;
  }
  
  $TanggalUjian = GetDateOption($w['TanggalUjian'], 'TanggalUjian');
  $JamMulai = GetTimeOption($w['JamMulai'], 'JamMulai');
  $JamSelesai = GetTimeOption($w['JamSelesai'], 'JamSelesai');
  $optusm = GetOption2('pmbusm', "concat(PMBUSMID, ' - ', Nama)", 'PMBUSMID', $w['PMBUSMID'], "KodeID='".KodeID."'", 'PMBUSMID');
  
  $s2 = "select date_format(UjianMulai, '%d')+0 as _fromday, date_format(UjianMulai, '%m')+0 as _frommonth, date_format(UjianMulai, '%Y')+0 as _fromyear,
  		date_format(UjianSelesai, '%d')+0 as _today, date_format(UjianSelesai, '%m')+0 as _tomonth, date_format(UjianSelesai, '%Y')+0 as _toyear, PMBPeriodID
		from pmbperiod where NA = 'N'";
  $q2 = _query($s2);
  $w2 = (_fetch_array($q2));

  // ambil gelombang euy
  	$optgelombang = "";
	$s3 = "select PMBPeriodID from pmbperiod order by PMBPeriodID desc";
	$q3 = _query($s3);
	while ($w3 = _fetch_array($q3)){
		if ($w[PMBPeriodID] == $w3[PMBPeriodID]){
			$sel = "selected='selected'";
		} else {
			$sel = "";
		}
		$optgelombang .= "<option value='".$w3[PMBPeriodID]."' ".$sel.">".$w3[PMBPeriodID]."</option>";
	}
  //////////////////////

  echo '
  		<script>
			function CheckForm(count,fromDay,fromMonth,fromYear,toDay,toMonth,toYear,activePeriod){
				
					
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
				var ruangs = "";
				var ruangx = "";
				var ujian = document.getElementById("PMBUSMID").value;
				var gelombang = document.getElementById("gelombang").value;
				
				for (i=0;i<count;i++){
					ruangx += "false";
					var ruang = document.getElementById("ruang"+i).checked;
					ruangs += ruang;
				}
				
				if (gelombang != activePeriod){
					errmsg += "Pilih gelombang yang aktif \\n";
				}				
				if (ujian == ""){
					errmsg += "Nama ujian harus diisi \\n";
				}
				if (fromJam >= toJam){
					errmsg += "Waktu mulai harus lebih awal dari waktu selesai\\n"
				}
				if (ruangs == ruangx){
					errmsg += "Pilih minimal satu ruangan";
				}
				if (errmsg != ""){
					alert (errmsg);
					return false;
				}
			}
		</script>
  		';
  
  
  
  echo "<p><table class=bsc cellspacing=1 width=100%>
  <form nama=formusm action='../$_SESSION[mnux].prodiusm.edit.php' method=POST onSubmit='return CheckForm($counting,$w2[_fromday],$w2[_frommonth],$w2[_fromyear],$w2[_today],$w2[_tomonth],$w2[_toyear],$w2[PMBPeriodID])'>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='prd' value='$prd' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td class=inp>Urutan:</td>
      <td class=ul1 colspan=3><input type=text name='Urutan' value='$w[Urutan]' size=4 maxlength=3 /></td>
      </tr>
  <tr><td class=inp>Gelombang PMB:</td>
      <td class=ul1 colspan=3>
	  <select id='gelombang' name='gelombang'>
	  	$optgelombang
	  </select>
	  </td>
      </tr>
  <tr><td class=inp>Mata Ujian:</td>
      <td class=ul1 colspan=3><select id='PMBUSMID' name='PMBUSMID'>$optusm</select></td>
      </tr>
  <tr><td class=inp>Jumlah Soal:</td>
      <td class=ul1><input type=text name='JumlahSoal' value='$w[JumlahSoal]' size=4 maxlength=3 /></td>
      </tr>

  <tr><td class=inp>Tanggal Ujian:</td>
      <td class=ul1 colspan=3>$TanggalUjian</td>
      </tr>
  <tr><td class=inp>Waktu Ujian:</td>
      <td class=ul1 colspan=3>$JamMulai s/d $JamSelesai</td>
	  </tr>
    $ruangcheckboxes;
  <tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
      </td></tr>
  </form>
  </table>";
}

function Simpan($md, $prd, $id) {
  TutupScript();
  $w = array();
  $w['Urutan'] = $_REQUEST['Urutan']+0;
  $w['Gelombang'] = $_REQUEST['gelombang'];
  $w['PMBUSMID'] = sqling($_REQUEST['PMBUSMID']);
  $w['JumlahSoal'] = $_REQUEST['JumlahSoal']+0;
  $w['TanggalUjian'] = "$_REQUEST[TanggalUjian_y]-$_REQUEST[TanggalUjian_m]-$_REQUEST[TanggalUjian_d]";
  $w['JamMulai'] = "$_REQUEST[JamMulai_h]:$_REQUEST[JamMulai_n]";
  $w['JamSelesai'] = "$_REQUEST[JamSelesai_h]:$_REQUEST[JamSelesai_n]";
  $w['PMBPeriodID'] = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
  $Ruang = $_REQUEST['Ruang'];
  
  $cekada = GetFields('pmbperiod', "KodeID='".KodeID."' and 
            left(UjianMulai,10)<= '$w[TanggalUjian]' and 
            left(UjianSelesai,10)>= '$w[TanggalUjian]' and NA", "N", "*");
  
  $cek = GetFields('pmbperiod', "KodeID='".KodeID."' and NA","N",
            "date_format(UjianMulai,'%d %M %Y') as Mulai, 
            date_format(UjianSelesai,'%d %M %Y') as Selesai");
  if(empty($cekada)){
    die(ErrorMsg('Kesalahan Tanggal', 
      "Tanggal yang anda setting tidak sesuai dengan Tanggal penjadwalan Ujian,<br/>
       yaitu dari Tanggal : <b>$cek[Mulai]</b> sampai dengan <b>$cek[Selesai]</b>.
      <hr size=1 color=silver />
      <p align=center>
      <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
  }
  
  foreach($Ruang as $pilihruang)
  {		if(empty($ruangstring)) { $ruangstring = $pilihruang; 	}
		else { $ruangstring .= ",".$pilihruang;	}
  }
  $w['RuangID'] = $ruangstring;
  
  $oke = '';
  $oke = CekMataUji($w, $id, $prd);
  if (!empty($w['RuangID'])) $oke .= CekRuang($w, $id, $prd, $md);
  
  if(empty($oke))
  {
	  // Simpan
	  if ($md == 0) {
		$s = "update prodiusm
		  set Urutan = '$w[Urutan]', 
		  	  PMBPeriodID = '$w[Gelombang]',
		  	  PMBUSMID = '$w[PMBUSMID]', 
			  JumlahSoal = '$w[JumlahSoal]', TanggalUjian = '$w[TanggalUjian]',
			  JamMulai = '$w[JamMulai]', JamSelesai = '$w[JamSelesai]',
			  RuangID = '$w[RuangID]',
			  LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
		  where KodeID = '".KodeID."' and ProdiUSMID = '$id' ";
		$r = _query($s);
		echo "<script>ttutup()</script>";
	  }
	  elseif ($md == 1) {
		$s = "insert into prodiusm
		  (KodeID, PMBUSMID, ProdiID, Urutan, PMBPeriodID,
		  TanggalUjian, JamMulai, JamSelesai, RuangID, JumlahSoal,
		  LoginBuat, TanggalBuat)
		  values
		  ('".KodeID."', '$w[PMBUSMID]', '$prd', '$w[Urutan]', '$w[Gelombang]',
		  '$w[TanggalUjian]', '$w[JamMulai]', '$w[JamSelesai]', '$w[RuangID]', '$w[JumlahSoal]',
		  '$_SESSION[_Login]', now())";
		$r = _query($s);
		echo "<script>ttutup()</script>";
	  }
	  else {
	  }
  }
  else {
    die(ErrorMsg('Jadwal Bentrok', 
      "Berikut adalah list jadwal yang bentrok: 
      <ol>$oke</ol>
      </p>"));
  }  
}

function CekMataUji($w, $id, $prd)
{	$s1 = "select pu.*, pu2.Nama as _NamaUjian, prd.Nama as _PRD  
			from prodiusm pu left outer join pmbusm pu2 on pu.PMBUSMID=pu2.PMBUSMID and pu2.KodeID='".KodeID."'
				left outer join prodi prd on pu.ProdiID=prd.ProdiID and prd.KodeID='".KodeID."'
			where pu.PMBPeriodID='$w[PMBPeriodID]' 
				and pu.PMBUSMID='$w[PMBUSMID]' 
				and pu.KodeID='".KodeID."'
				and INSTR(concat('|', pu.ProdiID, '|'), concat('|', '$prd', '|')) != 0
				and pu.ProdiUSMID <> '$id'";
	$r1 = _query($s1);
	while($w1 = _fetch_array($r1))
	{	
		$a .= "<li>
			<b>Jadwal USM untuk Mata Ujian $w[_NamaUjian] telah dijadwalkan</b>:<br />
			<table class=bsc width=400>
			<tr><td class=inp width=100>Mata Ujian:</td>
			  <td class=ul1>$w1[PMBUSMID] - $w1[_NamaUjian]</td>
			  </tr>
			<tr><td class=inp>Ruang:</td>
			  <td class=ul1>$w1[RuangID]</td>
			  </tr>
			<tr><td class=inp>Tanggal Ujian:</td>
			  <td class=ul1>$w1[TanggalUjian]&nbsp;</td>
			  </tr>
			<tr><td class=inp>Jam Ujian:</td>
			  <td class=ul1>$w1[JamMulai] &minus; $w1[JamSelesai]</td>
			  </tr>
			<tr><td class=inp>Program Studi:</td>
			  <td class=ul1>$w1[_PRD] <sup>($w1[ProdiID])</sup></td>
			  </tr>";
	
		$a .= "
			<tr><td colspan=2 align=center><u><i><b>Jadwal tidak dapat digabungkan karena memiliki Mata Uji sama</b></i></u></td></tr>";
		$a .= "<tr><td>&nbsp;</td></tr>
			  </table>
			</li>";
	}
	return $a;
}

function CekRuang($w, $id, $prd, $md)
{	$ruangcheck = '';
	$arrRuang = explode(',', $w['RuangID']);
	foreach($arrRuang as $ruang)
		$ruangcheck .= (empty($ruangcheck))? "INSTR(concat(',', pu.RuangID, ','), concat(',', '$ruang', ',')) != 0" :
												" or INSTR(concat(',', pu.RuangID, ','), concat(',', '$ruang', ',')) != 0";
	$ruangcheck = "and (".$ruangcheck.")";
	
	$s1 = "select pu.*, pu2.Nama as _NamaUjian, prd.Nama as _PRD 
			from prodiusm pu 
				left outer join pmbusm pu2 on pu.PMBUSMID=pu2.PMBUSMID and pu2.KodeID='".KodeID."'
				left outer join prodi prd on pu.ProdiID=prd.ProdiID and prd.KodeID='".KodeID."'
			where pu.PMBPeriodID='$w[PMBPeriodID]'
				$ruangcheck
				and pu.TanggalUjian = '$w[TanggalUjian]'
				and (('$w[JamMulai]:00' <= pu.JamMulai and pu.JamMulai <= '$w[JamSelesai]:59')
					or  ('$w[JamMulai]:00' <= pu.JamSelesai and pu.JamSelesai <= '$w[JamSelesai]:59'))
				and pu.KodeID='".KodeID."'
				and pu.ProdiUSMID <> '$id'
				";
	$r1 = _query($s1);
	while($w1 = _fetch_array($r1))
	{	
		$a .= "<li>
			<b>Jadwal USM bentrok dengan</b>:<br />
			<table class=bsc width=400>
			<tr><td class=inp width=100>Mata Ujian:</td>
			  <td class=ul1>$w1[PMBUSMID] - $w1[_NamaUjian]</td>
			  </tr>
			<tr><td class=inp>Ruang:</td>
			  <td class=ul1>$w1[RuangID]</td>
			  </tr>
			<tr><td class=inp>Tanggal Ujian:</td>
			  <td class=ul1>$w1[TanggalUjian]&nbsp;</td>
			  </tr>
			<tr><td class=inp>Jam Ujian:</td>
			  <td class=ul1>$w1[JamMulai] &minus; $w1[JamSelesai]</td>
			  </tr>
			<tr><td class=inp>Program Studi:</td>
			  <td class=ul1>$w1[_PRD] <sup>($w1[ProdiID])</sup></td>
			  </tr>";
		
		if($w['PMBUSMID'] != $w1['PMBUSMID'])
		$a .= "
			<tr><td colspan=2 align=center><u><i><b>Jadwal tidak dapat digabungkan karena memiliki Mata Uji berbeda</b></i></u></td></tr>";
		else if($w1['ProdiID'] == $prd)
		$a .= "
			<tr><td colspan=2 align=center><u><i><b>Jadwal tidak dapat digabungkan karena memiliki Program Studi sama</b></i></u></td></tr>";
		else
		$a .= "
			<tr><td colspan=2 align=center><input type=button name='Gabung' value='Satukan dengan jadwal ini' 
												onClick=\"location='?mnux=$_SESSION[mnux]&rnd=qo4qo3o480badaoiabsdfb224&gos=GabungkanProdi&md=$md&prd=$prd&id=$id&idtujuan=$w1[ProdiUSMID]'\"></td></tr>";
		
		$a .= "<tr><td>&nbsp;</td></tr>
			  </table>
			</li>";
	}

	return $a;
}

function GabungkanProdi($md, $prd, $id)
{	$idtujuan = $_REQUEST['idtujuan'];
	if($md == 0)
	{	$ProdiID = GetaField('prodiusm', "ProdiUSMID='$id' and KodeID", KodeID, 'ProdiID');
		$arrtempProdiID = explode('|', $ProdiID);
		if(count($arrTempProdiID) == 1)
		{	$s = "delete from prodiusm where ProdiUSMID='$id' and KodeID='".KodeID."'";
			$r = _query($s);
		}
		else
		{	foreach($arrtempProdiID as $key => $tempProdiID)
			{	if($tempProdiID == $prd) unset($arrTempProdiID[$key]);
			}
			$newProdiID=implode('|', $arrtempProdiID);
			$s = "update prodiusm set ProdiID = '$NewProdiID' where ProdiUSMID='$id' and KodeID='".KodeID."'";
			$r = _query($s);
		}
	}
	else if($md == 1)
	{	// Do Nothing, Just update Below
	}
	$arrProdi = explode('|', GetaField('prodiusm', "ProdiUSMID='$idtujuan' and KodeID", KodeID, 'ProdiID'));
	$arrProdi[] = $prd;
	$newProdiID = implode('|', $arrProdi);
	$s = "update prodiusm set ProdiID='$newProdiID'
			where ProdiUSMID='$idtujuan'";
	$r = _query($s);
	TutupScript();
	echo "<script>ttutup()</script>";
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=prodiusm';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}
?>
