<?php
include_once "dosen.hdr.php";
include_once "class/dwolister.class.php";

// Tidak boleh akses ke folder langsung  
chmod('file/bahanajar/index.html',0444);

// *** Functions ***
function CariDosen() {
  TampilkanFilterDosen('dosen', 1);
  //FormLast();
  //echo "<p><span id=lastsp class=inp1><a href='javascript:sib()'>Dosen ID Terakhir : </a></span><span class=inp1 id=inp>$_SESSION[ll]&nbsp;</span></p>";
  //echo "<a href=?mnux=$_SESSION[mnux].cetak>Cetak</a>";
  DaftarDosen($_SESSION['mnux'], "gos=DsnEdt&md=0&dsnid==Login=", "NIDN,Nama,Gelar,Homebase,Telephone");
}

function GetLastIDDosen($last){
	$s = "select max(d.Login) as Login from dosen d
          left outer join statusdosen sd on sd.StatusDosenID=d.StatusDosenID
        where sd.StatusDosenID = '$last'";
	$r = _query($s);
	$w = _fetch_array($r);
	$_SESSION['ll'] = $w['Login'];
	return $w['Login'];
}

function FormLast(){
  $optstts = GetOption2('statusdosen', "concat(StatusDosenID, ' - ', Nama)", "StatusDosenID", $_SESSION['last'], '', "StatusDosenID");
  $LAST = GetLastIDDosen($_SESSION['last']);
  echo "<script type=\"text/javascript\">
        function sib(){
          $('#last').fadeIn('slow');
          $('#inp').hide();
          $('#lastsp').hide();
        }
        </script>";
  echo "<div id='last' style='display:none'>
        <form action='?' method='POST'>
        <input type=hidden name='mnux' value='$_SESSION[mnux]'>
        <table class=box cellpadding=4 cellspacing=1>
        <tr><th class=ttl>Status Dosen</th><th class=ttl>DosenID Terakhir</th></tr>
        <tr><td class=ul><select name=last onchange='this.form.submit()'>$optstts</select></td><td class=ul align=center>$LAST</td></tr>
        </table></form></div>";
}

function DsnAdd($mnux='', $gos='DsnAddSav', $sub='') {
  if (empty($mnux)) $mnux = $_SESSION['mnux'];
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    if ($_SESSION['_LevelID']==100){
	$w = GetFields('dosen', 'Login', $_SESSION['_Login'], '*');
	}
	else{
	$w = GetFields('dosen', 'Login', $_REQUEST['dsnid'], '*');
	}
    $jdl = "Edit Data Pribadi";
    $_strdsnid = "<input type=hidden name='dsnid' value='$w[Login]'><b>$w[Login]</b>";
  }
  else {
    $w = array();
    $w['Login'] = '';
	$w['NIP'] = '';
	$w['NUPN'] = '';
	$w['NIK'] = '';
	$w['NIRA'] = '';
    $w['NIDN'] = '';
    $w['Nama'] = '';
    $w['Gelar'] = '';
    $w['Telephone'] = '';
    $w['Handphone'] = '';
    $w['Email'] = '';
    $w['ProdiID'] = '';
    $w['TempatLahir'] = '';
    $w['TanggalLahir'] = date('Y-m-d');
    $w['KelaminID'] = 'P';
    $w['AgamaID'] = 'I';
    $w['NA'] = 'N';
    $Homebase = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', '', '', 'ProdiID');
    $hm = "<tr><td class=inp>Prodi Homebase</td><td class=ul><select name='Homebase'>$Homebase</select></td></tr>";
    $jdl = "Tambah Dosen";
    $_strdsnid = "<input type=text name='dsnid' size=20 maxlength=20>";
  }
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  $optprodi = GetCheckboxes("prodi", 
	"ProdiID",
    "concat(ProdiID, ' - ', Nama) as NM", 
	"NM", 
	$w['ProdiID'], 
	'.');
  $TglLahir = GetDateOption($w['TanggalLahir'], 'TglLahir');
  $optagm = GetOption2('agama', "concat(Agama, ' - ', Nama)", 'Agama', $w['AgamaID'], '', 'Agama');
  $radkel = GetRadio("select Nama, Kelamin from kelamin order by Nama", "Kelamin", "Nama", "Kelamin", $w['KelaminID'], ", ");    
  CheckFormScript("Nama,Gelar");
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <input type=hidden name='sub' value='$sub'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Login/NIP *</td>
      <td class=ul>$_strdsnid</td></tr>";
	  if ($_SESSION['_LevelID']<>100) {
	  echo "<p align=center><font color=red>*) Kosongkan untuk auto-generated</font></p>";
	  }
  echo "<tr><td class=inp>NIDN</td>";
  if ($_SESSION['_LevelID']==100) {
     echo "<td class=ul><input type=text name='NIDN' value='$w[NIDN]'  size=20 maxlength=50>";
	 }
	 else {
	 echo "<td class=ul><input type=text name='NIDN' value='$w[NIDN]' size=20 maxlength=50>";
	 }
     echo "Nomer Induk Dosen Nasional</td></tr>
	 <tr><td class=inp>NIP</td>
      <td class=ul><input type=text name='NIP' value='$w[NIP]' size=20 maxlength=50>
      </td></tr>
  <tr><td class=inp>NIK</td>
      <td class=ul><input type=text name='NIK' value='$w[NIK]' size=20 maxlength=50>
      </td></tr>
  <tr><td class=inp>NUPN</td>
      <td class=ul><input type=text name='NUPN' value='$w[NUPN]' size=20 maxlength=50>
      </td></tr>
  <tr><td class=inp>NIRA</td>
      <td class=ul><input type=text name='NIRA' value='$w[NIRA]' size=20 maxlength=50>
      </td></tr>
  <tr><td class=inp>Nama Dosen</td>
      <td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Gelar Depan</td>
      <td class=ul><input type=text name='Gelar1' value='$w[Gelar1]' size=$0 maxlength=100></td></tr>
  <tr><td class=inp>Gelar Belakang</td>
      <td class=ul><input type=text name='Gelar' value='$w[Gelar]' size=$0 maxlength=100></td></tr>
  <tr><td class=inp>Tempat Lahir</td>
      <td class=ul><input type=text name='TempatLahir' value='$w[TempatLahir]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tanggal Lahir</td>
      <td class=ul>$TglLahir</td></tr>
  <tr><td class=inp>Jenis Kelamin</td>
      <td class=ul>$radkel</td></tr>
  <tr><td class=inp>Agama</td>
      <td class=ul><select name='AgamaID'>$optagm</select></td></tr>
  <tr><td class=inp># Telepon</td>
      <td class=ul><input type=text name='Telephone' value='$w[Telephone]' size=40 maxlength=50></td></tr>
  <tr><td class=inp># Ponsel</td>
       <td class=ul><input type=text name='Handphone' value='$w[Handphone]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>E-mail</td><td class=ul><input type=text name='Email' value='$w[Email]' size=40 maxlength=50></td></tr>
  $hm";
  if($_SESSION['_LevelID']<>100) {
  echo "<tr><td class=inp>Program Studi</td>
      <td class=ul>$optprodi</td></tr>
	  <tr><td class=inp>Tidak aktif?</td>
      <td class=ul><input type=checkbox name='NA' value='Y' $_na></td></tr>";
	}
	echo "
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&gos=&$snm=$sid'\"></td></tr>
  </form></table></p>";
}
function SetPasswordDosen($tgl) {
  $tmp = explode('-', $tgl);
  
  $tanggal = '';
  $bulan   = '';
  $tahun   = '';
  
  $tanggal = $tmp[2];
  $bulan   = $tmp[1];
  $tahun   = $tmp[0];
  
  $thn2digit = substr($tahun, -2);
  
  $pass = "$tanggal" . "$bulan" . "$thn2digit";
  
  return $pass;
}

