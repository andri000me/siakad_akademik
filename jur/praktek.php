<?php
// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$FilterMhswID = GetSetVar('FilterMhswID');
$FilterNamaMhsw = GetSetVar('FilterNamaMhsw');
$FilterProdiID = GetSetVar('FilterProdiID');


// *** Main ***
TampilkanJudul("Daftar Praktek Kerja Mahasiswa");
TampilkanFilter();
$gos = (empty($_REQUEST['gos']))? 'DftrMhswPraktek' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanFilter() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['FilterProdiID']);
  echo "<table class=box cellspacing=1 align=center width=940>
  <form name='frmFilterPraktek' action='?' method=POST>
  <input type=hidden name='gos' value='' />
  <input type=hidden name='praktekpage' value='1' />
  <tr>
      <td class=inp>Tahun Akd:</td>
      <td class=ul><input type=text name='TahunID' value='$_SESSION[TahunID]' size=5 maxlength=5 /></td>
      <td class=inp>Filter Prodi:</td>
      <td class=ul><select name='FilterProdiID' onChange='this.form.submit()'>$optprodi</select></td>
      </tr>
  <tr><td class=inp>Cari NPM:</td>
      <td class=ul><input type=text name='FilterMhswID' value='$_SESSION[FilterMhswID]' size=20 maxlength=20 /></td>
      <td class=inp>Cari Nama:</td>
      <td class=ul><input type=text name='FilterNamaMhsw' value='$_SESSION[FilterNamaMhsw]' size=20 maxlength=20 /></td>
      </tr>
  <tr>
      <td class=ul colspan=4 align=center>
        <input type=submit name='Cari' value='Cari Data' />
        <input type=button name='ResetFilter' value='Reset Filter'
          onClick=\"location='?mnux=$_SESSION[mnux]&gos=&TahunID=&FilterProdiID=&FilterMhswID=&FilterNamaMhsw='\" />
        &#9655;&#9654;
        <input type=button name='DaftarkanPraktekMhsw' value='Daftarkan Praktek Kerja Mhsw'
          onClick=\"javascript:PraktekEdit(1,0)\" />
        <input type=button name='CetakDaftarPraktek' value='Cetak Daftar Praktek Kerja'
          onClick=\"javascript:CetakPraktek()\" />
      </td>
      </tr>
  </form>
  </table>";
  RandomStringScript();
echo <<<SCR
  <script>
  <!--
  function PraktekEdit(md,id) {
    if (frmFilterPraktek.FilterProdiID.value == '') alert("Pilihan Program Studi terlebih dahulu");
    else {
      _rnd = randomString();
      lnk = "$_SESSION[mnux].edit.php?md="+md+"&PraktekKerjaID="+id+"&ProdiID="+frmFilterPraktek.FilterProdiID.value+"&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    }
  }
  function CetakPraktek() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?TahunID=$_SESSION[TahunID]&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnKelulusan(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].lulus.php?_rnd="+_rnd+"&PraktekKerjaID="+id;
    win2 = window.open(lnk, "", "width=700, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnDelete(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].hapus.php?_rnd="+_rnd+"&PraktekKerjaID="+id;
    win2 = window.open(lnk, "", "width=700, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
SCR;
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
function DftrMhswPraktek() {
  TampilkanFotoScript();
  // setup where-statement
  $whr_prodi = (empty($_SESSION['FilterProdiID']))? '' : "and m.ProdiID='$_SESSION[FilterProdiID]'";
  $whr_nama = (empty($_SESSION['FilterNamaMhsw']))? '' : "and m.Nama like '$_SESSION[FilterNamaMhsw]%'";
  $whr_nim  = (empty($_SESSION['FilterMhswID']))?   '' : "and m.MhswID like '$_SESSION[FilterMhswID]%'";
  $whr_tahun = (empty($_SESSION['TahunID']))? '' : "and p.TahunID = '$_SESSION[TahunID]' ";
  // Tampilkan
  $tapage = GetSetVar('praktekpage', 1);
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['praktekpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&praktekpage==PAGE='>=PAGE=</a>";
  $lst->tables = "praktekkerja p
    left outer join mhsw m on p.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    left outer join dosen d on d.Login = p.Pembimbing1 and d.KodeID = '".KodeID."'
    left outer join dosen dd on dd.Login = p.Pembimbing2 and dd.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = m.ProdiID
    where p.NA = 'N'
    $whr_prodi
    $whr_nama
    $whr_nim
    $whr_tahun
    ";
  $lst->fields = "p.*, m.Nama as NamaMhsw,
    date_format(p.TglMulai, '%d-%m-%Y') as _TglMulai,
    date_format(p.TglSelesai, '%d-%m-%Y') as _TglSelesai,
    m.PenasehatAkademik, 
    concat(d.Gelar1,' ',d.Nama,', ', d.Gelar) as Pembimbing1,
    concat(dd.Gelar1,' ',dd.Nama,', ', dd.Gelar) as Pembimbing2
    ";
  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><th class=ttl width=10>Edit</th>
        <th class=ttl width=80>NPM</th>
        <th class=ttl>Nama</th>
        <th class=ttl>Profil Perusahaan</th>
        <th class=ttl width=70>Tgl Mulai<hr size=1 color=white />Selesai</th>
        <th class=ttl width=180>Pembimbing</th>
        <th class=ttl width=10>Lulus</th>
        <th class=ttl width=10>Hapus</th>
        </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=cna=Lulus= align=center>
      <a href='#' onClick=\"javascript:PraktekEdit(0,=PraktekKerjaID=)\"><img src='img/edit.png' title='Edit Data Praktek Kerja' /></a>
      </td>
    <td class=cna=Lulus= align=center>
      =MhswID=
      <hr size=1 color=silver />
      <sup>=TahunID=</sup>
      </td>
    <td class=cna=Lulus=>=NamaMhsw=</td>
    <td class=cna=Lulus=>=NamaPerusahaan= <sup>=TeleponPerusahaan=</sup>
	  <br>
	  &nbsp;&nbsp;<font size=1 color=teal>=AlamatPerusahaan= 
	  =KotaPerusahaan=</font>
	  </td>
    <td class=cna=Lulus= align=center>
      <sup>=_TglMulai=
      <hr size=1 color=silver />
      =_TglSelesai=</sup>
      </td>
	<td class=cna=Lulus=>
      &bull; =Pembimbing1=<br />&bull; =Pembimbing2=
      </td>
    <td class=cna=Lulus= align=center>
      <a href='#' onClick=\"javascript:fnKelulusan(=PraktekKerjaID=)\"><img src='img/=Lulus=.gif' /></a>
      </td>
    <td class=cna=Lulus= align=center>
      <a href='#' onClick=\"javascript:fnDelete(=PraktekKerjaID=)\"><img src='img/del.gif' /></a>
      </td>
    </tr>
    <tr><td bgcolor=silver colspan=9 height=1></td></tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
}
?>
