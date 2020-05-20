<?php

// *** Parameters ***
$_jdwlProdi = GetSetVar('_jdwlProdi');
$_jdwlProg  = GetSetVar('_jdwlProg');
$_jdwlTahun = GetSetVar('_jdwlTahun');
$_jdwlHari  = GetSetVar('_jdwlHari');
$_jdwlKelas = GetSetVar('_jdwlKelas');
$_jdwlSemester = GetSetVar('_jdwlSemester');
$_jdwlMKKode = GetSetVar('_jdwlMKKode');

// *** Main ***
TampilkanJudul("Hasil Evaluasi Dosen");
TampilkanHeaderJadwal();
RandomStringScript();
// validasi
if (!empty($_jdwlTahun) && !empty($_jdwlProdi)) {
  $gos = (empty($_REQUEST['gos']))? 'DftrKuliah' : $_REQUEST['gos'];
  $gos();
}

// *** Functions ***
function TampilkanHeaderJadwal() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['_jdwlProdi']);
  $optprog  = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_jdwlProg'], "KodeID='".KodeID."'", 'ProgramID');
  $opthr = GetOption2('hari', 'Nama', 'HariID', $_SESSION['_jdwlHari'], '', 'HariID');
  //filter kelas
  if (!empty($_SESSION['_jdwlProdi'])) $flt=" And j.ProdiID='$_SESSION[_jdwlProdi]' AND j.ProgramID='$_SESSION[_jdwlProg]' ";
    $s = "Select DISTINCT(k.KelasID),k.Nama from kelas k, jadwal j where k.KelasID=j.NamaKelas $flt order by k.Nama";
  $r = _query($s);
  $optkelas = "<option value=''></option>";
  while ($w = _fetch_array($r)) {
  if ($_SESSION['_jdwlKelas']==$w['KelasID']) {
  $optkelas .= "<option value='$w[KelasID]' Selected>$w[Nama]</option>";
  }
  else $optkelas .= "<option value='$w[KelasID]'>$w[Nama]</option>";
  }
  
  // ===================================
  $s = "select DISTINCT(TahunID) from tahun where KodeID='".KodeID."' order by TahunID DESC";
	  $r = _query($s);
	  $opttahun = "<option value=''></option>";
	  while($w = _fetch_array($r))
		{  $ck = ($w['TahunID'] == $_SESSION['_jdwlTahun'])? "selected" : '';
		   $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
		}
  if (!empty($_SESSION['_jdwlTahun']) && !empty($_SESSION['_jdwlProdi'])) {
    JdwlEdtScript();
    $btn1 = "<input type=button name='CetakHasilProdi' value='Cetak Rekap' 
      onClick=\"javascript:cetakHasilProdi('$_SESSION[_jdwlTahun]', '$_SESSION[_jdwlProdi]','$_SESSION[_jdwlProg]')\" />";
      //<input type=button name='HapusSemua' value='Hapus Semua' 
      //  onClick=\"javascript:JdwlDelAll('$_SESSION[_jdwlTahun]', '$_SESSION[_jdwlProdi]','$_SESSION[_jdwlProg]')\" />
     
	 // <input type=button name='btnJadwalUjian' value='Jadwal UAS'
     //   onClick=\"location='?mnux=$_SESSION[mnux]ujian&_jdwlProdi=$_SESSION[_jdwlProdi]&_jdwlProg=$_SESSION[_jdwlProg]&_jdwlTahun=$_SESSION[_jdwlTahun]&_jdwlUjian=2'\" />
     
 $URL = $_SERVER['REQUEST_URI'];
    $btn2 = "
      
	  </br>
    ";
  }
  $optThn = "<option value=''></option>";
  
  echo <<<SCR
  <table class=box cellspacing=1 align=center width=960>
  <form name='frmJadwalHeader' action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  
  <tr><td class=wrn width=2 rowspan=5></td>
      <td class=inp>Tahun Akd:</td>
      <td class=ul1><select name='_jdwlTahun'>$opttahun</select></td>
      <td class=inp>Prg. Pendidikan:</td>
      <td class=ul1><select name='_jdwlProg'>$optprog</select></td>
      <td class=inp>Program Studi:</td>
      <td class=ul1 colspan=3><select name='_jdwlProdi'>$optprodi</select></td>
      </tr>
  <tr><td class=inp>Hari:</td>
      <td class=ul1><select name='_jdwlHari'>$opthr</select></td>
      <td class=inp>Kelas:</td>
      <td class=ul1><select name='_jdwlKelas'>$optkelas</select></td>
      <td class=inp>Filter MK:</td>
      <td class=ul1><input type=text name='_jdwlMKKode' value='$_SESSION[_jdwlMKKode]' size=10 maxlength=50 /></td>
      <td class=inp>Semester MK:</td>
      <td class=ul1>
        <input type=text name='_jdwlSemester' value='$_SESSION[_jdwlSemester]' size=3 maxlength=10 />
        </td>
      </tr>
  <tr><td bgcolor=silver height=1 colspan=10></td></tr>
  <tr><td class=ul1 colspan=10 nowrap>
      <input type=submit name='btnKirim' value='Kirim Parameter' />
      <input type=button name='btnReset' value='Reset Parameter'
        onClick="location='?mnux=$_SESSION[mnux]&_jdwlHari=&_jdwlKelas=&_jdwlMKKode=&_jdwlSemester='" />
      $btn1</td></tr>
  <tr>
      <td class=ul1 colspan=10 nowrap>
      $btn2
      </td>
      </tr>
  </form>
  </table>
