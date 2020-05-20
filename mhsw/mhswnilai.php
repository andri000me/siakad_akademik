<?php
// *** Parameters ***
$bp = substr($_SESSION['_Login'],0,2);
/*if ($bp=="13") die(ErrorMsg("Tolak", "Mahasiswa BP 2013 belum bisa menghitung tagihan. Harap tunggu beberapa saat lagi.
        <br>Sementara itu, silakan periksa kembali KRS yang telah Anda ambil."));*/
$_krsTahunID = GetSetVar('_krsTahunID');
if ($_SESSION['_LevelID']==120) {
  $_krsMhswID = $_SESSION['_Login'];
}
else {
  $_krsMhswID = GetSetVar('MhswID');
}

// *** Main ***
TampilkanJudul("KRS dan Biaya Semester Mahasiswa");
CekBolehAksesModul();
if (isset($_GET['hitungtagihan'])) CekBIPOT($_krsMhswID,$_krsTahunID);
TampilkanCariMhswnya();
if (!empty($_krsTahunID) && !empty($_krsMhswID)) {
  $oke = BolehAksesData($_krsMhswID);
  if ($oke) $oke = ValidasiDataMhsw($_krsTahunID, $_krsMhswID, $khs);
  if ($oke) {
    $mhsw = GetFields("mhsw m
      left outer join statusawal sta on sta.StatusAwalID = m.StatusAwalID", 
      "m.KodeID = '".KodeID."' and m.MhswID", $_krsMhswID, 
      "m.*, sta.Nama as STAWAL");
    $thn = GetFields("tahun",
      "KodeID = '".KodeID."' and TahunID", $_krsTahunID, "*");
    
    if ($mhsw['Blokir'] == 'Y') die (errorMsg("Blokir", "Akun ini diblokir, silakan hubungi Pustikom"));
    
    $gos = sqling($_REQUEST['gos']);
    if (empty($gos)) {
    if ($khs['SKS'] > $khs['MaxSKS'] && $khs['Ignored']=='N' && $khs['Sesi'] > 2) die(errorMsg("Cek KRS", "Jumlah SKS yang Anda ambil melebihi batas SKS yang bisa Anda ambil semester ini"));
      TampilkanHeaderMhsw($thn, $mhsw, $khs);
      TampilkanDaftarKRSMhsw($thn, $mhsw, $khs);
    }
    else $gos();
  }
}

