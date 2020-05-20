<?php

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$NamaMhsw = GetSetVar('NamaMhsw');
$MhswID = GetSetVar('MhswID');
$_tabWisuda = GetSetVar('_tabWisuda', 0);
if ($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==40) {
$_arrWisuda = array(
  "Setup Wisuda~Setup",
  "Setup Prasyarat~Prasyarat",
  "Daftar Wisudawan~Daftar"
  );
  }
else {
$_arrWisuda = array(
  "Daftar Wisudawan~Daftar"
  );
 }
RandomStringScript();

// *** Main ***
TampilkanJudul("Administrasi Wisuda");
TampilkanTab($_arrWisuda, $_tabWisuda);
$gos = (empty($_REQUEST['gos']))? "Daftar" : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanTab($arr, $tab) {
  echo "<table class=bsc cellspacing=1 align=center><tr>";
  $i = 0;
  foreach ($arr as $a) {
    $_a = explode('~', $a);
    $sel = ($i == $tab)? "class=menuaktif" : "class=menuitem";
    echo "<td $sel align=center><a href='?mnux=$_SESSION[mnux]&_tabWisuda=$i&gos=$_a[1]'>$_a[0]</a></td>";
    $i++;
  }
  echo "</tr></table><p></p>";
}

function TampilkanTahunWisuda() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
  $optTahun = "<option value=''></option>";
  $s = "Select TahunID, Nama from wisuda";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
  if ($_SESSION['TahunID']==$w['TahunID']) {
  $optTahun .= "<option value='$w[TahunID]' selected>$w[TahunID] - $w[Nama]</option>";
  }
  else $optTahun .= "<option value='$w[TahunID]'>$w[TahunID]  - $w[Nama]</option>";
  }
  echo "<table class=box cellspacing=1 align=center width=800>
    <form name='frm' action='?' method=POST>
    <tr>
        <td class=inp width=60>Gelombang:</td>
        <td class=ul nowrap>
          <select name='TahunID'>$optTahun</select>
         </td>
        <td class=inp width=80>Filter Prodi:</td>
        <td class=ul nowrap>
          <select name='ProdiID' onChange='this.form.submit()'>$optprodi</select>
          </td>
        </tr>
    <tr>
        <td class=inp>Cari NPM:</td>
        <td class=ul>
          <input type=text name='MhswID' value='$_SESSION[MhswID]' size=10 maxlength=20 />
          </td>
        <td class=inp>Cari Nama:</td>
        <td class=ul>
          <input type=text name='NamaMhsw' value='$_SESSION[NamaMhsw]' size=10 maxlength=50 />
          <input type=submit name='SetParam' value='Refresh' />
          <input type=button name='Reset' value='Reset' 
            onClick=\"location='?mnux=$_SESSION[mnux]&_tabWisuda=$_SESSION[_tabWisuda]&gos=Daftar&NamaMhsw=&MhswID='\" />
        </td>
        </tr>
    </form>
    </table>";
}

