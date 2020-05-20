<?php
session_start();

// *** Parameters ***

// *** Main ***
if ($_SESSION['_LevelID'] == 1) {
  $AplikanID = GetSetVar('_pmbLoginAplikanID');
}
elseif ($_SESSION['_LevelID'] == 29) {
  $AplikanID = $_SESSION['_Login'];
}
else die(ErrorMsg('Error',
  "Anda tidak berhak menjalankan modul ini."));

CekBolehAksesModul();
$oke = BolehAksesData($_SESSION['_Login']);
  
  if ($oke) {
    $gel = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
	
	$gos = sqling($_REQUEST['gos']);
    if (empty($gos)) {
      Main($gel, $AplikanID);
    }
    else $gos($gel, $AplikanID);
  }
  
// *** Functions ***

function Main($gel, $aplikanid)
{	$ButuhGanti = GetaField('aplikan', 'AplikanID', $aplikanid, 'PasswordBaru');
	//if($ButuhGanti == 'Y') CekGantiPassword($gel, $aplikanid);	
	Edit(0, $gel, $aplikanid);
}

function CekBolehAksesModul() {
  $arrAkses = array(1, 29);
  $key = array_search($_SESSION['_LevelID'], $arrAkses);
  if ($key === false)
    die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi Staf PMB untuk informasi lebih lanjut."));
}

function CekGantiPassword($gel, $aplikanid)
{	echo Konfirmasi("Password Baru", 
		"Anda harus mensetup password baru terlebih dahulu.<br>
		<br>
		<a href='#' onClick=\"location='?mnux=pmb/aplikanpassword'\">Setup Password Baru</a>");
}	

