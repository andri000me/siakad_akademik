<?php
// Author : Irvandy Goutama
// Start  : 29 April 2008
// Email  : irvandygoutama@gmail.com

// *** Parameters ***
$_statusmhswNama = GetSetVar('_statusmhswID');
$_statusmhswNama = GetSetVar('_statusmhswNama');
$_statusmhswProdi = GetSetVar('_statusmhswProdi');
$_statusmhswPrg = GetSetVar('_statusmhswPrg');
$_statusmhswNomer = GetSetVar('_statusmhswNomer');
$_statusmhswPage = GetSetVar('_statusmhswPage');
$_statusmhswUrut = GetSetVar('_statusmhswUrut', 1);
$arrUrutMhsw = array('NIM~psm.MhswID asc, m.Nama', 'NIM (balik)~psm.MhswID desc, m.Nama', 'Nama~m.Nama');
RandomStringScript();

// *** Main ***
TampilkanJudul("Pengurusan Status Mahasiswa");
  $gos = (empty($_REQUEST['gos']))? 'StatusMhsw' : $_REQUEST['gos'];
  $gos();

// *** Functions ***

function AmbilUrutanMhswID() {
  global $arrUrutMhsw;
  $a = ''; $i = 0;
  foreach ($arrUrutMhsw as $u) {
    $_u = explode('~', $u);
    $sel = ($i == $_SESSION['_statusmhswUrut'])? 'selected' : '';
    $a .= "<option value='$i' $sel>". $_u[0] ."</option>";
    $i++;
  }
  return $a;
}

function TampilkanHeader() {
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_statusmhswPrg'], "KodeID='".KodeID."'", 'ProgramID');
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['_statusmhswProdi'], "KodeID='".KodeID."'", 'ProdiID');
  $optstatusmhsw = GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)", 'StatusMhswID', $_SESSION['_statusmhswID'], "", 'StatusMhswID');
  $opturut = AmbilUrutanMhswID();
  
  echo "<table class=box cellspacing=1 align=center>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <input type=hidden name='_statusmhswPage' value='0' />
  
  <tr>
      <td class=inp>Cari NIM:</td>
      <td class=ul1><input type=text name='_statusmhswNomer' value='$_SESSION[_statusmhswNomer]' size=20 maxlength=30 /></td>
	  <td class=inp>Cari Nama:</td>
      <td class=ul1><input type=text name='_statusmhswNama' value='$_SESSION[_statusmhswNama]' size=20 maxlength=30 /></td>
	  </tr>
  <tr>
      <td class=inp>Prodi:</td>
      <td class=ul1><select name='_statusmhswProdi'>$optprodi</select></td>
      <td class=inp>Program:</td>
      <td class=ul1><select name='_statusmhswPrg'>$optprg</select></td>
      </tr>
  <tr>
      <td class=inp>Status:</td>
	  <td class=ul1><select name='_statusmhswID'>$optstatusmhsw</select></td>
	  <td class=inp>Sort:</td>
      <td class=ul1><select name='_statusmhswUrut'>$opturut</select></td>
	  </tr>
  <tr>
	  <td class=ul1 colspan=4 align=center nowrap>
      <input type=submit name='Submit' value='Submit' />
      <input type=button name='Reset' value='Reset'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=&_statusmhswPage=0&_statusmhswNama=&_statusmhswNomer='\" />
      &raquo&raquo<input type=button name='IsiFrm' value='Isi Formulir Perubahan' 
		onClick=\"location='?mnux=$_SESSION[mnux]&gos=StatusMhswEdt&md=1'\" />&laquo&laquo
      </td>
  </form>
  </table>";
}

