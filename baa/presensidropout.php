<?php
// Author : Irvandy Goutama
// Start  : 29 April 2008
// Email  : irvandygoutama@gmail.com

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$_presensidoNama = GetSetVar('_presensidoNama');
$_presensidoProdi = GetSetVar('_presensidoProdi');
$_presensidoPrg = GetSetVar('_presensidoPrg');
$_presensidoNomer = GetSetVar('_presensidoNomer');
$_presensidoPage = GetSetVar('_presensidoPage');
$_presensidoUrut = GetSetVar('_presensidoUrut', 1);
$_presensidoTglMulai_y = GetSetVar('_presensidoTglMulai_y', date('Y', strtotime( '-30 days')));
$_presensidoTglMulai_m = GetSetVar('_presensidoTglMulai_m', date('m', strtotime( '-30 days')));
$_presensidoTglMulai_d = GetSetVar('_presensidoTglMulai_d', date('d', strtotime( '-30 days')));
$_presensidoTglSelesai_y = GetSetVar('_presensidoTglSelesai_y', date('Y'));
$_presensidoTglSelesai_m = GetSetVar('_presensidoTglSelesai_m', date('m'));
$_presensidoTglSelesai_d = GetSetVar('_presensidoTglSelesai_d', date('d'));
$_SESSION['_presensidoNIMSementara'] = ($_REQUEST['_presensidoNIMSementara'] == 'Y')? 'Y' : 'N';

$arrUrutMhsw = array('NIM~m.MhswID asc, m.Nama', 'NIM (balik)~m.MhswID desc, m.Nama', 'Nama~m.Nama');
RandomStringScript();

// *** Main ***
TampilkanJudul("Pengurusan Drop Out Presensi");
  
  $gos = (empty($_REQUEST['gos']))? 'DropOutPresensi' : $_REQUEST['gos'];
  $gos();

// *** Functions ***

function AmbilUrutanMhswID() {
  global $arrUrutMhsw;
  $a = ''; $i = 0;
  foreach ($arrUrutMhsw as $u) {
    $_u = explode('~', $u);
    $sel = ($i == $_SESSION['_presensidoUrut'])? 'selected' : '';
    $a .= "<option value='$i' $sel>". $_u[0] ."</option>";
    $i++;
  }
  return $a;
}

function TampilkanHeaderFilter() {
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_presensidoPrg'], "KodeID='".KodeID."'", 'ProgramID');
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['_presensidoProdi'], "KodeID='".KodeID."'", 'ProdiID');
  $optstatusmhsw = GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)", 'StatusMhswID', $_SESSION['_presensidoID'], "", 'StatusMhswID');
  $opturut = AmbilUrutanMhswID();
  $opttglmulai = GetDateOption("$_SESSION[_presensidoTglMulai_y]-$_SESSION[_presensidoTglMulai_m]-$_SESSION[_presensidoTglMulai_d]", '_presensidoTglMulai');
  $opttglselesai = GetDateOption("$_SESSION[_presensidoTglSelesai_y]-$_SESSION[_presensidoTglSelesai_m]-$_SESSION[_presensidoTglSelesai_d]", '_presensidoTglSelesai');
  
  $NIMCheck = ($_SESSION['_presensidoNIMSementara'] == 'Y')? 'checked' : '';
  if(!empty($_SESSION['_presensidoProdi']))
	$TampilkanNIMSementara = (GetaField('prodi', "ProdiID='$_SESSION[_presensidoProdi]' and KodeID", KodeID, 'GunakanNIMSementara') == 'Y')?
							"<tr><td class=inp colspan=6>Tampilkan yang NIM Sementara saja? <input type=checkbox name='_presensidoNIMSementara' value='Y' $NIMCheck></tr>" 
								: '';
  
  echo "<table class=box cellspacing=1 align=center>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <input type=hidden name='_presensidoPage' value='0' />
  
  <tr>
      <td class=inp width=80>Thn Akademik:</td>
	  <td class=ul1 width=100><input type=text name='TahunID' value='$_SESSION[TahunID]' size=6 maxlength=10 /></td>
	  <td class=inp>Prodi:</td>
      <td class=ul1><select name='_presensidoProdi'>$optprodi</select></td>
      <td class=inp>Program:</td>
      <td class=ul1><select name='_presensidoPrg'>$optprg</select></td>
	  </tr>
  <tr>
      <td class=inp width=80>Cari NIM:</td>
      <td class=ul1 width=100><input type=text name='_presensidoNomer' value='$_SESSION[_presensidoNomer]' size=20 maxlength=30 /></td>
	  <td class=inp width=80>Cari Nama:</td>
      <td class=ul1 width=100><input type=text name='_presensidoNama' value='$_SESSION[_presensidoNama]' size=20 maxlength=30 /></td>
	  <td class=inp>Sort:</td>
      <td class=ul1><select name='_presensidoUrut'>$opturut</select></td>
	  </tr>
  <tr>
      <td class=inp>Tanggal Mulai:</td>
      <td class=ul1 colspan=2>$opttglmulai</td>
      <td class=inp>Tanggal Selesai:</td>
      <td class=ul1 colspan=2>$opttglselesai</td>
	  </tr>
  $TampilkanNIMSementara
  <tr>
	  <td class=ul1 colspan=6 align=center nowrap>
      <input type=submit name='Submit' value='Submit' />
      <input type=button name='Reset' value='Reset'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=&_presensidoPage=0&_presensidoNama=&_presensidoNomer='\" />
      </td>
  </form>
  </table>";
}

