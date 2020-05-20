<?php error_reporting(0);
// *** Parameters ***
$DosenID = $_SESSION['_Login'];
$page = GetSetVar('page', 1);
$dsn = GetFields('dosen', "Login='".$DosenID."' and KodeID", KodeID, "*");
$_filterMhs=GetSetVar('_filterMhs');
$_filterNama=GetSetVar('_filterNama');

// *** Main ***
TampilkanJudul("Penasehat Akademik: ".$dsn['Gelar1']." ".$dsn['Nama']." <sup>".$dsn['Gelar']."</sup>");
if (empty($dsn))
  die(ErrorMsg("Error",
    "Anda tidak berhak mengakses menu ini.<br />
    Modul ini khusus untuk dosen.
    <hr size=1 color=silver />
    Hubungi Sysadmin untuk informasi lebih lanjut."));

$gos = (empty($_REQUEST['gos']))? 'DftrMhsw' : $_REQUEST['gos'];
$gos($dsn);

// *** Functions ***
function DftrMhsw($dsn) {
	 $thn = GetFields("tahun","TahunID not like 'Tra%' and NA='N'  AND KodeID", KodeID, "max(TahunID) as TahunID");
  if ($_REQUEST['do']=='reset') {
	  $_SESSION['_filterMhs']='';
	  $_SESSION['_filterNama']='';
	}
  echo "<form action=? method=POST>
  <table class=box cellspacing=1 align=center width=100%>
  <tr><td class=inp >Cari: </td><td class=ul colspan=4>
  <input type=text name='_filterMhs' value='".$_SESSION['_filterMhs']."' placeholder='NPM' size=15 maxlength=15 />
  <input type=text name='_filterNama' value='".$_SESSION['_filterNama']."' placeholder='Nama Mahasiswa' size=15 /> <input type=submit value='Cari Mhs' />
<a href='?do=reset'><input type=button name='btnReset' value='Reset Pencarian' /></a> | 
      	<input type=button name='btnCetakDaftar' value='Cetak Semua Mhsw PA'
        onClick=\"javascript:fnCetakDaftar('".$dsn['Login']."')\" />
		<input type=button name='btnCetakDaftar' value='Cetak Mhsw PA yang Aktif Semester ini'
        onClick=\"javascript:fnCetakDaftarAktif('".$dsn['Login']."')\" />
      </td></tr>
  </table></form>
  <table class=box cellspacing=1 align=center width=100%>";
 
  $whr = (empty($_SESSION['_filterMhs']) && empty($_SESSION['_filterNama']) ? " ": " and m.MhswID LIKE '%".$_SESSION['_filterMhs']."%' ");
  $whr .= (empty($_SESSION['_filterNama']) && empty($_SESSION['_filterMhs']) ? " ": " and m.Nama LIKE '%".$_SESSION['_filterNama']."%' ");
  //echo $whr;
  // Tampilkan

  $Nama = $_SESSION['_Nama'];
       $NM = ucwords(strtolower($Nama));
       $NM = str_replace(" ","_",$NM);
       $NM = str_replace(".","_",$NM);
    $_SESSION['username'] = ($_SESSION['_LevelID']==120) ? substr($NM,0,10).'_'.$_SESSION['_Login']:$NM; // Must be already set

  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['page']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&page==PAGE='>=PAGE=</a>";
  $lst->tables = "mhsw m 
  	left outer join khs h on h.MhswID=m.MhswID and h.TahunID='".$thn['TahunID']."'
	left outer join prodi p on p.ProdiID=m.ProdiID
	left outer join khs s on s.MhswID=m.MhswID and s.Sesi=(h.Sesi-1)
    where m.KodeID = '".KodeID."'
      and m.PenasehatAkademik = '$dsn[Login]'
	  and m.StatusMhswID='A'
	  $whr
    order by m.TahunID, m.MhswID";

  $lst->fields = "m.MhswID, m.Nama as NamaMhsw, m.TahunID,
      m.ProdiID,p.Nama as NMProdi, h.ValidasiKe,h.SKS,h.MaxSKS,if (h.SetujuPA='Y','disetujui','belum disetujui') as Sudah,if (h.KonfirmasiKRS='Y' and h.SetujuPA!='Y','(Mahasiswa Menunggu Validasi)',if (h.KonfirmasiKRS!='Y','','(Sudah di Validasi)')) as StatusM,if (h.SetujuPA='Y','Y','N') as SetujuPA,m.TahunID as Angkatan,
	  if(s.IP is not NULL,s.IP,'data tidak tersedia') as IP,if(s.IPS is not NULL,s.IPS,'data tidak tersedia') as IPS, (SELECT sum(SKS) from krs where MhswID=m.MhswID and Tinggi='*' and BobotNilai>0 and TahunID not like 'Tra%' and TahunID not like 'Kliring%') as _TotalSKS,
      concat(LEFT(REPLACE(REPLACE(m.Nama,' ','_'),'.','_'),10),'_',m.MhswID) as Chat";
  $lst->headerfmt = "
	<table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><th class=ttl>Nmr</th>
      <th class=ttl>NPM</th>
      <th class=ttl>Nama Mahasiswa</th>
	  <th class=ttl>Angkatan</th>
      <th class=ttl width=90>Prodi</th>
	  <th class=ttl>Detail</th>
	  <th class=ttl>Status</th>
	  <th class=ttl>Proses</th>
      </tr>";
  $lst->footerfmt = "</table>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td>=MhswID=</td>
	<td>=NamaMhsw=</td>
	<td>=Angkatan=</td>
	<td>=NMProdi=</td>
	<td>SKS diambil: <b>=SKS= SKS</b> 
	<br>Maksimum SKS: <b>=MaxSKS= SKS</b>
	<br>IP Semester lalu: <b>=IPS=</b>
	<br>IP Kumulatif: <b>=IP=</b>
	<br>Total SKS: <b>=_TotalSKS= SKS</b> (tidak termasuk Nilai transfer/manual)</td>
	<td align='center'><img src='img/=SetujuPA=.gif'><br />=Sudah=<br>=StatusM=<br>
  <a class='btn btn-small btn-primary' onclick=\"chatWith('=Chat=')\"><i class='icon-envelope'></i> Kirim Pesan</a></td>
	<td align='center'><a href='#' onclick=\"javascript:ProsesMhsw('=MhswID=')\" class='btn btn-small btn-primary'>Validasi KRS</a> <br /><br />
						<a href='#' onclick=\"javascript:cetakTranskrip('=MhswID=','=ProdiID=')\" > <i class='icon icon-print'></i> Lihat Transkrip</a></td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount);
	
  RandomStringScript();
  echo <<<ESD
    <script>
    function fnCetakDaftar(dsn) {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].daftar.php?DosenID="+dsn+"&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    }
	function fnCetakDaftarAktif(dsn) {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].daftar.aktif.php?DosenID="+dsn+"&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    }
	 function fnReset() {
      lnk = "$_SESSION[mnux].php?";
      window.location(lnk);
	  }
	  function ProsesMhsw(mhswid) {
      lnk = "?mnux=$_SESSION[mnux].nilaimhs&mhswid="+mhswid;
      window.location=lnk;
	  }
	  function cetakTranskrip(mhswid,prodi) {
		if (prodi=='PGSD'){
      lnk = "baa/transkrip.sementara2.php?_TrMhswID="+mhswid;
		}else lnk = "baa/transkrip.sementara.php?_TrMhswID="+mhswid;
      win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
	  }

    </script>
ESD;
}
?>