function StatusMhsw() {
  TampilkanHeader();
  
  global $arrUrutMhsw;
  $_maxbaris = 10;
  include_once "class/dwolister.class.php";
  // Urutan
  
  if($_SESSION['_LevelID'] == 1)
  {	$edit = "<a href='#' onClick=\"location='?mnux=$_SESSION[mnux]&gos=StatusMhswEdt&md=0&id==ProsesStatusMhswID='\" />
      <img src='img/edit.png' /></a>";
  }
  
  $_urut = $arrUrutMhsw[$_SESSION['_statusmhswUrut']];
  $__urut = explode('~', $_urut);
  $urut = "order by ".$__urut[1];
  // Filter formulir
  $whr = array();
  if (!empty($_SESSION['_statusmhswNomer'])) $whr[] = "psm.MhswID like '%$_SESSION[_statusmhswNomer]%'";
  if (!empty($_SESSION['_statusmhswProdi']))   $whr[] = "m.ProdiID = '$_SESSION[_statusmhswProdi]' ";
  if (!empty($_SESSION['_statusmhswPrg']))   $whr[] = "m.ProgramID = '$_SESSION[_statusmhswPrg]' ";
  if (!empty($_SESSION['_statusmhswNama']))  $whr[] = "m.Nama like '%$_SESSION[_statusmhswNama]%'";
  if (!empty($_SESSION['_statusmhswID']))  $whr[] = "psm.StatusMhswID = '$_SESSION[_statusmhswID]'";
  
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : 'and '.$_whr;
  
  $pagefmt = "<a href='?mnux=$_SESSION[mnux]&gos=&_statusmhswPage==PAGE='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";

  $brs = "<hr size=1 color=silver />";
  $gantibrs = "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
  $lst = new dwolister;
  $lst->tables = "prosesstatusmhsw psm
	left outer join mhsw m on psm.MhswID = m.MhswID
    left outer join prodi _prd on m.ProdiID = _prd.ProdiID
	left outer join program _prg on m.ProgramID = _prg.ProgramID
    left outer join statusmhsw _stm on psm.StatusMhswLama = _stm.StatusMhswID
	left outer join statusmhsw _stm2 on psm.StatusMhswID = _stm2.StatusMhswID
    where psm.KodeID = '".KodeID."' 
      $_whr
      $urut";
  $lst->fields = "DISTINCT(psm.ProsesStatusMhswID), psm.MhswID, m.Nama, m.Kelamin, m.ProgramID, m.ProdiID, psm.StatusMhswLama, psm.StatusMhswID, psm.NA,
					_prg.Nama as _PRG, 
					_stm.Nama as NamaStatusLama, _stm2.Nama as NamaStatusBaru";
  //$lst->startrow = $_SESSION['_statusmhswPage']+0;
  $lst->maxrow = $_maxbaris;
  $lst->pages = $pagefmt;
  $lst->pageactive = $pageoff;
  $lst->page = $_SESSION['_statusmhswPage']+0;
  $lst->headerfmt = "<p><table class=box cellspacing=1 align=center width=600>
    
    <tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>NIM</th>
    <th class=ttl colspan=2>Nama</th>
    <th class=ttl>Prodi<hr size=1 color=silver />Program</th>
	<th class=ttl>Status Baru<hr size=1 color=silver />Status Lama</th>
    <th class=ttl>&nbsp;</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10>=NOMER=</td>
    <td class=ul1 width=10>
      $edit
      </td>
    <td class=ul1 width=80>=MhswID=</td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA= width=10 align=center><img src='img/=Kelamin=.bmp' /></td>
    <td class=cna=NA= width=120 align=center>
      =ProdiID=&nbsp;
      <hr size=1 color=silver />
      =_PRG=&nbsp;
      </td>
	<td class=cna=NA= width=120 align=center>
	  =NamaStatusBaru=&nbsp;
	  <hr size=1 color=silver />
	  <font color=silver>=NamaStatusLama=</font>&nbsp;</td>
	<td class=cna=NA= width=15 align=center><a href='#' onClick=\"PrintSurat(=ProsesStatusMhswID=)\"><img title='Surat Keterangan' src='img/printer2.gif'></a>
	  </td>
    </tr>".$gantibrs;
  $lst->footerfmt = "</table>
    <script>
		function PrintSurat(id)
		{	lnk = '$_SESSION[mnux].keterangan.php?_psmid='+id;
			win2 = window.open(lnk, '', 'width=600, height=500, scrollbars, status');
			if (win2.opener == null) childWindow.opener = self;
		}
	</script>";

  $hal = $lst->TampilkanHalaman($pagefmt, $pageoff);
  $ttl = $lst->MaxRowCount;
  echo $lst->TampilkanData();
  echo "<p align=center>Hal: $hal <br />(Tot: $ttl)</p>";
}

