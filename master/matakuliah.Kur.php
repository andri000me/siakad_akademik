<?php


function DefKur() {
  global $mnux, $pref, $token;
  TampilkanPilihanProdi($mnux, '', $pref, "Kur");
  if (!empty($_SESSION['prodi'])) {
    TampilkanKurikulum();
  }
}
function TampilkanKurikulum() {
  global $mnux, $pref, $token, $arrID;
  $s = "select * from kurikulum where ProdiID='$_SESSION[prodi]'
    order by KurikulumKode desc";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=700>";
  echo "<tr>
    <td class=ul1 colspan=5>
    <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=KurEdt&md=1'>Tambah Kurikulum</a> |
    <a href='?mnux=$mnux&$pref=$token'>Refresh Tampilan</a> |
	<a href='#' onClick=\"CetakKurikulum()\">Cetak</a>
    </td>
    </tr>
	<script>
	  function CetakKurikulum() {
		var _rnd = randomString();
		lnk = '$_SESSION[mnux].cetakkur.php?p=$_SESSION[prodi]&_rnd='+_rnd;
		win2 = window.open(lnk, '', 'width=800, height=600, scrollbars, status');
		if (win2.opener == null) childWindow.opener = self;
	  }
  </script>";
  RandomStringScript();
  echo "<tr><th class=ttl>Kode</th>
    <th class=ttl>Kurikulum</th>
    <th class=ttl>Sesi</th>
    <th class=ttl>Jml/tahun</th>
    <th class=ttl>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td $c width=100>
        <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=KurEdt&md=0&kurid=$w[KurikulumID]'><img src='img/edit.png' border=0>
        $w[KurikulumKode]</a></td>
      <td $c width=180>$w[Nama]</td>
      <td $c>$w[Sesi]</td>
      <td $c align=right>$w[JmlSesi]</td>
      <td $c align=center width=10><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function KurEdt() {
  global $mnux, $pref, $arrID;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('kurikulum', 'KurikulumID', $_REQUEST['kurid'], '*');
    $jdl = "Edit Kurikulum";
  }
  else {
    $w = array();
    $w['KurikulumID'] = 0;
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['KodeID'] = $arrID['Kode'];
    $w['KurikulumKode'] = '';
    $w['Nama'] = '';
    $w['Sesi'] = '';
    $w['JmlSesi'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Kurikulum";
  }
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  // Tampilkan form
  CheckFormScript("KurikulumKode,Nama,Sesi,JmlSesi");
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='sub' value='KurSav'>
  <input type=hidden name='prodi' value='$_SESSION[prodi]'>
  <input type=hidden name='KurikulumID' value='$w[KurikulumID]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode/Tahun:</td><td class=ul><input type=text name='KurikulumKode' value='$w[KurikulumKode]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Nama Kurikulum:</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Nama Sesi:</td><td class=ul><input type=text name='Sesi' value='$w[Sesi]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Jumlah Sesi/Tahun:</td><td class=ul><input type=text name='JmlSesi' value='$w[JmlSesi]' size=5 maxlength=2></td></tr>
  <tr><td class=inp>Tidak aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $_na></td></tr>
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]&$snm=$sid'\"></td></tr>
  </form></table>";
}
function KurSav() {
  $md = $_REQUEST['md'] +0;
  $KurikulumID = $_REQUEST['KurikulumID'];
  $KurikulumKode = $_REQUEST['KurikulumKode'];
  $Nama = sqling($_REQUEST['Nama']);
  $Sesi = sqling($_REQUEST['Sesi']);
  $JmlSesi = $_REQUEST['JmlSesi']+0;
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // simpan
  if ($md == 0) {
    $s = "update kurikulum set KurikulumKode='$KurikulumKode',
      Nama='$Nama', Sesi='$Sesi', JmlSesi='$JmlSesi', NA='$NA', TglEdit=now(), LoginEdit='$_SESSION[_Login]'
      where KurikulumID='$_REQUEST[KurikulumID]' ";
    $r = _query($s);
    // update jika jadi aktif
  }
  else {
    $s = "insert into kurikulum (KurikulumKode, KodeID, ProdiID, Nama, Sesi, JmlSesi, NA, TglBuat, LoginBuat)
      values('$KurikulumKode', '".KodeID."', '$_SESSION[prodi]', '$Nama', '$Sesi', '$JmlSesi', '$NA', now(), '$_SESSION[_Login]')";
    $r = _query($s);
    $KurikulumID = GetLastID();
  }
  // Non aktifkan yg lain
  /* ~~~ 03/11/2008 --> supaya bisa banyak kurikulum aktif
  if ($NA == 'N') {
    $s1 = "update kurikulum set NA='Y'
      where ProdiID='$_SESSION[prodi]' and KurikulumID<>$KurikulumID";
    $r1 = _query($s1);
  }
  */
  
  BerhasilSimpan("?mnux=$_SESSION[mnux]", 100);
}
?>