function Daftar() {
  TampilkanTahunWisuda();
  $ada = GetFields('wisuda',
    "KodeID='".KodeID."' and TahunID", $_SESSION['TahunID'], '*');
  if (empty($ada))
    echo ErrorMsg("Error",
      "Setup untuk Wisuda tahun: <b>$_SESSION[TahunID]</b> belum diset.<br />
      Anda bisa membuat setupnya di tab <a href='?mnux=$_SESSION[mnux]&_tabWisuda=1&gos=Setup'>[Setup Wisuda]</a>.");
  else Daftarnya();
}
function Daftarnya() {
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=800>
  <tr>
      <td class=ul colspan=10 align=center>
        <input type=button name='DaftarkanWisudawan' value='Daftarkan'
          onClick="javascript:Wisudawan(1, 0)" />
        <input type=button name='CetakDaftarWisuda' value='Cetak Daftar Wisuda' 
          onClick="javascript:CetakDaftarWisudawan()" />
        <input type=button name='CetakDaftarPerKab' value='Per Kota/Kabupaten'
          onClick="javascript:CetakDaftarWisudawanPerKab()" />
        <input type=button name='CetakAlbum' value='Cetak Buku Album'
          onClick="javascript:CetakAlbumWisuda()" />
        <input type=button name='BuatSlideShow' value='Buat SlideShow (PDF)'
          onClick="BuatSlideShowWisudawan()" />
        |
        <input type=button name='btnProsesAlumni' value='PRC Alumni'
          onClick="javascript:fnProsesAlumni('$_SESSION[TahunID]')" /><br>
        <input type=button name='BuatSlideShow' value='Cetak Buku Wisudawan'
          onClick="BuatBukuWisudawan()" />		  
      </td>
      </tr>
  </table>
  <div class=well style="margin:0 auto; width:80%"><h4>Mohon perhatian Bapak/Ibu Kasubag. Akademik Fakultas, beberapa hal yang perlu diperhatikan pada saat validasi data wisudawan:</h4>
  <ol><li>Harap diperiksa kembali data wisudawan termasuk nama, tempat lahir, tanggal lahir, judul skripsi, tanggal yudisium, nama pembimbing dan yudisium.<br>
  Apabila ada kesalahan pada data tersebut, harap diperbaiki.</li>
  <li>Setiap prasyarat harus di ceklis dan dipastikan sudah dipenuhi oleh masing-masing wisudawan termasuk Administrasi Labor (meskipun di fakultas tidak terdapat administrasi tersebut)<br>
  Calon wisudawan baru bisa membayar uang wisuda apabila sudah mendapat tanda ceklis warna hijau pada kolom Prasyarat di Administrasi Wisuda Portal.</li>
  </ol></div>
  <script>
  function CetakDaftarWisudawan() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakDaftarWisudawanPerKab() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].perkabupaten.php?_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakAlbumWisuda() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].album.php?_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function BuatSlideShowWisudawan() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].slideshow.php?_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
    function BuatBukuWisudawan() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].buku.php?_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnProsesAlumni(thn) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].prosesalumni.php?_rnd="+_rnd+"&thn="+thn;
    win2 = window.open(lnk, "", "width=600, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function Wisudawan(md, id) {
    if (frm.TahunID.value == "") alert("Masukkan Gelombang wisuda terlebih dahulu");
    else {
      if (frm.ProdiID.value == "") alert("Pilihlah Program Studi terlebih dahulu");
      else {
        _rnd = randomString();
        lnk = "$_SESSION[mnux].wan.php?md="+md+"&id="+id+"&TahunID="+frm.TahunID.value+"&ProdiID="+frm.ProdiID.value+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
        if (win2.opener == null) childWindow.opener = self;
      }
    }
  }
  //-->
  </script>
