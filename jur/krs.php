<?php
//$bp = substr($_SESSION['_Login'],0,2);
//if ($bp!="14") die(ErrorMsg("Tolak", "Mahasiswa BP 2013 belum bisa mengisi KRS. Tunggu beberapa saat lagi."));
include_once "$_SESSION[mnux].lib.php";

// *** Parameters ***

//if($_SESSION['_LevelID'] == 120)
//{   $mhsw = GetFields('mhsw', "MhswID='$_SESSION[_Login]' and KodeID", KodeID, "ProgramID, ProdiID");
  //  $TahunAktif = GetaField('tahun', "ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and NA='N' and KodeID", KodeID, "TahunID");
  //$_SESSION['_krsTahunID'] = $TahunAktif;
  //$_SESSION['_krsMhswID'] = $_SESSION['_Login'];
//}
$_krsTahunID = GetSetVar('_krsTahunID');
$_krsMhswID = GetSetVar('_krsMhswID');
$_tolak = GetSetVar('tolak');
if ($_SESSION['_LevelID']==120) {
  $_SESSION['_krsMhswID'] = $_SESSION['_Login'];
  $_krsMhswID = $_SESSION['_Login'];
}
$_krsHariID = GetSetVar('_krsHariID');


// *** Main ***
TampilkanJudul("KRS Mahasiswa");
CekBolehAksesModul();
TampilkanCariKRS();
// Menghapus data KRS bila melebihi Maximum SKS
    $khs = GetFields("khs", "TahunID='".$_krsTahunID."' AND MhswID", $_krsMhswID,"*");
$mhsw = GetFields('mhsw', "MhswID",$_krsMhswID, "*");
$strTahunID = '.'.$mhsw['TahunID'].'.';
$TahunID = GetaField('filterinputkrs', "LOCATE('$strTahunID',TahunID) and KodeID",KodeID, "filterid");
    
    /*$SKSKrs = GetaField('krs','KHSID',$khs['KHSID'], "sum(SKS)")+0;
      if ($SKSKrs > $khs['MaxSKS']) {
        while ($SKSKrs > $khs['MaxSKS']) {
          $detele = "DELETE from krs where KHSID='".$khs['KHSID']."' and KHSID!=0 and KHSID is not Null limit 1";
          $delete = _query($detele);
          $SKSKrs = GetaField('krs','KHSID',$khs['KHSID'], "sum(SKS)");
          $update = _query("UPDATE khs set SKS='".$SKSKrs."' where KHSID='".$khs['KHSID']."'");
        }
      }*/
      
if (!empty($_krsTahunID) && !empty($_krsMhswID)) {
  $oke = BolehAksesData($_krsMhswID);
  if ($oke) $oke = ValidasiDataMhsw($_krsTahunID, $_krsMhswID, $khs);
  if ($oke) {
    $khs = GetFields("khs", "TahunID='".$_krsTahunID."' AND MhswID", $_krsMhswID,"*");
    $mhsw = GetFields("mhsw m
      left outer join statusawal sta on sta.StatusAwalID = m.StatusAwalID", 
      "m.KodeID = '".KodeID."' and m.MhswID", $_krsMhswID, 
      "m.*, sta.Nama as STAWAL");
    $thn = GetFields("tahun",
      "KodeID = '".KodeID."' and ProdiID = '$khs[ProdiID]' and ProgramID = '$khs[ProgramID]' and TahunID", $_krsTahunID, "*");
    $gos = sqling($_REQUEST['gos']);
    if (empty($gos)) {
      if ($khs['StatusMhswID'] == 'A') {
        if (!empty($TahunID)) {
      echo "<center>Bukan periode input KRS untuk mahasiswa tahun masuk $mhsw[TahunID]</center>";
      //echo "$TahunID";
      }
      else {
      //echo "$TahunID";
        TampilkanHeaderMhsw($thn, $mhsw, $khs);
        TampilkanDaftarKRSMhsw($thn, $mhsw, $khs);
      }
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
          Untuk mengaktifkan mahasiswa silakan lakukan langkah berikut:
          <ol>
          <li>Klik menu Administrasi - Administrasi Cuti Aktif</li>
          <li>Klik tombol 'Isi Formulir Perubahan'</li>
          <li>Isi Nomor BP pada kolom NIM, lalu klik tombol Cari NIM</li>
          <li>Isikan Status Baru Mahasiswa yang diinginkan dan semua data harus diisi (boleh diisi '-')</li>
          <li>Klik Simpan</li>
          </ol>");
      }
    }
    else $gos();
  }
}

