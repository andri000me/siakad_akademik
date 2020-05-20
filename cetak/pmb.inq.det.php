<?php
// Author: Emanuel Setio Dewo
// 12 April 2006
include "../sisfokampus.php";
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  include "parameter.php";
  include "cekparam.php";
?>

<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META content="Emanuel Setio Dewo" name="author">
  <META content="Sisfo Kampus" name="description">
  <link href="index.css" rel="stylesheet" type="text/css">
  </HEAD>
<BODY>

<?php
  $PMBID = $_REQUEST['PMBID'];
  TampilkanJudul("Detail Calon Mahasiswa");
  $w = GetFields("pmb p
    left outer join program prg on p.ProgramID=prg.ProgramID
    left outer join prodi prd on p.ProdiID=prd.ProdiID
    left outer join pmbformulir pf on p.PMBFormulirID=pf.PMBFormulirID
    left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
    left outer join agama agm on p.Agama=agm.Agama",
    "p.PMBID", $PMBID,
    "p.*, prg.Nama as PRG, prd.Nama as PRD, pf.Nama as PF, pf.JumlahPilihan, 
    format(pf.Harga, 0) as HRG, sa.Nama as STAWAL, agm.Nama as AGM");
  $TglLahir = FormatTanggal($w['TanggalLahir']);
  $NamaSekolah = GetaField('asalsekolah', "SekolahID", $w['AsalSekolah'], 'Nama');
  $JS = GetaField('jurusansekolah', "JurusanSekolahID", $w['JurusanSekolah'], "concat(Nama, ' ', NamaJurusan)");
  // data ayah
  $HidupAyah = GetaField('hidup', "Hidup", $w['HidupAyah'], 'Nama');
  $AgamaAyah = GetaField('agama', 'Agama', $w['AgamaAyah'], 'Nama');
  $PendidikanAyah = GetaField('pendidikanortu', 'Pendidikan', $w['PendidikanAyah'], 'Nama');
  $PekerjaanAyah = GetaField('pekerjaanortu', 'Pekerjaan', $w['PekerjaanAyah'], 'Nama');
  // data ibu
  $HidupIbu = GetaField('hidup', "Hidup", $w['HidupIbu'], 'Nama');
  $AgamaIbu = GetaField('agama', 'Agama', $w['AgamaIbu'], 'Nama');
  $PendidikanIbu = GetaField('pendidikanortu', 'Pendidikan', $w['PendidikanIbu'], 'Nama');
  $PekerjaanIbu = GetaField('pekerjaanortu', 'Pekerjaan', $w['PekerjaanIbu'], 'Nama');
  // Tampilkan
  $btn = "<input type=button name='Tutup' value='Tutup' onClick=\"javascript:window.close()\">";
  echo <<<END
  <table class=bsc cellspacing=1 cellpadding=4 width=100%>
  <tr><td class=ul colspan=2><b>Data Calon Mahasiswa</b></td></tr>
  <tr><td class=inp>No PMB</td><td class=ul>$PMBID</td></tr>
  <tr><td class=inp>Nama Calon</td><td class=ul>$w[Nama]</td></tr>
  
  <tr><td class=ul><b>Data Pendaftaran</b></td><td class=ul>$btn</td></tr>
  <tr><td class=inp>Jenis Formulir</td><td class=ul>$w[PF] ($w[JumlahPilihan] pilihan, hrg: Rp. $w[HRG])</td></tr>
  <tr><td class=inp>Program</td><td class=ul>$w[ProgramID] $w[PRG]</td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul>$w[ProdiID] $w[PRD]</td></tr>
  <tr><td class=inp>Status Awal</td><td class=ul>$w[STAWAL]</td></tr>
  
  <tr><td class=ul><b>Data Pribadi Sesuai KTP</b></td><td class=ul>$btn</td></tr>
  <tr><td class=inp>Jenis Kelamin</td><td class=ul>$w[Kelamin]</td></tr>
  <tr><td class=inp>Tempat, Tgl lahir</td><td class=ul>$w[TempatLahir], $TglLahir</td></tr>
  <tr><td class=inp>Warga Negara</td><td class=ul>$w[WargaNegara] $w[Kebangsaan]</td></tr>
  <tr><td class=inp>Agama</td><td class=ul>$w[AGM]</td></tr>
  
  <tr><td class=inp>Alamat</td><td class=ul>$w[Alamat]</td></tr>
  <tr><td class=inp>Kota, Kode Pos</td><td class=ul>$w[Kota], $w[KodePos]</td></tr>
  <tr><td class=inp>RT/RW</td><td class=ul>$w[RT]/$w[RW]</td></tr>
  <tr><td class=inp>Propinsi, Negara</td><td class=ul>$w[Propinsi], $w[Negara]</td></tr>
  <tr><td class=inp>Telepon, HP</td><td class=ul>$w[Telepon], $w[Handphone]</td></tr>
  <tr><td class=inp>Email</td><td class=ul>$w[Email]</td></tr>
  
  <tr><td class=ul><b>Asal Sekolah</b></td><td class=ul>$btn</td></tr>
  <tr><td class=inp>Asal Sekolah</td><td class=ul>$w[AsalSekolah] $NamaSekolah</td></tr>
  <tr><td class=inp>Jurusan Sekolah</td><td class=ul>$w[JurusanSekolah] $JS</td></tr>
  <tr><td class=inp>Tahun Lulus</td><td class=ul>$w[TahunLulus]</td></tr>
  <tr><td class=inp>Nilai/NEM</td><td class=ul>$w[NilaiSekolah]</td></tr>
  
  <tr><td class=ul><b>Alamat Tinggal di Jakarta</b></td><td class=ul>$btn</td></tr>
  <tr><td class=inp>Alamat</td><td class=ul>$w[AlamatAsal]</td></tr>
  <tr><td class=inp>Kota, Kode Pos</td><td class=ul>$w[KotaAsal], $w[KodePosAsal]</td></tr>
  <tr><td class=inp>RT/RW</td><td class=ul>$w[RTAsal]/$w[RWAsal]</td></tr>
  <tr><td class=inp>Propinsi, Negara</td><td class=ul>$w[PropinsiAsal], $w[NegaraAsal]</td></tr>
  <tr><td class=inp>Telepon</td><td class=ul>$w[TeleponAsal]</td></tr>
  
  <tr><td class=ul><b>Data Orang Tua</b></td><td class=ul>$btn</td></tr>
  <tr><td class=inp1 align=right><b>Nama Ayah</b></td><td class=ul>$w[NamaAyah]</td></tr>
  <tr><td class=inp>Status</td><td class=ul>$HidupAyah</td></tr>
  <tr><td class=inp>Agama</td><td class=ul>$AgamaAyah</td></tr>
  <tr><td class=inp>Pendidikan</td><td class=ul>$PendidikanAyah</td></tr>
  <tr><td class=inp>Pekerjaan</td><td class=ul>$PekerjaanAyah</td></tr>
  
  <tr><td class=inp1 align=right><b>Nama Ibu</b></td><td class=ul>$w[NamaIbu]</td></tr>
  <tr><td class=inp>Status</td><td class=ul>$HidupIbu</td></tr>
  <tr><td class=inp>Agama</td><td class=ul>$AgamaIbu</td></tr>
  <tr><td class=inp>Pendidikan</td><td class=ul>$PendidikanIbu</td></tr>
  <tr><td class=inp>Pekerjaan</td><td class=ul>$PekerjaanIbu</td></tr>
  
  <tr><td class=ul><b>Alamat Orant Tua</b></td><td class=ul>$btn</td></tr>
  <tr><td class=inp>Alamat</td><td class=ul>$w[AlamatOrtu]</td></tr>
  <tr><td class=inp>Kota, Kode Pos</td><td class=ul>$w[KotaOrtu], $w[KodePosOrtu]</td></tr>
  <tr><td class=inp>RT/RW</td><td class=ul>$w[RTOrtu]/$w[RWOrtu]</td></tr>
  <tr><td class=inp>Propinsi, Negara</td><td class=ul>$w[PropinsiOrtu], $w[NegaraOrtu]</td></tr>
  <tr><td class=inp>Telepon</td><td class=ul>$w[TeleponOrtu]</td></tr>
  </table>
END;
?>

</BODY>
</HTML>