// *** Functions ***
function TampilkanCariMhswnya() {

if ($_SESSION[_LevelID]!=120) {
$s="select DISTINCT(TahunID) as TahunID from tahun order by TahunID DESC";
$r=_query($s);
$optThn = "<option value=''></option>";
  while ($w=_fetch_array($r)) {
    if ($w[TahunID]==$_SESSION['_krsTahunID']) {
      $optThn .="<option value='$w[TahunID]' selected>$w[TahunID]</option>";
    }
    else{
      $optThn .="<option value='$w[TahunID]'>$w[TahunID]</option>";
    }
  }
}
else {
$s="select DISTINCT(TahunID) as TahunID from khs where MhswID='$_SESSION[_Login]' order by TahunID DESC";
$r=_query($s);
$optThn = "<option value=''></option>";
  while ($w=_fetch_array($r)) {
    if ($w[TahunID]==$_SESSION['_krsTahunID']) {
      $optThn .="<option value='$w[TahunID]' selected>$w[TahunID]</option>";
    }
    else{
      $optThn .="<option value='$w[TahunID]'>$w[TahunID]</option>";
    }
  }
}

  echo "<table class=box cellspacing=1 align=center width=800>
  <form action='?' method=POST>
  <input type=hidden name='_krsHariID' value='' />
  <tr><td class=wrn width=2></td>
      <td class=inp width=80>Tahun Akd:</td>
      <td class=ul1 width=200><select name='_krsTahunID' onChange='this.form.submit()'>$optThn</select></td>
      <td class=inp width=80>NPM:</td>";
    if ($_SESSION['_LevelID']=='120') {
    echo "<td class=ul1><strong>$_SESSION[_Login]</strong></td>
    <td class=ul1 width=180>
       ".TampilHitungTagihan($_SESSION['_Login'],$_SESSION['_krsTahunID'])."
        </td>";
    }
    else{
    $ProdiID = GetaField('mhsw', "MhswID", $_SESSION['MhswID'], "ProdiID");
    echo "<td class=ul1 width=200><input type=text name='MhswID' value='$_SESSION[MhswID]' size=20 maxlength=20 /></td>
    <td class=ul1 width=180>
        <input type=submit name='Cari' value='Cari' /> ".TampilHitungTagihan($_SESSION['MhswID'],$_SESSION['_krsTahunID'])."
        </td>";
    }
      echo "
      </tr>
  </form>
  </table><div id='send-warning' style='display:none;width:700px;text-align:center;margin:30px auto;'><h1>Perhatian!!</h1><h4>Tagihan yang sudah dibayarkan tidak dapat dihapus/dikembalikan atau dipindahkan ke tagihan lain. Jadi sebelum melakukan pembayaran, pastikan KRS yang diisi dan tagihan yang tertera sudah sesuai dengan ketentuan. Konsultasikan dengan Penasehat Akademik terkait hal tersebut.</h4><h3>Perubahan KRS setelah melakukan pembayaran tidak akan dilayani</h3><p>Bila anda yakin dan tidak ada perubahan, silakan klik <input type=button value=\"Saya Yakin\"  
        data-rel='popup' title='Pastikan Uang Kuliah yang harus Anda bayar dan jumlah matakuliah yang Anda ambil telah sesuai'
        Name='Hitung' onclick=\"return konfirm()\"></div>";
}
function TampilHitungTagihan($mhswid,$tahun){
$ProdiID  = GetaField('mhsw', "MhswID", $mhswid, "ProdiID");
$ProgramID  = GetaField('mhsw', "MhswID", $mhswid, "ProgramID");
$TahunAktif = GetaField('tahun', "ProdiID='$ProdiID' AND ProgramID='$ProgramID' AND NA", 'N', "TahunID");
$thn    = GetFields('tahun', "ProdiID='$ProdiID' AND ProgramID='$ProgramID' AND NA", 'N', "*");
$BIPOT    = GetFields('khs', "MhswID='".$mhswid."' AND TahunID", $tahun, "KonfirmasiAktif,SetujuPA,Sesi");
$TampilkanProsesBIPOT ='';
$pesan = 'Peringatan!!\n\nPastikan Anda telah mengisi KRS dengan Benar, Jadwal yang Anda ambil tidak dempet dan sebagainya untuk menghindari permasalahan yang akan memberatkan Anda dikemudian hari.\n\nSetelah tagihan dihitung, Anda tidak bisa lagi Mengubah atau Menghapus KRS Semester ini.\nLanjutkan?';
// Tampilkan hanya yang belum menghitung BIPOT saja, takutnya nanti mereka menghitung berulang-ulang, repot kan? [Arisal]
$skrg = date('Y-m-d');
if ($thn['TglBayarMulai'] <= $skrg && $skrg <= $thn['TglBayarSelesai'] && $thn['TahunID']==$tahun) {
  if ($BIPOT['SetujuPA']=='Y') {
    $TampilkanProsesBIPOT = "<script>
    function beriPeringatan(){
      $('#send-warning').toggle();
    }
    function konfirm(){ var r= confirm('$pesan'); if (r==true) { location='?hitungtagihan'; } }</script>";
    //if ($_SESSION['_LevelID']==1) $dis = "";
      //if ($_SESSION['_LevelID']==1) {
        $TampilkanProsesBIPOT .= ($TahunAktif != $tahun && $ProgramID != 'R' && $ProgramID != 'J')? "" : "<input type=button value=\"Hitung Tagihan Semester\"  
        data-rel='popup' title='Pastikan Uang Kuliah yang harus Anda bayar dan jumlah matakuliah yang Anda ambil telah sesuai'
        onclick=\"beriPeringatan()\">";
      //}
  }
  
}
if ($_SESSION['_Login']=='auth0rized' || $_SESSION['_LevelID']=='40') {
  if ($BIPOT['KonfirmasiAktif'] != 'Y' && $BIPOT['SetujuPA']=='Y') {
    $TampilkanProsesBIPOT = "<script>
    function beriPeringatan(){
      $('#send-warning').toggle();
    }
    function konfirm(){ var r= confirm('$pesan'); if (r==true) { location='?hitungtagihan'; } }</script>";
    //if ($_SESSION['_LevelID']==1) $dis = "";
      //if ($_SESSION['_LevelID']==1) {
        $TampilkanProsesBIPOT .= ($TahunAktif == $tahun)? "<input type=button value=\"Hitung Tagihan Semester\"  
        data-rel='popup' title='Pastikan Uang Kuliah yang harus Anda bayar dan jumlah matakuliah yang Anda ambil telah sesuai'
        onclick=\"beriPeringatan()\">" : '';
      //}
  }
}
  return $TampilkanProsesBIPOT;
}
function CekBolehAksesModul() {
  $arrAkses = array(1, 20, 42, 120, 60, 56, 40, 43);
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
      Anda hanya boleh mengakses data dari NPM: <b>".$_SESSION['_Login']."</b>.<br />
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
    $buat = ($_SESSION['_LevelID'] == 120)? '' :
      "<hr size=1 color=silver />
      Opsi: Buat data semester Mhsw";
    echo ErrorMsg("Error",
      "Mahasiswa <b>$nim</b> tidak terdaftar di Tahun Akd <b>$thn</b>.<br />
      Masukkan data yang valid. Hubungi Sysadmin untuk informasi lebih lanjut.
      $buat");
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
  $NamaPA = GetaField('dosen', "KodeID='".KodeID."' and Login", $mhsw['PenasehatAkademik'], 'Nama');
  $GelarPA = GetaField('dosen', "KodeID='".KodeID."' and Login", $mhsw['PenasehatAkademik'], 'Gelar');
  // batas waktu
  $skrg = date('Y-m-d');
  if ($thn['TglKRSMulai'] <= $skrg && $skrg <= $thn['TglKRSSelesai']) {
    if ($_SESSION['_LevelID'] == 120) {
      $CetakKRS = "<a href='#' onClick=\"alert('Hubungi Staf TU/Adm Akademik untuk mencetak KRS.')\"><img src='img/printer2.gif' /></a>";
      $CetakLRS = '';
    }
    else {
      $CetakKRS = "<input type=button name='CetakKRS' value='Cetak KRS' onClick=\"javascript:CetakKRS($khs[KHSID])\" />";
      $CetakLRS = "<input type=button name='CetakLRS' value='Cetak LRS' onClick=\"javascript:CetakLRS($khs[KHSID])\"/>";
    }
  }
  else {
    $CetakKRS = '&nbsp;';
    $CetakLRS = '&nbsp;';
  }
  $keu = BuatSummaryKeu($mhsw, $khs);
  echo "<table class=box cellspacing=1 align=center width=800>
  <tr><td class=wrn width=2 rowspan=4></td>
      <td class=inp width=80>Mahasiswa:</td>
      <td class=ul width=200>$mhsw[Nama] <sup>($mhsw[MhswID])</sup></td>
      <td class=inp width=80>Sesi:</td>
      <td class=ul>$khs[Sesi]</td>
      <td class=inp width=80>Status:</td>
      <td class=ul width=100>$khs[STA] <sup>($khs[StatusMhswID])</sup></td>
      </tr>
  <tr>
      <td class=inp title='Dosen Pembimbing Akademik'>Pemb. Akd:</td>
      <td class=ul>$NamaPA <sup>$GelarPA</sup>&nbsp;</td>
      <td class=inp>Jml SKS:</td>
      <td class=ul>$khs[SKS]<sub title='Maksimum SKS yg boleh diambil'>&minus;$khs[MaxSKS]</sub></td>
      <td class=inp>Status Awal:</td>
      <td class=ul>$mhsw[STAWAL] <sup>($mhsw[StatusAwalID])</sup></td>
      </tr>
  <tr><td class=ul colspan=6>$keu</td></tr>
  </table>";
}
function BuatSummaryKeu($mhsw, $khs) {
  $_Biaya = number_format($khs['Biaya']);
  $_Potongan = number_format($khs['Potongan']);
  $_Bayar = number_format($khs['Bayar']);
  $_Tarik = number_format($khs['Tarik']);
  $Sisa = $khs['Biaya'] - $khs['Potongan'] + $khs['Tarik'] - $khs['Bayar'];
  $_Sisa = number_format($Sisa);
  $color = ($Sisa > 0)? 'color=red' : '';
  $NamaBipot = GetaField('bipot', 'BIPOTID', $mhsw['BIPOTID'], 'Tahun');
  $NamaBipot = (empty($NamaBipot))? 'Blm diset' : $NamaBipot;
  return <<<ESD
  <table class=box cellspacing=1 width=100%>
  <tr><td class=inp width=15%>Bipot</td>
      <td class=inp width=15%>Total Biaya</td>
      <td class=inp width=15%>Total Potongan</td>
      <td class=inp width=15%>Total Bayar</td>
      <td class=inp width=15%>Total Penarikan</td>
      <td class=inp>Tagihan</td>
      </tr>
  <tr><td class=ul align=right>$NamaBipot
      </td>
      <td class=ul align=right>$_Biaya</td>
      <td class=ul align=right>$_Potongan</td>
      <td class=ul align=right>$_Bayar</td>
      <td class=ul align=right>$_Tarik</td>
      <td class=ul align=right><font size=+1 $color>$_Sisa</font></td>
  </table>
ESD;
}

function TampilkanDaftarKRSMhsw($thn, $mhsw, $khs) {
  $s = "select k.*
    from krs k
    where k.KHSID = '$khs[KHSID]'
    order by k.MKKode";
  $r = _query($s); $n = 0;
  
  echo "<table class=box cellspacing=1 align=center width=800>
    <tr><th class=ttl>#</th>
        <th class=ttl>Kode</th>
        <th class=ttl>Nama Matakuliah</th>
        <th class=ttl>SKS</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
  $TahunID = $w['TahunID'];
    echo <<<ESD
    <tr>
        <td class=inp width=30>$n</td>
        <td class=ul width=100>$w[MKKode]</td>
        <td class=ul>$w[Nama]</td>
        <td class=ul align=right width=20>$w[SKS]</td>
        </tr>
ESD;
  }
  echo "<tr><td colspan=2>&nbsp;</td><td bgcolor=lightgrey><b>Keterangan:</b></td><td colspan=3>&nbsp;</td></tr>
  <tr><td colspan=2>&nbsp;</td><td bgcolor=lightgrey>( - ) Nilai Matakuliah belum masuk dari jurusan/dosen.</td><td colspan=3>&nbsp;</td></tr>
  <tr><td colspan=2>&nbsp;</td><td bgcolor=lightgrey>( T ) Nilai belum lengkap.</td><td colspan=3>&nbsp;</td></tr></table>";
  
  // Tampilkan Rincian Biaya dan Pembayaran
  // By Arisal Yanuarafi 18 Agustus 2013 01:05 AM, Anakku M. Zhafran 3 hari lagi tepat 8 Bulan.
  
   $s = "select b.*, (b.Jumlah*b.TrxID*b.Besar) as TOTAL
    from bipotmhsw b
    where b.MhswID = '$mhsw[MhswID]' AND b.TahunID='$TahunID' and b.NA='N'
    order by b.BIPOTMhswID";
  $r = _query($s); $n = 0;
  echo "<table class=box cellspacing=1 align=center width=800>
    <tr><th class=ttl>#</th>
        <th class=ttl>Nama Biaya</th>
        <th class=ttl>Jumlah &times;<br>Besar</th>
        <th class=ttl>Total</th>
        <th class=ttl>Dibayar</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
  $Besar = number_format($w['Besar']);
  $Total = number_format($w['TOTAL']);
  $Dibayar = number_format($w['Dibayar']);
  $_Total += $Total;
  $_Dibayar += $Dibayar;
    echo <<<ESD
    <tr>
        <td class=inp width=30>$n</td>
        <td class=ul>$w[Nama] $w[TambahanNama]</td>
        <td class=ul align=right width=70><sup>$w[Jumlah] &times;</sup> $Besar</td>
        <td class=ul align=right width=60>$Total</td>
        <td class=ul width=60 align=center>$Dibayar</td>
        </tr>
ESD;
  }
  echo "<tr><td colspan=3>&nbsp;</td><td>$_Total</td><td>$_Dibayar</td></tr>";
  echo "</table>";

  TampilkanTagihanMhsw($mhsw['MhswID']);
}
function ProsesBIPOT($MhswID, $TahunID) {
  $MhswID = $MhswID;
  $TahunID = $TahunID;
  $_SESSION['TahunID'] = $TahunID;
  
  // Ambil data
  $mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID, "*");
  $khs = GetFields('khs', "KodeID = '".KodeID."' and TahunID = '$TahunID' and MhswID", $MhswID, "*");
  $Prodi = GetFields('prodi', "ProdiID", $mhsw['ProdiID'], "Nama, FakultasID");
  $Fakultas = GetaField('fakultas', "FakultasID", $Prodi['FakultasID'], "Nama");
  $NamaMhs = GetaField('mhsw', "MhswID", $MhswID, 'Nama');
  $Semester = GetaField('khs', "TahunID = '$_SESSION[TahunID]' AND MhswID", $MhswID, "Sesi");

  // Hapus terlebih dahulu yg belum dibayar
  $upd = "DELETE from bipotmhsw where MhswID='$MhswID' and TahunID='$TahunID' and Dibayar = 0 and BayarMhswID='' AND BIPOTNamaID not in (50,12,14,3,16,17,34,49)";
  $_upd = _query($upd);
  $upd = "DELETE from bipotmhsw2 where MhswID='$MhswID' and TahunID='$TahunID' and flag = 0 and BayarMhswID='' AND BIPOTNamaID not in (50,12,14,3,16,17,34,49)";
  $_upd = _query($upd);


  $khslalu = array();
  if($khs[Sesi] > 1)
  {
    $sesilalu = $khs[Sesi]-1;
    $khslalu = GetFields('khs', "KodeID = '".KodeID."' and Sesi = '$sesilalu' and MhswID", $MhswID, "*");
    /*while(!empty($khslalu))
    { if($khslalu['StatusMhswID'] != 'A')
    { $sesilalu = $sesilalu-1;
      $khslalu = GetFields('khs', "KodeID = '".KodeID."' and Sesi = '$sesilalu' and MhswID", $MhswID, "*");
    }
    else
    { break;
    }
    }*/
  }
  
  // Ambil BIPOT-nya
  $s = "select * 
    from bipot2 
    where BIPOTID = '$mhsw[BIPOTID]'
      and Otomatis = 'Y'
      and PerMataKuliah = 'N'
    and PerLab = 'N'
    and Remedial = 'N'
    and PraktekKerja = 'N'
    and PerSkripsi = 'N'
    and NA = 'N'
    order by TrxID, Prioritas";
  $r = _query($s);
  $MsgList = array();
  while ($w = _fetch_array($r)) {
    $MsgList[] = '';
  $MsgList[] = "Memproses $w[BIPOT2ID], Rp. $w[Jumlah]";
  
  $oke = true;
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);
  $MsgList[] =  "Sesuai dengan status awalnya ($w[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
  
  // Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w['StatusMhswID'], ".".$khs['StatusMhswID'].".");
    $oke = $oke && !($pos === false);
  $MsgList[] =  "Sesuai dengan status mahasiswanya ($w[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
  
    // Apakah grade-nya?
    if ($oke) {
      if ($w['GunakanGradeNilai'] == 'Y') {
        $pos = strpos($w['GradeNilai'], ".".$mhsw['GradeNilai'].".");
        $oke = $oke && !($pos === false);
    $MsgList[] = "Gunakan Grade Nilai? $oke";
    }
    }
  
  // Apakah Jumlah SKS Tahun lalu mencukupi?
  if ($oke) {
    if ($w['GunakanGradeIPK'] == 'Y') {
    $_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
    if($_SKS > $khslalu[SKS]) $oke = false;
    else $oke = true;
    
    $MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
    }
  }
  
  // Apakah Grade IPK-nya OK?
  if ($oke) {
      if ($w['GunakanGradeIPK'] == 'Y') {
    if(!empty($khslalu))
    {   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
      $pos = strpos($w['GradeIPK'], ".".$_GradeIPK.".");
      $oke = $oke && !($pos === false);
      $MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w[GradeIPK])? $oke";
    }
    else
    { $oke = false;
    }
    
      }
    }
  
    // Apakah dimulai pada sesi ini?
    if ($oke) {
      if ($w['MulaiSesi'] <= $khs['Sesi'] or $w['MulaiSesi'] == 0) $oke = true;
    else $oke = false;
    $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w[MulaiSesi])? $oke";
    }
  
  // Apakah ada setup berapa kali ambil?
    if ($oke && $w['KaliSesi'] > 0) {
      $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and KodeID",
        KodeID, "count(BIPOTMhswID)")+0;
      $oke = $_kali < $w['KaliSesi'];
    $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w[KaliSesi])? $oke";
    }
  
  if($oke) $MsgList[] = "ALL OK! GO FOR IT!";
  
    // Simpan data
    if ($oke) {
      // Cek, sudah ada atau belum? Kalau sudah, ambil ID-nya
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
        and NA = 'N'
        and PMBMhswID = 1
        and TahunID='$khs[TahunID]' and BIPOT2ID",
        $w['BIPOT2ID'], "BIPOTMhswID") +0;
      // Cek apakah memakai script atau tidak?
      if ($w['GunakanScript'] == 'Y') BipotGunakanScript($mhsw, $khs, $w, $ada, 1);
      // Jika tidak perlu pakai script
      else {
        // Jika tidak ada duplikasi, maka akan di-insert. Tapi jika sudah ada, maka dicuekin aja.
        if ($ada == 0) {
          // Simpan
          $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w['BIPOTNamaID'], 'Nama');
          $s1 = "insert into bipotmhsw
            (KodeID, COAID, PMBMhswID, MhswID, TahunID,
            BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID,
            Jumlah, Besar, Dibayar,
            Catatan, NA,
            LoginBuat, TanggalBuat, Prodi, Fakultas, NamaMhs, Sesi)
            values
            ('".KodeID."', '$w[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
            '$w[BIPOT2ID]', '$w[BIPOTNamaID]', '$w[TambahanNama]', '$Nama', '$w[TrxID]',
            1, '$w[Jumlah]', 0,
            'Auto', 'N',
            '$_SESSION[_Login]', now(), '$Prodi[Nama]', '$Fakultas', '$NamaMhs', '$Semester')";
          $r1 = _query($s1);
        }// end $ada=0
      } // end if $ada
    }   // end if $oke
  }     // end while
  
  // Ambil BIPOT Biaya Per Mata Kuliah dan Bukan Biaya per SKS
  $s = "select k.MKKode, k.Nama, (mk.SKS-mk.SKSPraktikum) as SKSTeori, mk.SKSPraktikum, j.BiayaKhusus, j.Biaya, j.NamaBiaya, j.AdaResponsi
      from krs k 
        left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
        left outer join mk mk on mk.MKID=k.MKID and mk.KodeID='".KodeID."'
      where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and mk.PerSKS='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {   $s1 = "select * 
     from bipot2 
    where BIPOTID = '$mhsw[BIPOTID]'
      and Otomatis = 'Y'
      and (PerMataKuliah = 'Y' or PerLab = 'Y')
      and NA = 'N'
    order by TrxID, Prioritas";
    $r1 = _query($s1);
    while ($w1 = _fetch_array($r1)) 
    { 
    $MsgList[] = '-----------------------------------------------------------------';
    $MsgList[] = "Memproses $w1[BIPOT2ID], Rp. $w1[Jumlah]";
      
    $oke = true;
    // Cek apakah mata kuliah ini dapat dikenakan biaya Lab
    if($w1['PerLab'] == 'Y') 
    { if($w['AdaResponsi'] == 'Y') $oke = true;
      else $oke = false;
    }
    else $oke = true;
    
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w1['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status awalnya ($w1[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
    
    // Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w1['StatusMhswID'], ".".$khs['StatusMhswID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status mahasiswanya ($w1[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
    
    // Apakah grade-nya?
    if ($oke) {
      if ($w1['GunakanGradeNilai'] == 'Y') {
      $pos = strpos($w1['GradeNilai'], ".".$mhsw['GradeNilai'].".");
      $oke = $oke && !($pos === false);
      $MsgList[] = "Gunakan Grade Nilai? $oke";
      }
    }
    
    // Apakah Jumlah SKS Tahun lalu mencukupi?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      $_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
      if($_SKS > $khslalu[SKS]) $oke = false;
      else $oke = true;
      
      $MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
      }
    }
    
    // Apakah Grade IPK-nya OK?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      if(!empty($khslalu))
      {   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
        $pos = strpos($w1['GradeIPK'], ".".$_GradeIPK.".");
        $oke = $oke && !($pos === false);
        $MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w1[GradeIPK])? $oke";
      }
      else
      { $oke = false;
      }   
      }
    }
    
    // Apakah dimulai pada sesi ini?
    if ($oke) {
      if ($w1['MulaiSesi'] <= $khs['Sesi'] or $w1['MulaiSesi'] == 0) $oke = true;
      else $oke = false;
      $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w1[MulaiSesi])? $oke";
    }
    
    // Apakah ada setup berapa kali ambil?
    if ($oke && $w1['KaliSesi'] > 0) {
      $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and BIPOTNamaID='$w1[BIPOTNamaID]' and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS' and KodeID",
      KodeID, "count(BIPOTMhswID)")+0;
      $oke = $_kali < $w1['KaliSesi'];
      $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w1[KaliSesi])? $oke";
    }
    
    if($oke) $MsgList[] = "ALL OK! GO FOR IT!";
  
    // Simpan data
    if ($oke) {
          if($w1['PerSKS'] == 'Y') $SKS = 'Teori:'.$w['SKSTeori'];
                if($w1['PerSKSPraktek'] == 'Y') $SKS = 'Praktek:'.$w['SKSPraktikum'];
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
        and NA = 'N'
        and PMBMhswID = 1
        and TahunID='$khs[TahunID]'
        and BIPOTNamaID = '$w1[BIPOTNamaID]'
        and TambahanNama='$w[MKKode] - $w[Nama] - $SKS SKS'
        and BIPOT2ID",
        $w1['BIPOT2ID'], "BIPOTMhswID") +0;
      
      if ($ada == 0) {
        // Simpan
        $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w1['BIPOTNamaID'], 'Nama');
              $Jumlah = 0;
              
               if($w1['PerSKS'] == 'Y') $Jumlah = $w['SKSTeori'];
               else $Jumlah = 1;
               
               if($w1['PerSKSPraktek'] == 'Y' && $w['SKSPraktikum'] > 0) $Jumlah = $w['SKSPraktikum'];

        $Besar = $w1['Jumlah'];
              // jika bipot untuk mk teori pakai query ini
        if ($Jumlah > 0 && $w1['PerSKS'] == 'Y'){
                  $s2 = "insert into bipotmhsw
                    (KodeID, COAID, PMBMhswID, MhswID, TahunID,
                    BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
                    Jumlah, Besar, Dibayar,
                    Catatan, NA,
                    LoginBuat, TanggalBuat, Prodi, Fakultas, NamaMhs, Sesi)
                    values
                    ('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
                    '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '".$w['MKKode']." - ".$w['Nama']." - ".$SKS." SKS', '$Nama', '$w1[TrxID]', 
                    '$Jumlah', '$Besar', 0,
                    'Auto', 'N',
                    '$_SESSION[_Login]', now(), '$Prodi[Nama]', '$Fakultas', '$NamaMhs', '$Semester')";
                  $r2 = _query($s2);
              }
              // jika bipot untuk mk praktek pakai query ini
              if ($Jumlah > 0 && $w1['PerSKSPraktek'] == 'Y' && $w['SKSPraktikum'] > 0){
                 $s2 = "insert into bipotmhsw
                    (KodeID, COAID, PMBMhswID, MhswID, TahunID,
                    BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
                    Jumlah, Besar, Dibayar,
                    Catatan, NA,
                    LoginBuat, TanggalBuat, Prodi, Fakultas, NamaMhs, Sesi)
                    values
                    ('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
                    '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '".$w['MKKode']." - ".$w['Nama']." - ".$SKS." SKS', '$Nama', '$w1[TrxID]', 
                    '$Jumlah', '$Besar', 0,
                    'Auto', 'N',
                    '$_SESSION[_Login]', now(), '$Prodi[Nama]', '$Fakultas', '$NamaMhs', '$Semester')";
                  $r2 = _query($s2);
              }
        }
       }
    }
  }
  
  
  // Ambil BIPOT Biaya Praktek Kerja
  $s = "select k.MKKode, k.Nama, k.SKS, j.BiayaKhusus, j.Biaya, j.NamaBiaya, j.AdaResponsi
      from krs k 
        left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
        left outer join mk mk on mk.MKID=k.MKID and mk.KodeID='".KodeID."'
      where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and mk.PraktekKerja='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {   $s1 = "select * 
     from bipot2 
    where BIPOTID = '$mhsw[BIPOTID]'
      and Otomatis = 'Y'
      and (PraktekKerja = 'Y')
      and NA = 'N'
    order by TrxID, Prioritas";
    $r1 = _query($s1);
    while ($w1 = _fetch_array($r1)) 
    { 
    $MsgList[] = '-----------------------------------------------------------------';
    $MsgList[] = "Memproses $w1[BIPOT2ID], Rp. $w1[Jumlah]";
      
    $oke = true;
    
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w1['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status awalnya ($w1[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
    
    // Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w1['StatusMhswID'], ".".$khs['StatusMhswID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status mahasiswanya ($w1[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
    
    // Apakah grade-nya?
    if ($oke) {
      if ($w1['GunakanGradeNilai'] == 'Y') {
      $pos = strpos($w1['GradeNilai'], ".".$mhsw['GradeNilai'].".");
      $oke = $oke && !($pos === false);
      $MsgList[] = "Gunakan Grade Nilai? $oke";
      }
    }

    // Apakah Sudah Pernah Membayar Semester Sebelumnya
    if ($oke) {
      
      $_Dibayar = GetaField('bipotmhsw', "MhswID = '$MhswID' and TahunID!='$khs[TahunID]' and BIPOTNamaID='$w1[BIPOTNamaID]' and KodeID", KodeID, 'Dibayar');
      if($_Dibayar > 0) $oke = false;
      else $oke = true;
      
      $MsgList[] = "Apakah sudah pernah melakukan pembayaran Uang TA/Skripsi ($_Dibayar)? $oke";
    }
    
    // Apakah Jumlah SKS Tahun lalu mencukupi?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      $_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
      if($_SKS > $khslalu[SKS]) $oke = false;
      else $oke = true;
      
      $MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
      }
    }
    
    // Apakah Grade IPK-nya OK?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      if(!empty($khslalu))
      {   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
        $pos = strpos($w1['GradeIPK'], ".".$_GradeIPK.".");
        $oke = $oke && !($pos === false);
        $MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w1[GradeIPK])? $oke";
      }
      else
      { $oke = false;
      }   
      }
    }
    
    // Apakah dimulai pada sesi ini?
    if ($oke) {
      if ($w1['MulaiSesi'] <= $khs['Sesi'] or $w1['MulaiSesi'] == 0) $oke = true;
      else $oke = false;
      $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w1[MulaiSesi])? $oke";
    }
    
    // Apakah ada setup berapa kali ambil?
    if ($oke && $w1['KaliSesi'] > 0) {
      $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and BIPOTNamaID='$w1[BIPOTNamaID]' and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS' and KodeID",
      KodeID, "count(BIPOTMhswID)")+0;
      $oke = $_kali < $w1['KaliSesi'];
      $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w1[KaliSesi])? $oke";
    }
    
    if($oke) $MsgList[] = "ALL OK! GO FOR IT!";
    
    // Simpan data
    if ($oke) {
     
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
        and NA = 'N'
        and PMBMhswID = 1
        and TahunID='$khs[TahunID]'
        and BIPOTNamaID = '$w1[BIPOTNamaID]'
        and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS'
        and BIPOT2ID",
        $w1['BIPOT2ID'], "BIPOTMhswID") +0;
      
      if ($ada == 0) {
        // Simpan
        $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w1['BIPOTNamaID'], 'Nama');
        if($w1['PerSKS'] == 'Y') $Jumlah = $w['SKS'];
        else $Jumlah = 1;
        $Besar = $w1['Jumlah'];
        
        $s2 = "insert into bipotmhsw
        (KodeID, COAID, PMBMhswID, MhswID, TahunID,
        BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
        Jumlah, Besar, Dibayar,
        Catatan, NA,
        LoginBuat, TanggalBuat)
        values
        ('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
        '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '".$w['MKKode']." - ".$w['Nama']." - ".$w['SKS']." SKS', '$Nama', '$w1[TrxID]', 
        '$Jumlah', '$Besar', 0,
        'Auto', 'N',
        '$_SESSION[_Login]', now())";
        $r2 = _query($s2);
        }
       }
    }
  }
  
  // Ambil BIPOT Biaya Skripsi
  $s = "select k.MKKode, k.Nama, k.SKS, j.BiayaKhusus, j.Biaya, j.NamaBiaya, j.AdaResponsi
      from krs k 
        left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
        left outer join mk mk on mk.MKID=k.MKID and mk.KodeID='".KodeID."'
      where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and mk.TugasAkhir='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {   $s1 = "select * 
     from bipot2 
    where BIPOTID = '$mhsw[BIPOTID]'
      and Otomatis = 'Y'
      and (PerSkripsi = 'Y')
      and NA = 'N'
    order by TrxID, Prioritas";
    $r1 = _query($s1);
    while ($w1 = _fetch_array($r1)) 
    { 
    $MsgList[] = '-----------------------------------------------------------------';
    $MsgList[] = "Memproses $w1[BIPOT2ID], Rp. $w1[Jumlah]";
      
    $oke = true;
    
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w1['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status awalnya ($w1[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
    
    // Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w1['StatusMhswID'], ".".$khs['StatusMhswID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status mahasiswanya ($w1[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
    
    // Apakah grade-nya?
    if ($oke) {
      if ($w1['GunakanGradeNilai'] == 'Y') {
      $pos = strpos($w1['GradeNilai'], ".".$mhsw['GradeNilai'].".");
      $oke = $oke && !($pos === false);
      $MsgList[] = "Gunakan Grade Nilai? $oke";
      }
    }
    
    // Apakah Jumlah SKS Tahun lalu mencukupi?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      $_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
      if($_SKS > $khslalu[SKS]) $oke = false;
      else $oke = true;
      
      $MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
      }
    }

    // Apakah Sudah Pernah Membayar Semester Sebelumnya
    if ($oke) {
      
      $_Dibayar = GetaField('bipotmhsw', "MhswID = '$MhswID' and TahunID!='$khs[TahunID]' and BIPOTNamaID='$w1[BIPOTNamaID]' and KodeID", KodeID, 'Dibayar');
      if($_Dibayar > 0) $oke = false;
      else $oke = true;
      
      $MsgList[] = "Apakah sudah pernah melakukan pembayaran Uang TA/Skripsi ($_Dibayar)? $oke";
    }
    
    // Apakah Grade IPK-nya OK?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      if(!empty($khslalu))
      {   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
        $pos = strpos($w1['GradeIPK'], ".".$_GradeIPK.".");
        $oke = $oke && !($pos === false);
        $MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w1[GradeIPK])? $oke";
      }
      else
      { $oke = false;
      }   
      }
    }
    
    // Apakah dimulai pada sesi ini?
    if ($oke) {
      if ($w1['MulaiSesi'] <= $khs['Sesi'] or $w1['MulaiSesi'] == 0) $oke = true;
      else $oke = false;
      $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w1[MulaiSesi])? $oke";
    }
    
    // Apakah ada setup berapa kali ambil?
    if ($oke && $w1['KaliSesi'] > 0) {
      $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and BIPOTNamaID='$w1[BIPOTNamaID]' and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS' and KodeID",
      KodeID, "count(BIPOTMhswID)")+0;
      $oke = $_kali < $w1['KaliSesi'];
      $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w1[KaliSesi])? $oke";
    }
    
    if($oke) $MsgList[] = "ALL OK! GO FOR IT!";
    
    // Simpan data
    if ($oke) {
     
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
        and NA = 'N'
        and PMBMhswID = 1
        and TahunID='$khs[TahunID]'
        and BIPOTNamaID = '$w1[BIPOTNamaID]'
        and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS'
        and BIPOT2ID",
        $w1['BIPOT2ID'], "BIPOTMhswID") +0;
      
      if ($ada == 0) {
        // Simpan
        $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w1['BIPOTNamaID'], 'Nama');
        if($w1['PerSKS'] == 'Y') $Jumlah = $w['SKS'];
        else $Jumlah = 1;
        $Besar = $w1['Jumlah'];
        
        $s2 = "insert into bipotmhsw
        (KodeID, COAID, PMBMhswID, MhswID, TahunID,
        BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
        Jumlah, Besar, Dibayar,
        Catatan, NA,
        LoginBuat, TanggalBuat)
        values
        ('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
        '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '".$w['MKKode']." - ".$w['Nama']." - ".$w['SKS']." SKS', '$Nama', '$w1[TrxID]', 
        '$Jumlah', '$Besar', 0,
        'Auto', 'N',
        '$_SESSION[_Login]', now())";
        $r2 = _query($s2);
        }
       }
    }
  }
  
  // Masukkan Biaya Khusus dari tiap mata kuliah (termasuk biaya khusus mata kuliah praktek kerja - bila ada)
  $s = "select k.MKKode, k.Nama, k.SKS, j.BiayaKhusus, j.Biaya, j.NamaBiaya from krs k left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
      where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and j.BiayaKhusus='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))        
  { $ada = GetaField('bipotmhsw',
  "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
  and NA = 'N'
  and PMBMhswID = 1
  and TahunID='$khs[TahunID]' 
  and Nama='$w[NamaBiaya]'
  and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS'
  and BIPOT2ID",
  0, "BIPOTMhswID") +0;
  
  if ($ada == 0) {
    // Simpan
    
    $s2 = "insert into bipotmhsw
    (KodeID, COAID, PMBMhswID, MhswID, TahunID,
    BIPOT2ID, BIPOTNamaID, Nama, TambahanNama, TrxID, 
    Jumlah, Besar, Dibayar,
    Catatan, NA,
    LoginBuat, TanggalBuat)
    values
    ('".KodeID."', '', 1, '$mhsw[MhswID]', '$khs[TahunID]',
    0, 0, '$w[NamaBiaya]', '$w[MKKode] - $w[Nama] - $w[SKS] SKS', 1, 
    1, '$w[Biaya]', 0,
    'Biaya Khusus', 'N',
    '$_SESSION[_Login]', now())";
    $r2 = _query($s2);
  }
  }
  
  // Ambil BIPOT Remedial
  $s = "select k.MKKode, k.Nama, k.SKS
      from krsremedial k 
      where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {   $MsgList[] = '-----------------------------------------------------------------';
    $MsgList[] = '---------------------------REMEDIAL---------------------------';
    $s1 = "select * 
     from bipot2 
    where BIPOTID = '$mhsw[BIPOTID]'
      and Otomatis = 'Y'
      and Remedial = 'Y'
      and NA = 'N'
    order by TrxID, Prioritas";
    $r1 = _query($s1);
    while ($w1 = _fetch_array($r1)) 
    { 
    $MsgList[] = '-----------------------------------------------------------------';
    $MsgList[] = "Memproses $w1[BIPOT2ID] - $w[MKKode] - $w[Nama], Rp. $w1[Jumlah]";
      
    $oke = true;
    
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w1['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status awalnya ($w1[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
    
    // Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w1['StatusMhswID'], ".".$khs['StatusMhswID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status mahasiswanya ($w1[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
    
    // Apakah grade-nya?
    if ($oke) {
      if ($w1['GunakanGradeNilai'] == 'Y') {
      $pos = strpos($w1['GradeNilai'], ".".$mhsw['GradeNilai'].".");
      $oke = $oke && !($pos === false);
      $MsgList[] = "Gunakan Grade Nilai? $oke";
      }
    }
    
    // Apakah Jumlah SKS Tahun lalu mencukupi?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      $_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
      if($_SKS > $khslalu[SKS]) $oke = false;
      else $oke = true;
      
      $MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
      }
    }
    
    // Apakah Grade IPK-nya OK?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      if(!empty($khslalu))
      {   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
        $pos = strpos($w1['GradeIPK'], ".".$_GradeIPK.".");
        $oke = $oke && !($pos === false);
        $MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w1[GradeIPK])? $oke";
      }
      else
      { $oke = false;
      }   
      }
    }
    
    // Apakah dimulai pada sesi ini?
    if ($oke) {
      if ($w1['MulaiSesi'] <= $khs['Sesi'] or $w1['MulaiSesi'] == 0) $oke = true;
      else $oke = false;
      $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w1[MulaiSesi])? $oke";
    }
    
    // Apakah ada setup berapa kali ambil?
    if ($oke && $w1['KaliSesi'] > 0) {
      $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and BIPOTNamaID='$w1[BIPOTNamaID]' and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS' and KodeID",
      KodeID, "count(BIPOTMhswID)")+0;
      $oke = $_kali < $w1['KaliSesi'];
      $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w1[KaliSesi])? $oke";
    }
    
    if($oke) $MsgList[] = "ALL OK! GO FOR IT!";

    // Simpan data
    if ($oke) {
     
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
        and NA = 'N'
        and PMBMhswID = 1
        and TahunID='$khs[TahunID]'
        and BIPOTNamaID = '$w1[BIPOTNamaID]'
        and TambahanNama='Remedial: $w[MKKode] - $w[Nama] - $w[SKS] SKS'
        and BIPOT2ID",
        $w1['BIPOT2ID'], "BIPOTMhswID") +0;
      
      if ($ada == 0) {
        // Simpan
        $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w1['BIPOTNamaID'], 'Nama');
        if($w1['PerSKS'] == 'Y') $Jumlah = $w['SKS'];
        else $Jumlah = 1;
        $Besar = $w1['Jumlah'];
        
        $s2 = "insert into bipotmhsw
        (KodeID, COAID, PMBMhswID, MhswID, TahunID,
        BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
        Jumlah, Besar, Dibayar,
        Catatan, NA,
        LoginBuat, TanggalBuat)
        values
        ('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
        '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', 'Remedial: ".$w['MKKode']." - ".$w['Nama']." - ".$w['SKS']." SKS', '$Nama', '$w1[TrxID]', 
        '$Jumlah', '$Besar', 0,
        'Auto', 'N',
        '$_SESSION[_Login]', now())";
        $r2 = _query($s2);
        }
       }
    }
  }
  
  // Masukkan Biaya Khusus dari tiap mata kuliah remedial
  $s = "select k.MKKode, k.Nama, k.SKS, j.BiayaKhusus, j.Biaya, j.NamaBiaya 
      from krsremedial k left outer join jadwalremedial j on k.JadwalRemedialID=j.JadwalRemedialID and j.KodeID='".KodeID."'
      where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and j.BiayaKhusus='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))        
  { $ada = GetaField('bipotmhsw',
  "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
  and NA = 'N'
  and PMBMhswID = 1
  and TahunID='$khs[TahunID]' 
  and Nama='$w[NamaBiaya]'
  and TambahanNama='Remedial: $w[MKKode] - $w[Nama] - $w[SKS] SKS'
  and BIPOT2ID",
  0, "BIPOTMhswID") +0;
  
  if ($ada == 0) {
    // Simpan
    
    $s2 = "insert into bipotmhsw
    (KodeID, COAID, PMBMhswID, MhswID, TahunID,
    BIPOT2ID, BIPOTNamaID, Nama, TambahanNama, TrxID, 
    Jumlah, Besar, Dibayar,
    Catatan, NA,
    LoginBuat, TanggalBuat)
    values
    ('".KodeID."', '', 1, '$mhsw[MhswID]', '$khs[TahunID]',
    0, 0, '$w[NamaBiaya]', 'Remedial: $w[MKKode] - $w[Nama] - $w[SKS] SKS', 1, 
    1, '$w[Biaya]', 0,
    'Biaya Khusus', 'N',
    '$_SESSION[_Login]', now())";
    $r2 = _query($s2);
  }
  }
  // Uncomment lines below to print debugging messages
  /*echo "COUNT: ".count($MsgList);
  if(!empty($MsgList))
  { foreach($MsgList as $Msg)
    { echo "$Msg<br>";
    }
  }*/
  
  // H i T U NG   D E N D A
  //===========================================================================================
  //HitungDenda($MhswID, $TahunID); 
    HitungUlangBIPOTMhsw($MhswID, $TahunID);
  //===========================================================================================
  // Buat tagihan bank
  BuatTagihanBank($MhswID,$TahunID,$Semester);
}
function CekBIPOT($mhswid, $thn) {
$ProdiID = GetaField('mhsw', "MhswID", $mhswid, "ProdiID");
$Prg = GetaField('mhsw', "MhswID", $mhswid, "ProgramID");
$TahunAktif = GetaField('tahun', "ProdiID='$ProdiID' AND ProgramID='".$Prg."' AND NA", 'N', "TahunID");
$BIPOT = GetaField('bipotmhsw', "MhswID='".$mhswid."' AND TahunID", $tahun, "BIPOTMhswID");
  if (empty($BIPOT) && $TahunAktif==$thn) {
    SetBIPOTMhsw($mhswid, $thn, $ProdiID);
    ProsesBIPOT($mhswid, $thn);
  } 
}
function SetBIPOTMhsw($mhswid, $thn,$ProdiID) {
$ProgramID  = GetaField('mhsw', "MhswID", $mhswid, "ProgramID");
$TahunMhsw  = GetaField('mhsw', "MhswID", $mhswid, "TahunID");
$NamaBipot  = substr($thn, -1).'-'.$ProgramID.'-'.substr($TahunMhsw,-2);
$bipot    = GetaField('bipot', "Tahun = '".$NamaBipot."' AND NA='N' AND ProgramID='".$ProgramID."' AND ProdiID", $ProdiID, 'BIPOTID');
//if ($_SESSION['_Login']=='auth0rized') die ($NamaBipot);
// UPDATE KHS
$s      = "UPDATE khs set BIPOTID='".$bipot."' where MhswID='".$mhswid."' AND TahunID='".$thn."'";
$r      = _query($s);
// UPDATE MHSW
$s      = "UPDATE mhsw set BIPOTID='".$bipot."' where MhswID='".$mhswid."'";
$r      = _query($s);
}

