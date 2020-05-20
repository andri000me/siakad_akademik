<?php

$mhswbck = GetSetVar('mhswbck');

// *** Functions ***
function TampilkanHeaderMhsw($w) {
  $foto = FileFotoMhsw($w['MhswID'], $w['Foto']);
  // Tampilkan
  echo "<p>
  <h3 align='center'>Perhatian! Harap mengisi semua biodata dengan benar dan lengkap termasuk foto.<br><small>Anda tidak dapat mengakses menu lain sebelum mengisi lengkap semua biodata</small></h3>
  <table class=box cellspacing=2 cellpadding=4 width=600>

  <tr><td class=inp width=100>NPM</td>
     <td class=ul><b>$w[MhswID]</b></td>
     <td class=box rowspan=6 style='padding: 2pt' align=center width=124>
     <img src='$foto' height=120></td>
     </tr>

  <tr><td class=inp>Nama</td>
      <td class=ul><b>$w[Nama]</b></td></tr>
  <tr><td class=inp>Program</td>
      <td class=ul>$w[ProgramID] - <b>$w[PRG]</b></td></tr>
  <tr><td class=inp>Program Studi</td>
      <td class=ul>$w[ProdiID] - <b>$w[PRD]</b></td></tr>
  <tr><td class=inp>Pilihan</td>
      <td class=ul>";
	  if ($_SESSION['_LevelID']==120) {
      
		}
		else {
		echo "<input type=button name='Kembali' value='Kembali ke Daftar'
        onClick=\"location='?mnux=master/mhsw'\" /> ";
		}
		//Kunci Edit Photo --------------------------------------------------
		//if ($_SESSION['_LevelID']!=120) {
       echo "<input type=button name='GantiFoto' value='Ganti Foto'
        onClick=\"location='?mhswbck=$_SESSION[mnux]&mnux=master/mhsw.foto&mhswid=$w[MhswID]'\" />";
		//}
	// ------------------------------------------------------------------------------------------------------------
     echo "<input type=button name='CetakMhsw' value='Cetak Data'
	    onClick=\"CetakData('$w[MhswID]')\" />
	  </td></tr>
  </table>
  <script>
	function CetakData(id)
	{	lnk = \"$_SESSION[mnux].cetak.php?MhswID=\"+id;
		  win2 = window.open(lnk, \"\", \"width=600, height=400, scrollbars, status\");
		  if (win2.opener == null) childWindow.opener = self;
	}
  </script>";
}
/*
function pri() {
  include_once "mhsw.edt.pri.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmPribadi';
  $sub();
}
function almt() {
  include_once "mhsw.edt.almt.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmAlamat';
  $sub();
}
function akd() {
  include_once "mhsw.edt.akd.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmAkademik';
  $sub();
}
function sek() {
  include_once "mhsw.edt.sek.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmSekolah';
  $sub();
}
function ortu() {
  include_once "mhsw.edt.ortu.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmOrtu';
  $sub();
}
function bank() {
  include_once "mhsw.edt.bank.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmBank';
  $sub();
}
function pt() {
  include_once "mhsw.edt.pt.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmPT';
  $sub();
}

function ta() {
  include_once "mhsw.edt.ta.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmTA';
  $sub();
}

function mb(){
	include_once "mhsw.edt.masterbipot.php";
	$sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'BPTEDT'; 
	$sub();
}
*/

// *** Parameters ***
if (!empty($_SESSION['_Session'])) {
		if ($_SESSION['_LevelID']==120) {
	
		$arrmhswpg = array('Pribadi~pri',
				  'Alamat<br />Tetap~almt',
				  'Orang<br />Tua~ortu');
	
	  }
	  elseif ($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==40) {
			// ..
      $arrmhswpg = array('Pribadi~pri',
				  'Alamat<br />Tetap~almt',
				  'Orang<br />Tua~ortu',
          'Akademik~akd',
				  'Asal<br />Sekolah~sek',
				  'Asal Perguruan<br />Tinggi~pt',
				  'Bank~bank',
				  'Skripsi/TA~ta');
		}
	  else {
      //'Akademik~akd',
	$arrmhswpg = array('Pribadi~pri',
	  'Orang<br />Tua~ortu',
	  'Asal<br />Sekolah~sek',
	  'Asal Perguruan<br />Tinggi~pt');
	  }
  }
else {
echo "<script>window.location='?mnux=loginprc&gos=lout';</script>";
}
	//'Master Bipot->mb');
	if ($_SESSION['_LevelID']==120) {
			$mhswid = GetSetVar('mhswid');
			if ($mhswid != $_SESSION['_Login']){
			$mhswid=$_SESSION['_Login'];
			}
		}
	else
		{
			$mhswid = GetSetVar('mhswid');
		}
$mhswpg = GetSetVar('mhswpg', 'pri');
$submodul = GetSetVar('submodul', 'pri');

// *** Main ***
TampilkanJudul("Data Mahasiswa");
if (!empty($_SESSION['mhswid'])) {
  $datamhsw = GetFields("mhsw m
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join program prg on m.ProgramID=prg.ProgramID",
    'm.MhswID', $mhswid,
    "m.*, prd.Nama as PRD, prg.Nama as PRG");
	
	$dataTA = GetFields("ta t, dosen d",
   'd.NIDN=t.Pembimbing and t.MhswID', $mhswid,
   "t.*,d.Nama as NamaPembimbing,d.Gelar");
   
  if (!empty($datamhsw)) {
    if ($_REQUEST['delete']==1){
      deleteMahasiswa($_SESSION['mnux']);
    }else{
      TampilkanHeaderMhsw($datamhsw);
      TampilkanSubModul($_SESSION['mnux'], $arrmhswpg, $submodul, $datamhsw);
      include_once($_SESSION['mnux'].'.'.$submodul.'.php');
      //TampilkanSubMenu($mnux, $arrmhswpg, $pref, $token);
    }
  }
  else echo ErrorMsg("Kesalahan",
    "Terjadi kesalahan. Mahasiswa dengan NPM: <b>$mhswid</b> tidak ditemukan.");
}

// *** Functions ***
function TampilkanSubModul($mnux, $arr, $act, $m) {
  echo "<p><table class=bsc>";
  foreach ($arr as $a) {
    $i = explode('~', $a);
    $c = ($i[1] == $act)? "class=menuaktif" : "class=menuitem";
    echo "<td $c align=center><a href='?mnux=$mnux&submodul=$i[1]'>$i[0]</a></td>";
  }
  echo "</table></p>";
}
function deleteMahasiswa($mnux){
  if ($_SESSION['_LevelID']==1){
    $s = "SELECT * from mhsw where MhswID='$_SESSION[mhswid]'";
    $r = _query($s);
    while ($w = _fetch_array($r)){
      $ss = "INSERT into mhsw_deleted (`MhswID`, `Login`, `LevelID`, `Password`, `NIMSementara`, `Blokir`, `KDPIN`, `PMBID`, `PMBFormJualID`, `PSSBID`, `NIRM`, `NIMAN`, `BuktiSetoran`, `TahunID`, `TanggalMasuk`, `TahunGantiStatus`, `SemesterAwal`, `KodeID`, `BIPOTID`, `Autodebet`, `Nama`, `Foto`, `FotoWisuda`, `Skripsi`, `StatusAwalID`, `StatusMhswID`, `ProgramID`, `ProdiID`, `KonsentrasiID`, `KelasID`, `MatriID`, `MatriHadir`, `MatriNilai`, `PenasehatAkademik`, `Kelamin`, `WargaNegara`, `Kebangsaan`, `TempatLahir`, `TanggalLahir`, `TanggalLahirIjazah`, `Agama`, `StatusSipil`, `TinggiBadan`, `BeratBadan`, `Alamat`, `Kota`, `RT`, `RW`, `KodePos`, `Propinsi`, `Negara`, `Telepon`, `Telephone`, `Handphone`, `Email`, `Website`, `AlamatAsal`, `KotaAsal`, `RTAsal`, `RWAsal`, `KodePosAsal`, `PropinsiAsal`, `NegaraAsal`, `TeleponAsal`, `AnakKe`, `JumlahSaudara`, `NamaAyah`, `InstansiIbu`, `InstansiAyah`, `PangkatGolAyah`, `NIPAyah`, `AgamaAyah`, `PendidikanAyah`, `PekerjaanAyah`, `HidupAyah`, `NamaIbu`, `PangkatGolIbu`, `NIPIbu`, `AgamaIbu`, `PendidikanIbu`, `PekerjaanIbu`, `HidupIbu`, `AlamatOrtu`, `KotaOrtu`, `RTOrtu`, `RWOrtu`, `KodePosOrtu`, `PropinsiOrtu`, `NegaraOrtu`, `TeleponOrtu`, `HandphoneOrtu`, `EmailOrtu`, `Instansi`, `Jabatan`, `AlamatKantor`, `TeleponKantor`, `PendidikanTerakhir`, `AsalSekolah`, `AsalSekolah1`, `JenisSekolahID`, `PropinsiAsalID`, `AlamatSekolah`, `KotaSekolah`, `JurusanSekolah`, `NilaiSekolah`, `TahunLulus`, `IjazahSekolah`, `AsalPT`, `MhswIDAsalPT`, `ProdiAsalPT`, `LulusAsalPT`, `TglLulusAsalPT`, `IPKAsalPT`, `Pilihan1`, `Pilihan2`, `Pilihan3`, `BatasStudi`, `Harga`, `SudahBayar`, `NA`, `TanggalUjian`, `LulusUjian`, `TanggalLulusUjian`, `RuangID`, `NomerUjian`, `NilaiUjian`, `GradeNilai`, `TanggalLulus`, `Syarat`, `SyaratLengkap`, `BuktiSetoranMhsw`, `TanggalSetoranMhsw`, `TotalBiaya`, `TotalBayar`, `Dispensasi`, `DispensasiID`, `JudulDispensasi`, `CatatanDispensasi`, `NamaBank`, `NomerRekening`, `KurikulumID`, `IPK`, `TotalSKS`, `TotalSKSPindah`, `WisudaID`, `TAID`, `Predikat`, `SKPenyetaraan`, `TglSKPenyetaraan`, `SKMasuk`, `TglSKMasuk`, `Keluar`, `SKKeluar`, `TglSKKeluar`, `TahunKeluar`, `CatatanKeluar`, `NoIdentitas`, `NoFakultas`, `NoProdi`, `NoIjazah`, `TglIjazah`, `NomerAlumni`, `TotalSKS_`, `Catatan`, `PrestasiTambahan`, `MhswIDLama`, `CetakKTM`, `Iluni`, `CekForlap`, `LoginBuat`, `TanggalBuat`, `LoginEdit`, `TanggalEdit`) value
                      ('$w[MhswID]', '$w[Login]', '$w[LevelID]', '$w[Password]', '$w[NIMSementara]', '$w[Blokir]', '$w[KDPIN]', '$w[PMBID]', '$w[PMBFormJualID]', '$w[PSSBID]', '$w[NIRM]', '$w[NIMAN]', '$w[BuktiSetoran]', '$w[TahunID]', '$w[TanggalMasuk]', '$w[TahunGantiStatus]', '$w[SemesterAwal]', '$w[KodeID]', '$w[BIPOTID]', '$w[Autodebet]', '$w[Nama]', '$w[Foto]', '$w[FotoWisuda]', '$w[Skripsi]', '$w[StatusAwalID]', '$w[StatusMhswID]', '$w[ProgramID]', '$w[ProdiID]', '$w[KonsentrasiID]', '$w[KelasID]', '$w[MatriID]', '$w[MatriHadir]', '$w[MatriNilai]', '$w[PenasehatAkademik]', '$w[Kelamin]', '$w[WargaNegara]', '$w[Kebangsaan]', '$w[TempatLahir]', '$w[TanggalLahir]', '$w[TanggalLahirIjazah]', '$w[Agama]', '$w[StatusSipil]', '$w[TinggiBadan]', '$w[BeratBadan]', '$w[Alamat]', '$w[Kota]', '$w[RT]', '$w[RW]', '$w[KodePos]', '$w[Propinsi]', '$w[Negara]', '$w[Telepon]', '$w[Telephone]', '$w[Handphone]', '$w[Email]', '$w[Website]', '$w[AlamatAsal]', '$w[KotaAsal]', '$w[RTAsal]', '$w[RWAsal]', '$w[KodePosAsal]', '$w[PropinsiAsal]', '$w[NegaraAsal]', '$w[TeleponAsal]', '$w[AnakKe]', '$w[JumlahSaudara]', '$w[NamaAyah]', '$w[InstansiIbu]', '$w[InstansiAyah]', '$w[PangkatGolAyah]', '$w[NIPAyah]', '$w[AgamaAyah]', '$w[PendidikanAyah]', '$w[PekerjaanAyah]', '$w[HidupAyah]', '$w[NamaIbu]', '$w[PangkatGolIbu]', '$w[NIPIbu]', '$w[AgamaIbu]', '$w[PendidikanIbu]', '$w[PekerjaanIbu]', '$w[HidupIbu]', '$w[AlamatOrtu]', '$w[KotaOrtu]', '$w[RTOrtu]', '$w[RWOrtu]', '$w[KodePosOrtu]', '$w[PropinsiOrtu]', '$w[NegaraOrtu]', '$w[TeleponOrtu]', '$w[HandphoneOrtu]', '$w[EmailOrtu]', '$w[Instansi]', '$w[Jabatan]', '$w[AlamatKantor]', '$w[TeleponKantor]', '$w[PendidikanTerakhir]', '$w[AsalSekolah]', '$w[AsalSekolah1]', '$w[JenisSekolahID]', '$w[PropinsiAsalID]', '$w[AlamatSekolah]', '$w[KotaSekolah]', '$w[JurusanSekolah]', '$w[NilaiSekolah]', '$w[TahunLulus]', '$w[IjazahSekolah]', '$w[AsalPT]', '$w[MhswIDAsalPT]', '$w[ProdiAsalPT]', '$w[LulusAsalPT]', '$w[TglLulusAsalPT]', '$w[IPKAsalPT]', '$w[Pilihan1]', '$w[Pilihan2]', '$w[Pilihan3]', '$w[BatasStudi]', '$w[Harga]', '$w[SudahBayar]', '$w[NA]', '$w[TanggalUjian]', '$w[LulusUjian]', '$w[TanggalLulusUjian]', '$w[RuangID]', '$w[NomerUjian]', '$w[NilaiUjian]', '$w[GradeNilai]', '$w[TanggalLulus]', '$w[Syarat]', '$w[SyaratLengkap]', '$w[BuktiSetoranMhsw]', '$w[TanggalSetoranMhsw]', '$w[TotalBiaya]', '$w[TotalBayar]', '$w[Dispensasi]', '$w[DispensasiID]', '$w[JudulDispensasi]', '$w[CatatanDispensasi]', '$w[NamaBank]', '$w[NomerRekening]', '$w[KurikulumID]', '$w[IPK]', '$w[TotalSKS]', '$w[TotalSKSPindah]', '$w[WisudaID]', '$w[TAID]', '$w[Predikat]', '$w[SKPenyetaraan]', '$w[TglSKPenyetaraan]', '$w[SKMasuk]', '$w[TglSKMasuk]', '$w[Keluar]', '$w[SKKeluar]', '$w[TglSKKeluar]', '$w[TahunKeluar]', '$w[CatatanKeluar]', '$w[NoIdentitas]', '$w[NoFakultas]', '$w[NoProdi]', '$w[NoIjazah]', '$w[TglIjazah]', '$w[NomerAlumni]', '$w[TotalSKS_]', '$w[Catatan]', '$w[PrestasiTambahan]', '$w[MhswIDLama]', '$w[CetakKTM]', '$w[Iluni]', '$w[CekForlap]', '$w[LoginBuat]', '$w[TanggalBuat]', '$w[LoginEdit]', '$w[TanggalEdit]')";
      _query($ss);

      $ss = "DELETE from mhsw where MhswID='$w[MhswID]'";
      _query($ss);
    }
  }
  echo "<script>window.location='?mnux=master/mhsw';</script>";
}
?>
