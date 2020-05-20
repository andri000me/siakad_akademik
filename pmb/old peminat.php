<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 05/01/2008

// *** Parameters ***
$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
$_apliNama = GetSetVar('_apliNama');
$_apliID = GetSetVar('_apliID');
$_apliPage = GetSetVar('_apliPage');
$_curPres = GetSetVar('_curPres');
$_pmbol = $_POST['_pmbol'];
if (!empty($_SESSION['_apliID'])) { $_SESSION['_apliNama']=''; }
if (!empty($_pmbol)) {
	$_pmbol = str_replace("\'","'",$_pmbol);
		$cr_aplikan = GetFields('aplikan',"AplikanID",$_SESSION['_apliID'],'Nama,AplikanID');
		if (empty($cr_aplikan)) {
		$s = $_pmbol;
		$r = _query($s);
		$cr_aplikan = GetFields('aplikan',"AplikanID",$_SESSION['_apliID'],'Nama,AplikanID');
		$_SESSION['_apliNama'] = $cr_aplikan['Nama'];
		}
}

$_SESSION['_pmbol']='';

if ($_REQUEST['sbmt'] == 1) {
  $_SESSION['_apliPre'] = ($_REQUEST['_apliPre'] == 'Y')? 'Y' : 'N';
  $_SESSION['_apliGel'] = ($_REQUEST['_apliGel'] == 'Y')? 'Y' : 'N';
}

// *** Main ***
TampilkanJudul("Aplikan - $gelombang");
if (empty($gelombang)) {
  echo ErrorMsg("Error",
    "Tidak ada gelombang PMB yang aktif.<br />
    Hubungi Kepala PMB untuk mengaktifkan gelombang.");
}
else {
  $gos = (empty($_REQUEST['gos']))? 'DftrAplikan' : $_REQUEST['gos'];
  $gos($gelombang);
}

// *** Functions ***
function TampilkanHeaderAplikan($gelombang) {
  $ck = ($_SESSION['_apliPre'] == 'Y')? 'checked' : '';
  $ckgel = ($_SESSION['_apliGel'] == 'Y')? 'checked' : '';
  $optpresenter = GetOption2('presenter', "concat(PresenterID, ' - ', Nama)", 'PresenterID', $_SESSION['_curPres'], "KodeID='".KodeID."'", 'PresenterID');
  RandomStringScript();
  
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=800>
  <form name='frmAplikan' action='?' method=POST>
  <input type=hidden name='_apliPage' value=1 />
  <input type=hidden name='gos' value='' />
  <input type=hidden name='sbmt' value=1 />
  <tr>
      <td class=inp width=80>Cari Nama:</td>
      <td class=ul1>
        <input type=text name='_apliNama' value='$_SESSION[_apliNama]' size=20 maxlength=50 />
        <input type=submit name='btnCari' value='Cari' />
        <input type=button name='btnReset' value='Reset'
          onClick="window.location='?mnux=$_SESSION[mnux]&gos=&_apliNama=&_apliID=&_pmbol='" />
        
        </td>
		<td class=inp>Kode Aplikan:</td><td class=ul1>
		<input type=text name='_apliID' value='' size=20 maxlength=50 onChange="javascript:rubah(this)" onkeypress="DWRUtil.onReturn(event,return confirm('Import data pendaftaran Calon Mahasiswa online?\\nKlik Ok untuk melanjutkan')" />
		<input id='pmbol' name='_pmbol' type='hidden' value=""> <input type=submit name='btnCari' value='Import' onclick="return confirm('Import data pendaftaran Calon Mahasiswa ONLINE ??\\n\\nKlik Ok untuk melanjutkan...')" /></td>
      </tr>
	  <tr>
	  <td class=ul1 align=right colspan=4>Pilih Menurut Presenter: <select name='_curPres' 
			onChange='this.form.submit()'  />$optpresenter</select></td></tr>
  <tr><td class=ul1> <input type=button name='btnTambahAplikan' value='Tambah Aplikan'
        onClick="javascript:fnEditAplikan('$gelombang', 1, 0 )" /></td>
      <td class=ul1 align=right>Cetak Daftar Aplikan <a href='pmb/cetak.apl.xls.php'><img src='img/printer2.gif' title='Cetak Aplikan Gelombang ini' ></a>
      </td>
      <td class=ul1 align=center nowrap>
      Aplikan gelombang ini saja:
      <input type=checkbox name='_apliGel' value='Y' $ckgel onClick='this.form.submit()' /> 
      </td>
	  <td align=right> <input type=button name='btnDaftarAplikan' value='Cetak Aplikan Hari Ini'
        onClick="javascript:fnCetakAplikan('$gelombang')" />
      </td>
      </tr>
  </form>
  </table>
  
  <script>
  function fnEditAplikan(gel, md, id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].edt.php?gel="+gel+"&md="+md+"&id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=820, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnCetakAplikan(gel) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetakaplikan.php?gel="+gel+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnPMB(id) {
    if (confirm("Anda akan mendaftarkan aplikan ke PMB?")) {
      window.location = "?mnux=$_SESSION[mnux]&gel=$gelombang&gos=fnDaftarkanPMB&BypassMenu=1&id="+id;
    }
  }
  function fnEditHistory(gel, md, id) {
	_rnd = randomString();
	lnk = "$_SESSION[mnux].hst.php?gel="+gel+"&md="+md+"&id="+id+"&_rnd="+_rnd;
	win2 = window.open(lnk, "", "width=620, height=700, scrollbars, status");
	if(win2.opener == null) childWindow.opener = self;
  }
  </script>
     <script type='text/javascript'>
	 <!--
function Konfirm(pesan) {
	var answer = confirm("Apakah")
	if (answer){
		<?php echo 'dijawab'; ?>
	}
	else{
		alert("Thanks for sticking around!")
	}
}
//-->
function createRequestObject()
{
	var ro;
	var browser = navigator.appName;
	if(browser == "Microsoft Internet Explorer")
{
	ro = new ActiveXObject("Microsoft.XMLHTTP");
}
else
{
ro = new XMLHttpRequest();
}
return ro;
}

var xmlhttp = createRequestObject();
	function rubah(pilih)
	{
		var AplID = pilih.value;

		if (!AplID) return;
		xmlhttp.open('get', 'http://sisfo.itp.ac.id/spmb/export.php?aplid='+AplID, true);
		xmlhttp.onreadystatechange = function()
		{
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		document.getElementById("pmbol").value = xmlhttp.responseText;
		return false;
		}

		xmlhttp.send(null);
	}

</script>
ESD;
}

