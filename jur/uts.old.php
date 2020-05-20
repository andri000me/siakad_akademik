<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 21 Agustus 2008

// *** Parameters ***
$_utsProdi = GetSetVar('_utsProdi');
$_utsProg  = GetSetVar('_utsProg');
$_utsTahun = GetSetVar('_utsTahun');

// *** Main ***
TampilkanJudul("Penjadwalan UTS");
TampilkanHeaderUTS();
RandomStringScript();
// validasi
if (!empty($_utsTahun) && !empty($_utsProdi)) {
  $gos = (empty($_REQUEST['gos']))? 'DftrUTS' : $_REQUEST['gos'];
  $gos();
}

// *** Functions ***
function TampilkanHeaderUTS() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['_utsProdi']);
  $optprog  = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_utsProg'], "KodeID='".KodeID."'", 'ProgramID');
  if (!empty($_SESSION['_utsTahun']) && !empty($_SESSION['_utsProdi'])) {
    JdwlEdtScript();
    $btn1 = " |
      <input type=button name='HapusSemua' value='Hapus Semua Jadwal UTS' 
        onClick=\"javascript:JdwlDelAll('$_SESSION[_utsTahun]', '$_SESSION[_utsProdi]','$_SESSION[_utsProg]')\" />
     "; 
  }
  echo <<<SCR
  <table class=box cellspacing=1 align=center width=860>
  <form name='frmJadwalHeader' action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  
  <tr>
      <td class=inp>Tahun Akd:</td>
      <td class=ul1><input type=text name='_utsTahun' value='$_SESSION[_utsTahun]' size=5 maxlength=5 /></td>
      <td class=inp>Program:</td>
      <td class=ul1><select name='_utsProg'>$optprog</select></td>
      <td class=inp>Program Studi:</td>
      <td class=ul1 colspan=3><select name='_utsProdi'>$optprodi</select></td>
      </tr>
  <tr><td bgcolor=silver height=1 colspan=10></td></tr>
  <tr><td class=ul1 colspan=10 nowrap>
      <input type=submit name='btnKirim' value='Kirim Parameter' />
      <input type=button name='btnReset' value='Reset Parameter'
        onClick="location='?mnux=$_SESSION[mnux]&_utsHari=&_utsKelas=&_utsMKKode=&_utsSemester='" />
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
function DftrUTS() {
  // Buat Header
  echo "<table class=box cellspacing=1 align=center width=860>";
  $hdr = "
  <tr><th class=ttl width=50>#</th>
      <th class=ttl>Matakuliah</th>
	  <th class=ttl width=80>Kelas</th>
      <th class=ttl width=20>Print</th>
	  <th class=ttl width=75>Ujian</th>
      <th class=ttl width=75>Jam</th>
	  <th class=ttl width=40 title='Pembagian Kursi'>Kursi</th>
      <th class=ttl width=20 title='Hapus Jadwal'>Del</th>
	  <th class=ttl width=30 title='Hapus Jadwal'>Edit</th>
      </tr>";

  $whr_prg = (empty($_SESSION['_utsProg']))? '' : "and j.ProgramID = '$_SESSION[_utsProg]'";
  
  $s = "select kl.Nama as NamaKelas, j.JadwalID, j.ProdiID, j.ProgramID, j.HariID,
      j.RuangID, j.MKKode, j.Nama, j.DosenID, j.SKS,
      concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      LEFT(j.JamMulai, 5) as _JM, LEFT(j.JamSelesai, 5) as _JS,
      h.Nama as HR, mk.Sesi, j.Final, 
      j.JumlahMhsw, j.Kapasitas, 
      j.BiayaKhusus, j.Biaya, format(j.Biaya, 0) as _Biaya
    from kelas kl, jadwal j
      left outer join hari h on j.HariID = h.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join mk mk on mk.MKID = j.MKID
	  left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID
	where j.KodeID = '".KodeID."'
      and j.TahunID = '$_SESSION[_utsTahun]'
      and j.ProdiID = '$_SESSION[_utsProdi]'
      $whr_prg $whr_hr $whr_smt $whr_kls $whr_kd
	  and kl.KelasID=j.NamaKelas
      and j.NA = 'N'
	  and jj.Tambahan = 'N'
    order by j.UTSTanggal, j.UTSJamMulai, j.UTSJamSelesai, j.Nama";
  $r = _query($s); $n = 0;
  $HariID = -320;
  $kanan = "<img src='img/kanan.gif' />";
  echo $hdr;
  while ($w = _fetch_array($r)) {
    $n++;
    if ($w['Final'] == 'Y') $c = "class=nac";
    else $c = "class=ul";
    
    // Ambil dosen2
    $dsn = AmbilDosen2($w['JadwalID']);
    
    // Tampilkan data
	$JumlahJadwalUTS = GetaField('jadwaluts', "JadwalID='$w[JadwalID]' and KodeID", KodeID, "count(JadwalUTSID)");
	$rowspan = "rowspan=". (($JumlahJadwalUTS == 0)? 1 : $JumlahJadwalUTS);
	
    echo "<tr>
      <td class=inpx align=center width=20 $rowspan>$n</font></br>
        <div align=center><sub title='ID Jadwal'>#$w[JadwalID]</div></sub>
        </td>
      <td $c $rowspan><b>$w[Nama]</b> <sup>$w[MKKode]</sup><br>
		<div align=left>
			(<b>$w[HR]</b>, $w[_JM]&#8594;$w[_JS])
			</div>
		<div align=right>
			<b>Dosen:</b><i> $w[DSN]
			$dsn</i></div>
        </td>
      <td $c align=center $rowspan>
        &nbsp;<br>
		<font color=darkred><b>$w[NamaKelas]</b></font>&nbsp;<br>
		&nbsp;</br>
		<div valign=bottom># Mhsw: <b>$w[JumlahMhsw]</b></div>
        </td>";
	
	if($JumlahJadwalUTS == 0)
	{ if($w['Final'] == 'Y')
		echo "
		<td $c colspan=6 align=center><b>Belum terjadwal.</b></td>";
		else
		echo "
	  <td $c colspan=6 align=center><b>Belum terjadwal.</b> <a href='#' onClick=\"javascript:JdwlEdt(1, $w[JadwalID])\">>> Tambah <<</a></td>";
	}
	else
	{ 
	  $s1 = "select  ju.JadwalUTSID,
	            date_format(ju.Tanggal, '%d-%m-%y') as _UTSTanggal,
			    huts.Nama as _UTSHari, ju.JumlahMhsw as _JumlahMhswUTS,
			    LEFT(ju.JamMulai, 5) as _UTSJamMulai, LEFT(ju.JamSelesai, 5) as _UTSJamSelesai
				from jadwaluts ju left outer join hari huts on huts.HariID = date_format(ju.Tanggal, '%w')
				where ju.JadwalID='$w[JadwalID]' and ju.KodeID='".KodeID."'";
	  $r1 = _query($s1);
	  $nn=0;
	  	  while($w1 = _fetch_array($r1))
	  {	  $nn++;
	  if ($w['Final'] == 'Y')
	      {  $edt = "<img src='img/lock.jpg' width=26 title='Sudah difinalisasi. Sudah tidak dapat diedit.' />";
			 $del = "&times;";
			 $editkursi = "<a href='#' onClick=\"alert('Penempatan kursi mahasiswa sudah tidak dapat dilakukan.')\"><img src='img/kursi.jpg'></a>";
          }
		  else 
		  {
		  if ($nn==1) {
		  $JadwalUTSID1=$w1[JadwalUTSID];
		  }
		  else {
		  $JadwalUTSID2=$w1[JadwalUTSID];
		  $JadwalID=$w[JadwalID];
		  $edt = "<a href='#' onClick=\"javascript:JdwlEdt(0, $JadwalID, $JadwalUTSID1, $JadwalUTSID2 )\" title='Edit jadwal'><img src='img/edit.jpg' width=20 /></a>";
		  }
		  $del = ($w1['JumlahMhsw'] > 0)? "<abbr title='Tidak dapat dihapus karena sudah ada Mhsw yang mendaftar'>&times;</abbr>" : "<a href='#' onClick=\"javascript:JdwlDel($w[JadwalID],$w1[JadwalUTSID])\" title='Hapus jadwal'><img src='img/del.gif' /></a>";
			 $editkursi = "<a href='#' onClick=\"EdtKursi('$w1[JadwalUTSID]')\"><img src='img/kursi.jpg'></a>";
		  }
		  
		  echo "
		  <td $c align=center>
			Print
			</td>
		  <td $c align=center>
			<sup>$w1[_UTSHari]</sup><br />
			$w1[_UTSTanggal]
			</td>
		  <td $c align=center>
			<sup>$w1[_UTSJamMulai]</sup>&#8594;<sub>$w1[_UTSJamSelesai]</sub>
			</td>
		  
		  <td $c align=center valign=center nowrap>
			$editkursi
			<div valign=bottom># Mhsw: <b>$w1[_JumlahMhswUTS]</b></div>
			</td>
		  <td $c align=center>
			$del
		  </td>";
		  	if ($nn>0) {
		 	 					echo "<td class=ul1 align=center >$edt</td></tr>";
		  					} 	 
	   }
	    
	  
	}
	
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

function JdwlDel() {
  $id = $_REQUEST['id'];
  $utsid = $_REQUEST['utsid'];
  $s = "update jadwal set JadwalUTSID = 0 where JadwalID = '$id' ";
  $r = _query($s);
  $s = "delete from jadwaluts where JadwalUTSID = '$utsid' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}

function JdwlDelAll() {
  $thn = sqling($_REQUEST['thn']);
  $prd = sqling($_REQUEST['prd']);
  $prg = sqling($_REQUEST['prg']);
  $whr_prg = (empty($prg))? '' : "and ProgramID = '$prg' ";
  // Hapus
  $s = "update jadwal set JadwalUTSID = 0 where TahunID = '$thn' and ProdiID = '$prd' $whr_prg";
  $r = _query($s);
  $s = "select JadwalID from jadwal where TahunID = '$thn' and ProdiID = '$prd' $whr_prg";
  $r = _query($s);
  while ($w = _fetch_array($r)){
  	  $s2 = "delete from jadwaluts where JadwalID = $w[JadwalID] ";
	  $r2 = _query($s2);
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}

function JdwlEdtScript() {
  echo <<<SCR
  <script>
  function JdwlEdt(md, id, jutsid, jutsid2) {
    var _rnd = randomString();
    lnk = "$_SESSION[mnux].edit.php?md="+md+"&id="+id+"&jutsid="+jutsid+"&jutsid2="+jutsid2+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function JdwlDel(id,utsid) {
    if (confirm("Anda yakin akan menghapus jadwal ini?")) {
      var _rnd = randomString();
      window.location = "?mnux=$_SESSION[mnux]&BypassMenu=1&gos=JdwlDel&id="+id+"&utsid="+utsid+"&_rnd="+_rnd;
    }
  }
  function JdwlDelAll(thn, prd, prg) {
    var psn = (prg == "")? "Anda juga akan menghapus semua jadwal dari semua program pendidikan." : "";
    if (confirm("Anda yakin akan menghapus semua jadwal ini? "+psn)) {
      var _rnd = randomString();
      window.location = "?mnux=$_SESSION[mnux]&BypassMenu=1&gos=JdwlDelAll&thn=" + thn + "&prd=" + prd + "&prg=" + prg+"&_rnd="+_rnd;
    }
  }
  function EdtKursi(id) {
    var _rnd = randomString();
	lnk = "$_SESSION[mnux].pilihkursi.php?id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=1000, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>

</BODY>
</HEAD>
