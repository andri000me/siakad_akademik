<?php
// *** Functions ***
function DftrIdentitas() {
  $s = "select * from identitas order by Kode";
  $r = _query($s);
  $n = 0;
  //echo "<p><a href='?mnux=identitas&gos=IDEdt&md=1'>Tambah Identitas</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=500>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp>$n</td>
      <td $c><a href='?mnux=$_SESSION[mnux]&md=0&kod=$w[Kode]&gos=IDEdt'><img src='img/edit.png' border=0>
      $w[Kode]</a></td>
      <td $c>$w[Nama]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function CariPTDiktiScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function caript(frm){
    lnk = "cari/cariptdikti.php?PerguruanTinggiID="+frm.KodeHukum.value+"&Cari="+frm.Nama.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}
function IDEdt() {
  CariPTDiktiScript();
  $md = $_REQUEST['md'] +0;
  if ($md == 0) {
    $w = GetFields('identitas', 'Kode', $_REQUEST['kod'], '*');
    $jdl = "Edit Identitas";
    $strkod = "<input type=hidden name='Kode' value='$w[Kode]'><b>$w[Kode]</b>";
  }
  else {
    $w = array();
    $w['Kode'] = '';
    $w['KodeHukum'] = '';
    $w['Yayasan'] = '';
	$w['Nama'] = '';
    $w['TglMulai'] = date('Y-m-d');
    $w['Alamat1'] = '';
    $w['Alamat2'] = '';
    $w['Kota'] = '';
    $w['KodePos'] = '';
    $w['Telepon'] = '';
    $w['Fax'] = '';
    $w['Email'] = '';
    $w['Website'] = '';
    $w['NoAkta'] = '';
    $w['TglAkta'] = date('Y-m-d');
    $w['NoSah'] = '';
    $w['TglSah'] = date('Y-m-d');
    $w['Logo'] = '';
    $w['Jabatan'] = '';
    $w['Pejabat'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Identitas";
    $strkod = "<input type=text name='Kode' size=15 maxlength=10>";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $_TglMulai = GetDateOption($w['TglMulai'], 'TglMulai');
  $_TglAkta = GetDateOption($w['TglAkta'], 'TglAkta');
  $_TglSah = GetDateOption($w['TglSah'], 'TglSah');
  $snm = session_name(); $sid = session_id();
  $c1 = 'class=inp'; $c2 = 'class=ul';
  // tampilan formulir
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name=data>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='gos' value='IDSav'>
  <input type=hidden name='BypassMenu' value='1' />
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td $c1>Kode</td><td $c2>$strkod</td></tr>
  <tr><td $c1>Kode Perg. Tinggi</td><td $c2><input type=text name='KodeHukum' value='$w[KodeHukum]' size=15 maxlength=10></td></tr>
  <tr><td $c1>Nama Perg. Tinggi</td><td $c2><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=100> <a href='javascript:caript(data)'>Cari</a></td></tr>
  <tr><td $c1>Yayasan / Departemen</td><td $c2><input type=text name='Yayasan' value='$w[Yayasan]' size=50 maxlength=100></td></tr>
  <tr><td $c1>Tanggal Mulai</td><td $c2>$_TglMulai</td></tr>
  <tr><td rowspan=2 $c1>Alamat</td><td $c2><input type=text name='Alamat1' value='$w[Alamat1]' size=50 maxlength=100></td></tr>
  <tr><td $c2><input type=text name='Alamat2' value='$w[Alamat2]' size=50 maxlength=100></td></tr>
  <tr><td $c1>Kota</td><td $c2><input type=text name='Kota' value='$w[Kota]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Kode Pos</td><td $c2><input type=text name='KodePos' value='$w[KodePos]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Telepon</td><td $c2><input type=text name='Telepon' value='$w[Telepon]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Fax</td><td $c2><input type=text name='Fax' value='$w[Fax]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Email</td><td $c2><input type=text name='Email' value='$w[Email]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Website</td><td $c2><input type=text name='Website' value='$w[Website]' size=50 maxlength=50></td></tr>
  <tr><td $c1>No. Akta</td><td $c2><input type=text name='NoAkta' value='$w[NoAkta]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Tanggal Akta</td><td $c2>$_TglAkta</td></tr>
  <tr><td $c1>No. Pengesahan</td><td $c2><input type=text name='NoSah' value='$w[NoSah]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Tgl. Pengesahan</td><td $c2>$_TglSah</td></tr>
  <tr><td $c1>Logo</td><td $c2><input type=text name='Logo' value='$w[Logo]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Jabatan</td><td $c2><input type=text name='Jabatan' value='$w[Jabatan]' size=50 maxlength=50></td></tr>
  <tr><td $c1>Pejabat</td><td $c2><input type=text name='Pejabat' value='$w[Pejabat]' size=50 maxlength=50></td></tr>
  <tr><td $c1>NA (tidak aktif)?</td><td $c2><input type=checkbox value='Y' $na></td></tr>
  <tr><td colspan=2 align=center>
    <input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&snm=$sid'\"></td></tr>
  
  </form></table></p>";
}
function IDSav() {
  $md = $_REQUEST['md'] +0;
  $Kode = $_REQUEST['Kode'];
  $KodeHukum = $_REQUEST['KodeHukum'];
  $Yayasan = sqling($_REQUEST['Yayasan']);
  $Nama = sqling($_REQUEST['Nama']);
  $TglMulai = $_REQUEST['TglMulai_y'].'-'.$_REQUEST['TglMulai_m'].'-'.$_REQUEST['TglMulai_d'];
  $Alamat1 = sqling($_REQUEST['Alamat1']);
  $Alamat2 = sqling($_REQUEST['Alamat2']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = $_REQUEST['KodePos'];
  $Telepon = $_REQUEST['Telepon'];
  $Fax = $_REQUEST['Fax'];
  $Email = $_REQUEST['Email'];
  $Website = $_REQUEST['Website'];
  $NoAkta = $_REQUEST['NoAkta'];
  $TglAkta = "$_REQUEST[TglAkta_y]-$_REQUEST[TglAkta_m]-$_REQUEST[TglAkta_d]";
  $NoSah = $_REQUEST['NoSah'];
  $TglSah = "$_REQUEST[TglSah_y]-$_REQUEST[TglSah_m]-$_REQUEST[TglSah_d]";
  $Logo = $_REQUEST['Logo'];
  $Jabatan = $_REQUEST['Jabatan'];
  $Pejabat = $_REQUEST['Pejabat'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // simpan
  if ($md == 0) {
    $s = "update identitas set KodeHukum='$KodeHukum', Yayasan='$Yayasan',
      Nama='$Nama', TglMulai='$TglMulai', Alamat1='$Alamat1', Alamat2='$Alamat2',
      Kota='$Kota', KodePos='$KodePos', Telepon='$Telepon', Fax='$Fax', Email='$Email', Website='$Website',
      NoAkta='$NoAkta', TglAkta='$TglAkta', NoSah='$NoSah', TglSah='$TglSah', Jabatan='$Jabatan',Pejabat='$Pejabat',
      Logo='$Logo', NA='$NA'
      where Kode='$Kode' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields('identitas', 'Kode', $w['Kode'], '*');
    if (!empty($ada)) echo ErrorMsg('Gagal Simpan', "Data identitas tidak dapat disimpan.<br>
      Kode identitas: <b>$w[Kode]</b> telah dipakai oleh <b>$ada[Nama]</b>.<br>
      Gunakan kode lain.");
    else {
      $s = "insert into identitas (Kode, KodeHukum, Yayasan, Nama, TglMulai,
        Alamat1, Alamat2, Kota, KodePos, Telepon, Fax, Email, Website,
        NoAkta, TglAkta, NoSah, TglSah, Logo, Jabatan, Pejabat, NA)
        values ('$Kode', '$KodeHukum', '$Yayasan', '$Nama', '$TglMulai',
        '$Alamat1', '$Alamat2', '$Kota', '$KodePos', '$Telepon', '$Fax', '$Email', '$Website',
        '$NoAkta', '$TglAkta', '$NoSah', '$TglSah', '$Logo', '$Jabatan', '$Pejabat', '$NA')";
      $r = _query($s);
    }
  }
  //DftrIdentitas();
  BerhasilSimpan("?mnux=$_SESSION[mnux]");
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? 'DftrIdentitas' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Identitas Perguruan Tinggi");
$gos();
?>