function DftrAplikan($gelombang) {
  TampilkanHeaderAplikan($gelombang);
  
  $_maxbaris = 10;
  include_once "class/dwolister.class.php";
  
  $s = "select DISTINCT(j.JenjangID), j.Nama from prodi p left outer join jenjang j on p.JenjangID=j.JenjangID where p.KodeID='".KodeID."' and p.NA='N' order by JenjangID DESC"; 
  $r = _query($s);
  $n = 0;
  $arrPilihan = array();
  while($w = _fetch_array($r)) 
  {	$n++;
	$arrPilihan[]  = $w['JenjangID'].'~'.$w['Nama'].'~'.$n;
  }
  
  $titlePilihan = ""; $listPilihan = ""; $entryPilihan = "";
  foreach($arrPilihan as $pilih) 
  {	$arrPilih = explode('~', $pilih);
	$titlePilihan .= (empty($titlePilihan))? "Pilihan $arrPilih[1]" : "<hr size=1 color=silver />Pilihan $arrPilih[1]";
	  
	  $ss = "select ProdiID from prodi where KodeID='".KodeID."' and JenjangID='$arrPilih[0]'";
	  $rr = _query($ss);
	  $listPilihan .= "SUBSTR(concat(";
	  $nn = 0;
	  while($ww = _fetch_array($rr))
	  {	if($nn == 0) $listPilihan .= "if(concat(',', a.ProdiID ,',') like '%,$ww[ProdiID],%', ',$ww[ProdiID]', '')";
		else $listPilihan .= ", if(concat(',', a.ProdiID ,',') like '%,$ww[ProdiID],%', ',$ww[ProdiID]', '')";
		$nn++;
	  }
	  $listPilihan.= "), 2) as _Pilihan$arrPilih[2],";
	  
	  $entryPilihan .= (empty($entryPilihan))? "=_Pilihan$arrPilih[2]=&nbsp" : "<hr size=1 color=silver />=_Pilihan$arrPilih[2]=&nbsp";
  }
  
  // Filter formulir
  $whr = array();
  if (!empty($_SESSION['_apliNama'])) $whr[] = "a.Nama like '$_SESSION[_apliNama]%' ";
  if (!empty($_SESSION['_apliID'])) $whr[] = "a.AplikanID like '$_SESSION[_apliID]%' ";
  if ($_SESSION['_apliPre'] == 'Y')   $whr[] = "a.LoginBuat = '$_SESSION[_Login]' ";
  if ($_SESSION['_apliGel'] == 'Y')   $whr[] = "a.PMBPeriodID = '$gelombang' ";
  if (!empty($_SESSION['_curPres'])) $whr[] = "a.PresenterID = '$_SESSION[_curPres]' ";
  
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : ' and ' . $_whr;
  
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_apliPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "aplikan a
    left outer join asalsekolah b on a.AsalSekolah = b.SekolahID
	left outer join perguruantinggi pt on a.AsalSekolah = pt.PerguruanTinggiID
	where a.KodeID = '".KodeID."'
    $_whr
  order by a.PMBPeriodID desc, a.Nama";
  
  
  $lst->fields = "a.PMBPeriodID, a.PresenterID, a.StatusAplikanID, 
    a.AplikanID, a.Nama, a.Kelamin, a.Telepon, a.Handphone, a.Email,
    a.ProdiID,
    if(b.Nama like '_%', b.Nama, 
		if(pt.Nama like '_%', pt.Nama, a.AsalSekolah)) as _NamaSekolah, 
	a.TempatLahir,
	a.StatusAplikanID, a.StatusMundur, 
	a.ProdiID, 
	$listPilihan
	date_format(a.TanggalLahir, '<sup>%d</sup><br />%b<br /><sup>%Y</sup>') as _TanggalLahir,
    if(a.StatusMundur = 'N', 
      \"<img title='Edit Profil Aplikan', src='img/edit.png'></img>\", '&times') as EDIT, 
		if(a.StatusMundur = 'Y',
			'class=wrn',
			if (a.PMBID is NULL or a.PMBID = '',
			'class=ul', 
			'class=nac')) as _kelas
    ";
  $lst->startrow = $_SESSION['_apliPage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->page = $_SESSION['_apliPage']+0;
  
  $lst->headerfmt = "<table class=bsc cellspacing=1 align=center width=800>
    <tr>
    <th class=ttl width=30>#</th>
	<th class=ttl width=40>Status</th>
    <th class=ttl width=160>Nama</th>
    <th class=ttl width=100>Tmpt Lahir</th>
    <th class=ttl width=50>Tgl Lahir</th>
    <th class=ttl width=140>Asal Sekolah</th>
    <th class=ttl width=100>
      $titlePilihan
      </th>
    </tr>";
  
  $lst->detailfmt = "<tr>
    <td class=inp1 align=center>      
      <a href='#' onClick=\"javascript:fnEditAplikan('$gelombang', 0, '=AplikanID=')\" />=EDIT=</a>
      <sub>=AplikanID=</sub>
      </td>
	<td =_kelas=>
	  <a href='#' onClick=\"javascript:fnEditHistory('$gelombang', 0, '=AplikanID=')\" />
	  <img title='Edit Tahap Pendaftaran Aplikan' src='img/edit.png' /></a>
	  <sub>=StatusAplikanID=</sub>
	  </td>
    <td =_kelas=>
      =Nama=
      <div align=right>
	  <sup>=PresenterID=</sup>
      <sup>=PMBPeriodID=</sup>
      <img height=20 width=20 src='img/=Kelamin=.bmp' />
      </div>
      </td>
    <td =_kelas= align=center>=TempatLahir=</td>
    <td =_kelas= align=center>
      =_TanggalLahir=
      </td>
    <td =_kelas=>=_NamaSekolah=&nbsp;</td>
    <td =_kelas= align=center>
      $entryPilihan
    </td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>";

  $hal = $lst->TampilkanHalaman($pagefmt, $pageoff);
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

function fnDaftarkanPMB() {
  $id = sqling($_REQUEST['id']);
  $gel = sqling($_REQUEST['gel']);
  $a = GetFields('aplikan', "AplikanID", $id, '*');
  if (empty($a)) {
    echo ErrorMsg("Error",
    "Data aplikan tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='btnKembali' value='Kembali'
    onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
  }
  else {

	$FormulirID = GetAField('pmbformjual', 'AplikanID', $id, 'PMBFormulirID');
	if(empty($FormulirID))
	{	echo ErrorMsg("Gagal", "Aplikan belum membeli formulir.<br> Data tidak disimpan. <br>
							<input type=button name='Kembali' value='Kembali'
								onClick=\"javascript:history.go(-1)\" />");
	}
	else
	{
	
    $PMBID = GetNextPMBID($gel);
	$arrayPilihan = explode(',', $a['Pilihan1']);
	$arrayPilihan2 = explode(',', $a['Pilihan2']);
	$arrayGabung = array();
	foreach($arrayPilihan as $pilih) {  if(!empty($pilih))  $arrayGabung[] = $pilih; } 
	foreach($arrayPilihan2 as $pilih) {  if(!empty($pilih))  $arrayGabung[] = $pilih; } 
    $s = "insert into pmb
      (PMBID, PMBPeriodID, KodeID, AplikanID,
      Nama, StatusAwalID, Kelamin, Pilihan1, Pilihan2, ProgramID, 
      TempatLahir, TanggalLahir, Agama, PMBFormulirID, PMBFormJualID, 
      Alamat, Kota, RT, RW, KodePos, Propinsi, Negara,
      Telepon, Handphone, Email, 
      PendidikanTerakhir, AsalSekolah, JenisSekolahID,
      AlamatSekolah, KotaSekolah, JurusanSekolah,
      NilaiSekolah, TahunLulus,
      LoginBuat, TanggalBuat)
      values
      ('$PMBID', '$gel', '".KodeID."', '$a[AplikanID]',
      '$a[Nama]', 'B', '$a[Kelamin]', '$arrayGabung[0]', '$arrayGabung[1]', '$a[ProgramID]',
      '$a[TempatLahir]', '$a[TanggalLahir]', '$a[Agama]', '$a[PMBFormulirID]', '$a[PMBFormJualID]',
      '$a[Alamat]', '$a[Kota]', '$a[RT]', '$a[RW]', '$a[KodePos]', '$a[Propinsi]', '$a[Negara]',
      '$a[Telepon]', '$a[Handphone]', '$a[Email]',
      '$a[PendidikanTerakhir]', '$a[AsalSekolah]', '$a[JenisSekolahID]',
      '$a[AlamatSekolah]', '$a[KotaSekolah]', '$a[JurusanSekolah]',
      '$a[NilaiSekolah]', '$a[TahunLulus]',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    // Set Status Aplika menjadi DFT
    SetStatusAplikan('DFT', $a['AplikanID']);
    // Tampilkan pesan //$_pmbNomer
    echo Konfirmasi("Proses Selesai",
    "Proses pendaftaran Aplikan ke PMB telah selesai.<br />
    Nomer PMB: <font size=+1>$PMBID</font>
    <hr size=1 color=silver /> 
    <input type=button name='btnKembali' value='Kembali'
      onClick=\"window.location='?mnux=$_SESSION[mnux]&gos='\" />
    <input type=button name='btnDataPMB' value='$PMBID'
      onClick=\"location='?mnux=pmb/pmbform&gos=&_pmbNomer=$PMBID'\" /> (klik untuk masuk ke data PMB).<br />
      ");
    
    // Reload
    $tmr = 10000;
    $_tmr = $tmr / 1000;
    echo <<<SCR
    <p align=center>
    <font color=red>Jika dalam $_tmr detik tidak ada respons,<br />
    maka sistem akan mengembalikan ke modul presenter.</font> 
    <script>
    window.onload=setTimeout("window.location='?mnux=$_SESSION[mnux]&gos='", $tmr);
    </script>
SCR;
    }
  }
}
?>