function DsnAddSav($gos='DsnEdt') {
  $md = $_REQUEST['md']+0;
  $Login = sqling($_REQUEST['dsnid']);
  if(empty($Login))
  {	$Login = GetDosenID(date('Y'));
  }
  $Homebase = $_REQUEST['Homebase'];
  $NIDN = sqling($_REQUEST['NIDN']);
  $NIP = sqling($_REQUEST['NIP']);
  $NIRA = sqling($_REQUEST['NIRA']);
  $NUPN = sqling($_REQUEST['NUPN']);
  $NIK = sqling($_REQUEST['NIK']);
  $Nama = sqling($_REQUEST['Nama']);
  $TempatLahir = sqling($_REQUEST['TempatLahir']);
  $TanggalLahir = "$_REQUEST[TglLahir_y]-$_REQUEST[TglLahir_m]-$_REQUEST[TglLahir_d]";
  $Gelar = sqling($_REQUEST['Gelar']);
  $Gelar1 = sqling($_REQUEST['Gelar1']);
  $Telephone = sqling($_REQUEST['Telephone']);
  $Handphone = sqling($_REQUEST['Handphone']);
  $Email = sqling($_REQUEST['Email']);
  $KelaminID = $_REQUEST['Kelamin'];
  $AgamaID = $_REQUEST['AgamaID'];
  $ProdiID = $_REQUEST['ProdiID'];
  $_ProdiID = (empty($ProdiID))? '' : '.'.implode('.', $ProdiID).'.';
  
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update dosen set NIDN='$NIDN', NIP='$NIP', NIK='$NIK',NIRA='$NIRA',NUPN='$NUPN', Nama='$Nama',
      TempatLahir='$TempatLahir', TanggalLahir='$TanggalLahir', Gelar1='$Gelar1',
      Gelar='$Gelar', Telephone='$Telephone', Handphone='$Handphone',
      KelaminID='$KelaminID', AgamaID='$AgamaID',
      Email='$Email', ProdiID='$_ProdiID', NA='$NA'
      where Login='$Login' ";
    $r = _query($s);
    BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=$gos&dsnid=$Login", 100);
  }
  else {
    $ada = GetFields('dosen', "Login", $Login, '*');
    if (empty($ada)) {
      $pass = setPasswordDosen($TanggalLahir);
      $s = "insert into dosen (Login, NIDN, Nama, TempatLahir, TanggalLahir,
        AgamaID, KelaminID, Password, Homebase,
        KodeID, Gelar1, Gelar, Telephone, Handphone,
        Email, ProdiID, NA, NUPN,NIK,NIP,NIRA, LoginBuat, TanggalBuat)
        values ('$Login', '$NIDN', '$Nama', '$TempatLahir', '$TanggalLahir',
        '$AgamaID', '$KelaminID', PASSWORD('$pass'), '$Homebase',
        '$_SESSION[KodeID]', '$Gelar1','$Gelar', '$Telephone', '$Handphone',
        '$Email', '$_ProdiID', '$NA', '$NUPN','$NIK','$NIP','$NIRA','$_SESSION[_login]',now())";
      $r = _query($s);
      $_SESSION['dsnid'] = $_REQUEST['dsnid'];
      $_SESSION['dsnsub'] = "DsnEdtPribadi";
      $_SESSION['dsncr'] = $_REQUEST['dsnid'];
      $_SESSION['dsnkeycr'] = "Login";
      BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=$gos&dsnid=$Login", 100);
    }
    else echo ErrorMsg("Gagal", "Data dosen tidak dapat disimpan karena NIP: <b>$Login</b> sudah dipakai oleh:
      <b>$ada[Nama]</b>.<br>
      Gunakan NIP lain.<hr size=1 color=silver>
      Pilihan: <a href='?mnux=$_SESSION[mnux]&gos=DsnAdd&md=1'>Tambah Dosen</a> |
      <a href='?mnux=$_SESSION[mnux]&gos='>Kembali ke Daftar Dosen</a>");
  }
}
// *** Edit Dosen ***
function TampilkanHeaderDosenEdit($w) {
  global $arrID;
  echo "<p><table class=box  width=600><tr><td><table class=box cellspacing=1 cellpadding=4 width=400>
  <tr><td class=inp>NIP/Login:</td>
      <td class=ul1><b>$w[Login]</b></td></tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul1><b> $w[Gelar1] $w[Nama]</b>, $w[Gelar]</td></tr>
  <tr><td class=inp>Pilihan:</td>
      <td class=ul1>";
	  if ($_SESSION['_LevelID']<>100) {
      echo "<input type=button name='Kembali' value='Kembali ke Daftar Dosen'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=&sub='\" /><br>";
		}
     echo"<input type=button name='Refresh' value='Refresh'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=0&dsnid=$_SESSION[dsnid]'\" />
		<input type=button name='GantiFoto' value='Ganti Foto'
        onClick=\"location='?mhswbck=$_SESSION[mnux]&mnux=master/dosen.foto&dosenid=$w[Login]'\" />
      </td></tr>
  </table></td><td>";
  if (empty($w[Foto])) {
  echo "<img src=img/gambar.gif width=100>";
  }
  else {
  echo "<img src='foto/dosen/kecil/$w[Foto]' width=100>";
  }
  echo "</td></tr></table></p>";
}
function TampilkanMenuEditDosen($w) {
  $arrMenuDosen = array('Data Pribadi->DsnEdt->DsnEdtPribadi',
    'Alamat->DsnEdt->DsnAlmt',
    'Akademik->DsnEdt->DsnEdtAkademik',
    ($_SESSION['_LevelID']==100)? '':'Jabatan->DsnEdt->DsnEdtJabatan',
    'Pengajaran->DsnEdt->DsnEdtPengajaran',
    'Penelitian->DsnEdt->TampilTelitiDsn',
	'Bahan Ajar->DsnEdt->BahanAjar',
    //'Pengabdian->DsnEdt->DsnEdtPengabdian',
    'Pendidikan->DsnEdt->TampilPendidikan',
	'Pekerjaan->DsnEdt->TampilKerjaDsn');
  
  echo "<p align=center><table class=menu cellspacing=1 cellpadding=4 align=center><tr>";
  $_SESSION['dsnsub'] = (empty($_SESSION['dsnsub']))? 'DsnEdtPribadi' : $_SESSION['dsnsub'];
  for ($i = 0; $i < sizeof($arrMenuDosen); $i++) {
    $mn = explode('->', $arrMenuDosen[$i]);
    $c = ($mn[2] == $_SESSION['dsnsub'])? 'class=menuaktif' : 'class=menuitem';
    echo "<td $c><a href='?mnux=$_SESSION[mnux]&gos=$mn[1]&dsnid=$w[Login]&dsnsub=$mn[2]'>$mn[0]</a></td>";
  }
  echo "</tr></table></p>";
}
function DsnEdt() {
  $w = GetFields('dosen', "Login", $_SESSION['dsnid'], '*');
  TampilkanHeaderDosenEdit($w);
  TampilkanMenuEditDosen($w);
  if (!empty($_SESSION['dsnsub'])) $_SESSION['dsnsub']();
}
function DsnEdtPribadi() {
  $_REQUEST['md']+0;
  DsnAdd($_SESSION['mnux'], 'DsnEdtPribadiSav', 'DsnEdtPribadi');
}
function DsnEdtPribadiSav() {
  DsnAddSav('DsnEdt');
}

