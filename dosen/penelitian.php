<?php

// *** Parameters ***
$Ketua = GetSetVar('Ketua');
$DosenID = GetSetVar('NIDN');
$Judul = GetSetVar('Judul');
$_tabPenelitian = GetSetVar('_tabPenelitian', 0);
$_arrWisuda = array(
  "Penelitian Internal~PenelitianInternal",
  "Penelitian DIKTI~PenelitianDIKTI",
  "Buku Ajar~BukuAjar",
  "Penyelenggaraan Forum Ilmiah~ForumIlmiah"
  );
RandomStringScript();

// *** Main ***
TampilkanJudul("Administrasi Wisuda");
TampilkanTab($_arrWisuda, $_tabPenelitian);
$gos = (empty($_REQUEST['gos']))? "PenelitianInternal" : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanTab($arr, $tab) {
  echo "<table class=bsc cellspacing=1 align=center><tr>";
  $i = 0;
  foreach ($arr as $a) {
    $_a = explode('~', $a);
    $sel = ($i == $tab)? "class=menuaktif" : "class=menuitem";
    echo "<td $sel align=center><a href='?mnux=$_SESSION[mnux]&_tabPenelitian=$i&gos=$_a[1]'>$_a[0]</a></td>";
    $i++;
  }
  echo "</tr></table><p></p>";
}