function StatusMhswEdt()
{ 	
  $md = $_REQUEST['md'] +0;
  // Jika Edit
  if ($md == 0) {
    $w = GetFields('prosesstatusmhsw', "ProsesStatusMhswID", $_REQUEST['id'], '*');
    $jdl = "Edit Proses Status Mhsw";
	$opttgl = GetDateOption($w['Tanggal'], 'Tanggal');
	$NIM = $w['MhswID'];
	$Nama = GetaField('mhsw', 'MhswID', $NIM, 'Nama');
	$HiddenNama = $Nama;
	$optstatusmhsw = "<input type=hidden name='StatusMhswID' value='$w[StatusMhswID]'>".$w['StatusMhswID'].' - '.GetaField('statusmhsw', 'StatusMhswID', $w['StatusMhswID'], 'Nama');
	$tahunakademik = "<input type=hidden name='TahunID' value='$w[TahunID]'>".$w['TahunID'];
  }
  // Jika tambah
  else {
    $w = array();
	$MhswID = (empty($_REQUEST['MhswID']))? ((empty($_SESSION['_statusmhswNomer']))? "" : $_SESSION['_statusmhswNomer']) : $_REQUEST['MhswID'] ;
	$w['TahunID'] = '';
	$cari = GetaField('mhsw', 'MhswID', $MhswID, 'MhswID');
	$w['StatusMhswLama'] = (empty($cari))? "- Tidak ada" : GetaField('mhsw', "MhswID='$MhswID' and KodeID", KodeID, 'StatusMhswID'); 
    $opttgl = GetDateOption(date('Y-m-d'), 'Tanggal');
	$jdl = "Formulir Perubahan Status Mahasiswa";
	$NIM = "<input type=text name='MhswID' value='$MhswID'>
	<input type=button name=Cari' value='Cari NIM' onClick=\"location='?mnux=$_SESSION[mnux]&gos=StatusMhswEdt&md=1&MhswID='+frmstatusmhsw.MhswID.value\">";
	$Nama = (empty($cari))? "- Tidak ditemukan -" : GetaField('mhsw', 'MhswID', $MhswID, 'Nama');
	$HiddenNama = (empty($cari))? "" : GetaField('mhsw', 'MhswID', $MhswID, 'Nama');
	$optstatusmhsw = "<select name='StatusMhswID'>".GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)", 'StatusMhswID', $_SESSION['_statusmhswID'], "", 'StatusMhswID')."</select>";
	$tahunakademik = "<input type=text name='TahunID' value='$w[TahunID]'>";
  }

  
  
  $NamaStatusLama = GetaField('statusmhsw', 'StatusMhswID', $w['StatusMhswLama'], 'Nama');
  CheckFormScript('MhswID,Nama,StatusMhswID,Perihal,Pejabat,Jabatan');
  echo "
  <table class=bsc cellspacing=1 width=500>
  <form name='frmstatusmhsw' action='?' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$_REQUEST[id]' />
  <input type=hidden name='gos' value='Simpan' />
  <tr><td class=inp>NIM:</td>
      <td class=ul>$NIM</td>
      </tr>
  <tr><td class=inp>Nama:</td>
	  <td class=ul1 color=gray><input type=hidden name='Nama' value='$HiddenNama'>$Nama</td>
  <tr><td class=inp>
  </tr>
  <tr><th class=ttl colspan=4>Status Lama:</th></tr>
  <tr><td class=inp>Status Lama:</td>
      <td class=ul><input type=hidden name='StatusMhswLama' value='$w[StatusMhswLama]'>$w[StatusMhswLama] - $NamaStatusLama</td>
  </tr>
  <tr><th class=ttl colspan=4>Ubah Menjadi:</td>
  <tr><td class=inp>Status Baru:</td>
      <td class=ul colspan=3>
        $optstatusmhsw
      </td></tr>
  <tr>
	  <td class=inp>Tahun Akd:<br></td>
      <td class=ul>$tahunakademik</td>
      </tr>
  <tr><td class=inp>No. SK:</td>
      <td class=ul colspan=3>
      <input type=text name='SK' value='$w[SK]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Perihal:</td>
      <td class=ul colspan=3>
      <input type=text name='Perihal' value='$w[Perihal]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Tgl. SK:</td>
      <td class=ul colspan=3>
      $opttgl
      </td></tr>
  <tr><td class=inp>Pejabat:</td>
      <td class=ul colspan=3>
      <input type=text name='Pejabat' value='$w[Pejabat]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Jabatan:</td>
      <td class=ul colspan=3>
      <input type=text name='Jabatan' value='$w[Jabatan]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul colspan=3>
      <textarea name='Keterangan' cols=30 rows=2>$w[Keterangan]</textarea>
      </td></tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />
      </td></tr>
  </form>
  </table>";
}
function Simpan() {
  $md = $_REQUEST['md']+0;
  $id = $_REQUEST['id']+0;
  $MhswID = $_REQUEST['MhswID'];
  $TahunID = $_REQUEST['TahunID'];
  $StatusMhswLama = $_REQUEST['StatusMhswLama'];
  $StatusMhswID = $_REQUEST['StatusMhswID'];
  $SK = sqling($_REQUEST['SK']);
  $Perihal = sqling($_REQUEST['Perihal']);
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Pejabat = sqling($_REQUEST['Pejabat']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  
  // Checking
  if ($StatusMhswLama == $StatusMhswID)
    die(ErrorMsg('Gagal',
      "Anda tidak boleh mengisikan status yg sama dengan yg lama.<br />
      Gunakan formulir ini hanya untuk perubahan status
      <hr size=1 color=silver />
      Opsi: <input type=button name='Batal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
  
  $ada = GetaField('mhsw', 'MhswID', $MhswID, 'MhswID');
  if (empty($ada))
    die(ErrorMsg('Gagal',
      "Nomor NIM $MhswID tidak terdaftar di sistem.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Batal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
  
  if($md == 0)
  {	  
	  $s = "update prosesstatusmhsw
			set TahunID='$TahunID', SK='$SK', Perihal='$Perihal'.
			StatusMhswID = '$StatusMhswID', Pejabat='$Pejabat', Jabatan='$Jabatan',
			Keterangan = '$Keterangan', 
			LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
			where ProsesStatusMhswID = '$id'";
	  $r = _query($s);
  }
  else if($md == 1)
  {
	  $s = "insert into prosesstatusmhsw
		(Tanggal, KodeID, MhswID, TahunID, SK, Perihal,
		StatusMhswLama, StatusMhswID,
		Pejabat, Jabatan, Keterangan,
		LoginBuat, TglBuat, NA)
		values
		('$Tanggal', '".KodeID."', '$MhswID', '$TahunID', '$SK', '$Perihal',
		'$StatusMhswLama', '$StatusMhswID',
		'$Pejabat', '$Jabatan', '$Keterangan',
		'$_SESSION[_Login]', now(), 'N')";
	  $r = _query($s);
  }
  else
  die(ErrorMsg('Error',
      "Mode edit tidak ditemukan.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
  
  $s = "update mhsw
    set StatusMhswID = '$StatusMhswID'
    where MhswID = '$MhswID' ";
  $r = _query($s);
  
  $khsada = GetaField('khs', "MhswID='$MhswID' and TahunID='$TahunID' and KodeID", KodeID, 'KHSID');
  if(!empty($khsada))
  {	$s = "update khs set StatusMhswID='$StatusMhswID' where KHSID='$khsada'";
	$r = _query($s);
  }
  else
  {	$mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID,
      "Nama, ProgramID, ProdiID, BIPOTID, StatusMhswID");
    // Ambil semester terakhir mhsw
    $_sesiakhir = GetaField('khs', "KodeID='".KodeID."' and MhswID", $MhswID,
      "max(Sesi)")+0;
    if ($_sesiakhir > 0) {
      $_khs = GetFields('khs', "KodeID='".KodeID."' and MhswID='$MhswID' and Sesi", 
        $_sesiakhir, '*');
      $Sesi = $_khs['Sesi']+1;
      $MaxSKS = GetaField('maxsks', "KodeID='".KodeID."' 
        and DariIP <= $_khs[IPS] and $_khs[IPS] <= SampaiIP
        and ProdiID", $mhsw['ProdiID'], 'SKS')+0;
    }
    else {
      $Sesi = 1;
      $MaxSKS = GetaField('prodi', "KodeID='".KodeID."' and ProdiID",
        $mhsw['ProdiID'], 'DefSKS');
    }
  
    // Simpan
    $s = "insert into khs
      (TahunID, KodeID, ProgramID, ProdiID, 
      MhswID, StatusMhswID,
      Sesi, IP, MaxSKS,
      LoginBuat, TanggalBuat, NA)
      values
      ('$TahunID', '".KodeID."', '$mhsw[ProgramID]', '$mhsw[ProdiID]',
      '$MhswID', '$StatusMhswID',
      '$Sesi', 0, $MaxSKS,
      '$_SESSION[_Login]', now(), 'N')";
    $r = _query($s);
  }
  
  StatusMhsw();
}

?>