function DsnEdtAkademik() {
  if (!empty($_REQUEST['dsnsub1'])) {
    $_REQUEST['dsnsub1']();
  }
  $w = GetFields('dosen', 'Login', $_SESSION['dsnid'], '*');
  $TglBekerja = GetDateOption($w['TglBekerja'], 'TglBekerja');
  $StatusDosen = GetOption2('statusdosen', "concat(StatusDosenID, ' - ', Nama)", 'StatusDosenID', $w['StatusDosenID'], '', 'StatusDosenID');
  $StatusKerja = GetOption2('statuskerja', "concat(StatusKerjaID, ' - ', Nama)", 'StatusKerjaID', $w['StatusKerjaID'], '', 'StatusKerjaID');
  $Homebase = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['Homebase'], '', 'ProdiID');
  $_ProdiID = GetRadioProdi($w['ProdiID'], 'ProdiID');
  $Jenjang = GetOption2('jenjang', "concat(JenjangID, ' - ', Nama)", 'JenjangID', $w['JenjangID'], '', 'JenjangID');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='DsnEdtAkademikSav'>
  <input type=hidden name='dsnid' value='$w[Login]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>Profile</b></td></tr>
  <tr><td class=inp>Mulai Bekerja</td>
      <td class=ul>$TglBekerja</td></tr>
  <tr><td class=inp>Status Dosen</td>
      <td class=ul><select name='StatusDosenID'>$StatusDosen</select></td></tr>
  <tr><td class=inp>Status Kerja</td>
      <td class=ul><select name='StatusKerjaID'>$StatusKerja</select></td></tr>";
	  if ($_SESSION['_LevelID']<>100) {
  echo "<tr><td class=inp>Prodi Homebase</td>
      <td class=ul><select name='Homebase'>$Homebase</select></td></tr>
  <tr><td class=inp>Mengajar di Prodi</td>
      <td class=ul>$_ProdiID</td></tr>";
	  }
  echo "<tr><td class=inp>Kode Instansi Induk</td>
      <td class=ul><input type=text name='InstitusiInduk' value='$w[InstitusiInduk]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Lulus Perg. Tinggi</td>
      <td class=ul><input type=text name='LulusanPT' value='$w[LulusanPT]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Jenjang Pendidikan Tertinggi</td>
      <td class=ul><select name='JenjangID'>$Jenjang</select></td></tr>
  <tr><td class=inp>Keilmuan</td>
      <td class=ul><input type=text name='Keilmuan' value='$w[Keilmuan]' size=40 maxlength=100></td></tr>  
  
  <tr><td colspan=2 align=center><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table>";
}
function GetOptGol($prodi='', $gol='', $kat='') {
  $a = '<option>-</option>';
  if (!empty($prodi)) {
    $s = "select GolonganID, KategoriID, Pangkat
      from golongan 
    where ProdiID='$prodi'
      order by GolonganID, KategoriID";
    $r = _query($s);
    while ($w = _fetch_array($r)) {
      $sel = (($w['GolonganID'] == $gol) && ($w['KategoriID'] == $kat))? 'selected' : '';
      $a .= "<option value='$w[GolonganID]~$w[KategoriID]' $sel>$w[GolonganID]-$w[KategoriID] : $w[Pangkat]</option>";
    }
  }
  return $a;
}
function DsnEdtJabatan() {
  if (!empty($_REQUEST['dsnsub1'])) {
    $_REQUEST['dsnsub1']();
  }
  $w = GetFields('dosen', 'Login', $_SESSION['dsnid'], '*');
  $Jabatan = GetOption2('jabatan', "concat(JabatanID, ' - ', Nama)", 'JabatanID', $w['JabatanID'], '', 'JabatanID');
  $JabatanDikti = GetOption2('jabatandikti', "concat(JabatanDiktiID, ' - ', Nama)", 'JabatanDiktiID', $w['JabatanDiktiID'], '', 'JabatanDiktiID');
  //$Golongan = GetOption2('golongan', "concat(GolonganID, ' - ', KategoriID, ' - ', Pangkat)", 'GolonganID, KategoriID', $w['GolonganID'], "ProdiID='$w[Homebase]'", "concat(GolonganID,'~',KategoriID)");
  $optgol = GetOptGol('21201', $w['GolonganID'], $w['KategoriID']);
  $optikt = GetOption2("ikatan", "concat(IkatanID, ' - ', Nama, ' (', format(Besar, 0), ')')", "IkatanID", $w['IkatanID'], '', 'IkatanID');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='DsnEdtJabatanSav'>
  <input type=hidden name='dsnid' value='$w[Login]'>
  <input type=hidden name='BypassMenu' value='1' />
    
  <tr><td class=ul colspan=2><b>Jabatan</b></td></tr>
  <tr><td class=inp>Jabatan Akademik</td>
      <td class=ul><select name='JabatanID'>$Jabatan</select> <a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnsub=JbtnEdt'>Tambah  </a></td></tr>
  <tr><td class=inp>Jabatan Dikti</td>
      <td class=ul><select name='JabatanDiktiID'>$JabatanDikti</select></td></tr>
  <tr><td class=inp>Golongan</td>
      <td class=ul><select name='GolonganID'>$optgol</select> <a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnsub=GolEdt'>Tambah  </a></td></tr>
  <tr><td class=inp>Tunjangan Ikatan</td>
      <td class=ul><select name='IkatanID'>$optikt</select> <a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnsub=IktEdt'>Tambah  </a></td></tr>
  
  <tr><td class=ul colspan=2><b>Bank</b></td></tr>
  <tr><td class=inp>Nama Bank</td>
      <td class=ul><input type=text name='NamaBank' value='$w[NamaBank]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Nama Akun</td>
      <td class=ul><input type=text name='NamaAkun' value='$w[NamaAkun]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Nomer Akun</td>
      <td class=ul><input type=text name='NomerAkun' value='$w[NomerAkun]' size=50 maxlength=50></td></tr>
  
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table>";
}
function DsnEdtAkademikSav() {
  $dsnid = $_REQUEST['dsnid'];
  $TglBekerja = "$_REQUEST[TglBekerja_y]-$_REQUEST[TglBekerja_m]-$_REQUEST[TglBekerja_d]";
  $StatusDosenID = $_REQUEST['StatusDosenID'];
  $StatusKerjaID = $_REQUEST['StatusKerjaID'];
  $Homebase = $_REQUEST['Homebase'];
  $_ProdiID = array();
  $_ProdiID = $_REQUEST['ProdiID'];
  $ProdiID = implode('.', $_ProdiID);
  $ProdiID = ".$ProdiID.";
  $LulusanPT = sqling($_REQUEST['LulusanPT']);
  $JenjangID = $_REQUEST['JenjangID'];
  $Keilmuan = sqling($_REQUEST['Keilmuan']);
  $InstitusiInduk = sqling($_REQUEST['InstitusiInduk']);
  
  $s = "update dosen
    set TglBekerja='$TglBekerja', StatusDosenID='$StatusDosenID', StatusKerjaID='$StatusKerjaID',
    Homebase='$Homebase', ProdiID='$ProdiID',
    LulusanPT='$LulusanPT', JenjangID='$JenjangID', Keilmuan='$Keilmuan', InstitusiInduk='$InstitusiInduk', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where Login='$dsnid' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$dsnid&dsnsub=DsnEdtAkademik", 100);
}
function DsnEdtJabatanSav() {
  $dsnid = $_REQUEST['dsnid'];
  $JabatanID = $_REQUEST['JabatanID'];
  $JabatanDiktiID = $_REQUEST['JabatanDiktiID'];
  $Gol = $_REQUEST['GolonganID'];
  if (!empty($Gol)) {
    $arrgol = explode('~', $Gol);
    $GolonganID = $arrgol[0];
    $KategoriID = $arrgol[1];
  }
  else {
    $GolonganID = '';
    $KategoriID = '';
  }
  $IkatanID = $_REQUEST['IkatanID'];
  $NamaBank = sqling($_REQUEST['NamaBank']);
  $NamaAkun = sqling($_REQUEST['NamaAkun']);
  $NomerAkun = sqling($_REQUEST['NomerAkun']);
  $s = "update dosen set JabatanID='$JabatanID', JabatanDiktiID='$JabatanDiktiID',
    GolonganID='$GolonganID', KategoriID='$KategoriID', IkatanID='$IkatanID',
    NamaBank='$NamaBank', NamaAkun='$NamaAkun', NomerAkun='$NomerAkun', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where Login='$dsnid' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$dsnid&dsnsub=DsnEdtJabatan", 100);
}
function DsnAlmt() {
  $w = GetFields('dosen', 'Login', $_SESSION['dsnid'], '*');
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='DsnAlmtSav'>
  <input type=hidden name='dsnsub' value='DsnAlmt'>
  <input type=hidden name='dsnid' value='$w[Login]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>Alamat</b></td></tr>
  <tr><td class=inp>No KTP</td>
      <td class=ul><input type=text name='KTP' value='$w[KTP]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>No Telepon</td>
      <td class=ul><input type=text name='Telephone' value='$w[Telephone]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>No HP</td>
      <td class=ul><input type=text name='Handphone' value='$w[Handphone]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>E-mail</td>
      <td class=ul><input type=text name='Email' value='$w[Email]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Alamat</td>
      <td class=ul><textarea name='Alamat' cols=40 rows=3>$w[Alamat]</textarea></td></tr>
  <tr><td class=inp>Kota</td>
      <td class=ul><input type=text name='Kota' value='$w[Kota]' size=30 maxlength=30></td></tr>
  <tr><td class=inp>Kode Pos</td>
      <td class=ul><input type=text name='KodePos' value='$w[KodePos]' size=30 maxlength=30></td></td>
  <tr><td class=inp>Propinsi</td>
      <td class=ul><input type=text name='Propinsi' value='$w[Propinsi]' size=30 maxlength=30></td></tr>
  <tr><td class=inp>Negara</td>
      <td class=ul><input type=text name='Negara' value='$w[Negara]' size=30 maxlength=30></td></tr>
  <tr><td class=ul colspan=2 align=center><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function DsnAlmtSav() {
  $dsnid = $_REQUEST['dsnid'];
  $KTP = sqling($_REQUEST['KTP']);
  $Telephone = $_REQUEST['Telephone'];
  $Handphone = $_REQUEST['Handphone'];
  $Email = sqling($_REQUEST['Email']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  // Simpan
  $s = "update dosen set KTP='$KTP', Telephone='$Telephone',
    Handphone='$Handphone', Email='$Email', Alamat='$Alamat',
    Kota='$Kota', KodePos='$KodePos', Propinsi='$Propinsi', Negara='$Negara', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where Login='$dsnid' ";
  $r = _query($s);
  BerhasilSimpan("?dsnid=$dsnid&mnux=$_SESSION[mnux]&gos=dsnedt&dsnsub=DsnAlmt", 100);
}
  
function CariPTScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function caript(frm){
    lnk = "cari/cariperguruantinggi.php?PerguruanTinggiID="+frm.AsalPT.value+"&Cari="+frm.NamaPT.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}

function DsnEdtPendidikan() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
  	$w_ = GetFields('dosenpendidikan', 'DosenPendidikanID', $_REQUEST['DosenPendidikanID'], '*');
	  $PT = GetFields('perguruantinggi','PerguruanTinggiID',$w_['PerguruanTinggiID'],'Nama,PerguruanTinggiID');
	  $jdl = "Update Pendidikan Dosen";   
  }
  else {
    $jdl = "Tambah Pendidikan Dosen";
    $w_ = array();
	}
  
  $w = GetFields('dosen','Login',$_SESSION['dsnid'],'*');
  $Tglijazah = GetDateOption($w_['TanggalIjazah'], 'Tglijazah');
  $Jenjang = GetOption2('jenjang', "concat(JenjangID, ' - ', Nama)", 'JenjangID', $w_['JenjangID'], '', 'JenjangID');
  //$Nomor = GetOption2('jenjang','JenjangID','JenjangID',$w_['Nomor'],'','JenjangID');
  $NoBenua = GetOption2('benua',"concat(KodeBenua,' - ', NamaBenua)",'KodeBenua',$w_['KodeBenua'],'','KodeBenua');
  $Negara = GetOption2('negara','NamaNegara','NamaNegara',$w_['NamaNegara'],'','NamaNegara');
    
  CariPTScript();
  
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=900>
  <form action='?' method=POST Name='data'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='DsnEdt'>
  <input type=hidden name='dsnsub' value='TampilPendidikan'>
  <input type=hidden name='dsnsub1' value='DsnEdtPendidikanSav'>
  <input type=hidden name='dsnid' value='$_SESSION[dsnid]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='DosenPendidikanID' value='$w_[DosenPendidikanID]'>
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Nomor Urut</td><td class=ul><input type=text name='Nomor' value='$w_[Nomor]' size=4 maxlength=3></td></tr>
  <tr><td class=inp1>Gelar</td><td class=ul><input type=text name='Gelar' value='$w_[Gelar]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Jenjang</td><td class=ul><select name='JenjangID'>$Jenjang</select></td></tr>
  <tr><td class=inp1>Tanggal Lulus Ijasah</td><td class=ul>$Tglijazah</td></tr>
  <tr><td class=inp1>Kode P.T</td><td class=ul><input type=text name='AsalPT' value='$PT[PerguruanTinggiID]' size=10 maxlength=10></td></tr></td></tr>
  <tr><td class=inp1>Kode Asal P.T</td><td class=ul><input type=text name='NamaPT' value='$PT[Nama]' size=50> <a href='javascript:caript(data)'>Cari</a></td></tr>
  <tr><td class=inp1>Negara</td><td class=ul><input type=text name='NamaNegara' value='$w_[NamaNegara]' size=30 maxlength=50></td></tr>
  <tr><td class=inp1>Benua</td><td class=ul><select name='KodeBenua'>$NoBenua</select></td></tr>
  <tr><td class=inp1>Bidang Ilmu</td><td class=ul><input type=text name='BidangIlmu' value='$w_[BidangIlmu]' size=40 maxlength=></td></tr>
  <tr><td class=inp1>Prodi DIKTI</td><td class=ul><input type=text name='Prodidosen' value='$w_[Prodi]' size=40 maxlength=></td></tr>
  
  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=1001&dsnsub=TampilPendidikan'\"></td></tr>
  </form></table>";
  //<tr><td class=inp1>Negara</td><td class=ul><select name='NamaNegara'>$Negara</select></td></tr>
}