function BukuAjar() { 
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=800>
  <tr>
      <td class=ul colspan=10 align=center>
        <input type=button name='BukuAjar' value='Buat Buku Ajar'
          onClick="javascript:BukuAjar(1, 0)" />
      </td>
      </tr>
  </table>
  
  <script>
  <!--
  function CetakDaftarWisudawan() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
ESD;
  // tampilkan daftar wisudawan
  //$whr_nidn = (empty($_SESSION['NIDN']))? '' : "and d.nidn_ketua='$_SESSION[NIDN]'";
//  $whr_tahun = (empty($_SESSION['Tahun']))? '' : "and w.TahunID = '$_SESSION[TahunID]' ";
//  $whr_judul = (empty($_SESSION['Judul']))? '' : "and p.judul = '$_SESSION[Judul]' ";
  // Tampilkan
  $tapage = GetSetVar('BukuAjar', 1);
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['BukuAjar']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&gos=BukuAjar&BukuAjar==PAGE='>=PAGE=</a>";
  $lst->tables = "dosen_bukuajar p
    where 1
    order by p.BahanAjarID";
  $lst->fields = <<<ESD
   p.*, concat("<a href=#", p.nidn, " onClick='javascript:BukuAjar(0, ", p.BahanAjarID, ")' /><img src='img/edit.png' /></a>") as fnEdit
ESD;
  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=800>
    <tr><th class=ttl width=80>NIDN</th>
        <th class=ttl width=10 title='Edit Buku Ajar'>Edit</th>
        <th class=ttl>Judul</th>
        <th class=ttl>Nama</th>
        <th class=ttl width=10 title='Hapus Buku Ajar'>Del</th>
        </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp><a name='=nidn='></a>=nidn=</td>
    <td class=ul align=center>
      =fnEdit=
      </td>
    <td class=ul>=judul=</td>
    <td class=ul>=nama_lengkap=</td>
    <td class=ul align=center>
      <a href='#=nidn=' onClick=\"javascript:ConfirmDelete(=BahanAjarID=)\"><img src='img/del.gif' /></a>
      </td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
  echo <<<ESD
  
  </table>
 
  <script>
  function PenelitianDIKTI(md, id) {
        _rnd = randomString();
        lnk = "$_SESSION[mnux].dikti.php?md="+md+"&id="+id+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
  }
  function PenelitianInternal(md, id) {
        _rnd = randomString();
        lnk = "$_SESSION[mnux].internal.php?md="+md+"&id="+id+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
  }
  function BukuAjar(md, id) {
        _rnd = randomString();
        lnk = "$_SESSION[mnux].bukuajar.php?md="+md+"&id="+id+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
  }
  function ConfirmDelete(id) {
    if (confirm("Benar Anda akan menghapus data ini?")) {
      window.location="?BypassMenu=1&mnux=$_SESSION[mnux]&gos=HapusBukuAjar&id="+id;
    }
  }
  </script>
ESD;
}
function PenelitianDIKTI() {
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=800>
  <tr>
      <td class=ul colspan=10 align=center>
        <input type=button name='PenelitianBaru' value='Buat Penelitian Baru'
          onClick="javascript:PenelitianDIKTI(1, 0)" />
      </td>
      </tr>
  </table>
  
  <script>
  <!--
  function CetakDaftarWisudawan() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
ESD;
  // tampilkan daftar wisudawan
  //$whr_nidn = (empty($_SESSION['NIDN']))? '' : "and d.nidn_ketua='$_SESSION[NIDN]'";
//  $whr_tahun = (empty($_SESSION['Tahun']))? '' : "and w.TahunID = '$_SESSION[TahunID]' ";
//  $whr_judul = (empty($_SESSION['Judul']))? '' : "and p.judul = '$_SESSION[Judul]' ";
  // Tampilkan
  $tapage = GetSetVar('dikti', 1);
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dikti']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&gos=PenelitianDIKTI&dikti==PAGE='>=PAGE=</a>";
  $lst->tables = "dosen_penelitian_dikti p
    where 1
    order by p.PenelitianID";
  $lst->fields = <<<ESD
   p.*, concat("<a href=#", p.nidn_ketua, " onClick='javascript:PenelitianDIKTI(0, ", p.PenelitianID, ")' /><img src='img/edit.png' /></a>") as fnEdit
ESD;
  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=800>
    <tr><th class=ttl width=80>NIDN</th>
        <th class=ttl width=10 title='Edit Data Penelitian'>Edit</th>
        <th class=ttl>Tahun</th>
        <th class=ttl>Judul Penelitian</th>
        <th class=ttl>Ketua</th>
        <th class=ttl>Anggota</th>
        <th class=ttl>Jumlah Dana</th>
        <th class=ttl width=10 title='Hapus Data Penelitian'>Del</th>
        </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp><a name='=nidn_ketua='></a>=nidn_ketua=</td>
    <td class=ul align=center>
      =fnEdit=
      </td>
    <td class=ul>=tahun=</td>
    <td class=ul>=judul=</td>
    <td class=ul>=nama_ketua=</td>
    <td class=ul>=nama_anggota=</td>
    <td class=ul>=jumlah_dana=</td>
    <td class=ul align=center>
      <a href='#=nidn_ketua=' onClick=\"javascript:ConfirmDelete(=PenelitianID=)\"><img src='img/del.gif' /></a>
      </td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
  echo <<<ESD
  
  </table>
 
  <script>
  function PenelitianDIKTI(md, id) {
        _rnd = randomString();
        lnk = "$_SESSION[mnux].dikti.php?md="+md+"&id="+id+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
  }
  function PenelitianInternal(md, id) {
        _rnd = randomString();
        lnk = "$_SESSION[mnux].internal.php?md="+md+"&id="+id+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
  }
   function BukuAjar(md, id) {
        _rnd = randomString();
        lnk = "$_SESSION[mnux].bukuajar.php?md="+md+"&id="+id+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
  }
  function ConfirmDelete(id) {
    if (confirm("Benar Anda akan menghapus data ini?")) {
      window.location="?BypassMenu=1&mnux=$_SESSION[mnux]&gos=HapusPenelitianDIKTI&id="+id;
    }
  }
  </script>
ESD;
}
function HapusPenelitianDIKTI() {
  $id = $_REQUEST['id']+0;
  $s = "delete from dosen_penelitian_dikti where PenelitianID = '$id' limit 1";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=PenelitianDIKTI&_tabPenelitian=$_SESSION[_tabPenelitian]", 10);
}
function HapusPenelitianInternal() {
  $id = $_REQUEST['id']+0;
  $s = "delete from dosen_penelitian_internal where PenelitianID = '$id' limit 1";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=PenelitianInternal&_tabPenelitian=$_SESSION[_tabPenelitian]", 10);
}
function HapusBukuAjar() {
  $id = $_REQUEST['id']+0;
  $s = "delete from dosen_bukuajar where BahanAjarID = '$id' limit 1";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=BukuAjar&_tabBukuAjar=$_SESSION[__tabBukuAjar]", 10);
}