ESD;
  // tampilkan daftar wisudawan
  $whr_prodi = (empty($_SESSION['ProdiID']))? '' : "and m.ProdiID='$_SESSION[ProdiID]'";
  $whr_nama = (empty($_SESSION['NamaMhsw']))? '' : "and m.Nama like '$_SESSION[NamaMhsw]%'";
  $whr_nim  = (empty($_SESSION['MhswID']))?   '' : "and m.MhswID like '$_SESSION[MhswID]%'";
  $whr_tahun = (empty($_SESSION['TahunID']))? '' : "and w.TahunID = '$_SESSION[TahunID]' ";
  // Tampilkan
  $tapage = GetSetVar('wsdpage', 1);
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['wsdpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&wsdpage==PAGE='>=PAGE=</a>";
  $lst->tables = "wisudawan w
    left outer join mhsw m on w.MhswID = m.MhswID and m.KodeID = '".KodeID."'
	left outer join khs h on h.MhswID = m.MhswID and h.TahunID = '$_SESSION[TahunID]'
    where w.TahunID = '$_SESSION[TahunID]'
    $whr_prodi
    $whr_nama
    $whr_nim
    order by w.MhswID";
  $lst->fields = <<<ESD
    w.*, w.Predikat, m.TotalSKS as SKS,
    m.Nama, m.IPK as IPT, m.Kelamin, m.FotoWisuda,
    if (m.Foto is NULL or m.FotoWisuda = '', 'img/tux001.jpg', concat('foto/wisudawan/kecil/',m.FotoWisuda)) as _Foto,
    if (m.ProdiID = 'PGSD', '', '') as module,
    if (m.Keluar = 'Y', "<img src='img/lock.jpg' width=20 title='Sudah diproses kelulusannya' />",
      concat("<a href=#", m.MhswID, " onClick='javascript:Wisudawan(0, ", w.WisudawanID, ")' /><img src='img/edit.png' /></a>")) as fnEdit
ESD;
  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=800>
    <tr><th class=ttl width=80>NPM</th>
        <th class=ttl width=10 title='Edit Data Wisudawan'>Edit</th>
        <th class=ttl>Nama Mhsw</th>
        <th class=ttl>IPK | SKS<hr size=1 color=silver />Predikat</th>
        <th class=ttl colspan=2>Prasyarat</th>
        <th class=ttl width=10 title='Hapus Data Wisudawan'>Del</th>
        </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp><a name='=MhswID='></a>=MhswID=</td>
    <td class=ul align=center>
      =fnEdit=
      </td>
    <td class=ul>
      =Nama=
      <div align=right>
      <sub>
      ".(($_SESSION['_LevelID']=='1' || $_SESSION['_LevelID']=='43' || $_SESSION['_LevelID']=='40' || $_SESSION['_LevelID']=='42')? "
      <a href=#=MhswID= onClick=\"javascript:CetakIjazah('=MhswID=','=module=')\" title='Cetak Transkrip' data-rel='tooltip'>
      <img src='img/printer2.gif' /></a> &laquo;Cetak Transkrip
      |":"")."
      <a href='#=MhswID=' onClick=\"javascript:EditProfile('=MhswID=')\" title='Edit Profile' data-rel='tooltip'>
      Edit Profile</a>
      |
      <a href='#=MhswID=' onClick=\"javascript:TampilkanFoto('=MhswID=', '=Nama=', '=_Foto=')\" title='=_Foto=' data-rel='tooltip'>
      Ganti Foto</a>
      |</sub>
      <a href='#=MhswID=' onClick=\"javascript:TampilkanFoto('=MhswID=', '=Nama=', 'foto/wisudawan/sedang/=FotoWisuda=')\" title='=_Foto='> 
      <img src='=_Foto=' width=30 /></a>
      </div>
      </td>
    <td class=ul>
      <div align=center>&nbsp;=IPT= | =SKS=</div>
      <hr size=1 color=silver />
      <div align=center>=Predikat=&nbsp;</div></td>
    <td class=ul width=10 align=center><img src='img/=PrasyaratLengkap=.gif' /></td>
    <td class=ul>=Prasyarat=&nbsp;</td>
    <td class=ul align=center>
      <a href='#=MhswID=' onClick=\"javascript:ConfirmDelete(=WisudawanID=)\"><img src='img/del.gif' /></a>
      </td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
  echo <<<ESD
  </table>
  
  <script>
  function ConfirmDelete(id) {
    if (confirm("Benar Anda akan menghapus data ini?")) {
      window.location="?BypassMenu=1&mnux=$_SESSION[mnux]&gos=HapusWisudawan&id="+id;
    }
  }
  function TampilkanFoto(MhswID, Nama, Foto) {
    jQuery.facebox("<font size=+1>"+Nama+"</font> <sup>(" + MhswID + ")</sup><hr size=1 color=silver /><img src='"+Foto+"' width=400 /> <br /><a href='#' onClick=\"javascript:GantiFotoMhsw('"+MhswID+"')\">Ganti Foto</a>");
  }
  function GantiFotoMhsw(MhswID) {
    _rnd = randomString();
    lnk = "master/gantifotowisuda.php?MhswID="+MhswID+"&back=../index.php?mnux=$_SESSION[mnux]&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=400, height=300, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function EditProfile(MhswID) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].profile.php?MhswID="+MhswID+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakIjazah(MhswID,module) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].ijazah"+module+".php?_TrMhswID="+MhswID+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
ESD;
}
function HapusWisudawan() {
  $id = $_REQUEST['id']+0;
  $s = "delete from wisudawan where WisudawanID = '$id' limit 1";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=Daftar&_tabWisuda=$_SESSION[_tabWisuda]", 10);
}