function DsnEdtPendidikanSav() {
  $md = $_REQUEST['md']+0;
  $Tglijazah_d = $_REQUEST['Tglijazah_d'];
  $Tglijazah_m = $_REQUEST['Tglijazah_m'];
  $Tglijazah_y = $_REQUEST['Tglijazah_y'];
  
  $dsnid = $_REQUEST['dsnid'];
  $Nomor = $_REQUEST['Nomor']+0;
  $DosenPendidikanID = $_REQUEST['DosenPendidikanID'];
  $Tglijazah = "$Tglijazah_y-$Tglijazah_m-$Tglijazah_d";
  $Gelar = $_REQUEST['Gelar'];
  $JenjangID = $_REQUEST['JenjangID'];
  $PTID = $_REQUEST['AsalPT'];
  $NamaNegara = $_REQUEST['NamaNegara'];
  $Nomor = $_REQUEST['Nomor'];
  $BidangIlmu = sqling($_REQUEST['BidangIlmu']);
  $KodeBenua = $_REQUEST['KodeBenua'];
  $ProdiDikti = $_REQUEST['Prodidosen'];
  
  if ($md == 0){
    $s = "update dosenpendidikan
      set Nomor=$Nomor, TanggalIjazah='$Tglijazah', Gelar='$Gelar', JenjangID='$JenjangID',
      PerguruanTinggiID='$PTID', NamaNegara='$NamaNegara',
      BidangIlmu='$BidangIlmu', KodeBenua = '$KodeBenua', Prodi = '$ProdiDikti',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where DosenPendidikanID=$DosenPendidikanID";
	  $r = _query($s);
  }
	else {
	  $in = "insert into dosenpendidikan (DosenID, Nomor, TanggalIjazah, Gelar, JenjangID,
      PerguruanTinggiID, NamaNegara, BidangIlmu, KodeBenua,
      LoginBuat, TanggalBuat)
  	  values ('$dsnid', $Nomor, '$Tglijazah', '$Gelar', '$JenjangID',
      '$PTID', '$NamaNegara', '$BidangIlmu', '$KodeBenua',
      '$_SESSION[_Login]', now())";
	  $r = _query($in); 
	}
  TampilPendidikan1();
}
function TampilKerjaDsn() {
  $dsnsub1 = (empty($_REQUEST['dsnsub1']))? "TampilKerjaDsn1" : $_REQUEST['dsnsub1'];
  $dsnsub1();
}
function TampilKerjaDsn1(){
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosenpekerjaan
    where DosenID='$_SESSION[dsnid]' $where
    order by DosenPekerjaanID";
  //$NamaPT = GetaField('perguruantinggi','PerguruanTinggiID','=PerguruanTinggiID=','Nama');
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4 width=800>
    <tr>
      <td class=ul colspan=9>
      <a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnid=$_SESSION[dsnid]&dsnsubs=TampilKerjaDsn&dsnsub=DsnEdtPekerjaan'>Tambah Pekerjaan</td>
      </tr>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Edit</th>
	  <th class=ttl>Jabatan</th>
	  <th class=ttl>Nama Institusi</th>
	  <th class=ttl>Alamat Institusi</th>
	  <th class=ttl>Kota</th>
	  <th class=ttl>Kodepos</th>
	  <th class=ttl>Telepon</th>
	  <th class=ttl>Fax</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=><a href=\"?mnux=$_SESSION[mnux]&gos=DsnEdt&md=0&dpid==DosenPekerjaanID=&dsnid=$_SESSION[dsnid]&dsnsub=TampilKerjaDsn&dsnsub1=DsnEdtPekerjaan\"><img src='img/edit.png' border=0>
      </a></td>
	  <td class=cna=NA= nowrap>=Jabatan=</a></td>
	  <td class=cna=NA=>=Institusi=</td>
	  <td class=cna=NA=>=Alamat=</td>
	  <td class=cna=NA=>=Kota=</td>
	  <td class=cna=NA=>=Kodepos=</td>
	  <td class=cna=NA=>=Telepon=</td>
	  <td class=cna=NA=>=Fax=</td>
	  <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}
