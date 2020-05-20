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
$_jdwlTahun = GetSetVar('_jdwlTahun');
$_jdwlProdi = GetSetVar('_jdwlProdi');
$_jdwlProg  = GetSetVar('_jdwlProg');

// *** Special Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0;
$resid = $_REQUEST['resid']+0;
$Kembali = $_REQUEST['Kembali']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $resid, $Kembali);

// *** Functions ***
function Edit($md, $id, $resid, $Kembali) {
echo '
<link type="text/css" href="../datepicker2/datePicker.css" rel="stylesheet" />	
<script type="text/javascript" src="../datepicker2/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../datepicker2/date-id.js"></script>
<!--[if IE]>
<script type="text/javascript" src="../datepicker2/jquery.bgiframe.js"></script>
<![endif]-->
<script type="text/javascript" src="../datepicker2/jquery.datePicker.js"></script>
';

$s2 = "select date_format(TglMulai, '%d')+0 as _fromday, date_format(TglMulai, '%m')+0 as _frommonth, date_format(TglMulai, '%Y')+0 as _fromyear,
	date_format(TglSelesai, '%d')+0 as _today, date_format(TglSelesai, '%m')+0 as _tomonth, date_format(TglSelesai, '%Y')+0 as _toyear
	from jadwal where NA = 'N' and JadwalID = $id";
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
	setDatePicker('KuliahTanggal','KuliahTanggal','');
		var dts = '".$start."';
		if (dts) {
			dts = new Date(dts);
				$('#KuliahTanggal').dpSetStartDate(dts.addDays(0).asString());
			}
		var dts = '".$end."';
		if (dts) {
			dts = new Date(dts);
				$('#KuliahTanggal').dpSetEndDate(dts.addDays(0).asString());
			}
});
</script>";

