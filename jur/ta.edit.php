<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Skripsi/Tugas Akhir");
echo <<<SCR
  <script src="../$_SESSION[mnux].edit.script.js"></script>
SCR;

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$TAID = GetSetVar('TAID');
$md = GetSetVar('md');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $TAID);

// *** Functions ***
function Edit($md, $TAID) {
  if ($md == 0) {
    $jdl = "Edit: Skripsi/Tugas Akhir";
    $w = GetFields('ta', 'TAID', $TAID, '*');
    $w['NamaMhsw'] = GetaField('mhsw', "KodeID='".KodeID."' and MhswID", $w['MhswID'], 'Nama');
    $w['DosenID'] = $w['Pembimbing'];
    $w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['Pembimbing'], 'Nama');
    $ro = "readonly=TRUE disabled=TRUE";
    $edtMhsw = "";
    // Apakah sudah lulus?
    if ($w['Lulus'] == 'Y')
      die(ErrorMsg("Error",
        "Skripsi/TA sudah diset lulus.<br />
        Data sudah tidak dapat diedit lagi.<br />
        Hubungi Sysadmin untuk informasi lebih lanjut.
        <hr size=1 color=silver />
        Opsi: <input type=button name='Tutup' value='Tutup' onClick='window.close()' >"));
  }
  elseif ($md == 1) {
    $jdl = "Tambah: Skripsi/Tugas Akhir";
    $w = array();
    $w['TahunID'] = $_SESSION['TahunID'];
    $w['TglDaftar'] = date('Y-m-d');
    $w['TglMulai'] = date('Y-m-d');
    $w['TglSelesai'] = date('Y-m-d');
    $ro = '';
    $edtMhsw = <<<SCR
        &raquo;
        <a href='#'
          onClick="javascript:CariMhsw('$_SESSION[FilterProdiID]', 'frmTA')" />Cari...</a> |
        <a href='#' onClick="javascript:frmTA.MhswID.value='';frmTA.NamaMhsw.value=''">Reset</a>
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
  CheckFormScript("MhswID,Judul,DosenID");
  echo <<<SCR
  <table class=box cellspacing=1 width=100%>
  <form name='frmTA' action='../$_SESSION[mnux].edit.php' method=POST
    onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='TAID' value='$TAID' />
  
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul>
      <input type=text name='TahunID' value='$w[TahunID]' size=5 maxlength=5 />
      </td></tr>
  <tr><td class=inp>Mahasiswa:</td>
      <td class=ul>
        <input type=text name='MhswID' value='$w[MhswID]' size=10 maxlength=30 $ro />
        <input type=text name='NamaMhsw' value='$w[NamaMhsw]' size=30 maxlength=50 $ro
          onKeyUp="javascript:CariMhsw('$_SESSION[FilterProdiID]', 'frmTA')"/>
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
  <tr><td class=inp>Judul Skripsi/TA:</td>
      <td class=ul><input type=text name='Judul' value='$w[Judul]'
        size=60 maxlength=255 /></td>
      </tr>
  <tr><td class=inp>Deskripsi/<br />Abstrak:</td>
      <td class=ul>
      <textarea name='Deskripsi' cols=70 rows=3>$w[Deskripsi]</textarea>
      </td></tr>
  <tr><td class=inp>Dosen Pembimbing:</td>
      <td class=ul>
      <input type=text name='DosenID' value='$w[DosenID]' size=10 maxlength=50 />
      <input type=text name='Dosen' value='$w[Dosen]' size=30 maxlength=50 onKeyUp="javascript:CariDosen('$_SESSION[ProdiID]', 'frmTA')" />
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[ProdiID]', 'frmTA')" />Cari...</a> |
      <a href='#' onClick="javascript:frmTA.DosenID.value='';frmTA.Dosen.value=''">Reset</a>
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
function Simpan($md, $TAID) {
  //Ambil parameters
  $TahunID = sqling($_REQUEST['TahunID']);
  $MhswID = sqling($_REQUEST['MhswID']);
  $Judul = sqling($_REQUEST['Judul']);
  $Deskripsi = sqling($_REQUEST['Deskripsi']);
  $TglDaftar = "$_REQUEST[TglDaftar]";
  $TglMulai = "$_REQUEST[TglMulai]";
  $TglSelesai = "$_REQUEST[TglSelesai]";
  $DosenID = sqling($_REQUEST['DosenID']);
  $Status = sqling($_REQUEST['Status']);
  if ($md == 0) {
    $s = "update ta
      set Judul = '$Judul',
          Deskripsi = '$Deskripsi',
          TglDaftar = '$TglDaftar',
          TglMulai = '$TglMulai',
          TglSelesai = '$TglSelesai',
          Pembimbing = '$DosenID',
          LoginEdit = '$_SESSION[_Login]',
          Status = '$Status',
          TanggalEdit = now(),
          TahunID = '$TahunID'
      where TAID = '$TAID' ";
    $r = _query($s);
    TutupScript();
  }
  elseif ($md == 1) {
    // Sudah ambil matakuliah skripsi/TA
    $blm = GetaField("krs k
      left outer join mk mk on k.MKID = mk.MKID",
      "k.KodeID='".KodeID."' and k.NA = 'N' and mk.TugasAkhir='Y' and k.MhswID", $MhswID, "count(k.MKID)");
    //echo "<h1>&raquo; $blm</h1>";

    if ($blm == 0) {
      die(ErrorMsg("Error",
        "Mahasiswa ini belum mengambil mata kuliah Skripsi/Tugas Akhir.<br />
        Hubungi Tata Usaha Program Studi untuk informasi lebih lanjut.
        <hr size=1 color=silver />
        <input type=button name='Tutup' value='Batal' onClick='window.close()' />"));
    }
    // Apakah sudah pernah mendaftarkan TA?
    $ada = GetFields('ta', "NA='N' and KodeID='".KodeID."' and MhswID",
      $MhswID, '*');
    if (empty($ada)) {
      $s = "insert into ta
        (TahunID, KodeID, MhswID,
        TglDaftar, TglMulai, TglSelesai,
        Judul, Deskripsi, Pembimbing,
        LoginBuat, TanggalBuat)
        values
        ('$TahunID', '".KodeID."', '$MhswID',
        '$TglDaftar', '$TglMulai', '$TglSelesai',
        '$Judul', '$Deskripsi', '$DosenID',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
      TutupScript();
    }
    else {
      echo ErrorMsg("Error",
        "Mahasiswa ini telah mendaftarkan diri untuk skripsi/ta sebelumnya.<br />
        Selesaikan dahulu administrasi pendaftaran skripsi/ta sebelumnya.
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
