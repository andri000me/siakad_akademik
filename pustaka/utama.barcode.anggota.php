<?php
// *** Parameters ***
$_srcmhswkey = GetSetVar('Keyword');
$_srcmhswval = GetSetVar('Val');
$prodi = GetSetVar('prodi');
if ($_REQUEST['Val'] == 'Reset') {
  $_SESSION['Keyword'] = '';
  $_SESSION['Val'] = '';
  $_SESSION['prodi'] = '';
}
if ($_REQUEST['bVal'] == 'Batalkan Antrian Pencetakan') {
	$_SESSION['_antrianAnggotaCetakID']='';
	 $_SESSION['bKeyword'] = '';
  $_SESSION['bVal'] = '';
  $_SESSION['prodi'] = '';
}


// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrAnggota' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function AggEdtScript() {
  echo <<<SCR
  <script>
  function cetakAntrian() {
    lnk = "$_SESSION[mnux].barcode.cetak.php?CetakID=anggota";
    win2 = window.open(lnk, "", "width=540, height=360, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function antriCetak(ID) {
	$.ajax({
        	url: "pustaka/ajx.antricetak.php?AnggotaID="+ID,
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
function DftrAnggota() {
  global $_maxbaris;
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['prodi']);
  include_once "class/dwolister.class.php";
  AggEdtScript();
  $_anggotapage = GetSetVar('_anggotapage');
  
  $whr = array();
  if (($_SESSION['Val'] != 'Reset') &&
  !empty($_SESSION['Val']) && !empty($_SESSION['Keyword'])) {
	
	$whr[] = "p.$_SESSION[Val] like '%$_SESSION[Keyword]%' ";
	
    $ord = "order by p.StatusMhswID,p.$_SESSION[Val]";
  }
  if (!empty($_SESSION['prodi'])) $whr[] = "p.ProdiID='$_SESSION[prodi]'";
  if (!empty($whr)) $strwhr = "where " .implode(' and ', $whr);
  if (empty($strwhr)) $strwhr = "where p.ProdiID=''";
  
  

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=8></td></tr>";
  $lst = new dwolister;
  $lst->page = $_SESSION['_anggotapage']+0;
  $lst->pages= "<a href='?mnux=$_SESSION[mnux]&gos=barcode.anggota&sub=&_anggotapage==PAGE='>=PAGE=</a>";
  
  $lst->pageactive = "=PAGE=";
  
  $lst->tables = "pustaka_anggota p left outer join prodi pr on pr.ProdiID=p.ProdiID $strwhr $ord";
  $lst->fields = "p.AnggotaID,p.Nama, p.Handphone, p.Email, p.Alamat, p.Kunjungan, p.NA, p.StatusMhswID,
                  if (p.NA='Y','Tidak Aktif','Aktif') as Keanggotaan";
  
  $lst->maxrow = 10;
  $lst->headerfmt = "<p>
  <style>.cnaY{background:#ccc;}</style>
  <form name='frm' method='post' action=?>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='_anggotapage' value='1' />
  <table class=box cellspacing=1 align=center width=800>
  <tr>
    <td class=inp colspan=3>
    	Program Studi:
    </td>
    <td class=ul1 colspan=5>
    <select name='prodi'>$optprodi</select>
        </td>
        </tr>
    <tr>
    <td class=inp colspan=3>
    	Cari Anggota:
    </td>
    <td class=ul1 colspan=5>
    <input type='text' name='Keyword' size=30 style='margin-bottom:4px' value='$_SESSION[Keyword]' />
        <input type=submit name='Val' value='AnggotaID'  />
        <input type=submit name='Val' value='Nama'  />
        <input type=submit name='Val' value='Reset'  />
        </td>
        </tr>
     <tr>
     <td colspan=8 align=center>
    	<input type=submit name='bVal' value='Batalkan Antrian Pencetakan'  /> 
            <input type='button' value='Cetak Antrian' onclick=\"javascript:cetakAntrian()\" />
    </td>
    </tr>
    
    <tr>
    <th class=ttl colspan=1>#</th>
    <th class=ttl colspan=1>ID Anggota</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Detail</th>
    <th class=ttl>Keanggotaan</th>
    <th class=ttl>Antri</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=cna=NA= width=100><b>=AnggotaID=</b></td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA=>Handphone: =Handphone=<br />Email: =Email=<br />Alamat: =Alamat=</td>
    <td class=cna=NA= align=center>=Keanggotaan=</td>
    <td class=ul1 width=10 align=center><input type='radio' name='=AnggotaID=' onclick=\"javascript:antriCetak('=AnggotaID=')\"  /></td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table></form>";
  $hal = $lst->TampilkanHalaman();
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

?>
