<?php

// *** Parameters ***
$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
$_pmbNama = GetSetVar('_pmbNama');
$_pmbFrmID = GetSetVar('_pmbFrmID');
$_pmbPrg = GetSetVar('_pmbPrg');
$_pmbNomer = GetSetVar('_pmbNomer');
$_pmbPage = GetSetVar('_pmbPage');
$_pmbUrut = GetSetVar('_pmbUrut', 0);
$arrUrut = array('Nomer PMB~p.PMBID asc, p.Nama', 'Nomer PMB (balik)~p.PMBID desc, p.Nama', 'Nama~p.Nama');
RandomStringScript();

// *** Main ***
TampilkanJudul("Status Calon Mahasiswa - $gelombang");
if (empty($gelombang)) {
  echo ErrorMsg("Error",
    "Tidak ada gelombang PMB yang aktif.<br />
    Hubungi Kepala PMB untuk mengaktifkan gelombang.");
}
else {
  $gos = (empty($_REQUEST['gos']))? 'DftrForm' : $_REQUEST['gos'];
  $gos($gelombang);
}

// *** Functions ***
function IsiFormulirScript($gel) {
  echo <<<SCR
  <script>
  function IsiFormulir(MD,GEL,ID) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].isi.php?md="+MD+"&gel="+GEL+"&id="+ID+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=700, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
function CetakKartuScript() {
  echo <<<SCR
  <script>
  function CetakKartu(ID) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].kartutest.php?id="+ID+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
    window.location = "?mnux=$_SESSION[mnux]";
  }
  function SuratPernyataan(PMBID, WID) {
	lnk = "pmb/pmblulus.pernyataan.php?PMBID="+PMBID+"&WID="+WID;
    win2 = window.open(lnk, "", "width=500, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakSuratKelulusan(PMBID, WID) {
	lnk = "pmb/pmblulus.suratmaru.php?PMBID="+PMBID+"&WID="+WID;
    win2 = window.open(lnk, "", "width=500, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakKTM(PMBID, WID) {
	lnk = "pmb/pmblulus.KTM.php?PMBID="+PMBID+"&WID="+WID;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function PilihKursi(ID, gel)
  {	_rnd = randomString();
    lnk = "$_SESSION[mnux].pilihkursi.php?id="+ID+"&_rnd="+_rnd+"&gel="+gel;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
  }
  function KonfirmasiProsesNIM(pmbid) {
    lnk = "$_SESSION[mnux].prosesnim.php?pmbid="+pmbid;
    win2 = window.open(lnk, "", "width=600, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

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

function TampilkanHeader($gel) {
  IsiFormulirScript($gel);
  CetakKartuScript();
  $optfrm = GetOption2('pmbformulir', 'Nama', 'Nama', $_SESSION['_pmbFrmID'],
    "KodeID='".KodeID."'", 'PMBFormulirID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_pmbPrg'], "KodeID='".KodeID."'", 'ProgramID');
  $opturut = GetUrutanPMB();
  if($_SESSION['_LevelID'] != 33)
  {	$AmbilLalu = "<input type=button name='btnAmbilPMBLalu' value='Ambil Dari Periode Lalu'
        onClick=\"javascript:AmbilPMBLalu('$gel')\" />"; 
  
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
      <td class=inp>Cari No. Formulir:</td>
      <td class=ul1><input type=text name='_pmbNomer' value='$_SESSION[_pmbNomer]' size=20 maxlength=30 /></td>
      <td class=inp>Urutkan:</td>
      <td class=ul1><select name='_pmbUrut'>$opturut</select></td>
      </tr>
  <tr>
      <td class=inp>Program:</td>
      <td class=ul1><select name='_pmbPrg'>$optprg</select></td>
      <td class=ul1 colspan=2 align=center nowrap>
      <input type=submit name='Submit' value='Submit' />
      <input type=button name='Reset' value='Reset'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=&_pmbPage=0&_pmbNama=&_pmbNomer='\" />
      &raquo&raquo<input type=button name='IsiFrm' value='Isi Formulir' onClick=\"javascript:IsiFormulir(1,'$gel','')\" />&laquo&laquo
      $AmbilLalu
      </td>
  </form>
  </table>";
  }
  // Javascript
  echo <<<ESD
  <script>
  function AmbilPMBLalu(gel) {
    lnk = "$_SESSION[mnux].lalu.php?gel="+gel;
    win2 = window.open(lnk, "", "width=820, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
ESD;
}

function DftrForm($gel) {
  TampilkanHeader($gel);
  
  global $arrUrut;
  $_maxbaris = 10;
  include_once "class/dwolister.class.php";
  // Urutan
  
  if($_SESSION['_LevelID'] != 33)
  {
	  $_urut = $arrUrut[$_SESSION['_pmbUrut']];
	  $__urut = explode('~', $_urut);
	  $urut = "order by ".$__urut[1];
	  // Filter formulir
	  $whr = array();
	  if (!empty($_SESSION['_pmbFrmID'])) $whr[] = "p.PMBFormulirID='$_SESSION[_pmbFrmID]'";
	  if (!empty($_SESSION['_pmbPrg']))   $whr[] = "p.ProgramID = '$_SESSION[_pmbPrg]' ";
	  if (!empty($_SESSION['_pmbNama']))  $whr[] = "p.Nama like '%$_SESSION[_pmbNama]%'";
	  if (!empty($_SESSION['_pmbNomer'])) $whr[] = "p.PMBID like '%$_SESSION[_pmbNomer]%'";
	  
	  $_whr = implode(' and ', $whr);
	  $_whr = (empty($_whr))? '' : 'and '.$_whr;
  }
  else
  {	$_whr = "and p.PMBID = $_SESSION[_Login]";
  }
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_pmbPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $gel = substr($gel, 0, 4);
  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "pmb p 
    left outer join pmbformulir f on p.PMBFormulirID = f.PMBFormulirID
    left outer join prodi _p1 on p.Pilihan1 = _p1.ProdiID
    left outer join prodi _p2 on p.Pilihan2 = _p2.ProdiID
    left outer join prodi _p3 on p.Pilihan3 = _p3.ProdiID
    left outer join program _prg on p.ProgramID = _prg.ProgramID
    left outer join statusawal _sta on p.StatusAwalID = _sta.StatusAwalID
    left outer join aplikan a on a.AplikanID=p.AplikanID
    where p.KodeID = '".KodeID."' 
      and p.PMBPeriodID like '$gel%'
      $_whr
      $urut";
  $lst->fields = ($_SESSION['_LevelID'] == '36' || $_SESSION['_LevelID'] == '32' || $_SESSION['_LevelID'] == '30' || $_SESSION['_LevelID'] == '1' || $_SESSION['_LevelID'] == '40' || $_SESSION['_LevelID'] == '51') ? "p.PMBID, p.Nama, p.Kelamin, p.ProdiID, p.Pilihan1, p.Pilihan2, p.Pilihan3,
    f.Nama as FRM, _p1.Nama as P1, 
  if(f.JumlahPilihan <= 2, _p2.Nama, '-') as P2, 
  if(f.JumlahPilihan <= 2, p.NA, 'Y') as NA2,
  if(f.JumlahPilihan <= 3, _p3.Nama, '-') as P3,
  if(f.JumlahPilihan <= 3, p.NA, 'Y') as NA3,
  if(p.StatusAwalID='S', concat('<font color=blue>',_sta.Nama,'<font>') , _sta.Nama) as STA,
    if(p.MhswID > 0,concat('
    <a href=# onClick=\"javascript:SuratPernyataan(''',p.PMBID,''')\" /><img src=\"img/printer2.gif\" /> Surat Pernyataan</a> <br />
    <a href=# onClick=\"javascript:CetakKTM(''',p.PMBID,''')\" /><img src=\"img/printer2.gif\" /> Cetak KTM</a> <br />
    <a href=# onClick=\"javascript:CetakSuratKelulusan(''',p.PMBID,''')\" /><img src=\"img/printer2.gif\" /> Keterangan Lulus</a> <br />'),
    if((p.MhswID is null or p.MhswID = '') and p.LulusUjian='Y',
    concat('<input type=button value=\"Buat NPM Baru\" onClick=\"javascript:KonfirmasiProsesNIM(''',p.PMBID,''')\" />'),'')) as Cetak,
    if((p.MhswID is null or p.MhswID = '') and p.LulusUjian='N',
    'Belum Lulus Seleksi','<b>&raquo; Lulus Seleksi</b>') as _Keterangan1,
    if((p.MhswID is null or p.MhswID = '') and p.TotalBiaya != p.TotalBayar and p.LulusUjian='Y',
    'Belum Bayar Semester I','') as _Keterangan2,
    _prg.Nama as PRG, p.CetakKartu, p.NA,
  if(f.Wawancara = 'Y' and f.USM = 'Y',
    (
    if (EXISTS(select ru.RuangUSMID from ruangusm ru where ru.PMBID=p.PMBID and KodeID='".KodeID."')
      and (EXISTS(select w.WawancaraUSMID from wawancara w where w.PMBID=p.PMBID and PMBPeriodID='$gel' and KodeID='".KodeID."')),
      'kursiN', 'kursiY')
    ),
    (
      if(f.Wawancara = 'N' and f.USM = 'Y',
      (
      if (EXISTS(select ru.RuangUSMID from ruangusm ru where ru.PMBID=p.PMBID and KodeID='".KodeID."'),
        'kursiN', 'kursiY')
      ),
      (
      if(f.Wawancara = 'Y' and f.USM = 'N',
        (
        if (EXISTS(select w.WawancaraUSMID from wawancara w where w.PMBID=p.PMBID and PMBPeriodID='$gel' and KodeID='".KodeID."'),
          'kursiN', 'kursiY')
        ),'kursiN')
      ))
    )) as _JenisKursi, a.Foto" : "p.PMBID, p.Nama, p.Kelamin, p.ProdiID, p.Pilihan1, p.Pilihan2, p.Pilihan3,
    f.Nama as FRM, _p1.Nama as P1, 
  if(f.JumlahPilihan <= 2, _p2.Nama, '-') as P2, 
  if(f.JumlahPilihan <= 2, p.NA, 'Y') as NA2,
  if(f.JumlahPilihan <= 3, _p3.Nama, '-') as P3,
  if(f.JumlahPilihan <= 3, p.NA, 'Y') as NA3,
  if(p.StatusAwalID='S', concat('<font color=blue>',_sta.Nama,'<font>') , _sta.Nama) as STA,
    if(p.MhswID > 0,concat('Sudah Menjadi Mahasiswa'),
    if((p.MhswID is null or p.MhswID = '') and p.LulusUjian='Y' and p.TotalBiaya=p.TotalBayar and p.TotalBayar>0,'Sudah Bayar Uang Pendaftaran, lakukan pengecekan Bukti Fisik','')) as Cetak,
    if((p.MhswID is null or p.MhswID = '') and p.LulusUjian='N',
    'Belum Lulus Seleksi','<b>&raquo; Lulus Seleksi</b>') as _Keterangan1,
    if((p.MhswID is null or p.MhswID = '') and p.TotalBiaya != p.TotalBayar and p.LulusUjian='Y',
    'Belum Bayar Semester I','') as _Keterangan2,
    _prg.Nama as PRG, p.CetakKartu, p.NA,
  if(f.Wawancara = 'Y' and f.USM = 'Y',
    (
    if (EXISTS(select ru.RuangUSMID from ruangusm ru where ru.PMBID=p.PMBID and KodeID='".KodeID."')
      and (EXISTS(select w.WawancaraUSMID from wawancara w where w.PMBID=p.PMBID and PMBPeriodID='$gel' and KodeID='".KodeID."')),
      'kursiN', 'kursiY')
    ),
    (
      if(f.Wawancara = 'N' and f.USM = 'Y',
      (
      if (EXISTS(select ru.RuangUSMID from ruangusm ru where ru.PMBID=p.PMBID and KodeID='".KodeID."'),
        'kursiN', 'kursiY')
      ),
      (
      if(f.Wawancara = 'Y' and f.USM = 'N',
        (
        if (EXISTS(select w.WawancaraUSMID from wawancara w where w.PMBID=p.PMBID and PMBPeriodID='$gel' and KodeID='".KodeID."'),
          'kursiN', 'kursiY')
        ),'kursiN')
      ))
    )) as _JenisKursi, a.Foto";
  //$lst->startrow = $_SESSION['_pmbPage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->page = $_SESSION['_pmbPage']+0;
  $lst->headerfmt = "<p><table class=box cellspacing=1 align=center width=1000>
    
    <tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>PMB #</th>
    <th class=ttl colspan=2>Nama</th>
    <th class=ttl>Status</th>
    <th class=ttl>Formulir<hr size=1 color=silver />Program</th>
    <th class=ttl>Pilihan1</th>
    <th class=ttl>Pilihan2</th>
    <th class=ttl>Proses</th>
    <th class=ttl>&nbsp;</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=ul1 width=10>
      <a href='#' onClick=\"javascript:IsiFormulir(0,'$gel','=PMBID=')\" />
      <img src='img/edit.png' /></a>
      </td>
    <td class=ul1 width=80>=PMBID=</td>
    <td class=cna=NA=>=Nama= <img src='img/=Kelamin=.bmp' /></td>
    <td class=cna=NA= width=10 align=center><a href='#' onClick=\"PilihKursi('=PMBID=', '$gel')\"><img src='http://spmb.bunghatta.ac.id/foto_file/small_=Foto=' width=60></a></td>
    <td class=cna=NA= width=70>=STA=</td>
    <td class=cna=NA= width=120>
      =FRM=&nbsp;
      <hr size=1 color=silver />
      =PRG=&nbsp;
      </td>
    <td class=cna=NA= width=140>=P1=&nbsp;</td>
    <td class=cna=NA2= width=140>=P2=&nbsp;</td>
    <td class=cna=NA3= width=140>
    		=Cetak=
            =_Keterangan1=
            =_Keterangan2=
    </td>
    <td class=ul1 width=10 align=center>
      <a href='#' onClick=\"javascript:CetakKartu('=PMBID=')\" /><img src='img/printer2.gif' /></a><br />
      <sup>=CetakKartu=&times;</sup>
      </td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>";

  $hal = $lst->TampilkanHalaman($pagefmt, $pageoff);
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

?>
