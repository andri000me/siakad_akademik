<?php
// *** Parameters ***
$_srcmhswkey = GetSetVar('bKeyword');
$_srcmhswval = GetSetVar('bVal');
$Penerbit = GetSetVar('Penerbit');
$Tahun = GetSetVar('Tahun');
if ($_REQUEST['bVal'] == 'Reset') {
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
  function Edt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].bibliografi.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=940, height=560, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function Hps(MD, ID, BCK) {
    if(confirm('Yakin ingin menghapus?')){
    lnk = "$_SESSION[mnux].bibliografi.hapus.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=1, height=1, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
    }
  }
  function pengarang(MD, BCK) {
    ID = Number($("#Pengarang").val()) + 0;
    lnk = "$_SESSION[mnux].pengarang.edt.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=440, height=160, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function penerbit(MD, BCK) {
    ID = Number($("#Penerbit").val()) + 0;
    lnk = "$_SESSION[mnux].penerbit.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=440, height=160, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function kotaterbit(MD, BCK) {
    ID = Number($("#KotaTerbit").val()) + 0;
    lnk = "$_SESSION[mnux].kotaterbit.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=440, height=160, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
function Dftar() {
  global $_maxbaris;
  /*$optpengarang = GetOption3('app_pustaka1.mst_author', "author_name", 'author_id', $_SESSION['Pengarang'],
  				"", 'author_id');
  $optpenerbit = GetOption3('app_pustaka1.mst_publisher', "publisher_name", 'publisher_id', $_SESSION['Penerbit'],
    				"", 'publisher_id');
  $optkota = GetOption3('app_pustaka1.mst_place', "place_name", 'place_id', $_SESSION['KotaTerbit'],
            "", 'place_id'); */
  $opttahun = GetOption3('app_pustaka1.biblio', "publish_year", 'publish_year', $_SESSION['Tahun'],
    				"", 'publish_year','','',"publish_year"); 
  include_once "class/dwolister.class.php";
  EdtScript();
  $_page = GetSetVar('_page');
  
  $whr = array();
  if (($_SESSION['bVal'] != 'Reset') &&
  !empty($_SESSION['bVal']) && !empty($_SESSION['bKeyword'])) {
  $_SESSION[bVal] = ($_SESSION[bVal]=='Judul')? "title":$_SESSION[bVal];
  $_SESSION[bVal] = ($_SESSION[bVal]=='Nomor Panggil')? "call_number":$_SESSION[bVal];
  $_SESSION[bVal] = ($_SESSION[bVal]=='ISBN')? "isbn_issn":$_SESSION[bVal];

	$whr[] = "b.$_SESSION[bVal] like '%$_SESSION[bKeyword]%' ";
	
    $ord = "order by b.$_SESSION[bVal]";
  }
  if (!empty($_SESSION['Pengarang'])) $whr[] = "b.author_id='$_SESSION[Pengarang]'";
  if (!empty($_SESSION['KotaTerbit'])) $whr[] = "b.publish_place_id='$_SESSION[KotaTerbit]'";
  if (!empty($_SESSION['Penerbit'])) $whr[] = "b.publisher_id='$_SESSION[Penerbit]'";
  if (!empty($_SESSION['Tahun'])) $whr[] = "b.publish_year='$_SESSION[Tahun]'";
  if (!empty($whr)) $strwhr = "where " .implode(' and ', $whr);
  

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=8></td></tr>";
  $lst = new dwolister;
  $lst->page = $_SESSION['_page']+0;
  $lst->pages= "<a href='?mnux=$_SESSION[mnux]&gos=bibliografi&sub=&_page==PAGE='>=PAGE=</a>";
  
  $lst->pageactive = "=PAGE=";
  
  $lst->tables = "app_pustaka1.biblio b 
                    left outer join app_pustaka1.mst_publisher p on p.publisher_id=b.publisher_id
                    left outer join app_pustaka1.mst_place pl on pl.place_id=b.publish_place_id
                    $strwhr order by b.biblio_id DESC";
  $lst->fields = "b.*, 
  (select concat('(',ma.author_name,')') from app_pustaka1.biblio_author ba left outer join app_pustaka1.mst_author ma on ma.author_id=ba.author_id where ba.biblio_id=b.biblio_id limit 1) as _author,
   p.publisher_name as _Penerbit, pl.place_name as _Tempat, (SELECT count(item_id) from app_pustaka1.item where biblio_id=b.biblio_id) as JmlEks";
  
  $lst->maxrow = 10;
  $lst->headerfmt = "<p><form name='frm' method='post' action=?>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='_page' value='1' />
  <table class=box cellspacing=1 align=center width=100%>
  <!--<tr>
    <td class=inp colspan=3>
      Pengarang:
    </td>
    <td class=ul1 colspan=5>
    <select name='Pengarang' id='Pengarang'>$optpengarang</select>
    <input type='button' name='edtPengarang' value='Edit Pengarang' onClick=\"javascript:pengarang('0', '$_SESSION[mnux]')\" />
    <input type='button' name='delPengarang' value='Hapus Pengarang' onClick=\"javascript:pengarang('3', '$_SESSION[mnux]')\" />
      </td>
        </tr>
    <tr>
    <td class=inp colspan=3>
    	Penerbit:
    </td>
    <td class=ul1 colspan=5>
    <select name='Penerbit' id='Penerbit'>$optpenerbit</select>
    <input type='button' name='edtPenerbit' value='Edit Penerbit' onClick=\"javascript:penerbit('0', '$_SESSION[mnux]')\" />
    <input type='button' name='delPenerbit' value='Hapus Penerbit' onClick=\"javascript:penerbit('3', '$_SESSION[mnux]')\" />
      </td>
        </tr>
    <tr>
    <td class=inp colspan=3>
      Kota Terbit:
    </td>
    <td class=ul1 colspan=5>
    <select name='KotaTerbit' id='KotaTerbit'>$optkota</select>
    <input type='button' name='edtKotaTerbit' value='Edit Kota' onClick=\"javascript:kotaterbit('0', '$_SESSION[mnux]')\" />
    <input type='button' name='delKotaTerbit' value='Hapus Kota' onClick=\"javascript:kotaterbit('3', '$_SESSION[mnux]')\" />
      </td>
        </tr>
     <tr>-->
    <td class=inp colspan=3>
    	Tahun Terbit:
    </td>
    <td class=ul1 colspan=5>
    <select name='Tahun' id='Tahun'>$opttahun</select>
       </td>
        </tr>
    <tr>
    <tr>
    <td class=inp colspan=3>
    	Cari :
    </td>
    <td class=ul1 colspan=5>
    <input type='text' name='bKeyword' size=30 style='margin-bottom:4px' value='$_SESSION[bKeyword]' />
        <input type=submit name='bVal' value='Judul'  />
        <input type=submit name='bVal' value='Nomor Panggil'  />
        <input type=submit name='bVal' value='ISBN'  />
        <input type=submit name='bVal' value='Reset'  />
        </td>
        </tr>
     <tr>
     <td colspan=8>
    <input type=button name='Tambah' value='Tambah Bibliografi'
        onClick=\"javascript:Edt(1, '', '$_SESSION[mnux]')\" />
      <input type=button name='Refresh' value='Refresh'
        onClick=\"window.location='?mnux=$_SESSION[mnux]'\" /> | 
        <input type=button name='Pengarang' value='Tambah Pengarang'
        onClick=\"javascript:pengarang(1, '$_SESSION[mnux]')\" />
        <input type=button name='Penerbit' value='Tambah Penerbit'
        onClick=\"javascript:penerbit(1, '$_SESSION[mnux]')\" />
        <input type=button name='KotaTerbit' value='Tambah Kota Terbit'
        onClick=\"javascript:kotaterbit(1, '$_SESSION[mnux]')\" />
  </td>
    </tr>
    </table>
    <table class=box cellspacing=1 align=center width=100%>
    <tr>
    <th class=ttl colspan=1>#</th>
    <th class=ttl colspan=4 width='400'>Judul</th>
    <th class=ttl width='300' colspan=2>Detail</th>
    <th class=ttl width='50' colspan=2>Hapus</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=ul1 colspan=4>
      <a href='#' onClick=\"javascript:Edt(0, '=biblio_id=', '$_SESSION[mnux]')\"><img src='img/edit.png' /></a> <b>=title=</b> <i>=_author=</i></td>
    <td class=cna=NA= colspan=2>Penerbit: =_Penerbit=, =_Tempat=<br />ISBN/ISSN: =isbn_issn=<br />Nomor Panggil: =call_number=<br />Tahun: =publish_year=<br />Jumlah Eksemplar: =JmlEks=</td>
    <td align=center><a href='#' onClick=\"javascript:Hps(0, '=biblio_id=', '$_SESSION[mnux]')\"><img src='img/del.gif' /></a></td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table></form>";
  $hal = $lst->TampilkanHalaman();
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

?>