SCR;
}
function DftrKuliah() {
  // Buat Header
  echo "<table class=box cellspacing=1 align=center width=960>";
  $hdr = "<tr><th class=ttl width=50 colspan=2>#</th>
      <th class=ttl width=60>Ruang</th>
      <th class=ttl width=75>Jam</th>
      <th class=ttl width=80>Kode <sup>Smt</sup></th>
      <th class=ttl>Matakuliah</th>
      <th class=ttl width=50>Kelas</th>
      <th class=ttl width=20>SKS</th>
      <th class=ttl width=200>Dosen</th>
      <th class=ttl width=40>Cetak</th>
      </tr>";

  $whr_prg = (empty($_SESSION['_jdwlProg']))? '' : "and j.ProgramID = '$_SESSION[_jdwlProg]'";
  $whr_hr  = ($_SESSION['_jdwlHari'] == '')? '' : "and j.HariID = '$_SESSION[_jdwlHari]'";
  $whr_smt = (empty($_SESSION['_jdwlSemester']))? '' : "and mk.Sesi = '$_SESSION[_jdwlSemester]' ";
  $whr_kls = ($_SESSION['_jdwlKelas'] == '')? '' : "and j.NamaKelas like '$_SESSION[_jdwlKelas]%' ";
  $whr_kd  = ($_SESSION['_jdwlMKKode'] == '')? '' : "and j.MKKode like '$_SESSION[_jdwlMKKode]%' ";
  
  $s = "select j.JadwalID, j.JadwalRefID, j.ProdiID, j.ProgramID, j.HariID, j.AdaResponsi,
      j.RuangID, j.MKKode, j.Nama, j.NamaKelas, j.DosenID, j.SKS, j.JenisJadwalID, 
      concat(d.Gelar1, ' ',d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      LEFT(j.JamMulai, 5) as _JM, LEFT(j.JamSelesai, 5) as _JS,
      h.Nama as HR, mk.Sesi, j.Final,
      j.JumlahMhsw, j.Kapasitas,
      j.BiayaKhusus, j.Biaya, format(j.Biaya, 0) as _Biaya,
	  k.Nama as _NamaKelas
    from jadwal j
      left outer join hari h on j.HariID = h.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join mk mk on mk.MKID = j.MKID
	  left outer join kelas k on k.KelasID = j.NamaKelas
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$_SESSION[_jdwlTahun]'
      and j.ProdiID = '$_SESSION[_jdwlProdi]'
      $whr_prg $whr_hr $whr_smt $whr_kls $whr_kd
      and j.NA = 'N'
    order by j.HariID, j.RuangID, j.JamMulai, j.JamSelesai";
  $r = _query($s); $n = 0;
  $HariID = -320;
  $kanan = "<img src='img/kanan.gif' />";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($HariID != $w['HariID']) {
      $HariID = $w['HariID'];
      echo "<tr>
        <td class=ul1 colspan=15><font size=+1>$w[HR]</font> <sup><a name='Hari_$HariID'>$HariID</a></sup></td>
        </tr>";
      echo $hdr;
    }
    
    // Ambil dosen2
    $dsn = AmbilDosen2($w['JadwalID']);
    
    // Tampilkan data
    //&#8904;
    $HRG = ($w['BiayaKhusus'] == 'Y')? "<div align=right><sup>Biaya: Rp. <b>$w[_Biaya]</b></sup></div>" : '';
	$print = "<a href='#' onClick=\"javascript:CetakHasil($w[JadwalID])\">Cetak Hasil</a>";
  //$print = "--";
	echo "<tr>
      <td class=inp width=20>$n</td>
      <td class=ul width=26 align=center>
        $edt
        <br />
        <sub title='ID Jadwal'>#$w[JadwalID]</sub>
        </td>
      <td $c>
        $w[RuangID]
        <div align=right><sub align=right>$w[ProgramID]</sub></div>
        </td>
      <td $c align=center>
        <sup>$w[_JM]</sup>&#8594;<sub>$w[_JS]</sub>
        </td>
      <td $c>$w[MKKode]<sup>$w[Sesi]</sup>
		</td>
      <td $c>
        $w[Nama]
        </td>
      <td $c align=center>
        <sub>$w[_NamaKelas]</sub>&nbsp;<br />
        $w[JumlahMhsw]<sup title='Kapasitas Kelas'>&#8594;$w[Kapasitas]</sup><br />
        </td>
      <td $c align=right>$w[SKS]</td>
      <td $c>
        $w[DSN]
        $dsn
        <div align=right>
        $dosen
        </div>
        </td>
      <td $c align=left valign=bottom nowrap>
        $print
        </td>
      </tr>";
  }
  echo "</table></p>";
}