function HitungUlangBIPOTMhsw($MhswID, $TahunID) {
  // Hitung Total BIPOT & Pembayaran
  $biaya = GetaField("bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID = b2.BIPOT2ID",
      "bm.PMBMhswID = 1 and bm.KodeID = '".KodeID."'
      and bm.NA = 'N'
      and bm.TrxID = 1
      and bm.TahunID = '$TahunID' and bm.MhswID", $MhswID,
      "sum(bm.Jumlah * bm.Besar)")+0;
  if (!empty($biaya)) { $up = _query("update khs set KonfirmasiAktif='Y' where KodeID = '".KodeID."'
      and MhswID = '$MhswID' 
      and TahunID = '$TahunID'
    ");
  }
  $potongan = GetaField("bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID = b2.BIPOT2ID",
      "bm.PMBMhswID = 1 and bm.KodeID = '".KodeID."'
      and bm.NA = 'N'
      and bm.TrxID = -1
      and bm.TahunID = '$TahunID' and bm.MhswID", $MhswID,
      "sum(bm.Jumlah * bm.Besar)")+0;
  $bayar = GetaField('bayarmhsw',
      "PMBMhswID = 1 and KodeID = '".KodeID."'
      and NA = 'N'
      and TrxID = 1
      and TahunID = '$TahunID' and MhswID", $MhswID,
      "sum(Jumlah)")+0;
  $tarik = GetaField('bayarmhsw',
      "PMBMhswID = 1 and KodeID = '".KodeID."'
      and NA = 'N'
      and TrxID = -1
      and TahunID = '$TahunID' and MhswID", $MhswID,
      "sum(Jumlah)")+0;
  // Update data PMB
  $s = "update khs
    set Biaya = $biaya, Potongan = $potongan,
        Bayar = $bayar, Tarik = $tarik
    where KodeID = '".KodeID."'
      and MhswID = '$MhswID' 
      and TahunID = '$TahunID'
    ";
  $r = _query($s);
  $jml = $biaya - $bayar + $tarik - $potongan;
  return $jml;
}
function HitungDenda($MhswID,$TahunID) {
  $Denda = 100000; 
  $s  = "SELECT MhswID from bipotmhsw2 where flag=0 and MhswID='$MhswID' AND TahunID='$TahunID' group by MhswID";
  $r  = _query($s);
  while ($w = _fetch_array($r)) {
    $ada = GetaField('bipotmhsw2', "MhswID", $w['MhswID'], "MhswID");
    $MhswID = $w['MhswID'];
    if (empty($ada)) {
    // Ambil data
      $data = GetFields('mhsw m left outer join prodi p on p.ProdiID=m.ProdiID left outer join fakultas f on f.FakultasID=p.FakultasID', "MhswID", $MhswID, "f.Nama as 
                fakultas, p.Nama as jurusan, m.Nama as mhs");
      $Semester = GetaField('khs', "TahunID = '$_SESSION[TahunID]' AND MhswID", $MhswID, "Sesi");

      // Cek, apakah sudah ada BIPOT nya
      $adakah = GetaField("bipotmhsw", "TahunID='$TahunID' AND BIPOTNamaID='14' AND MhswID", $MhswID, "BIPOTMhswID");

        // Jika sudah ada cek dulu apakah sudah bayar
        if (!empty($adakah)) {
          echo "BIPOT ditemukan! $MhswID<br>";
          $Dibayarnya = GetaField("bipotmhsw", "TahunID='$TahunID' AND BIPOTNamaID='14' AND MhswID", $MhswID, "Dibayar");
          
          // Jika belum dibayar, buat denda baru jika ada
          if ($Dibayar == 0) {
            echo "Belum Bayar! <br>";
            $run = _query("UPDATE bipotmhsw set Besar='$Denda' where BIPOTMhswID='$adakah' AND MhswID='$MhswID'");
            echo "Diupdate Pembayaran $MhswID menjadi Rp". number_format($Denda). ".<br>";
          }
        }
        else { 
        echo "BIPOT Tidak ditemukan! $MhswID<br>";
        // Buat BIPOT 
        $t = _query("INSERT INTO bipotmhsw(KodeID, COAID, PMBMhswID, MhswID, TahunID,
            BIPOT2ID, BIPOTNamaID, Nama, TambahanNama, TrxID, 
            Jumlah, Besar, Dibayar,
            Catatan, NA,
            LoginBuat, TanggalBuat)
            values
            ('".KodeID."', '', 1, '$MhswID', '$_SESSION[TahunID]',
            0, 14, 'Denda', '',1, 
            1, '$Denda', 0,
            'auto', 'N',
            '$_SESSION[_Login]', now())");
            echo "BIPOT sudah dibuat untuk $MhswID, Denda sebesar Rp". number_format($Denda). "<br>";
        }
        
    }
    
  }
// BerhasilSimpan('?mnux=mhsw/mhswnilai',10);
        
}
?>
