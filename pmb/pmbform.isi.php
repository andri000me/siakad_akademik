<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Formulir PMB");

// *** Parameters ***
$gel = sqling($_REQUEST['gel']);
$id = sqling($_REQUEST['id']);
$md = $_REQUEST['md']+0;


// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
//$gos = 'Simpan';
$gos($md, $gel, $id);
//$gos(1, 20092, 0);

// *** Functions ***

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
    if($i == 1) $opt = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['Pilihan'.$i], "KodeID='".KodeID."'", 'ProdiID', 0, 0);
	else $opt = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['Pilihan'.$i], "KodeID='".KodeID."'", 'ProdiID');
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

function CekSudahProsesNIM($pmb) {
  if (!empty($pmb['MhswID'])) {
    die(ErrorMsg('Error',
      "Anda sudah tidak dapat mengubah data ini karena Cama sudah diproses menjadi mahasiswa.<br />
      Nomer Pokok Mahasiswa-nya adalah: <b>$pmb[MhswID]</b>.<br />
      Hubungi Sysadmin atau Bagian Administrasi Akademik untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='Tutup' value='Tutup Jendela Ini' onClick=\"window.close()\" />"));
  }
}

function Edit($md, $gel, $id) {
  $sisfo = GetFields('identitas', 'Kode', KodeID, '*');
  if ($md == 0) {
    $jdl = 'Edit Data PMB';
    if ($_SESSION['_Login']!='auth0rized'){
    $w = GetFields('pmb p left outer join aplikan a on a.AplikanID=p.AplikanID and p.PMBID=a.PMBID', 'p.PMBID', $id, 'p.*, a.Jurusan,p.TanggalLahirIjazah');
  }else{
    $w = GetFields('pmb p left outer join aplikan a on a.AplikanID=p.AplikanID and p.PMBID=a.PMBID', 'p.PMBID', $id, 'p.*, a.Jurusan,p.TanggalLahirIjazah');
  }
    CekSudahProsesNIM($w);
    $JumlahPilihan = GetaField('pmbformulir', "PMBFormulirID = '$w[PMBFormulirID]' and KodeID", KodeID, 'JumlahPilihan');
	if(empty($JumlahPilihan)) $JumlahPilihan = 3;
	if($JumlahPilihan == 1) { $da2 = "disabled"; $da3 = "disabled"; }
	else if($JumlahPilihan == 2) { $da2 = ''; $da3 = "disabled"; }
	else { $da2 = ''; $da3 = ''; }
	$_PMBID = "<input type=hidden id='id' name='id' value='$w[PMBID]'><b>$w[PMBID]</b>";
  }
  elseif ($md == 1) {
    $jdl = 'Masukkan Data PMB';
    $w = array();
    $w['StatusAwalID'] = 'B';
    $w['TanggalLahir'] = date('Y-m-d');
    $_PMBID = "<font color=red>Auto-generated</font><input type=hidden id='id' name='id' value='$w[PMBID]'><b>$w[PMBID]</b>";
	$da2 = ''; $da3 = '';
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Parameters
  $TanggalLahir = GetDateOption($w['TanggalLahir'], 'TanggalLahir');
  $optfrm = GetOption2('pmbformulir', "concat(Nama, ' (', JumlahPilihan, ' pilihan)')", 'Nama', $w['PMBFormulirID'], "KodeID='".KodeID."'", 'PMBFormulirID');
  $optstawal = GetOption2('statusawal', "concat(StatusAwalID, ' - ', Nama)",
    'StatusAwalID', $w['StatusAwalID'], '', 'StatusAwalID');
  $optkel = GetOption2('kelamin', "concat(Kelamin, ' - ', Nama)", 'Kelamin', $w['Kelamin'], '', 'Kelamin');
  $optagm = GetOption2('agama', "concat(Agama, ' - ', Nama)", 'Agama', $w['Agama'], '', 'Agama');
  $optagamaayah = GetOption2('agama', "concat(Agama, ' - ', Nama)", 'Agama', $w['AgamaAyah'], '', 'Agama');
  $optagamaibu = GetOption2('agama', "concat(Agama, ' - ', Nama)", 'Agama', $w['AgamaIbu'], '', 'Agama');
  $optsipil = GetOption2('statussipil', "concat(StatusSipil, ' - ', Nama)", 'StatusSipil', $w['StatusSipil'], '', 'StatusSipil');
  $optpendidikan = GetOption2('pendidikanterakhir', "PendidikanTerakhir", 'Urutan', $w['PendidikanTerakhir'], '', 'PendidikanTerakhir');
  $optpendidikanayah = GetOption2('pendidikanortu', "concat(Pendidikan, '. ', Nama)", 'Pendidikan', $w['PendidikanAyah'], '', 'Pendidikan');
  $optpendidikanibu = GetOption2('pendidikanortu', "concat(Pendidikan, '. ', Nama)", 'Pendidikan', $w['PendidikanIbu'], '', 'Pendidikan');
  $opthidupayah = GetOption2('hidup', 'Nama', 'Hidup', $w['HidupAyah'], '', 'Hidup');
  $opthidupibu  = GetOption2('hidup', 'Nama', 'Hidup', $w['HidupIbu'], '', 'Hidup');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $w['ProgramID'], "KodeID='".KodeID."'", 'ProgramID');
  $optwarganegara = GetOption2('warganegara', "WargaNegara", 'WargaNegara', $w['WargaNegara'], '', 'WargaNegara');
  $Pilihan2 = GetPilihan2($w, $da2, $da3);
  $optpenghasilanayah = GetOption2('penghasilanortu', "Nama", 'Nama', $w['PenghasilanAyah'], '', 'Nama');
  $optpenghasilanibu = GetOption2('penghasilanortu', "Nama", 'Nama', $w['PenghasilanIbu'], '', 'Nama');
  $opttempattinggal = GetOption2('tempattinggal', "Nama", 'TempatTinggalID', $w['TempatTinggal'], '', ''); 
  $optbiayastudi = GetOption2('biayastudi', "Nama", 'BiayaStudiID', $w['BiayaStudi'], '', '');
  $arrPrestasiTambahan = explode('~', $w['PrestasiTambahan']);
  $NamaSekolah1 = GetaField('asalsekolah', 'SekolahID', $w['AsalSekolah'], "concat(Nama)");
  $JurusanSekolah = (empty($w['JurusanSekolah']))? GetaField('jurusansekolah','JurusanSekolahID',$w['Jurusan'],"concat(Nama, ' - ', NamaJurusan)"):$w['JurusanSekolah'];
  
 
  $opttempatkuliah = GetOption2('tempatkuliah', 'Nama', 'TempatKuliahID', $w['TempatKuliahID'], '', 'Nama', 0, 0);
  $s1 = "select PMBFormulirID, JumlahPilihan from pmbformulir where KodeID='".KodeID."' and NA='N'";
  $r1 = _query($s1);
  while($w1 = _fetch_array($r1))
  {	$hiddenformdata .= "<input type=hidden id='Form$w1[PMBFormulirID]' name='Form$w1[PMBFormulirID]' value='$w1[JumlahPilihan]' />";
  }
  // Tampilkan
  TampilkanJudul($jdl);
  
  echo "<script>
	function carinomor(frm)
	{	
		temp = frm.AplikanID.value;
		
		if(temp!='')
		{
				id = document.getElementById('id').value;
				lnk = '../$_SESSION[mnux].isi.cari.php?gel='+frmisi.gel.value+'&id='+id+'&n='+temp;
				win2 = window.open(lnk, '', 'width=900, height=600, scrollbars, status');
				win2.creator = self;
		}
		else
		{	alert('Masukkan nomor atau nama aplikan terlebih dahulu');
		}
	}
	
	function carisekolah(frm){
		lnk = 'carisekolah.php?SekolahID='+frm.AsalSekolah.value+'&Cari='+frm.NamaSekolah.value+'&frm='+frm.name;
		win2 = window.open(lnk, '', 'width=600, height=600, scrollbars, status');
		win2.creator = self;
	}
	function caript(frm){
		lnk = 'cariperguruantinggi.php?SekolahID='+frm.AsalSekolah.value+'&Cari='+frm.NamaSekolah.value+'&frm='+frm.name;
		win2 = window.open(lnk, '', 'width=600, height=600, scrollbars, status');
		win2.creator = self;
	}
	function tambahsekolah(frm) {
		lnk = 'pmbasalsek.edit.php?md=1';
		win2 = window.open(lnk, '', 'width=600, height=600, scrollbars, status');
		win2.creator = self;
	}
	function tambahpt(frm) {
		lnk = 'pmbasalpt.edit.php?md=1';
		win2 = window.open(lnk, '', 'width=600, height=600, scrollbars, status');
		win2.creator = self;
    }
  </script>
  ";
  
  CheckFormScript("Nama,TempatLahir,Kelamin,StatusAwalID, ProgramID, PMBFormulirID");
  JumlahPilihanScript();
  echo "<table class=bsc cellspacing=1 width=100%>
  <form name='frmisi' id='frmisi' action='../$_SESSION[mnux].isi.php' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='gel' value='$gel' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='gos' value='Simpan' />
  $hiddenformdata
  
  <tr>
      <td class=inp>No. Pendaftaran:</td>
      <td class=ul1>$_PMBID</td>
      <td class=inp>Status Cama:</td>
      <td class=ul1><select name='StatusAwalID'>$optstawal</select></td>
  </tr>
  <tr>
	  <td class=inp>No. Aplikan: </td>
	  <td class=ul1 colspan=3><input type=text id='aplikan' name='AplikanID' value='$w[AplikanID]'>
							  <input type=button name='CariAplikan' value='Cari Aplikan'
									onClick=\"carinomor(frmisi)\"/>
	  <br>
	  <font color=red>*) Dapat dicari melalui nomor aplikan atau nama aplikan.</font>
	  </td>
  </tr>
  
  <tr><th class=ttl colspan=4>Pilihan Program Studi</th></tr>
  <tr>
      <td class=inp>Formulir:</td>
      <td class=ul1 colspan=3>
        <select name='PMBFormulirID' onChange=\"UbahJumlahPilihan()\">$optfrm</select > <br />
        </td>
      </tr>
  <tr><td class=inp>Program Pendidikan:</td>
      <td class=ul1 colspan=3>
      <select name='ProgramID'>$optprg</select>
      </td></tr>	  
  $Pilihan2
  <tr><td class=inp>Pilihan Tempat Kuliah:</td>
	  <td class=ul1 colspan=3>
	  <select name='TempatKuliahID'>$opttempatkuliah</select>
	  </td></tr>
  
  <tr><th class=ttl colspan=4>Data Pribadi Cama</th></tr>
  <tr><td class=inp>Nama Lengkap:</td>
      <td class=ul1 colspan=3>
      <input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Warga Negara:</td>
      <td class=ul1><select name='WargaNegara'>$optwarganegara</select></td>
      <td class=inp>Kebangsaan:</td>
      <td class=ul1><input type=text name='Kebangsaan' value='$w[Kebangsaan]' size=20 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Tanggal Lahir:</td>
      <td class=ul1>$TanggalLahir<br />
	  <input type=text name='TanggalLahirIjazah' value='$w[TanggalLahirIjazah]' size=20 maxlength=40 /> (Ijazah)</td>
	  <td class=inp>Tempat Lahir:</td>
      <td class=ul1><input type=text name='TempatLahir' value='$w[TempatLahir]' size=20 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Jenis Kelamin:</td>
      <td class=ul1><select name='Kelamin'>$optkel</select></td>
      <td class=inp>Golongan Darah:</td>
      <td class=ul1><input style='text-transform: uppercase' type=text name='GolonganDarah' value='$w[GolonganDarah]' size=5 maxlength=10/></td>
      </tr>
  <tr><td class=inp>Agama:</td>
      <td class=ul1><select name='Agama'>$optagm</select></td>
      <td class=inp>Status Perkawinan:</td>
      <td class=ul1><select name='StatusSipil'>$optsipil</select></td>
      </tr>
  <tr><td class=inp>Tinggi Badan:</td>
      <td class=ul1><input type=text name='TinggiBadan' value='$w[TinggiBadan]' size=3 maxlength=5 /> cm</td>
      <td class=inp>Berat Badan:</td>
      <td class=ul1><input type=text name='BeratBadan' value='$w[BeratBadan]' size=3 maxlength=5 /> kg</td>
      </tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan Cepat' />
      <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
      </td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr><th class=ttl colspan=4>Pendidikan Terakhir:</th></tr>
  <tr><td class=inp>Pendidikan Terakhir:</td>
      <td class=ul1><select name='PendidikanTerakhir'>$optpendidikan</select></td>
	  <td class=inp>Jurusan Sekolah :</td>
      <td class=ul1>
      <input type=text name='JurusanSekolah' value='$JurusanSekolah' size=40 maxlength=50 />
      </td>
  </tr>
  <tr><td class=inp>ID Sekolah :</td>
      <td class=ul1>
		<input type='text' name='AsalSekolah' value='$w[AsalSekolah]' size=20 maxlength=50 disabled>
		<input type='hidden' name='SavAsalSekolah' value='$w[AsalSekolah]' ></td>
	  </tr>
  <tr><td class=inp>Nama Sekolah :</td>
	  <td class=ul1 colspan=3><input type=text name='NamaSekolah' value='$NamaSekolah1' size=50 maxlength=255>
	    <a class=\"info\" href=\"javascript:void(0)\"><font size=0.8>Tips: </font><span>Masukkan <b>nama sekolah parsial</b> diikuti dengan <b>tanda koma</b> dan <b>nama kota</b> di mana sekolah terebut berada. <br \>
					  Contoh: 'Budi Utomo, Jakarta' ATAU 'Negeri 1, Surabaya'.<br \><br \>
					  <b>TIPS: Hindari penggunaan kata-kata umum</b>, seperti SMA, SMU, SLTA, SEKOLAH, SCHOOL, dll. <br \>
					  Gunakan kata-kata yang dapat mengidentifikasi sekolah yang dicari secara unik.</span></a>
		<br><a href='#self' onClick=\"javascript:frmisi.AsalSekolah.value='';frmisi.NamaSekolah.value='';\">Reset</a> &bull;
		<a href='javascript:carisekolah(frmisi)'>Cari Sekolah</a> &bull; 
		<a href='javascript:caript(frmisi)'>Cari Perguruan Tinggi</a> &bull; 
		<a href='javascript:tambahsekolah(frmisi)'>Tambah Sekolah</a> &bull;
		<a href='javascript:tambahpt(frmisi)'>Tambah Perguruan Tinggi</a>
		</td></tr>
  </tr>
  <tr><td class=inp>Alamat Sekolah :</td>
	  <td class=ul1 colspan=3><input type=text name='AlamatSekolah' value='$w[AlamatSekolah]' size=50 maxlength=255></td>
	  </tr>
	
  <tr><td class=inp>Tahun Lulus :</td>
      <td class=ul1>
      <input type=text name='TahunLulus' value='$w[TahunLulus]' size=5 maxlength=5 />
      </td>
	  <td class=inp>Nilai UAN :</td>
      <td class=ul1>
      <input type=text name='NilaiSekolah' value='$w[NilaiSekolah]' size=5 maxlength=5 />
      </td>
      </tr>
  <tr><td class=inp>Prestasi Lainnya:</td>
	  <td class=ul1 colspan=3><input type=text name='PrestasiTambahan1' value='$arrPrestasiTambahan[0]' size=80 maxlength=100 ></td>
  </tr>
  <tr><td></td>
	  <td class=ul1 colspan=3><input type=text name='PrestasiTambahan2' value='$arrPrestasiTambahan[1]' size=80 maxlength=100></td>
  </tr>
  <tr><td></td>
      <td class=ul1 colspan=3><input type=text name='PrestasiTambahan3' value='$arrPrestasiTambahan[2]' size=80 maxlength=100></br>
	  *) <i>Prestasi dapat berupa prestasi apa saja di bidang <b>seni</b>, <b>olahraga</b>, atau <b>akademik</b>. </i></br>
	  *) <i>Harap menuliskan <b>peringkat</b>, <b>nama lengkap kompetisi/event</b> dan <b>tahun</b> peraihan prestasi bila memungkinkan<i> 
	  </td>
  </tr>
  
  <tr><th class=ttl colspan=4>Alamat</th></tr>
  <tr><td class=inp>Tempat Tinggal:</td>
	  <td class=ul1 colspan=3><select name='TempatTinggal'>$opttempattinggal</select></td></tr>
  <tr><td class=inp>Alamat lengkap:</td>
      <td class=ul1 colspan=3>
      <textarea name='Alamat' cols=70 rows=4>$w[Alamat]</textarea>
      </td></tr>
  <tr><td class=inp>RT/RW:</td>
      <td class=ul1><input type=text name='RT' value='$w[RT]' size=10 maxlength=10 />
      / <input type=text name='RW' value='$w[RW]' size=10 maxlength=10 /></td>
      <td class=inp>Kode Pos:</td>
      <td class=ul1><input type=text name='KodePos' value='$w[KodePos]' size=10 maxlength=10 /></td>
      </tr>
  <tr><td class=inp>Kota/Kabupaten:</td>
      <td class=ul1><input type=text name='Kota' value='$w[Kota]' size=20 maxlength=50 /></td>
      <td class=inp>Propinsi:</td>
      <td class=ul1><input type=text name='Propinsi' value='$w[Propinsi]' size=20 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Telpon/ponsel:</td>
      <td class=ul1>
        <input type=text name='Telepon' value='$w[Telepon]' size=10 maxlength=50 /> /
        <input type=text name='Handphone' value='$w[Handphone]' size=10 maxlength=50 />
      </td>
      <td class=inp>E-mail:</td>
      <td class=ul1>
        <input type=text name='Email' value='$w[Email]' size=20 maxlength=50 />
      </td></tr>
  
  <tr><th class=ttl colspan=4>Data Orangtua</th></tr>
  <tr><td class=inp>Nama Ayah:</td>
      <td class=ul1><input type=text name='NamaAyah' value='$w[NamaAyah]' size=20 maxlength=50 /></td>
      <td class=inp>Nama Ibu:</td>
      <td class=ul1><input type=text name='NamaIbu' value='$w[NamaIbu]' size=20 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Agama Ayah:</td>
      <td class=ul1><select name='AgamaAyah' />$optagamaayah</td>
      <td class=inp>Agama Ibu:</td>
      <td class=ul1><select name='AgamaIbu' />$optagamaibu</td>
      </tr>
  <tr><td class=inp>Pendidikan Ayah:</td>
      <td class=ul1><select name='PendidikanAyah'>$optpendidikanayah</select></td>
      <td class=inp>Pendidikan Ibu:</td>
      <td class=ul1><select name='PendidikanIbu'>$optpendidikanibu</select></td>
      </tr>
  <tr><td class=inp>Pekerjaan Ayah:</td>
      <td class=ul1><input type=text name='PekerjaanAyah' value='$w[PekerjaanAyah]' size=20 maxlength=50 /></td>
      <td class=inp>Pekerjaan Ibu:</td>
      <td class=ul1><input type=text name='PekerjaanIbu' value='$w[PekerjaanIbu]' size=20 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Penghasilan/bulan:</td>
      <td class=ul1><select name='PenghasilanAyah'>$optpenghasilanayah</select></td>
      <td class=inp>Penghasilan/bulan:</td>
      <td class=ul1><select name='PenghasilanIbu'>$optpenghasilanibu</select></td>
      </tr>
  <tr><td class=inp>Status Kehidupan:</td>
      <td class=ul1><select name='HidupAyah'>$opthidupayah</select></td>
      <td class=inp>Status kehidupan:</td>
      <td class=ul1><select name='HidupIbu'>$opthidupibu</select></td>
      </tr>
  <tr><td class=inp>Biaya Studi:</td>
	  <td class=ul1 colspan=3><select name='BiayaStudi'>$optbiayastudi</select></td>
	  </tr>
  
  <tr><th class=ttl colspan=4>Alamat Orangtua</th></tr>
  <tr><td class=inp>Alamat lengkap:</td>
      <td class=ul1 colspan=3>
      <textarea name='AlamatOrtu' cols=70 rows=4>$w[AlamatOrtu]</textarea>
      </td></tr>
  <tr><td class=inp>RT/RW:</td>
      <td class=ul1><input type=text name='RTOrtu' value='$w[RTOrtu]' size=10 maxlength=10 />
      / <input type=text name='RWOrtu' value='$w[RWOrtu]' size=10 maxlength=10 /></td>
      <td class=inp>Kode Pos:</td>
      <td class=ul1><input type=text name='KodePosOrtu' value='$w[KodePosOrtu]' size=10 maxlength=10 /></td>
      </tr>
  <tr><td class=inp>Kota:</td>
      <td class=ul1><input type=text name='KotaOrtu' value='$w[KotaOrtu]' size=20 maxlength=50 /></td>
      <td class=inp>Propinsi:</td>
      <td class=ul1><input type=text name='PropinsiOrtu' value='$w[PropinsiOrtu]' size=20 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Telpon/ponsel:</td>
      <td class=ul1>
        <input type=text name='TeleponOrtu' value='$w[TeleponOrtu]' size=10 maxlength=50 /> /
        <input type=text name='HandphoneOrtu' value='$w[HandphoneOrtu]' size=10 maxlength=50 />
      </td>
      <td class=inp>E-mail:</td>
      <td class=ul1>
        <input type=text name='EmailOrtu' value='$w[EmailOrtu]' size=20 maxlength=50 />
      </td></tr>
  
  <tr><th class=ttl colspan=4>Detail Pekerjaan (Jika sudah bekerja)</th></tr>
  <tr><td class=inp>Nama Perusahaan:</td>
      <td class=ul1 colspan=3>
      <input type=text name='NamaPerusahaan' value='$w[NamaPerusahaan]' size=30/></td></tr>
  <tr><td class=inp>Alamat Perusahaan:</td>
      <td class=ul1 colspan=3><input type=text name='AlamatPerusahaan' value='$w[AlamatPerusahaan]' size=50/></td>
      </tr>
  <tr><td class=inp>No Telp. dan Fax</td>
      <td class=ul1><input type=text name='TeleponPerusahaan' value='$w[TeleponPerusahaan]' size=30/></td>
      </tr>
  <tr><td class=inp>Jabatan Saat Ini:</td>
      <td class=ul1><input type=text name='JabatanPerusahaan' value='$w[JabatanPerusahaan]' size=20s></td>
      </tr>
  
  
  <tr><td class=ul1 colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
      </td></tr>
  
  </form>
  </table>";
}

