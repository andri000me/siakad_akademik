<?php
// *** Functions ***
function DefMKSet() {
  global $mnux, $pref, $token;
  TampilkanPilihanKurikulum();
  //TampilkanPilihanProdi($mnux, '', $pref, "MK");
  if (!empty($_SESSION['prodi'])) {
    TampilkanMenuMKSetara(); 
    TampilkanMK();
  }
}
function TampilkanMenuMKSetara() {
  global $mnux, $pref, $token;
  echo "<p><a href='?' onClick=\"CetakDaftar('".$_SESSION['kurid_'.$_SESSION['prodi']]."')\">Cetak</a>
			<script>
				function CetakDaftar(kurid)
				{	lnk = '$_SESSION[mnux].MKSetara.cetak.php?kurid='+kurid;
					win2 = window.open(lnk, \"\", \"width=600, height=400, scrollbars, status, resizable\");
					if (win2.opener == null) childWindow.opener = self;
				}	
			</script></p>";
}
function TampilkanMK() {
  if (!empty($_SESSION['kurid_'.$_SESSION['prodi']])) TampilkanMK1();
}
function TampilkanMK1() {
  global $mnux, $pref, $arrID;
  $arrKurid = GetFields('kurikulum', "KurikulumID", $_SESSION['kurid_'.$_SESSION['prodi']], '*');
  $s = "select mk.*
    from mk mk
    where mk.KurikulumID='$arrKurid[KurikulumID]'
    order by mk.MKKode";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=800>
    <tr><th class=ttl>No</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Nama</th>
    <th class=ttl>SKS</th>
    <th class=ttl colspan=2>Setara</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $MKSetara = TRIM($w['MKSetara'], '.');
    $MKSetara = str_replace('.', ', ', $MKSetara);
    echo "<tr>
    <td class=inp width=24>$n</td>
    <td class=ul width=100>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul width=24 align=right>$w[SKS]</td>
    <td class=ul width=10><a href='?mnux=$mnux&$pref=$_SESSION[$pref]&mkkode_$_SESSION[prodi]=$w[MKKode]&sub=EdtSet'><img src='img/edit.png'></a></td>
    <td class=ul>$MKSetara&nbsp;</td>
    </tr>";
  }
  echo "</table></p>";
}
function TampilkanHeaderMatakuliah($mk) {
  global $mnux, $pref;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
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
      <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]'\">
      </td>
      <td class=inp>Prasyarat</td><td class=ul>$mk[Prasyarat]</td></tr>
  </table></p>";
}
function EdtSet() {
  global $mnux, $pref, $arrID;
  $prodi = $_SESSION['prodi'];
  $mkkode = $_SESSION['mkkode_'.$prodi];
  $kurid = $_SESSION['kurid_'.$prodi];
  $kuriset = GetSetVar('kuriset');
  //$mk = GetFields('mk', "KurikulumID='$kurid' and MKKode", $mkkode, '*');
  $mk = GetFields("mk mk left outer join prodi prd on mk.ProdiID=prd.ProdiID
    left outer join kurikulum kur on mk.KurikulumID=kur.KurikulumID
    left outer join dosen d on mk.Penanggungjawab=d.Login", 
    "mk.KurikulumID='$kurid' and mk.MKKode", $mkkode, 
    "mk.*, kur.Nama as KUR, prd.Nama as PRD, concat(d.Nama, ', ', d.Gelar) as PJ");
  /*
  $optkurikulum = GetOption2('kurikulum', "concat(KurikulumKode, ' - ', Nama)", 'KurikulumID', $_SESSION['kurid_'], "ProdiID='$_SESSION[prodi]'", 'KurikulumID');  
  $optmk = GetOption2('mk', "concat(MKKode, ' - ', Nama)", 'MKKode', '', 
    "MKKode<>'$mkkode' and KurikulumID=".$_SESSION['kurid_'.$_SESSION['prodi']], 'MKKode'); 
  */
  $optkuri = GetOption2('kurikulum', "concat(KurikulumKode, ' - ', Nama)", 'KurikulumID', $_SESSION['kuriset'], "ProdiID='$prodi' and KodeID='".KodeID."'", 'KurikulumID');  	
  $optmk = GetOption2('mk', "concat(MKKode, ' - ', Nama)", 'MKKode', '', 
    "MKKode<>'$mkkode' and KurikulumID='".$kuriset."'", 'MKKode'); 
	
	
  // Headernya
  TampilkanHeaderMatakuliah($mk);
  $setara = GetMKSetara($mk['MKSetara'], $mkkode);
  //die(var_dump($mkkode));
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=5><b>Matakuliah Setara</td></tr>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='mkkode_$prodi' value='$mkkode'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='sub' value='EdtSetSav'>
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=inp colspan=2>Kurikulum:</td>
    <td class=ul colspan=3><select name='kuriset' onChange=\"window.location='?mnux=$mnux&$pref=$_SESSION[$pref]&mkkode_$_SESSION[prodi]=$mkkode&sub=EdtSet&kuriset='+this.value\">$optkuri</select>
  <tr><td class=inp colspan=2>Tambahkan:</td>
    <td class=ul colspan=3><select name='mkkode_add'>".$optmk."</select>
  <input type=submit name='Tambahkan' value='Tambahkan'></td></tr>
  </form>
  $setara
  </table></p>"; 
}
function EdtSetSav() {
  $prodi = $_SESSION['prodi'];
  $mkkode = $_REQUEST['mkkode_'.$prodi];
  $kurid = $_SESSION['kurid_'.$prodi];
  $add = $_REQUEST['mkkode_add'];
  $mk = GetFields('mk', "KurikulumID='$kurid' and MKKode", $mkkode, '*');
  $_setara = TRIM($mk['MKSetara'], '.');
  // Simpan
  $arrSet = array();
  if (!empty($_setara)) {
    $arrSet = explode('.', $_setara);
  }
  $key = array_search($add, $arrSet);
  if ($key === false) {
    $arrSet[] = $add;
    $arrSet = array_unique($arrSet);
    sort($arrSet);
    $_setara = '.'.implode('.', $arrSet).'.';
    $s = "update mk set MKSetara='$_setara' where MKID='$mk[MKID]' ";
    $r = _query($s);
	
  }
  // Tambahkan pula di matakuliah setaranya
  $mk1 = GetFields('mk', "KurikulumID='$kurid' and MKKode", $add, '*');
  $_setara1 = TRIM($mk1['MKSetara'], '.');
  $arrSet1 = array();
  if (!empty($_setara1)) {
    $arrSet1 = explode('.', $_setara1);
  }
  $key1 = array_search($mkkode, $arrSet);
  if ($key === false) {
    $arrSet1[] = $mkkode;
    $arrSet1 = array_unique($arrSet1);
    sort($arrSet1);
    $_setara1 = '.'.implode('.', $arrSet1).'.';
    $s1 = "update mk set MKSetara='$_setara1' where MKID='$mk1[MKID]' ";
    $r1 = _query($s1);
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&sub=EdtSet", 100);
}
function GetMKSetara($stara, $mkkode) {
  global $mnux, $pref, $token;
  $stara = TRIM($stara, '.');
  $arrset = explode('.', $stara);
  $a = '';
  for ($i = 0; $i < sizeof($arrset); $i++) {
    $kd = $arrset[$i];
    if (!empty($kd)) {
      $kurid = $_SESSION['kurid_'.$_SESSION['prodi']];
      //die($kurid);
      $mk = GetFields('mk', "ProdiID='$_SESSION[prodi]' and MKKode", $kd, "Nama, SKS");
      $n = $i+1;
      $a .= "<tr><td class=inp>$n</td>
      <td class=ul>$kd</td>
      <td class=ul>$mk[Nama]</td>
      <td class=ul>$mk[SKS]</td>
      <td class=ul align=center><a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=EdtSetDel&mkkode_$_SESSION[prodi]=$mkkode&del=$kd'><img src='img/del.gif'></a></td>
      </tr>";
    }
  }
  return $a;
}
function EdtSetDel() {
  $mkkode = $_SESSION['mkkode_'.$_SESSION['prodi']];
  $del = $_REQUEST['del'];
  $kurid = $_SESSION['kurid_'.$_SESSION['prodi']];
  $mk = GetFields('mk', "KurikulumID='$kurid' and MKKode", $mkkode, '*');
  $setara = $mk['MKSetara'];
  $setara = str_replace($del.'.', '', $setara);
  $s = "update mk set MKSetara='$setara' where MKID='$mk[MKID]' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&sub=EdtSet", 1000);
}
?>
