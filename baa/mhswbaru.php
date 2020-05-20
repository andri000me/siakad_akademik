<?php

// *** Parameters ***
include_once "$_SESSION[mnux].lib.php";

$gels = GetFields('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID, Nama");
$gelombang = $gels['PMBPeriodID'];

$_pmbNama = GetSetVar('_pmbNama');
$_pmbFrmID = GetSetVar('_pmbFrmID');
$_pmbPrg = GetSetVar('_pmbPrg');
$_pmbNomer = GetSetVar('_pmbNomer');
$_pmbPage = GetSetVar('_pmbPage');
$_pmbUrut = GetSetVar('_pmbUrut', 0);

$arrUrut = array('Nomer PMB~p.PMBID asc, p.Nama', 'Nomer PMB (balik)~p.PMBID desc, p.Nama', 'Nama~p.Nama');


// *** Main ***
TampilkanJudul("Pemrosesan Mahasiswa Baru :: $gels[Nama]");
if (empty($gelombang)) {
  echo ErrorMsg("Error",
    "Tidak ada gelombang PMB yang aktif.<br />
    Hubungi Kepala PMB untuk mengaktifkan gelombang.");
}
else {
  $gos = (empty($_REQUEST['gos']))? 'DftrCama' : $_REQUEST['gos'];
  $gos($gels, $gelombang);
}

// *** Functions ***
function GetUrutanPMB() {
  global $arrUrut;
  $a = ''; $i = 0;
  foreach ($arrUrut as $u) {
    $_u = explode('~', $u);
    $sel = ($i == $_SESSION['_pmbUrut'])? 'selected' : '';
    $a .= "<option value='$i' $sel>". $_u[0] ."</option>";
    $i++;
  }
  return $a;
}

function TampilkanHeader($gels, $gel) {
  $optfrm = GetOption2('pmbformulir', 'Nama', 'Nama', $_SESSION['_pmbFrmID'],
    "KodeID='".KodeID."'", 'PMBFormulirID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_pmbPrg'], "KodeID='".KodeID."'", 'ProgramID');
  $opturut = GetUrutanPMB();
  echo "<table class=box cellspacing=1 align=center>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <input type=hidden name='_pmbPage' value='0' />
  
  <tr>
      <td class=inp>Cari Nama:</td>
      <td class=ul1><input type=text name='_pmbNama' value='$_SESSION[_pmbNama]' size=20 maxlength=30 /></td>
      <td class=inp width=100>Filter Formulir:</td>
      <td class=ul1>
        <select name='_pmbFrmID'>$optfrm</select>
      </td>
      </tr>
  <tr>
      <td class=inp>Cari No. PMB:</td>
      <td class=ul1><input type=text name='_pmbNomer' value='$_SESSION[_pmbNomer]' size=20 maxlength=30 /></td>
      <td class=inp>Urutkan:</td>
      <td class=ul1><select name='_pmbUrut'>$opturut</select></td>
      </tr>
  <tr>
      <td class=inp>Program:</td>
      <td class=ul1><select name='_pmbPrg'>$optprg</select></td>
      <td class=ul1 colspan=2 align=center>
      <input type=submit name='Submit' value='Submit' />
      <input type=button name='Reset' value='Reset'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=&_pmbPage=0&_pmbNama=&_pmbNomer='\" />
      </td>
  </form>
  </table>";

}

function DftrCama($gels, $gel) {
  TampilkanHeader($gels, $gel);
  
  global $_maxbaris, $arrUrut;
  include_once "class/dwolister.class.php";
  // Urutan
  $_urut = $arrUrut[$_SESSION['_pmbUrut']];
  $__urut = explode('~', $_urut);
  $urut = "order by ".$__urut[1];
  // Filter formulir
  $whr = array();
  if (!empty($_SESSION['_pmbFrmID'])) $whr[] = "p.PMBFormulirID='$_SESSION[_pmbFrmID]'";
  if (!empty($_SESSION['_pmbPrg']))   $whr[] = "p.ProgramID = '$_SESSION[_pmbPrg]' ";
  if (!empty($_SESSION['_pmbNama']))  $whr[] = "p.Nama like '%$_SESSION[_pmbNama]%'";
  if (!empty($_SESSION['_pmbNomer'])) $whr[] = "p.PMBID like '%$_SESSION[_pmbNomer]%'";
  if ($_SESSION['_LevelID']==1) $editF = "<a href=# onClick=\"javascript:fnEditFormulir('=PMBID=')\" /><img src='img/edit.png'></a>";
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : 'and '.$_whr;
  $NIMSementara = GetaField('prodi', 'ProdiID', $pmb['ProdiID'], 'GunakanNIMSementara');
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_pmbPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "pmb p 
    left outer join pmbformulir f on p.PMBFormulirID = f.PMBFormulirID
    left outer join prodi _p on p.ProdiID = _p.ProdiID
    left outer join program _prg on p.ProgramID = _prg.ProgramID
    left outer join statusawal _sta on p.StatusAwalID = _sta.StatusAwalID
	left outer join mhsw m on m.MhswID = p.MhswID
    where p.KodeID = '".KodeID."' 
      and p.PMBPeriodID='$gel'
      $_whr
      $urut";
  $lst->fields = "p.PMBID, p.MhswID, p.Nama, p.Kelamin, 
    p.ProdiID, p.Pilihan1, p.Pilihan2, p.Pilihan3,
    f.Nama as FRM, p.LulusUjian, m.NIMSementara,
    _p.Nama as Prodi,
    _sta.Nama as STA, _prg.Nama as PRG,
    format(p.TotalBiaya, 0) as _TotalBiaya,
    format(p.TotalBayar, 0) as _TotalBayar,
    if (p.LulusUjian = 'Y', 
      if (p.MhswID is NULL or p.MhswID = '',
      concat('<a href=\'?mnux=$_SESSION[mnux]&gos=MhswBaruEdt&PMBID=', p.PMBID, '\'>
      <img src=\'img/edit.jpg\' width=30 title=\'Pembayaran dan pemrosesan Cama\' /></a>'),
      '<img src=\'img/lock.jpg\' width=30 title=\'Sudah diproses menjadi mahasiswa\' />'), '&times') as EDT,
	  if (p.LulusUjian = 'Y',
	  concat('<a href=\'#\' onClick=\"javascript:CetakKartu(\'', p.PMBID, '\',\'', m.NIMSementara, '\')\" /><img src=\'img/printer2.gif\' /></a>'),
	  '&nbsp;') as _ktm";
  $lst->page = $_SESSION['_pmbPage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->headerfmt = "<p><table class=box cellspacing=1 align=center width=1000>
    
    <tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>
      PMB #
      <hr size=1 color=silver />
      NPM
      </th>
    <th class=ttl colspan=2>Nama</th>
    <th class=ttl>
      Status
      <hr size=1 color=silver />
      Kelulusan
      </th>
    <th class=ttl>Formulir<hr size=1 color=silver />Program</th>
    <th class=ttl>Prodi</th>
    <th class=ttl width=100>BIPOT
      <a href='#' onClick=\"alert('Jumlah Biaya yg harus dibayarkan oleh Cama pada tahap 1 pembayaran')\"><img src='img/info.gif' /></a>
      <hr size=1 color=silver />
      Bayar</th>
    <th class=ttl>&nbsp;</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=ul1 width=10 align=center>
      =EDT=
      </td>
    <td class=ul width=80 align=center>
      =PMBID=&nbsp;
      <hr size=1 color=silver />
      <sub>=MhswID=</sub>
      </td>
    <td class=cnn=LulusUjian=>=Nama=</td>
    <td class=cnn=LulusUjian= width=10 align=center><img src='img/=Kelamin=.bmp' /></td>
    <td class=cnn=LulusUjian= width=70 align=center>
      =STA=
      <hr size=1 color=silver />
      <img src='img/=LulusUjian=.gif' />
      </td>
    <td class=cnn=LulusUjian= width=120>
      =FRM=&nbsp;$editF
      <hr size=1 color=silver />
      =PRG=&nbsp;
      </td>
    <td class=cnn=LulusUjian= width=200>
      =ProdiID=
      <hr size=1 color=silver />
      =Prodi=&nbsp;</td>
    <td class=cnn=LulusUjian= align=right>
      =_TotalBiaya=
      <hr size=1 color=silver />
      =_TotalBayar=
      </td>
    <td class=ul1 width=10 align=center>
      =_ktm=
      </td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>
	<script>
		function CetakKartu(pmbid,ob)
		{	
			if (ob == 'Y'){
				alert ('Mahasiswa ini masih memiliki NPM Sementara. \\n Lakukan Konversi NPM terlebih dahulu');
			} else {
				lnk = \"$_SESSION[mnux].ktm.php?pmbid=\"+pmbid;
				  win2 = window.open(lnk, \"\", \"width=600, height=400, scrollbars, resizable, status\");
				  if (win2.opener == null) childWindow.opener = self;
			 }
		}
		 function fnEditFormulir(PMBID) {
        lnk = \"$_SESSION[mnux].edt.formulir.php?mnux=$_SESSION[mnux]&PMBID=\"+PMBID+\"&gos=\";
        win2 = window.open(lnk, \"\", \"width=400, height=200, top=250,left=380,scrollbars\");
        if (win2.opener == null) childWindow.opener = self;
      }
	</script>";
  $hal = $lst->TampilkanHalaman();
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}
function MhswBaruEdt($gels, $gel) {
  $PMBID = $_REQUEST['PMBID'];
  $pmb = GetFields("pmb 
    left outer join prodi prd on pmb.ProdiID = prd.ProdiID
    left outer join program prg on pmb.ProgramID = prg.ProgramID
    left outer join statusawal sta on pmb.StatusAwalID = sta.StatusAwalID 
    left outer join asalsekolah a on pmb.AsalSekolah = a.SekolahID
	left outer join perguruantinggi pt on pmb.AsalSekolah = pt.PerguruanTinggiID",
	"pmb.KodeID='".KodeID."' and pmb.PMBID", 
    $PMBID,
    "pmb.*, prd.Nama as PROD, prg.Nama as PROG,
	if(a.Nama like '_%', a.Nama, 
		if(pt.Nama like '_%', pt.Nama, pmb.AsalSekolah)) as _NamaSekolah,
    sta.Nama as STAWAL"
    );
  if (empty($pmb))
    echo ErrorMsg('Error',
      "Calon Mahasiswa dengan nomer PMB: <b>$PMBID</b> tidak ditemukan.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Kembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
  else MhswBaruEdt1($gels, $gel, $pmb);
}
function MhswBaruEdt1($gels, $gel, $pmb) {
  TampilkanHeaderMhswBaruEdt($gels, $gel, $pmb);
  if (empty($pmb['MhswID'])) {
    if (empty($pmb['BIPOTID'])) AmbilBIPOTID($pmb);
    TampilkanDataBIPOT($gels, $gel, $pmb);
    TampilkanDataBayar($gels, $gel, $pmb);
  }
  else echo Konfirmasi('Telah Diproses',
    "Calon Mahasiswa ini telah diproses menjadi mahasiswa.<br />
    Silakan hubungi BAA untuk informasi lebih lanjut.<br />
    Atau hubungi Sysadmin untuk keterangan lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Kembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
}
function AmbilBIPOTID($pmb) {
  $bipot = GetFields('bipot', "KodeID='".KodeID."' and NA='N' and `Def`='Y' 
    and ProgramID='$pmb[ProgramID]' and ProdiID",
    $pmb['ProdiID'], '*');
  $bipot['BIPOTID'] += 0;

  if ($bipot['BIPOTID'] == 0)
    die(ErrorMsg('Error',
      "Belum ada master BIPOT (biaya & potongan) untuk Program: <b>$pmb[ProgramID]</b> dan
      Program-Studi: <b>$pmb[ProdiID]</b>.<br />
      Coba hubungi Kepala BAU/BAA untuk setup master BIPOT.<br />
      Atau hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
  else {
    $s = "update pmb set BIPOTID = '$bipot[BIPOTID]' where KodeID='".KodeID."' and PMBID='$pmb[PMBID]' ";
    $r = _query($s);
    echo Konfirmasi('Update Data',
      "Updating Data...<br />
      Please wait a second.");
    echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=MhswBaruEdt&PMBID=$pmb[PMBID]';</script>";
  }
}
function BuatSummaryBIPOT($pmb) {
  $TotalBiaya = number_format($pmb['TotalBiaya']);
  $TotalBayar = number_format($pmb['TotalBayar']);
  $Sisa = $pmb['TotalBiaya'] - $pmb['TotalBayar'];
  $_Sisa = number_format($Sisa);
  $color = ($Sisa > 0)? "color=red" : '';
  
  // Informasi BIPOT dan Uang Kuliah SKS
  $tahun = date('Y').'1-'.$pmb['ProgramID'].'-'.substr(date('Y'),-2);
  $prodi = GetFields('prodi',"ProdiID", $pmb['ProdiID'],"*");
  $BIPOTID = GetaField('bipot',"ProgramID='$pmb[ProgramID]' and ProdiID='$pmb[ProdiID]' and Tahun",$tahun,"BIPOTID");
  $BiayaSKSTeori = GetFields('bipot2',"PerSKS='Y' and BIPOTID",$BIPOTID,"*");
  $BiayaSKSPrakt = GetFields('bipot2',"PerSKSPraktek='Y' and BIPOTID",$BIPOTID,"*");
  $UangKuliah	= ($BiayaSKSPrakt['Jumlah'] * $prodi['DefSKSPraktek']) + ($BiayaSKSTeori['Jumlah'] * $prodi['DefSKS'])* 0.5;
  
  return "<table class=bsc cellspacing=1 width=100%>
  <tr>
  	  <td class=inp width=25%>Biaya SKS (50%)</td>
	  <td class=inp width=25%>Total Biaya</td>
      <td class=inp width=25%>Total Bayar</td>
      <td class=inp>Kekurangan</td>
      </tr>
  <tr>
  		<td align=right>".$UangKuliah."</td>
  		<td align=right>$TotalBiaya</td>
      <td align=right>$TotalBayar</td>
      <td align=right><font size=+1 $color>$_Sisa</font></td>
  </table>";
}
function TampilkanHeaderMhswBaruEdt($gels, $gel, $pmb) {
  // Jika belum menjadi Mhsw, maka lakukan perhitungan bipot & pembayaran
  if (empty($pmb['MhswID'])) {
    $MhswID = '&nbsp;';
    // Cek data pembayaran Calon Siswa
    $TotalBiaya = GetaField("bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID = b2.BIPOT2ID",
      "bm.PMBMhswID = 0 and bm.KodeID = '".KodeID."' and b2.SaatID = 1
      and bm.TahunID = '$pmb[PMBPeriodID]' and bm.PMBID", $pmb['PMBID'],
      "sum(bm.TrxID * bm.Jumlah * bm.Besar)")+0;
    $TotalBayar = GetaField('bayarmhsw',
      "PMBMhswID = 0 and KodeID = '".KodeID."'
      and TahunID = '$pmb[PMBPeriodID]' and PMBID", $pmb['PMBID'],
      "sum(Jumlah)")+0;
    if ($TotalBayar > 0 || $pmb['StatusAwalID']=='S') {
      KonfirmasiProsesNIMScript();
      $TombolProses = "&raquo; <input type=button name='Proses' value='Proses NPM' 
        onClick=\"javascript:KonfirmasiProsesNIM('$pmb[PMBID]')\" /> &laquo; <br />";
    }
    else {
      $TombolProses = '&nbsp;';
    }
    $Tombol2 = ($_SESSION['_LevelID']==42 || $_SESSION['_LevelID']==60 || $_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==20) ? "<input type=button name='Proses' value='Proses BIPOT'
        onClick=\"location='?mnux=$_SESSION[mnux]&BypassMenu=1&gos=ProsesBIPOT&PMBID=$pmb[PMBID]'\" />
      <input type=button name='HapusSemua' value='Hapus Semua BIPOT' 
        onClick=\"javascript:BIPOTDELALLCONF('$pmb[PMBID]')\" />
      <input type=button name='TambahBipot' value='Tambah Bipot'
        onClick=\"javascript:BIPOTEdit('$pmb[PMBID]', 1, 0)\" />
      <input type=button name='TambahBayar' value='Tambah Pembayaran'
        onClick=\"javascript:ByrEdit('$pmb[PMBID]', 1, 0, 0)\" />
	  <input type=button name='Tagihan' value='Print Tagihan' onClick=\"PrintTagihan('$pmb[PMBID]')\">":"";
  }
  else {
    $MhswID = "&raquo; <b>$pmb[MhswID]</b>";
    $Tombol2 = '';
  }
  $summary = BuatSummaryBIPOT($pmb);
  $arrPT = explode('~', $pmb['PrestasiTambahan']);
  foreach($arrPT as $Prestasi) 
  {	if(!empty($Prestasi)) $PrestasiTambahan .= (empty($PrestasiTambahan))? $Prestasi : "<br>".$Prestasi;
  }
  $GradePMB = GetaField('pmbgrade', "NilaiUjianMin <= $pmb[NilaiUjian] and $pmb[NilaiUjian] <= NilaiUjianMax and KodeID", KodeID, 'GradeNilai');
  
  
  
  echo "<table class=box cellspacing=1 align=center width=800>
  <tr><th class=ttl colspan=4>Data Calon Mahasiswa</th></tr>
  <tr><td class=inp>Nomer PMB:</td>
      <td class=ul1><b>$pmb[PMBID]</b> $MhswID</td>
      <td class=inp>Nama Cama:</td>
      <td class=ul1><b>$pmb[Nama]</b>&nbsp;</td>
      </tr>
  <tr><td class=inp>Program:</td>
      <td class=ul1>$pmb[PROG] <sup>($pmb[ProgramID])</sup></td>
      <td class=inp>Status:</td>
      <td class=ul1>$pmb[STAWAL] <sup>($pmb[StatusAwalID])</sup></td>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul1>$pmb[PROD]&nbsp; <sup>($pmb[ProdiID])</sup></td>
      <td class=inp>Asal Sekolah:</td>
      <td class=ul1>$pmb[_NamaSekolah]&nbsp;</td>
      </tr>
  <tr><td class=inp>GradeNilai:</td>
      <td class=ul1>$pmb[NilaiUjian]&nbsp; <sup>(Grade PMB: $GradePMB)</sup></td>
      <td class=inp>Prestasi:</td>
      <td class=ul1>$PrestasiTambahan&nbsp;</td>
      </tr>	  
  <tr><td class=ul1 colspan=4>
      $summary
      </td></tr>
  <tr><td class=ul1 colspan=4 align=center>
      $TombolProses
      <input type=button name='Kembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />
      <input type=button name='Refresh' value='Refresh'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=MhswBaruEdt&PMBID=$pmb[PMBID]'\" />
      $Tombol2
      </td></tr>
  </table>";
}
function HapusBIPOTScript() {
  echo <<<SCR
  <script>
  function BIPOTDELCONF(id, pmbid) {
    if (confirm("Benar Anda akan menghapus BIPOT ini?")) {
      window.location="?mnux=$_SESSION[mnux]&gos=HapusBIPOT&BypassMenu=1&_BIPOTMhswID="+id+"&PMBID="+pmbid;
    }
  }
  function BIPOTDELALLCONF(pmbid) {
    if (confirm("Benar Anda akan menghapus semua biaya di bawah ini? Biaya yang sudah terbayar tidak akan dihapus.")) {
      window.location="?mnux=$_SESSION[mnux]&gos=HapusSemuaBIPOT&BypassMenu=1&PMBID="+pmbid;
    }
  }
  function BIPOTEdit(pmbid, md, id) {
    lnk = "$_SESSION[mnux].bipotedit.php?pmbid="+pmbid+"&md="+md+"&id="+id;
    win2 = window.open(lnk, "", "width=400, height=400, scrollbars, resizable, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function ByrEdit(pmbid, md, bayar, bipotmhsw) {
    lnk = "$_SESSION[mnux].bayar.php?pmbid="+pmbid+"&md="+md+"&bayar="+bayar+"&bipotmhsw="+bipotmhsw;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function PrintTagihan(pmbid) {
    lnk = "$_SESSION[mnux].tagihan.php?pmbid="+pmbid;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, resizable, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function KonfirmasiHapusBayar(byrid, pmbid) {
    if (confirm("Anda benar akan menghapus data pembayaran ini? Mungkin daftar BIPOT di atas menjadi tidak balance lagi.")) {
      window.location="?mnux=$_SESSION[mnux]&gos=HapusBayar&byrid="+byrid+"&PMBID="+pmbid;
    }
  }
  	       
  </script>
SCR;
}
function HapusBIPOT($gels, $gel) {
  $_BIPOTMhswID = $_REQUEST['_BIPOTMhswID']+0;
  $PMBID = sqling($_REQUEST['PMBID']);
  $s = "delete from bipotmhsw where BIPOTMhswID = '$_BIPOTMhswID' ";
  $r = _query($s);
  HitungUlangBIPOTPMB($PMBID);
  echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=MhswBaruEdt&PMBID=$PMBID'</script>";
}
function HapusSemuaBIPOT($gels, $gel) {
  $PMBID = sqling($_REQUEST['PMBID']);
  
  //$pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $PMBID, '*');
  $s = "delete from bipotmhsw
        where PMBMhswID = 0
          and PMBID = '$PMBID'
          and Dibayar = 0
          and TahunID = '$gel'
          and KodeID = '".KodeID."' ";
  $r = _query($s);
  
  HitungUlangBIPOTPMB($PMBID);
  echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=MhswBaruEdt&PMBID=$PMBID'</script>";
}
function HapusBayar($gels, $gel) {
  $PMBID = sqling($_REQUEST['PMBID']);
  $byrid = sqling($_REQUEST['byrid']);
  // Hapus header
  $s = "delete from bayarmhsw
    where BayarMhswID = '$byrid' ";
  $r = _query($s);
  // Hapus detail
  $s1 = "delete from bayarmhsw2
    where BayarMhswID = '$byrid' ";
  $r1 = _query($s1);
  HitungUlangBIPOTPMB($PMBID);
  echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=MhswBaruEdt&PMBID=$PMBID'</script>";
}
function TampilkanDataBIPOT($gels, $gel, $pmb) {
  HapusBIPOTScript();
  $s = "select bm.*, s.Nama as _saat,
    format(bm.Jumlah, 0) as JML,
    format(bm.TrxID*bm.Besar, 0) as BSR,
    format(bm.Dibayar, 0) as BYR
    from bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID = b2.BIPOT2ID
      left outer join saat s on b2.SaatID = s.SaatID
    where bm.PMBMhswID = 0
      and bm.KodeID = '".KodeID."'
      and bm.PMBID = '$pmb[PMBID]'
    order by b2.Prioritas, bm.TrxID, bm.BIPOTMhswID";
  $r = _query($s); $n = 0;
  echo "<table class=box cellspacing=1 align=center width=800>";
  echo "<tr>
    <th class=ttl colspan=10>Daftar Biaya & Potongan (BIPOT)</th>
    </tr>";
  echo "<tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>Keterangan</th>
    <th class=ttl>Jumlah &times;<br />Besar</th>
    <th class=ttl>Total</th>
    <th class=ttl>Dibayar</th>
    <th class=ttl>Catatan</th>
    <th class=ttl>&times;</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $sub = $w['Jumlah'] * $w['Besar'] * $w['TrxID'];
    $_sub = number_format($sub);
    $ttl += $sub;
    $byr += $w['Dibayar'];
    if ($_SESSION['_LevelID'] == 1) {
      //if($w['Dibayar']>0){
      $del = ($w['Dibayar']>0)? '&nbsp;':"<a href='#' onClick=\"BIPOTDELCONF($w[BIPOTMhswID], '$pmb[PMBID]')\"><img src='img/del.gif' /></a>";
    }
	$TambahanNama = (empty($w['TambahanNama']))? "" : "($w[TambahanNama])";
    echo "<tr>
      <td class=inp width=15>$n</td>
      <td class=ul width=10>
        <a href='#' onClick=\"javascript:BIPOTEdit('$pmb[PMBID]', 0, $w[BIPOTMhswID])\"><img src='img/edit.png' /></a>
        </td>
      <td class=ul>
        $w[Nama] $TambahanNama<br />
        <div align=right><sub>$w[_saat]</sub></div>
        </td>
      <td class=ul norwap>
        <sup>$w[JML] &times;</sup><br />
        <div align=right>$w[BSR]</div>
        </td>
      <td class=ul align=right nowrap>$_sub</td>
      <td class=ul align=right nowrap>
        $w[BYR] <!--
        <a href='#' onClick=\"javascript:ByrEdit('$pmb[PMBID]', 1, 0, $w[PMBMhswID])\"><img src='img/edit.png' /></a>
        -->
        </td>
      <td class=ul1>$w[Catatan]&nbsp;</td>
      <td class=ul1 align=center width=10>
        $del
        </td>
      </tr>";
  }
  $TTL = number_format($ttl);
  $BYR = number_format($byr);
  $SS = number_format($ttl - $byr);
  echo "<tr><td bgcolor=silver colspan=10 height=1></td></tr>";
  echo "<tr>
    <td class=ul1 colspan=4 align=right><b>Total:</td>
    <td class=ul1 align=right><b>$TTL</b></td>
    <td class=ul1 align=right><b>$BYR</b></td>
    <td class=ul1 colspan=2>Sisa: <font size=+1>$SS</font></td>
    </tr>";
  echo "</table>";
  
}
function ProsesBIPOT($gels, $gel) {
  $PMBID = sqling($_REQUEST['PMBID']);
  $pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $PMBID, '*');
  if (empty($pmb))
    die(ErrorMsg('Error',
      "Data Cama dengan nomer PMB: <b>$PMBID</b> tidak ditemukan.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali'
        onClick=\"location='$_SESSION[mnux]&gos='\" />"));
  // Ambil BIPOT-nya
  $s = "select * 
    from bipot2 
    where BIPOTID = '$pmb[BIPOTID]'
      and Otomatis = 'Y'
	  and PerMataKuliah = 'N'
	  and PerSKS = 'N'
	  and PerLab = 'N'
      and NA = 'N'
    order by TrxID, Prioritas";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $oke = true;
    
	
	// Apakah sesuai dengan status awalnya?
    $pos = strpos($w['StatusAwalID'], ".".$pmb['StatusAwalID'].".");
    $oke = $oke && !($pos === false);

	// Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w['StatusMhswID'], ".A.");
    $oke = $oke && !($pos === false);
	
    // Apakah grade-nya?
    if ($oke) {
      if ($w['GunakanGradeNilai'] == 'Y') {
        $pos = strpos($w['GradeNilai'], ".".$pmb['GradeNilai'].".");
        $oke = $oke && !($pos === false);
      }
    }
	if ($oke) {
      if ($w['GunakanGradeIPK'] == 'Y') $oke=false; 
    }
    
	// Apakah dimulai pada sesi 1?
    if ($oke) {
      if ($w['MulaiSesi'] <= 1) $oke = true;
	  else $oke = false;
    }
	
    // Simpan data
    if ($oke) {
      // Cek, sudah ada atau belum? Kalau sudah, ambil ID-nya
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and PMBID = '$pmb[PMBID]'
        and TahunID='$pmb[PMBPeriodID]' and BIPOT2ID",
        $w['BIPOT2ID'], "BIPOTMhswID") +0;
      // Cek apakah memakai script atau tidak?
      if ($w['GunakanScript'] == 'Y') BipotGunakanScript($pmb, '', $w, $ada, 0);
      // Jika tidak perlu pakai script
      else {
        // Jika tidak ada duplikasi, maka akan di-insert. Tapi jika sudah ada, maka dicuekin aja.
        if ($ada == 0) { 
          // Simpan
		  $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w['BIPOTNamaID'], 'Nama');
          // Cek Jumlah jika memiliki beasiswa
		  /*if(GetaField('bipotnama', 'BIPOTNamaID', $w['BIPOTNamaID'], 'DipotongBeasiswa') == 'Y')
		  { $Jumlah = (1 - ($pmb['Diskon']/100))*$w['Jumlah'];
		  }
		  else
		  { $Jumlah = $w['Jumlah'];
		  }*/
		  $s1 = "insert into bipotmhsw
            (KodeID, COAID, PMBMhswID, PMBID, TahunID,
            BIPOT2ID, BIPOTNamaID, Nama, TrxID,
            Jumlah, Besar, Dibayar,
            Catatan, NA,
            LoginBuat, TanggalBuat)
            values
            ('".KodeID."', '$w[COAID]', 0, '$pmb[PMBID]', '$pmb[PMBPeriodID]',
            '$w[BIPOT2ID]', '$w[BIPOTNamaID]', '$Nama', '$w[TrxID]',
            1, '$w[Jumlah]', 0,
            'Auto', 'N',
            '$_SESSION[_Login]', now())";
          $r1 = _query($s1);
        }// end $ada=0
      } // end if $ada
    }   // end if $oke
  }     // end while
  HitungUlangBIPOTPMB($PMBID);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=MhswBaruEdt&PMBID=$pmb[PMBID]", 100);
}

function TampilkanDataBayar($gels, $gel, $pmb) {
  echo "<table class=box cellspacing=1 align=center width=800>";
  echo "<tr>
    <th class=ttl colspan=8>Daftar Pembayaran</th>
    </tr>";
  echo "<tr>
    <th class=ttl width=15>#</th>
    <th class=ttl width=80>Tanggal</th>
    <th class=ttl width=100>No. Bukti</th>
    <th class=ttl width=100>Besar Pembayaran</th>
    <th class=ttl>Catatan</th>
    <th class=ttl width=10>&times;</th>
    </tr>";

  $s = "select bm.BayarMhswID, bm.Keterangan, bm.Jumlah,
      date_format(bm.Tanggal, '%d-%m-%Y') as TGL,
      format(bm.Jumlah, 0) as JML
    from bayarmhsw bm
    where bm.KodeID = '".KodeID."'
      and bm.PMBMhswID = 0
      and bm.PMBID = '$pmb[PMBID]'
    order by bm.Tanggal";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $del = ($_SESSION['_LevelID'] == 1)? "<a href='#' onClick=\"javascript:KonfirmasiHapusBayar('$w[BayarMhswID]', '$pmb[PMBID]')\"><img src='img/del.gif' /></a>" : '&nbsp;';
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul1>$w[TGL]</td>
      <td class=ul1>$w[BayarMhswID]</td>
      <td class=ul1 align=right>$w[JML]</td>
      <td class=ul1>$w[Keterangan]&nbsp;</td>
      <td class=ul1 align=center width=10>&nbsp;</td>
      </tr>";
  }
  echo "</table>";
}
function KonfirmasiProsesNIMScript() {
  echo <<<SCR
  <script>
  function KonfirmasiProsesNIM_xx(pmbid) {
    if (confirm("Cama telah melunasi biaya yg harus dibayarkan di awal tahun. Anda yakin akan memproses NIM utk Cama ini sekarang?")) {
      window.location = "?mnux=$_SESSION[mnux]&gos=ProsesNIM&BypassMenu=1&PMBID="+pmbid;
    }
  }
  function KonfirmasiProsesNIM(pmbid) {
    lnk = "$_SESSION[mnux].prosesnim.php?pmbid="+pmbid;
    win2 = window.open(lnk, "", "width=600, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>