function AmbilDosen2($id) {
  $s = "select d.Nama, d.Gelar, jd.JenisDosenID
    from jadwaldosen jd
      left outer join dosen d on d.Login = jd.DosenID and d.KodeID = '".KodeID."'
    where jd.JadwalID = '$id'
    order by d.Nama";
  $r = _query($s);
  //die("<pre>$s</pre>");
  $a = array();;
  while ($w = _fetch_array($r)) {
    $a[] = "&rsaquo; $w[Nama] <sup>($w[Gelar])</sup>";
  }
  $a = (!empty($a))? "<br />".implode("<br />", $a) : '';
  return $a;
}
function AmbilResponsi($id) {
   $s = "select jr.JadwalID, jr.JadwalRefID, h.Nama as _NamaHari, LEFT(jr.JamMulai, 5) as _JM, LEFT(jr.JamSelesai, 5) as _JS, 
			jr.RuangID, r.Nama as _NamaRuang, jr.JenisJadwalID, jj.Nama as _NamaJenisJadwal
    from jadwal jr
      left outer join ruang r on jr.RuangID = r.RuangID and r.KodeID = '".KodeID."'
	  left outer join hari h on h.HariID = jr.HariID
	  left outer join jenisjadwal jj on jj.JenisJadwalID=jr.JenisJadwalID
	where jr.JadwalRefID = '$id'
    order by jj.JenisJadwalID, jr.HariID, jr.JamMulai, jr.JamSelesai";
  $r = _query($s);
  //die("<pre>$s</pre>");
  $a = array();;
  $n = 0; $jj = 'K';
  while ($w = _fetch_array($r)) {
    if($jj != $w['JenisJadwalID'])
	{	$n = 0;
		$jj = $w['JenisJadwalID'];
	}
	$n++;
	$a[] = "&rsaquo; <b>$w[_NamaJenisJadwal] #$n</b> $w[_NamaHari], $w[_JM] - $w[_JS], $w[_NamaRuang]($w[RuangID]) <a href='#' onClick=\"JdwlLabEdt(0, '$w[JadwalRefID]', '$w[JadwalID]')\"><img src='img/edit.png' /></a>";
  }
  $a = (!empty($a))? "<br />".implode("<br />", $a) : '';
  return $a;
}

