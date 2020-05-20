<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Praktek Kerja");
echo <<<SCR
  <script src="../$_SESSION[mnux].edit.script.js"></script>
SCR;

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$TahunID = GetSetVar('TahunID');
$PraktekKerjaID = GetSetVar('PraktekKerjaID');
$md = GetSetVar('md');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $PraktekKerjaID);

// *** Functions ***
function Edit($md, $PraktekKerjaID) {
  if ($md == 0) {
    $jdl = "Edit: Praktek Kerja";
    $w = GetFields('praktekkerja', 'PraktekKerjaID', $PraktekKerjaID, '*');
    $w['NamaMhsw'] = GetaField('mhsw', "KodeID='".KodeID."' and MhswID", $w['MhswID'], 'Nama');
    $w['DosenID1'] = $w['Pembimbing1'];
    $w['Dosen1'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['Pembimbing1'], 'Nama');
    $w['DosenID2'] = $w['Pembimbing2'];
    $w['Dosen2'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['Pembimbing2'], 'Nama');
    $ro = "readonly=TRUE disabled=TRUE";
    $edtMhsw = "";
    // Apakah sudah lulus?
    if ($w['Lulus'] == 'Y')
      die(ErrorMsg("Error",
        "Praktek Kerja sudah diset lulus.<br />
        Data sudah tidak dapat diedit lagi.<br />
        Hubungi Sysadmin untuk informasi lebih lanjut.
        <hr size=1 color=silver />
        Opsi: <input type=button name='Tutup' value='Tutup' onClick='window.close()' >"));
  }
  elseif ($md == 1) {
    $jdl = "Tambah Praktek Kerja";
    $w = array();
    $w['TahunID'] = $_SESSION['TahunID'];
    $w['TglDaftar'] = date('Y-m-d');
    $w['TglMulai'] = date('Y-m-d');
    $w['TglSelesai'] = date('Y-m-d');
    $ro = '';
    $edtMhsw = <<<SCR
        &raquo;
        <a href='#'
          onClick="javascript:CariMhsw('$_SESSION[FilterProdiID]', 'frmPraktek')" />Cari...</a> |
        <a href='#' onClick="javascript:frmPraktek.MhswID.value='';frmPraktek.NamaMhsw.value=''">Reset</a>
SCR;
  }
  else die(ErrorMsg("Error",
    "<p align=center>Mode edit: <b>$md</b> tidak dikenali oleh sistem.<br />
    <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));
  // Tampilkan
  TampilkanJudul($jdl);
  
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
	setDatePicker('TglDaftar','TglDaftar','');
	setDatePicker('TglMulai','TglSelesai','end');
	setDatePicker('TglSelesai','TglMulai','start');
});
</script>";

function GetDateOption3($value,$name){
	$a = "<input type=hidden name=".$name." id=alt".$name." value=".$value." /><input type=text id=".$name." value=".$value." readonly=true />";
	return $a;
}

  // parameters
  $opttgldaftar = GetDateOption3($w['TglDaftar'], 'TglDaftar');
  $opttglmulai  = GetDateOption3(date('Y-m-d'), 'TglMulai');
  $opttglselesai= GetDateOption3(date('Y-m-d'), 'TglSelesai');
  CheckFormScript("MhswID,NamaPerusahaan,NamaPekerjaan,DosenID");
  echo <<<SCR
  <table class=box cellspacing=1 width=100%>
  <form name='frmPraktek' action='../$_SESSION[mnux].edit.php' method=POST
    onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='PraktekKerjaID' value='$PraktekKerjaID' />
  
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul>
      <input type=text name='TahunID' value='$w[TahunID]' size=5 maxlength=5 />
      </td></tr>
  <tr><td class=inp>Mahasiswa:</td>
      <td class=ul>
        <input type=text name='MhswID' value='$w[MhswID]' size=10 maxlength=30 $ro />
        <input type=text name='NamaMhsw' value='$w[NamaMhsw]' size=30 maxlength=50 $ro
          onKeyUp="javascript:CariMhsw('$_SESSION[FilterProdiID]', 'frmPraktek')"/>
        $edtMhsw
      </td></tr>
  <tr><td class=inp>Tgl Daftar:</td>
      <td class=ul>$opttgldaftar</td>
      </tr>
  <tr><td class=inp>Tgl Mulai:</td>
      <td class=ul>$opttglmulai</td>
      </tr>
  <tr><td class=inp>Tgl Selesai:</td>
      <td class=ul>$opttglselesai</td>
      </tr>
  <tr><td class=inp>Nama Perusahaan:</td>
      <td class=ul><input type=text name='NamaPerusahaan' value='$w[NamaPerusahaan]'
        size=60 maxlength=100 /></td>
      </tr>
  <tr><td class=inp>Alamat Perusahaan:</td>
      <td class=ul><input type=text name='AlamatPerusahaan' value='$w[AlamatPerusahaan]'
        size=60 maxlength=255 /></td>
      </tr>
  <tr><td class=inp>Kota Perusahaan:</td>
      <td class=ul><input type=text name='KotaPerusahaan' value='$w[KotaPerusahaan]'
        size=15 maxlength=100 /></td>
      </tr>
  <tr><td class=inp>Telepon Perusahaan:</td>
      <td class=ul><input type=text name='TeleponPerusahaan' value='$w[TeleponPerusahaan]'
        size=15 maxlength=20 /></td>
      </tr>
  <tr><td class=inp>Nama Pekerjaan:</td>
      <td class=ul><input type=text name='NamaPekerjaan' value='$w[NamaPekerjaan]'
        size=60 maxlength=100 /></td>
      </tr>
  <tr><td class=inp>Deskripsi:</td>
      <td class=ul>
      <textarea name='Deskripsi' cols=60 rows=3>$w[Deskripsi]</textarea>
      </td></tr>
  <tr><td class=inp>Dosen Pembimbing I:</td>
      <td class=ul>
      <input type=text name='DosenID1' value='$w[DosenID1]' size=10 maxlength=50 />
      <input type=text name='Dosen1' value='$w[Dosen1]' size=30 maxlength=50 onKeyUp="javascript:CariDosen('$_SESSION[ProdiID]', 'frmPraktek','1')" />
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[ProdiID]', 'frmPraktek','1')" />Cari...</a> |
      <a href='#' onClick="javascript:frmPraktek.DosenID1.value='';frmPraktek.Dosen1.value=''">Reset</a>
      </td></tr>
   <tr><td class=inp>Dosen Pembimbing II:</td>
      <td class=ul>
      <input type=text name='DosenID2' value='$w[DosenID2]' size=10 maxlength=50 />
      <input type=text name='Dosen2' value='$w[Dosen2]' size=30 maxlength=50 onKeyUp="javascript:CariDosen('$_SESSION[ProdiID]', 'frmPraktek','2')" />
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[ProdiID]', 'frmPraktek','1')" />Cari...</a> |
      <a href='#' onClick="javascript:frmPraktek.DosenID.value='';frmPraktek.Dosen.value=''">Reset</a>
      </td></tr>
      <tr><td class=inp>Tindakan:</td>
      <td class=ul>
      <input type=radio value='1' name='Status'> <img src="../img/diterima.gif" /> Setujui <input type=radio name='Status' value='2'> <img src="../img/ditolak.gif" /> Tolak
      </td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  </form>
  </table>
  
  <div class='box0' id='caridosen'></div>
  <div class='box0' id='carimhsw'></div>
  
  <script>
  <!--
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
  function CariDosen(ProdiID, frm, id) {
    if (eval(frm + ".Dosen" + id + ".value != ''")) {
      eval(frm + ".Dosen" + id + ".focus()");
      showDosen(ProdiID, frm, eval(frm +".Dosen" + id + ".value"), 'caridosen', id);
      toggleBox('caridosen', 1);
    }
  }
  function CariMhsw(ProdiID, frm) {
    if (eval(frm + ".NamaMhsw.value != ''")) {
      eval(frm + ".NamaMhsw.focus()");
      showMhsw(ProdiID, frm, eval(frm +".NamaMhsw.value"), 'carimhsw');
      toggleBox('carimhsw', 1);
    }
  }
  //-->
  </script>
SCR;
}
function Simpan($md, $PraktekID) {
  //Ambil parameters
  $TahunID = sqling($_REQUEST['TahunID']);
  $MhswID = sqling($_REQUEST['MhswID']);
  $NamaPerusahaan = sqling($_REQUEST['NamaPerusahaan']);
  $AlamatPerusahaan = sqling($_REQUEST['AlamatPerusahaan']);
  $KotaPerusahaan = sqling($_REQUEST['KotaPerusahaan']);
  $TeleponPerusahaan = sqling($_REQUEST['TeleponPerusahaan']);
  $NamaPekerjaan = sqling($_REQUEST['NamaPekerjaan']);
  $Deskripsi = sqling($_REQUEST['Deskripsi']);
  $TglDaftar = "$_REQUEST[TglDaftar_]";
  $TglMulai = "$_REQUEST[TglMulai]";
  $TglSelesai = "$_REQUEST[TglSelesai]";
  $DosenID1 = sqling($_REQUEST['DosenID1']);
  $DosenID2 = sqling($_REQUEST['DosenID2']);
  $Status = sqling($_REQUEST['Status']);
  if ($md == 0) {
    $s = "update praktekkerja
      set 
      TahunID = '$TahunID',
      NamaPerusahaan = '$NamaPerusahaan',
		  AlamatPerusahaan = '$AlamatPerusahaan',
		  KotaPerusahaan = '$KotaPerusahaan',
		  TeleponPerusahaan = '$TeleponPerusahaan',
		  NamaPekerjaan = '$NamaPekerjaan',
          Deskripsi = '$Deskripsi',
          TglDaftar = '$TglDaftar',
          TglMulai = '$TglMulai',
          TglSelesai = '$TglSelesai',
          Pembimbing1 = '$DosenID1',
          Pembimbing2 = '$DosenID2',
          Status = '$Status',
          LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where PraktekKerjaID = '$PraktekID' ";
    $r = _query($s);
    TutupScript();
  }
  elseif ($md == 1) {
    // Sudah ambil matakuliah praktek kerja
    $blm = GetaField("krs k
      left outer join mk mk on k.MKKode = mk.MKKode",
      "k.KodeID='".KodeID."' and k.NA = 'N' and mk.PraktekKerja='Y' and k.MhswID", $MhswID, "count(k.MKID)");
    //echo "<h1>&raquo; $blm</h1>";

    if ($blm == 0) {
      die(ErrorMsg("Error",
        "Mahasiswa ini belum mengambil mata kuliah Praktek Kerja.<br />
        Hubungi Tata Usaha Program Studi untuk informasi lebih lanjut.
        <hr size=1 color=silver />
        <input type=button name='Tutup' value='Batal' onClick='window.close()' />"));
    }
    // Apakah sudah pernah mendaftar?
    $ada = GetFields('praktekkerja', "Lulus='N' and KodeID='".KodeID."' and MhswID",
      $MhswID, '*');
    if (empty($ada)) {
      $s = "insert into praktekkerja
        (TahunID, KodeID, MhswID,
        TglDaftar, TglMulai, TglSelesai,
        NamaPerusahaan, AlamatPerusahaan, KotaPerusahaan, TeleponPerusahaan, 
		NamaPekerjaan, Deskripsi, Pembimbing1,Pembimbing2,
        LoginBuat, TanggalBuat)
        values
        ('$TahunID', '".KodeID."', '$MhswID',
        '$TglDaftar', '$TglMulai', '$TglSelesai',
        '$NamaPerusahaan', '$AlamatPerusahaan', '$KotaPerusahaan', '$TeleponPerusahaan', 
		'$NamaPekerjaan', '$Deskripsi', '$DosenID1','$DosenID2',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
      TutupScript();
    }
    else {
      echo ErrorMsg("Error",
        "Mahasiswa ini telah mendaftarkan diri untuk praktek kerja sebelumnya.<br />
        Selesaikan dahulu administrasi pendaftaran praktek kerja sebelumnya.
        <hr size=1 color=silver />
        <input type=button name='Tutup' value='Tutup'
          onClick='window.close()' />");
    }
  }
  else echo (ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali oleh sistem.
    <hr size=1 color=silver />
    <p align=center><input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
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
