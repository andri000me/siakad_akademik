<?php
// Costumized by 	: Arisal Yanuarafi 
// Mulai			: 13 Desember 2011

echo $_SESSION['mnux'];

include_once "$_SESSION[mnux].lib.php";
KrsLaluScript();
// *** Parameters ***

$_krsTahunID = GetSetVar('_krsTahunID');
$_krsMhswID = GetSetVar('_krsMhswID');
$_krsHariID = GetSetVar('_krsHariID');
$_semesteR = GetSetVar('_semesteR');
$_goss = GetSetVar('_goss');

// *** Main ***
// Arisal Yanuarafi :
TampilkanJudul("Master KRS");
CekBolehAksesModul();
TampilkanCariKRS();
if (!empty($_goss) && !empty($_semesteR)) {
	if (is_numeric($_semesteR)) {
		$ada = GetaField('khs', "KodeID='".KodeID."' and Sesi='$_semesteR' and MhswID",
    	$_krsMhswID, 'KHSID')+0;
 		 if ($ada > 0) {
   		 echo ErrorMsg("Error",
      "Mahasiswa <b>$_krsMhswID</b> sudah terdaftar utk Semester <b>$_semesteR</b>.<br />
      Silakan mengecek data mahasiswa, mungkin ada kesalahan.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&_goss='\" />");
 		 }
  		else {
  		$mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $_krsMhswID,
      			"Nama, ProgramID, ProdiID, BIPOTID, StatusMhswID");
	  	$MaxSKS = GetaField('prodi', "KodeID='".KodeID."' and ProdiID",
        $mhsw['ProdiID'], 'DefSKS');
		
  		$s = "insert into khs
      (TahunID, KodeID, ProgramID, ProdiID, 
      MhswID, StatusMhswID,
      Sesi, IP, MaxSKS,
      LoginBuat, TanggalBuat, NA)
      values
      ('$_krsTahunID', '".KodeID."', '$mhsw[ProgramID]', '$mhsw[ProdiID]',
      '$_krsMhswID', '$mhsw[StatusMhswID]',
      '$_semesteR', 0, $MaxSKS, 
      '$_SESSION[_Login]', now(), 'N')";
   		 $r = _query($s);
			$_goss='';
		$_semesteR='';
		BerhasilSimpan("?mnux=$_SESSION[mnux]&_goss=", 100);

		}
	}
	else {
	 echo ErrorMsg("Error",
      "Oops....Semester harus diisi dengan angka.<br />
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&_goss='\" />");
	}
}
else {
$_goss='';
$_semesteR='';

if (!empty($_krsTahunID) && !empty($_krsMhswID)) {
  $oke = BolehAksesData($_krsMhswID);
  if ($oke) $oke = ValidasiDataMhsw($_krsTahunID, $_krsMhswID, $khs);
  if ($oke) {
    $mhsw = GetFields("mhsw m
      left outer join statusawal sta on sta.StatusAwalID = m.StatusAwalID", 
      "m.KodeID = '".KodeID."' and m.MhswID", $_krsMhswID, 
      "m.*, sta.Nama as STAWAL");
    $thn = GetFields("tahun",
      "KodeID = '".KodeID."' and ProdiID = '$khs[ProdiID]' and ProgramID = '$khs[ProgramID]' and TahunID", $_krsTahunID, "*");
    $gos = sqling($_REQUEST['gos']);
    if (empty($gos)) {
      if ($khs['StatusMhswID'] == 'A') {
        TampilkanHeaderMhsw($thn, $mhsw, $khs);
        TampilkanDaftarKRSMhsw($thn, $mhsw, $khs);
      }
      else {
        $status = GetaField('statusmhsw', 'StatusMhswID', $khs['StatusMhswID'], 'Nama');
        echo ErrorMsg('Error',
          "Mahasiswa <b>$mhsw[Nama]</b> <sup>$mhsw[MhswID]</sup> tidak dapat mengambil KRS.<br />
          Berikut adalah alasannya:
          <hr size=1 color=silver />
          
          Status mahasiswa: <font size=+1>$status</font>.<br />
          Mahasiswa dengan status ini tidak dapat mengambil KRS.<br />
          Hanya mahasiswa Aktif saja yg boleh mengambil KRS.<br />
          Hubungi BAA untuk informasi status mahasiswa.");
      }
    }
    else $gos();
  }
}
}
// *** Functions ***
// Ilham
function TampilkanCariKRS() {
  if($_SESSION['_LevelID'] == 120) {	  
	  $_inputTahun = "<b>$_SESSION[_krsTahunID]</b>";
	  $_inputNIM = "<b>$_SESSION[_krsMhswID]</b>";
  } else {
	  $s = "select DISTINCT(TahunID) from tahun where KodeID='".KodeID."' order by TahunID DESC";
	  $r = _query($s);
	  $opttahun = "<option value=''></option>";
	  while($w = _fetch_array($r))
		{  $ck = ($w['TahunID'] == $_SESSION['_krsTahunID'])? "selected" : '';
		   $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
		}
	  
	  $_inputTahun = "<select name='_krsTahunID' onChange='this.form.submit()'>$opttahun</select>";
	  $_inputNIM = "<input type=text name='_krsMhswID' value='$_SESSION[_krsMhswID]' size=20 maxlength=50 onFocus='select()'/>";
	  $_inputCari = "<input type=submit name='Cari' value='Cari' />";
  }
  
  echo "<table class=box cellspacing=1 align=center width=800>
  <form action='?' method=POST>
  <input type=hidden name='_krsHariID' value='' />
  <tr><td class=wrn width=2></td>
      <td class=inp width=80>Tahun Akd:</td>
      <td class=ul1 width=200>$_inputTahun</td>
      <td class=inp width=80>NIM:</td>
      <td class=ul1>$_inputNIM</td>
      <td class=ul1 width=180>
        $_inputCari
        </td>
      </tr>
  </form>
  </table>";
}
function CekBolehAksesModul() {
  $arrAkses = array(1, 20,24, 40, 41, 120, 51);
  $key = array_search($_SESSION['_LevelID'], $arrAkses);
  if ($key === false)
    die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut."));
}
function BolehAksesData($nim) {
  if ($_SESSION['_LevelID'] == 120 && $_SESSION['_Login'] != $nim) {
    echo ErrorMsg('Error',
      "Anda tidak boleh melihat data KRS mahasiswa lain.<br />
      Anda hanya boleh mengakses data dari NIM: <b>$_SESSION[_Login]</b>.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut");
    return false;
  } else return true;
}
function ValidasiDataMhsw($thn, $nim, &$khs) {
  $khs = GetFields("khs k
    left outer join statusmhsw s on s.StatusMhswID = k.StatusMhswID", 
    "k.KodeID = '".KodeID."' and k.TahunID = '$thn' and k.MhswID",
    $nim, 
    "k.*, s.Nama as STA");
  if (empty($khs)) {
    if($_SESSION['_LevelID'] == 120)
	{ echo ErrorMsg("Error",
      "Anda tidak terdaftar di Tahun Akd <b>$thn</b>.<br />
      Hubungi Kepala Akademik untuk informasi lebih lanjut.");
	}
	else
	{ 
    /*
	$daftarkanMhs = "<input type=submit value='Daftarkan Mahasiswa' />";
	  echo ErrorMsg("Error",
      "Mahasiswa <b>$nim</b> tidak terdaftar di Tahun Akd <b>$thn</b>.<br />
      <hr size=1 color=silver />
       <form action='?' method='POST'><b>Daftarkan Semester:</b> <input type=number name='_semesteR' size='2' maxlength='2' /> 
	  <input type='hidden' name='_goss' value='1' />
	  <input type='hidden' name='_krsTahunID' value='$thn' />
	  <input type='hidden' name='_krsMhswID' value='$nim' />
	  $daftarkanMhs  <input type=button name='LihatSemester' value='Lihat Sejarah Semester Mhsw'
        onClick=\"javascript:InquirySemesterMhsw('$nim')\" /></form>
	  
	  ");
    */
    echo ErrorMsg("Error",
      "Mahasiswa <b>$nim</b> tidak terdaftar di Tahun Akd <b>$thn</b>. Sesuai kebijakan, proses mendaftarkan mahasiswa pada periode akademik tertentu hanya dapat dilakukan oleh bagian registrasi.<br />
      <hr size=1 color=silver />
       <input type=button name='LihatSemester' value='Lihat Sejarah Semester Mhsw'
        onClick=\"javascript:InquirySemesterMhsw('$nim')\" />
    
    ");
	}
    return false;
  }
  else {
    return true;
  }
}

function TampilkanHeaderMhsw($thn, $mhsw, $khs) {
  $KRSMulai = FormatTanggal($thn['TglKRSMulai']);
  $KRSSelesai = FormatTanggal($thn['TglKRSSelesai']);
  $BayarMulai = FormatTanggal($thn['TglBayarMulai']);
  $BayarSelesai = FormatTanggal($thn['TglBayarSelesai']);
  $pa = GetFields('dosen', "KodeID='".KodeID."' and Login", $mhsw['PenasehatAkademik'], 'Nama, Gelar');
  // batas waktu
  $skrg = date('Y-m-d');
  //if ($thn['TglKRSMulai'] <= $skrg && $skrg <= $thn['TglKRSSelesai']) {

      $CetakKRS = "";
      $CetakLRS = "";

	KRSScript();
  //}
  //else {
  //  $CetakKRS = '&nbsp;';
  //  $CetakLRS = '&nbsp;';
  // }
  
  echo "<table class=box cellspacing=1 align=center width=800>
  <tr><td class=wrn width=2 rowspan=4></td>
      <td class=inp width=80>Mahasiswa:</td>
      <td class=ul width=200>$mhsw[Nama] <sup>($mhsw[MhswID])</sup></td>
      <td class=inp width=80>Sesi:</td>
      <td class=ul>$khs[Sesi]</td>
      <td class=inp width=80>Status:</td>
      <td class=ul width=100>$khs[STA] <sup>($khs[StatusMhswID])</sup></td>
      </tr>
  <tr><td class=inp title='Dosen Pembimbing Akademik'>Pemb. Akd:</td>
      <td class=ul>$pa[Nama] <sup>$pa[Gelar]</sup>&nbsp;</td>
      <td class=inp>Jml SKS:</td>
      <td class=ul>$khs[SKS]<sub title='Maksimum SKS yg boleh diambil'>&minus;$khs[MaxSKS]</sub></td>
      <td class=inp>Status Awal:</td>
      <td class=ul>$mhsw[STAWAL] <sup>($mhsw[StatusAwalID])</sup></td>
      </tr>
  </table>";
  HitungUlangKRS($khs[MhswID],$khs[TahunID]);
}

function TampilkanDaftarKRSMhsw($thn, $mhsw, $khs) {
  // Edit: Ilham
  // kl.Nama AS NamaKelas line: 194
  // line: 205 - 206
  $whr_hari = ($_SESSION['_krsHariID'] == '')? '' : "and j.HariID='$_SESSION[_krsHariID]'";
  $s = "SELECT k.*, j.JadwalID,
    j.MKID, j.Nama AS MKNama, j.HariID, j.NamaKelas,
    LEFT(j.JamMulai, 5) AS JM, LEFT(j.JamSelesai, 5) AS JS,
    j.RuangID, mk.Sesi, j.AdaResponsi,
    CONCAT(d.Nama, ' <sup>', d.Gelar, '</sup>') AS DSN, j.JenisJadwalID, jj.Nama AS _NamaJenisJadwal, jj.Tambahan, kl.Nama AS NamaKelas
    FROM krs k
         LEFT OUTER JOIN jadwal j 
         ON j.JadwalID = k.JadwalID 
            LEFT OUTER JOIN dosen d
            ON d.Login = j.DosenID and d.KodeID = '".KodeID."'
                LEFT OUTER JOIN mk 
                ON mk.MKID = k.MKID 
                    LEFT OUTER JOIN jenisjadwal jj 
                    ON jj.JenisJadwalID = j.JenisJadwalID
                        LEFT OUTER JOIN kelas kl
                        ON kl.KelasID = j.NamaKelas       
	WHERE k.TahunID = '$khs[TahunID]'
		AND k.MhswID = '$khs[MhswID]'
      	AND k.NA = 'N'
      	$whr_hari
    ORDER BY j.HariID, j.RuangID, j.JamMulai, j.JamSelesai";
  $r = _query($s);
  //die("<pre>$s</pre>");
  
  // Apakah sudah melebihi batas waktu ambil/ubah KRS?
  $skrg = date('Y-m-d');

    KRSScript();
    $ambil = "<input type=button name='TambahMK' value='Ambil MataKuliah' onClick=\"javascript:AmbilKRS('$mhsw[MhswID]', '$khs[TahunID]')\" />";
    $boleh = true;

  // Tampilkan
  $opthari = GetOption2('hari', 'Nama', 'HariID', $_SESSION['_krsHariID'], '', 'HariID');
  echo "<table class=box cellspacing=1 align=center width=800>";
  echo "<tr>
    <script>
    function KeHari(frm) {
      window.location = '?mnux=$_SESSION[mnux]&_krsHariID='+frm[frm.selectedIndex].value;
    }
    </script>
    <td class=ul1 colspan=10>
       $ambil
      <img src='img/kanan.gif' /> <b>Daftar Matakuliah Yang Diambil Mahasiswa:</b>
    </td></tr>";
  $hdr = "<tr>
    <th class=ttl width=30>#</th>
	<th class=ttl width=80>Kode <sup>Smt</sup></th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl width=40>SKS</th>
	<th class=ttl width=40>Nilai</th>
	<th class=ttl width=40>Bobot</th>
    <th class=ttl width=20 title='Hapus KRS'>Del</th>
    </tr>";
  $n = 0;
  $hr = -3;

  while ($w = _fetch_array($r)) {
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      $_hr = GetaField('hari', 'HariID', $hr, 'Nama');
      echo "<tr><td class=ul1 colspan=10><b>$_hr</b> <sup>$hr</sup></td></tr>";
      echo $hdr;
    }
    $n++;
    $del = ($boleh)? "<a href='#' onClick=\"javascript:HapusKRS($w[MhswID],'$w[TahunID]',$w[KRSID])\" title='Hapus KRS' /><img src='img/del.gif' /></a>" : '&times;';
    
	// Bila ditandai bukan kuliah biasa, diarsir....
	if($w['Tambahan'] == 'Y')
	{	$class='cnaY';
		$TagTambahan = "<b>( $w[_NamaJenisJadwal] ) </b>";
		$FieldResponsi = '';
	}
	else
	{	$class='ul1';
		$TagTambahan = '';
		$FieldResponsi = '<br>';
		if($w['AdaResponsi'] == 'Y')
		{	$FieldResponsi .= AmbilResponsi($w['JadwalID'], $w['KRSID'], $w['MhswID'], $thn['TahunID']);
		}	
	}
		
	echo "<tr>
      <td class=inp>$n</td>
	  <td class=$class>$w[MKKode]<sup>$w[Sesi]</sup></td>
      <td class=$class>$w[Nama] $TagTambahan $FieldResponsi</td>
      <td class=$class align=center>$w[SKS]</td>
	  <td class=$class align=center>$w[GradeNilai]</td>
	  <td class=$class align=center>$w[BobotNilai]</td>
      <td class=$class align=center>$del</td>
	        </tr>";
  }
  echo "</table></p>";
}
function HapusKRS() {
  $krsid = sqling($_REQUEST['krsid'])+0;
  $tahunid = sqling($_REQUEST['tahunid']);
  $mhswid = sqling($_REQUEST['mhswid'])+0;
  $jdwlid = GetaField('krs', 'KRSID', $krsid, 'JadwalID');
  // Penghapusan
  $s = "delete from krs where KRSID = $krsid ";
  $r = _query($s);
  // Hapus data presensi
  $s1 = "delete from presensimhsw where KRSID = $krsid";
  $r1 = _query($s1);
  // update data
  HitungUlangKRS($mhswid,$tahunid);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}
function HapusSemua() {
  $khsid = $_REQUEST['khsid']+0;
  // Ambil data KRS siswa
  $s = "select JadwalID, KRSID
    from krs
    where KHSID = '$khsid' ";
  $r = _query($s);
  // Hapus 1-per-1 & update data
  while ($w = _fetch_array($r)) {
    $ss = "delete from krs where KRSID = $w[KRSID] ";
    $rr = _query($ss);
    // Hapus data presensi
    $s1 = "delete from presensimhsw where KRSID = $w[KRSID]";
    $r1 = _query($s1);
    HitungPeserta($w['JadwalID']);
  }
  HitungUlangKRS($khsid);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}
function HapusSemua_xxx() {
  $khsid = $_REQUEST['khsid']+0;
  $s = "delete from krs where KHSID = '$khsid' ";
  $r = _query($s);
  // update data
  $jdwlid = GetaField('krs', 'KRSID', $krsid, 'JadwalID');
  HitungPeserta($jdwlid);
  HitungUlangKRS($khsid);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}

function KRSScript() {
  RandomStringScript();
  echo <<<SCR
  
  <script>
  <!--
  function AmbilKRS(mhswid, tahunid) {
    lnk = "$_SESSION[mnux].ambil.php?mhswid="+mhswid+"&tahunid="+tahunid;
    win2 = window.open(lnk, "", "width=720, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
  }

  function HapusKRS(mhswid,tahunid,krsid) {
    if (confirm("Anda yakin akan menghapus matakuliah ini dari KRS Anda?")) {
      window.location = "?mnux=$_SESSION[mnux]&gos=HapusKRS&tahunid="+tahunid+"&krsid="+krsid+"&mhswid="+mhswid;
    }
  }

  function KRSLabEdt(md, jid, krsid, krsresid, jenis) {
    lnk = "$_SESSION[mnux].resedit.php?md="+md+"&jid="+jid+"&krsid="+krsid+"&krsresid="+krsresid+"&jenis="+jenis;
	win2 = window.open(lnk, "", "width=600, height=300, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
  }
  -->
  </script>
SCR;
}

function AmbilResponsi($id, $krsid, $mhswid, $tahunid) {
   $arrEkstra = array();
   $a = array();
   // Cek apakah ada jadwal tambahan yang harus diambil. Bila ada 1 saja yang dijadwalkan berarti harus diambil
   $s = "select DISTINCT(jr.JenisJadwalID) as _JenisJadwalID from jadwal jr
			where jr.JadwalRefID='$id' and jr.TahunID='$tahunid' and jr.KodeID='".KodeID."'";
   $r = _query($s);
   while($w = _fetch_array($r)) $arrEkstra[] = $w['_JenisJadwalID'];

	if(!empty($arrEkstra))
	{	foreach($arrEkstra as $ekstra)
		{	$s = "select k.KRSID, jr.JadwalID, jr.JadwalRefID, h.Nama as _NamaHari, LEFT(jr.JamMulai, 5) as _JM, LEFT(jr.JamSelesai, 5) as _JS, 
					jr.RuangID, r.Nama as _NamaRuang, jr.JenisJadwalID, jj.Nama as _NamaJenisJadwal 
				from krs k left outer join jadwal jr on k.JadwalID=jr.JadwalID 
					left outer join ruang r on jr.RuangID = r.RuangID and r.KodeID = '".KodeID."'
					left outer join hari h on h.HariID = jr.HariID
					left outer join jenisjadwal jj on jj.JenisJadwalID=jr.JenisJadwalID
				where jr.JenisJadwalID='$ekstra' 
					and k.MhswID='$mhswid' 
					and k.TahunID='$tahunid'
					and k.KodeID='".KodeID."'
				order by jj.JenisJadwalID, jr.HariID, jr.JamMulai, jr.JamSelesai";
			$r = _query($s);
			$n = _num_rows($r);
			if($n == 0)
			{	$NamaJenisJadwal = GetaField('jenisjadwal', "JenisJadwalID", $ekstra, "Nama");
				$a[] = "&rsaquo; <font color=red>$NamaJenisJadwal ( belum terjadwal ) </font><a href='#' onClick=\"KRSLabEdt(1, '$id', '$krsid', '$w[KRSID]', '$ekstra')\"><font size=0.8m>Tambah</font></a>";
			}
			else if($n == 1)
			{	$w = _fetch_array($r);
				$a[] = "&rsaquo; <b>$w[_NamaJenisJadwal] &rsaquo;&rsaquo;</b> $w[_NamaHari], $w[_JM] - $w[_JS], $w[_NamaRuang]($w[RuangID]) <a href='#' onClick=\"KRSLabEdt(0, '$id', '$krsid', '$w[KRSID]', '$w[JenisJadwalID]')\"><font size=0.8m>Edit</font></a>";
			}
			else
			{   $a[] = "Seharusnya ga ke sini";
			}
		}
	}
	$a = (!empty($a))? "<br />".implode("<br />", $a) : '';
   return $a;
   /*$s = "select jr.JadwalID, jr.JadwalRefID, h.Nama as _NamaHari, LEFT(jr.JamMulai, 5) as _JM, LEFT(jr.JamSelesai, 5) as _JS, 
			jr.RuangID, r.Nama as _NamaRuang, jr.JenisJadwalID, jj.Nama as _NamaJenisJadwal
    from krs k 
	  left outer join jadwal jr on k.JadwalID = jr.JadwalID and jr.JadwalRefID = '$id'
             left outer join ruang r on jr.RuangID = r.RuangID and r.KodeID = '".KodeID."'
	  left outer join hari h on h.HariID = jr.HariID
	  left outer join jenisjadwal jj on jj.JenisJadwalID=jr.JenisJadwalID
	where k.KodeID='".KodeID."' and k.MhswID='$mhswid' and k.TahunID='$tahunid' and jj.Tambahan='Y'
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
  return $a;*/
}
function KrsLaluScript() {
  echo <<<SCR
  <script>
  function InquirySemesterMhsw(mhswid) {
    lnk = "inq/mhsw_semester.php?mhswid=" + mhswid;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>
