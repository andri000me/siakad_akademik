<?php
// *** Parameters ***
$_srcmhswkey = GetSetVar('Keyword');
$_srcmhswval = GetSetVar('Val');
$prodi = GetSetVar('prodi');
$InstitusiID = GetSetVar('InstitusiID');
if ($_REQUEST['Val'] == 'Reset') {
  $_SESSION['Keyword'] = '';
  $_SESSION['Val'] = '';
  $_SESSION['prodi'] = '';
}


// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'DftrAnggota' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function AggEdtScript() {
  echo <<<SCR
  <script>
  function AggEdt(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].anggota.edit.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=540, height=360, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function bebasPustaka(MD, ID, BCK) {
    lnk = "$_SESSION[mnux].anggota.bebaspustaka.php?md="+MD+"&id="+ID+"&bck="+BCK;
    win2 = window.open(lnk, "", "width=540, height=360, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function importMhswtoAgt(BCK) {
    $('#import_agt').val('loading...');
     $('#import_agt').attr('disabled', 'disabled');
	$.ajax({
        	url: "pustaka/import.anggota.php",
	        type: 'GET',
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					alert('Proses import dari tabel mhsw selesai. sebanyak ' + data + ' rekord baru telah dibuat.');
					window.location = '?mnux=' + BCK;
				
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				alert('Proses import dari tabel mhsw gagal !');
        $('#import_agt').val('Update Status Anggota');
        $('#import_agt').removeAttr('disabled');
	    	} 	        
	   });
  }
  function DsableAgt(BCK) {
    $('#update_agt').val('loading...');
     $('#update_agt').attr('disabled', 'disabled');
	$.ajax({
        	url: "pustaka/disable.anggota.php",
	        type: 'GET',
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					alert('Proses telah selesai. Sebanyak ' + data + ' rekord anggota telah diupdate.');
					window.location = '?mnux=' + BCK;
				
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				alert('Proses import dari tabel mhsw gagal !');
        $('#update_agt').val('Update Status Anggota');
        $('#update_agt').removeAttr('disabled');
	    	} 	        
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
	
    $ord = "";
  }
  if (!empty($_SESSION['prodi'])) $whr[] = "p.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['InstitusiID'])) $whr[] = "p.InstitusiID='$_SESSION[InstitusiID]'";
  if (!empty($whr)) $strwhr = "where " .implode(' and ', $whr);
  if (empty($strwhr)) $strwhr = "where p.ProdiID=''";
  
  

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=8></td></tr>";
  $lst = new dwolister;
  $lst->page = $_SESSION['_anggotapage']+0;
  $lst->pages= "<a href='?mnux=$_SESSION[mnux]&gos=anggota&sub=&_anggotapage==PAGE='>=PAGE=</a>";
  
  $lst->pageactive = "=PAGE=";
  
  $lst->tables = "pustaka_anggota p left outer join prodi pr on pr.ProdiID=p.ProdiID $strwhr order by p.NA DESC,AnggotaID ";
  $lst->fields = "p.AnggotaID,p.Nama, p.Handphone, p.Email, p.Alamat, p.Kunjungan, p.NA, p.StatusMhswID";
  
  $lst->maxrow = 10;
  $lst->headerfmt = "<p><form name='frmAnggota' method='post' action=?>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='_anggotapage' value='1' />
  <table class=box cellspacing=1 align=center width=800>
  <tr>
    <td class=inp colspan=3>
      Jenis Anggota:
    </td>
    <td class=ul1 colspan=5>
          <select name='InstitusiID'>
                <option value=''></option>
                <option value='MHS' ".($_SESSION['InstitusiID']=='MHS' ? "selected":"").">Mahasiswa</option>
                <option value='DOS' ".($_SESSION['InstitusiID']=='DOS' ? "selected":"").">Dosen</option>
                <option value='KAR' ".($_SESSION['InstitusiID']=='KAR' ? "selected":"").">Karyawan</option>
                <option value='MHL' ".($_SESSION['InstitusiID']=='MHL' ? "selected":"").">Mahasiswa Luar Biasa</option>
                <option value='UMM' ".($_SESSION['InstitusiID']=='UMM' ? "selected":"").">Umum</option>
          </select>
        </td>
        </tr>
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
     <td colspan=8>
    <input type=button name='Tambah' value='Tambah Anggota'
        onClick=\"javascript:AggEdt(1, '', '$_SESSION[mnux]')\" />
      <input type=button name='Refresh' value='Refresh'
        onClick=\"window.location='?mnux=$_SESSION[mnux]'\" /> | 
    <input type=button name='Import' value='Import Anggota (Tabel Mhsw)'
        onClick=\"javascript:importMhswtoAgt('$_SESSION[mnux]')\" id='import_agt' /> |
    <input type=button name='Dsable' value='Update Status Anggota'
        onClick=\"javascript:DsableAgt('$_SESSION[mnux]')\" id='update_agt' />
    </td>
    </tr>
    
    <tr>
    <th class=ttl colspan=1>#</th>
    <th class=ttl colspan=2>ID Anggota</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Detail</th>
    <th class=ttl>Bebas<br />Pustaka</th>
    <th class=ttl>Aktif?</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=ul1 width=10>
      <a href='#' onClick=\"javascript:AggEdt(0, '=AnggotaID=', '$_SESSION[mnux]')\"><img src='img/edit.png' /></a></td>
    <td class=cna=NA= width=100><b>=AnggotaID=</b></td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA=>Handphone: =Handphone=<br />Email: =Email=<br />Alamat: =Alamat=</td>
    <td class=ul1 width=10>
      <a href='#' onClick=\"javascript:bebasPustaka(0, '=AnggotaID=', '$_SESSION[mnux]')\"><img src='img/printer2.gif' /></a></td>
    <td class=ul1 width=10 align=center><img src='img/book=NA=.gif' /></td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table></form>";
  $hal = $lst->TampilkanHalaman();
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

?>
