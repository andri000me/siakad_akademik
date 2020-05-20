<?php

if ($_SESSION['_LevelID']==120){
echo "<script>window.location='?mnux=$_SESSION[mnux].edt&mhswbck=$_SESSION[mnux]&gos=MhswEdt&mhswid=$_SESSION[_Login]';</script>";
}
// *** Functions ***
function TampilkanFilterMhsw() {
  		$optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['prodi']);
  		$optprid = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], "KodeID='$_SESSION[KodeID]'", 'ProgramID');
	
  // Tampilkan formulir
  if ($_SESSION['_LevelID']==120) {
  $_SESSION[srcmhswval]='';
  $_SESSION['srcmhswkey']='Nama';
  }
  else {
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=800>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='mhswpage' value='1' />

  <tr><td class=inp>Program:</td>
      <td class=ul1><select name='prid' onChange='this.form.submit()'>$optprid</select></td>
      <td class=inp>Program Studi:</td>
      <td class=ul1><select name='prodi' onChange='this.form.submit()'>$optprodi</select></td></tr>
  <tr><td class=inp>Cari Mhsw:</td>
      <td class=ul1 colspan=3>
      <input type=text name='srcmhswval' value='$_SESSION[srcmhswval]' size=20 maxlength=20>
      <input type=submit name='srcmhswkey' value='NPM'>
      <input type=submit name='srcmhswkey' value='Nama'>
      <input type=submit name='srcmhswkey' value='Reset'>
	  <input type=button name='CetakSemua' value='Cetak' onClick=\"CetakSemuaMhsw('$_SESSION[prodi]', '$_SESSION[prid]', '$_SESSION[srcmhswkey]', '$_SESSION[srcmhswval]')\"></td>
      </tr>
  </form>
  </table></p>
  <script>
	function CetakSemuaMhsw(prodi, program, srcmhswkey, srcmhswval)
	{	lnk = '$_SESSION[mnux].cetak.php?prodi='+prodi+'&program='+program+'&srcmhswkey='+srcmhswkey+'&srcmhswval='+srcmhswval;
		  win2 = window.open(lnk, \"\", \"width=600, height=400, scrollbars, status, resizable\");
		  if (win2.opener == null) childWindow.opener = self;
	}
  </script>";
  }
}
function DaftarMhsw() {
  TampilkanFotoScript();
  // setup where-statement
  $whr = array();
  $ord = '';
  if (($_SESSION['srcmhswkey'] != 'Reset') &&
  !empty($_SESSION['srcmhswkey']) && !empty($_SESSION['srcmhswval'])) {
	if ($_SESSION['_LevelID']==120) {
    $whr[] = "m.MhswID='$_SESSION[_Login]' ";
	}
	else {
	$whr[] = "m.$_SESSION[srcmhswkey] like '%$_SESSION[srcmhswval]%' ";
	}
    $ord = "order by m.$_SESSION[srcmhswkey]";
  }
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($whr)) $strwhr = "where " .implode(' and ', $whr);
  $strwhr = str_replace('NPM', "MhswID", $strwhr);
  $ord = str_replace('NPM', "MhswID", $ord);
  // Tampilkan
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['mhswpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&mhswpage==PAGE='>=PAGE=</a>";
if ($_SESSION['_LevelID']==120) {
  $lst->tables = "mhsw m
    	left outer join prodi prd on m.ProdiID=prd.ProdiID
    	left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    	$strwhr  and MhswID='$_SESSION[_Login]'  $ord";
	}
	else {
	$lst->tables = "mhsw m
    	left outer join prodi prd on m.ProdiID=prd.ProdiID
    	left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    	$strwhr$ord";
	$queryXL = "$strwhr$ord";
	}
  $lst->fields = "m.MhswID, m.Nama, m.StatusAwalID, m.StatusMhswID,
    m.Kelamin,
    m.Telepon, m.Handphone, m.Email, 
    if (m.Foto is NULL or m.Foto = '', 'img/tux001.jpg', concat('foto/',m.Foto)) as _Foto,
    m.ProgramID, m.ProdiID, m.Alamat, m.Kota,
    prd.Nama as PRD, sm.Nama as SM, sm.Keluar";
  $lst->headerfmt = "<p><center>
  	<form method='post' action='master/mhsw.xls.php'>
	<input type='hidden' value=\"$queryXL\" name='query'>
	<input type='submit' value='Cetak XLS'>
	</form></center>
	<table class=table table-striped cellspacing=1 cellpadding=4 width=800>
    <tr><th class=ttl>No.</th>
    <th class=ttl>".NPM."</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Status</th>
    <th class=ttl>Telp/HP</th>
    <th class=ttl>Hapus</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10><a name='=MhswID='>=NOMER=</a></td>
    <td class=cna=Keluar= width=120>
      <a href='?mnux=$_SESSION[mnux].edt&mhswbck=$_SESSION[mnux]&gos=MhswEdt&mhswid==MhswID='><img src='img/edit.png'>
      =MhswID=</a>
      <img src='img/=Kelamin=.bmp' align=right />
      </td>
    <td class=cna=Keluar= nowrap>
      <b>=Nama=</b>
      <a href='#' onClick=\"javascript:TampilkanFoto('=MhswID=', '=Nama=', '=_Foto=')\" title='=_Foto='>
      <img src='=_Foto=' width=30 align=right /></a>
      </td>
    <td class=cna=Keluar=>=ProgramID=&nbsp;
      <hr size=1 color=silver />
      =PRD=&nbsp;</td>
    <td class=cna=Keluar= width=60 align=center>=SM=</td>
    <td class=cna=Keluar=>=Telepon=&nbsp;
      <hr size=1 color=silver />
      =Handphone=&nbsp;</td>
	<td align=right><a href='?mnux=$_SESSION[mnux].edt&mhswbck=$_SESSION[mnux]&gos=MhswEdt&mhswid==MhswID=&delete=1' onclick=\"return confirm('Yakin menghapus mahasiswa ini???');\">Hapus (X)</a></td>  
    </tr>
    <tr><td bgcolor=silver colspan=6 height=1></td></tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  if ($_SESSION['_LevelID']==120){
      echo "<p><table class=box cellspacing=1 cellpadding=4 width=700>
  <tr><td align=center><font color=darkred>Klik gambar <img src='img/edit.png'> untuk melihat data lebih detail</font></td></tr></table></p>";
  }
  else {
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
	}
}
function TampilkanFotoScript() {
  echo <<<SCR
  <script>
  function TampilkanFoto(MhswID, Nama, Foto) {
    jQuery.facebox("<font size=+1>"+Nama+"</font> <sup>(" + MhswID + ")</sup><hr size=1 color=silver /><img src='"+Foto+"' />");
  }
  </script>
SCR;
}


// *** Parameters ***
$mhswpage = GetSetVar('mhswpage', 1);
$_srcmhswkey = GetSetVar('srcmhswkey');
$_srcmhswval = GetSetVar('srcmhswval');
if ($_REQUEST['srcmhswkey'] == 'Reset') {
  $_SESSION['srcmhswkey'] = '';
  $_SESSION['srcmhswval'] = '';
}
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? 'DaftarMhsw' : $_REQUEST['gos'];


// *** Main ***
if ($_SESSION['_LevelID'] ==120){
TampilkanJudul("Data Mahasiswa");
}
else
{
TampilkanJudul("Master Mahasiswa");
}
TampilkanFilterMhsw();
$gos();
?>
