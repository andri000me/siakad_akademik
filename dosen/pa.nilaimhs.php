<?php
// *** Parameters ***
if (!empty($_SESSION['_Session'])) {
	if ($_SESSION['_LevelID']==100) {
	$MhswID = $_REQUEST['mhswid'];
	}
	else {
	$MhswID = GetSetVar('MhswID');
	}
}
else {
echo "<script>window.location='welcome';</script>";
}
if (empty($MhswID)) echo "<script>window.location='?mnux=dosen/pa';</script>";
$mhsw = GetFields("mhsw m
      left outer join dosen d on m.PenasehatAkademik = d.Login and d.KodeID='".KodeID."'
      left outer join prodi prd on prd.ProdiID = m.ProdiID and prd.KodeID='".KodeID."'
      left outer join program prg on prg.ProgramID = m.ProgramID and prg.KodeID='".KodeID."'
      ",
      "m.KodeID='".KodeID."' and m.MhswID", $MhswID,
      "m.*, prd.Nama as _PRD, prg.Nama as _PRG,
      d.Nama as DSN, d.Gelar");

// *** Main ***
if ($_SESSION['_LevelID']==100) {
TampilkanJudul("Daftar Nilai dan KRS Mahasiswa");
}

TampilkanHeaderMhsw($MhswID, $mhsw);
$gos = (empty($_REQUEST['gos']))? "EditNilaiMhsw" : $_REQUEST['gos'];
if (!empty($mhsw)) $gos($MhswID, $mhsw);
$thn = GetFields("tahun","NA='N' AND ProdiID='$mhsw[ProdiID]' AND KodeID", KodeID, "*");
$khs = GetFields("khs","NA='N' AND MhswID='$mhsw[MhswID]' AND TahunID", $thn['TahunID'], "*");
	if (!empty($khs['KHSID'])){
		
		TampilkanDaftarKRSMhsw($thn, $mhsw, $khs);
	}else{
    echo "tidak ditemukan transaksi akademik mahasiswa prodi $mhsw[ProdiID] tahun ".$khs['TahunID'];
  }

// *** Functions ***
function TampilkanHeaderMhsw($MhswID, $w) {
  
  echo "
  <table class=box cellspacing=1 width=800>
  <tr><td colspan=4><input type='button' onclick=\"Javascript:history.go(-1)\" value='&laquo; Kembali ke Daftar Mahasiswa'></td></tr>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <tr><td class=inp width=100>NPM:</td>
      <td class=ul width=210>";
	  if ($_SESSION['_LevelID']==100) {
        echo "<strong>$MhswID</strong>";
		}
		else {
		echo "<input type=text name='MhswID' value='$_SESSION[MhswID]' size=12 maxlength=20 />";
		echo "<input type=submit name='Ambil' value='Get Data' />";
		}
		echo "</td>
      <td class=inp width=100>Mahasiswa:</td>
      <td class=ul><b>$w[Nama]</b>&nbsp;</td>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul>$w[_PRD] <sup>$w[ProdiID]</sup>&nbsp;</td>
      <td class=inp>Prg. Pendidikan:</td>
      <td class=ul>$w[_PRG] <sup>$w[ProgramID]</sup>&nbsp;</td>
      </tr>
  <tr><td class=inp>Penasehat Akd:</td>
      <td class=ul>$w[DSN] <sup>$w[Gelar]</sup>&nbsp;</td>
      <td class=inp>Masa Studi:</td>
      <td class=ul>$w[TahunID] &#8594; $w[BatasStudi]</td>
      </tr>
  </form>
  </table>";

}
function EditNilaiMhsw($MhswID, $mhsw) {
	echo "<center><h1>Nilai Semester</h1></center>";
  $s = "select k.*,
      @KOR := (select count(kn.KoreksiNilaiID)
      from koreksinilai kn
      where kn.KRSID = k.KRSID),
      if (@KOR = 0, '&nbsp;', concat(@KOR, '&times;')) as JML
    from krs k
    where k.KodeID = '".KodeID."'
      and k.MhswID = '$MhswID'
	  and k.Final = 'Y'
	  and k.NA='N'
    order by k.TahunID, k.MKKode";
  $r = _query($s);
  $n = 0; $_thn = 'laskdfj'; $sks = 0;
  $hdr = "<tr>
    <th class=ttl width=30>#</th>
    <th class=ttl width=80>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl width=20>SKS</th>
    <th class=ttl width=20>Nilai</th>";
	if (!$_SESSION['_LevelID']==100) {
    echo "<th class=ttl width=30>Koreksi</th>";
	}
    echo "</tr>";
  echo "<table class=box cellspacing=1 width=800 align=center>";
  while ($w = _fetch_array($r)) {
    if ($_thn != $w['TahunID']) {
      $_thn = $w['TahunID'];
	  $IPS = HitungIPS($MhswID,$w['TahunID']);
      echo "<tr>
        <td class=ul1 colspan=10>Thn Akd: <font size=+1>$w[TahunID]</font> / IP Semester: <b>$IPS</b></td>
        </tr>";
      echo $hdr;
      $n = 0;
    }
    $n++;
    // Detail
      $c = 'class=ul';
      $sks += $w['SKS'];
    if ($w['BobotNilai'] == 0) {
      $Nilai = '&times;';
    }
    else {
      $Nilai = "$w[GradeNilai] <sup>$w[BobotNilai]</sup>";
    }
    echo "<tr><td class=inp>$n</td>
        <td $c>$w[MKKode]</td>
        <td $c>$w[Nama]</td>
        <td $c align=right>$w[SKS]</td>
        <td $c align=center>$Nilai</td>";
        echo "</tr>";
  }
  echo "<tr>
    <td class=ul colspan=3 align=right>Total SKS:</td>
    <td class=ul align=right><font size=+1>$sks</font></td>
    </tr>
  </table>";
  
}

function TampilkanDaftarKRSMhsw($thn, $mhsw, $khs) {
	//echo "Y $thn[TahunID] $mhsw[MhswID] $khs[KHSID]";
    $statusKHS = ($khs['SetujuPA']=='Y')? "Sudah Disetujui":"Belum Disetujui";
    $NamaMhsw = str_replace("'","",ucwords($mhsw['Nama']));
    $TombolValidasi = "<button class='btn btn-large btn-primary' 
    		onclick=\"javascript:modalPopup('dosen/ajx/pa.verify','Validasi KRS: $khs[MhswID]','$khs[KHSID]','$khs[MhswID]','','')\">Validasi</button> ";
    echo "<center><h2>KRS ($statusKHS)</h2>".$TombolValidasi."</center>";
  $s = "SELECT k.*, j.JadwalID,
    j.MKID, j.Nama AS MKNama, j.HariID, j.NamaKelas,
    LEFT(j.JamMulai, 5) AS JM, LEFT(j.JamSelesai, 5) AS JS,
    j.RuangID, mk.Sesi, j.AdaResponsi,
    CONCAT(d.Gelar1,' ',d.Nama, ' <sup>', d.Gelar, '</sup>') AS DSN, j.JenisJadwalID, jj.Nama AS _NamaJenisJadwal, jj.Tambahan, kl.Nama AS NamaKelas
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
	WHERE k.KHSID = '$khs[KHSID]'
      AND k.NA = 'N'
    ORDER BY j.HariID, j.RuangID, j.JamMulai, j.JamSelesai";
  $r = _query($s);
  //die("<pre>$s</pre>");
 //echo $s;
  
  // Apakah sudah melebihi batas waktu ambil/ubah KRS?
 // hanya superuser yang memiliki hak akses tak terbatas ..... by Arisal Yanuarafi

		// selain superuser tergantung jadwal
		  $skrg = date('Y-m-d');	  
			$boleh = false;
			$ambil = '';
			$paket = '';
			$hapus = '';

  // Tampilkan
  echo "<table class=box cellspacing=1 align=center width=800>";
  echo "<tr>
    <script>
    function KeHari(frm) {
      window.location = '?mnux=$_SESSION[mnux]&_krsHariID='+frm[frm.selectedIndex].value;
    }
    </script>
    <td class=ul1 colspan=10>
      $ambil
      $paket
      $hapus
      <img src='img/kanan.gif' /> <b>Daftar Matakuliah Yang Diambil Mahasiswa Pada $thn[Nama]:</b>
    </td></tr>";
  $hdr = "<tr>
    <th class=ttl width=30>#</th>
    <th class=ttl width=80>Jam Kuliah</th>
    <th class=ttl width=50>Ruang</th>
	<th class=ttl width=80>Kode <sup>Smt</sup></th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl width=20>SKS</th>
    <th class=ttl width=200>Dosen</th>
    <th class=ttl width=40>Kelas</th>
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
	$TotalSKS += $w['SKS']; 
	echo "<tr>
      <td class=inp>$n</td>
      <td><sup>".$w['JM']."</sup>&#8594;<sub>$w[JS]</sub></td>
      <td align=center>$w[RuangID]&nbsp;</td>
	  <td>$w[MKKode]<sup>$w[Sesi]</sup></td>
      <td>$w[Nama] $TagTambahan $FieldResponsi</td>
      <td align=right>$w[SKS]</td>
      <td>$w[DSN]</td>
      <td align=center>$w[NamaKelas]&nbsp;</td>
      </tr>";
  }
  echo "<tr><td align=center colspan=8> TOTAL ".$TotalSKS." SKS / BEBAN MAKS. ".$khs[MaxSKS]." SKS</td></tr>";
  echo "</table></p>";
}

?>
