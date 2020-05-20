<?php
// *** Parameters ***
$_srcmhswkey = GetSetVar('bKeyword');
$_srcmhswval = GetSetVar('bVal');
$maxrow 	 = GetSetVar('_barcodeMaxrow');
if ($_REQUEST['bVal'] == 'Reset') {
  $_SESSION['bKeyword'] = '';
  $_SESSION['bVal'] = '';
  $_SESSION['prodi'] = '';
}
if ($_REQUEST['bVal'] == 'Batalkan Antrian Pencetakan') {
	$_SESSION['_antrianKatalogID']='';
	 $_SESSION['bKeyword'] = '';
  $_SESSION['bVal'] = '';
  $_SESSION['prodi'] = '';
}



// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'Dftar' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function EdtScript() {
  echo <<<SCR
  <script>
  function cetakAntrian() {
    lnk = "$_SESSION[mnux].katalog.cetak.php?CetakID=katalog";
    win2 = window.open(lnk, "", "width=740, height=660, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function antriCetak(ID) {
	$.ajax({
        	url: "pustaka/ajx.antrikatalog.php?BukuID="+ID,
	        type: 'GET',
			
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
	   });
  }
  </script>
SCR;
}
function Dftar() {
  global $_maxbaris;
  include_once "class/dwolister.class.php";
  EdtScript();
  $_page = GetSetVar('_page');
  
  $whr = array();
  if (($_SESSION['bVal'] != 'Reset') &&
  !empty($_SESSION['bVal']) && !empty($_SESSION['bKeyword'])) {
	
	$whr[] = "b.$_SESSION[bVal] like '%$_SESSION[bKeyword]%' ";
	
    $ord = "order by b.$_SESSION[bVal]";
  }
  if (!empty($whr)) $strwhr = "where " .implode(' and ', $whr);
  

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=8></td></tr>";
  $lst = new dwolister;
  $lst->page = $_SESSION['_page']+0;
  $lst->pages= "<a href='?mnux=$_SESSION[mnux]&gos=barcode&sub=&_page==PAGE='>=PAGE=</a>";
  
  $lst->pageactive = "=PAGE=";
  
  $lst->tables = "app_pustaka1.biblio b
                    $strwhr order by b.biblio_id DESC";
  $lst->fields = "b.*";
  
  $lst->maxrow = $_SESSION['_barcodeMaxrow'];
  $lst->headerfmt = "<p><form name='frm' method='post' action=?>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='_page' value='1' />
  <table class=box cellspacing=1 align=center width=800>
    <tr>
    <td class=inp>
    	Cari :
    </td>
    <td class=ul1>
    <input type='text' name='bKeyword' size=30 style='margin-bottom:4px' value='$_SESSION[bKeyword]' />
        <input type=submit name='bVal' value='title'  />
        <input type=submit name='bVal' value='isbn_issn'  />
        <input type=submit name='bVal' value='Reset'  />
        </td>
      <td class=inp>Maks. Baris: </td>
      <td class=ul1><input type='text' name='_barcodeMaxrow' value='$_SESSION[_barcodeMaxrow]' /></td>
        </tr>
      <tr>
      	<td colspan=4 align=right>
            <input type=submit name='bVal' value='Batalkan Antrian Pencetakan'  /> 
            <input type='button' value='Cetak Antrian' onclick=\"javascript:cetakAntrian()\" />
        </td>
      </tr>
    </table>
    <table class=box cellspacing=1 align=center width=800>
    <tr>
    <th class=ttl colspan=1>#</th>
    <th class=ttl colspan=3 width='400'>Judul</th>
    <th class=ttl>Antri</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td colspan=3>=title=</td>
    <td class=ul1 width=10 align=center><input type='radio' name='=biblio_id=' onclick=\"javascript:antriCetak('=biblio_id=')\"  /></td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table></form>";
  $hal = $lst->TampilkanHalaman();
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

?>