function TampilTelitiDsn() {
  $dsnsub1 = (empty($_REQUEST['dsnsub1']))? "TampilTelitiDsn1" : $_REQUEST['dsnsub1'];
  $dsnsub1();
}
function TampilTelitiDsn1(){
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosenpenelitian
    where DosenID='$_SESSION[dsnid]' $where
    order by DosenPenelitianID";
  //$NamaPT = GetaField('perguruantinggi','PerguruanTinggiID','=PerguruanTinggiID=','Nama');
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4 width=600>
    <td class=ul colspan=9><a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnid=$_SESSION[dsnid]&dsnsub=TampilTelitiDsn&dsnsub1=DsnEdtPenelitian'>Tambah Penelitian</a></td></tr>
    <tr>
	  <th class=ttl>Penelitian</th>
      <th class=ttl>Tahun</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp1 width=18 align=right><a href=\"?mnux=$_SESSION[mnux]&gos=DsnEdt&md=0&dlid==DosenPenelitianID=&dsnid=$_SESSION[dsnid]&dsnsub=TampilTelitiDsn&dsnsub1=DsnEdtPenelitian\"><img src='img/edit.png'>&nbsp;
    =NOMER=</a></td>
    <td class=cna=NA= nowrap>=NamaPenelitian=</td>
    <td class=cna=NA= align=center width=30>=TahunPenelitian=</td>
    <td class=cna=NA= align=center width=10><img src='img/book=NA=.gif'></td>
    </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

function DsnEdtPenelitian() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
  	$w = GetFields('dosenpenelitian', 'DosenPenelitianID', $_REQUEST['dlid'], '*');
	  $jdl = "Update Penelitian Dosen";   
      $hapus = "<tr><td class=inp1>Hapus Penelitian</td><td class=ul><input type=checkbox name='NA' value='Y'></td></tr>";
      $TelkatID = GetaField("penelitian_subkategori", "Subkat_ID", $w['Subkat_ID'],"Telkat_ID");
  	
  }
  else {
    $w = array();
    $w['DosenID'] = $_REQUEST['dsnid'];
    $w['DosenPenelitianID'] = 0;
    $w['Subkat_ID']=0;
    $w['TahunPenelitian']='';
    $jdl = "Tambah Penelitian Dosen";
    $hapus='';
	}
  
  //$w  = GetFields('dosen','DosenID',$_SESSION['dsnid'],'*');
  //$Kota = GetOption2('perguruantinggi', 'Kota', 'Kota', $w['Kota'], '', 'Kota');
  	$optKategori = GetOption2('penelitian_kategori', 'Nama', 'Telkat_ID', $TelkatID, '', 'Telkat_ID');
  	$optSubKategori = GetOption2('penelitian_subkategori', 'Nama', 'Subkat_ID', $w['Subkat_ID'], "Telkat_ID='$TelkatID'", 'Subkat_ID');
  	$SubkategoriOpt = (!empty($optSubKategori))? $optSubKategori:"";
  echo "<p><table class=box cellspacing=1 cellpadding=4 width='600'>
  <form action='?' method=POST Name='data'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='DsnEdtPenelitianSav'>
  <input type=hidden name='dsnid' value='$w[DosenID]'>
  <input type=hidden name='dlid' value='$w[DosenPenelitianID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Judul Penelitian</td><td class=ul><input type=text name='penelitian' value='$w[NamaPenelitian]' size=50></td></tr>
  <tr><td class=inp1>Nama Peneliti</td><td class=ul><input type=text name='NamaPeneliti' value='$w[NamaPeneliti]' size=50></td></tr>
  <tr><td class=inp1>Tahun Penelitian</td><td class=ul><input type=text name='TahunPenelitian' value='$w[TahunPenelitian]' size=5 maxlength=4></td></tr>
  <tr><td class=inp1>Kategori</td><td class=ul ><select name='KategoriID' onchange=\"javascript:subkat(this)\">$optKategori</select>
  <br />Sub Kategori : <select name='SubKategori' id='Kategori' class='nones'>$SubkategoriOpt</select></td></tr>
  $hapus

  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=TampilTelitiDsn'\"></td></tr>
  </form></table>";
  ?>
  <script type='text/javascript'>
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
				function subkat(pilih)
				{
					var kat = pilih.value;
				
					if (!kat) document.getElementById("Kategori").innerHTML ='';
						xmlhttp.open('get', 'master/ajx/subkategori.penelitiandosen.php?kat='+kat, true);
						xmlhttp.onreadystatechange = function()
					{
					if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
						document.getElementById("Kategori").innerHTML = xmlhttp.responseText;
						return false;
					}
					xmlhttp.send(null);
				}
		</script>
	<?php
}
function DsnEdtPenelitianSav() {
  $md = $_REQUEST['md']+0;
  $dsnid = sqling($_REQUEST['dsnid']);
  $Penelitian = sqling($_REQUEST['penelitian']);
  $NamaPeneliti = sqling($_REQUEST['NamaPeneliti']);
  $TahunPenelitian = sqling($_REQUEST['TahunPenelitian']);
  $dlid = sqling($_REQUEST['dlid']);
  $Subkat_ID = sqling($_REQUEST['SubKategori']);
  
  if ($md == 0){
    $s = "update dosenpenelitian
      set NamaPenelitian='$Penelitian', NamaPeneliti='$NamaPeneliti', Subkat_ID='$Subkat_ID', TahunPenelitian='$TahunPenelitian', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where DosenPenelitianID='$dlid'";
    $r = _query($s); 
  }
  else {
    $in = "insert into dosenpenelitian (DosenID, NamaPenelitian, NamaPeneliti,TahunPenelitian, Subkat_ID, TanggalBuat,LoginBuat)
      values ('$dsnid', '$Penelitian','$NamaPeneliti,'$TahunPenelitian', '$Subkat_ID', now(),'$_SESSION[_Login]')";
    $r = _query($in); 
  }
  if ($_REQUEST['NA']=='Y'){
  	$hps = "delete from dosenpenelitian where DosenPenelitianID='$dlid'";
    $r = _query($hps);
   }
 BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$dsnid&dsnsub=TampilTelitiDsn", 100);
 //echo $s;
}

function JbtnEdt() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
  	$w = GetFields('jabatan', 'JabatanID', $_REQUEST['jbtid'], '*');
	  $jdl = "Jabatan";   
	  $dis='disabled';
  }
  else {
    $w = array();
    $w['JabatanID'] = $_REQUEST['jbtid'];
    $jdl = "Jabatan";
    $dis='';
	}
  
  //$w  = GetFields('dosen','DosenID',$_SESSION['dsnid'],'*');
  //$Kota = GetOption2('perguruantinggi', 'Kota', 'Kota', $w['Kota'], '', 'Kota');
  if($w[NA]=='Y'){
    $c='checked';
  }else{
    $c='';
  }
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST Name='datajbt'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='JbtnEdtSav'>
  <input type=hidden name='jbtid' value='$w[JabatanID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>ID Jabatan</td><td class=ul><input type=text name='jbtid' value='$w[JabatanID]' size=20 maxlength=100 $dis></td></tr>
<tr><td class=inp1>Nama Jabatan</td><td class=ul><input type=text name='nmjbt' value='$w[Nama]' size=20 maxlength=100></td></tr>
<tr><td class=inp1>NA?</td><td class=ul><input type=checkbox name='na' value='Y' $c></td></tr>

  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=DsnEdtJabatan'\"></td></tr>
  </form></table>";
  
  DaftarJabatan($_SESSION['mnux'], "gos=DsnEdt&md=0&jbtid==JabatanID=", "Nama");

}

function JbtnEdtSav($gos='DsnEdt') {
  $md = $_REQUEST['md']+0;
  $JabatanID = sqling($_REQUEST['jbtid']);
 
  $NA = (empty($_REQUEST['na']))? 'N' : $_REQUEST['na'];
  if ($md == 0) {
    $s = "update jabatan set Nama='$_REQUEST[nmjbt]',NA='$NA'
      where JabatanID='$JabatanID' ";
    $r = _query($s);
    BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=$gos&md=1&dsnsub=JbtnEdt", 100);
  }
  else {
    $ada = GetFields('jabatan', "JabatanID", $JabatanID, '*');
    if (empty($ada)) {
      $s = "insert into jabatan 
        values ('$JabatanID', '$_REQUEST[nmjbt]', 'N', 'N')";
      $r = _query($s);

      BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=$gos&md=1&dsnsub=JbtnEdt", 100);
    }
    else echo ErrorMsg("Gagal", "Data Jabatan tidak dapat disimpan karena Kode Jabatan: <b>$JabatanID</b> sudah dipakai oleh:
      <b>$ada[Nama]</b>.<br>
      Gunakan Kode lain.<hr size=1 color=silver>
      Pilihan: <a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnsub=JbtnEdt'>Tambah Jabatan</a>");
  }
}
function GolEdt() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
  	$w = GetFields('golongan', 'GolonganID', $_REQUEST['golid'], '*');
	  $jdl = "Golongan";   
	  $dis='disabled';
  }
  else {
    $w = array();
    $w['GolonganID'] = $_REQUEST['jbtid'];
    $jdl = "Golongan";
    $dis='';
	}
  
  //$w  = GetFields('dosen','DosenID',$_SESSION['dsnid'],'*');
  //$Kota = GetOption2('perguruantinggi', 'Kota', 'Kota', $w['Kota'], '', 'Kota');
  if($w[NA]=='Y'){
    $c='checked';
  }else{
    $c='';
  }
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST Name='datagol'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='GolEdtSav'>
  <input type=hidden name='golid' value='$w[GolonganID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Kode Golongan</td><td class=ul><input type=text name='golid' value='$w[GolonganID]' size=20 maxlength=100 $dis></td>
  <td class=inp1>Tunjangan Fungsional</td><td class=ul><input type=text name='tfungsional' value='$w[TunjanganFungsional]' size=20 maxlength=100></td></tr>
