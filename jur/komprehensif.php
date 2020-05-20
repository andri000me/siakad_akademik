<?php

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$FilterMhswID = GetSetVar('FilterMhswID');
$FilterNamaMhsw = GetSetVar('FilterNamaMhsw');
$FilterProdiID = GetSetVar('FilterProdiID');

// *** Main ***
TampilkanJudul("Ujian Akhir");
TampilkanFilter();
$gos = (empty($_REQUEST['gos']))? 'DaftarKomprehensif' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanFilter() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['FilterProdiID']);
  echo "<table class=box cellspacing=1 align=center width=940>
  <form name='frmFilterKomprehensif' action='?' method=POST>
  <input type=hidden name='gos' value='' />
  <input type=hidden name='komprepage' value='1' />
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
        <input type=button name='DaftarkanMhswKomprehensif' value='Daftarkan Komprehensif Mhsw'
          onClick=\"javascript:KomprehensifEdit(1,0)\" />
		<input type=button name='SetupKompre' value='Setup Komprehensif'
          onClick=\"javascript:SetupKomprehensif()\" />
	  <br>
		<input type=button name='CetakFormKompre' value='Cetak Formulir'
          onClick=\"javascript:CetakForm()\" />
		<input type=button name='CetakJadwalKompre' value='Cetak Jadwal'
          onClick=\"javascript:CetakJadwal()\" />
		<input type=button name='CetakJadwalKomprePerHari' value='Cetak Jadwal Per Hari'
		  onClick=\"javascript:CetakJadwalPerHari()\" />
      </td>
      </tr>
  </form>
  </table>";
  RandomStringScript();
