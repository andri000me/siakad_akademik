<?php

//Kostumisasi oleh Arisal Yanuarafi Mulai 7 Maret 2012

session_start();
error_reporting(E_ALL);
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Daftarkan BukuAjar");
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
    $jdl = "Edit Buku Ajar";
    $w = GetFields('dosen_bukuajar', 'BahanAjarID', $id, '*');
    $ro = "readonly=true";
    $btn = "";
  }
  elseif ($md == 1) {
    $jdl = "Tambah buku Ajar";
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
          onClick=\"javascript:Caridosen('frmBukuAjar','')\" />Cari...</a> |
        <a href='#' onClick=\"javascript:frmBukuAjar.nidn.value='';frmBukuAjar.Namadosen.value=''\">Reset</a>";

	$optTahun = '';
    $thnAwal = 2009;
    while ($thnAwal < date('Y')){
    	$thnAwal++;
        $optTahun .= "<option value='$thnAwal' ".($thnAwal==$w[tahun] ? "selected":"").">$thnAwal</option>";
    }
   TampilkanJudul($jdl); 
  echo <<<ESD
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmBukuAjar' action='?' method=POST>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td class=inp>Nama :</td>
      <td class=ul>
      <input type=text name='NIDN' value='$w[nidn]' size=10 maxlength=30 $ro />
        <input type=text name='Namadosen' value='$w[nama_lengkap]' size=30 maxlength=50  />
        $btn1  
        </td></tr>
  <tr><td class=inp>Judul:<br /></td>
      <td class=ul><textarea name='judul' cols=60 rows=2>$w[judul]</textarea></td></tr>
       </td></tr>
  <tr><td class=inp>ISBN :<br /></td>
      <td class=ul><input type=text name='isbn' value='$w[isbn]' size=45 maxlength=150 /></td></tr>
       </td></tr>
  <tr><td class=inp>Jumlah halaman :<br /></td>
      <td class=ul><input type=text name='jml_halaman' value='$w[jml_halaman]' size=45 maxlength=150 /></td></tr>
       </td></tr>
  <tr><td class=inp>Penerbit :<br /></td>
      <td class=ul><input type=text name='penerbit' value='$w[penerbit]' size=45 maxlength=150 /></td></tr>
       </td></tr>
  <tr><td class=inp>Tahun :<br /></td>
      <td class=ul><select name='tahun'>$optTahun</select></td></tr>
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
  $nidn = sqling($_REQUEST['NIDN']);
  $nama_lengkap = sqling($_REQUEST['Namadosen']);
  $judul = sqling($_REQUEST['judul']);
  $isbn = sqling($_REQUEST['isbn']);
  $jml_halaman = sqling($_REQUEST['jml_halaman']);
  $penerbit = sqling($_REQUEST['penerbit']);
  $tahun = sqling($_REQUEST['tahun']);
  if ($md == 0) {
    $s = "update dosen_bukuajar set
          nidn = '$nidn',
          nama_lengkap = '$nama_lengkap',
          judul = '$judul',
          isbn = '$isbn',
          jml_halaman = '$jml_halaman',
          penerbit = '$penerbit',
          tahun = '$tahun'
      where BahanAjarID = '$id' ";
    $r = _query($s);

    TutupScript();
  }
  elseif ($md == 1) {
      		$s = "insert into dosen_bukuajar
        (nidn, nama_lengkap, judul, isbn, jml_halaman, penerbit,  tahun)
        values
       ('$nidn', '$nama_lengkap', '$judul', '$isbn', '$jml_halaman', '$penerbit',  '$tahun')
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
    opener.location='../index.php?mnux=$_SESSION[mnux]&_tabBukuAjar=$_SESSION[_tabBukuAjar]&gos=BukuAjar';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>