// *** Functions ***
// Ilham
function TampilkanCariKRS() {
  //if($_SESSION['_LevelID'] == 120) {    
  //  $_inputTahun = "<b>$_SESSION[_krsTahunID]</b>";
  //  $_inputNIM = "<b>$_SESSION[_krsMhswID]</b>";
  //} else {

  //Hanya menampilkan no. BP yang bersangkutan jika Akses Mahasiswa (edited by:Arisal)
  
  if ($_SESSION['_LevelID'] == 120) {
    // Lakukan pengecekan semester awal untuk menghindari kesalahan pada script selanjutnya
    //$cekSemesterAwal = GetaField('mhsw',"MhswID", $_SESSION['_Login'],"SemesterAwal");
    // BIla belum ada semesterawal update mhsw
    //if (empty($cekSemesterAwal)) $u = _query("UPDATE mhsw set SemesterAwal=concat(TahunID,'1') where MhswID = '$_SESSION[_Login]' limit 1");
    $TahunID = GetaField('dosenevaluasi', "NA='N' AND KodeID", KodeID, 'TahunID');
    $BelumEvaluasi = GetaField("krs", "TahunID='".$TahunID."' AND NA='N' AND EvaluasiDosen='N' AND MhswID", $_SESSION['_Login'],"count(KRSID)");
    if ($BelumEvaluasi) { die(errorMsg("Evaluasi Dosen", "Anda belum mengisi $BelumEvaluasi blanko evaluasi kinerja dosen. Untuk bisa mengakses menu ini Anda diwajibkan untuk melengkapi seluruh isian tersebut. <hr>Silakan isi <a href='evaluasidosen'>Blanko Evaluasi Kinerja Dosen</a> terlebih dahulu."));}
      $mhs = GetFields("mhsw", "MhswID", $_SESSION['_Login'],"*");
      $TahunMasuk = $mhs['SemesterAwal'];
    $s = "select DISTINCT(TahunID) from tahun where TahunID not like 'Tran%' AND TahunID >= $TahunMasuk AND ProdiID='".$mhs['ProdiID']."' AND ProgramID='$mhs[ProgramID]' AND KodeID='".KodeID."' order by TahunID DESC";
    $r = _query($s);
    $opttahun = "<option value=''></option>";
    while($w = _fetch_array($r))
    {  $ck = ($w['TahunID'] == $_SESSION['_krsTahunID'])? "selected" : '';
       $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
    }
    
    $_inputTahun = "<select name='_krsTahunID' onChange='this.form.submit()'>$opttahun</select>";
        $_inputNIM = "<input type=text name='_krsMhswID' value='$_SESSION[_Login]' size=20 maxlength=50 onFocus='select()'/>";
    }
    else
    {
    $s = "select DISTINCT(TahunID) from tahun where KodeID='".KodeID."' order by TahunID DESC";
    $r = _query($s);
    $opttahun = "<option value=''></option>";
    while($w = _fetch_array($r))
    {  $ck = ($w['TahunID'] == $_SESSION['_krsTahunID'])? "selected" : '';
       $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
    }
    
    $_inputTahun = "<select name='_krsTahunID' onChange='this.form.submit()' class='nones'>$opttahun</select>";
        $_inputNIM = "<input type=text name='_krsMhswID' value='$_SESSION[_krsMhswID]' size=20 maxlength=50 onFocus='select()'/>";
     }
    $_inputCari = "<input type=submit name='Cari' value='Cari' />";
 // }
  
  echo "<table class=box cellspacing=1 align=center width=800>
  <form action='?' method=POST>
  <input type=hidden name='_krsHariID' value='' />
  <tr><td class=wrn width=2></td>
      <td class=inp width=80>Tahun Akd:</td>
      <td class=ul1 width=200>$_inputTahun</td>
      <td class=inp width=80>".NPM.":</td>
      <td class=ul1>$_inputNIM</td>
      <td class=ul1 width=180>
        $_inputCari
        </td>
      </tr>
  </form>
  </table>";
}
function CekBolehAksesModul() {
  $arrAkses = array(1, 20, 40, 42, 43, 44, 120, 56, 66, 440, 51,996,30,60);
  $key = array_search($_SESSION['_LevelID'], $arrAkses);
  if ($key === false)
    die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi Operator untuk informasi lebih lanjut."));
}
function BolehAksesData($nim) {
  if ($_SESSION['_LevelID'] == 120 && $_SESSION['_Login'] != $nim) {
    echo ErrorMsg('Error',
      "Anda tidak boleh melihat data KRS mahasiswa lain.<br />
      Anda hanya boleh mengakses data dari ".NPM.": <b>$_SESSION[_Login]</b>.<br />
      Hubungi Operator untuk informasi lebih lanjut");
    return false;
  } else return true;
}
function SetMaksimumSKS($thn, $nim){
$TahunSkrg      = substr($thn, 0, 4);
$SemesterSkrg     = substr($thn, -1, 1);
$TahunLalu      = ($SemesterSkrg == 2)? $TahunSkrg : $TahunSkrg-1;
$SemesterLalu   = ($SemesterSkrg == 2)? '1' : '2';
$Gabung       = $TahunLalu.$SemesterLalu;
$mhsw       = GetFields('mhsw',"MhswID",$nim, "left(MhswID,2) as BP,SemesterAwal,ProdiID");

$SemesterAwal     = $mhsw['SemesterAwal'];
$khs        = GetFields('khs', "TahunID = '".$Gabung."' AND MhswID", $nim, 'StatusMhswID,IPS');
$StatusMhswID   = $khs['StatusMhswID'];
  if ($Gabung >= $SemesterAwal) {
    $IP = $khs['IPS']+0;
    // IP Semester

    // Maksimum SKS
    $ProdiID = $mhsw['ProdiID'];
    $MaxSKS = GetaField('maxsks', "NA='N' and KodeID='".KodeID."' 
        and DariIP <= $IP and $IP <= SampaiIP
        and ProdiID", $ProdiID, 'SKS');
        
    // Reset Nilai Tertinggi
    ResetNilaiTertinggi($nim);
    BuatNilaiTertinggi($nim);
    
    // IPK
      $IPK = GetaField('krs left outer join khs on krs.KHSID=khs.KHSID', "krs.KodeID='".KodeID."' and krs.GradeNilai !='' 
                And krs.GradeNilai is not Null And not krs.GradeNilai='-' and not krs.GradeNilai='T' and krs.Tinggi='*' 
              and krs.NA='N' and (khs.TahunID <= $Gabung or krs.KHSID=0) and krs.MhswID",
      $nim,
      "sum(krs.BobotNilai * krs.SKS)/sum(krs.SKS)");
      
    // UPDATE KHS Lalu
    $update = _query("UPDATE khs set IP='".$IPK."', IPS='".$IP."' where MhswID='".$nim."' AND TahunID='".$Gabung."'");
    
    // UPDATE KHS Sekarang
    $khs = GetFields('khs',"MhswID='".$nim."' and TahunID", $thn, "*");
    if ($khs['Ignored']!='Y') {
      $update = "UPDATE khs set MaxSKS='".$MaxSKS."' where MhswID='".$nim."' AND TahunID='".$thn."'";
      $q = _query($update);
    }
  }   
}
function ResetNilaiTertinggi($MhswID) {
  $s = "update krs set Tinggi = '' where MhswID='$MhswID' and KodeID='".KodeID."' ";
  $r = _query($s);
}

function BuatNilaiTertinggi($MhswID) {
  // Ambil semuanya dulu
  $s = "select KRSID, MKKode, BobotNilai, GradeNilai, SKS, Tinggi
    from krs
    where KodeID = '".KodeID."'
      and MhswID = '$MhswID'
    order by MKKode";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $ada = GetFields('krs', "Tinggi='*' and KRSID<>'$w[KRSID]' and MhswID='$MhswID' and MKKode", $w['MKKode'], '*');
    // Jika nilai sekarang lebih tinggi
  $_wBobotNilai = $w['BobotNilai'];
  $_adaBobotNilai = (isset($ada['BobotNilai'])? $ada['BobotNilai']:'');
    if ($_wBobotNilai > $_adaBobotNilai) {
      $s1 = "update krs set Tinggi='*' where KRSID='$w[KRSID]' ";
      $r1 = _query($s1);
      // Cek yg lalu, kalau tinggi, maka reset
    $tinggi = (isset($ada['Tinggi'])? $ada['Tinggi']:'');
      if ($tinggi == '*') {
        $s1a = "update krs set Tinggi='' where KRSID='$ada[KRSID]' ";
        $r1a = _query($s1a);
      }
    }
    // Jika yg lama lebih tinggi, maka ga usah diapa2in
    else {
    }
  }
}

function ValidasiDataMhsw($thn, $nim, $khs) {
// Setting dulu maksimum SKS mengurangi beban staff untuk melakukan Proses IPK dan Proses Max SKS by Arisal Yanuarafi untuk UBH Juli 2013
  $StatusAwalID = GetaField("mhsw", "MhswID", $nim, "StatusAwalID");
  //if ($_SESSION['_LevelID'] == '120' && $StatusAwalID != 'S') SetMaksimumSKS($thn, $nim);
  SetMaksimumSKS($thn, $nim);
  $SKS = GetaField('krs', "MhswID='".$nim."' AND TahunID", $thn, "sum(SKS)");
    $update = _query('UPDATE khs SET SKS="'.$SKS.'" WHERE MhswID="'.$nim.'" AND TahunID="'.$thn.'"');
  /*if ($SKS > 24 && $_SESSION[_Login]==120) { $update = _query('UPDATE mhsw SET Blokir="Y" WHERE MhswID="'.$nim.'"');
        die(errorMsg("Blokir", "Maaf, Akun ini diblokir sementara karena telah mengisi KRS lebih dari yang ditentukan ($SKS SKS). 
        Untuk mengaktifkan akun portal Anda kembali silakan hubungi Pustikom.")); }*/
  $mhs = GetFields('mhsw m left outer join program p on p.ProgramID=m.ProgramID','m.MhswID', $nim,"m.*,p.Nama as PRG");
  $khs = GetFields("khs k
    left outer join statusmhsw s on s.StatusMhswID = k.StatusMhswID", 
    "k.KodeID = '".KodeID."' and k.TahunID = '$thn' and k.MhswID",
    $nim, 
    "k.*, s.Nama as STA");
  $skrg = date('Y-m-d');
  if (empty($khs)) {
  if($_SESSION['_LevelID'] == 120){ 
  $ThnTinggi = GetaField('tahun',"TahunID not like 'Tran%' AND KodeID",KodeID,'max(TahunID)');
  $_ThnTinggi = GetaField('tahun',"TahunID not Like 'Tran%' AND TahunID",$ThnTinggi,'Nama');
  $_thn = GetFields("tahun",
      "KodeID = '".KodeID."' and ProdiID = '$mhs[ProdiID]' and ProgramID = '$mhs[ProgramID]' and TahunID", $ThnTinggi, "*");
  
    
      echo ErrorMsg("Error",
        "Mahasiswa <b>$nim</b> tidak terdaftar di Tahun Akd <b>$thn</b>.<br />
        Masukkan data yang valid. Hubungi bagian Registrasi untuk informasi lebih lanjut.
        ");
    
  }else if($_SESSION['_LevelID']==60 || $_SESSION['_Login']=='auth0rized'){
  	$opt = GetOption2('program', 'Nama', 'ProgramID', $mhs['ProgramID'], '', 'ProgramID');
    $fid = GetFields('mhsw m left outer join prodi p on p.ProdiID=m.ProdiID', "m.MhswID", $nim, "p.FakultasID");
    $ThnTinggi = GetaField('tahun',"TahunID not like 'Tran%' AND KodeID",KodeID,'max(TahunID)');
    $_ThnTinggi = GetaField('tahun',"TahunID not like 'Tran%' AND TahunID",$ThnTinggi,'Nama');
    $_thn = GetFields("tahun",
      "KodeID = '".KodeID."' and ProdiID = '$mhs[ProdiID]' and ProgramID = '$mhs[ProgramID]' and TahunID", $ThnTinggi, "*");
    echo "<h2 align=center>
    			Nama : $mhs[Nama]<br>
    			No. BP: $nim<br>
    			Program: $mhs[PRG]<br>
    			<small>Status: $mhs[StatusMhswID]</small></h2>
    <p>Mahasiswa ini tidak terdaftar di Tahun Akd <b>$thn</b>.<br />
      Bila akan mengaktifkan pada semester ini, Silakan klik tombol daftarkan.
      <hr size=1 color=silver />
      <form name='Daftarkan' action='jur/krs.daftarkan.php' method=POST>
    <input type='hidden' name='MhswID' value='$nim'>
    <input type='hidden' name='TahunID' value='$thn'><center>"; ?>
    	Ganti Program : 
    	<SELECT Name='ProgramID' class='nones'><?php echo $opt;?></SELECT>
      <br /><input type=submit value='Daftarkan Mahasiswa ini' name='BuatData' onclick="return confirm('Anda mendaftarkan mahasiswa pada semester ini?')" />
    <?php echo "</center>
    </form></p>";

  }else{
    echo ErrorMsg("Error",
        "Mahasiswa <b>$nim, $mhs[Nama]</b>  tidak terdaftar di Tahun Akd <b>$thn</b>.<br />
        Masukkan data yang valid. Hubungi bagian Registrasi untuk informasi lebih lanjut.
        ");
    return false;
  }
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
  $pa = GetFields('dosen', "KodeID='".KodeID."' and Login", $mhsw['PenasehatAkademik'], "Nama, Gelar, Gelar1, concat(REPLACE(REPLACE(Nama,' ','_'),'.','_')) as Chat");
  // batas waktu
  $skrg = date('Y-m-d');
  //if ($thn['TglKRSMulai'] <= $skrg && $skrg <= $thn['TglKRSSelesai']) {
      if ($_SESSION['_LevelID'] == 120) {
      $CetakKRS = "<input type=button name='CetakKRS' value='Cetak KRS' onClick=\"javascript:CetakKRS($khs[KHSID])\" /></a>";
      $CetakLRS = '';
    }
    else {
      $CetakKRS = "<input type=button name='CetakKRS' value='Cetak KRS' onClick=\"javascript:CetakKRS($khs[KHSID])\" />";
      //$CetakLRS = "<input type=button name='CetakLRS' value='Cetak LRS' onClick=\"javascript:CetakLRS($khs[KHSID])\"/>";
      $CetakLRS = "";
    }
  KRSScript();
  //}
  //else {
  //  $CetakKRS = '&nbsp;';
  //  $CetakLRS = '&nbsp;';
  // }
  
  echo "<table class=box cellspacing=1 align=center width=850>
  <tr><td class=wrn width=2 rowspan=4></td>
      <td class=inp width=80>Mahasiswa:</td>
      <td class=ul width=200>$mhsw[Nama] <sup>($mhsw[MhswID])</sup></td>
      <td class=inp width=80>Sesi:</td>
      <td class=ul>$khs[Sesi]</td>
      <td class=inp width=80>Status:</td>
      <td class=ul width=100>$khs[STA] <sup>($khs[StatusMhswID])</sup></td>
      </tr>
  <tr><td class=inp>Batas KRS:</td>
      <td class=ul></td>
      <td class=inp>Jml SKS:</td>
      <td class=ul>$khs[SKS]<sub title='Maksimum SKS yg boleh diambil'>&minus;$khs[MaxSKS]</sub></td>
      <td class=inp>Status Awal:</td>
      <td class=ul>$mhsw[STAWAL] <sup>($mhsw[StatusAwalID])</sup></td>
      </tr>
  <tr><td class=inp>Batas Bayar:</td>
      <td class=ul></td>
      <td class=inp title='Dosen Pembimbing Akademik'>Pemb. Akd:</td>
      <td class=ul>$pa[Gelar1] $pa[Nama] <sup>$pa[Gelar]</sup>&nbsp;<br>
                  <a class='btn btn-small btn-primary' onclick=\"chatWith('$pa[Chat]')\"><i class='icon-envelope'></i> Kirim Pesan</a></td>
      <td class=ul colspan=2>
        $CetakLRS
        $CetakKRS
        <br />
        <sup>$khs[CetakKRS]&times; Cetak KRS</sup>
        </td>
      </tr>
  </table>";
}
function TampilkanPesanKRSSelesai() {
  echo "<table class=box cellspacing=1 align=center width=800>
  <tr><th class=wrn>Bukan periode input KRS.</th></tr>
  </table>";
}
function TampilkanDaftarKRSMhsw($thn, $mhsw, $khs) {
  $whr_hari = ($_SESSION['_krsHariID'] == '')? '' : "and j.HariID='$_SESSION[_krsHariID]'";
  $s = "SELECT k.*, j.JadwalID,
    j.MKID, j.Nama AS MKNama, j.HariID, j.NamaKelas, mk.SKS as _SKS,
    LEFT(j.JamMulai, 5) AS JM, LEFT(j.JamSelesai, 5) AS JS,
    j.RuangID, mk.Sesi, j.AdaResponsi,
    CONCAT(d.Gelar1,' ',d.Nama, ' <sup>', d.Gelar, '</sup>') AS DSN, j.JenisJadwalID, jj.Nama AS _NamaJenisJadwal, jj.Tambahan, kl.Nama AS NamaKelas,k._Presensi
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
      $whr_hari
    ORDER BY j.HariID, j.RuangID, j.JamMulai, j.JamSelesai";
  $r = _query($s);
  //die("<pre>$s</pre>");
  $skrg = date('Y-m-d');
  // Apakah sudah melebihi batas waktu ambil/ubah KRS?
  $ambil = '-';$paket = '';$hapus = ''; $boleh = false;
  if ($_SESSION['_LevelID']=='40' || $_SESSION['_LevelID']=='1' || $_SESSION['_LevelID']=='42' || $_SESSION['_LevelID']=='43') {
      KRSScript();
    $ambil = ($khs['SetujuPA']=='Y' ? "<input type=button name='Tolak' value='Batalkan Setuju PA' onClick=\"javascript:TolakSetujuPA('$mhsw[MhswID]', '$khs[KHSID]')\">":"<input type=button name='TambahMK' value='Ambil Matakuliah' onClick=\"javascript:AmbilKRS('$mhsw[MhswID]', '$khs[KHSID]')\" />");
    $paket = ($khs['SetujuPA']=='Y')? "":"<input type=button name='AmbilPaket' value='Ambil Paket' onClick=\"javascript:AmbilPaket('$mhsw[MhswID]', '$khs[KHSID]')\" />";
    $hapus = ($khs['SetujuPA']=='Y')? "":"<input type=button name='HapusSemua' value='Hapus Semua' onClick=\"javascript:HapusSemua('$khs[KHSID]')\" />";
    $boleh = true;
  }
  elseif ($_SESSION['_LevelID']==120){
    // selain superuser tergantung jadwal
      if ($thn['TglKRSMulai'] <= $skrg && $skrg <= $thn['TglKRSSelesai']) {
      KRSScript();
      if ($khs['SetujuPA']!='Y'){
          //$ambil = (($khs['Sesi']==1) && $mhsw['StatusAwalID']=='B') ? "<input type=button name='TambahMK' value='Ambil Matakuliah' onClick=\"javascript:AmbilKRS('$mhsw[MhswID]', '$khs[KHSID]')\" />":"<input type=button name='TambahMK' value='Ambil Matakuliah' onClick=\"javascript:AmbilKRS('$mhsw[MhswID]', '$khs[KHSID]')\" />";
          //$paket = ($khs['Sesi']==1)? "<input type=button name='AmbilPaket' value='Ambil Paket' onClick=\"javascript:AmbilPaket('$mhsw[MhswID]', '$khs[KHSID]')\" />":"";
          $ambil = "<input type=button name='TambahMK' value='Ambil Matakuliah' onClick=\"javascript:AmbilKRS('$mhsw[MhswID]', '$khs[KHSID]')\" />";
          $paket = ($khs['Sesi']==1)? "":"";
          //$paket .= ($khs['Sesi']==2)? "<input type=button name='AmbilPaket' value='Ambil Paket' onClick=\"javascript:AmbilPaket('$mhsw[MhswID]', '$khs[KHSID]')\" />":$paket;
      }
      $hapus = ($khs['SetujuPA']=='Y' && $khs['Bayar'] > 0)? "":"<input type=button name='HapusSemua' value='Hapus Semua' onClick=\"javascript:HapusSemua('$khs[KHSID]')\" />";
      if (($khs['Sesi']==1 || $khs['Sesi']==2) && empty($_SESSION['tolak'])) {
        //$q1 = "UPDATE khs set SetujuPA='Y' where KHSID='".$khs['KHSID']."' AND MhswID='".$khs['MhswID']."'";
        //$p1 = _query($q1);
        //$hapus .= "<a href='file/tata-cara-pengisian-krs.pdf' target='_blank'> Cara mengisi KRS?</a>";
      }
      $boleh = true;
      }
      else {
      TampilkanPesanKRSSelesai();
      }
  }
  
  // Tampilkan
  $opthari = GetOption2('hari', 'Nama', 'HariID', $_SESSION['_krsHariID'], '', 'HariID');
  echo "<table class=box cellspacing=1 align=center width=850>";
  echo "<tr>
    <script>
    function KeHari(frm) {
      window.location = '?mnux=$_SESSION[mnux]&_krsHariID='+frm[frm.selectedIndex].value;
    }
    </script>
    <td class=ul1 colspan=10>
      <select name='_krsHariID' onChange=\"javascript:KeHari(this)\">$opthari</select>
      $ambil
      $paket
      $hapus
      <img src='img/kanan.gif' /> <b>Daftar Matakuliah Yang Diambil Mahasiswa:</b>
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
    <th class=ttl width=50>Hadir</th>
    <th class=ttl width=20 title='Hapus KRS'>Hapus</th>
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
  if ($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==120){
    $del = ($boleh)? "<a href='#' onClick=\"javascript:HapusKRS('$w[KHSID]','$w[KRSID]')\" title='Hapus KRS' /><img src='img/del.gif' /></a>" : '&times;';
  } else $del='';
    
  // Bila ditandai bukan kuliah biasa, diarsir....
  if($w['Tambahan'] == 'Y')
  { $class='cnaY';
    $TagTambahan = "<b>( $w[_NamaJenisJadwal] ) </b>";
    $FieldResponsi = '';
  }
  else
  { $class='ul1';
    $TagTambahan = '';
    $FieldResponsi = '<br>';
    if($w['AdaResponsi'] == 'Y')
    { $FieldResponsi .= AmbilResponsi($w['JadwalID'], $w['KRSID'], $w['MhswID'], $thn['TahunID']);
    } 
  }
    
  echo "<tr>
      <td class=inp>$n</td>
      <td class=$class><sup>$w[JM]</sup>&#8594;<sub>$w[JS]</sub></td>
      <td class=$class align=center>$w[RuangID]&nbsp;</td>
    <td class=$class>$w[MKKode]<sup>$w[Sesi]</sup></td>
      <td class=$class>$w[Nama] $TagTambahan $FieldResponsi</td>
      <td class=$class align=right>$w[_SKS]</td>
      <td class=$class>$w[DSN]</td>
      <td class=$class align=center>$w[NamaKelas]&nbsp;</td>
      <td class=$class align=center>$w[_Presensi]<sub>x</sub></td>
      <td class=$class align=center>".(($khs['SetujuPA']=='Y' && $khs['Bayar'] > 0)? "":$del)."</td>
      </tr>";
  }
  echo "</table></p>";
  if ($khs['SetujuPA'] == 'N') $updatesx = _query("UPDATE khs set KonfirmasiKRS = 'N' where MhswID='$mhsw[MhswID]' and TahunID='$khs[TahunID]'");
  echo "<table class=box cellspacing=1 align=center width=800>
  <tr><td width=100><b>Status KRS:</b></td><td align=left>".(($khs['SetujuPA']=='Y')? "KRS yang Anda ajukan telah disetujui PA.":(($khs['SetujuPA']=='N')? 
  "KRS yang Anda ajukan tidak disetujui.".(($khs['KonfirmasiKRS']!='Y')? "<br>
  Bila Anda telah selesai mengisi KRS dan yakin tidak ada perubahan lagi silakan tekan tombol<input type='button' name='KonfirmasiKRS'
  value='Saya telah selesai mengisi KRS' onClick=\"javascript:KonfirmasiKRS('$mhsw[MhswID]', '$khs[KHSID]')\">":""): "KRS Anda belum diverifikasi oleh Pembimbing Akademik, silakan hubungi Dosen Pembimbing
  Akademik Anda untuk melakukan verifikasi. Sebelum diverifikasi, nama Anda tidak tertera di absen perkuliahan.
  .".(($khs['KonfirmasiKRS']!='Y')? "<br>
  Bila Anda telah selesai mengisi KRS dan yakin tidak ada perubahan lagi silakan tekan tombol<input type='button' name='KonfirmasiKRS'
  value='Saya telah selesai mengisi KRS' onClick=\"javascript:KonfirmasiKRS('$mhsw[MhswID]', '$khs[KHSID]')\">
  ":"")))."</td></tr>
  <tr><td><b>Catatan PA:</b></td><td>".$khs['Alasan']."</td></tr></table>";

  if ($_SESSION['_LevelID']==60){
    echo "<form name='Batalkan' action='jur/krs.batalkan.php' method=POST>
    <input type='hidden' name='MhswID' value='$khs[MhswID]'>
    <input type='hidden' name='TahunID' value='$khs[TahunID]'><center>"; ?>
      <br /><input type=submit value='Batalkan Registrasi Mahasiswa ini (X)' name='BuatData' onclick="return confirm('Anda membatalkan registrasi mahasiswa pada semester ini?')" />
    <?php echo "</center>
    </form>";
  }

}
function HapusKRS() {
  $khsid = $_REQUEST['khsid'];
  $krsid = $_REQUEST['krsid'];
  $jdwlid = GetaField('krs', 'KRSID', $krsid, 'JadwalID');
  $KonfirmasiAktif = GetFields('khs', "KHSID", $khsid, "KonfirmasiAktif,Bayar");
  if ($_SESSION['_LevelID']=='120') 
    {
        if ($KonfirmasiAktif['SetujuPA']!='Y') {
      // Penghapusan
      $s = "delete from krs where KRSID = $krsid ";
      $r = _query($s);
      // Hapus data presensi
      $s1 = "delete from presensimhsw where KRSID = $krsid";
      $r1 = _query($s1);
      // update data
      HitungPeserta($jdwlid);
      HitungUlangKRS($khsid);
      }
  }
  else {
    // Penghapusan
      $s = "delete from krs where KRSID = $krsid ";
      $r = _query($s);
      // Hapus data presensi
      $s1 = "delete from presensimhsw where KRSID = $krsid";
      $r1 = _query($s1);
      // update data
      HitungPeserta($jdwlid);
      HitungUlangKRS($khsid);
      }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}
function HapusSemua() {
  $khsid = $_REQUEST['khsid'];
  $KonfirmasiAktif = GetFields('khs', "KHSID", $khsid, "SetujuPA,Bayar,MhswID,TahunID");
  if ($_SESSION['_LevelID']=='120')
    {  
      if ($KonfirmasiAktif['SetujuPA']!='Y' && $KonfirmasiAktif['Bayar']==0) {
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
    $SKS = GetaField('krs', "MhswID='".$KonfirmasiAktif['MhswID']."' AND TahunID", $KonfirmasiAktif['TahunID'], "sum(SKS)");
    $update = 'UPDATE khs SET KonfirmasiAktif="N",SKS="'.$SKS.'" WHERE MhswID="'.$KonfirmasiAktif['MhswID'].'" AND TahunID="'.$KonfirmasiAktif['TahunID'].'"';
    $delete_bipot = 'DELETE FROM bipotmhsw WHERE MhswID="'.$KonfirmasiAktif['MhswID'].'" AND Dibayar=0 AND TahunID="'.$KonfirmasiAktif['TahunID'].'" AND BIPOTNamaID!=14';
    $delete_bipot2 = 'DELETE FROM bipotmhsw2 WHERE MhswID="'.$KonfirmasiAktif['MhswID'].'" AND flag="0" AND BayarMhswID=""  AND TahunID="'.$KonfirmasiAktif['TahunID'].'" AND BIPOTNamaID!=14';
        }
    }
    else {
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
    $s1 = "UPDATE khs set SKS=0 where KHSID='$khsid'";
    if ($r1 = _query($s1)){
          //die($s1);
    }
    $s1 = "DELETE from krs where MhswID='$KonfirmasiAktif[MhswID]' and TahunID='$KonfirmasiAktif[TahunID]'";
    $r1 = _query($s1);
  HitungUlangKRS($khsid);
  }
  //BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
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
  function AmbilKRS(mhswid, khsid) {
    lnk = "$_SESSION[mnux].ambil.php?mhswid="+mhswid+"&khsid="+khsid;
    win2 = window.open(lnk, "", "width=1000, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
  }
  function AmbilPaket(mhswid, khsid) {
    lnk = "$_SESSION[mnux].ambilpaket.php?mhswid="+mhswid+"&khsid="+khsid;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
  }
  function TolakSetujuPA(mhswid, khsid){
  lnk = "$_SESSION[mnux].tolaksetujupa.php?mhswid="+mhswid+"&khsid="+khsid;
  win2 = window.open(lnk, "", "width=1, height=1, scrollbars, status, resizable");
  if (win2.opener == null) childWindow.opener = self;
  }
  function KonfirmasiKRS(mhswid, khsid){
  lnk = "$_SESSION[mnux].konfirmasikrs.php?mhswid="+mhswid+"&khsid="+khsid;
  win2 = window.open(lnk, "", "width=1, height=1, scrollbars, status, resizable");
  if (win2.opener == null) childWindow.opener = self;
  }
  function HapusKRS(khsid,krsid) {
    if (confirm("Anda yakin akan menghapus matakuliah ini dari KRS Anda?")) {
      window.location = "?mnux=$_SESSION[mnux]&gos=HapusKRS&khsid="+khsid+"&krsid="+krsid;
    }
  }
  function HapusSemua(khsid) {
    if (confirm("Anda yakin akan menghapus semua matakuliah di KRS?")) {
      window.location = "?mnux=$_SESSION[mnux]&gos=HapusSemua&khsid="+khsid;
    }
  }
  function CetakKRS(khsid) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?khsid="+khsid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
    window.location = "?mnux=$_SESSION[mnux]&gos=CetakKRS&BypassMenu=1&khsid="+khsid;
  }
  function CetakLRS(khsid) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].lrs.php?khsid="+khsid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status, resizable");
    if (win2.opener == null) childWindow.opener = self;
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
function CetakKRS() {
  $khsid = $_REQUEST['khsid']+0;
  if ($khsid > 0) {
    $s = "update khs set CetakKRS = CetakKRS+1 where KHSID='$khsid' ";
    $r = _query($s);
  }
  echo "<script>
  window.location = '?mnux=$_SESSION[mnux]&gos=';
  </script>";
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
  { foreach($arrEkstra as $ekstra)
    { $s = "select k.KRSID, jr.JadwalID, jr.JadwalRefID, h.Nama as _NamaHari, LEFT(jr.JamMulai, 5) as _JM, LEFT(jr.JamSelesai, 5) as _JS, 
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
      { $NamaJenisJadwal = GetaField('jenisjadwal', "JenisJadwalID", $ekstra, "Nama");
        $a[] = "&rsaquo; <font color=red>$NamaJenisJadwal ( belum terjadwal ) </font><a href='#' onClick=\"KRSLabEdt(1, '$id', '$krsid', '$w[KRSID]', '$ekstra')\"><font size=0.8m>Tambah</font></a>";
      }
      else if($n == 1)
      { $w = _fetch_array($r);
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
  { $n = 0;
    $jj = $w['JenisJadwalID'];
  }
  $n++;
  $a[] = "&rsaquo; <b>$w[_NamaJenisJadwal] #$n</b> $w[_NamaHari], $w[_JM] - $w[_JS], $w[_NamaRuang]($w[RuangID]) <a href='#' onClick=\"JdwlLabEdt(0, '$w[JadwalRefID]', '$w[JadwalID]')\"><img src='img/edit.png' /></a>";
  }
  $a = (!empty($a))? "<br />".implode("<br />", $a) : '';
  return $a;*/
}
?>
