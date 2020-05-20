<?php
// Author: Emanuel Setio Dewo
// 20 July 2006
// www.sisfokampus.net

$jbtpage = GetSetVar('jbtpage', 0)+0;
$golpage = GetSetVar('golpage', 0)+0;
$iktpage = GetSetVar('iktpage', 0)+0;

// *** functions ***
function TampilkanFilterDosen($mnux='dosen', $add=1) {
  global $arrID;
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  $ck_nama  = ($_SESSION['dsnurt'] == 'Nama') ? 'checked' : '';
  $ck_login = ($_SESSION['dsnurt'] == 'Login') ? 'checked' : '';
  $ck_nidn  = ($_SESSION['dsnurt'] == 'NIDN')  ? 'checked' : ''; 
  //$stradd = ($add == 0)? '' : "<a href='?mnux=dosen&gos=DsnAdd&md=1'>Tambah Dosen</a>";
  $stradd = ($add == 0)? '' : "&raquo; <input type=button name='TambahDosen' value='Tambah Dosen' onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnAdd&md=1'\" />";

  echo "<p><table class=box cellspacing=1 cellpadding=4 width=800>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='dsnpage' value='1'>
  <tr><td class=inp>Cari Dosen:</td>
      <td class=ul1 colspan=3>
      <input type=text name='dsncr' value='$_SESSION[dsncr]' size=10 maxlengh=10>
      <input type=submit name='dsnkeycr' value='Login'>   
      <input type=submit name='dsnkeycr' value='Nama'>
      <input type=submit name='dsnkeycr' value='NIDN' />
      <input type=submit name='dsnkeycr' value='Reset'>
      </td></tr>
  <tr><td class=inp></td>
	  <td class=ul1 colspan=3>
	  $stradd
      <input type=button name='btnExportXL' value='Export Daftar Dosen'
        onClick=\"location='$_SESSION[mnux].xl.php'\" />
	  <input type=button name='btnRekeningDosen' value='Cetak Rek. Dosen'
	    onClick=\"CetakRekeningDosen()\" />
	  <input type=button name='btnRiwayatDosen' value='Cetak Profil Dosen'
	    onClick=\"CetakProfilDosen()\" />	
      </td></tr>
  <tr><td class=inp>Urut berdasarkan:</td>
      <td class=ul1>
      <input type=radio name='dsnurt' value='Nama' onClick='this.form.submit()' $ck_nama> Nama,
      <input type=radio name='dsnurt' value='Login' onClick='this.form.submit()' $ck_login> Login/NIP,
      <input type=radio name='dsnurt' value='NIDN' onClick='this.form.submit()' $ck_nidn> NIDN
      </td>
      <td class=inp>Filter Homebase :</td>
      <td class=ul1><select name='prodi' onChange='this.form.submit()'>$optprd</select></td>
      </tr>
  </form></table></p>
  <script>
	function CetakRekeningDosen()
	{	lnk = \"$_SESSION[mnux].rekdosen.php?\";
      win2 = window.open(lnk, \"\", \"width=600, height=400, scrollbars, status, resizable\");
      if (win2.opener == null) childWindow.opener = self;
	}
	function CetakProfilDosen()
	{	lnk = \"$_SESSION[mnux].riwayathidup.cetak.php?\";
      win2 = window.open(lnk, \"\", \"width=600, height=400, scrollbars, status, resizable\");
      if (win2.opener == null) childWindow.opener = self;
	}
  </script>
  ";
}
function DaftarDosen($mnux='', $lnk='', $fields='') {
  global $_defmaxrow, $_FKartuUSM;
  include_once "class/dwolister.class.php";
  
  if (empty($mnux)) $mnux = $_SESSION['mnux'];
  //$lnk = "gos=DsnEdt&md=0&dsnid==Login="; 
  // Buat Header:
  $_f = explode(',', $fields);
  $hdr = ''; $brs = '';
  for ($i = 0; $i < sizeof($_f); $i++) {
    $hdr .= "<th class=ttl>". $_f[$i] . "</th>";
    $brs .= "<td class=cna=NA=>=".$_f[$i]."=&nbsp;</td>";
  }
  $whr = array();
  if (!empty($_SESSION['dsnkeycr']) && !empty($_SESSION['dsncr'])) {
    if ($_SESSION['dsnkeycr'] == 'Login') {
      $whr[] = "$_SESSION[dsnkeycr] like '$_SESSION[dsncr]%'";
    } 
    else $whr[] = "$_SESSION[dsnkeycr] like '%$_SESSION[dsncr]%'";
  }
  $where = implode(' and ', $whr);
  $where = (empty($where))? '' : "and $where";
  $hom = (empty($_SESSION['prodi'])) ? '' : "and Homebase = '$_SESSION[prodi]'";
  
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$mnux&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosen
    where KodeID='$_SESSION[KodeID]' $where $hom
    order by $_SESSION[dsnurt]";
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4 width=800>
    <tr>
	  <th class=ttl colspan=2>#</th>
	  <th class=ttl>Login/NIP</th>
    $hdr
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
      <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=ul1 width=10><a href=\"?mnux=$mnux&$lnk\"><img src='img/edit.png' border=0></a></td>
      <td class=cna=NA=>=Login=</td>
      $brs
      <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
      </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

function DaftarJabatan($mnux='', $lnk='', $fields='') {
  global $_defmaxrow;//, $_FKartuUSM;
  include_once "class/dwolister.class.php";
  
  if (empty($mnux)) $mnux = $_SESSION['mnux'];
  //$lnk = "gos=DsnEdt&md=0&dsnid==Login="; 
  // Buat Header:
  $_f = explode(',', $fields);
  $hdr = ''; $brs = '';
  for ($i = 0; $i < sizeof($_f); $i++) {
    $hdr .= "<th class=ttl>". $_f[$i] . "</th>";
    $brs .= "<td class=cna=NA=>=".$_f[$i]."=&nbsp;</td>";
  }
 
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['jbtpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$mnux&gos=DsnEdt&md=1&jbtpage==PAGE='>=PAGE=</a>";
  $lst->tables = "jabatan
    order by JabatanID";
  $lst->fields = "*";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4 width=800>
    <tr>
	  <th class=ttl colspan=2>#</th>
	  <th class=ttl>Kode Jabatan</th>
    $hdr
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
      <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=ul1 width=10><a href=\"?mnux=$mnux&$lnk\"><img src='img/edit.png' border=0></a></td>
      <td class=cna=NA=>=JabatanID=</td>
      $brs
      <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
      </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

function DaftarGolongan($mnux='', $lnk='', $fields='') {
  global $_defmaxrow;//, $_FKartuUSM;
  include_once "class/dwolister.class.php";
  
  if (empty($mnux)) $mnux = $_SESSION['mnux'];
  //$lnk = "gos=DsnEdt&md=0&dsnid==Login="; 
  // Buat Header:
  $_f = explode(',', $fields);
  $hdr = ''; $brs = '';
  for ($i = 0; $i < sizeof($_f); $i++) {
    $hdr .= "<th class=ttl>". $_f[$i] . "</th>";
    $brs .= "<td class=cna=NA=>=".$_f[$i]."=&nbsp;</td>";
  }
 
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['golpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$mnux&gos=DsnEdt&md=1&golpage==PAGE='>=PAGE=</a>";
  $lst->tables = "golongan
    order by GolonganID";
  $lst->fields = "*";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4 width=800>
    <tr>
	  <th class=ttl colspan=2>#</th>
	  <th class=ttl>Kode Golongan</th>
    $hdr
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
      <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=ul1 width=10><a href=\"?mnux=$mnux&$lnk\"><img src='img/edit.png' border=0></a></td>
      <td class=cna=NA=>=GolonganID=</td>
      $brs
      <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
      </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

function DaftarIkatan($mnux='', $lnk='', $fields='') {
  global $_defmaxrow, $_FKartuUSM;
  include_once "class/dwolister.class.php";
  
  if (empty($mnux)) $mnux = $_SESSION['mnux'];
  //$lnk = "gos=DsnEdt&md=0&dsnid==Login="; 
  // Buat Header:
  $_f = explode(',', $fields);
  $hdr = ''; $brs = '';
  for ($i = 0; $i < sizeof($_f); $i++) {
    $hdr .= "<th class=ttl>". $_f[$i] . "</th>";
    $brs .= "<td class=cna=NA=>=".$_f[$i]."=&nbsp;</td>";
  }
 
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['iktpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$mnux&gos=DsnEdt&md=1&iktpage==PAGE='>=PAGE=</a>";
  $lst->tables = "ikatan
    order by IkatanID";
  $lst->fields = "*";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4 width=800>
    <tr>
	  <th class=ttl colspan=2>#</th>
	  <th class=ttl>Kode Ikatan</th>
    $hdr
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
      <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=ul1 width=10><a href=\"?mnux=$mnux&$lnk\"><img src='img/edit.png' border=0></a></td>
      <td class=cna=NA=>=IkatanID=</td>
      $brs
      <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
      </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}


?>