<tr><td class=inp1>Kategori</td><td class=ul><input type=text name='kategori' value='$w[KategoriID]' size=20 maxlength=100></td>
<td class=inp1>Tunjangan SKS</td><td class=ul><input type=text name='tsks' value='$w[TunjanganSKS]' size=20 maxlength=100></td></tr>
<tr><td class=inp1>Program Studi</td><td class=ul><input type=text name='prodi' value='$w[ProdiID]' size=20 maxlength=100></td>
<td class=inp1>Tunjangan Transportasi</td><td class=ul><input type=text name='ttransport' value='$w[TunjanganTransport]' size=20 maxlength=100></td></tr>
<tr><td class=inp1>Pangkat</td><td class=ul><input type=text name='pangkat' value='$w[Pangkat]' size=20 maxlength=100></td>
<td class=inp1>Tunjangan Tetap</td><td class=ul><input type=text name='ttetap' value='$w[TunjanganTetap]' size=20 maxlength=100></td></tr>
<tr><td class=inp1>Nama</td><td class=ul><input type=text name='namagol' value='$w[Nama]' size=20 maxlength=100></td>
<td class=inp1>NA?</td><td class=ul><input type=checkbox name='na' value='Y' $c></td></tr>



  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=DsnEdtJabatan'\"></td></tr>
  </form></table>";
  
  DaftarGolongan($_SESSION['mnux'], "gos=DsnEdt&md=0&golid==GolonganID=", "KategoriID,ProdiID,Pangkat,Nama,TunjanganFungsional,TunjanganSKS,TunjanganTransport,TunjanganTetap");

}

function GolEdtSav($gos='DsnEdt') {
  $md = $_REQUEST['md']+0;
  $GolonganID = sqling($_REQUEST['golid']);
 
  $NA = (empty($_REQUEST['na']))? 'N' : $_REQUEST['na'];
  if ($md == 0) {
    $s = "update golongan set KategoriID='$_REQUEST[kategori]',ProdiID='$_REQUEST[prodi]',Pangkat='$_REQUEST[pangkat]', Nama='$_REQUEST[namagol]', TunjanganFungsional='$_REQUEST[tfungsional]', TunjanganSKS='$_REQUEST[tsks]', TunjanganTransport='$_REQUEST[ttransport]', TunjanganTetap='$_REQUEST[ttetap]',NA='$NA'
      where GolonganID='$GolonganID' ";
    $r = _query($s);
    BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=$gos&md=1&dsnsub=GolEdt", 100);
  }
  else {
    $ada = GetFields('golongan', "GolonganID", $GolonganID, '*');
    if (empty($ada)) {
      $s = "insert into golongan 
        values ('$GolonganID', '$_REQUEST[kategori]','$_REQUEST[prodi]','','$_REQUEST[pangkat]','$_REQUEST[namagol]','','$_REQUEST[tfungsional]','$_REQUEST[tsks]','$_REQUEST[ttransport]','$_REQUEST[ttetap]', 'N')";
      $r = _query($s);

      BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=$gos&md=1&dsnsub=GolEdt", 100);
    }
    else echo ErrorMsg("Gagal", "Data Golongan tidak dapat disimpan karena Kode Golongan: <b>$GolonganID</b> sudah dipakai oleh:
      <b>$ada[Pangkat]</b>.<br>
      Gunakan Kode lain.<hr size=1 color=silver>
      Pilihan: <a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnsub=GolEdt'>Tambah Golongan</a>");
  }
}

function IktEdt() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
  	$w = GetFields('ikatan', 'IkatanID', $_REQUEST['iktid'], '*');
	  $jdl = "Ikatan";   
	  $dis='disabled';
  }
  else {
    $w = array();
    $w['IkatanID'] = $_REQUEST['iktid'];
    $jdl = "Ikatan";
    $dis='';
	}
  
  //$w  = GetFields('dosen','DosenID',$_SESSION['dsnid'],'*');
  //$Kota = GetOption2('perguruantinggi', 'Kota', 'Kota', $w['Kota'], '', 'Kota');
  if($w[NA]=='Y'){
    $c='checked';
  }else{
    $c='';
  }
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST Name='datagol'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='IktEdtSav'>
  <input type=hidden name='iktid' value='$w[IkatanID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Kode Ikatan</td><td class=ul><input type=text name='iktid' value='$w[IkatanID]' size=20 maxlength=100 $dis></td></tr>
<tr><td class=inp1>Nama Ikatan</td><td class=ul><input type=text name='nmikt' value='$w[Nama]' size=20 maxlength=100></td></tr>
<tr><td class=inp1>Besar Tunjangan Ikatan</td><td class=ul><input type=text name='besar' value='$w[Besar]' size=20 maxlength=100></td></tr>
<tr><td class=inp1>NA?</td><td class=ul><input type=checkbox name='na' value='Y' $c></td></tr>


  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=DsnEdtJabatan'\"></td></tr>
  </form></table>";
  
  DaftarIkatan($_SESSION['mnux'], "gos=DsnEdt&md=0&iktid==IkatanID=", "Nama,Besar");

}