function DropOutPresensi() {
  TampilkanHeaderFilter();
  global $arrUrutMhsw;
  $_maxbaris = 10;
  include_once "class/dwolister.class.php";
  // Urutan
  
  if($_SESSION['_LevelID'] == 1)
  {	$edit = "<a href='#' onClick=\"location='?mnux=$_SESSION[mnux]&gos=StatusMhswEdt&md=0&id==ProsesStatusMhswID='\" />
      <img src='img/edit.png' /></a>";
  }
  
  $_urut = $arrUrutMhsw[$_SESSION['_presensidoUrut']];
  $__urut = explode('~', $_urut);
  $urut = "order by ".$__urut[1];
  
  
  
  // Filter formulir
  $whr = array();
  if (!empty($_SESSION['_presensidoNomer'])) $whr[] = "m.MhswID like '%$_SESSION[_presensidoNomer]%'";
  if (!empty($_SESSION['_presensidoPrg']))   $whr[] = "m.ProgramID = '$_SESSION[_presensidoPrg]' ";
  if (!empty($_SESSION['_presensidoNama']))  $whr[] = "m.Nama like '%$_SESSION[_presensidoNama]%'";
  if (!empty($_SESSION['TahunID'])) $whr[] = "m.TahunID = '$_SESSION[TahunID]'";
  if (!empty($_SESSION['_presensidoProdi']))   
  {	  $whr[] = "m.ProdiID = '$_SESSION[_presensidoProdi]' ";
	  if ($_SESSION['_presensidoNIMSementara'] == 'Y') 
	  {	 $tmp = GetaField('prodi', 'ProdiID', $_SESSION['_presensidoProdi'], 'FormatNIMSementara'); 
		  $tmp = str_replace('~YY~', substr($_SESSION['TahunID'], 2, 2), $tmp);
		  $tmp = str_replace('~YYYY~', substr($_SESSION['TahunID'], 0, 4), $tmp);
		  $tmp = str_replace('~PRG~', $_SESSION['_presensidoPrg'], $tmp);
		  $digit = substr($tmp, strpos($tmp, '~NMR')+4, 1)+0;
		  $tmp = str_replace('~NMR3~', '~NMR~', $tmp); 
		  $tmp = str_replace('~NMR4~', '~NMR~', $tmp);
		  $tmp = str_replace('~NMR5~', '~NMR~', $tmp);  
		  $pos = strpos($tmp, '~NMR~');
		  $pattern = substr($tmp, 0, $pos);
		  $rpattern = substr($tmp, $pos+5);
		  if(!empty($pattern)) $whr[] = "LEFT(m.MhswID, ".strlen($pattern).") = '$pattern'"; 
		  if(!empty($rpattern)) $whr[] =  "RIGHT(m.MhswID, ".strlen($rpattern).") = '$rpattern'";
	  }
  }
  
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : 'and '.$_whr;
  
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_presensidoPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "mhsw m 
	left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join presensimhsw p on p.MhswID = m.MhswID
    left outer join presensi p2 on p2.PresensiID=p.PresensiID
	left outer join prodi _prd on m.ProdiID = _prd.ProdiID
	left outer join program _prg on m.ProgramID = _prg.ProgramID
    left outer join jenispresensi jp on p.JenisPresensiID=jp.JenisPresensiID
	where m.KodeID = '".KodeID."' 
      $_whr
	  and '$_SESSION[_presensidoTglMulai_y]-$_SESSION[_presensidoTglMulai_m]-$_SESSION[_presensidoTglMulai_d]' <= p2.Tanggal
	  and p2.Tanggal <= '$_SESSION[_presensidoTglSelesai_y]-$_SESSION[_presensidoTglSelesai_m]-$_SESSION[_presensidoTglSelesai_d]'
	  and jp.Nilai=0
	  and sm.Keluar='N'
	  group by p.MhswID
	  $urut
	  ";
  $lst->fields = "m.MhswID, m.Nama, m.Kelamin, m.ProgramID, m.ProdiID, p.NA,
					_prg.Nama as _PRG, count(PresensiMhswID) as _CNT, sm.Keluar";
  //$lst->startrow = $_SESSION['_presensidoPage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->page = $_SESSION['_presensidoPage']+0;
  $lst->headerfmt = "<p><table class=box cellspacing=1 align=center width=600>
    
    <tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>NIM</th>
    <th class=ttl colspan=2>Nama</th>
    <th class=ttl>Prodi<hr size=1 color=silver />Program</th>
	<th class=ttl>&sum; Mangkir</th>
	<th class=ttl>Drop?</th>
	
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=ul1 width=10>
      
      </td>
    <td class=ul1 width=80>=MhswID=</td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA= width=10 align=center><img src='img/=Kelamin=.bmp' /></td>
    <td class=cna=NA= width=120 align=center>
      =ProdiID=&nbsp;
      <hr size=1 color=silver />
      =_PRG=&nbsp;
      </td>
	<td class=cna=NA= width=60 align=center>=_CNT=<sub>&times;</sub> &nbsp;<a href='#' onClick=\"PerincianKehadiran('=MhswID=')\"><img src='img/edit.jpg' height=20 title='Perincian Kehadiran'><a></td>
	<td class=cna=NA= width=15 align=center><a href='#' onClick=\"DropMhsw('=MhswID=')\"><img title='Drop Mahasiswa ini' src='img/drop.bmp'></a></td>
	
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>";
  
  //<td class=cna=NA= width=15 align=center><a href='#' onClick=\"PrintSurat('=MhswID=')\"><img title='Surat Keterangan' src='img/printer2.gif'></a></td>

  $hal = $lst->TampilkanHalaman($pagefmt, $pageoff);
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
  DropOutScript();
}