//======================================================================Internal===============================
function PenelitianInternal() {
echo "<style>.left{float:left; margin:0 10px 0 5px;} </style>";
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=800>
  <tr>
      <td class=ul colspan=6 >
      <form action='?' method='post' class='left'><input type='text' name='NIDN' value='$_SESSION[NIDN]' /> <input type="submit" value="NIDN" /></form>
      <form action='?' method='post' class='left'><input type='text' name='Judul' value='$_SESSION[Judul]' /> <input type="submit" value="Judul" /></form>
      <form action='?' method='post' class='left'><input type='text' name='Ketua' value='$_SESSION[Ketua]' /> <input type="submit" value="Ketua" /></form>
      <br /> <br />
       <form action='?' method='post' class='left'><input type='hidden' name='Ketua' value='' /> <input type='hidden' name='Judul' value='' /> <input type='hidden' name='NIDN' value='' /><input type="submit" value="Reset Pencarian" /></form>
        <input type=button name='PenelitianBaru' value='Buat Penelitian Baru'
          onClick="javascript:PenelitianInternal(1, 0)" />
      </td>
      </tr>
  </table>
  
  <script>
  <!--
  function CetakDaftarWisudawan() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
ESD;
  // tampilkan daftar wisudawan
$whr_nidn = (empty($_SESSION['NIDN']))? '' : "and p.NIDN_Ketua like '%$_SESSION[NIDN]%'";
$whr_judul = (empty($_SESSION['Judul']))? '' : "and p.judul like '%$_SESSION[Judul]%' ";
 $whr_ketua = (empty($_SESSION['Ketua']))? '' : "and p.nama_ketua like '%$_SESSION[Ketua]%' ";
  // Tampilkan
  $tapage = GetSetVar('wsdpage', 1);
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['wsdpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&wsdpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosen_penelitian_internal p
    where 1 $whr_nidn $whr_judul $whr_ketua
    order by p.PenelitianID";
  $lst->fields = <<<ESD
   p.*, concat("<a href=#", p.nidn_ketua, " onClick='javascript:PenelitianInternal(0, ", p.PenelitianID, ")' /><img src='img/edit.png' /></a>") as fnEdit
ESD;
  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=800>
    <tr><th class=ttl width=80>NIDN</th>
        <th class=ttl width=10 title='Edit Data Penelitian'>Edit</th>
        <th class=ttl>Tahun</th>
        <th class=ttl>Judul Penelitian</th>
        <th class=ttl>Ketua</th>
        <th class=ttl>Anggota</th>
        <th class=ttl>Jumlah Dana</th>
        <th class=ttl width=10 title='Hapus Data Penelitian'>Del</th>
        </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp><a name='=NIDN_Ketua='></a>=NIDN_Ketua=</td>
    <td class=ul align=center>
      =fnEdit=
      </td>
    <td class=ul>=tahun=</td>
    <td class=ul>=judul=</td>
    <td class=ul>=nama_ketua=</td>
    <td class=ul>=nama_anggota=</td>
    <td class=ul>=jumlah_dana=</td>
    <td class=ul align=center>
      <a href='#=NIDN_Ketua=' onClick=\"javascript:ConfirmDelete(=PenelitianID=)\"><img src='img/del.gif' /></a>
      </td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
  echo <<<ESD
  </table>
  
  <script>
  function PenelitianDIKTI(md, id) {
        _rnd = randomString();
        lnk = "$_SESSION[mnux].dikti.php?md="+md+"&id="+id+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
  }
  function PenelitianInternal(md, id) {
        _rnd = randomString(); 
        lnk = "$_SESSION[mnux].internal.php?md="+md+"&id="+id+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
  }
  function ConfirmDelete(id) {
    if (confirm("Benar Anda akan menghapus data ini?")) {
      window.location="?BypassMenu=1&mnux=$_SESSION[mnux]&gos=HapusPenelitianInternal&id="+id;
    }
  }
  </script>
ESD;
}
?>
