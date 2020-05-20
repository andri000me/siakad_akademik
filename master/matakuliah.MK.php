<?php
// *** Functions ***
function DefMK() {
  global $mnux, $pref, $token;
  TampilkanPilihanKurikulum();
  //TampilkanPilihanProdi($mnux, '', $pref, "MK");
  if (!empty($_SESSION['prodi'])) {
    TampilkanMenuMK();
    TampilkanMK();
  }
}

function TampilkanMenuMK() {
  global $mnux, $pref;
  RandomStringScript();
  $kurid = $_SESSION['kurid_'.$_SESSION['prodi']];
  echo <<<ESD
  <p><a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=MKEdt&md=1'>Tambah Matakuliah</a> |
  <a href='#' onClick="CetakMatakuliah()">Cetak</a> | <a href='#' onClick="CetakStruktur()">Cetak Struktur Kurikulum</a>
  
  <script>
  <!--
  function CetakMatakuliah() {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?k=$kurid&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakStruktur() {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].cetakstrukturkur.php?KurikulumID=$kurid&ProdiID=$_SESSION[prodi]&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
ESD;
}
function TampilkanMK() {
  if (!empty($_SESSION['kurid_'.$_SESSION['prodi']])) TampilkanMK1();
}
function TampilkanMK1() {
  global $mnux, $pref, $arrID;
  $arrKurid = GetFields('kurikulum', "KurikulumID", $_SESSION['kurid_'.$_SESSION['prodi']], '*');
  $mx = GetaField("mk", "KurikulumID", $arrKurid['KurikulumID'], "max(Sesi)");
  // Tampilkan
  $arrKurid['JmlSesi'] = ($arrKurid['JmlSesi'] == 0)? 1 : $arrKurid['JmlSesi'];
  $lebar = 100 / $arrKurid['JmlSesi'];
  echo "<p><table class=bsc width=100% cellspacing=1 cellpadding=4>";
  for ($i=1; $i<=$mx; $i++) {
    //$col++;
    if ($i % $arrKurid['JmlSesi'] == 1) echo "<tr>";
    echo "<td valign=top width=$lebar%>";
    TampilkanMKSesi($arrKurid, $i);
    echo "</td>";
    if ($i % $arrKurid['JmlSesi'] == 0) echo "</tr>";
  }
  echo "</table></p>";
}
function TampilkanMKSesi($arrKurid, $Sesi) {
  global $mnux, $pref, $arrID;
  $s = "select mk.*, kons.KonsentrasiKode, kons.Nama as KONS
      from mk as mk
      left outer join konsentrasi kons on mk.KonsentrasiID=kons.KonsentrasiID
      where mk.ProdiID='$_SESSION[prodi]' and mk.Sesi='$Sesi' and mk.KurikulumID='$arrKurid[KurikulumID]'
      order by kons.KonsentrasiKode, mk.MKKode";
  $r = _query($s);
  //echo $s;
  $n = 0;
  $tot = 0;
  $kons = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=100%>";
  echo "<tr><td colspan=4>Sesi: <font size=+1>$Sesi</font> ($arrKurid[Sesi])</td></tr>";
  echo "<tr><th class=ttl width=5>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Singkatan</th>
    <th class=ttl colspan=2 width=5>SKS</th>
    <th class=ttl title='Prasyarat'>Pras</th></tr>";
  while ($w = _fetch_array($r)) {
    if ($kons != $w['KonsentrasiID']) {
      $kons = $w['KonsentrasiID'];
      echo "<tr><td colspan=4 class=inp1>$w[KonsentrasiKode] - <b>$w[KONS]</td></tr>";
    }
    $n++;
    $tot += $w['SKS'];
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $wjb = ($w['Wajib'] == 'Y')? "<font color=red title='Wajib'>*</font>" : '&nbsp;';
    // Ambil prasyarat
    $_pras = GetArrayTable("select mk.MKKode
      from mkpra left outer join mk on mkpra.PraID=mk.MKID
      where mkpra.MKID=$w[MKID] order by mk.MKKode", 'MKKode', 'MKKode', ' ');
    $pras = (empty($_pras))? '' : 
      "<a onClick='javascript:alert(\"$_pras\")' title='$_pras'><img src='img/check.gif'></a>";
    if (!empty($w['JenisMKID'])) $NamaMK = "<b>$w[Nama]</b>";
    else $NamaMK = $w['Nama'];
    $string_KKN = ($w['KKN']=='Y')? "KKN":"";
    $string_PL = ($w['PraktekKerja']=='Y')? "PL":"";
    $string_TA = ($w['TugasAkhir']=='Y')? "TA":"";
    $string_SKS = ($w['PerSKS']=='Y')? "":"Non-SKS";
    echo "<tr><td class=inp>$n</td>
      <td $c width=100 nowrap><a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=MKEdt&md=0&mkid=$w[MKID]'><img src='img/edit.png' border=0>
      $w[MKKode]</a></td>
      <td $c>$NamaMK <sup>$string_KKN $string_PL $string_TA $string_SKS</sup></td>
      <td $c>$w[Singkatan]&nbsp;</td>
      <td class=inp1 align=center>$wjb</td>
      <td $c align=right>$w[SKS]<sup>$w[SKSPraktikum]</sup></td>
      <td $c><a href='?mnux=$mnux&pref=$_SESSION[$pref]&sub=PraEdt&mkid=$w[MKID]' title='Edit Prasyarat'><img src='img/edit.png'></a>
      $pras</td>
      </tr>";
  }
  echo "<tr><td colspan=3 align=right>Total :</td><td colspan=2 align=right><strong>$tot</strong></td></tr>";
  echo "</table></p>";
}
function MKEdt() {
  global $mnux, $pref, $arrID;
  $md = $_REQUEST['md'] +0;
  if ($md == 0) {
    $w = GetFields('mk', "MKID", $_REQUEST['mkid'], '*');
    $jdl = "Edit Matakuliah";
    $pra = "<input type=button name='Prasyarat' value='Edit Prasyarat' onClick=\"location='?mnux=$mnux&$pref=MK&sub=PraEdt&mkid=$w[MKID]'\">";
  }
  else {
    $w = array();
    $w['MKID'] = 0;
    $w['KurikulumID'] = $_SESSION['kurid_'.$_SESSION['prodi']];
    $w['KodeID'] = KodeID;
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['KonsentrasiID'] = '';
    $w['MKKode'] = '';
    $w['Nama'] = '';
    $w['Nama_en'] = '';
    $w['Singkatan'] = '';
    $w['Responsi'] = 'N';
    $w['JenisMKID'] = '';
    $w['JenisPilihanID'] = 0;
    $w['JenisKurikulumID'] = 0;
    $w['Wajib'] = 'N';
    $w['Sesi'] = 1;
    $w['Deskripsi'] = '';
    $w['SKS'] = 0;
    $w['SKSTatapMuka'] = 0;
    $w['SKSPraktikum'] = 0;
    $w['SKSPraktekLap'] = 0;
    $w['SKSMin'] = 0;
    $w['IPKMin'] = 0;
    $w['Penanggungjawab'] = '';
	$w['PraktekKerja'] = 'N';
	$w['Komprehensif'] = 'N';
    $w['TugasAkhir'] = 'N';
    $w['KKN'] = 'N';
    $w['Prasyarat'] = '.';
    $w['NA'] = 'N';
    $jdl = "Tambah Matakuliah";
    $pra = '';
  }
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $_wjb = ($w['Wajib'] == 'Y')? 'checked' : '';
  $_kkn = ($w['KKN'] == 'Y')? 'checked' : '';
  $_pk = ($w['PraktekKerja'] == 'Y')? 'checked' : '';
  $_kp = ($w['Komprehensif'] == 'Y')? 'checked' : '';
  $_ta = ($w['TugasAkhir'] == 'Y')? 'checked' : '';
  $_persks = ($w['PerSKS'] == 'N')? 'checked' : '';
  $chkres = ($w['Responsi'] == 'Y') ? 'checked' : '';
  $optkuri = GetOption2('kurikulum', "concat(KurikulumKode, ' - ', Nama)", 'KurikulumID', $w['KurikulumID'], "ProdiID='$w[ProdiID]' and KodeID='$w[KodeID]'", 'KurikulumID');  
  $optkons = GetOption2('konsentrasi', "concat(KonsentrasiKode, ' - ', Nama)", 'KonsentrasiKode', $w['KonsentrasiID'], "ProdiID='$w[ProdiID]' and KodeID='$w[KodeID]'", 'KonsentrasiID');
  $optjenmk = GetOption2('jenismk', "concat(Singkatan, ' - ', Nama)", 'Singkatan', $w['JenisMKID'], "ProdiID='$w[ProdiID]' and KodeID='$w[KodeID]'", 'JenisMKID');
  $optjenpil = GetOption2('jenispilihan', "concat(Singkatan, ' - ', Nama)", 'Singkatan', $w['JenisPilihanID'], "ProdiID='$w[ProdiID]' and KodeID='$w[KodeID]'", 'JenisPilihanID');
  $optjenkur = GetOption2('jeniskurikulum', "concat(Singkatan, ' - ', Nama)", 'Singkatan', $w['JenisKurikulumID'], "ProdiID='$w[ProdiID]' and KodeID='$w[KodeID]'", 'JenisKurikulumID');
  $optpj = GetOption2('dosen', "concat(Nama, ', ', Gelar)", "Nama", $w['Penanggungjawab'], "KodeID='".KodeID."' and INSTR(ProdiID, '.$_SESSION[prodi].')>0", "Login");
  $snm = session_name(); $sid = session_id();
  // Tampilkan form
  //TuliskanScriptPrasyarat();
  CheckFormScript("MKKode,Nama,Sesi,SKS");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='data' method=POST onSubmit=\"return CheckForm(data)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='MKID' value='$w[MKID]'>
  <input type=hidden name='prodi' value='$w[ProdiID]'>
  <input type=hidden name='KodeID' value='$w[KodeID]'>
  <input type=hidden name='KurikulumID' value='$w[KurikulumID]'>
  <input type=hidden name='sub' value='MKSav'>
  
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Kode Matakuliah</td><td class=ul><input type=text name='MKKode' value='$w[MKKode]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Nama Matakuliah</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=100></td></tr>
  <tr><td class=inp>Nama (Inggris)</td><td class=ul><input type=text name='Nama_en' value='$w[Nama_en]' size=40 maxlength=100></td></tr>
  <tr><td class=inp>Singkatan</td><td class=ul><input type=text name='Singkatan' value='$w[Singkatan]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>KurikulumID</td><td class=ul><select name='KurikulumID'>$optkuri</select></td></tr>
  <tr><td class=inp>Punya Responsi/Lab?</td><td class=ul><input type=checkbox name='res' $chkres value='Y'></td></tr>
  <tr><td class=inp>Jenis</td><td class=ul><select name='JenisMKID'>$optjenmk</select></td></tr>
  <tr><td class=inp>Pilihan Wajib</td><td class=ul><select name='JenisPilihanID'>$optjenpil</select></td></tr>
  <tr><td class=inp>Pilihan Kurikulum</td><td class=ul><select name='JenisKurikulumID'>$optjenkur</select></td></tr>
  <tr><td class=inp>Matakuliah Wajib?</td><td class=ul><input type=checkbox name='Wajib' value='Y' $_wjb></td></tr>
  <tr><td class=inp>Konsentrasi</td><td class=ul><select name='KonsentrasiID'>$optkons</select></td></tr>
  <tr><td class=inp>Sesi</td><td class=ul><input type=text name='Sesi' value='$w[Sesi]' size=3 maxlength=3></td></tr>
  <tr><td class=inp>SKS</td><td class=ul><input type=text name='SKS' value='$w[SKS]' size=3 maxlength=3></td></tr>
  <tr><td class=inp nowrap>&nbsp;» SKS Tatap Muka</td><td class=ul><input type=text name='SKSTatapMuka' value='$w[SKSTatapMuka]' size=3 maxlength=3></td></tr>
  <tr><td class=inp nowrap>&nbsp;» SKS Praktikum</td><td class=ul><input type=text name='SKSPraktikum' value='$w[SKSPraktikum]' size=3 maxlength=3></td></tr>
  <tr><td class=inp nowrap>&nbsp;» SKS Praktek Lapangan</td><td class=ul><input type=text name='SKSPraktekLap' value='$w[SKSPraktekLap]' size=3 maxlength=3></td></tr>
  <tr><td class=inp>SKS Minimal</td><td class=ul><input type=text name='SKSMin' value='$w[SKSMin]' size=3 maxlength=3></td></tr>
  <tr><td class=inp>IPK Minimal</td><td class=ul><input type=text name='IPKMin' value='$w[IPKMin]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>KKN-PPM?</td>
      <td class=ul><input type=checkbox name='KKN' value='Y' $_kkn />
      </td></tr>
  <tr><td class=inp>Praktek Lapangan?</td>
      <td class=ul><input type=checkbox name='PraktekKerja' value='Y' $_pk />
      </td></tr>
  <tr><td class=inp>Komprehensif?</td>
      <td class=ul><input type=checkbox name='Komprehensif' value='Y' $_kp />
      </td></tr>
  <tr><td class=inp>Tugas Akhir?</td>
      <td class=ul><input type=checkbox name='TugasAkhir' value='Y' $_ta />
      *) Jika dicentang,<br />
      maka MK ini dihitung sebagai Skripsi/TA
      </td></tr>
    <tr><td class=inp>Bukan Biaya PerSKS?</td>
      <td class=ul><input type=checkbox name='PerSKS' value='N' $_persks />
      *) Jika dicentang,<br />
      Biaya tidak dihitung berdasarkan jumlah SKS
      </td></tr>
  <tr><td class=inp>Penanggung jawab</td><td class=ul><select name='Penanggungjawab'>$optpj</select></td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul><textarea name='Keterangan' cols=30 rows=4>$w[Keterangan]</textarea></td></tr>
  <tr><td class=inp>Tidak aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $_na></td></tr>
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset Name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]&$snm=$sid'\">
    $pra</td></tr>
  </form></table>";
  
  /*
    <tr><td class=inp1>Matakuliah Prasyarat</td>
    <td class=ul><input type=text name='Prasyarat' value='$w[Prasyarat]' size=40 maxlength=200>
    <a href='javascript:prasyarat(data)'>Cari</a></td></tr>
  */
}
function TuliskanScriptPrasyarat() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function prasyarat(frm){
    lnk = "cetak/cariprasyarat.php?MKID="+frm.MKID.value+"&Prasyarat="+frm.Prasyarat.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </SCRIPT>
EOF;
}
function MKSav() {
  $md = $_REQUEST['md'] +0;
  $MKID = $_REQUEST['MKID'];
  $MKKode = sqling($_REQUEST['MKKode']);
  $KonsentrasiID = $_REQUEST['KonsentrasiID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Nama_en = sqling($_REQUEST['Nama_en']);
  $Singkatan = sqling($_REQUEST['Singkatan']);
  $KurikulumID = strtoupper(sqling($_REQUEST['KurikulumID']));
  $JenisMKID = $_REQUEST['JenisMKID'];
  $JenisPilihanID = $_REQUEST['JenisPilihanID'];
  $JenisKurikulumID = $_REQUEST['JenisKurikulumID'];
  $Wajib = (empty($_REQUEST['Wajib']))? 'N' : $_REQUEST['Wajib'];
  $Sesi = $_REQUEST['Sesi']+0;
  $Deskripsi = sqling($_REQUEST['Deskripsi']);
  $SKS = $_REQUEST['SKS']+0;
  $SKSTatapMuka = $_REQUEST['SKSTatapMuka']+0;
  $SKSPraktikum = $_REQUEST['SKSPraktikum']+0;
  $SKSPraktekLap = $_REQUEST['SKSPraktekLap']+0;
  $SKSMin = $_REQUEST['SKSMin']+0;
  $IPKMin = $_REQUEST['IPKMin'];
  $KurikulumID = $_REQUEST['KurikulumID'];
  $ProdiID = $_REQUEST['prodi'];
  $KodeID = $_REQUEST['KodeID'];
  $Penanggungjawab = $_REQUEST['Penanggungjawab'];
  $Prasyarat = $_REQUEST['Prasyarat'];
  $PraktekKerja = (empty($_REQUEST['PraktekKerja']))? 'N' : $_REQUEST['PraktekKerja'];
  $Komprehensif = (empty($_REQUEST['Komprehensif']))? 'N' : $_REQUEST['Komprehensif'];
  $TugasAkhir = (empty($_REQUEST['TugasAkhir']))? 'N' : $_REQUEST['TugasAkhir'];
  $KKN = (empty($_REQUEST['KKN']))? 'N' : $_REQUEST['KKN'];
  $PerSKS = (empty($_REQUEST['PerSKS']))? 'Y' : $_REQUEST['PerSKS'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $RES = (empty($_REQUEST['res']))? 'N' : $_REQUEST['res'];
  $Keterangan = sqling($_REQUEST['Keterangan']);
  //
  if ($md == 0) {
    $s = "update mk set MKKode='$MKKode', KonsentrasiID='$KonsentrasiID',
      Nama='$Nama', Nama_en='$Nama_en', Singkatan='$Singkatan',
	  KurikulumID='$KurikulumID',
      JenisMKID='$JenisMKID', JenisPilihanID='$JenisPilihanID',
      JenisKurikulumID='$JenisKurikulumID',
      Wajib='$Wajib', Sesi='$Sesi',
      Deskripsi='$Deskripsi',
      SKS='$SKS', SKSTatapMuka='$SKSTatapMuka',
      SKSPraktikum='$SKSPraktikum', SKSPraktekLap='$SKSPraktekLap',
      SKSMin='$SKSMin', IPKMin='$IPKMin',
      Penanggungjawab='$Penanggungjawab',
      Prasyarat='$Prasyarat',
      KKN = '$KKN',
      PraktekKerja = '$PraktekKerja',
	  Komprehensif = '$Komprehensif',
	  TugasAkhir = '$TugasAkhir',
      PerSKS = '$PerSKS',
      Keterangan = '$Keterangan',
      NA='$NA', Responsi='$RES',
      TglEdit=now(), LoginEdit='$_SESSION[_Login]'
      where MKID='$MKID'";
    $r = _query($s);
    echo "<script>window.location='?mnux=$_SESSION[mnux]';</script>";
  }
  else {
    // Cek dulu
    $ada = GetFields("mk", "KurikulumID='$KurikulumID' and MKKode", $MKKode, "*");
    if (empty($ada)) {
      $s = "insert into mk (KurikulumID, KodeID, ProdiID, KonsentrasiID,
        MKKode, Nama, Nama_en, Singkatan,
        JenisMKID, JenisPilihanID, JenisKurikulumID,
        Wajib, Sesi, Deskripsi,
        SKS, SKSTatapMuka,
        SKSPraktikum, SKSPraktekLap,
        SKSMin, IPKMin, Prasyarat, Penanggungjawab, Responsi,
        KKN, PraktekKerja, Komprehensif, TugasAkhir, Keterangan,
        NA, TglBuat, LoginBuat)
        values ('$KurikulumID', '".KodeID."', '$ProdiID', '$KonsentrasiID',
        '$MKKode', '$Nama', '$Nama_en', '$Singkatan',
        '$JenisMKID', '$JenisPilihanID', '$JenisKurikulumID',
        '$Wajib', '$Sesi', '$Deskripsi',
        '$SKS', '$SKSTatapMuka',
        '$SKSPraktikum', '$SKSPraktekLap',
        '$SKSMin', '$IPKMin', '$Prasyarat', '$Penanggungjawab', '$RES',
        '$KKN', '$PraktekKerja', '$Komprehensif', '$TugasAkhir', '$Keterangan',
        '$NA', now(), '$_SESSION[_Login]')";
      $r = _query($s);
      echo "<script>window.location='?mnux=$_SESSION[mnux]';</script>";
    }
    else echo ErrorMsg("Tidak Ditambahkan",
      "Matakuliah dengan kode <b>$MKKode</b> tidak dapat ditambahkan karena sudah ada di kurikulum ini.");
  }
}

// *** Edit Prasyarat ***
function TampilkanHeaderMatakuliah($mk) {
  global $mnux, $pref;
  echo "<table class=box cellspacing=1 cellpadding=4 width=800>
  <tr><td class=inp>Kode MK:</td>
      <td class=ul>$mk[MKKode]</td>
      <td class=inp>Program Studi</td>
      <td class=ul>$mk[ProdiID] - $mk[PRD]</td>
      <td class=inp>Kurikulum</td>
      <td class=ul>$mk[KUR]</td></tr>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul>$mk[Nama]</td>
      <td class=inp>Jumlah SKS:</td>
      <td class=ul>$mk[SKS]</td>
      <td class=inp>Penanggungjawab:</td>
      <td class=ul>$mk[PJ]&nbsp;</td></tr>
  <tr><td class=inp>Pilihan:</td>
      <td class=ul colspan=3>
      <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$mnux&$pref=MK'\">
      <input type=button name='Edit' value='Edit MK' onClick=\"location='?mnux=$mnux&$pref=MK&sub=MKEdt&md=0&mkid=$mk[MKID]'\"></td>
      <td class=inp>Prasyarat</td><td class=ul>$mk[Prasyarat]</td></tr>
  </table>";
  TampilkanJudul("Prasyarat Matakuliah");
}
function PraEdt() {
  $mkid = $_REQUEST['mkid'];
  $mk = GetFields("mk mk left outer join prodi prd on mk.ProdiID=prd.ProdiID
    left outer join kurikulum kur on mk.KurikulumID=kur.KurikulumID
    left outer join dosen d on mk.Penanggungjawab=d.Login", 
    'mk.MKID', $mkid, 
    "mk.*, kur.Nama as KUR, prd.Nama as PRD, concat(d.Nama, ', ', d.Gelar) as PJ");
  TampilkanHeaderMatakuliah($mk);
  TampilkanTambahPrasyarat($mk);
  TampilkanDaftarPrasyarat($mk);
}
function TampilkanDaftarPrasyarat($mk) {
  global $mnux, $pref;
  $s = "select mp.*, mk.MKKode, mk.Nama, concat(n.Nama, ' (', n.Bobot, ')') as NLI
    from mkpra mp
      left outer join mk mk on mp.PraID=mk.MKID
      left outer join nilai n on mp.NilaiID=n.NilaiID
    where mp.MKID='$mk[MKID]'
    order by mk.MKKode";
  $r = _query($s); $n = 0;
  echo "<table class=box cellspacing=1 cellpadding=4 width=800>
    <tr><th class=ttl width=20>#</th>
    <th class=ttl width=70>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Alternatif</th>
    <th class=ttl width=20>Nilai</th>
    <th class=ttl width=20>Hapus</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[MKAlternatif]&nbsp;</td>
    <td class=ul>$w[Nilai]&nbsp;</td>
    <td class=ul align=center><a href='?mnux=$mnux&$pref=MK&sub=PraDel&BypassMenu=1&mkid=$mk[MKID]&slnt=matakuliah.MK&slntx=PraDel&del=$w[MKPraID]'><img src='img/del.gif'></a></td>
    </tr>";
  }
  echo "</table></p>";
}
function TampilkanTambahPrasyarat($mk='') {
  global $mnux, $pref;
  $optnil = GetOption2('nilai', "concat(Nama, ' - ', Bobot)", 'Bobot desc', '', "KodeID='$_SESSION[KodeID]' and ProdiID='$_SESSION[prodi]'", "NilaiID");
  $optpra = GetOption2("mk", "concat(MKKode, ' - ', Nama, ' (', SKS, ')')",
    'MKKode', '', "ProdiID='$_SESSION[prodi]'", "MKID");
  echo "<table class=box cellspacing=1 cellpadding=4 width=800>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='MK'>
  <input type=hidden name='sub' value='PraSav'>
  <input type=hidden name='mkid' value='$mk[MKID]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <input type=hidden name='slnt' value='matakuliah.MK'>
  <input type=hidden name='slntx' value='PraSav'>
  <tr><td class=inp>Tambah Prasyarat</td><td class=ul><select name='PraID'>$optpra</select></td></tr>
  <tr><td class=inp>Prasyarat Alternatif</td><td class=ul><input type=text name='MKAlternatif' size=60 maxlength=255><br />
      <sup>*) Kode MK diapit tanda titik (.)</sup><br />
	  <sup>Contoh: .MKD2101.MKD2102.MKD2103.</sup>
	  </td></tr>
  <tr><td class=inp>Nilai Minimal</td><td class=ul><select name='NilaiID'>$optnil</select>
    <input type=submit name='Simpan' value='Simpan'></td></tr>
  </table>";
}
function PraSav() {
	$mkid = $_REQUEST['mkid'];
	$PraID = $_REQUEST['PraID'];
	$NilaiID = $_REQUEST['NilaiID']+0;
	$MKAlternatif = $_REQUEST['MKAlternatif'];
	$Nilai = ($NilaiID == 0)? 'F' : GetaField('nilai', 'NilaiID', $NilaiID, 'Nama');
	$Bobot = GetaField('nilai', 'NilaiID', $NilaiID, 'Bobot')+0;
	if((empty($mkid))or(empty($PraID))or(empty($NilaiID))or(empty($MKAlternatif))){
    die(ErrorMsg('Error',
        "Pilihan Tambah Prasyarat, Prasyarat Alternatif dan Nilai Minima Tidak boleh kosong.<br />
        Hubungi Sysadmin untuk informasi lebih lanjut.
        <hr size=1 color=silver />
         <input type=button name='Tutup' value='Kembali' onClick=\"window.parent.location.href ='?mnux=$_SESSION[mnux]&mkid=$mkid&sub=PraEdt'\" />
		"));
	}else{
	  if (!empty($PraID)) {
		$ada = GetaField('mkpra', "MKID='$mkid' and PraID", $PraID, "MKPraID");
		if (empty($ada)) {
		  $mk = GetFields('mk', "MKID", $mkid, "*");
		  $pr = GetFields('mk', "MKID", $PraID, "*");
		  $s = "insert into mkpra (MKID, PraID, NilaiID, MKKode, MKPra, MKAlternatif, Bobot, Nilai)
			values('$mkid', '$PraID', '$NilaiID', '$mk[MKKode]', '$pr[MKKode]', '$MKAlternatif', '$Bobot', '$Nilai')";
		  $r = _query($s);
		  UpdatePrasyarat($mkid);
		  BerhasilSimpan("?mnux=$_SESSION[mnux]&mkid=$mkid&sub=PraEdt", 100);
		} 
		else echo ErrorMsg("Tidak Dapat Disimpan",
		  "Prasyarat sudah ada, prasyarat tidak akan ditambahkan.");
	  }
	}
}
function PraDel() {
  $del = $_REQUEST['del'];
  $mkid = $_REQUEST['mkid'];
  $s = "delete from mkpra where MKPraID='$del' ";
  $r = _query($s);
  UpdatePrasyarat($mkid);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mkid=$mkid&sub=PraEdt", 100);
}
function UpdatePrasyarat($mkid) {
//GetArrayTable($sql, $key, $label, $separator=', ') {
  $pra = GetArrayTable("select mp.PraID, mk.MKKode
    from mkpra mp
      left outer join mk mk on mp.PraID=mk.MKID
    where mp.MKID='$mkid'
    order by mk.MKKode", 'MKID', 'MKKode', ',');
  $s = "update mk set Prasyarat='$pra' where MKID='$mkid' ";
  $r = _query($s);
}
function CetakMK() {
  global $_lf;
  $mxc = 114;
  $mxb = 50;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $kurid = $_SESSION['kurid_'.$_SESSION['prodi']];
  $kur = GetFields('kurikulum', 'KurikulumID', $kurid, '*');
  $prd = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $hdr = str_pad("Daftar Matakuliah $prd ($_SESSION[prodi])", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Kurikulum: $kur[KurikulumKode], $kur[Nama]", $mxc, ' ', STR_PAD_BOTH).$_lf.$grs.
    str_pad('No.', 6). str_pad('Sesi', 6). str_pad('Kode', 10). str_pad('Nama', 45).
    str_pad('SKS', 5). str_pad('Wjb', 4). " R/L " . " Prasyarat"  . $_lf.$grs;
  // file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  // data
  $s = "select mk.MKID, mk.MKKode, mk.Nama, mk.Nama_en, mk.SKS, mk.Sesi, mk.Wajib, mk.Responsi
    from mk
    where mk.KurikulumID=$kurid and NA='N'
    order by mk.Sesi,mk.MKKode";
  $r = _query($s); $n = 0; $ttl = 0;
  $brs = 0;
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
    $n++;
    $res = ($w['Responsi'] == 'Y') ? str_pad('Ya', 5) : str_pad(' ', 5);
    if ($brs >= $mxb) {
      fwrite($f, chr(12));
      fwrite($f, $hdr);
      $brs = 0;
    }
    $brs++;
    $wjb = ($w['Wajib'] == 'Y')? '*' : '';
    $ttl += $w['SKS'];
    $_pras = GetArrayTable("select mk.MKKode, mkpra.Nilai
      from mkpra left outer join mk on mkpra.PraID=mk.MKID
      where mkpra.MKID=$w[MKID] order by mk.MKKode", 'MKKode', 'MKKode', ' ', '', 'Nilai');
    fwrite($f, str_pad($n, 6).
      str_pad($w['Sesi'], 6).
      str_pad($w['MKKode'], 10).
      str_pad($w['Nama'], 45).
      str_pad($w['SKS'], 5).
      str_pad($wjb, 5, ' ', STR_PAD_BOTH).
      $res.
      $_pras.

      $_lf);
  }
  fwrite($f, $grs);
  fwrite($f, str_pad("Total : ", 67, ' ', STR_PAD_LEFT).
    str_pad($ttl, 5).$_lf);
  fclose($f);
  TampilkanFileDWOPRN($nmf, "matakuliah");
}
?>
