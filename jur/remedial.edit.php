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
$_remTahun=GetSetVar('_remTahun');
$_remProdi=GetSetVar('_remProdi');
$_remProg=GetSetVar('_remProg');

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
  {	  if ($md == 0) $jdl = "Edit Jadwal Remedial";
	  elseif ($md == 1) $jdl = "Tambah Jadwal Remedial";
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
		$w['BiayaKhusus'] = $_REQUEST['BiayaKhusus'];
		$w['Biaya'] = $_REQUEST['Biaya'];
		$w['NamaBiaya'] = $_REQUEST['NamaBiaya'];
		$w['DosenID'] = $_REQUEST['DosenID'];
		$w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
	  }
  else
  {
	  if ($md == 0) {
		$jdl = "Edit Jadwal Remedial";
		$w = GetFields('jadwalremedial', 'JadwalRemedialID', $id, '*');
		$w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
		$w['JamMulai'] = substr($w['JamMulai'], 0, 5);
		$w['JamSelesai'] = substr($w['JamSelesai'], 0, 5);
	  }
	  elseif ($md == 1) {
		$jdl = "Tambah Jadwal Remedial";
		$w = array();
		$w['Dosen'] = '';
		$w['ProgramID'] = $_SESSION['_remProg'];
		$w['TglMulai'] = date('Y-m-d');
		$w['TglSelesai'] = date('Y-m-d');
		$w['HariID'] = date('w');
		$w['BiayaKhusus'] = 'N';
	  }
	  else {
	  }
  }
  // Parameters
  JdwlScript();
  $prodi = GetFields('prodi', "KodeID='".KodeID."' and ProdiID", $_SESSION['_remProdi'], '*');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID',
    $w['ProgramID'], "KodeID='".KodeID."'", 'ProgramID');
  $opthari = GetOption2('hari', "Nama", 'HariID', $w['HariID'], '', 'HariID');
  $opttglmulai = GetDateOption2($w['TglMulai'], 'TglMulai', "AmbilHari('TglMulai', 'frmJadwal')");
  $opttglselesai = GetDateOption2($w['TglSelesai'], 'TglSelesai', "");
  $ck_biayakhusus = ($w['BiayaKhusus'] == 'Y')? 'checked' : '';
  
  $TanggalPenting = AmbilTanggalPenting($w['JadwalRemedialID']);
  // Tampilkan
  CheckFormScript("ProgramID,HariID,JamMulai,JamSelesai,DosenID,MKID");
  TampilkanJudul($jdl);
  echo <<<END
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmJadwal' action='../$_SESSION[mnux].edit.php' method=POST onSubmit="return CheckForm(this)">
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='_remTahun' value='$_SESSION[_remTahun]' />
  <input type=hidden name='_remProdi' value='$_SESSION[_remProdi]' />
  <input type=hidden name='id' value='$id' />
  
  <tr><td class=inp>Program Studi:</td>
      <td class=ul1><b>$prodi[Nama]</b> <sup>($_SESSION[_remProdi])</sup></td>
      <td class=inp>Program:</td>
      <td class=ul1><select name='_remProg'>$optprg</select></td>
      </tr>
  <tr><td class=inp>Tanggal Mulai Remedial:</td>
      <td class=ul1>$opttglmulai</td>
	  <td class=inp>Tanggal Selesai Remedial:</td>
	  <td class=ul1>$opttglselesai</td>
	  </tr>

  <tr><td class=inp>Matakuliah:</td>
      <td class=ul1 colspan=3 nowrap>
      <input type=hidden name='MKID' value='$w[MKID]' />
      <input type=text name='MKKode' value='$w[MKKode]' size=10 maxlength=50 />
      <input type=text name='MKNama' value='$w[Nama]' size=30 maxlength=50 onKeyUp="javascript:CariMK('$_SESSION[_remProdi]', 'frmJadwal')"/>
      <input type=text name='SKS' value='$w[SKS]' size=3 maxlength=3> <sub>SKS</sub>
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariMK('$_SESSION[_remProdi]', 'frmJadwal')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwal.MKID.value='';frmJadwal.MKKode.value='';frmJadwal.MKNama.value='';frmJadwal.SKS.value=0">Reset</a>
      </div>
      </td>
      </tr>

  <tr><td class=inp>Dosen Pengampu:</td>
      <td class=ul1 colspan=3 nowrap>
      <input type=text name='DosenID' value='$w[DosenID]' size=10 maxlength=50 />
      <input type=text name='Dosen' value='$w[Dosen]' size=30 maxlength=50 onKeyUp="javascript:CariDosen('$_SESSION[_remProdi]', 'frmJadwal')" />
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[_remProdi]', 'frmJadwal')" />Cari...</a> |
      <a href='#' onClick="javascript:frmJadwal.DosenID.value='';frmJadwal.Dosen.value=''">Reset</a>
      </div>
      </td>
      </tr>

  <tr><td class=inp>Kelas:</td>
      <td class=ul1><input type=text name='NamaKelas' value='$w[NamaKelas]' size=10 maxlength=10 /></td>
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
  <tr><td class=inp>Tanggal<sup>2</sup> Penting:</td>
	  <td colspan=3>
			<table id='TanggalPenting' border=0 cellspacing=0 cellpadding=0 width=100%>
				$TanggalPenting
			</table>
		    <input type=button name='TambahTanggal' value='Tambah Tanggal Remedial' onClick="fnTambahTanggal()">
			<input type=hidden id='StringTanggalPenting' name='StringTanggalPenting' value=''>
	  </td>
	  </tr>
  <tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' onClick="return SimpanKeString()"/>
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
  function CariRuang(ProdiID, frm) {
    if (eval(frm + ".RuangID.value != ''")) {
      eval(frm + ".RuangID.focus()");
      showRuang(ProdiID, frm, eval(frm +".RuangID.value"), 'cariruang');
      toggleBox('cariruang', 1);
    }
  }
  function AmbilHari(name, frm)
  {	  var theDate = new Date();
		
		theDate.setFullYear(Number(eval(frm+'.'+name+'_y.value')), Number(eval(frm+'.'+name+'_m.value'))-1, Number(eval(frm+'.'+name+'_d.value')));

		frmJadwal.HariID.value = theDate.getDay();
  }
  
  // Fungsi untuk menambah entry ke dalam tabel Tanggal-tanggal penting
  function fnTambahTanggal()
  {	  
	  var tbl = document.getElementById('TanggalPenting');
	  var count = tbl.rows.length;
	  var nextid = 0;
	  if(count == 0) nextid = 0;
	  else nextid = parseInt(tbl.rows[count-1].id) + 1; 
	  
	  var d = new Date();
	  var tglremid = "<input type=hidden id='PresensiRemedialID"+nextid+"' name='PresensiRemedialID"+nextid+"' value='0'>";
	  var tgloption = 
		"<select id='Tanggal"+nextid+"_d' name='Tanggal"+nextid+"_d'>"+getnumberoption(1, 31, 1, d.getDate())+"</select> "+
        "<select id='Tanggal"+nextid+"_m' name='Tanggal"+nextid+"_m'>"+getmonthoption(d.getMonth()+1)+"</select> "+
		"<select id='Tanggal"+nextid+"_y' name='Tanggal"+nextid+"_y'>"+getnumberoption(1930, d.getFullYear()+2, 1, d.getFullYear())+"</select> ";
	  var jamoption = 
		"<select id='JamMulai"+nextid+"_h' name='JamMulai"+nextid+"_h'>"+getnumberoption(0, 23, 1, '09')+"</select>:"+
		"<select id='JamMulai"+nextid+"_n' name='JamMulai"+nextid+"_n'>"+getnumberoption(0, 59, 1, '00')+"</select> - "+
		"<select id='JamSelesai"+nextid+"_h' name='JamSelesai"+nextid+"_h'>"+getnumberoption(0, 23, 1, '09')+"</select>:"+
		"<select id='JamSelesai"+nextid+"_n' name='JamSelesai"+nextid+"_n'>"+getnumberoption(0, 59, 1, '00')+"</select> ";
	  var eventtext = "<font size=0.9em>Keterangan: </font><input type=text id='EventPenting"+nextid+"' name='EventPenting"+nextid+"' value='' size=30 maxlength=100><a href='#self' onClick=\"fnDeleteTanggal('"+nextid+"')\">&times;</a>";
	  
		try {
			var newRow = tbl.insertRow(count);//creation of new row
			newRow.setAttribute('id', nextid);
			var newCell = newRow.insertCell(0);//first  cell in the row
			newCell.innerHTML = tglremid+tgloption;
			var newCell = newRow.insertCell(1);//first  cell in the row
			newCell.innerHTML = jamoption;
			var newCell = newRow.insertCell(2);//second cell in the row
			newCell.setAttribute('class', 'inpx');
			newCell.innerHTML = eventtext;//insertion of the text box and the remove text using their variable
		} catch (ex) {
			alert(ex); //if exception occurs
		}  
  }
  
  // Fungsi untuk menghapus entry dalam tabel Tanggal-tanggal penting dengan index "nomer"
  function fnDeleteTanggal(nomer)
  {	  	try 
		{	var indexOfRows = document.getElementById('Tanggal'+nomer+'_d').parentNode.parentNode.rowIndex;
			var table = document.getElementById('TanggalPenting');//identification of table
			table.deleteRow(indexOfRows);//deletion of the clicked row
			
		} catch (ex) {
			alert(ex);
		}
  }
  
  // Mengembalikan sebuah string untuk mensimulasikan dropdown dengan pilihan angka
  function getnumberoption(start, end, interval, selected) {
    temp = '';
	for (var i=start; i <= end; i+=interval) {
	  stri = padleft(i, 2, '0');
	  select = '';
	  if(i == selected) select = 'selected';
	  temp = temp+"<option "+select+">"+stri+"</option>";
	}
	return temp;
  }
  // Mengembalikan sebuah string untuk mensimulasikan dropdown dengan pilihan bulan-bulan
  function getmonthoption(selected)
  {	  var arrBulan = new Array('', 'Januari', 'Februari', 'Maret', 'April', 'Mai', 'Juni', 'Juli',
         'Agustus', 'September', 'Oktober', 'November', 'Desember');
	  temp = '';
	  max = arrBulan.length;
	  for (var k=1; k <= max; k++) {
		stri = padleft(k, 2, '0');
		select = '';
		if(k == selected) select = 'selected';
		temp += "<option value="+k+" "+select+">"+arrBulan[k]+"</option>";
	  }
	  return temp;
  }
  function padleft(str, thelength, pad)
  {	temp2 = str+'';
	extratext = '';
	if(temp2.length < thelength && temp2.length > 0)
	{	remaining = Math.floor((thelength-temp2.length)/temp2.length);
		for(var j=0; j < remaining; j++) extratext = extratext+''+pad;
	}
	return extratext+''+temp2;
  }
  
  // Fungsi untuk mengambil data yang didapat dari tabel Tanggal-tanggal penting dan memasukkannya ke dalam Elemen StringTanggalPenting
  // Data kemdian bisa disimpan melalui metode Save()
  function SimpanKeString()
  {	var tbl = document.getElementById('TanggalPenting');
	var count = tbl.rows.length;
	var tanggaldivider = '<|>';
	var isitanggaldivider = '!/!';
	var entryarray = new Array();
	var result = '';
	// Ambil Tanggal-tanggal penting dan simpan ke dalam array
	for(var i = 0; i < count; i++)
	{	trid = parseInt(tbl.rows[i].id);
		tglremid = document.getElementById('PresensiRemedialID'+trid).value;
		tgl = document.getElementById('Tanggal'+trid+'_y').value+'-'+padleft(document.getElementById('Tanggal'+trid+'_m').value, 2, '0')+'-'+padleft(document.getElementById('Tanggal'+trid+'_d').value, 2, '0');
		jammulai = padleft(document.getElementById('JamMulai'+trid+'_h').value, 2, '0')+':'+padleft(document.getElementById('JamMulai'+trid+'_n').value, 2, '0');
		jamselesai = padleft(document.getElementById('JamSelesai'+trid+'_h').value, 2, '0')+':'+padleft(document.getElementById('JamSelesai'+trid+'_n').value, 2, '0');
		theevent = document.getElementById('EventPenting'+trid).value;
		
		// Jika isi dari textbox tentang isi dari event kosong....
		if(theevent == '') 
		{	alert("Kolom 'Keterangan' pada baris "+(i+1)+" tidak boleh kosong");
			return false;
		}
		entryarray[i] = tglremid+isitanggaldivider+tgl+isitanggaldivider+jammulai+isitanggaldivider+jamselesai+isitanggaldivider+theevent;
		
	}
	entryarray.sort();
	
	for(var i = 0; i < entryarray.length; i++)
	{	if(i == 0) result += entryarray[i];
		else result += tanggaldivider+entryarray[i];
	}
	
	document.getElementById('StringTanggalPenting').value = result;
	return true;
  }
  </script>