function IktEdtSav($gos='DsnEdt') {
  $md = $_REQUEST['md']+0;
  $IkatanID = sqling($_REQUEST['iktid']);
 
  $NA = (empty($_REQUEST['na']))? 'N' : $_REQUEST['na'];
  if ($md == 0) {
    $s = "update ikatan set Nama='$_REQUEST[nmikt]',Besar='$_REQUEST[besar]'
      where IkatanID='$IkatanID' ";
    $r = _query($s);
    BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=$gos&md=1&dsnsub=IktEdt", 100);
  }
  else {
    $ada = GetFields('ikatan', "IkatanID", $IkatanID, '*');
    if (empty($ada)) {
      $s = "insert into ikatan 
        values ('$IkatanID', '$_REQUEST[nmikt]','$_REQUEST[besar]','','','','', 'N')";
      $r = _query($s);

      BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=$gos&md=1&dsnsub=IktEdt", 100);
    }
    else echo ErrorMsg("Gagal", "Data Ikatan tidak dapat disimpan karena Kode Ikatan: <b>$IkatanID</b> sudah dipakai oleh:
      <b>$ada[Nama]</b>.<br>
      Gunakan Kode lain.<hr size=1 color=silver>
      Pilihan: <a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnsub=IktEdt'>Tambah Ikatan</a>");
  }
}

function TampilPendidikan() {
  $dsnsub1 = (empty($_REQUEST['dsnsub1']))? "TampilPendidikan1" : $_REQUEST['dsnsub1'];
  $dsnsub1();
}
function TampilPendidikan1(){
  include_once "class/dwolister.class.php";  
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosenpendidikan left outer join perguruantinggi pt on pt.PerguruanTinggiID = dosenpendidikan.PerguruanTinggiID 
  	left outer join jenjang j on j.JenjangID = dosenpendidikan.JenjangID
    where dosenpendidikan.DosenID='$_SESSION[dsnid]' $where
    order by Nomor";
  $lst->fields = "dosenpendidikan.*, pt.Nama as Nama, j.Nama as jnama, date_format(TanggalIjazah, '%d-%m-%Y') as TGLIJZ ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
  	<td class=ul colspan=9><a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnid=$_SESSION[dsnid]&dsnsub=TampilPendidikan&dsnsub1=DsnEdtPendidikan'>Tambah Pendidikan</td></tr>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Edit</th>
	  <th class=ttl>Gelar</th>
	  <th class=ttl>Jenjang</th>
	  <th class=ttl>Tanggal Lulus Ijazah</th>
	  <th class=ttl>Nama Perguruan Tinggi</th>
	  <th class=ttl>Negara</th>
	  <th class=ttl>Bidang Ilmu</th>
	  <th class=ttl>Prodi DIKTI</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
      <td class=inp1 width=18 align=right>=Nomor=</td>
      <td class=cna=NA=><a href=\"?mnux=$_SESSION[mnux]&gos=DsnEdt&md=0&dsnid=$_SESSION[dsnid]&dsnsub=TampilPendidikan&dsnsub1=DsnEdtPendidikan&DosenPendidikanID==DosenPendidikanID=\"><img src='img/edit.png' border=0>
      </a></td>
	  <td class=cna=NA= nowrap>=Gelar=</a></td>
	  <td class=cna=NA=>=jnama=</td>
	  <td class=cna=NA=>=TGLIJZ=</td>
	  <td class=cna=NA=>=Nama=</td>
	  <td class=cna=NA=>=NamaNegara=</td>
	  <td class=cna=NA=>=BidangIlmu=</td>
	  <td class=cna=NA=>=Prodi=</td>
	  <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

function DsnEdtPekerjaan() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
    $w = GetFields('dosenpekerjaan', 'DosenPekerjaanID', $_REQUEST['dpid'], '*');
    $jdl = "Update Pekerjaan Dosen";   
  }
  else {
    $w = array();
    $w['DosenID'] = $_REQUEST['dsnid'];
    $w['DosenPekerjaanID'] = 0;
    $jdl = "Tambah Pekerjaan Dosen";
  }
  
  //$w  = GetFields('dosen','DosenID',$_SESSION['dsnid'],'*');
  $Kota = GetOption2('perguruantinggi', 'Kota', 'Kota', $w['Kota'], '', 'Kota');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST Name='data'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='DsnEdtPekerjaanSav'>
  <input type=hidden name='dsnid' value='$w[DosenID]'>
  <input type=hidden name='dpid' value='$w[DosenPekerjaanID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Nama Institusi</td><td class=ul><input type=text name='Institusi' value='$w[Institusi]' size=20 maxlength=100></td></tr>
  <tr><td class=inp1>Jabatan</td><td class=ul><input type=text name='Jabatan' value='$w[Jabatan]' size=20 maxlength=50></td></tr>
  <tr><td class=inp1>Alamat Institusi</td><td class=ul><input type=text name='Alamat' value='$w[Alamat]' size=30 maxlength=100></td></tr>
  <tr><td class=inp1>Kota</td><td class=ul><input type=text name='Kota' value='$w[Kota]' maxlength=50 size=30></td></tr>
  <tr><td class=inp1>Kodepos</td><td class=ul><input type=text name='Kodepos' value='$w[Kodepos]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Telepon</td><td class=ul><input type=text name='Telepon' value='$w[Telepon]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Fax</td><td class=ul><input type=text name='Fax' value='$w[Fax]' size=10 maxlength=10></td></tr>
    
  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=TampilKerjaDsn'\"></td></tr>
  </form></table>";
}