function BolehAksesData($aplikanid) {
  if ($_SESSION['_LevelID'] == 33 && $_SESSION['_Login'] != $aplikanid) {
    echo ErrorMsg('Peringatan',
      "Anda tidak boleh melihat data aplikan lain.<br />
      Anda hanya boleh mengakses data dari AplikanID <b>$aplikanid</b>.<br />
      Hubungi Staf PMB untuk informasi lebih lanjut");
    return false;
  } 
  else if(GetaField('pmb', "AplikanID='$aplikanid' and KodeID", KodeID, 'LulusUjian') == 'Y') {
    echo ErrorMsg('Gagal Akses',
      "Anda tidak dapat mengakses data dari AplikanID: <b>$aplikanid</b> lagi.<br />
	  Anda telah didaftarkan menjadi mahasiswa. <br />
      Hubungi Staf PMB untuk informasi lebih lanjut");
    return false;
  }
  else return true;
}

function GetOptionsFromData($sourceArray, $chosen)
	{	
			$optresult = "";
			if($chosen == '' or empty($chosen))	
			{ 	$optresult .= "<option value='' selected></option>"; }
			else { $optresult .= "<option value=''></option>"; }
			for($i=0; $i < count($sourceArray); $i++)
			{	if($chosen == $sourceArray[$i])
				{	$optresult .= "<option value='$sourceArray[$i]' selected>$sourceArray[$i]</option>"; }
				else
				{ 	$optresult .= "<option value='$sourceArray[$i]'>$sourceArray[$i]</option>"; }
			}
			return $optresult;
	}
	
function GetPilihan2($w, $da2, $da3) {
  $a = '';
  for ($i = 1; $i <= 3; $i++) {
    $opt = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['Pilihan'.$i], "KodeID='".KodeID."'", 'ProdiID');
    $da = ''; 
	$da = ($i == 2)? $da2 : $da;
	$da = ($i == 3)? $da3 : $da;
	$a .= "<tr>
      <td class=inp>Pilihan $i:</td>
      <td class=ul1 colspan=3>
      <select name='Pilihan$i' $da>$opt</select>
      </td>
      </tr>";
  }
  return $a;
}

function CekSudahProsesNIM($aplikan) {
  if (!empty($aplikan['MhswID'])) {
    die(ErrorMsg('Error',
      "Anda sudah tidak dapat mengubah data ini karena Cama sudah diproses menjadi mahasiswa.<br />
      Nomer Induk Mahasiswa-nya adalah: <b>$aplikan[MhswID]</b>.<br />
      Hubungi Sysadmin atau Bagian Administrasi Akademik untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='Tutup' value='Tutup Jendela Ini' onClick=\"window.close()\" />"));
  }
}

function Edit($md, $gel, $id) {
    $jdl = 'Edit Data Aplikan';
    $w = GetFields('aplikan', "AplikanID='$id' and KodeID", KodeID, '*');
    CekSudahProsesNIM($w);
    $_AplikanID = "<b>$w[AplikanID]</b>";
 
  echo <<<ESD
  <SCRIPT LANGUAGE="JavaScript1.2">
  function carisekolah(frm){
    lnk = "pmb/carisekolah.php?SekolahID="+frm.AsalSekolah.value+"&Cari="+frm.NamaSekolah.value;
	win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = frm;
  }
  function caript(frm){
    lnk = "pmb/cariperguruantinggi.php?SekolahID="+frm.AsalSekolah.value+"&Cari="+frm.NamaSekolah.value;
	win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = frm;
  }
  function ChooseThis(name, target)
  {	var count = data.JumlahSumberInfo.value;
	if(document.getElementById(name+target).checked == true)
	{	for(var i = 0; i < count; i++)
		{	document.getElementById(name+i).checked = false;
		}
		document.getElementById(name+target).checked = true;
	}
  }
  </script>
ESD;

  
  // Parameters
  $NamaSekolah = GetaField('asalsekolah', 'SekolahID', $w['AsalSekolah'], "concat(Nama, ', ', Kota)");
  $optkelamin = GetRadio("select Kelamin, Nama, concat(Kelamin, ' - ', Nama) as _kel from kelamin where NA='N'",
    'Kelamin', "_kel", 'Kelamin', $w[Kelamin], ', ');
  $TanggalLahir = GetDateOption($w['TanggalLahir'], 'TGL');
  $TanggalDaftar = GetDateOption($w['TanggalEdit'], 'TGLBuat');
  $optagama = GetOption2('agama', "concat(Agama, ' - ', Nama)", 'Agama', $w['Agama'], '', 'Agama');
  $optpendidikan = GetOption2('pendidikanortu', 'Nama', 'Pendidikan', $w['PendidikanAyah'], '', 'Pendidikan');
  $optpekerjaan = GetOption2('pekerjaanortu', 'Nama', 'Nama', $w['PekerjaanAyah'], '', 'Pekerjaan');
  $SumberInformasi = GetSumberInformasi($w['SumberInformasi']);
  $PilihanProgram = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $w['ProgramID'], '', 'ProgramID');
  $optpresenter = GetOption2('presenter', "concat(PresenterID, ' - ', Nama)", 'PresenterID', $w['PresenterID'], '', 'PresenterID');
  $sudahbekerjachecked = ($w['SudahBekerja'] == 'Y')? 'checked' : '';  
	
  // Tampilkan
  CheckFormScript("PresenterID,Program,Nama,Kelamin,AsalSekolah,TahunLulus,NilaiSekolah,TempatLahir");
  TampilkanJudul($jdl);

  echo <<<ESD
  <table class=bsc cellspacing=1 width=800>
  <form name='data' action='?mnux=$_SESSION[mnux]' method=POST onSubmit="return CheckForm(this)" />
  <input type=hidden name='gel' value='$gel' />
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='id' value='$id' />  
  
  <tr>
      <td class=inp>No. Peserta:</td>
      <td class=ul1>$_AplikanID</td>
	  
  <tr><td class=inp>Presenter: </td>
	  <td class=ul1><select name='PresenterID'>$optpresenter</select></td>
	  </tr>
  <tr><td bgcolor=green colspan=2 height=1></td></tr>
  
  <tr><td class=inp>Nama :</td>
      <td class=ul1>
      <input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50 />
      </td>
      </tr>
  <tr><td class=inp>Jenis Kelamin :</td>
      <td class=ul1>$optkelamin</td>
      </tr>
  <tr><td class=inp>Tempat Lahir :</td>
      <td class=ul1>
      <input type=text name='TempatLahir' value='$w[TempatLahir]' size=30 maxlength=50 />
      </td>
      </tr>
  <tr><td class=inp>Tanggal Lahir :</td>
      <td class=ul1>
      $TanggalLahir
      </td>
      </tr>
  <tr><td class=inp>Alamat :</td>
      <td class=ul1>
      <input type=text name='Alamat' value='$w[Alamat]' size=40 maxlength=200 />
      </td>
      </tr>
  <tr><td class=inp>Kota :</td>
      <td class=ul1>
      <input type=text name='Kota' value='$w[Kota]' size=30 maxlength=50 /> 
      </tr>
  <tr><td class=inp>Propinsi/Kode Pos: </td>
      <td class=ul1>
	  <input type=text name='Propinsi' value='$w[Propinsi]' size=30 maxlength=50 />/
      <input type=text name='KodePos' value='$w[KodePos]' size=10 maxlength=50 />
      </td>
      </tr>
  <tr><td class=inp>RT / RW :</td>
      <td class=ul1>
      <input type=text name='RT' value='$w[RT]' size=5 maxlength=5 /> /
      <input type=text name='RW' value='$w[RW]' size=5 maxlength=5 />
      </td>
      </tr>
  <tr><td class=inp>Telepon / Ponsel :</td>
      <td class=ul1>
      <input type=text name='Telepon' value='$w[Telepon]' size=20 maxlength=50 /> /
      <input type=text name='Handphone' value='$w[Handphone]' size=20 maxlength=50 />
      </td>
      </tr>
  <tr><td class=inp>E-mail :</td>
      <td class=ul1>
      <input type=text name='Email' value='$w[Email]' size=40 maxlength=50 />
      </td>
      </tr>
  
  <tr><th class=ttl colspan=2>Pendidikan Terakhir</th></tr>
  <tr><td class=inp>ID Sekolah :</td>
      <td class=ul1>
		<input type='text' name='AsalSekolah' value='$w[AsalSekolah]' size=20 maxlength=50 disabled>
		<input type='hidden' name='SavAsalSekolah' value='$w[AsalSekolah]' ></td>
	  </tr>
  <tr><td class=inp>Nama Sekolah :</td>
	  <td class=ul1><input type=text name='NamaSekolah' value='$NamaSekolah' size=50 maxlength=255>
	  <br><input type="button" onClick="javascript:data.AsalSekolah.value='';data.NamaSekolah.value='';" value="Reset" />
		<input type="button" onClick='javascript:carisekolah(data)' value="Cari Sekolah" />
		<input type="button" onClick='javascript:caript(data)' value="Cari Perguruan Tinggi" />
		<!--
		<input type="button" onClick='javascript:tambahsekolah(data)' value="Tambah Institusi" />
		-->
		<br /><br />
	    <div class="info"><b>TIPS</b>
		<ul>
		<li>Masukkan <b>nama sekolah parsial</b> diikuti dengan <b>tanda koma</b> dan <b>nama kota</b> di mana sekolah terebut berada. <br \>
			Contoh: 'Budi Utomo, Jakarta' ATAU 'Negeri 1, Surabaya'.
		</li>
		<li><b>Hindari penggunaan kata-kata umum</b>, seperti SMA, SMU, SLTA, SEKOLAH, SCHOOL, dll. <br \>
					  Gunakan kata-kata yang dapat mengidentifikasi sekolah yang dicari secara unik.
		</li>
		</ul>
		</div>
		</td></tr>
      </tr>
  <tr><td class=inp>Alamat Sekolah :</td>
	  <td class=ul1><input type=text name='AlamatSekolah' value='$w[AlamatSekolah]'></td></tr>
  <tr><td class=inp>Jurusan Sekolah :</td>
      <td class=ul1>
      <input type=text name='JurusanSekolah' value='$w[JurusanSekolah]' size=40 maxlength=50 />
      </td>
      </tr>
      
  <tr><td class=inp>Tahun Lulus :</td>
      <td class=ul1>
      <input type=text name='TahunLulus' value='$w[TahunLulus]' size=5 maxlength=5 />
      </td>
      </tr>
  <tr><td class=inp>Nilai UAN :</td>
      <td class=ul1>
      <input type=text name='NilaiSekolah' value='$w[NilaiSekolah]' size=5 maxlength=5 />
      </td>
      </tr>
  <tr><td class=inp>Sudah Bekerja?</td>
	  <td class=ul1>
		<input type=checkbox name='SudahBekerja' value='Y' $sudahbekerjachecked>
	  </td>
  </tr>
      
  <tr><th class=ttl colspan=2>Data Orangtua/Wali</th></tr>
  <tr><td class=inp>Nama Orangtua/Wali :</td>
      <td class=ul1>
      <input type=text name='NamaAyah' value='$w[NamaAyah]' size=30 maxlength=50 />
      </td>
      </tr>
  <tr><td class=inp>Pendidikan Terakhir :</td>
      <td class=ul1>
      <select name='PendidikanAyah'>$optpendidikan</select>
      </td>
      </tr>
  <tr><td class=inp>Pekerjaan :</td>
      <td class=ul1>
      <select name='PekerjaanAyah'>$optpekerjaan</select>
      </td>
      </tr>

  <tr><th class=ttl colspan=2>Pilihan Program Studi</th></tr>
ESD;
  
  $s1 = "select DISTINCT(j.JenjangID), j.Nama, j.Keterangan 
			from prodi p left outer join jenjang j on p.JenjangID=j.JenjangID
			where p.KodeID='".KodeID."' and p.NA='N' order by j.JenjangID DESC" ;
  $r1 = _query($s1);
  
  $_Jenjang = "928rlaisfav9sfap";
  while($w1 = _fetch_array($r1))
  {	
	$Pilihan = GetPilihanProdi($w1['JenjangID'], $w['ProdiID']);
	echo "<tr><td class=inp>&bull; $w1[Keterangan] ($w1[Nama]) :</td>
			  <td class=ul1>$Pilihan</td>
			  </tr>";
	
  }

  echo <<<ESD
  <tr><td class=inp>Program : </td>
	  <td class=ul1><select name='Program'>$PilihanProgram</select></td>
	  </tr>
  
  <tr><td class=inp valign=top>
      Atas inisiatif siapa Sdr/i datang ke<br />$Institusi?
      </td>
      <td class=ul1>
      <textarea name='Catatan' cols=30 rows=3>$w[Catatan]</textarea>
      </td>
      </tr>
  
  <tr><td class=inp valign=top>Sumber Informasi :</td>
      <td class=ul1>
      $SumberInformasi
      </td>
      </tr>
  
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='btnSimpan' value='Simpan' />
      <input type=reset name='btnReset' value='Reset' />
      </td>
      </tr>
  </form>
  </table>
ESD;
}

