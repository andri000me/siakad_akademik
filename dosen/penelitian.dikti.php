<?php

//Kostumisasi oleh Arisal Yanuarafi Mulai 7 Maret 2012

session_start();
error_reporting(E_ALL);
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Daftarkan penelitian Dikti");
echo <<<SCR
  <script src="../$_SESSION[mnux].dikti.js"></script>
SCR;

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$DosenID = GetSetVar('DosenID');
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id);

// *** Functions ***
function Edit($md, $id) {
  if ($md == 0) {
    $jdl = "Edit Penelitian";
    $w = GetFields('dosen_penelitian_dikti', 'PenelitianID', $id, '*');
    $ro = "readonly=true";
    $btn = "";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Penelitian";
    $w = array();
    $ro = '';
   
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  // tampilkan
   $btn1 = "&raquo;
        <a href='#'
          onClick=\"javascript:Caridosen('frmPenelitian','')\" />Cari...</a> |
        <a href='#' onClick=\"javascript:frmPenelitian.NIDN.value='';frmPenelitian.Namadosen.value=''\">Reset</a>";
    $btn2 = "&raquo;
        <a href='#'
          onClick=\"javascript:Caridosen('frmPenelitian','_Anggota')\" />Cari...</a> |
        <a href='#' onClick=\"javascript:frmPenelitian.NIDN_Anggota.value='';frmPenelitian.Namadosen_Anggota.value=''\">Reset</a>";
        
        $optTahun = '';
    $thnAwal = 2009;
    while ($thnAwal < date('Y')){
    	$thnAwal++;
        $optTahun .= "<option value='$thnAwal' ".($thnAwal==$w[tahun] ? "selected":"").">$thnAwal</option>";
    }
  TampilkanJudul($jdl);
	$rdDN = ($w['sumber_dana']=='Dalam Negeri') ? " checked ": "";
	$rdLN = ($w['sumber_dana']=='Luar Negeri') ? " checked ": "";
  echo <<<ESD
  
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmPenelitian' action='?' method=POST>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td class=inp>Ketua :</td>
      <td class=ul>
      <input type=text name='NIDN' value='$w[nidn_ketua]' size=10 maxlength=30 $ro />
        <input type=text name='Namadosen' value='$w[nama_ketua]' size=30 maxlength=50  />
          
        $btn1
      </td></tr>
  <tr><td class=inp>Anggota :</td>
      <td class=ul>
      <input type=text name='NIDN_Anggota' value='$w[NIDN_Anggota]' size=10 maxlength=30 $ro />
        <input type=text name='Namadosen_Anggota' value='$w[nama_anggota]' size=30 maxlength=50  />
          
        $btn2
      </td></tr>
  <tr><td class=inp>Judul Penelitian :<br /></td>
      <td class=ul><textarea name='judul' cols=60 rows=2>$w[judul]</textarea></td></tr>
       </td></tr>
  <tr><td class=inp>Nama Skim :<br /></td>
      <td class=ul><input type=text name='nama_skim' value='$w[nama_skim]' size=45 maxlength=150 /></td></tr>
       </td></tr>
       <tr><td class=inp>Tahun :<br /></td>
      <td class=ul><select name='tahun'>$optTahun</select></td></tr>
       </td></tr>
  <tr><td class=inp>Bidang Penelitian :<br /></td>
      <td class=ul><input type=text name='bidang_penelitian' value='$w[bidang_penelitian]' size=45 maxlength=150 /></td></tr>
       </td></tr>
  <tr><td class=inp>Bidang Penelitian Lain :<br /></td>
      <td class=ul><input type=text name='bidang_penelitian_lain' value='$w[bidang_penelitian_lain]' size=45 maxlength=150 /></td></tr>
       </td></tr>
  <tr><td class=inp>Tujuan Sosial Ekonomi :<br /></td>
      <td class=ul><input type=text name='tujuan_sosial_ekonomi' value='$w[tujuan_sosial_ekonomi]' size=45 maxlength=150 /></td></tr>
        </td></tr>
  <tr><td class=inp>Tujuan Sosial Ekonomi Lain :<br /></td>
      <td class=ul><input type=text name='tujuan_sosial_ekonomi_lain' value='$w[tujuan_sosial_ekonomi_lain]' size=45 maxlength=150 /></td></tr>
      </td></tr>
      <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  </form>
  </table>
  
  <div class='box0' id='caridosen'></div>
  
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

  function Caridosen(frm,dicari) {
    if (eval(frm + ".Namadosen" + dicari + ".value != ''")) {
      eval(frm + ".Namadosen" + dicari + ".focus()");
      showDosen(frm, eval(frm +".Namadosen" + dicari + ".value"), 'caridosen', dicari);
      toggleBox('caridosen', 1);
    }
  }
  //-->
  </script>
ESD;
}

function Simpan($md, $id) {
  $judul = sqling($_REQUEST['judul']);
  $nama_ketua = sqling($_REQUEST['Namadosen']);
  $nama_anggota = sqling($_REQUEST['Namadosen_Anggota']);
  $nidn_ketua = sqling($_REQUEST['NIDN']);
  $nama_skim = sqling($_REQUEST['nama_skim']);
  $tahun = sqling($_REQUEST['tahun']);
  $bidang_penelitian = sqling($_REQUEST['bidang_penelitian']);
  $bidang_penelitian_lain = sqling($_REQUEST['bidang_penelitian_lain']);
  $tujuan_sosial_ekonomi = sqling($_REQUEST['tujuan_sosial_ekonomi']);
  $tujuan_sosial_ekonomi_lain = sqling($_REQUEST['tujuan_sosial_ekonomi_lain']);
  if ($md == 0) {
    $s = "update dosen_penelitian_dikti set
          nama_ketua = '$nama_ketua',
          nama_anggota = '$nama_anggota',
          nidn_ketua = '$nidn_ketua',
          judul = '$judul',
          nama_skim = '$nama_skim',
          tahun = '$tahun',
          bidang_penelitian = '$bidang_penelitian',
          bidang_penelitian_lain = '$bidang_penelitian_lain',
          tujuan_sosial_ekonomi = '$tujuan_sosial_ekonomi',
          tujuan_sosial_ekonomi_lain = '$tujuan_sosial_ekonomi_lain'
      where PenelitianID = '$id' ";
    $r = _query($s);

    TutupScript();
  }
  elseif ($md == 1) {
      		$s = "insert into dosen_penelitian_dikti
        (nama_ketua, nama_anggota, nidn_ketua, judul, nama_skim, bidang_penelitian, bidang_penelitian_lain,  tujuan_sosial_ekonomi, tujuan_sosial_ekonomi_lain)
        values
       ('$nama_ketua', '$nama_anggota', '$nidn_ketua', '$judul', '$nama_skim', '$bidang_penelitian', '$bidang_penelitian_lain',  '$tujuan_sosial_ekonomi', '$tujuan_sosial_ekonomi_lain')
       ";
      $r = _query($s);
	  

      TutupScript();     
    
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&_tabPenelitian=$_SESSION[_tabPenelitian]&gos=PenelitianDikti';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>