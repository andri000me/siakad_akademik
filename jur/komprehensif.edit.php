<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 21 Agustus 2008

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Ujian Komprehensif");
echo <<<SCR
  <script src="../$_SESSION[mnux].edit.script.js"></script>
SCR;

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$KompreID = GetSetVar('KompreID');
$md = GetSetVar('md');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $KompreID);

// *** Functions ***
function Edit($md, $KompreID) {
  if ($md == 0) {
    $jdl = "Edit: Komprehensif";
    $w = GetFields('kompre', 'KompreID', $KompreID, '*, LEFT(JamMulai, 5) as JM, LEFT(JamSelesai, 5) as JS');
    $w['NamaMhsw'] = GetaField('mhsw', "KodeID='".KodeID."' and MhswID", $w['MhswID'], 'Nama');
    $w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');
    $ro = "readonly=TRUE disabled=TRUE";
    $edtMhsw = "";
    // Apakah sudah lulus?
    if ($w['Lulus'] == 'Y')
      die(ErrorMsg("Error",
        "Skripsi/Komprehensif sudah diset lulus.<br />
        Data sudah tidak dapat diedit lagi.<br />
        Hubungi Sysadmin untuk informasi lebih lanjut.
        <hr size=1 color=silver />
        Opsi: <input type=button name='Tutup' value='Tutup' onClick='window.close()' >"));
  }
  elseif ($md == 1) {
    $jdl = "Tambah: Komprehensif";
    $w = array();
    $w['TahunID'] = $_SESSION['TahunID'];
    $w['TanggalDaftar'] = date('Y-m-d');
    $w['TanggalUjian'] = date('Y-m-d');
    $ro = '';
    $edtMhsw = <<<SCR
        &raquo;
        <a href='#'
          onClick="javascript:CariMhsw('$_SESSION[FilterProdiID]', 'frmKomprehensif')" />Cari...</a> |
        <a href='#' onClick="javascript:frmKomprehensif.MhswID.value='';frmKomprehensif.NamaMhsw.value=''">Reset</a>
SCR;
  }
  else die(ErrorMsg("Error",
    "<p align=center>Mode edit: <b>$md</b> tidak dikenali oleh sistem.<br />
    <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));
  // Tampilkan
  TampilkanJudul($jdl);
  // parameters
  $opttgldaftar = GetDateOption($w['TanggalDaftar'], 'TanggalDaftar');
  $waktukompre = '';
  
  // Bila PilihanKompre = 'Y', berarti setiap mata uji memiliki jadwal masing2. Bila PilihanKompre = 'N', Keluarkan satu pilihan Tanggal dan Waktu
  $ProdiIDMhsw = GetaField('mhsw', "MhswID='$w[MhswID]' and KodeID", KodeID, "ProdiID");
  $PilihanKompre = GetaField('prodi', "ProdiID='$ProdiIDMhsw' and KodeID", KodeID, "PilihanKompre");
  
  $opttglmulai  = GetDateOption($w['TanggalUjian'], 'TanggalUjian');
	$optjammulai = GetTimeOption($w['JM'], 'JamMulai'.$i);
	$optjamselesai = GetTimeOption($w['JS'], 'JamSelesai'.$i);
	$waktukompre  = "<tr><td class=inp>Tanggal Ujian:</td>
						 <td class=ul1>$opttglmulai</td></tr>
					 <tr><td class=inp>Jam Mulai Ujian:</td>
						  <td class=ul1>$optjammulai</td></tr>
					  <tr><td class=inp>Jam Selesai Ujian:</td>
						  <td class=ul1>$optjamselesai</td></tr>
					 <tr><td class=inp>Ruang:</td>
						  <td class=ul1><input type=hidden name='Kapasitas' value=''>
								<input type=text name='RuangID' value='$w[RuangID]' size=5 maxlength=50 
								  onKeyUp=\"javascript:CariRuang('$_SESSION[FilterProdiID]', 'frmKomprehensif')\" />
								&raquo;
							  <a href='#'
								onClick=\"javascript:CariRuang('$_SESSION[FilterProdiID]', 'frmKomprehensif')\" />Cari...</a></td></tr>
					<tr><td class=inp>Dosen Penguji:</td>
						<td class=ul1><input type=text name='DosenID' value='$w[DosenID]' size=10 maxlength=50 />
						 <input type=text name='Dosen' value='$w[Dosen]' size=30 maxlength=50 onKeyUp=\"javascript:CariDosen('$_SESSION[FilterProdiID]', 'frmKomprehensif')\" />
						 <div style='text-align:right'>
						  &raquo;
						  <a href='#'
							onClick=\"javascript:CariDosen('$_SESSION[FilterProdiID]', 'frmKomprehensif')\" />Cari...</a></td>
						 </tr>";
  
  
  CheckFormScript("TahunID,NamaMhsw");
  echo <<<SCR
  <table class=box cellspacing=1 width=100%>
  <form name='frmKomprehensif' action='../$_SESSION[mnux].edit.php' method=POST
    onSubmit='return CheckForm(this)'>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='KompreID' value='$KompreID' />
  <input type=hidden name='PilihanKompre' value='$PilihanKompre' />
  
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul>
      <input type=text name='TahunID' value='$w[TahunID]' size=5 maxlength=5 $ro />
      </td></tr>
  <tr><td class=inp>Mahasiswa:</td>
      <td class=ul>
        <input type=text name='MhswID' value='$w[MhswID]' size=10 maxlength=30 $ro />
        <input type=text name='NamaMhsw' value='$w[NamaMhsw]' size=30 maxlength=50 $ro
          onKeyUp="javascript:CariMhsw('$_SESSION[FilterProdiID]', 'frmKomprehensif')"/>
        $edtMhsw
      </td></tr>
  <tr><td class=inp>Tanggal Daftar:</td>
	  <td class=ul>$opttgldaftar</td></tr>	
  $waktukompre
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  </form>
  </table>
  
  <div class='box0' id='caridosen'></div>
  <div class='box0' id='carimhsw'></div>
  <div class='box0' id='cariruang'></div>
  
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
  function CariRuang(ProdiID, frm) {
    if (eval(frm + ".RuangID.value != ''")) {
      eval(frm + ".RuangID.focus()");
      showRuang(ProdiID, frm, eval(frm +".RuangID.value"), 'cariruang');
      toggleBox('cariruang', 1);
    }
  }
  //-->
  </script>
SCR;
}
function Simpan($md, $KompreID) {
  //Ambil parameters
  $TahunID = sqling($_REQUEST['TahunID']);
  $MhswID = sqling($_REQUEST['MhswID']);
  $PilihanKompre = $_REQUEST['PilihanKompre'];
  
  if ($md == 0) {
		$TanggalUjian = $_REQUEST['TanggalUjian_y'].'-'.$_REQUEST['TanggalUjian_m'].'-'.$_REQUEST['TanggalUjian_d'];
		$JamMulai = $_REQUEST['JamMulai_h'].':'.$_REQUEST['JamMulai_n'].':00';
		$JamSelesai = $_REQUEST['JamSelesai_h'].':'.$_REQUEST['JamSelesai_n'].':00';
		$RuangID = $_REQUEST['RuangID'];
		$DosenID = $_REQUEST['DosenID'];
	$s = "update kompre
      set TanggalUjian='$TanggalUjian', JamMulai='$JamMulai', JamSelesai='$JamSelesai', RuangID='$RuangID', DosenID='$DosenID',
		  LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where KompreID = '$KompreID' ";
    $r = _query($s);
	
	// Update juga 
	$ProdiID = GetaField('mhsw', "MhswID='$MhswID' and KodeID", KodeID, "ProdiID");
	  $s1 = "select * from komprematauji where ProdiID='$ProdiID' and KodeID='".KodeID."'";
	  $r1 = _query($s1);
	  while($w1 = _fetch_array($r1))
	  {		$ada = GetaField('kompredosen', "KompreID = '$KompreID' and KompreMataUjiID = '$w1[KompreMataUjiID]' and KodeID", KodeID, "KompreDosenID");
			if(empty($ada))
			{	$s = "insert into kompredosen 
						(KompreID, KodeID, KompreMataUjiID, DosenID, 
							Tanggal, JamMulai, JamSelesai, RuangID,
							LoginBuat,TanggalBuat) 
					  values
						('$KompreID', '".KodeID."', '$w1[KompreMataUjiID]', '$DosenID', 
						'$TanggalUjian', '$JamMulai', '$JamSelesai', '$RuangID',
						'$_SESSION[_Login]',now())";
				$q = _query($s);
			}
			else  
			{	$s = "update kompredosen set DosenID = '$DosenID' ,
				Tanggal = '$Tanggal', JamMulai = '$JamMulai', JamSelesai = '$JamSelesai', 
				RuangID = '$RuangID', LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
				where KompreID = '$KompreID' and KompreMataUjiID = '$w1[KompreMataUjiID]' and KodeID='".KodeID."'";
				$q = _query($s);	
			}
	  }
	
    TutupScript();
  }
  elseif ($md == 1) {
    // Sudah ambil matakuliah kompre
    $blm = GetaField("krs k
      left outer join mk mk on k.MKID = mk.MKID",
      "k.KodeID='".KodeID."' and k.NA = 'N' and mk.Komprehensif='Y' and k.MhswID", $MhswID, "count(k.MKID)");
   

    if ($blm == 0) {
      die(ErrorMsg("Error",
        "Mahasiswa ini belum mengambil mata kuliah Komprehensif.<br />
        Hubungi Tata Usaha Program Studi untuk informasi lebih lanjut.
        <hr size=1 color=silver />
        <input type=button name='Tutup' value='Batal' onClick='window.close()' />"));
    }
    // Apakah sudah pernah mendaftarkan Komprehensif?
    $ada = GetFields('kompre', "Final='N' and KodeID='".KodeID."' and MhswID",
      $MhswID, '*');
    if (empty($ada)) {
		$TanggalDaftar = $_REQUEST['TanggalDaftar_y'].'-'.$_REQUEST['TanggalDaftar_m'].'-'.$_REQUEST['TanggalDaftar_d'];
		
			$TanggalUjian = $_REQUEST['TanggalUjian_y'].'-'.$_REQUEST['TanggalUjian_m'].'-'.$_REQUEST['TanggalUjian_d'];
			$JamMulai = $_REQUEST['JamMulai_h'].':'.$_REQUEST['JamMulai_n'].':00';
			$JamSelesai = $_REQUEST['JamSelesai_h'].':'.$_REQUEST['JamSelesai_n'].':00';
			$RuangID = $_REQUEST['RuangID'];
			$DosenID = $_REQUEST['DosenID'];
	  
	  $s = "insert into kompre
        (TahunID, KodeID, MhswID, TanggalDaftar, TanggalUjian, JamMulai, JamSelesai, RuangID, DosenID, 
        LoginBuat, TanggalBuat)
        values
        ('$TahunID', '".KodeID."', '$MhswID', '$TanggalDaftar', '$TanggalUjian', '$JamMulai', '$JamSelesai', '$RuangID', '$DosenID',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
	  
	  $KompreID = mysql_insert_id();
	  
	  $ProdiID = GetaField('mhsw', "MhswID='$MhswID' and KodeID", KodeID, "ProdiID");
	  $s1 = "select * from komprematauji where ProdiID='$ProdiID' and KodeID='".KodeID."'";
	  $r1 = _query($s1);
	  while($w1 = _fetch_array($r1))
	  {		$ada = GetaField('kompredosen', "KompreID = '$KompreID' and KompreMataUjiID = '$w1[KompreMataUjiID]' and KodeID", KodeID, "KompreDosenID");
			if(empty($ada))
			{	$s = "insert into kompredosen 
						(KompreID, KodeID, KompreMataUjiID, DosenID, 
							Tanggal, JamMulai, JamSelesai, RuangID,
							LoginBuat,TanggalBuat) 
					  values
						('$KompreID', '".KodeID."', '$w1[KompreMataUjiID]', '$DosenID', 
						'$TanggalUjian', '$JamMulai', '$JamSelesai', '$RuangID',
						'$_SESSION[_Login]',now())";
				$q = _query($s);
			}
			else  
			{	$s = "update kompredosen set DosenID = '$DosenID' ,
				Tanggal = '$Tanggal', JamMulai = '$JamMulai', JamSelesai = '$JamSelesai', 
				RuangID = '$RuangID', LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
				where KompreID = '$KompreID' and KompreMataUjiID = '$w1[KompreMataUjiID]' and KodeID='".KodeID."'";
				$q = _query($s);	
			}
	  }
  
	  
	  
      TutupScript();
    }
    else {
      echo ErrorMsg("Error",
        "Mahasiswa ini telah mendaftarkan diri untuk komprehensif sebelumnya.<br />
        Selesaikan dahulu administrasi pendaftaran komprehensif sebelumnya.
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
