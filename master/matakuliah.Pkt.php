<?php
// Author: Emanuel Setio Dewo
// 30 Jan 2006

function DefPkt() {
  global $mnux, $pref, $token;
  //TampilkanPilihanKurikulum();
  TampilkanPilihanProdi($mnux, '', $pref, "Pkt");
  if (!empty($_SESSION['prodi'])) {
    //TampilkanMenuPaket();
    TampilkanPaket();
  }
}
function TampilkanMenuPaket() {
  global $mnux, $pref;
  echo "<p><a href='?mnux=$mnux&$pref=$_SESSION[$pref]&md=1&sub=PktEdt'>Tambah Paket</a> |
  <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=PrnAll'>Cetak Semua Paket</a></p>";
}
function TampilkanPaket1() {
  //$kri = TampilkanDaftarPaket();
  $knn = '';
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td valign=top>$kri</td>
  <td valign=top>$knn</td></tr>
  </table></p>";
}
function TampilkanPaket() {
  global $mnux, $pref;
  $s = "select mp.*, kr.Nama as KUR
    from mkpaket mp
    left outer join kurikulum kr on mp.KurikulumID=kr.KurikulumID
    where mp.KodeID='$_SESSION[KodeID]' and mp.ProdiID='$_SESSION[prodi]'
    order by mp.Nama";
  $r = _query($s);
  $n=0;
  $sprt = "&#8718;";
  $h = "<table class=box cellspacing=1 width=800>
    <tr>
        <td colspan=2 class=ul1><font size=+1>Daftar Paket</font></td>
        <td class=ul1 colspan=6>
          <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&md=1&sub=PktEdt'>Tambah Paket</a> $sprt
          <a href='?mnux=$_SESSION[mnux]&mk=Pkt&sub='>Refresh</a>
        </td>
    </tr>
    <tr>
    <th class=ttl rowspan=2 width=20>#</th>
    <th class=ttl rowspan=2>Nama Paket</th>
    <th class=ttl rowspan=2>Kurikulum</th>
    <th class=ttl rowspan=2>Deskripsi</th>
    <th class=ttl rowspan=2 width=20>NA</th>
	<th class=ttl rowspan=2 width=20>Cetak</th>
    <th class=ttl colspan=3>Isi Paket</th>
    </tr>
    
    <tr>
    <th class=ttl width=20>Edit</th>
    <th class=ttl width=20>MK</th>
    <th class=ttl width=20>SKS</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $arrjml = GetFields("mkpaketisi mpi left outer join mk mk on mpi.MKID=mk.MKID", 
      'mpi.MKPaketID', 
      $w['MKPaketID'], 
      "count(*) as JMLMK, sum(mk.SKS) as JMLSKS");
    $h .= "<tr>
      <td class=inp>$n</td>
      <td $c width=200>
        <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&md=0&mpid=$w[MKPaketID]&sub=PktEdt'><img src='img/edit.png' border=0>
        $w[Nama]</a></td>
      <td $c width=180>$w[KUR]&nbsp;</td>
      <td $c>$w[Deskripsi]&nbsp;</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
	  <td $c align=center><a href='#' onClick=\"CetakPaketMataKuliah('$w[MKPaketID]')\"><img src='img/printer2.gif' width=15></a></td>
      <td class=inp1 align=center><a href='?mnux=$mnux&$pref=$_SESSION[$pref]&mpid=$w[MKPaketID]&sub=IsiPkt'><img src='img/fileshare.gif' border=0></a></td>
      <td $c align=right>$arrjml[JMLMK]</td>
      <td $c align=right>$arrjml[JMLSKS]</td>
      </tr>";
  }
  echo $h. "</table>
	<script>
		function CetakPaketMataKuliah(paketid)
		{	lnk = \"$_SESSION[mnux].cetakpaket.php?MKPaketID=\"+paketid;
			win2 = window.open(lnk, \"\", \"width=600, height=400, scrollbars, status\");
			if (win2.opener == null) childWindow.opener = self;
		}
	</script>";
}
function PktEdt() {
  global $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('mkpaket', 'MKPaketID', $_REQUEST['mpid'], '*');
    $jdl = "Edit Paket Matakuliah";
  }
  else {
    $w = array();
    $w['MKPaketID'] = 0;
    $w['KodeID'] = $_SESSION['KodeID'];
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['KurikulumID'] = 0;
    $w['Nama'] = '';
    $w['Deskripsi'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Paket Matakuliah";
  }
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  $optkurid = GetOption2("kurikulum", "concat(KurikulumKode, ' - ', Nama)",
    "KurikulumKode", $w['KurikulumID'], "KodeID='$w[KodeID]' and ProdiID='$w[ProdiID]'", "KurikulumID", 1);
  $NamaProdi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  // Tampilkan
  CheckFormScript("Nama,KurikulumID");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='sub' value='PktSav'>
  <input type=hidden name='MKPaketID' value='$w[MKPaketID]'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <input type=hidden name='KodeID' value='$w[KodeID]'>
  <input type=hidden name='ProdiID' value='$w[ProdiID]'>
  
  <tr><td colspan=2 class=ul><sup>$_SESSION[prodi]</sup> <font size=+1>$NamaProdi</font></td></tr>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Nama Paket</td>
      <td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td>
      </tr>
  <tr><td class=inp>Kurikulum</td>
      <td class=ul><select name='KurikulumID'>$optkurid</select></td>
      </tr>
  <tr><td class=inp>Deskripsi</td>
      <td class=ul><textarea name='Deskripsi' cols=35 rows=3>$w[Deskripsi]</textarea></td>
      </tr>
  <tr><td class=inp>Tidak aktif?</td>
      <td class=ul><input type=checkbox name='NA' value='Y' $_na></td> Centang jika tdk aktif
      </tr>
  <tr><td colspan=2 class=ul1 align=center>
      <input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]&$snm=$sid'\"></td>
      </tr>
  </form>
  </table></p>";
}
function PktSav() {
  $md = $_REQUEST['md']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $KurikulumID = $_REQUEST['KurikulumID'];
  $Deskripsi = sqling($_REQUEST['Deskripsi']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $KodeID = $_REQUEST['KodeID'];
  $ProdiID = $_REQUEST['ProdiID'];
  
  if ($md == 0) {
    $s = "update mkpaket 
      set Nama='$Nama', 
          KurikulumID='$KurikulumID', 
          Deskripsi='$Deskripsi', 
          NA='$NA',
          LoginEdit = '$_SESSION[_Login]',
          TglEdit = now()
      where MKPaketID='$_REQUEST[MKPaketID]' ";
  }
  else {
    $s = "insert into mkpaket 
      (Nama, KodeID, ProdiID, KurikulumID,
      Deskripsi, NA, LoginBuat, TglBuat)
      values 
      ('$Nama', '$KodeID', '$ProdiID', '$KurikulumID',
      '$Deskripsi', '$NA', '$_SESSION[_Login]', now())";
  }
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=Pkt&sub=", 100);
}

// *** Isi Paket MK ***

function IsiPkt() {
  global $mnux, $pref;
  $mpid = $_REQUEST['mpid'];
  $s = "select mpi.*, mk.MKKode, mk.Nama, mk.SKS
    from mkpaketisi mpi
    left outer join mk on mpi.MKID=mk.MKID
    where mpi.MKPaketID='$mpid'
    order by mk.Sesi, mk.MKKode";
  $r = _query($s);
  $n = 0;
  $arrPaket = GetFields('mkpaket', "MKPaketID", $mpid, 'Nama, KurikulumID, ProdiID');
  $NamaPaket = $arrPaket['Nama'];
  $NamaKurikulum = GetaField('kurikulum', 'KurikulumID', $arrPaket['KurikulumID'], 'Nama');
  $NamaProdi = GetaField('prodi', 'ProdiID', $arrPaket['ProdiID'], 'Nama');
  $optmkid = GetOption2('mk', "concat(Nama, ' - ', MKKode, ' (', SKS, ')')", 'Nama', '',
    "KurikulumID='$arrPaket[KurikulumID]'", 'MKID');
  $kuraktif = GetaField('kurikulum', "ProdiID = 99 and NA", 'N', "KurikulumID");
  $optmkmpk = GetOption2('mk', "concat(MKKode, ' - ', Nama, ' (', SKS, ')')", 'MKKode', '',
    "KurikulumID='$kuraktif'", 'MKID');

  echo "<p><table class=box cellspacing=1 cellpadding=4 width=700>
    <!-- Header -->
    <tr><td class=inp colspan=2>Paket :</td>
        <td class=ul1 colspan=3><b>$NamaPaket</b></td>
        </tr>
    <tr><td class=inp colspan=2>Program Studi :</td>
        <td class=ul1 colspan=3><b>$NamaProdi</b></td>
        </tr>
    <tr><td class=inp colspan=2>Kurikulum :</td>
        <td class=ul1 colspan=3><b>$NamaKurikulum</b></td>
        </tr>
    <tr><td class=inp colspan=2>Tambah MK :</td>
        <form action='?' method=POST>
        <input type=hidden name='mnux' value='$mnux'>
        <input type=hidden name='$pref' value='$_SESSION[$pref]'>
        <input type=hidden name='mpid' value='$mpid'>
        <input type=hidden name='sub' value='IsiPktAdd'>
        <input type=hidden name='kurid' value='$arrPaket[KurikulumID]'>
        <input type=hidden name='BypassMenu' value='1' />
        
        <td class=ul1 colspan=3><select name='MKID'>$optmkid</select><br />
        <input type=submit name='Tambah' value='Tambah'>
        <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]'\">
        </td>
        </form>
    
    <tr><th class=ttl width=24>#</th>
        <th class=ttl width=100>Kode MK</th>
        <th class=ttl>Matakuliah</th>
        <th class=ttl width=40>SKS</th>
        <th class=ttl width=20 title='Hapus data'>Del</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
      <td $c>$n</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[SKS]</td>
      <td class=ul>
        <a href='?mnux=$mnux&$pref=$_SESSION[$pref]&mpiid=$w[MKPaketIsiID]&mpid=$mpid&BypassMenu=1&mkid=$w[MKID]&sub=IsiPktDel'><img src='img/del.gif' /></a>
        </td>
    </tr>";
  }
  echo "</table></p>";
}
function IsiPktDel() {
  $mpid = $_REQUEST['mpid'];
  $mkid = $_REQUEST['MKID'];
  $mpiid = $_REQUEST['mpiid'];
  $s = "delete from mkpaketisi where MKPaketIsiID='$mpiid' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=Pkt&sub=IsiPkt&mpid=$mpid", 100);
}
function IsiPktAdd() {
  $mpid = $_REQUEST['mpid'];
  $mkid = $_REQUEST['MKID'];
  $kurid = $_REQUEST['kurid'];
  $ada = GetFields('mkpaketisi', "KurikulumID='$kurid' and MKPaketID='$mpid' and MKID", $mkid, '*');
  
  if (empty($ada)) {
    $s = "insert into mkpaketisi (MKPaketID, KurikulumID, MKID, NA)
      values('$mpid', '$kurid', '$mkid', 'N')";
    $r = _query($s);
  }
  else echo ErrorMsg("Tidak Disimpan", "Matakuliah telah ada dalam daftar.");
  BerhasilSimpan("?mnux=$_SESSION[mnux]&mk=Pkt&sub=IsiPkt&mpid=$mpid", 100);
}
function PrnAll() {
  global $_lf;
  $nmf = "tmp\$_SESSION[_Login].dwoprn";
  $mxc = 80;
  $grs1 = str_pad('-', $mxc, '-').$_lf;
  $grs2 = str_pad('=', $mxc, '=').$_lf;
  $nm = GetaField("prodi", "ProdiID", $_SESSION['prodi'], "Nama");
  
  $f = fopen($nmf, 'w');
  $hdr = str_pad("Paket Matakuliah $nm ($_SESSION[prodi])", $mxc, ' ', STR_PAD_BOTH).$_lf.$_lf.$grs1;
  fwrite($f, $hdr);
  // Data
  $s = "select mp.MKPaketID, mp.Nama
    from mkpaket mp
    where mp.ProdiID='$_SESSION[prodi]'
    order by mp.Nama";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    fwrite($f, "$n. $w[Nama]\r\n".$grs1);
    $s1 = "select mpi.MKPaketIsiID, mk.MKKode, mk.Nama, mk.SKS
      from mkpaketisi mpi
        left outer join mk on mpi.MKID=mk.MKID
      where mpi.MKPaketID=$w[MKPaketID]
      order by mk.MKKode";
    $r1 = _query($s1);
    while ($w1 = _fetch_array($r1)) {
      fwrite($f, "     ".
        str_pad($w1['MKKode'], 10).
        str_pad($w1['Nama'], 30).
        str_pad($w1['SKS'], 4, ' ', STR_PAD_LEFT).
        $_lf);
    }
    fwrite($f, $grs2);
  }
  fclose($f);
  TampilkanFileDWOPRN($nmf, "matakuliah");
}
?>