function Simpan($md, $gel, $id)
{
  include_once "statusaplikan.lib.php";
  $AplikanID = $_REQUEST['AplikanID'];
  
	if($AplikanID == '' or empty($AplikanID))
	{	die(ErrorMsg('Error',
			  "Nomer Aplikan ID kosong<br />
			  Anda harus memasukkan nomer AplikanID yang benar.<br />
			  Hubungi Sysadmin untuk informasi lebih lanjut.
			  <hr size=1 color=silver />
			  Opsi: <input type=button name='Kembali' value='Kembali'
				onClick=\"javascript:history.go(-1)\" />
				<input type=button name='Tutup' value='Tutup'
				onClick=\"window.close()\" />"));
	}
	else
	{ $sss = "select Nama from `aplikan` where AplikanID='$AplikanID'";
	  $rrr = _query($sss);
	  $nnn = _num_rows($rrr);
	  
	  if($nnn == 0)
	  {	die(ErrorMsg('Error',
			  "Nomer Aplikan ID tidak diketemukan di dalam database<br />
			  Anda harus memasukkan nomer AplikanID yang benar.<br />
			  Hubungi Sysadmin untuk informasi lebih lanjut.
			  <hr size=1 color=silver />
			  Opsi: <input type=button name='Kembali' value='Kembali'
				onClick=\"javascript:history.go(-1)\" />
				<input type=button name='Tutup' value='Tutup'
				onClick=\"window.close()\" />"));
	  }
	  else if($nnn > 1)
	  {	die(ErrorMsg('Error',
			  "Ditemukan Aplikan ID yang dobel. Harap dicek terlebih dahulu<br />
			  Hubungi Sysadmin untuk informasi lebih lanjut.
			  <hr size=1 color=silver />
			  Opsi: <input type=button name='Kembali' value='Kembali'
				onClick=\"javascript:history.go(-1)\" />
				<input type=button name='Tutup' value='Tutup'
				onClick=\"window.close()\" />"));
	  
	  }
	  else
	  {
  
	  $Nama = sqling($_REQUEST['Nama']);
	  $StatusAwalID = sqling($_REQUEST['StatusAwalID']);
	  $TempatLahir = sqling($_REQUEST['TempatLahir']);
	  $TanggalLahirIjazah = sqling($_REQUEST['TanggalLahirIjazah']);
	  $Kelamin = sqling($_REQUEST['Kelamin']);
	  $TanggalLahir = "$_REQUEST[TanggalLahir_y]-$_REQUEST[TanggalLahir_m]-$_REQUEST[TanggalLahir_d]";
	  $GolonganDarah = sqling($_REQUEST['GolonganDarah']);
	  $Agama = sqling($_REQUEST['Agama']);
	  $StatusSipil = sqling($_REQUEST['StatusSipil']);
	  $TinggiBadan = sqling($_REQUEST['TinggiBadan']);
	  $BeratBadan = sqling($_REQUEST['BeratBadan']);
	  $WargaNegara = sqling($_REQUEST['WargaNegara']);
	  $Kebangsaan = sqling($_REQUEST['Kebangsaan']);
	  $TempatTinggal = $_REQUEST['TempatTinggal'];
	  $Alamat = sqling($_REQUEST['Alamat']);
	  $RT = sqling($_REQUEST['RT']);
	  $RW = sqling($_REQUEST['RW']);
	  $KodePos = sqling($_REQUEST['KodePos']);
	  $Kota = sqling($_REQUEST['Kota']);
	  $Propinsi = sqling($_REQUEST['Propinsi']);
	  $Telepon = sqling($_REQUEST['Telepon']);
	  $Handphone = sqling($_REQUEST['Handphone']);
	  $Email = sqling($_REQUEST['Email']);
	  $JarakRumah = $_REQUEST['JarakRumah'];
	  $PendidikanTerakhir = sqling($_REQUEST['PendidikanTerakhir']);
	  $AsalSekolah = sqling($_REQUEST['SavAsalSekolah']);
	  $AlamatSekolah = $_REQUEST['AlamatSekolah'];
	  $JurusanSekolah = $_REQUEST['JurusanSekolah'];
	  $TahunLulus = sqling($_REQUEST['TahunLulus']);
	  $NilaiSekolah = sqling($_REQUEST['NilaiSekolah']);
	  $arrPT = array();
	  for($i = 1; $i <= 3; $i++)
	  {	$arrPT[] = str_replace('~', '-', sqling($_REQUEST['PrestasiTambahan'.$i]));
	  }
	  foreach($arrPT as $PT)
	  {	$PrestasiTambahan = implode('~', $arrPT);
	  }
	  $NamaAyah = sqling($_REQUEST['NamaAyah']);
	  $AgamaAyah = $_REQUEST['AgamaAyah'];
	  $PendidikanAyah = $_REQUEST['PendidikanAyah'];
	  $PekerjaanAyah = sqling($_REQUEST['PekerjaanAyah']);
	  $AlamatAyah = sqling($_REQUEST['AlamatAyah']);
	  $PenghasilanAyah = $_REQUEST['PenghasilanAyah'];
	  $HidupAyah = $_REQUEST['HidupAyah'];
	  
	  $NamaIbu = sqling($_REQUEST['NamaIbu']);
	  $AgamaIbu = $_REQUEST['AgamaIbu'];
	  $PendidikanIbu = $_REQUEST['PendidikanIbu'];
	  $PekerjaanIbu = sqling($_REQUEST['PekerjaanIbu']);
	  $AlamatIbu = sqling($_REQUEST['AlamatIbu']);
	  $PenghasilanIbu = $_REQUEST['PenghasilanIbu'];
	  $HidupIbu = $_REQUEST['HidupIbu'];
	  $BiayaStudi = $_REQUEST['BiayaStudi'];
	  
	  $AlamatOrtu = sqling($_REQUEST['AlamatOrtu']);
	  $RTOrtu = sqling($_REQUEST['RTOrtu']);
	  $RWOrtu = sqling($_REQUEST['RWOrtu']);
	  $KodePosOrtu = sqling($_REQUEST['KodePosOrtu']);
	  $KotaOrtu = sqling($_REQUEST['KotaOrtu']);
	  $PropinsiOrtu = sqling($_REQUEST['PropinsiOrtu']);
	  $TeleponOrtu = sqling($_REQUEST['TeleponOrtu']);
	  $HandphoneOrtu = sqling($_REQUEST['HandphoneOrtu']);
	  $EmailOrtu = sqling($_REQUEST['EmailOrtu']);
	  
	  $NamaPerusahaan = $_REQUEST['NamaPerusahaan'];
	  $AlamatPerusahaan = $_REQUEST['AlamatPerusahaan'];
	  $TeleponPerusahaan = $_REQUEST['TeleponPerusahaan'];
	  $JabatanPerusahaan = $_REQUEST['JabatanPerusahaan'];
	  
	  $PMBFormulirID = $_REQUEST['PMBFormulirID'];
	  $ProgramID = sqling($_REQUEST['ProgramID']);
	  $TempatKuliahID = $_REQUEST['TempatKuliahID'];
	  $UangKesehatan = $_REQUEST['UangKesehatan'];
	  $UkuranJaket = $_REQUEST['UkuranJaket'];
	  
	  $frm = GetFields('pmbformulir', 'PMBFormulirID', $PMBFormulirID, '*');
	  $pil = array();
	  $vpil = array();
	  $epil = array();
	  for ($i = 1; $i <= $frm['JumlahPilihan']; $i++) {
		$pil[] = 'Pilihan'.$i;
		$vpil[] = "'".sqling($_REQUEST['Pilihan'.$i])."'";
		$epil[] = 'Pilihan'.$i."='".$_REQUEST['Pilihan'.$i]."'";
	  }
	  $_pil = implode(', ', $pil);
	  $_vpil = implode(', ', $vpil);
	  $_epil = implode(', ', $epil);
	  
	  TutupScript();
	  // simpan
	  if ($md == 0) {
		$s = "update pmb
		  set StatusAwalID = '$StatusAwalID',
			  Nama = '$Nama',
			  TempatLahir = '$TempatLahir', TanggalLahir = '$TanggalLahir', TanggalLahirIjazah='$TanggalLahirIjazah',
			  Kelamin = '$Kelamin', GolonganDarah = '$GolonganDarah',
			  Agama = '$Agama', StatusSipil = '$StatusSipil',
			  TinggiBadan = '$TinggiBadan', BeratBadan = '$BeratBadan',
			  WargaNegara = '$WargaNegara', Kebangsaan = '$Kebangsaan',
			  TempatTinggal = '$TempatTinggal', Alamat = '$Alamat',
			  RT = '$RT', RW = '$RW', KodePos = '$KodePos',
			  Kota = '$Kota', Propinsi = '$Propinsi',
			  Telepon = '$Telepon', Handphone = '$Handphone', Email = '$Email',
			  PendidikanTerakhir = '$PendidikanTerakhir',
			  AsalSekolah = '$AsalSekolah', AlamatSekolah = '$AlamatSekolah', TahunLulus = '$TahunLulus', 
			  NilaiSekolah = '$NilaiSekolah', PrestasiTambahan = '$PrestasiTambahan',
			  NamaAyah = '$NamaAyah', AgamaAyah = '$AgamaAyah', PendidikanAyah = '$PendidikanAyah', 
			  PekerjaanAyah = '$PekerjaanAyah', HidupAyah = '$HidupAyah', PenghasilanAyah = '$PenghasilanAyah',
			  NamaIbu = '$NamaIbu', AgamaIbu = '$AgamaIbu', PendidikanIbu = '$PendidikanIbu', 
			  PekerjaanIbu = '$PekerjaanIbu', HidupIbu = '$HidupIbu', PenghasilanIbu = '$PenghasilanIbu',
			  BiayaStudi = '$BiayaStudi',
			  
			  AlamatOrtu = '$AlamatOrtu',
			  RTOrtu = '$RTOrtu', RWOrtu = '$RWOrtu', KodePosOrtu = '$KodePosOrtu',
			  KotaOrtu = '$KotaOrtu', PropinsiOrtu = '$PropinsiOrtu',
			  TeleponOrtu = '$TeleponOrtu', HandphoneOrtu = '$HandphoneOrtu', EmailOrtu = '$EmailOrtu',
			  PMBFormulirID = '$PMBFormulirID', ProgramID = '$ProgramID', TempatKuliahID='$TempatKuliahID',
			  
			  NamaPerusahaan = '$NamaPerusahaan', AlamatPerusahaan = '$AlamatPerusahaan',
			  TeleponPerusahaan = '$TeleponPerusahaan', JabatanPerusahaan = '$JabatanPerusahaan',
			  UangKesehatan = '$UangKesehatan',UkuranJaket='$UkuranJaket',
			  $_epil,
			  LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
		  where PMBID = '$id' ";
		$r = _query($s);
		SetStatusAplikan('DFT', $AplikanID, $gel);
		
		echo "<script>ttutup()</script>";
	  }
	  elseif ($md == 1) {
		// Cek jika ID manual
		if (!empty($id)) {
		  $ada = GetaField('pmb', "KodeID='".KodeID."' and PMBID", $id, 'PMBID');
		  if (!empty($ada)) {
			die(ErrorMsg('Error',
			  "Nomer PMB sudah ada.<br />
			  Anda harus memasukkan nomer PMB yang lain.<br />
			  Atau kosongkan untuk mendapatkan nomer secara otomatis.<br />
			  Hubungi Sysadmin untuk informasi lebih lanjut.
			  <hr size=1 color=silver />
			  Opsi: <input type=button name='Kembali' value='Kembali'
				onClick=\"javascript:history.go(-1)\" />
				<input type=button name='Tutup' value='Tutup'
				onClick=\"window.close()\" />"));
		  }
		}
		// Jika menggunakan penomeran otomatis
		else {
		  $id = GetNextPMBIDFromGel($gel);
		}
		
		$FormulirID = GetaField('aplikan', 'AplikanID', $AplikanID, 'PMBFormulirID');
		
		if(empty($FormulirID) or $FormulirID == '' or $FormulirID == NULL)
		{	echo Konfirmasi("Gagal", "Aplikan belum membeli formulir.<br> Data tidak disimpan. <br>
								<input type=button name='Kembali' value='Kembali'
									onClick=\"javascript:history.go(-1)\" />");
		}
		else
		{
			// Baru kemudian disimpan
			$PMBFormJualID = GetaField('aplikan', 'AplikanID', $AplikanID, 'PMBFormJualID');
			$s = "insert into pmb
			  (PMBID, AplikanID, PMBPeriodID, KodeID, StatusAwalID, Nama, 
			  TempatLahir, TanggalLahir, TanggalLahirIjazah, Kelamin, GolonganDarah,
			  Agama, StatusSipil, TinggiBadan, BeratBadan,
			  WargaNegara, Kebangsaan,
			  TempatTinggal, Alamat, RT, RW, KodePos, Kota, Propinsi, 
			  Telepon, Handphone, Email,
			  PendidikanTerakhir, AsalSekolah, AlamatSekolah, 
			  TahunLulus, NilaiSekolah, PrestasiTambahan, 
			  NamaAyah, AgamaAyah, PendidikanAyah, PekerjaanAyah, HidupAyah, PenghasilanAyah,
			  NamaIbu, AgamaIbu, PendidikanIbu, PekerjaanIbu, HidupIbu, PenghasilanIbu, BiayaStudi, 
			  AlamatOrtu, RTOrtu, RWOrtu, KodePosOrtu, KotaOrtu, PropinsiOrtu,
			  TeleponOrtu, HandphoneOrtu, EmailOrtu,
			  NamaPerusahaan, AlamatPerusahaan, TeleponPerusahaan, JabatanPerusahaan,UangKesehatan,UkuranJaket,
			  PMBFormulirID, ProgramID, ProdiID, TempatKuliahID, $_pil,
			  
			  LoginBuat, TanggalBuat)
			  values
			  ('$id', '$AplikanID', '$gel', '".KodeID."', '$StatusAwalID', '$Nama', 
			  '$TempatLahir', '$TanggalLahir', '$TanggalLahirIjazah', '$Kelamin', '$GolonganDarah',
			  '$Agama', '$StatusSipil', '$TinggiBadan', '$BeratBadan',
			  '$WargaNegara', '$Kebangsaan',
			  '$TempatTinggal', '$Alamat', '$RT', '$RW', '$KodePos', '$Kota', '$Propinsi', 
			  '$Telepon', '$Handphone', '$Email',
			  '$PendidikanTerakhir', '$AsalSekolah', '$AlamatSekolah',
			  '$TahunLulus', '$NilaiSekolah', '$PrestasiTambahan', 
			  '$NamaAyah', '$AgamaAyah', '$PendidikanAyah', '$PekerjaanAyah', '$HidupAyah', '$PenghasilanAyah', 
			  '$NamaIbu', '$AgamaIbu', '$PendidikanIbu', '$PekerjaanIbu', '$HidupIbu', '$PenghasilanIbu', '$BiayaStudi',
			  '$AlamatOrtu', '$RTOrtu', '$RWOrtu', '$KodePosOrtu', '$KotaOrtu', '$PropinsiOrtu',
			  '$TeleponOrtu', '$HandphoneOrtu', '$EmailOrtu',
			  '$NamaPerusahaan', '$AlamatPerusahaan', '$TeleponPerusahaan', '$JabatanPerusahaan','$UangKesehatan','$UkuranJaket',
			  '$PMBFormulirID', '$ProgramID', '$ProdiID', '$TempatKuliahID', $_vpil,
			  
			  '$_SESSION[_Login]', now())";
			$r = mysql_query($s) or die(mysql_error());
			
			$s = "update aplikan set PMBID='$id' where AplikanID='$AplikanID'";
			$r = _query($s);
			
			SetStatusAplikan('DFT', $AplikanID, $gel);
			
			if($md == 1)
			  {	
				echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=PilihKursi&md=$md&gel=$gel&id=$id'</script>";
			  }
		  }
		}
		  else {
			die(ErrorMsg('Error',
			  "Terjadi kesalahan mode edit.<br />
			  Mode <b>$md</b> tidak dikenali oleh sistem.
			  <hr size=1 color=silver />
			  <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
		  }
		}  
    }
}

function PilihKursi($md, $gel, $id)
{	
	if($md == 1)
	{	$prodistring = ''; $arrProdi = array();
		$pmb = GetFields('pmb p left outer join pmbformulir pf on p.PMBFormulirID=pf.PMBFormulirID', "p.PMBID='$id' and p.KodeID", KodeID, "p.Pilihan1, p.Pilihan2, p.Pilihan3, p.PMBID, p.Nama, p.ProgramID, pf.USM, pf.Wawancara, pf.Nama as _NamaForm");
		for($i = 1; $i <= 3; $i++) $arrProdi[] = $pmb["Pilihan$i"];
		foreach ($arrProdi as $key => $value) {
		  if (is_null($value) || $value=="") unset($arrProdi[$key]);
		}
		$arrProdi = array_unique($arrProdi);
		
		foreach($arrProdi as $perprodi)
		{	$prodistring .= (empty($prodistring))? "$perprodi": " / $perprodi"; 
		}
		echo "<p><table class=box cellspacing=2 cellpadding=4 width=500 align=center>
				  <tr><td class=inp width=200>PMBID:</td>
					 <td class=ul><b>$pmb[PMBID]</b></td></tr>
				  <tr><td class=inp>Nama:</td>
					  <td class=ul><b>$pmb[Nama]</b></td></tr>
				  <tr><td class=inp>Program:</td>
					  <td class=ul><b>$pmb[ProgramID]</b></td></tr>
				  <tr><td class=inp>Program Studi Pilihan:</td>
					  <td class=ul><b>$prodistring</b.</td></tr>
			  </table></p>";
		$n = 0;
		if($pmb['USM'] == 'Y')
		{
			$prodistring = ''; 
			foreach($arrProdi as $perprodi)
			{	$prodistring .= (empty($prodistring))? "(INSTR(concat('|', ProdiID, '|'), concat('|', '$perprodi', '|'))" :
														" OR INSTR(concat('|', ProdiID, '|'), concat('|', '$perprodi', '|'))";
			}
			$prodistring .= ')';
			$s = "select ProdiUSMID from prodiusm where KodeID='".KodeID."' and PMBPeriodID='$gel' and 
					$prodistring";
			$r = _query($s);
			while($w = _fetch_array($r))
			{	
				$n++;
				echo  "<Iframe name='frame$n' src='../$_SESSION[mnux].frame.php?PMBID=$id&ProdiUSMID=$w[ProdiUSMID]&gel=$gel' align=center width=99% height=750 frameborder=0></Iframe>";
			}
		}
		else
		{	echo "<font size=2><b>Tidak ada USM yang dijadwalkan untuk Formulir $pmb[_NamaForm].</b></font>&nbsp;&nbsp;<input type=button name='Tutup' value='Tutup' onClick=\"window.close()\"";
		}
		
		if($pmb['Wawancara'] == 'Y')
		{	/*$prodistring = ''; 
			foreach($arrProdi as $perprodi)
			{	$prodistring .= (empty($prodistring))? "(INSTR(concat('|', ProdiID, '|'), concat('|', '$perprodi', '|'))" :
														" OR INSTR(concat('|', ProdiID, '|'), concat('|', '$perprodi', '|'))";
			}
			$prodistring .= ')';
			$s = "select WawancaraUSMID from wawancarausm where KodeID='".KodeID."' and PMBPeriodID='$gel' and 
					$prodistring";
			$r = _query($s);
			while($w = _fetch_array($r))
			{*/	
				$n++;
				echo  "<Iframe name='frame$n' src='../$_SESSION[mnux].framewawancara.php?PMBID=$id&gel=$gel' align=center width=99% height=500 frameborder=0></Iframe>";
			//}
		}
		else
		{	echo "<font size=2><b>Tidak ada Wawancara yang dijadwalkan untuk Formulir $pmb[_NamaForm].</b></font>&nbsp;&nbsp;<input type=button name='Tutup' value='Tutup' onClick=\"window.close()\"";
		}
	}
}

function JumlahPilihanScript()
{	echo <<<SCR
	<SCRIPT>
		function UbahJumlahPilihan()
		{	id = frmisi.PMBFormulirID.value;
			var jmlpil = 0;
			
			if(id != '')
			{	jmlpil = document.getElementById('Form'+id).value;
			}
			else jmlpil = 3;
			
			if(jmlpil <= 1)
			{	frmisi.Pilihan2.value = '';
				frmisi.Pilihan2.disabled = true;
				frmisi.Pilihan3.value = '';
				frmisi.Pilihan3.disabled = true;
			}
			else if(jmlpil <=2)
			{	frmisi.Pilihan2.disabled = false;
				frmisi.Pilihan3.value = '';
				frmisi.Pilihan3.disabled = true;
			}
			else
			{	frmisi.Pilihan2.disabled = false;
				frmisi.Pilihan3.disabled = false;
			}
		}
	</SCRIPT>

SCR;
}

function GetNextPMBIDFromGel($gel) {
  $gelombang = GetFields('pmbperiod', "PMBPeriodID='$gel' and KodeID", KodeID, "FormatNoPMB, DigitNoPMB");
  // Buat nomer baru
  $nomer = str_pad('', $gelombang['DigitNoPMB'], '_', STR_PAD_LEFT);
  $nomer = $gelombang['FormatNoPMB'].$nomer;
  $akhir = GetaField('pmb',
    "PMBID like '$nomer' and KodeID", KodeID, "max(PMBID)");
  $nmr = str_replace($gelombang['FormatNoPMB'], '', $akhir);
  $nmr++;
  $baru = str_pad($nmr, $gelombang['DigitNoPMB'], '0', STR_PAD_LEFT);
  $baru = $gelombang['FormatNoPMB'].$baru;
  return $baru;
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&_pmbPage=0';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}
?>