function DsnEdtPekerjaanSav() {
  $md = $_REQUEST['md']+0;
  $dsnid = $_REQUEST['dsnid'];
  $Institusi = sqling($_REQUEST['Institusi']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $Kodepos = sqling($_REQUEST['Kodepos']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Fax = sqling($_REQUEST['Fax']);
  $dpid = $_REQUEST['dpid'];
  
  if ($md == 0){
    $s = "update dosenpekerjaan
      set Institusi='$Institusi', Jabatan='$Jabatan', Alamat='$Alamat',
      Kota='$Kota', Telepon='$Telepon',
      Fax='$Fax', Kodepos = '$Kodepos'
      where DosenPekerjaanID='$dpid'";
	  $r = _query($s); 
	}
	else {
	  $in = "insert into dosenpekerjaan (DosenID, Institusi, Jabatan, Alamat, Kota, Kodepos, Telepon, Fax)
  		values ('$dsnid', '$Institusi', '$Jabatan', '$Alamat', '$Kota', '$Kodepos', '$Telepon', '$Fax')";
	  $r = _query($in); 
	}
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$dsnid&dsnsub=TampilKerjaDsn", 100);
}
function DsnEdtPengajaran() {
  $dsnajarpage = GetSetVar('dsnajarpage', 1);
  $DosenID = GetaField('dosen',"Login",$_SESSION[dsnid],'NIDN');
  $fmtTgl = '%d-%m-%Y';
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['dsnajarpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnajarpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosen d,jadwal j
    left outer join hari h on j.HariID=h.HariID
    left outer join kelas k on k.KelasID=j.NamaKelas
    where j.DosenID=d.Login and d.NIDN='$DosenID'
    order by j.TahunID, j.HariID, j.JamMulai desc";
  //$NamaPT = GetaField('perguruantinggi','PerguruanTinggiID','=PerguruanTinggiID=','Nama');
  $lst->fields = "j.TahunID, j.MKKode, j.MKID, j.Nama, k.Nama as NMKelas, j.JenisJadwalID, date_format(j.TglMulai, '$fmtTgl') as Tanggal, h.Nama as HR,
    j.JamMulai, j.JamSelesai ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Hari</th>
    <th class=ttl>Tanggal</th>	
    <th class=ttl>Jam</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
	  <td class=ul>=TahunID=</td>
	  <td class=ul>=MKKode=</td>
	  <td class=ul>=Nama=</td>
	  <td class=ul>=NMKelas=</td>
	  <td class=ul>=JenisJadwalID=</td>
	  <td class=ul>=HR=</td>
	  <td class=ul>=Tanggal=</td>
	  <td class=ul>=JamMulai= ~ =JamSelesai=</td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";

}

function GetDosenID($thn) {
  $_DosenDigit = 4;
  // Buat nomer baru
  $nomer = str_pad('', $_DosenDigit, '_', STR_PAD_LEFT);
  $nomer = $thn . $nomer;
  $akhir = GetaField('dosen',
    "Login like '$nomer' and KodeID", KodeID, "max(Login)");
  $nmr = str_replace($thn, '', $akhir);
  $nmr++;
  $baru = str_pad($nmr, $_DosenDigit, '0', STR_PAD_LEFT);
  $baru = $thn.$baru;
  return $baru;
}

// *** Tambahan untuk ITP by Arisal Yanuarafi ***
function BahanAjar() {
  $dsnsub1 = (empty($_REQUEST['dsnsub1']))? "TampilBahanAjar" : $_REQUEST['dsnsub1'];
  $dsnsub1();
}
function TampilBahanAjar(){
	$dsnbhnajar = GetSetVar('dsnbhnajar', 1);
  $DosenID = GetaField('dosen',"Login",$_SESSION[dsnid],'NIDN');
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['dsnbhnajar']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnbhnajar==PAGE=&dsnid=$_SESSION[dsnid]&dsnsub=BahanAjar'>=PAGE=</a>";
  $lst->tables = "dosenbahanajar
    where NIDN='$DosenID' $where
    order by Matakuliah,Nama";
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4 width=750>
    <td class=ul colspan=9><a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dsnid=$_SESSION[dsnid]&dsnsub=BahanAjar&dsnsub1=EdtBahanAjar'>Tambah Bahan Ajar</td></tr>
    <tr>
	  <th class=ttl width=20>#</th>
	  <th class=ttl width=30>TahunID</th>
        <th class=ttl width=280>Matakuliah</th>
        <th class=ttl>Bahan Ajar <sup>Total Download</sup></th>
      <th class=ttl width=10>#</th>
      <th class=ttl width=10>Del</th>
    </tr>";
  $lst->detailfmt = "<tr>
    <td class=inp1 align=right>=NOMER=</td>
    <td class=cna=NA=>=TahunID=
     </td>
    <td class=cna=NA= nowrap><b>=Matakuliah=</b></td>
    <td class=cna=NA= nowrap><a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=0&dlid==FileID=&dsnid=$_SESSION[dsnid]&dsnsub=BahanAjar&dsnsub1=EdtBahanAjar'>=Nama= </a>&laquo;=Ukuran=Kb<sup>=TotalDownload=&times;</td>
    <td class=cna=NA= nowrap><a href='mhsw/download.php?fn==MD5Code=' target='_blank'><img src=img/disket.gif /></a></td>
    <td class=cna=NA= nowrap><a href='?mnux=$_SESSION[mnux]&gos=DsnEdt&md=1&dlid==FileID=&dsnid=$_SESSION[dsnid]&dsnsub=BahanAjar&dsnsub1=DelBahanAjar' onclick=\"return confirm('Anda yakin menghapus Bahan Ajar ini?')\" ><img src=img/del.gif /></td>
    </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

function DelBahanAjar() {
$id = sqling($_REQUEST['dlid']);
$File = GetaField('dosenbahanajar',"FileID", $id,"concat(NIDN,'_',MD5Code,'.',TipeFile)");
chmod('file/bahanajar/'.$File,0644);
$s = "delete from dosenbahanajar where FileID='$id'";
$r = _query($s);
unlink('file/bahanajar/'.$File);
BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=BahanAjar", 100);
}

function EdtBahanAjar() {
  $md = $_REQUEST['md']+0;
  $DosenID = GetaField('dosen',"Login",$_SESSION[dsnid],'NIDN');
  $Tahun = GetaField('tahun',"TahunID not like 'Tra%' and NA",N,'max(TahunID)');
  $s1 = "select distinct(j.Nama) as NamaMK, j.MKKode,j.JadwalID, j.ProdiID from jadwal j,dosen d
    where j.DosenID=d.Login and d.NIDN='$DosenID' and j.TahunID='$Tahun'
    order by NamaMK";
	$r1 = _query($s1);
	$optMK = "<option value=''></option>";
    // **** tambah untuk semua matakuliah ****
    $optMK .= "<option value='Semua'> - SEMUA MATAKULIAH SAYA -</option>";
	while ($w1 = _fetch_array($r1)) {
    $Prodi = GetFields('prodi',"ProdiID", $w1[ProdiID],'Nama,JenjangID');
    $Jenjang = GetaField('jenjang',"JenjangID",$Prodi[JenjangID],'Nama');
		if ($w['MKKode']==$w1['MKKode']) {
			$optMK .= "<option value='$w1[JadwalID]' selected>$w1[MKKode] - $w1[NamaMK] - $Jenjang</option>";
		}
        else $optMK .= "<option value='$w1[JadwalID]'>$w1[MKKode] - $w1[NamaMK] - $Jenjang - $Matakuliah $DosenID</option>"; 
	}
    	
		
  if($md == 0){
  	$w = GetFields('dosenbahanajar', 'FileID', $_REQUEST['dlid'], '*');
	$w['DosenID'] = $_REQUEST['dsnid'];
	  $jdl = "Update Nama File Bahan Ajar Dosen";   
	  $Nama = "<input type=text name='Nama' value='$w[Nama]' size=70 maxlength=90>";
	  $PilihanMK ="";
      $Hide = ($w['Hide']=='Y')? "checked='checked'" : '';
      $unHide =($w['Hide']=='N')? "checked='checked'" : '';
  }
  else {
    $w = array();
    $w['NIDN'] = $_REQUEST['dsnid'];
    $jdl = "Tambah File Bahan Ajar Dosen";
	$Nama = "<input type=file name='nama' size=70>";
	$PilihanMK = "<tr><td class=inp1>Matakuliah</td><td class=ul><select name='MKKode'>$optMK</select></td></tr>";
    $unHide ="checked='checked'";
}

	
  echo "<p><form action='?' method=POST Name='data' enctype='multipart/form-data'>
  <table class=box cellspacing=1 cellpadding=4 width=650>
  <input type=hidden name='MAX_FILE_SIZE' value='624288000' />
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='EdtBahanAjarSav'>
  <input type=hidden name='dsnid' value='$w[NIDN]'>
  <input type=hidden name='dlid' value='$w[FileID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  $PilihanMK
  <tr><td class=inp1>Nama File</td><td class=ul>$Nama</td></tr>
  <tr><td class=inp1>Sembunyikan</td><td class=ul><input type='radio' name='Hide' value='Y' $Hide />Ya <input type='radio' name='Hide' value='N' $unHide />Tidak <sup> (Jika dipilih Ya, maka file tidak terlihat oleh mahasiswa)</sup></td></tr>
  

  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=BahanAjar'\"></td></tr>
  </table></form>";
}
?><?php
function EdtBahanAjarSav() {
// terjadi pertukaran arti antara MKID dan MKKode, karena yang diinginkan matakuliah yang sama namanya tidak ditampilkan lagi.
  $md = $_REQUEST['md']+0;
  $dsnid = $_REQUEST['dsnid'];
  $DosenID = GetaField('dosen',"Login",$dsnid,'NIDN');
  $Login = GetaField('dosen',"Login",$dsnid,'Login');
  $NamaF = sqling($_REQUEST['Nama']);
  $MKKode = GetaField('jadwal', "JadwalID", sqling($_REQUEST['MKKode']),'MKKode');
  $dlid = $_REQUEST['dlid'];
  $Hide = sqling($_REQUEST['Hide']);
  			// berdasarkan mkid
		  if ($MKKode != 'Semua') {
		  	$MKID = GetaField('jadwal', "JadwalID", sqling($_REQUEST['MKKode']),'MKID');
			$Matakuliah = GetaField('jadwal', "JadwalID", sqling($_REQUEST['MKKode']),'Nama');
			$TahunID = GetaField('jadwal', "JadwalID", sqling($_REQUEST['MKKode']),'TahunID');
			}
			// kalau yang dipilih semua matakuliah
			else {
			$MKKode = 'Semua';
			$Matakuliah = '** SEMUA **';
			}
 
	  if ($md == 0){
		$s = "update dosenbahanajar
		  set Nama='$NamaF',Hide='$Hide'
		  where FileID='$dlid'";
		$r = _query($s); 
	  }
	  else {
		$dir = 'file/bahanajar/';
		$file = $_FILES['nama']['tmp_name'];
		$tipe = $_FILES['nama']['type'];
		 $arrtipe = explode('/', $tipe);
		$extensi = $arrtipe[1];
		$name = $_FILES['nama']['name'];
		$Berkas = $dir.$extensi;
		$file_extension = strtolower(substr(strrchr($name,"."),1));
	   // Mendefinisikan extensi file
	   $exts = array('zip', 
						'rar',
						'doc',
						'docx',
						'xls',
						'xlsx',
						'ppt',
						'pptx',
						'pdf',
						'txt',
						'jpg',
						'jpeg',
						'gif',
						'fla',
						'rtf'
						);
	
			 // Periksa extensi
			if (!in_array(($file_extension), $exts)) {
						die(ErrorMsg('Error',
						"File dengan extensi .$file_extension tidak bisa diupload, karena tidak terdaftar dalam whitelist.<br>
						Harap upload file dengan extension normal."));
				}
			else {
					$Next = GetaField('dosenbahanajar', "MKKode='$MKKode' and NIDN",$DosenID,'count(FileID)')+1;
				 	$MD5Code = md5($Next.$MKKode.$DosenID);
					$nama_filenya = date('YmdHis').$DosenID.'_'.$MD5Code.'.'.$file_extension;
				if (!move_uploaded_file($file, $dir.$nama_filenya)) {
       			die(ErrorMsg('Error',
						"Terjadi kesalahan fatal. Tidak bisa mengupload file. Harap hubungi sysadmin, Kode Error: ".$_FILES['nama']['error']));
  				 } else {
				 $ukuran = filesize($dir.$nama_filenya)/1024;
				 $TanggalBuat = date('Y-m-d H:i:s');
				$in = "insert into dosenbahanajar (MKID, MKKode, Matakuliah, Nama, NIDN, TahunID, MD5Code, TipeFile, Ukuran, Hide, TanggalBuat)
				values ('$MKID', '$MKKode', '$Matakuliah','$name','$DosenID','$TahunID','$MD5Code','$file_extension',$ukuran, '$Hide','$TanggalBuat')";
				$r = _query($in); 
				chmod('file/bahanajar',0755);
				chmod($dir.$nama_filenya,0444);
			}
			
		}
		
 	}


	BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=DsnEdt&dsnid=$dsnid&dsnsub=BahanAjar", 100);
}

// *** Paramaters ***
$dsnsub = GetSetVar('dsnsub');
$dsnurt = GetSetVar('dsnurt', 'Login');
$dsnid = GetSetVar('dsnid');
$dsncr = GetSetVar('dsncr');
$dsnkeycr = GetSetVar('dsnkeycr');
$dsnpage = GetSetVar('dsnpage');
$prodi = GetSetVar('prodi');
$last = GetSetVar('last', "H");
if ($dsnkeycr == 'Reset') {
  $dsncr = '';
  $_SESSION['dsncr'] = '';
  $dsnkeycr = '';
  $_SESSION['dsnkeycr'] = '';
}
$gos = (empty($_REQUEST['gos']))? 'CariDosen' : $_REQUEST['gos'];

// *** Main ***
if ($_SESSION['_LevelID']==100) {
TampilkanJudul("Data Dosen");
}
else {
TampilkanJudul("Master Dosen");
}
$gos();

?>
