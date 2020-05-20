<?php

// *** Parameters ***
$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
$_pmbLoginNama = GetSetVar('_pmbLoginNama');
$_pmbLoginAplikanID = GetSetVar('_pmbLoginAplikanID');
$_pmbLoginPage = GetSetVar('_pmbLoginPage');
$_pmbLoginUrut = GetSetVar('_pmbLoginUrut', 1);
$arrUrut = array('Nomer Aplikan~a.AplikanID asc, a.Nama', 'Nomer Aplikan (balik)~a.AplikanID desc, a.Nama', 'Nama~a.Nama');
RandomStringScript();

// *** Main ***
TampilkanJudul("Setup Login  dan Password Aplikan - $gelombang");
if (empty($gelombang)) {
  echo ErrorMsg("Error",
    "Tidak ada gelombang PMB yang aktif.<br />
    Harap setup gelombang dulu.");
}
else {
  $gos = (empty($_REQUEST['gos']))? 'DftrLoginForm' : $_REQUEST['gos'];
  $gos($gelombang);
}

// *** Functions ***

function DftrLoginForm($gel)
{	$l = DftrLoginList($gel);
	$i = EditEntry($gel);
	echo "<p><table class=bsc cellspacing=1 align=center width=1000>
	<tr><td class=ul valign=top width=600>
		$l
		</td>
		<td class=ul1 valign=top>
		$i
		</td>
		</tr>
	</table></p>";
}

function GetUrutanAplikan() {
  global $arrUrut;
  $a = ''; $i = 0;
  foreach ($arrUrut as $u) {
    $_u = explode('~', $u);
    $sel = ($i == $_SESSION['_pmbLoginUrut'])? 'selected' : '';
    $a .= "<option value='$i' $sel>". $_u[0] ."</option>";
    $i++;
  }
  return $a;
}

function TampilkanHeader($gel) {
  $opturut = GetUrutanAplikan();
  $a = "<table class=box cellspacing=1 width=100%>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <input type=hidden name='_pmbLoginPage' value='0' />
  
  <tr>
      <td class=inp>Cari Nama:</td>
      <td class=ul1><input type=text name='_pmbLoginNama' value='$_SESSION[_pmbLoginNama]' size=20 maxlength=30 /></td>
      <td class=inp width=100>Urutkan:</td>
      <td class=ul1><select name='_pmbLoginUrut'>$opturut</select></td>
      </tr>
  <tr>
      <td class=inp></td>
      <td class=ul1></td>
      <td class=ul1 colspan=2 align=center nowrap>
      <input type=submit name='Submit' value='Submit' />
      <input type=button name='Reset' value='Reset'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=&_pmbLoginPage=0&_pmbLoginNama=&_pmbLoginNomer='\" />
      </td>
  </form>
  </table>";
  return $a;
}

function DftrLoginList($gel) {
  $a = TampilkanHeader($gel);
  
  global $arrUrut;
  $_maxbaris = 10;
  include_once "class/dwolister.class.php";
  // Urutan
  $_urut = $arrUrut[$_SESSION['_pmbLoginUrut']];
  $__urut = explode('~', $_urut);
  $urut = "order by ".$__urut[1];
  // Filter formulir
  $whr = array();
  if (!empty($_SESSION['_pmbLoginNama']))  $whr[] = "a.Nama like '%$_SESSION[_pmbLoginNama]%'";
  
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : 'and '.$_whr;
  
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_pmbLoginPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "aplikan a 
    where a.KodeID = '".KodeID."' 
      and a.PMBPeriodID='$gel'
      $_whr
      $urut";
  $lst->fields = "a.AplikanID, a.Nama, a.JmlReset, if(a.PasswordBaru='Y', '<font color=red>Default</font>', '<font color=blue>Set</font>') as PASSSTATUS, a.NA, if(a.AplikanID='$_SESSION[_pmbLoginAplikanID]', 'inp1', 'ul1') as CLASS";
  //$lst->startrow = $_SESSION['_pmbPage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->page = $_SESSION['_pmbLoginPage']+0;
  $lst->headerfmt = "<table class=box cellspacing=1 width=100%>
    
    <tr>
    <th class=ttl>#</th>
    <th class=ttl>PMB #</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Status</th>
    <th class=ttl>Reset</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class==CLASS= width=80>=AplikanID=</td>
    <td class==CLASS=><a href='?mnux=$_SESSION[mnux]&gos=&_pmbLoginAplikanID==AplikanID='>=Nama=</td>
    <td class==CLASS= width=50 align=center>=PASSSTATUS=</td>
	<td class==CLASS= width=50 align=center>=JmlReset=x</td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>";

  $hal = $lst->TampilkanHalaman($pagefmt, $pageoff);
  $ttl = $lst->MaxRowCount;
  $a .= $lst->TampilkanData();
  $a .= "<p align=left>Hal: $hal <br />(Tot: $ttl)</p>";
	return $a;
}

function EditEntry($gel)
{	$aplikan = GetFields('aplikan', "AplikanID='$_SESSION[_pmbLoginAplikanID]' and KodeID", KodeID, '*');
	
	if(empty($aplikan))
	{	$aplikan['AplikanID'] = "<font color=red>Auto-generated</font>";
		$aplikanhidden = '';
		$aplikan['TanggalLahir'] = (date('Y')-18).'-'.date('m-d');
		$ResetPassword = '';
	}
	else
	{	$aplikanhidden = "<input type=hidden name='AplikanID' value='$aplikan[AplikanID]'>";
		$ResetPassword = "<input type=button name='DefPass' value='Reset Password' 
								onClick=\"location='?mnux=$_SESSION[mnux]&gos=ResetPassword'\"/>"; 
	}
	$opttgllahir = GetDateOption($aplikan['TanggalLahir'], 'TanggalLahir');
	
	CheckFormScript("Nama");
	$a = "<table class=box cellspacing=1  width=100%>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td colspan=2><input type=button name='Reset' value='&raquo&raquo Form Baru &laquo&laquo'
			onClick=\"location='?mnux=$_SESSION[mnux]&gos=&_pmbLoginAplikanID='\" /></td>
	  <td rowspan=3><a href='#' onClick=\"CetakKartu('$aplikan[AplikanID]', '$gel')\"><img src='img/printer2.gif' width=20></a></td></tr>
  <tr>
      <td class=inp>No. PMB:</td>
      <td class=ul1>$aplikanhidden$aplikan[AplikanID]</td>
  </tr>
  <tr>
      <td class=inp>Nama Peserta:</td>
      <td class=ul1><input type=text name='Nama' value='$aplikan[Nama]' size=20 maxlength=50></td>
  </tr>
  <tr>
      <td class=inp>Tanggal Lahir:</td>
      <td class=ul1 colspan=2>$opttgllahir</td>
  </tr>
  <tr>
      <td class=ul1 colspan=3 align=center nowrap>
		  <input type=submit name='Submit' value='Simpan' />
		  $ResetPassword
	  </td>
  </tr>
  </form>
  </table>
  <script>
	function CetakKartu(id, gel) {
    lnk = '$_SESSION[mnux].kartu.php?gel='+gel+'&id='+id;
	win2 = window.open(lnk, '', 'width=800, height=700, scrollbars, status, resizable');
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>";
  return $a;
}

function Simpan($gel)
{	
	$AplikanID = $_REQUEST['AplikanID'];
	$Nama = $_REQUEST['Nama'];
	$TanggalLahir = "$_REQUEST[TanggalLahir_y]-$_REQUEST[TanggalLahir_m]-$_REQUEST[TanggalLahir_d]";
	
	if(empty($AplikanID) or $AplikanID=='')
	{
		$aplikanid = GetNextAplikanID($gel);
	
		$s = "insert into aplikan set AplikanID='$aplikanid', Nama='$Nama', TanggalLahir='$TanggalLahir', Login='$aplikanid', Password=PASSWORD('$TanggalLahir'), 
								KodeID='".KodeID."', PMBPeriodID='$gel', 
								LoginBuat='$_SESSION[_Login]', TanggalBuat=now(), LoginEdit='$_SESSION[_Login]', TanggalEdit=now()";
		$r = _query($s);
		$page = 0;
	}
	else
	{	$aplikanid = $AplikanID;
		$s = "update aplikan set Nama='$Nama', TanggalLahir='$TanggalLahir',
								LoginEdit='$_SESSION[_Login]', TanggalEdit=now() where AplikanID='$aplikanid' and KodeID='".KodeID."'";
		$r = _query($s);
		
		$ttl2 = GetaField('aplikan', "PMBPeriodID='$gel' and LEFT(AplikanID, 5)=LEFT('$AplikanID', 5) and AplikanID <= '$AplikanID' and KodeID", KodeID, 'count(AplikanID)');
		$ttl = GetaField('pmb', "PMBPeriodID='$gel' and LEFT(AplikanID, 5)=LEFT('$AplikanID', 5) and KodeID", KodeID, 'count(AplikanID)');
		$page = ceil(($ttl-$ttl2)/10);
	}
	BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=&_pmbLoginAplikanID=$aplikanid&_pmbLoginPage=$page", 10);
}

function ResetPassword($gel)
{	if(empty($_SESSION['_pmbLoginAplikanID']))
	{	echo ErrorMsg("Gagal", "Tidak ada Login Aplikan yang dipilih untuk di-reset.");	
	}
	else
	{	$TanggalLahir = GetaField('aplikan', "AplikanID='$_SESSION[_pmbLoginAplikanID]' and KodeID", KodeID, "TanggalLahir");
		
		$s = "update aplikan set Password=LEFT(PASSWORD('$TanggalLahir'), 10), PasswordBaru='Y', Hint='', HintAnswer='', JmlReset = JmlReset+1 where AplikanID='$_SESSION[_pmbLoginAplikanID]' and KodeID='".KodeID."'";
		$r = _query($s);
		echo Konfirmasi("Berhasil", 
		"Reset berhasil.<br />
		Tampilan akan kembali ke semula dalam 3 detik.
		<hr size=1 color=silver />
		Atau klik: <a href='?mnux=$_SESSION[mnux]'>[ Kembali ]</a>");
		echo "<script type='text/javascript'>window.onload=setTimeout('window.location=\"?mnux=$_SESSION[mnux]\"', 3000);</script>";
	}
}

function GetNextAplikanID($gel) {
  $gelombang = GetFields('pmbperiod', "PMBPeriodID='$gel' and KodeID", KodeID, "FormatNoAplikan, DigitNoAplikan");
  // Buat nomer baru
  $nomer = str_pad('', $gelombang['DigitNoAplikan'], '_', STR_PAD_LEFT);
  $nomer = $gelombang['FormatNoAplikan'].$nomer;
  $akhir = GetaField('aplikan',
    "AplikanID like '$nomer' and KodeID", KodeID, "max(AplikanID)");
  $nmr = str_replace($gelombang['FormatNoAplikan'], '', $akhir);
  $nmr++;
  $baru = str_pad($nmr, $gelombang['DigitNoAplikan'], '0', STR_PAD_LEFT);
  $baru = $gelombang['FormatNoAplikan'].$baru;
  return $baru;
}
?>