function Setup() {
  $_maxbaris = 10;
  $fmtTgl = '%d-%m-%Y';
  
  include_once "class/dwolister.class.php";
  $page = GetSetVar('_setuppage', 1);
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&_tabWisuda=$_SESSION[_tabWisuda]&gos=Setup&_setuppage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "wisuda w
    order by w.TahunID desc";
  $lst->fields = "w.WisudaID, w.TahunID, w.Nama, w.NA, w.Jumlah,
    date_format(w.TglMulai, '$fmtTgl') as _TglMulai,
    date_format(w.TglSelesai, '$fmtTgl') as _TglSelesai,
    date_format(w.TglWisuda, '$fmtTgl') as _TglWisuda
    ";
  $lst->page = $_SESSION['_setuppage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->headerfmt = "<p><table class=box cellspacing=1 align=center width=600>
    <tr>
      <td class=ul1 colspan=9>
      <input type=button name='BuatTahun' value='Buat Tahun Baru' 
        onClick=\"javascript:WisudaEdit(1, 0)\" />
      <input type=button name='Refresh' value='Refresh'
        onClick=\"location='?mnux=$_SESSION[mnux]&_tabWisuda=$_SESSION[_tabWisuda]&gos=Setup'\" />
      </td>
    </tr>
    <tr>
      <th class=ttl width=80 colspan=2>Tahun</th>
      <th class=ttl>Keterangan</th>
      <th class=ttl width=80>Pendaftaran</th>
      <th class=ttl width=80>Tgl Wisuda</th>
      <th class=ttl width=50>Jumlah</th>
      <th class=ttl width=30>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=ul1 align=center width=10>
      <a href='#' onClick=\"javascript:WisudaEdit(0, =WisudaID=)\"><img src='img/edit.png' /></a></td>
    <td class=cna=NA= width=70><font size=+1>=TahunID=</font></td>
    <td class=cna=NA=>=Nama=&nbsp;</td>
    <td class=cna=NA= align=center>=_TglMulai=<hr size=1 color=silver />=_TglSelesai=</td>
    <td class=cna=NA= align=center>=_TglWisuda=&nbsp;</td>
    <td class=cna=NA= align=right>=Jumlah= <sup>&#985;</sup></td>
    <td class=ul align=center><img src='img/book=NA=.gif' /></td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>";
  $hal = $lst->TampilkanHalaman();
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
  
  echo <<<ESD
  <script>
  function WisudaEdit(md, id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].edit.php?md="+md+"&id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=400, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
ESD;
}

function Prasyarat() {
  $s = "select *
    from wisudaprasyarat
    where KodeID = '".KodeID."'
    order by PrasyaratID";
  $r = _query($s);
  
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <tr><td class=ul1 colspan=10>
      <input type=button name='TambahPrasyarat' value='Tambah Prasyarat'
        onClick="javascript:PrasyaratEdit(1, '')" />
      <input type=button name='Refresh' value='Refresh'
        onClick="location='?mnux=$_SESSION[mnux]&_tabWisuda=$_SESSION[_tabWisuda]&gos=Prasyarat'" />
      </td>
      </tr>
  <tr><th class=ttl width=80 colspan=2>Kode</th>
      <th class=ttl>Prasyarat</th>
      <th class=ttl width=10>NA</th>
      </tr>
ESD;
  while ($w = _fetch_array($r)) {
    echo "<tr>
    <td class=ul width=10>
      <a href='#' onClick=\"javascript:PrasyaratEdit(0, '$w[PrasyaratID]')\"><img src='img/edit.png' /></a>
      </td>
    <td class=cna$w[NA] width=70><a name='$w[PrasyaratID]'></a>$w[PrasyaratID]</td>
    <td class=cna$w[NA]>$w[Nama]&nbsp;</td>
    <td class=cna$w[NA] align=center width=10><img src='img/book$w[NA].gif' /></a>
    </tr>";
  }
  echo <<<ESD
  </table></p>
  
  <script>
  function PrasyaratEdit(md, id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].prasyarat.php?md="+md+"&id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=400, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
ESD;
}
?>
