<?php
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2005-12-28

function DftrSek() {
  $CariSekolah = GetSetVar('CariSekolah', 'Nama Sekolah');
  $arrCariSekolah = array('Nama Sekolah'=>'Nama', 'Kode Sekolah'=>'SekolahID');
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='index.php' method=GET>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value=''>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='SRSEK' value='$_REQUEST[SRSEK]'>
  <tr><td class=inp>Cari Sekolah:</td><td class=ul colspan=2><input type=text name='NamaSekolah' value='$_SESSION[NamaSekolah]' size=20 maxlength=20>
    <input type=submit name='CariSekolah' value='Nama Sekolah'> <input type=submit name='CariSekolah' value='Kode Sekolah'></td></tr>
  <tr><td class=inp>Filter Kota:</td><td class=ul><input type=text name='KotaSekolah' value='$_SESSION[KotaSekolah]' size=20 maxlength=20></td><td class=ul><input type=submit name='KotaSekolahS' value='Filter'> *) kosongkan jika tidak ingin difilter</td>
  </form></table>";
  $whr = array();
  if (!empty($_SESSION['NamaSekolah'])) { 
		if ($arrCariSekolah[$CariSekolah] == 'SekolahID') $whr[] = $arrCariSekolah[$CariSekolah] . " like '$_SESSION[NamaSekolah]%' ";
		else $whr[] = $arrCariSekolah[$CariSekolah] . " like '%$_SESSION[NamaSekolah]%' ";
	}
  if (!empty($_SESSION['KotaSekolah'])) $whr[] = "Kota like '%$_SESSION[KotaSekolah]%' ";
  $_whr = implode(" and ", $whr);
  $_whr = (empty($_whr))? '' : "where $_whr";
  
  include_once "class/dwolister.class.php";

  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_asalsekPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";
  
  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  
  $lister = new dwolister;
  $lister->tables = "asalsekolah $_whr
    order by " . $arrCariSekolah[$CariSekolah];
	//echo $lister->tables;
    $lister->fields = "*";
    $lister->startrow = $_SESSION['_asalsekPage']+0;
	  $lister->maxrow = $_maxbaris;
	  $lister->pages = $pagefmt;
	  $lister->pageactive = $pageoff;
	  $lister->page = $_SESSION['_asalsekPage']+0;
	  
    $lister->headerfmt = "<table class=box cellspacing=1 cellpadding=4>
      <tr><td class=ul colspan=8><a href='#' onClick=\"EdtSekolah(1, '')\">Tambah Sekolah</a></td></tr>
	  
      <tr>
	  <th class=ttl>No.</th>
      <th class=ttl>Kode</th>
	  <th class=ttl>Nama</th>
      <th class=ttl>Jenis</th>
	  <th class=ttl>Kota</th>
	  <th class=ttl>Propinsi</th>
	  <th class=ttl>Telephone</th>
	  <th class=ttl>NA</th>
      </tr>";
    $lister->detailfmt = "<tr>
	  <td class=inp width=18 align=right>=NOMER=</td>
      <td class=cna=NA= nowrap><a href='#self' onClick=\"EdtSekolah(0, '=SekolahID=')\"><img src='img/edit.png' border=0>
      =SekolahID=</a></td>
	  <td class=cna=NA=>=Nama=</a></td>
	  <td class=cna=NA=>=JenisSekolahID=&nbsp;</td>
	  <td class=cna=NA=>=NamaKabupaten=&nbsp;</td>
	  <td class=cna=NA=>=NamaPropinsi=&nbsp;</td>
	  <td class=cna=NA=>=Telephone=&nbsp;</td>
	  <td class=cna=NA=><center><img src='img/book=NA=.gif' border=0></td></tr>";
    $lister->footerfmt = "</table>";
	$hal = $lister->TampilkanHalaman($pagefmt, $pageoff);
    $ttl = $lister->MaxRowCount;
    echo $lister->TampilkanData();
    echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>
		<script>
			function EdtSekolah(md, sekid)
			{	lnk = '$_SESSION[mnux].edit.php?md='+md+'&sekid='+sekid;
				win2 = window.open(lnk, '', 'width=620, height=700, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
			}
		</script>";
}

// *** Parameters ***
$_asalsekPage = GetSetVar('_asalsekPage');
$asalsekolah = GetSetVar("NamaSekolah");
$kotasekolah = GetSetVar("KotaSekolah");
$gos = (empty($_REQUEST['gos']))? "DftrSek" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Asal Sekolah");
$gos();
?>
