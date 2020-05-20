<?php

function GetSetVar($str, $def='') {
  if (isset($_REQUEST[$str])) {
	$_SESSION[$str] = $_REQUEST[$str];
	return $_REQUEST[$str];
  }
  else {
    if (isset($_SESSION[$str])) return $_SESSION[$str];
    else {
	  $_SESSION[$str] = $def;
	  return $def;
    }
  }
}
// Ambil Pilihan Bulan
function GetMonthOption($_default=1) {
    global $arrBulan;
    $_tmp = "";
	$_max = count($arrBulan);
	for ($i=1; $i<$_max; $i++) {
	  $stri = str_pad($i, 2, '0', STR_PAD_LEFT);
	  if ($_default==$i) $_tmp = "$_tmp <option value='$stri' selected>". $arrBulan[$i] ."</option>";
	  else $_tmp = "$_tmp <option value='$stri'>". $arrBulan[$i] ."</option>";
	}
	return $_tmp;
}
// Buat Pilihan numerik
function GetNumberOption($_start, $_end, $_default=0, $interval=1, $pad=2) {
    $_tmp = "";
	for ($i=$_start; $i <= $_end; $i+=$interval) {
	  $stri = str_pad($i, $pad, '0', STR_PAD_LEFT);
	  if ($i == $_default) $_tmp = "$_tmp <option selected>$stri</option>";
	  else $_tmp = "$_tmp <option>$stri</option>";
	}
	return $_tmp;
}
function GetOption2($_table, $_field, $_order='', $_default='', $_where='', $_value='', $not=0) {
  global $strCantQuery;
	if (!empty($_order)) $str_order = " order by $_order ";
	else $str_order = "";
	if ($not==0) $strnot = "NA='N'"; else $strnot = '';
	if (!empty($_where)) {
	  if (empty($strnot)) $_where = "$_where"; else $_where = "and $_where";
	}
	if (!empty($_value)) {
	  $_fieldvalue = ", $_value";
	  $fk = $_value;
	}
	else {
	  $_fieldvalue = '';
	  $fk = $_field;
	}
  $_tmp = "<option value=''></option>";
	$_sql = "select $_field $_fieldvalue from $_table where $strnot $_where $str_order";
	$_res = _query($_sql);

  while ($w = _fetch_array($_res)) {
	  if (!empty($_value)) $_v = "value='" . $w[$_value]."'";
	  else $_v = '';
	  if ($_default == $w[$fk])
	    $_tmp = "$_tmp <option $_v selected>". $w[$_field]."</option>";
	  else
	    $_tmp = "$_tmp <option $_v>". $w[$_field]."</option>";    
  }
	return $_tmp;
}
function GetRadio($_sql, $_name, $_disp, $_key, $_default='', $_pisah='<br>') {
  $r = _query($_sql);
  $_ret = array();
  while ($w = _fetch_array($r)) {
    $ck = ($w[$_key] == $_default)? 'checked' : '';
    $_ret[] = "<input type=radio name='$_name' value='$w[$_key]' $ck> $w[$_disp]";
  }
  return implode($_pisah, $_ret);
}
function GetDateOption($dt, $nm='dt') {
  $arr = Explode('-', $dt);
  $_dy = GetNumberOption(1, 31, $arr[2]);
  $_mo = GetMonthOption($arr[1]);
  $_yr = GetNumberOption(1945, Date('Y')+1, $arr[0]);
  return "<select name='".$nm."_d'>$_dy</select>
    <select name='".$nm."_m'>$_mo</select>
    <select name='".$nm."_y'>$_yr</select>";
}
function GetTimeOption($dt, $nm='tm') {
  $arr = Explode(':', $dt);
  $_hr = GetNumberOption(1, 24, $arr[0]);
  $_mn = GetNumberOption(0, 59, $arr[1]);
  return "<select name='".$nm."_h'>$_hr</select>
    <select name='".$nm."_n'>$_mn</select>";
}
function GetaField($_tbl,$_key,$_value,$_result) {
  global $strCantQuery;
	$_sql = "select $_result from $_tbl where $_key='$_value' limit 1";
	$_res = _query($_sql);
	//echo $_sql;
	if (_num_rows($_res) == 0) return '';
	else {
	  $w = _fetch_array($_res);
	  return $w[$_result];
	}
}
function GetFields($_tbl, $_key, $_value, $_results) {
  global $strCantQuery;
	$s = "select $_results from $_tbl where $_key='$_value' limit 1";
	$r = _query($s);
	//echo "<pre>$s</pre>";
	if (_num_rows($r) == 0) return '';
	else {
	  /*$res = array();
	  for ($i=0; $i < mysql_num_fields($r); $i++) {
		$res[mysql_field_name($r, $i)] = mysql_result($r, 0, mysql_field_name($r, $i));
	  } */
	  return _fetch_array($r);
	}
}
function GetArrayTable($sql, $key, $label, $separator=', ', $diapit='') {
  // Digunakan untuk menerjemahkan array dalam string
  $r = _query($sql);
  $ret = '';
  while ($w = _fetch_array($r)) {
    $ret .= $diapit.$w[$label] .$diapit. $separator;
  }
  return TRIM($ret, $separator);
}
function sqling($str) {
    $str = stripslashes($str);
	return addslashes($str);
}
function FixQuotes($str) {
    $str = stripslashes($str);
	$str = str_replace('"', '&quot;', $str);
	$str = str_replace("'", '&#39;', $str);
	return $str;
}
function TampilkanFile($file) {
  $f = fopen($file, 'r');
  $isi = fread($f, filesize($file));
  fclose($f);
  
  // Tampilkan
  echo "<table class=box cellspacing=1 cellpadding=4";
  $brs = explode("\r\n", $isi);
  for ($i=0; $i < sizeof($brs); $i++) {
    $det = explode('=', $brs[$i]);
    echo "<tr><td class=inp1><b>$det[0]</td><td class=inp2>:</td><td class=inp2>$det[1]</td></tr>";
  }
  echo "</table>";
}
function GetLastID() {
  $sql = "select LAST_INSERT_ID() as ID";
  $res = mysql_query($sql);
  return mysql_result($res, 0, 'ID');
}
function Konfirmasi1($isi) {
  return "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
  <tr><th class=ttl>$isi</th></tr>
  </table></p>";
}
function Konfirmasi($jdl, $isi) {
  Return "<p><center><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td><img src='img/tux001.jpg'></td>
  <td>$isi</td></tr></table></center></p>";
}
function ErrorMsg1($jdl, $isi) {
  Return "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
  <tr><th bgcolor=#AA0000 colspan=2><font color=yellow>$jdl</th></tr>
  <tr><td><img src='img/tux001.jpg'></td>
  <td>$isi</td></tr></table></p>";
}
function ErrorMsg($jdl='Error', $isi='') {
  Return "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
  <tr><td rowspan=2 width=10><img src='img/tux001.jpg'></td>
  <td bgcolor=#FF6600><font color=yellow><strong>$jdl</strong></font></td>
  <tr><td>$isi</td></tr>
  </table></p>";
}
function TampilkanJudul($str='') {
  //echo "<p><font face='Times New Roman' size=6 color=gray>$str</font></p>";
  echo "<div class=Judul>$str</div>";
}
function TampilkanSubMenu($mnux, $arr=array(), $pref='', $tok='') {
  $sb = '';
  for ($i=0; $i<sizeof($arr); $i++) {
    $r = Explode('->', $arr[$i]);
    $cl = ($r[1] == $tok) ? "class=menuaktif" : "class=menuitem";
    $sb .= "<td $cl><a href=\"?mnux=$mnux&$pref=$r[1]\">$r[0]</a></td>";
  }
  echo "<p><table class=menu cellspacing=1 cellpadding=4>$sb</table></p>";
}
function TampilkanSubMenu2($mnux, $arr=array(), $pref='', $tok='') {
  $sb = '';
  for ($i=0; $i<sizeof($arr); $i++) {
    $r = explode('->', $arr[$i]);
    //$cl = ($r[1] == $tok)? "class=menuaktif" : "class=menuitem";
    $sb .= "<li $cl><a href=\"?mnux=$mnux&$pref=$r[1]\">$r[0]</a></li>";
  }
  echo "<ul class=submenu>$sb</ul>";
}
function StripEmpty($var) {
  if (empty($var)) return '&nbsp;';
	else return $var;
}
function FormatTanggal($tgl='', $sprt='/') {
  global $arrBulan;
  $tgl = substr($tgl, 0, 10);
  $arr = explode('-', $tgl); 
  //$arr1 = intval($arr[1]);
  $nBulan = $arrbulan[$arr[1]];
  return (empty($arr))? '' : "$arr[2]$sprt$arr[1]$sprt$arr[0]";
}
function GetNextPMBID($prodi='') {
  $_PMBDigit = 3;
  $pmbaktif = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
  $pmbmx = GetaField('pmbweb', "PMBID like '$pmbaktif%' and NA", 'N', "right(max(PMBID),3)");
  //$pmbcnt = ltrim($pmbmx, $pmbaktif);
  $pmbcnt = intval($pmbmx)+1;
  $pmbcnt = $pmbaktif."-W".str_pad($pmbcnt, $_PMBDigit, '0', STR_PAD_LEFT);
  return $pmbcnt;
  //echo $pmbmx.' : '.$pmbaktif.' : '.$pmbcnt;
}
function GetNextPSSBID($tahun) {
  global $_PMBDigit;
  $mx = GetaField('pssb', "PSSBID like '$tahun%' and NA", 'N', "max(PSSBID)");
  $cnt = str_replace($tahun, '', $mx)+1;
  $cnt = $tahun.str_pad($cnt, $_PMBDigit, '0', STR_PAD_LEFT);
  return $cnt;
}
function GetNextNIM($ProdiID, $kdp='') {
  // Ambil Setup NIM
  $stp = GetaField('prodi', 'ProdiID', $ProdiID, 'FormatNIM');
  $tmp = $stp;
  $tmp = str_replace('~YY~', date('y'), $tmp);
  $tmp = str_replace('~YYYY~', date('Y'), $tmp);
  // untuk check
  $check = $tmp;
  $check = str_replace('~NMR3~', '___', $check);
  $check = str_replace('~NMR4~', '____', $check);
  $check = str_replace('~NMR5~', '_____', $check);
  // check dulu
  $s = "select max(MhswID) as LAST from mhsw where MhswID like '$check' ";
  $r = _query($s);
  $w = _fetch_array($r);
  
  if (empty($w['LAST'])) {
    $Last = $tmp;
    $Last = str_replace('~NMR3~', '001', $Last);
    $Last = str_replace('~NMR4~', '0001', $Last);
    $Last = str_replace('~NMR5~', '00001', $Last);
  }
  else {
    $_lst = $w['LAST'];
    $base = $tmp;
    $base = str_replace('~NMR3~', '', $base);
    $base = str_replace('~NMR4~', '', $base);
    $base = str_replace('~NMR5~', '', $base);
    $_lst = str_replace($base, '', $_lst) +1;
    // Format jumlah digit
    $Last = $tmp;
    $Last = str_replace('~NMR3~', str_pad($_lst, 3, '0', STR_PAD_LEFT), $Last);
    $Last = str_replace('~NMR4~', str_pad($_lst, 4, '0', STR_PAD_LEFT), $Last);
    $Last = str_replace('~NMR5~', str_pad($_lst, 5, '0', STR_PAD_LEFT), $Last);
  }
  return $Last;
}
function GetNextPMBFormulirID($pmbfid=1) {
  global $_PMBDigit;
  $pmbaktif = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
  $pmbaktif = substr($pmbaktif, 0, 4);
  $panjang = str_pad('_', $_PMBDigit, '_');
  $pmbmx = GetaField('pmbformjual', "PMBFormJualID like '".$pmbaktif.$panjang."' and NA",
    'N', "max(PMBFormJualID)");
  $pmbcnt = str_replace($pmbaktif, '', $pmbmx)+1;
  $pmbcnt = $pmbaktif.str_pad($pmbcnt, $_PMBDigit, '0', STR_PAD_LEFT);
  return $pmbcnt;
}
function GetNextBPM() {
  global $_BPMDigit;
  $_BPMDigit = (empty($_BPMDigit))? 5 : $_BPMDigit;
  $thn = date('Y').'2';
  $panjang = str_pad('_', $_BPMDigit, '_');
  $bpmmx = GetaField('bayarmhsw', "BayarMhswID like '".$thn.$panjang."' and NA", 
    'N', "max(BayarMhswID)");
  $bpmcnt = str_replace($thn, '', $bpmmx)+1;
  $bpmcnt = $thn.str_pad($bpmcnt, $_BPMDigit, '0', STR_PAD_LEFT);
  return $bpmcnt;
}
function DaftarProdi($mnux) {
  $a = "<table class=box cellspacing=1 cellpadding=4>
    <tr><td></td><th class=ttl>Kode</th><th class=ttl>Nama</th>
    <th class=ttl>NA</th><td></td></tr>";
  $s = "select ProdiID, Nama, NA from prodi order by ProdiID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    if ($_SESSION['prodiid'] == $w['ProdiID']) {
      $ki = "<img src='img/kanan.gif'>";
      $ka = "<img src='img/kiri.gif'>";
      $c = "class=inp1";
    }
    else {
      $ki = ''; $ka = ''; $c = 'class=ul';
    }
    $a .= "<tr><td align=center>$ki</td>
      <td $c>$w[ProdiID]</td>
      <td $c><a href='?$mnux&prodiid=$w[ProdiID]'>$w[Nama]</a></td>
      <td $c>$w[NA]</td>
      <td align=center>$ka</td></tr>";
  }
  return $a . "</table>";
}
function GetRadioProdi($prodis='', $nama='ProdiID') {
  $s = "select * from prodi order by ProdiID";
  $r = _query($s);
  $rd = '';
  $nama .= "[]";
  while ($w = _fetch_array($r)) {
    $pos = strpos($prodis, $w['ProdiID']);
    $ck = ($pos === false)? '' : "checked";
    $rd .= "<input type=checkbox name='$nama' value='$w[ProdiID]' $ck>$w[ProdiID] - $w[Nama]<br>";
  }
  return $rd;
}
function CheckFormScript($_str='') {
  $arr = explode(',', $_str);
  echo "<SCRIPT LANGUAGE=\"JavaScript1.2\">
  <!--
  function CheckForm(form) {
    strs = \"\"; 
    ";
  for ($i=0; $i<sizeof($arr); $i++) {
    $nm = trim($arr[$i]);
    echo "
    if (form.$nm.value == \"\") {
      strs += \"$nm tidak boleh kosong\\n\"; }\n";
  }
  echo "if (strs != \"\") alert(strs);
  return strs == \"\";
  }
  -->
  </SCRIPT>";
}
function TampilkanPeriodePMB($mnux='') {
  global $arrID;
  $opt = GetOption2("pmbformulir", "concat(Nama, ' (', JumlahPilihan, ' pilihan) : Rp. ', format(Harga, 0))",
    'PMBFormulirID', $_SESSION['pmbfid'], "KodeID='$_SESSION[KodeID]'", 'PMBFormulirID');
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=ul colspan=2><strong>$arrID[Nama]</strong></td></tr>
  <tr><td class=ul>Periode/Gelombang</td><td class=ul>: <input type=text name='pmbaktif' value='$_SESSION[pmbaktif]' size=10 maxlength=20> <input type=submit name='Gelombang' value='Gelombang'></td></tr>
  <tr><td class=ul>Jenis Formulir</td><td class=ul>: <select name='pmbfid' onChange='this.form.submit()'>$opt</select></tr></td>
  </form>
  </table><br>";
}
function TampilkanPeriodePMBLaporan($mnux) {
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=inp1>Periode PMB</td>
    <td class=ul><input type=text name='_pmbaktif' value='$_SESSION[_pmbaktif]' size=10 maxlength=10>
    <input type=submit name='Go' value='Go!'></td></tr>
  </form></table></p>";
}
function TampilkanFileDWOPRN($nmf, $mnux='') {
  global $_lf, $_HeaderPrn, $_EjectPrn;
  // Tampilkan
  echo "<p><a href='dwoprn.php?f=$nmf'>Cetak Laporan</a> $kembali
    | <a href='?mnux=$mnux&gos='>Kembali</a> |
    <a href='' onClick='window.close()'>Tutup</a></p>";
  $f = fopen($nmf, "r");
  $isi = fread($f, filesize($nmf));
  fclose($f);
  $isi = str_replace($_lf, "<br>", $isi);
  $isi = str_replace($_HeaderPrn, '', $isi);
  $isi = str_replace($_EjectPrn, '', $isi);
  echo "<pre>";
  echo $isi;
  echo "</pre>";
}
function TampilkanPilihanProdi($mnux='', $gos='', $pref='', $token='') {
  global $arrID;
  if (empty($_SESSION['_ProdiID'])) $_prodi = '-1';
  else {
    $_ProdiID = trim($_SESSION['_ProdiID'], ',');
    //echo $_ProdiID;
    $arrProdi = explode('.', $_ProdiID);
    $_prodi = (empty($arrProdi))? '-1' : $_ProdiID; //implode(', ', $arrProdi);
  }
  $opt = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], "KodeID='$arrID[Kode]' and ProdiID in ($_prodi)", 'ProdiID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <input type=hidden name='$pref' value='$token'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul>
  <select name='prodi' onChange='this.form.submit()'>$opt</select></td></tr>
  </form></table></p>";
}
function TampilkanPilihanProdiProgram($mnux='', $gos='', $pref='', $token='') {
  global $arrID;
// Tampilkan hanya prodi yang berhak
  if (empty($_SESSION['_ProdiID'])) $_prodi = '-1';
  else {
    $_ProdiID = trim($_SESSION['_ProdiID'], ',');
    //echo $_ProdiID;
    $arrProdi = explode(',', $_ProdiID);
    $_prodi = (empty($arrProdi))? '-1' : $_ProdiID; //implode(', ', $arrProdi);
  }
  $opt = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], "KodeID='$arrID[Kode]' and ProdiID in ($_prodi)", 'ProdiID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], "KodeID='$arrID[Kode]'", 'ProgramID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <input type=hidden name='$pref' value='$token'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul>
  <select name='prodi' onChange='this.form.submit()'>$opt</select></td></tr>
  <tr><td class=inp1>Program</td><td class=ul>
  <select name='prid' onChange='this.form.submit()'>$optprg</select></td></tr>
  </form></table></p>";
}
function TampilkanTahunProdiProgram($mnux='', $gos='', $pref='', $token='', $JarakNPM=0) {
  global $arrID;
  if (empty($_SESSION['_ProdiID'])) $_prodi = '-1';
  else {
    $_ProdiID = trim($_SESSION['_ProdiID'], ',');
    //echo $_ProdiID;
    $arrProdi = explode(',', $_ProdiID);
    $_prodi = (empty($arrProdi))? '-1' : $_ProdiID; //implode(', ', $arrProdi);
  }
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], "KodeID='$arrID[Kode]' and ProdiID in ($_prodi)", 'ProdiID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], "KodeID='$arrID[Kode]'", 'ProgramID');
  if ($JarakNPM == 1) {
    $_npm = "<tr><td class=inp1>Dari NPM</td>
      <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50>
      s/d <input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50>
	  </td></tr>";
  }
  else $_npm = '';

  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <input type=hidden name='$pref' value='$token'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Tahun Akademik</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
    <input type=submit name='Tentukan' value='Tentukan'></td></tr>
  $_npm
  <tr><td class=inp1>Program</td><td class=ul>
    <select name='prid' onChange='this.form.submit()'>$optprg</select></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul>
    <select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  </form></table>";
}
function TampilkanPilihanFakultas($mnux='', $gos='', $pref='', $token='') {
  global $arrID;
  $optfak = GetOption2('fakultas', "concat(FakultasID, ' - ', Nama)", "FakultasID", $_SESSION['fakid'], "KodeID='$arrID[Kode]'", 'FakultasID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <input type=hidden name='$pref' value='$token'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp>Fakultas</td><td class=ul><select name='fakid' onChange='this.form.submit()'>$optfak</select></td></tr>
  <tr><td class=inp>Tahun Akd.</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
    <input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  </form></table></p>";
}

function EditUserProfile($tbl, $lgn, $mnux, $gos, $gosval) {
  $w = GetFields($tbl, "Login", $lgn, '*');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$gos' value='$gosval'>
  <input type=hidden name='TabelUser' value='$tbl'>
  <input type=hidden name='LoginUser' value='$lgn'>
  
  <tr><th class=ttl colspan=2>Edit Profile</th></tr>
  <tr><td class=inp1>Nama</td>
    <td class=ul><input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Telepon</td>
    <td class=ul><input type=text name='Telephone' value='$w[Telephone]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Handphone</td>
    <td class=ul><input type=text name='Handphone' value='$w[Handphone]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>E-mail</td>
    <td class=ul><input type=text name='Email' value='$w[Email]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Alamat</td>
    <td class=ul><textarea name='Alamat' cols=40 rows=4>$w[Alamat]</textarea></td></tr>
  <tr><td class=inp1>Kota</td>
    <td class=ul><input type=text name='Kota' value='$w[Kota]' size=30 maxlength=50></td></tr>
  <tr><td class=inp1>Propinsi</td>
    <td class=ul><input type=text name='Propinsi' value='$w[Propinsi]' size=30 maxlength=50></td></tr>
  <tr><td class=inp1>Negara</td>
    <td class=ul><input type=text name='Negara' value='$w[Negara]' size=30 maxlength=30></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </table></p>";
}
function SaveUserProfile() {
  $TabelUser = $_REQUEST['TabelUser'];
  $LoginUser = $_REQUEST['LoginUser'];
  $Nama = sqling($_REQUEST['Nama']);
  $Telephone = sqling($_REQUEST['Telephone']);
  $Handphone = sqling($_REQUEST['Handphone']);
  $Email = sqling($_REQUEST['Email']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  // Simpan
  $s = "update $TabelUser set Nama='$Nama', Telephone='$Telephone', Handphone='$Handphone', Email='$Email',
    Alamat='$Alamat', Kota='$Kota', Propinsi='$Propinsi', Negara='$Negara'
    where Login='$LoginUser' ";
  $r = _query($s);
  echo Konfirmasi("Berhasil", "Profile berhasil diupdate");
}
function TampilkanPilihanInstitusi($mnux) {
  $opt = GetOption2('identitas', "concat(Kode, ' - ', Nama)", 'Kode', $_SESSION['KodeID'], '', 'Kode');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=inp1>Institusi :</td><td class=ul><select name='KodeID' onChange='this.form.submit()'>$opt</select></td></tr>
  </form></table></p>";
}
function TampilkanPilihanKurikulum() {
  global $mnux, $pref, $arrID;
  // Tampilkan hanya prodi yang berhak
  if (empty($_SESSION['_ProdiID'])) $_prodi = '-1';
  else {
    $_ProdiID = trim($_SESSION['_ProdiID'], ',');
    //echo $_ProdiID;
    $arrProdi = explode(',', $_ProdiID);
    $_prodi = (empty($arrProdi))? '-1' : $_ProdiID; //implode(', ', $arrProdi);
  }
  $optprodi = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], "KodeID='$arrID[Kode]' and ProdiID in ($_prodi)", 'ProdiID');
  $optkurid = GetOption2("kurikulum", "concat(KurikulumKode, ' - ', Nama)",
    "KurikulumKode", $_SESSION["kurid_$_SESSION[prodi]"], "ProdiID='$_SESSION[prodi]' and KodeID='$arrID[Kode]'", "KurikulumID", 1);
  // Tampilkan form
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprodi</select></td></tr>
  <tr><td class=inp1>Kurikulum</td><td class=ul><select name='kurid_$_SESSION[prodi]' onChange='this.form.submit()'>$optkurid</select></td></tr>
  </form></table></p>";
}
// Buat pilihan dalam bentu checkbox
function GetCheckboxes($table, $key, $Fields, $Label, $Nilai='', $Separator=',', $whr = '') {
  $_whr = (empty($whr))? '' : "and $whr";
  $s = "select $key, $Fields
    from $table
    where NA='N' $_whr order by $key";
  $r = _query($s);
  $_arrNilai = explode($Separator, $Nilai);
  $str = '';
  while ($w = _fetch_array($r)) {
    $_ck = (array_search($w[$key], $_arrNilai) === false)? '' : 'checked';
    $str .= "<input type=checkbox name='".$key."[]' value='$w[$key]' $_ck> $w[$Label]<br />";
  }
  return $str;
}
function TampilkanPMBSyarat($w, $sprt='<br />') {
  // Syarat2
  $s = "select *
    from pmbsyarat
    where NA='N' and KodeID='$_SESSION[KodeID]'
      and INSTR(StatusAwalID, '.$w[StatusAwalID].') >0
      and INSTR(ProdiID, '.$w[ProdiID].') >0
    order by PMBSyaratID";
  $r = _query($s);
  $w['Syarat'] = TRIM($w['Syarat'], '.');
  $_arrNilai = explode('.', $w['Syarat']);
  $_a = array();
  while ($x = _fetch_array($r)) {
    $ck = (array_search($x['PMBSyaratID'], $_arrNilai) === false)? '' : 'checked';
    $_a[] = "<input type=checkbox name='PMBSyaratID[]' value='$x[PMBSyaratID]' $ck> $x[PMBSyaratID] - $x[Nama]";
  }
  $a = implode($sprt, $_a);
  return $a;
}
function FileFotoMhsw($mhswid, $FotoMhsw='') {
  // Ambil gambar
  $FotoMhsw = (empty($FotoMhsw))? GetaField('mhsw', 'MhswID', $mhswid, 'Foto') : $FotoMhsw;
  $def = "img/tux001.jpg";
  if (!empty($FotoMhsw)) {
    $fn = $FotoMhsw;
    if (file_exists($fn)) $foto = $fn;
    else $foto = $def;
  }
  else $foto = $def;
  return $foto;
}
function PopupMsg($namafile) {
//echo $namafile;
echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  win2 = window.open("$namafile", "", "width=600, height=300, scrollbars, status");
  win2.creator = self;
  </SCRIPT>
EOF;
}
function TampilkanPencarianMhsw($mnux='mhswakd', $gos='CariMhsw', $btn=2) {
  global $arrID;
  $strbtn = "<input type=submit name='crmhsw' value='NPM'>";
  $strbtn .= ($btn >=2 )? "<input type=submit name='crmhsw' value='Nama'>" : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Prodi :</td><td class=ul>$_SESSION[_ProdiID]</td></tr>
  <tr><td class=inp1>Cari Mahasiswa :</td><td class=ul><input type=text name='crmhswid' value='$_SESSION[crmhswid]' size=20 maxlength=50>
    $strbtn</td></tr>
  </form></table></p>";
}
function TampilkanPencarianMhswTahun($mnux='mhswakd', $gos='CariMhsw', $btn=2) {
  global $arrID;
  $strbtn = "<input type=submit name='crmhsw' value='NPM'>";
  $strbtn .= ($btn >=2 )? "<input type=submit name='crmhsw' value='Nama'>" : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Prodi :</td><td class=ul>$_SESSION[_ProdiID]</td></tr>
  <tr><td class=inp1>Cari Mahasiswa :</td><td class=ul><input type=text name='crmhswid' value='$_SESSION[crmhswid]' size=20 maxlength=50></td></tr>
  <tr><td class=inp1>Tahun Akademik :</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=20 maxlength=10></td></tr>
  <tr><td colspan=2>$strbtn</td></tr>
  </form></table></p>";
}
function donothing() {
  return '';
}
function TampilkanPencarianCAMA($mnux='') {
  global $arrID;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  
  <tr><td colspan=2 class=ul><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Cari PMBID</td><td class=ul><input type=text name='srcpmbid' value='$_SESSION[srcpmbid]' size=20 maxlength=50>
    <input type=submit name='Cari' value='Cari No. PMB'></td></tr>
  </form></table></p>";
}
function HitungBatasStudi($TahunID, $prodi) {
  $DefJumlahTahun = 3;
  $prd = GetFields('prodi', 'ProdiID', $w['ProdiID'], "*");
  $_thn = substr($TahunID, 0, 4);
  $_ses = substr($TahunID, 4, 1);
  $_jmlthn = ($prd['JumlahSesi'] == 0)? $DefJumlahTahun : floor($prd['BatasStudi']/$prd['JumlahSesi']);
  $_sisa = $prd['BatasStudi'] % $prd['JumlahSesi'];
  $_BatasTahun = $_thn + $_jmlthn;
  $_BatasSemes = $_ses + $_sisa;
  $_BatasSemes = ($_BatasSemes > $prd['JumlahSesi'])? $_BatasSemes-$prd['JumlahSesi'] : $_BatasSemes;
  $BatasStudi = $_BatasTahun.$_BatasSemes;
  Return $BatasStudi;
}
function NamaTahun($tahun) {
  $arr = array('1'=>'Ganjil', '2'=>'Genap', '1p'=>'Pendek Ganjil', '2p'=>'Pendek Genap');
  $_tahun = substr($tahun, 0, 4)+0;
  $_tahun1 = $_tahun + 1;
  $_smt = substr($tahun, 4, 4);
  return $arr[$_smt] . " $_tahun/$_tahun1";
}
?>