SCR;
}

function AmbilTanggalPenting($jrid)
{	$returnstring = '';
	
	$s = "select * from presensiremedial where JadwalRemedialID='$jrid' and KodeID='".KodeID."'";
	$r = _query($s);
	$n = 0;
	while($w = _fetch_array($r))
	{	$opttgl = GetDateOptionWithID($w['Tanggal'], 'Tanggal'.$n);
		$optjammulai = GetTimeOptionWithID($w['JamMulai'], 'JamMulai'.$n);
		$optjamselesai = GetTimeOptionWithID($w['JamSelesai'], 'JamSelesai'.$n);
		$returnstring.= "<input type=hidden id='PresensiRemedialID".$n."' name='PresensiRemedialID".$n."' value='$w[PresensiRemedialID]'>
						 <tr id=$n>
							 <td class=u1l>$opttgl</td>
							 <td class=ul1>$optjammulai-$optjamselesai</td>
							 <td class=inpx><font size=0.9em>Keterangan: </font><input type=text id='EventPenting".$n."' name='EventPenting".$n."' value='$w[Keterangan]' size=30 maxlength=100><a href='#self' onClick=\"fnDeleteTanggal('".$n."')\">&times;</a></td>
						 </tr>";
		$n++;
	}
	return $returnstring;
}	


