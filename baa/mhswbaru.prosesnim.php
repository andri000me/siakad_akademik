<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Edit BIPOT");

// *** Parameters ***
$pmbid = sqling($_REQUEST['pmbid']);
$pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $pmbid, '*');

// *** Main ***
TampilkanJudul("Pemrosesan NPM");
// Cek apakah Cama sudah diproses menjadi mhsw sebelumnya?
if (!empty($pmb['MhswID'])) {
  die(ErrorMsg('Error',
    "Cama dengan no. PMB: <b>$pmbid</b> telah diproses menjadi mahasiswa.<br />
    Nomer NPM: <b>$pmb[MhswID]</b>.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
}
// Jika lolos cek, maka jalankan fungsi2
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiProsesNIM' : $_REQUEST['gos'];
$gos($pmbid, $pmb);

// *** Functions ***
function GetTahunCama($pmb) {
  $s = "select TahunID, Nama
    from tahun
    where KodeID = '".KodeID."'
      and ProgramID = '$pmb[ProgramID]'
      and ProdiID = '$pmb[ProdiID]'
    order by TahunID desc
    limit 10";
  $r = _query($s);
  $opt = "<option value=''>-</option>";
  while ($w = _fetch_array($r)) {
    $sel = ($pmb['PMBPeriodID'] == $w['TahunID'])? 'selected' : '';
    $_thn = str_pad($w['TahunID'], 8, '.', STR_PAD_RIGHT);
    $opt .= "<option value='$w[TahunID]' $sel>". $_thn . ' ' . $w['Nama'] . "</option>";
  }
  return $opt;
}
function KonfirmasiProsesNIM($pmbid, $pmb) {
  $PRG = GetaField('program', "KodeID='".KodeID."' and ProgramID", $pmb['ProgramID'], 'Nama');
  $PRD = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $pmb['ProdiID'], 'Nama');
  $TahunID = substr($pmb['PMBPeriodID'], 0, 4);
  $optthn = GetTahunCama($pmb);
  CheckFormScript('Tahun_Akd');
  echo Konfirmasi('Konfirmasi Proses NPM',
    "<p><table class=bsc cellspacing=1 width=100%>
    <tr><td class=ul1 colspan=2>
        Anda akan memproses NPM dari Cama.<br />
        Setelah memperoleh NPM, Mhsw dapat memulai aktivitas akademiknya.<br />
        Berikut adalah beberapa langkah yang akan dilakukan oleh Portal secara otomatis:
        </td>
        </tr>
    <tr><td class=ul1 colspan=2>
        <ol>
        <li>Portal akan membuatkan NPM untuk Cama.</li>
        <li>Data diri Cama akan disalin ke data mahasiswa.</li>
        <li>History bipot & pembayaran akan ditransfer ke data mahasiswa.</li>
        <li>Membuat data akademik semester 1.</li>
        </ol>
        </td>
        </tr>
    <form action='../$_SESSION[mnux].prosesnim.php' method=POST onSubmit=\"return CheckForm(this)\">
    <input type=hidden name='pmbid' value='$pmbid' />
    <input type=hidden name='gos' value='ProsesNIM' />
    
    <tr><td class=inp width=110>Tahun Akademik:</td>
        <td class=ul1>
        <select name='Tahun_Akd' style='font-family:mono'>$optthn</select>
        <abbr title='Data Cama akan dimasukkan ke tahun akademik ini. Bila pilihan ini kosong, segera hubungi Kepala BAA untuk mengaktifkan tahun akademik yang benar.'>(?)</abbr>
        </td></tr>
    <tr><td class=inp>Cama:</td>
        <td class=ul1>$pmb[Nama] <sup>($pmbid)</sup></td>
        </tr>
    <tr><td class=inp>Prg. Studi:</td>
        <td class=ul1>$PRD <sup>($pmb[ProdiID])</sup></td>
        </tr>
    <tr><td class=inp>Prg. Pendidikan:</td>
        <td class=ul1>$PRG <sup>($pmb[ProgramID])</sup>
        </td></tr>
    <tr><td class=inp>Opsi:</td>
        <td class=ul1>
        <input type=submit name='Proses' value='Proses' />
        <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
        </td></tr>
    </form>
    </table></p>");
}
function ProsesNIM($pmbid, $pmb) {
  $StatusMhswID = 'A'; // Default
  $Tahun_Akd = $_REQUEST['Tahun_Akd'];
  
  // 1. Buat NIM Mhsw & Hitung Batas studi
  $NIM = (GetaField('prodi', 'ProdiID', $pmb['ProdiID'], 'GunakanNIMSementara')=='Y')?  GetNextNIMSementara($Tahun_Akd, $pmb) : GetNextNIM($Tahun_Akd, $pmb);
  $NIMSementara = GetaField('prodi', 'ProdiID', $pmb['ProdiID'], 'GunakanNIMSementara');
  $Batas = HitungBatasStudi($Tahun_Akd, $pmb['ProdiID']);
  //die($Batas);
  
  // 2. Copy data PMB ke Mhsw
  $s = "insert into mhsw
    (MhswID, Login, LevelID, KodeID, Password,
    NIMSementara, PMBID, PSSBID, TahunID, BIPOTID, SemesterAwal,
    Nama, StatusAwalID, StatusMhswID,
    ProgramID, ProdiID, Kelamin, WargaNegara, Kebangsaan,
    TempatLahir, TanggalLahir,
    Agama, StatusSipil,
    TinggiBadan, BeratBadan,
    Alamat, Kota, RT, RW, KodePos, Propinsi, Negara,
    Telepon, Handphone, Email,
    AlamatAsal, KotaAsal, RTAsal, RWAsal, KodePosAsal, PropinsiAsal, NegaraAsal,
    TeleponAsal,
    NamaAyah, AgamaAyah, PendidikanAyah, PekerjaanAyah, HidupAyah,
    NamaIbu, AgamaIbu, PendidikanIbu, PekerjaanIbu, HidupIbu,
    AlamatOrtu, KotaOrtu, RTOrtu, RWOrtu, KodePosOrtu, PropinsiOrtu, NegaraOrtu,
    TeleponOrtu, HandphoneOrtu, EmailOrtu,
    PendidikanTerakhir, AsalSekolah, JenisSekolahID, AlamatSekolah, KotaSekolah, 
    JurusanSekolah, NilaiSekolah, TahunLulus, IjazahSekolah,
    AsalPT, MhswIDAsalPT, ProdiAsalPT, LulusAsalPT, TglLulusAsalPT,
    IPKAsalPT, Pilihan1, Pilihan2, Pilihan3, PrestasiTambahan,
    BatasStudi, LulusUjian, NilaiUjian, GradeNilai, Syarat, SyaratLengkap,
    TotalBiaya, TotalBayar, LoginBuat, TanggalBuat)
    values
    ('$NIM', '$NIM', '120', '".KodeID."', md5('$pmb[TanggalLahir]'),
	'$NIMSementara', '$pmb[PMBID]', '$pmb[PSSBID]', LEFT('$Tahun_Akd',4), '$pmb[BIPOTID]', '$Tahun_Akd',
    '$pmb[Nama]', '$pmb[StatusAwalID]', '$StatusMhswID',
    '$pmb[ProgramID]', '$pmb[ProdiID]', '$pmb[Kelamin]', '$pmb[WargaNegara]', '$pmb[Kebangsaan]',
    '$pmb[TempatLahir]', '$pmb[TanggalLahir]',
    '$pmb[Agama]', '$pmb[StatusSipil]',
    '$pmb[TinggiBadan]', '$pmb[BeratBadan]',
    '$pmb[Alamat]', '$pmb[Kota]', '$pmb[RT]', '$pmb[RW]', '$pmb[KodePos]', '$pmb[Propinsi]', '$pmb[Negara]',
    '$pmb[Telepon]', '$pmb[Handphone]', '$pmb[Email]',
    '$pmb[AlamatAsal]', '$pmb[KotaAsal]', '$pmb[RTAsal]', '$pmb[RWAsal]', '$pmb[KodePosAsal]', '$pmb[PropinsiAsal]', '$pmb[NegaraAsal]',
    '$pmb[TeleponAsal]',
    '$pmb[NamaAyah]', '$pmb[AgamaAyah]', '$pmb[PendidikanAyah]', '$pmb[PekerjaanAyah]', '$pmb[HidupAyah]',
    '$pmb[NamaIbu]', '$pmb[AgamaIbu]', '$pmb[PendidikanIbu]', '$pmb[PekerjaanIbu]', '$pmb[HidupIbu]',
    '$pmb[AlamatOrtu]', '$pmb[KotaOrtu]', '$pmb[RTOrtu]', '$pmb[RWOrtu]', '$pmb[KodePosOrtu]', '$pmb[PropinsiOrtu]', '$pmb[NegaraOrtu]',
    '$pmb[TeleponOrtu]', '$pmb[HandphoneOrtu]', '$pmb[EmailOrtu]',
    '$pmb[PendidikanTerakhir]', '$pmb[AsalSekolah]', '$pmb[JenisSekolahID]', '$pmb[AlamatSekolah]', '$pmb[KotaSekolah]',
    '$pmb[JurusanSekolah]', '$pmb[NilaiSekolah]', '$pmb[TahunLulus]', '$pmb[IjazahSekolah]',
    '$pmb[AsalPT]', '$pmb[MhswIDAsalSekolah]', '$pmb[ProdiAsalPT]', '$pmb[LulusAsalPT]', '$pmb[TglLulusAsalPT]',
    '$pmb[IPKAsalPT]', '$pmb[Pilihan1]', '$pmb[Pilihan2]', '$pmb[Pilihan3]', '$pmb[PrestasiTambahan]',
    '$Batas', '$pmb[LulusUjian]', '$pmb[NilaiSekolah]', '$pmb[GradeNilai]', '$pmb[Syarat]', '$pmb[SyaratLengkap]',
    '$pmb[TotalBiaya]', '$pmb[TotalBayar]', '$_SESSION[_Login]', now())";
  // Nilai Ujian untuk Kasih Bangsa adalah Nilai Sekolah. Ralat baris kedua di atas ini untuk mengubah ke default
  $r = _query($s);
  
  // Set NIM di data PMB
  $s = "update pmb set MhswID = '$NIM'
    where KodeID='".KodeID."' and PMBID = '$pmbid' ";
  $r = _query($s);
  
  // Set Status Aplikan dari murid PMB menjadi REG
  include_once "../pmb/statusaplikan.lib.php";
  SetStatusAplikan('REG', GetaField('pmb', "PMBID='$pmbid' and KodeID", KodeID, "AplikanID"), GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID"));
  
  // Transfer BIPOTMhsw ke Mhsw
  $s = "update bipotmhsw
    set MhswID = '$NIM',
        PMBMhswID = 1,
        TahunID = '$Tahun_Akd',
        LoginEdit = '$_SESSION[_Login]',
        TanggalEdit = now()
    where PMBID = '$pmbid'
      and PMBMhswID = 0
      and KodeID = '".KodeID."' ";
  $r = _query($s);
  
  // Transfer Pembayaran ke Mhsw
  $s = "update bayarmhsw
    set MhswID = '$NIM',
        PMBMhswID = 1,
        TahunID = '$Tahun_Akd',
        LoginEdit = '$_SESSION[_Login]',
        TanggalEdit = now()
    where PMBID = '$pmbid'
      and PMBMhswID = 0
      and KodeID = '".KodeID."' ";
  $r = _query($s);
  
  // Otomatis Registrasi di Semester
  $MaxSKS = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $pmb['ProdiID'], 'DefSKS')+0;
  $s = "insert into khs
    (KodeID, TahunID, ProgramID, ProdiID,
    MhswID, StatusMhswID, Sesi, BIPOTID,
    Biaya, Bayar, MaxSKS,
    Keterangan, LoginBuat, TanggalBuat)
    values
    ('".KodeID."', '$Tahun_Akd', '$pmb[ProgramID]', '$pmb[ProdiID]',
    '$NIM', '$StatusMhswID', 1, '$pmb[BIPOTID]',
    '$pmb[TotalBiaya]', '$pmb[TotalBayar]', $MaxSKS,
    'Auto-registrasi', '$_SESSION[_Login]', now())";
  $r = _query($s);
  
  // Tutup aplikasi
  TutupScript();
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}

?>