function Simpan($gel, $id) {
	include_once "statusaplikan.lib.php";

  $PresenterID = $_REQUEST['PresenterID'];
  $Nama = mysql_escape_string(sqling($_REQUEST['Nama']));
  $Kelamin = sqling($_REQUEST['Kelamin']);
  $TempatLahir = sqling($_REQUEST['TempatLahir']);
  $TanggalLahir = "$_REQUEST[TGL_y]-$_REQUEST[TGL_m]-$_REQUEST[TGL_d]";
  $TGLBuat = "$_REQUEST[TGLBuat_y]-$_REQUEST[TGLBuat_m]-$_REQUEST[TGLBuat_d]";
  $Agama = sqling($_REQUEST['Agama']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $RT = sqling($_REQUEST['RT']);
  $RW = sqling($_REQUEST['RW']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Handphone = sqling($_REQUEST['Handphone']);
  $Email = sqling($_REQUEST['Handphone']);
  $AsalSekolah = sqling($_REQUEST['SavAsalSekolah']);
  $AlamatSekolah = sqling($_REQUEST['AlamatSekolah']);
  $JurusanSekolah = sqling($_REQUEST['JurusanSekolah']);
  $TahunLulus = sqling($_REQUEST['TahunLulus']);
  $SudahBekerja = (!empty($_REQUEST['SudahBekerja']))? 'Y' : 'N';
  $NilaiSekolah = $_REQUEST['NilaiSekolah'];
  $NamaAyah = sqling($_REQUEST['NamaAyah']);
  $PendidikanAyah = sqling($_REQUEST['PendidikanAyah']);
  $PekerjaanAyah = sqling($_REQUEST['PekerjaanAyah']);
  $Program = $_REQUEST['Program'];
  $Catatan = sqling($_REQUEST['Catatan']);
  $SumberInfo = implode(',', $_REQUEST['sumberinfo']);
  // Simpan
  $Pilihan = $_REQUEST['Pilihan'];
  $ProdiID = "";
  foreach($Pilihan as $prodi)
  {	$ProdiID .= (empty($ProdiID))? $prodi : ','.$prodi;
  }
  // simpan
    $s = "update aplikan
      set PresenterID = '$PresenterID',
		  Nama = '$Nama', Kelamin = '$Kelamin', TempatLahir = '$TempatLahir', TanggalLahir = '$TanggalLahir',
          Agama = '$Agama', Alamat = '$Alamat', Kota = '$Kota', Propinsi = '$Propinsi', KodePos = '$KodePos', RT = '$RT', RW = '$RW',
          Telepon = '$Telepon', Handphone = '$Handphone', Email = '$Email',
          AsalSekolah = '$AsalSekolah', AlamatSekolah='$AlamatSekolah', JurusanSekolah = '$JurusanSekolah', TahunLulus = '$TahunLulus', 
		  SudahBekerja = '$SudahBekerja', NilaiSekolah = '$NilaiSekolah',
          NamaAyah = '$NamaAyah', PendidikanAyah = '$PendidikanAyah', PekerjaanAyah = '$PekerjaanAyah',
          ProdiID='$ProdiID', ProgramID = '$Program', 
          Catatan = '$Catatan', SumberInformasi = '$SumberInfo',
          LoginEdit = '$_SESSION[_Login]', TanggalEdit = '$TGLBuat'
      where AplikanID = '$id' ";
    $r = _query($s);
	SetStatusAplikan('APL', $id, $gel);
    BerhasilSimpan("?mnux=$_SESSION[mnux]", 1000);
  
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

function GetSumberInformasi($nilai) {
  $arrnilai = explode(',', $nilai);
  $ret = '';
  $s = "select * from sumberinfo where KodeID='".KodeID."' and NA='N' order by InfoID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $ck = (array_search($w['InfoID'], $arrnilai) === false)? '' : 'checked';
    $ret .= "<input type=checkbox id='sumberinfo$n' name='sumberinfo[]' value='$w[InfoID]' $ck> $w[Nama]<br />";
	$n++;
  }
  $ret .="<input type=hidden name='JumlahSumberInfo' value='$n'>";
  return $ret;
}


function GetPilihanProdi($jen, $pil) {
  $arr = explode(',', $pil);
  $ret = array();
  $s = "select ProdiID, Nama from prodi where KodeID='".KodeID."' and JenjangID='$jen' and NA='N' order by ProdiID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $ck = (array_search($w['ProdiID'], $arr) === false)? '' : 'checked';
    $ret[] = "<input type=checkbox name='Pilihan[]' value='$w[ProdiID]' $ck> $w[ProdiID]";
  }
  $_ret = implode('&nbsp;&nbsp;', $ret);
  return $_ret;
}
?>