function DropOutScript()
{	echo <<<SCR
	<script>
		function PrintSurat(id)
		{	
			lnk = "$_SESSION[mnux].printsurat.php?MhswID="+id;
			win2 = window.open(lnk, "", "width=500, height=500, scrollbars, status");
			if (win2.opener == null) childWindow.opener = self;
		}
		function DropMhsw(id)
		{	if(confirm("Anda yakin akan menset status mahasiswa dengan id "+id+" menjadi Drop-Out?"))
			{	window.location='?mnux=$_SESSION[mnux]&gos=DropMhsw&MhswID='+id;
			}
		}
		function PerincianKehadiran(id)
		{	window.location='?mnux=$_SESSION[mnux]&gos=Perincian&MhswID='+id;
		}
	</script>
SCR;
}

function DropMhsw()
{	$MhswID = $_REQUEST['MhswID'];
	$_MhswID = GetaField('mhsw', "MhswID='$MhswID' and KodeID", KodeID, 'MhswID');
	if(empty($_MhswID))
	{	die(ErrorMsg('Gagal',
		  "MhswID yang dicari tidak ditemukan.<br />
		  Harap Menghubungi System Administrator.
		  <hr size=1 color=silver />
		  Opsi: <input type=button name='Batal' value='Batal'
			onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
	}
	
	$s = "update mhsw set StatusMhswID='D' where MhswID='$_MhswID'";
	$r = _query($s);
	
	$s = "update khs set StatusMhswID='D' where MhswID='$_MhswID' and TahunID='$_SESSION[TahunID]'";
	$r = _query($s);
	
	DropOutPresensi();
}

function Perincian()
{	$MhswID = $_REQUEST['MhswID'];
	$mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, 'MhswID, Nama, ProdiID, StatusMhswID');
	$statusmhsw = GetFields('statusmhsw', 'StatusMhswID', $mhsw['StatusMhswID'], 'Nama');
	if(empty($mhsw['MhswID']))
	{	die(ErrorMsg('Gagal',
		  "MhswID yang dicari tidak ditemukan.<br />
		  Harap Menghubungi System Administrator.
		  <hr size=1 color=silver />
		  Opsi: <input type=button name='Batal' value='Batal'
			onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
	}
	
	echo "<table class=box cellspacing=1 width=500>
				<tr><td class=inp>Mahasiswa:</td>
					<td class=ul1>$mhsw[Nama]<sup>($mhsw[MhswID])</sup></td>
					<td class=inp>Status:</td>
					<td class=ul1>$mhsw[StatusMhswID] - $statusmhsw[Nama]</td>
			</table>";
	echo "<table class=box cellspacing=1 width=600>
	  ";
	$s = "select KRSID, MKKode, Nama from krs where MhswID='$MhswID' and TahunID='$_SESSION[TahunID]' order by MKKode";
	$r = _query($s);
	while($w = _fetch_array($r))
	{	
		echo "<tr><th class=ttl colspan=6>$w[MKKode] / $w[Nama]</th></tr>";
		$s1 = "select pm.JenisPresensiID, p.*, jp.Nilai, date_format(p.Tanggal, '%d %M %Y') as _Tanggal 
				from presensimhsw pm left outer join presensi p on pm.PresensiID=p.PresensiID
									 left outer join jenispresensi jp on jp.JenisPresensiID=pm.JenisPresensiID
				where pm.KRSID='$w[KRSID]'
					and '$_SESSION[_presensidoTglMulai_y]-$_SESSION[_presensidoTglMulai_m]-$_SESSION[_presensidoTglMulai_d]' <= p.Tanggal
					and p.Tanggal <= '$_SESSION[_presensidoTglSelesai_y]-$_SESSION[_presensidoTglSelesai_m]-$_SESSION[_presensidoTglSelesai_d]'
				order by p.Pertemuan";
		$r1 = _query($s1);
		
		
		while($w1 = _fetch_array($r1))
		{	$jpidcolor = ($w1['Nilai'] == 1)? 'blue' : 'red';
			
			echo "<tr><td class=inp>$w1[Pertemuan]</td>
					  <td class=ul1>$w1[Catatan]<sup>$w1[DosenID]</sup></td>
					  <td class=ul1>$w1[_Tanggal]</td>
					  <td class=ul1><sup>$w1[JamMulai]</sup>&rarr;<sub>$w1[JamSelesai]</sub></td>
					  <td class=ul1 bgcolor='$jpidcolor' align=center><font color='white'>$w1[JenisPresensiID]</font></td>
					</tr>";
		}
	}
	echo "<tr><td colspan=6 align=center><input type=button name='Drop' value='Drop Mahasiswa ini' onClick=\"DropMhsw('$MhswID')\">
										 <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" /></td></tr>
		</table>";
	DropOutScript();
}
?>