function GetDateOption3($value,$name){
	$a = "<input type=hidden name=".$name." id=alt".$name." value=".$value." /><input type=text id=".$name." value=".$value." readonly=true />";
	return $a;
}


  if($Kembali == 1)
  {	if($md == 0) $jdl = "Edit Jadwal Ekstra";
	else if ($md == 1) $jdl = "Tambah Jadwal Ekstra";
	else die(ErrorMsg('Fatal Error', "Mode Edit tidak diketahui"));
	
	$w = GetFields('jadwal j left outer join prodi prd on j.ProdiID=prd.ProdiID
								 left outer join program prg on j.ProgramID=prg.ProgramID', 
				'j.JadwalID', $id, 
				'j.DosenID, j.NamaKelas, j.RencanaKehadiran, j.MaxAbsen, prd.Nama as _PRD, prg.Nama as _PRG,
				j.MKID, j.MKKode, j.Nama, j.SKS');
	$w['HariID'] = $_REQUEST['HariID'];
	$w['JamMulai'] = $_REQUEST['JamMulai'];
	$w['JamSelesai'] = $_REQUEST['JamSelesai'];
	$w['DosenID'] = $_REQUEST['DosenID'];
	$w['RuangID'] = $_REQUEST['RuangID'];
	$w['Kapasitas'] = $_REQUEST['Kapasitas'];
	$w['KuliahTanggal'] = $_REQUEST['KuliahTanggal'];
	$w['_RencanaKehadiranRes'] = $_REQUEST['_RencanaKehadiranRes'];
	$w['_MaxAbsenRes'] = $_REQUEST['_MaxAbsenRes'];
	$w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
  }
  else
  {	  if ($md == 0) {
		$jdl = "Edit Jadwal Ekstra";
		$w = GetFields('jadwal jr left outer join jadwal j on jr.JadwalRefID=j.JadwalID
										  left outer join prodi prd on j.ProdiID=prd.ProdiID
										  left outer join program prg on j.ProgramID=prg.ProgramID', 
				'jr.JadwalID', $resid, 
				'jr.*, j.ProdiID, j.ProgramID, j.DosenID, j.NamaKelas, j.RencanaKehadiran, j.MaxAbsen, prd.Nama as _PRD, prg.Nama as _PRG,
				j.MKID, j.MKKode, j.Nama, j.SKS, jr.RencanaKehadiran as _RencanaKehadiranRes, jr.MaxAbsen as _MaxAbsenRes');
		$w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
		$w['JamMulai'] = substr($w['JamMulai'], 0, 5);
		$w['JamSelesai'] = substr($w['JamSelesai'], 0, 5);
	  }
	  elseif ($md == 1) {
		$jdl = "Tambah Jadwal Ekstra";
		$w = GetFields('jadwal j left outer join prodi prd on j.ProdiID=prd.ProdiID
								 left outer join program prg on j.ProgramID=prg.ProgramID', 
				'j.JadwalID', $id, 
				'j.DosenID, j.NamaKelas, j.RencanaKehadiran, j.MaxAbsen, prd.Nama as _PRD, prg.Nama as _PRG,
				j.MKID, j.MKKode, j.Nama, j.SKS');
		$w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
		$w['KuliahTanggal'] = date('Y-m-d');
		$w['HariID'] = date('w');
		$w['_RencanaKehadiranRes'] = $w['RencanaKehadiran'];
		$w['_MaxAbsenRes'] = $w['MaxAbsen'];
	  }
	  else die(ErrorMsg('Fatal Error', "Mode Edit tidak diketahui"));
  }
  $opthari = GetOption2('hari', "Nama", 'HariID', $w['HariID'], '', 'HariID');
  $opttglkuliah = GetDateOption3($w['KuliahTanggal'], 'KuliahTanggal');
  $optJamMulai = GetTimeOption($w['JamMulai'], 'JamMulai');
  $optJamSelesai = GetTimeOption($w['JamSelesai'], 'JamSelesai');
  $optjenisjadwal = GetOption2('jenisjadwal', "concat(JenisJadwalID, ' - ', Nama)", 'JenisJadwalID', $w['JenisJadwalID'], "NA='N' and Tambahan='Y'", 'JenisJadwalID', 0, 0);
  // Parameters
  JdwlScript();
  // Tampilkan
  CheckFormScript("HariID,RuangID");
  TampilkanJudul($jdl);

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
		
		var kapasitas = document.forms[0].Kapasitas.value;
		
		var errmsg = "";
		
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
  <form name='frmJadwal' action='../$_SESSION[mnux].editlab.php' method=POST onSubmit="return CheckForm(this)">
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='TahunID' value='$_SESSION[_jdwlTahun]' />
  <input type=hidden name='ProdiID' value='$_SESSION[_jdwlProdi]' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='resid' value='$resid' />
  
  <tr><td class=inp>Program Studi:</td>
      <td class=ul1><b>$w[_PRD]</b> <sup>($_SESSION[_jdwlProdi])</sup></td>
      <td class=inp>Program:</td>
      <td class=ul1>$w[_PRG] <sup>$w[ProgramID]</sup></td>
      </tr>
   <tr><td class=inp>Matakuliah:</td>
      <td class=ul1 colspan=3 nowrap>
	  $w[MKKode] - $w[Nama] <sub>$w[SKS] SKS</sub></td></tr>
  <tr><td class=inp>Dosen Pengampu:</td>
      <td class=ul1 colspan=3 nowarp>$w[DosenID] - $w[Dosen]</td>
      </tr>
  <tr><td class=inp>Kelas:</td>
      <td class=ul1>$w[NamaKelas]</td>
      </tr>
  <tr><td class=inp>Rencana Kehadiran Dosen:</td>
      <td class=ul1>$w[RencanaKehadiran]</td>
      <td class=inp>Maksimum Absen:</td>
      <td class=ul1>$w[MaxAbsen]</td>
      </tr>
  <tr><td colspan=4><hr color=silver size=3></td></tr>
  <tr><td class=inp>Jenis Jadwal Tambahan:</td>
	  <td class=ul1 colspan=3><select name='JenisJadwalID'>$optjenisjadwal</select></td>
	  </tr>
  <tr><td class=inp>Tanggal Mulai Tambahan:</td>
      <td class=ul1 colspan=3>$opttglkuliah</td>
	  </tr>
  <tr><td class=inp>Hari:</td>
      <td class=ul1><select name='HariID'>$opthari</select></td>
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
        <input type=text name='Kapasitas' value='$w[Kapasitas]' size=4 maxlength=5 />
        <sub>orang</sub>
        </td>
      </tr>
  <tr><td class=inp>Rencana Kehadiran Tambahan:</td>
      <td class=ul1><input type=text name='_RencanaKehadiranRes' value='$w[_RencanaKehadiranRes]' size=4 maxlength=4></td>
      <td class=inp>Maksimum Absen Tambahan:</td>
      <td class=ul1><input type=text name='_MaxAbsenRes' value='$w[_MaxAbsenRes]' size=4 maxlength=4></td>
      </tr>
  <tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' onclick="return cekJdwl()" />
      <input type=button name='Batal' value='Batal' onClick="window.close()" />
      </td></tr>
  </form>
  </table>

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
  function CariRuang(ProdiID, frm) {
    if (eval(frm + ".RuangID.value != ''")) {
      eval(frm + ".RuangID.focus()");
      showRuang(ProdiID, frm, eval(frm +".RuangID.value"), eval(frm +".HariID.value"),eval(frm +".JamMulai_h.value"), eval(frm +".JamMulai_n.value"), eval(frm +".JamSelesai_h.value"),eval(frm +".JamSelesai_n.value"), tahun, 'cariruang');
      toggleBox('cariruang', 1);
  }
  function AmbilHari()
  {	  var theDate = new Date();
		
		theDate.setFullYear(Number(frmJadwal.KuliahTanggal_y.value), Number(frmJadwal.KuliahTanggal_m.value)-1, Number(frmJadwal.KuliahTanggal_d.value));

		frmJadwal.HariID.value = theDate.getDay();
  }
  </script>
SCR;
}

function KembaliScript($md, $id, $resid, $w) {
  echo <<<ESD
  <script>
  function Kembali() {
    window.location="../$_SESSION[mnux].editlab.php?gos=Edit&md=$md&id=$id&resid=$resid&Kembali=1&KuliahTanggal=$w[KuliahTanggal]"+
      "&HariID=$w[HariID]&JamMulai=$w[JamMulai]&JamSelesai=$w[JamSelesai]&RuangID=$w[RuangID]&Kapasitas=$w[Kapasitas]&DosenID=$w[DosenID]"+
      "&_RencanaKehadiranRes=$w[_RencanaKehadiranRes]&_MaxAbsenRes=$w[_MaxAbsenRes]&KuliahTanggal=$w[KuliahTanggal]";
  }
  </script>
ESD;
}

function Simpan($md, $id, $resid, $Kembali) {
  $w = GetFields('jadwal', "JadwalID", $id, '*');
  
  $w['JenisJadwalID'] = $_REQUEST['JenisJadwalID'];
  $w['HariID'] = $_REQUEST['HariID'];
  $w['JamMulai'] = "$_REQUEST[JamMulai_h]:$_REQUEST[JamMulai_n]";
  $w['JamSelesai'] = "$_REQUEST[JamSelesai_h]:$_REQUEST[JamSelesai_n]";
  $w['RuangID'] = sqling($_REQUEST['RuangID']);
  $w['Kapasitas'] = $_REQUEST['Kapasitas']+0;
  $w['KuliahTanggal'] = "$_REQUEST[KuliahTanggal]";
  $w['_RencanaKehadiranRes'] = $_REQUEST['_RencanaKehadiranRes'];
  $w['_MaxAbsenRes'] = $_REQUEST['_MaxAbsenRes'];
  
  // *** Cek semuanya dulu ***
  $oke = '';
  if (!empty($w['RuangID'])) $oke .= CekRuang($w, $id, $resid);
  //if (!empty($w['RuangID'])) $oke .= CekJadwalLab($w, $id, $resid);
  // Ambil data MK
  $mk = GetFields('mk', "MKID", $w['MKID'], "Nama,MKKode,KurikulumID,SKS,Sesi");
  // Jika semuanya baik2 saja
  if (empty($oke)) {
    // Jika mode=edit
    if ($md == 0) {
      $s = "update jadwal
        set JenisJadwalID = '$w[JenisJadwalID]',
			HariID = '$w[HariID]',
            JamMulai = '$w[JamMulai]',
            JamSelesai = '$w[JamSelesai]',
            DosenID = '$w[DosenID]',
            RuangID = '$w[RuangID]',
            Kapasitas = '$w[Kapasitas]',
            KuliahTanggal = '$w[KuliahTanggal]',
			RencanaKehadiran = '$w[_RencanaKehadiranRes]',
			MaxAbsen = '$w[_MaxAbsenRes]',
			TglEdit = now(),
            LoginEdit = '$_SESSION[_Login]'
		where JadwalID='$resid'";
		//where JadwalResponsiID = '$resid' ";
      $r = _query($s);
      TutupScript();
    }
    elseif ($md == 1) {
      $s = "insert into jadwal
		(KodeID, TahunID, ProdiID, ProgramID, NamaKelas, MKID, MKKode, Nama, 
		JadwalRefID, KuliahTanggal, HariID, JamMulai, JamSelesai, SKSAsli, SKS, JenisJadwalID,
        DosenID, Kapasitas, RuangID, RencanaKehadiran, KehadiranMin, MaxAbsen, TglBuat, LoginBuat)
        values
        ('".KodeID."', '$w[TahunID]', '$w[ProdiID]', '$w[ProgramID]', '$w[NamaKelas]', '$w[MKID]', '$w[MKKode]', '$w[Nama]',
		'$id', '$w[KuliahTanggal]', '$w[HariID]', '$w[JamMulai]', '$w[JamSelesai]', '$w[SKSAsli]', 0, '$w[JenisJadwalID]',
        '$w[DosenID]', '$w[Kapasitas]', '$w[RuangID]', '$w[_RencanaKehadiranRes]', '$w[_RencanaKehadiranRes]', '$w[_MaxAbsenRes]', now(), '$_SESSION[_Login]')";
      $r = _query($s);
      TutupScript();
    }
  }
  // Jika ada yg salah
  else {
    KembaliScript($md, $id, $resid, $w);
	die(ErrorMsg('Jadwal Bentrok', 
      "Berikut adalah pesan kesalahannya: 
      <ol>$oke</ol>
      <hr size=1 color=silver />
      <p align=center>
      <input type=button name='Kembali' value='Kembali' onClick=\"javascript:Kembali()\" />
	  <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
  }
}
function CekRuang($w, $JadwalID, $JadwalResponsiID) {
  $s = "select j.JadwalID, j.MKKode, j.Nama, j.JamMulai, j.JamSelesai, j.DosenID, j.SKS,
    j.ProdiID, j.ProgramID, j.AdaResponsi,
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
	  and j.JadwalID <> '$JadwalResponsiID'
      ";
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
/*
function CekJadwalLab($w, $JadwalID, $JadwalResponsiID) {
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
      and jr.NA = 'N'
	  and jr.JadwalResponsiID <> '$JadwalResponsiID'";
	
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
          <td class=ul1>Responsi/Lab</td>
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