function JdwlDel() {
  $id = $_REQUEST['id'];
  $s = "delete from jadwal where JadwalID = '$id' ";
  $r = _query($s);
  $s = "delete from jadwal where JadwalRefID = '$id' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}

function JdwlDelAll() {
  $thn = sqling($_REQUEST['thn']);
  $prd = sqling($_REQUEST['prd']);
  $prg = sqling($_REQUEST['prg']);
  $whr_prg = (empty($prg))? '' : "and ProgramID = '$prg' ";
  // Hapus Jadwal2 UTS terlebih dahulu
  $s = "select JadwalID from jadwal where TahunID='$thn' and ProdiID='$prd' $whr_prg";
  $r = _query($s);
  while($w = _fetch_array($r))
  {	$s1 = "delete from jadwaluts where JadwalID='$w[JadwalID]' and KodeID='".KodeID."'";
	$r1 = _query($s1);
	$s1 = "delete from jadwaluas where JadwalID='$w[JadwalID]' and KodeID='".KodeID."'";
	$r1 = _query($s1);
  }
  
  $s = "delete from jadwal where TahunID = '$thn' and ProdiID = '$prd' $whr_prg";
  $r = _query($s);
  
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}

function JdwlEdtScript() {
  echo <<<SCR
  <script>
  function JdwlEdt(md, id) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].edit.php?md="+md+"&id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function JdwlLabEdt(md, id, resid) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].editlab.php?md="+md+"&id="+id+"&resid="+resid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function JdwlDsnEdt(id) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].dosen.php?id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function JdwlDel(id) {
    if (confirm("Anda yakin akan menghapus jadwal ini?")) {
      var _rnd = randomString();
      window.location = "?mnux=$_SESSION[mnux]&BypassMenu=1&gos=JdwlDel&id="+id+"&_rnd="+_rnd;
    }
  }
  function JdwlDelAll(thn, prd, prg) {
    var psn = (prg == "")? "Anda juga akan menghapus semua jadwal dari semua program pendidikan." : "";
    if (confirm("Anda yakin akan menghapus semua jadwal ini? "+psn)) {
      var _rnd = randomString();
      window.location = "?mnux=$_SESSION[mnux]&BypassMenu=1&gos=JdwlDelAll&thn=" + thn + "&prd=" + prd + "&prg=" + prg+"&_rnd="+_rnd;
    }
  }
  function CetakJadwal() {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].pdf.php?TahunID=$_SESSION[_jdwlTahun]&ProdiID=$_SESSION[_jdwlProdi]&ProgramID=$_SESSION[_jdwlProg]&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    
  }
  function CetakJadwalCek() {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].pdf.cek.php?TahunID=$_SESSION[_jdwlTahun]&ProdiID=$_SESSION[_jdwlProdi]&ProgramID=$_SESSION[_jdwlProg]&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    
  }
  function CetakJadwalXLS() {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].xls1.php?TahunID=$_SESSION[_jdwlTahun]&ProdiID=$_SESSION[_jdwlProdi]&ProgramID=$_SESSION[_jdwlProg]&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    
  }
  function CetakFormulirKRS() {
    if (frmJadwalHeader._jdwlProg.value == '') {
      alert("Tentukan dahulu Program Pendidikan yang akan dicetak formulir KRS-nya.");
    }
    else {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].formkrs.php?TahunID=$_SESSION[_jdwlTahun]&ProdiID=$_SESSION[_jdwlProdi]&ProgramID=$_SESSION[_jdwlProg]&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    }
  }
  function CetakJadwalDosen() {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].dosen.pdf.php?TahunID=$_SESSION[_jdwlTahun]&ProdiID=$_SESSION[_jdwlProdi]&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }
  function CetakJadwalRuang() {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].ruang.pdf.php?TahunID=$_SESSION[_jdwlTahun]&HariID=$_SESSION[_jdwlHari]&ProdiID=$_SESSION[_jdwlProdi]+_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }
  function CetakHasil(id) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?id="+id+"&pid=$_SESSION[_jdwlProdi]&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function cetakHasilProdi(thn,prd,prg) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.rekap.php?thn="+thn+"&prd="+prd+"&prg="+prg+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakKursiUTS(id) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].kursiuts.php?id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakKursiLab(id) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].kursilab.php?id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakKursiUAS(id) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].kursiuas.php?id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function PindahKelas(JadwalID) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].pindah.php?JadwalID="+JadwalID+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>

</BODY>
</HEAD>
