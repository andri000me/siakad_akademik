<?php
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2005-12-28

function DftrPT() {
  $CariPerguruanTinggi = GetSetVar('CariPerguruanTinggi', 'Nama Perguruan Tinggi');
  $arrCariPerguruanTinggi = array('Nama Perguruan Tinggi'=>'Nama', 'Kode Perguruan Tinggi'=>'PerguruanTinggiID');
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='index.php' method=GET>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value=''>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='SRPT' value='$_REQUEST[SRPT]'>
  <tr><td class=inp>Cari Perguruan Tinggi:</td><td class=ul colspan=2><input type=text name='NamaPerguruanTinggi' value='$_SESSION[NamaPerguruanTinggi]' size=20 maxlength=20>
    <input type=submit name='CariPerguruanTinggi' value='Nama Perguruan Tinggi'> <input type=submit name='CariPerguruanTinggi' value='Kode Perguruan Tinggi'></td></tr>
  <tr><td class=inp>Filter Kota:</td><td class=ul><input type=text name='KotaPerguruanTinggi' value='$_SESSION[KotaPerguruanTinggi]' size=20 maxlength=20></td><td class=ul><input type=submit name='KotaPerguruanTinggiS' value='Filter'> *) kosongkan jika tidak ingin difilter</td>
  </form></table>";
  $whr = array();
  if (!empty($_SESSION['NamaPerguruanTinggi'])) { 
		if ($arrCariPerguruanTinggi[$CariPerguruanTinggi] == 'PerguruanTinggiID') $whr[] = $arrCariPerguruanTinggi[$CariPerguruanTinggi] . " like '$_SESSION[NamaPerguruanTinggi]%' ";
		else $whr[] = $arrCariPerguruanTinggi[$CariPerguruanTinggi] . " like '%$_SESSION[NamaPerguruanTinggi]%' ";
	}
  if (!empty($_SESSION['KotaPerguruanTinggi'])) $whr[] = "Kota like '%$_SESSION[KotaPerguruanTinggi]%' ";
  $_whr = implode(" and ", $whr);
  $_whr = (empty($_whr))? '' : "where $_whr";
  
  include_once "class/dwolister.class.php";

  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_asalPTPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";
  
  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  
  $lister = new dwolister;
  $lister->tables = "perguruantinggi $_whr
    order by " . $arrCariPerguruanTinggi[$CariPerguruanTinggi];
	//echo $lister->tables;
    $lister->fields = "*";
    $lister->startrow = $_SESSION['_asalPTPage']+0;
	  $lister->maxrow = $_maxbaris;
	  $lister->pages = $pagefmt;
	  $lister->pageactive = $pageoff;
	  $lister->page = $_SESSION['_asalPTPage']+0;
	  
    $lister->headerfmt = "<table class=box cellspacing=1 cellpadding=4>
      <tr><td class=ul colspan=8><a href='#' onClick=\"EdtPerguruanTinggi(1, '')\">Tambah Perguruan Tinggi</a></td></tr>
	  
      <tr>
	  <th class=ttl>No.</th>
      <th class=ttl>Kode</th>
	  <th class=ttl>Nama</th>
      <th class=ttl>Jenis</th>
	  <th class=ttl>Kota</th>
	  <th class=ttl>Website</th>
	  <th class=ttl>Telephone</th>
	  <th class=ttl>NA</th>
      </tr>";
    $lister->detailfmt = "<tr>
	  <td class=inp width=18 align=right>=NOMER=</td>
      <td class=cna=NA= nowrap><a href='#self' onClick=\"EdtPerguruanTinggi(0, '=PerguruanTinggiID=')\"><img src='img/edit.png' border=0>
      =PerguruanTinggiID=</a></td>
	  <td class=cna=NA=>=Nama=</a></td>
	  <td class=cna=NA=>=JenisPerguruanTinggiID=&nbsp;</td>
	  <td class=cna=NA=>=Kota=&nbsp;</td>
	  <td class=cna=NA=>=Website=&nbsp;</td>
	  <td class=cna=NA=>=Telephone=&nbsp;</td>
	  <td class=cna=NA=><center><img src='img/book=NA=.gif' border=0></td></tr>";
    $lister->footerfmt = "</table>";
	$hal = $lister->TampilkanHalaman($pagefmt, $pageoff);
    $ttl = $lister->MaxRowCount;
    echo $lister->TampilkanData();
    echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>
		<script>
			function EdtPerguruanTinggi(md, PTid)
			{	lnk = '$_SESSION[mnux].edit.php?md='+md+'&PTid='+PTid;
				win2 = window.open(lnk, '', 'width=620, height=700, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
			}
		</script>";
}

// *** Parameters ***
$_asalPTPage = GetSetVar('_asalPTPage');
$asalPT = GetSetVar("NamaPerguruanTinggi");
$kotaPT = GetSetVar("KotaPerguruanTinggi");
$gos = (empty($_REQUEST['gos']))? "DftrPT" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Asal Perguruan Tinggi");
$gos();
?>
