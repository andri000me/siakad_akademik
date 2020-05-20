<?php
// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'frmSekolah' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function CariSekolahScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function carisekolah(frm){
    lnk = "cari/carisekolah.php?SekolahID="+frm.AsalSekolah.value+"&Cari="+frm.NamaSekolah.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}
function frmSekolah() {
  global $datamhsw;
  CariSekolahScript();
  $NamaSekolah = GetaField('asalsekolah', 'SekolahID', $datamhsw['AsalSekolah'], "concat(Nama, ', ', Kota)");
  $optjur = GetOption2('jurusansekolah', "concat(JurusanSekolahID, ' - ', Nama, ' - ', NamaJurusan)", 'JurusanSekolahID', $datamhsw['JurusanSekolah'], '', 'JurusanSekolahID');
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' name='data' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]' />
  <input type=hidden name='submodul' value='$_SESSION[submodul]' />
  <input type=hidden name='sub' value='SekolahSav' />
  <input type=hidden name='BypassMenu' value='1' />

  <tr><td colspan=2 class=ul><b>Sekolah Menengah Atas Mahasiswa</td></tr>

  <tr><td class=inp rowspan=2 width=130>Sekolah Asal</td><td class=ul><input type=text name='AsalSekolah' value='$datamhsw[AsalSekolah]' size=10 maxlength=50></td></tr>
  <tr><td class=ul align=justify><input type=text name='NamaSekolah' value='$NamaSekolah' size=40 maxlength=50> <a href='javascript:carisekolah(data)'>Cari</a><br />
  					Isikan bagian dari Nama Asal Sekolah, beri tanda baca koma [ , ] lalu isi Nama Kota/Kabupaten Sekolah.<br />
                    <b>Contoh: Muhammadiyah, Padang</b><br /><br />
                    <i>Hindari menulis SMA/SMK/MA agar hasil tidak terlalu banyak.</i></td></tr>
  
  <tr><td class=inp>Jenis Sekolah</td><td class=ul><b>$datamhsw[JenisSekolahID]</b></td></tr>
  <tr><td class=inp>Jurusan</td><td class=ul><select name='JurusanSekolah'>$optjur</select></td></tr>
  <tr><td class=inp>Tahun Lulus</td><td class=ul><input type=text name='TahunLulus' value='$datamhsw[TahunLulus]' size=10 maxlength=5></td></tr>
  <tr><td class=inp>Rata-rata UN</td><td class=ul><input type=text name='NilaiSekolah' value='$datamhsw[NilaiSekolah]' size=5 maxlength=5></td></tr>
  <tr><td class=ul colspan=2 align=center><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function SekolahSav() {
  $AsalSekolah = $_REQUEST['AsalSekolah'];
  $JurusanSekolah = $_REQUEST['JurusanSekolah'];
  $TahunLulus = $_REQUEST['TahunLulus'];
  $NilaiSekolah = $_REQUEST['NilaiSekolah'];
  $JenisSekolahID = GetaField('asalsekolah', 'SekolahID', $AsalSekolah, 'JenisSekolahID');
  // Simpan
  $s = "update mhsw set AsalSekolah='$AsalSekolah', JenisSekolahID='$JenisSekolahID',
    JurusanSekolah='$JurusanSekolah',
    TahunLulus='$TahunLulus', NilaiSekolah='$NilaiSekolah', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&submodul=$_SESSION[submodul]", 100);
}

?>