echo <<<SCR
  <script>
	  function KomprehensifEdit(md,id) {
		  _rnd = randomString();
		  lnk = "$_SESSION[mnux].edit.php?md="+md+"&_rnd="+_rnd+"&KompreID="+id;
		  win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
		  if (win2.opener == null) childWindow.opener = self;
	  }
	  function EditJadwal(md,id,prodi) {
		_rnd = randomString();
		lnk = "$_SESSION[mnux].jadwal.php?md="+md+"&_rnd="+_rnd+"&KompreID="+id+"&ProdiID="+prodi;
		win2 = window.open(lnk, "", "width=1000, height=500, scrollbars, status");
		win2.moveTo(100,100);
		if (win2.opener == null) childWindow.opener = self;
	  }
	  function DetailKompre(md,id,prodi) {
			_rnd = randomString();
			lnk = "$_SESSION[mnux].nilai.php?md="+md+"&_rnd="+_rnd+"&KompreID="+id+"&ProdiID="+prodi;
			win2 = window.open(lnk, "detail", "width=500, height=500, scrollbars, status");
			win2.moveTo(100,100);
			if (win2.opener == null) childWindow.opener = self;
	  }
	  function CetakKomprehensif() {
		_rnd = randomString();
		lnk = "$_SESSION[mnux].cetak.php?TahunID=$_SESSION[TahunID]&_rnd="+_rnd;
		win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
		if (win2.opener == null) childWindow.opener = self;
	  }
	  function CetakForm() {
		_rnd = randomString();
		lnk = "$_SESSION[mnux].cetakformulir.php?ProdiID=$_SESSION[FilterProdiID]&_rnd="+_rnd;
		win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status, resizable");
		if (win2.opener == null) childWindow.opener = self;
	  }
	  function CetakJadwal() {
		_rnd = randomString();
		lnk = "$_SESSION[mnux].cetakJadwal.php?_rnd="+_rnd+"TahunID=$_SESSION[TahunID]&ProdiID=$_SESSION[FilterProdiID]";
		win2 = window.open(lnk, "cetakJ", "width=700, height=600, scrollbars, status, resizable");
		if (win2.opener == null) childWindow.opener = self;
	  }
	  function CetakJadwalPerHari() {
		_rnd = randomString();
		lnk = "$_SESSION[mnux].cetakjadwalperhari.php?_rnd="+_rnd+"TahunID=$_SESSION[TahunID]&ProdiID=$_SESSION[FilterProdiID]";
		win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status, resizable");
		if (win2.opener == null) childWindow.opener = self;
	  }
	  function SetupKomprehensif() {
		_rnd = randomString();
		lnk = "$_SESSION[mnux].setup.php?TahunID=$_SESSION[TahunID]&_rnd="+_rnd;
		win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
		if (win2.opener == null) childWindow.opener = self;
	  }
    function CetakSKPenguji(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].SK.penguji.php?_rnd="+_rnd+"&KompreID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
    }
    function CetakUndangan(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].undangan.php?_rnd="+_rnd+"&KompreID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
    }
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
function DaftarKomprehensif() {
  TampilkanFotoScript();
  
  // setup where-statement
  $whr_prodi = (empty($_SESSION['FilterProdiID']))? '' : "and m.ProdiID='$_SESSION[FilterProdiID]'";
  $whr_nama = (empty($_SESSION['FilterNamaMhsw']))? '' : "and m.Nama like '$_SESSION[FilterNamaMhsw]%'";
  $whr_nim  = (empty($_SESSION['FilterMhswID']))?   '' : "and m.MhswID like '$_SESSION[FilterMhswID]%'";
  $whr_tahun = (empty($_SESSION['TahunID']))? '' : "and k.TahunID = '$_SESSION[TahunID]' ";
  
  $s = "select * from kompre k left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
  		where k.NA = 'N' $whr_prodi $whr_tahun $whr_nim $whr_nama";
  $qs = _query($s);
  $group = (mysql_num_rows($qs) == 0)? '' : 'Group by k.MhswID, k.Gagal';

  // Tampilkan
  $komprepage = GetSetVar('komprepage', 1);
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['komprepage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&komprepage==PAGE='>=PAGE=</a>";
  $lst->tables = "kompre k
    left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = m.ProdiID
	where k.NA = 'N'
    $whr_prodi
    $whr_nama
    $whr_nim
    $whr_tahun
	$group
    ";
  $lst->fields = "k.*, m.Nama as NamaMhsw,
    k.TanggalUjian as _TglUjian,
    m.PenasehatAkademik, 
	concat('cna',k.Final) as _Cls,
	m.ProdiID as _ProdiID,
	if(prd.PilihanKompre = 'Y', 
		concat('<a href=\'#\' onclick=\"javascript:EditJadwal(0, ', 
				k.KompreID,
				', \'',
				m.ProdiID, 
				'\')\" title=\'Edit Jadwal Ujian\'><img src=\'img/edit.jpg\' /></a>'),
		concat('<a href=\'#\' onclick=\"javascript:KomprehensifEdit(0, ', 
				k.KompreID,
				')\" title=\'Edit Jadwal Ujian\'>',
				'<sup>', k.TanggalUjian, '</sup></a>')) as _Jadwal";

  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=940>
    <tr><th class=ttl width=20>#</th>
        <th class=ttl width=150>NPM</th>
        <th class=ttl>Nama</th>
		<th class=ttl width=100>Tanggal Ujian</th>
		<th class=ttl width=200>Cetak</th>
    <th class=ttl width=80>Nilai</th>
        <th class=ttl width=100>Lulus Kompre</th>
        </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class==_Cls= align=center>
      =NOMER=
      </td>
    <td class==_Cls= align=center>
	<a href='#' onClick=\"javascript:KomprehensifEdit(0,=KompreID=)\"><img src='img/edit.png' title='Edit Ujian Akhir' /></a>
  =MhswID=
	<hr size=1 color=silver />
      <sup>=TahunID=</sup>
      </td>
    <td class==_Cls=>=NamaMhsw=</td>
	<td align='center' class==_Cls=>=_Jadwal=</td>
  <td class==_Cls=><div align=right>
      &laquo; <a href='#' onClick=\"javscript:CetakSKPenguji(=KompreID=)\" title='Cetak SK Penguji'>SK Penguji</a><br>
      &laquo; <a href='#' onClick=\"javscript:CetakUndangan(=KompreID=)\" title='Undangan'>Undangan</a><br>
      </div></td>
	 <td class==_Cls= align=center>=NilaiRata=</td>
    <td class==_Cls= align=center>
      <a href=\"javascript:DetailKompre(0,=KompreID=,'=_ProdiID=')\" title='Detail Ujian'><img src='img/=Lulus=.gif' /></a>
      </td>
    </tr>
    <tr><td bgcolor=silver colspan=8 height=1></td></tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
}

?>