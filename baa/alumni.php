<?php

// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$alumnipage = GetSetVar('alumnipage');
$srcmhswval = GetSetVar('srcmhswval');
$srcmhswkey = GetSetVar('srcmhswkey');

// *** Main ***
TampilkanJudul("Alumni");
$gos = (empty($_REQUEST['gos']))? 'DftrAlumni' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeaderAlumni() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['prodi']);
  $optprid = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], "KodeID='$_SESSION[KodeID]'", 'ProgramID');

  // Tampilkan formulir
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=700>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='alumnipage' value='1' />

  <tr><td class=inp>Program:</td>
      <td class=ul1><select name='prid' onChange='this.form.submit()'>$optprid</select></td>
      <td class=inp>Program Studi:</td>
      <td class=ul1><select name='prodi' onChange='this.form.submit()'>$optprodi</select></td></tr>
  <tr><td class=inp>Cari Mhsw:</td>
      <td class=ul1 colspan=3>
      <input type=text name='srcmhswval' value='$_SESSION[srcmhswval]' size=20 maxlength=20>
      <input type=submit name='srcmhswkey' value='NPM'>
      <input type=submit name='srcmhswkey' value='Nama'>
      <input type=submit name='srcmhswkey' value='Reset'></td>
      </tr>
  </form>
  </table></p>";
}
function DftrAlumni() {
  TampilkanHeaderAlumni();
  TampilkanFotoScript();
  // setup where-statement
  $whr = array();
  $ord = '';
  if (($_SESSION['srcmhswkey'] != 'Reset') &&
  !empty($_SESSION['srcmhswkey']) && !empty($_SESSION['srcmhswval'])) {
    $whr[] = "m.$_SESSION[srcmhswkey] like '%$_SESSION[srcmhswval]%' ";
    $ord = "order by m.$_SESSION[srcmhswkey]";
  }
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='".$_SESSION['prodi']."'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($whr)) $strwhr = "and " .implode(' and ', $whr);
  $strwhr = str_replace('NPM', "MhswID", $strwhr);
  $ord = str_replace('NPM', "MhswID", $ord);
  // Tampilkan
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['alumnipage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&alumnipage==PAGE='>=PAGE=</a>";

  $lst->tables = "alumni a
    left outer join mhsw m on m.MhswID = a.MhswID and m.KodeID = '".KodeID."'
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    where a.KodeID = '".KodeID."'
    $strwhr $ord";
  $lst->fields = "m.MhswID, m.Nama, m.StatusAwalID, m.StatusMhswID,
    m.Kelamin,
    m.Telepon, m.Handphone, m.Email, 
    if (m.Foto is NULL or m.Foto = '', 'img/tux001.jpg', m.Foto) as _Foto,
    m.ProgramID, m.ProdiID, m.Alamat, m.Kota,
    prd.Nama as PRD, sm.Nama as SM, sm.Keluar";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4 width=700>
    <tr><th class=ttl>No.</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Status</th>
    <th class=ttl>Telp/HP</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10><a name='=MhswID='>=NOMER=</a></td>
    <td class=cna=Keluar= width=130>
      <a href='?mnux=$_SESSION[mnux].edt&mhswbck=$_SESSION[mnux]&gos=fnEdit&mhswid==MhswID='><img src='img/edit.png'>
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
    </tr>
    <tr><td bgcolor=silver colspan=6 height=1></td></tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
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
?>