function GabungkanScript($md, $id, $w) {
  echo <<<ESD
  <script>
  function Gabung() {
    window.location="../$_SESSION[mnux].edit.php?gos=Simpan&md=$md&id=$id&_remTahun=$w[_remTahun]"+
      "&_remProg=$w[ProgramID]&_remProdi=$w[ProdiID]"+
      "&MKID=$w[MKID]&MKKode=$w[MKKode]&MKNama=$w[MKNama]&SKS=$w[SKS]&DosenID=$w[DosenID]"+
      "&NamaKelas=$w[NamaKelas]&RencanaKehadiran=$w[RencanaKehadiran]"+
      "&KehadiranMin=$w[KehadiranMin]&MaxAbsen=$w[MaxAbsen]"+
      "&BiayaKhusus=$w[BiayaKhusus]&Biaya=$w[Biaya]&NamaBiaya=$w[NamaBiaya]&TglMulai=$w[TglMulai]&TglSelesai=$w[TglSelesai]"+
      "&_Gabungkan=721222";
  }
  function Kembali() {
    window.location="../$_SESSION[mnux].edit.php?gos=Edit&md=$md&id=$id&_remTahun=$w[_remTahun]&Kembali=1"+
      "&_remProg=$w[ProgramID]&_remProdi=$w[ProdiID]"+
      "&MKID=$w[MKID]&MKKode=$w[MKKode]&MKNama=$w[MKNama]&SKS=$w[SKS]&DosenID=$w[DosenID]"+
      "&NamaKelas=$w[NamaKelas]&RencanaKehadiran=$w[RencanaKehadiran]"+
      "&KehadiranMin=$w[KehadiranMin]&MaxAbsen=$w[MaxAbsen]"+
      "&BiayaKhusus=$w[BiayaKhusus]&Biaya=$w[Biaya]&NamaBiaya=$w[NamaBiaya]&TglMulai=$w[TglMulai]&TglSelesai=$w[TglSelesai]";
  }
  </script>
ESD;
}
function Simpan($md, $id, $Kembali) {
  include_once "../util.lib.php";
  
  $_Gabungkan = $_REQUEST['_Gabungkan']+0;
  $w = array();
  $w['TahunID'] = sqling($_REQUEST['_remTahun']);
  $w['ProgramID'] = sqling($_REQUEST['_remProg']);
  $w['ProdiID'] = sqling($_REQUEST['_remProdi']);
  $w['MKID'] = $_REQUEST['MKID'];
  $w['MKKode'] = $_REQUEST['MKKode'];
  $w['MKNama'] = $_REQUEST['MKNama'];
  $w['SKS'] = $_REQUEST['SKS']+0;
  $w['DosenID'] = $_REQUEST['DosenID'];
  $w['NamaKelas'] = sqling($_REQUEST['NamaKelas']);
  $w['RencanaKehadiran'] = $_REQUEST['RencanaKehadiran']+0;
  $w['KehadiranMin'] = $_REQUEST['KehadiranMin']+0;
  $w['MaxAbsen'] = $_REQUEST['MaxAbsen']+0;
  $TanggalPenting = $_REQUEST['StringTanggalPenting'];
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
  //if (!empty($w['RuangID'])) $oke .= CekRuang($w, $id);
  //$oke .= CekDosen($w, $id);
  // Ambil data MK
  $mk = GetFields('mk', "MKID", $w['MKID'], "Nama,MKKode,KurikulumID,SKS,Sesi");
  // Jika semuanya baik2 saja
  if (empty($oke) || $_Gabungkan == 721222) {
    // Ambil Semua tanggal yang sudah terdaftar di database ke dalam array
	$arrTanggalYangAda = array();
	$s1 = "select PresensiRemedialID from presensiremedial where JadwalRemedialID='$id' and KodeID='".KodeID."'";
	$r1 = _query($s1);
	while($w1 = _fetch_array($r1)) $arrTanggalYangAda[] = $w1['PresensiRemedialID']; 
	
	// Bila TanggalPenting memiliki isi, masukkan/update tanggal2 penting yang terdaftar di database
	if(!empty($TanggalPenting))
	{	$arrTanggalPenting = explode('<|>', $TanggalPenting);
		
		foreach($arrTanggalPenting as $tanggal)
		{	$arr = explode('!/!', $tanggal);
			if($arr[0] == 0)
			{	$s1 = "insert into presensiremedial 
						set Tanggal = '$arr[1]', JamMulai = '$arr[2]:00', JamSelesai = '$arr[3]:00', Keterangan = '$arr[4]',
							JadwalRemedialID= '$id', KodeID='".KodeID."', LoginBuat='$_SESSION[_Login]', TanggalBuat=now()";
				$r1 = _query($s1);
			}
			else
			{	$s1 = "update presensiremedial
						set Tanggal = '$arr[1]', JamMulai = '$arr[2]:00', JamSelesai = '$arr[3]:00', Keterangan='$arr[4]',
							KodeID='".KodeID."', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
						where PresensiRemedialID='$arr[0]'";
				$r1 = _query($s1);
				$arrTanggalYangAda = RemoveElementFromArray($arr[0], $arrTanggalYangAda);
			}
			$n++;
		}
	}
	
	if(!empty($arrTanggalYangAda))
	{	foreach($arrTanggalYangAda as $tglremid)
		{	$s1 = "delete from presensiremedial where PresensiRemedialID='$tglremid' and KodeID='".KodeID."'";
			$r1 = _query($s1);
		}
	}
	
	// Jika mode=edit
    if ($md == 0) {
      $s = "update jadwalremedial
        set ProgramID = '$w[ProgramID]',
			ProdiID = '$w[ProdiID]',
            MKID = '$w[MKID]',
            MKKode = '$mk[MKKode]',
            Nama = upper('$mk[Nama]'),
            NamaKelas = upper('$w[NamaKelas]'),
            SKS = '$w[SKS]',
            SKSAsli = '$mk[SKS]',
            DosenID = '$w[DosenID]',
            RencanaKehadiran = '$w[RencanaKehadiran]',
            KehadiranMin = '$w[KehadiranMin]',
			MaxAbsen = '$w[MaxAbsen]',
            Kapasitas = '$w[Kapasitas]',
            TglMulai = '$w[TglMulai]',
			TglSelesai = '$w[TglSelesai]',
			BiayaKhusus = '$w[BiayaKhusus]',
            Biaya = '$w[Biaya]',
			NamaBiaya = '$w[NamaBiaya]',
            TglEdit = now(),
            LoginEdit = '$_SESSION[_Login]'
        where JadwalRemedialID = '$id' ";
      $r = _query($s);
	  
	  TutupScript();
    }
    elseif ($md == 1) {
      $s = "insert into jadwalremedial
        (KodeID, TahunID, ProdiID, ProgramID,
        NamaKelas, MKID, MKKode, Nama, TglMulai, TglSelesai, 
        SKSAsli, SKS,
        DosenID, RencanaKehadiran, KehadiranMin, MaxAbsen, 
        BiayaKhusus, Biaya, NamaBiaya, 
        TglBuat, LoginBuat)
        values
        ('".KodeID."', '$w[TahunID]', '$w[ProdiID]', '$w[ProgramID]',
        upper('$w[NamaKelas]'), '$w[MKID]', '$mk[MKKode]', upper('$mk[Nama]'), '$w[TglMulai]', '$w[TglSelesai]', 
        '$mk[SKS]', '$w[SKS]',
        '$w[DosenID]', '$w[RencanaKehadiran]', '$w[KehadiranMin]', '$w[MaxAbsen]',
        '$w[BiayaKhusus]', '$w[Biaya]', '$w[NamaBiaya]',
        now(), '$_SESSION[_Login]')";
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
      <input type=button name='Paksakan' value='Gabungkan Jadwal'
        onClick='javascript:Gabung()' />
      </p>"));
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
function GetDateOptionWithID($dt, $nm='dt') {
  $arr = Explode('-', $dt);
  $_dy = GetNumberOption(1, 31, $arr[2]);
  $_mo = GetMonthOption($arr[1]);
  $_yr = GetNumberOption(1930, Date('Y')+2, $arr[0]);
  return "<select id='".$nm."_d' name='".$nm."_d'>$_dy</select>
    <select id='".$nm."_m' name='".$nm."_m'>$_mo</select>
    <select id='".$nm."_y' name='".$nm."_y'>$_yr</select>";
}
function GetTimeOptionWithID($dt, $nm='tm') {
  $arr = Explode(':', $dt);
  $_hr = GetNumberOption(0, 23, $arr[0]);
  $_mn = GetNumberOption(0, 59, $arr[1]);
  return "<select id='".$nm."_h' name='".$nm."_h'>$_hr</select>
    <select id='".$nm."_n' name='".$nm."_n'>$_mn</select>";
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
