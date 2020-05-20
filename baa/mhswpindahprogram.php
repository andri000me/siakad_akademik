<?php
// Author : Emanuel Setio Dewo
// Start  : 21/11/2008
// Email  : setiodewo@gmail.com


// *** Parameters ***
$crMhswID = GetSetVar('crMhswID');
$crNamaMhsw = GetSetVar('crNamaMhsw');

// *** Main ***
TampilkanJudul("Mahasiswa Pindah Prodi");
$gos = (empty($_REQUEST['gos']))? 'CariMhsw' : $_REQUEST['gos'];
$gos();


// *** Functions ***
function HeaderCariMhsw() {
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=800>
  <form name='frmCariMhsw' action='?' method=POST>
  <tr><td class=inp width=100>NIM/NPM:</td>
      <td class=ul>
        <input type=text name='crMhswID' value='$_SESSION[crMhswID]' size=20 maxlength=50 />
      </td>
      <td class=inp width=100>Nama Mhsw:</td>
      <td class=ul nowrap>
        <input type=text name='crNamaMhsw' value='$_SESSION[crNamaMhsw]' size=30 maxlength=50 /> 
        <input type=submit name='btnCari' value='Cari Mhsw' />
        <input type=button name='btnReset' value='Reset' onClick="location='?mnux=$_SESSION[mnux]&gos=&crMhswID=&crNamaMhsw='" />
      </td>
  </tr>
  </form>
  </table>
ESD;
}
function CariMhsw() {
  HeaderCariMhsw();
  TampilkanFotoScript();
  // setup where-statement
  $whr = array();
  if ($_SESSION['crMhswID'] != '') $whr[] = "and m.MhswID like '$_SESSION[crMhswID]%' ";
  if ($_SESSION['crNamaMhsw'] != '') $whr[] = "and m.Nama like '%$_SESSION[crNamaMhsw]%' ";
  $strwhr = implode("\n", $whr);
  
  // Tampilkan
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['mhswpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&mhswpage==PAGE='>=PAGE=</a>";

  $lst->tables = "mhsw m
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join statusawal sa on m.StatusAwalID=sa.StatusAwalID
    where m.MhswID is not NULL
    $strwhr $ord";
  $lst->fields = "m.MhswID, m.Nama, m.StatusAwalID, m.StatusMhswID,
    m.Kelamin,
    m.Telepon, m.Handphone, m.Email, 
    if (m.Foto is NULL or m.Foto = '', 'img/tux001.jpg', m.Foto) as _Foto,
    if (m.StatusAwalID = 'D', concat('<a href=\'?mnux=$_SESSION[mnux]&gos=fnKonversi&MhswID=', m.MhswID,'\'><img src=img/edit.png /></a>'), '&times;') as _Konversi,
    m.ProgramID, m.ProdiID, m.Alamat, m.Kota,
    prd.Nama as PRD, sm.Nama as SM, sm.Keluar, sa.Nama as SA";
  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=800>
    <tr><th class=ttl>No.</th>
    <th class=ttl>NPM<hr size=1 color=silver />Pindahkan</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Status<hr size=1 color=silver />Masuk</th>
    <th class=ttl>Telp/HP</th>
    <th class=ttl width=20>Konversi<br />MK</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp width=10><a name='=MhswID='>=NOMER=</a></td>
    <td class=cna=Keluar= width=100>
      <a href='?mnux=$_SESSION[mnux]&gos=fnDropin&MhswID==MhswID='><img src='img/edit.png'>
      =MhswID=</a>
      <img src='img/=Kelamin=.bmp' align=right />
      </td>
    <td class=cna=Keluar= nowrap>
      <b>=Nama=</b>
      <a href='#' onClick=\"javascript:TampilkanFoto('=MhswID=', '=Nama=', '=_Foto=')\" title='=_Foto='>
      <img src='=_Foto=' width=30 align=right /></a>
      </td>
    <td class=cna=Keluar=>=ProgramID=&nbsp;
      <hr size=1 color=silver />
      =PRD=&nbsp;</td>
    <td class=cna=Keluar= width=60 align=center>
      =SM=
      <hr size=1 color=silver />
      =SA=
      </td>
    <td class=cna=Keluar=>=Telepon=&nbsp;
      <hr size=1 color=silver />
      =Handphone=&nbsp;</td>
    <td class=cna=Keluar= align=center>=_Konversi=</td>
    </tr>
    <tr><td bgcolor=silver colspan=7 height=1></td></tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
}
function TampilkanFotoScript() {
  echo <<<SCR
  <script>
  function TampilkanFoto(MhswID, Nama, Foto) {
    jQuery.facebox("<font size=+1>"+Nama+"</font> <sup>(" + MhswID + ")</sup><hr size=1 color=silver /><img src='"+Foto+"' />");
  }
  </script>
SCR;
}
function fnDropin() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $mhsw = GetFields('mhsw', "MhswID = '$MhswID' and KodeID", KodeID, '*');
  $prd = GetaField('prodi', "ProdiID = '$mhsw[ProdiID]' and KodeID", KodeID, "Nama");
  $prg = GetaField('program', "ProgramID = '$mhsw[ProgramID]' and KodeID", KodeID, "Nama");
  $_mk = GetFields('krs', 'MhswID', $MhswID, "count(KRSID) as _JmlMK, sum(SKS) as _JmlSKS");
  $_smt = GetaField('khs', 'MhswID', $MhswID, "max(Sesi)")+0;
  $sta = GetFields('statusmhsw', 'StatusMhswID', $mhsw['StatusMhswID'], 'Nama, Keluar');
  // Cek apakah mhsw sudah keluar?
  if ($sta['Keluar'] == 'Y')
    die(ErrorMsg('Error',
      "Mahasiswa $mhsw[Nama] <sup>($MhswID)</sup> sudah keluar dengan status: $sta[Nama].<br />
      Mahasiswa tidak dapat pindah prodi lagin.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
  
  // Penasehat Akademik
  $stawal = GetaField('statusawal', 'StatusAwalID', $mhsw['StatusAwalID'], 'Nama');
  if (empty($mhsw['PenasehatAkademik'])) {
    $pa = "&times; Belum diset";
  }
  else {
    $dsn = GetFields('dosen', "Login='$mhsw[PenasehatAkademik]' and KodeID", KodeID, "Nama, Gelar");
    $pa = "$dsn[Nama] <sup>$dsn[Gelar]</sup>";
  }
  $rowspan = 20;
  
  $_JmlSKS = $_mk['_JmlSKS']+0;
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', '', "KodeID='".KodeID."'", 'ProdiID');
  $optprogram = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', '', "KodeID='".KodeID."'", 'ProgramID');
  CheckFormScript('ProdiID, ProgramID');
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=700>
  <tr><td class=wrn width=1 rowspan=$rowspan></td>
      <td class=ul align=center colspan=4>
        Anda akan memindahkan mahasiswa berikut ini ke prodi lain.<br />
        Mohon untuk dicek detailnya sebelum melakukan proses.
      </td>
      <td class=wrn width=1 rowspan=$rowspan></td>
      </tr>
  <tr><td class=inp>NIM/NPM:</td>
      <td class=ul><b>$mhsw[MhswID]</td>
      <td class=inp>Nama Mhsw:</td>
      <td class=ul><b>$mhsw[Nama]&nbsp;</td>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul>$prd <sup>$prg</sup></td>
      <td class=inp>Penasehat Akd:</td>
      <td class=ul>$pa</td>
      </tr>
  <tr><td class=inp>Total MK:</td>
      <td class=ul>$_mk[_JmlMK] MK, Total SKS: $_JmlSKS, Semester: $_smt</td>
      <td class=inp>Status Mhsw:</td>
      <td class=ul>$sta[Nama] <sup>&minus; $stawal</sup></td>
      </tr>
  
  <form action='?' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='fnProses' />
  <input type=hidden name='MhswID' value='$MhswID' />
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=4>Pindah Ke Prodi:</th></tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul><select name='ProdiID'>$optprodi</select></td>
      <td class=inp>Program Pendidikan:</td>
      <td class=ul><select name='ProgramID'>$optprogram</select></td>
      </tr>
  <tr><th class=ul colspan=4 align=center>Catatan:</th></tr>
  <tr><td class=ul colspan=4>
      <img src='img/warn.png' align=right />
      <ol><li>Status mahasiswa akan diset sebagai 'Keluar'.</li>
          <li>Mahasiswa akan dibuatkan NIM/NPM baru di prodi/program baru.</li>
          <li>Status mahasiswa di data baru diset sebagai 'Drop-in'.</li>
          <li>Setelah itu lakukan konversi MK di modul 'Prodi &raquo; Konversi Mhsw Pindahan'.</li>
      </td></tr>
  
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='btnProses' value='Proses Pindah Prodi' />
      <input type=button name='btnBatal' value='Batal Pindah' 
        onClick="location='?mnux=$_SESSION[mnux]&gos='" />
      </td></tr>
  </form>
  </table>
  <p>
ESD;
}
function fnProses() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $ProdiID = sqling($_REQUEST['ProdiID']);
  $ProgramID = sqling($_REQUEST['ProgramID']);
  
  $mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, '*');
  // Cek apakah prodi-nya sama?
  if ($ProdiID == $mhsw['ProdiID'])
    die(ErrorMsg('Error',
      "Anda tidak bisa memindahkan Mhsw: <b>$mhsw[nama]</b> <sup>($MhswID)</sup><br />
      ke Prodi yang sama ($ProdiID &raquo; $mhsw[ProdiID]).<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=fnDropin&MhswID=$MhswID'\" />
      <input type=button name='btnBatal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
  // Konfirmasi sekali lagi
  $sta = GetaField('statusmhsw', 'StatusMhswID', $mhsw['StatusMhswID'], 'Nama');
  $stawal = GetaField('statusawal', 'StatusAwalID', $mhsw['StatusAwalID'], 'Nama');
  $TahunID = GetaField('tahun', "ProgramID='$ProgramID' and ProdiID='$ProdiID' and KodeID",
    KodeID, 'TahunID');
  
  CheckFormScript('TahunID');
  echo Konfirmasi("Konfirmasi",
    "<table class=bsc cellspacing=1 width=100%>
    <form action='?' method=POST onSubmit='return CheckForm(this)'>
    <input type=hidden name='mnux' value='$_SESSION[mnux]' />
    <input type=hidden name='gos' value='fnProsesnya' />
    <input type=hidden name='MhswID' value='$MhswID' />
    <input type=hidden name='ProdiID' value='$ProdiID' />
    <input type=hidden name='OldProdiID' value='$mhsw[ProdiID]' />
    <input type=hidden name='ProgramID' value='$ProgramID' />
    <input type=hidden name='OldProgramID' value='$mhsw[ProgramID]' />
    
    <tr><td class=ul1 colspan=2 align=center>
        Anda akan memproses pemindahan program studi mhsw berikut:
        </td></tr>
    <tr><td class=inp width=100>NIM/NPM:</td>
        <td class=ul1>$MhswID</td>
        </tr>
    <tr><td class=inp>Nama:</td>
        <td class=ul1>$mhsw[Nama]</td>
        </tr>
    <tr><td class=inp>Status:</td>
        <td class=ul1>$sta <sup>$stawal</sup></td>
        </tr>
    <tr><td class=inp>Perpindahan:</td>
        <td class=ul1>
        $mhsw[ProdiID] <sup>$mhsw[ProgramID]</sup> &raquo; $ProdiID <sup>$ProgramID</sup>
        </td></tr>
    <tr><td class=inp>Tahun Akademik:</td>
        <td class=ul1>
        <input type=text name='TahunID' value='$TahunID' size=5 maxlength=5 />
        </td></tr>
    <tr><td class=ul1 colspan=2 align=center>
        <input type=submit name='btnProses' value='Proses' />
        <input type=button name='btnBatal' value='Batal'
          onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />
        </td></tr>
    </form>
    </table>");
}
function fnProsesnya() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $ProdiID = sqling($_REQUEST['ProdiID']);
  $ProgramID = sqling($_REQUEST['ProgramID']);
  $TahunID = sqling($_REQUEST['TahunID']);
  // Cek Tahun
  $ada = GetFields('tahun', "ProdiID='$ProdiID' and ProgramID='$ProgramID' and KodeID",
    KodeID, '*');
  if (empty($ada))
    die(ErrorMsg("Error - $TahunID",
    "Kalendar akademik dengan kode: <b>$TahunID</b> tidak ditemukan<br />
    untuk Program Studi: $ProdiID dan Program Pendidikan: $ProgramID.<br />
    Hubungi Kepala BAA untuk memastikan tahun akademik yang aktif.<br />
    Atau hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='btnKembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos=fnDropin&MhswID=$MhswID'\" />
    <input type=button name='btnBatal' value='Batal'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />")); 
  
  $mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, '*');
  $baru = $mhsw;
  $baru['ProdiID'] = $ProdiID;
  $baru['ProgramID'] = $ProgramID;
  $baru['StatusAwalID'] = 'D';
  $NIM = (GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], 'GunakanNIMSementara')=='Y')?  GetNextNIMSementara($TahunID, $baru) : GetNextNIM($TahunID, $baru);
  $BIPOTID = GetaField('bipot', "ProdiID='$ProdiID' and ProgramID='$ProgramID' and Def='Y' and KodeID",
    KodeID, 'BIPOTID');
  $BatasStudi = HitungBatasStudi($TahunID, $ProdiID);
  // Salin data mhsw
  $s = "insert into mhsw
    (MhswID, Login, LevelID, `Password`,
    KDPIN, PMBID, TahunID, KodeID, BIPOTID,
    Autodebet, Nama, Foto,
    StatusAwalID, StatusMhswID, ProgramID, ProdiID,
    PenasehatAkademik, Kelamin, WargaNegara, Kebangsaan,
    TempatLahir, TanggalLahir, Agama, StatusSipil,
    TinggiBadan, BeratBadan,
    Alamat, Kota, RT, RW, KodePos, Propinsi, Negara, Telephone, Handphone, Email,
    AlamatAsal, KotaAsal, RTAsal, RWAsal, KodePosAsal, PropinsiAsal, NegaraAsal, TeleponAsal,
    AnakKe, JumlahSaudara,
    NamaAyah, AgamaAyah, PendidikanAyah, PekerjaanAyah, HidupAyah,
    NamaIbu, AgamaIbu, PendidikanIbu, PekerjaanIbu, HidupIbu,
    AlamatOrtu, KotaOrtu, RTOrtu, RWOrtu, KodePosOrtu, PropinsiOrtu, NegaraOrtu, TeleponOrtu,
    HandphoneOrtu, EmailOrtu,
    PendidikanTerakhir, AsalSekolah, AsalSekolah1, 
    AlamatSekolah, KotaSekolah, JurusanSekolah, NilaiSekolah,
    TahunLulus, IjazahSekolah,
    AsalPT, MhswIDAsalPT, ProdiAsalPT, LulusAsalPT, TglLulusAsalPT, IPKAsalPT,
    BatasStudi, NA,
    NamaBank, NomerRekening,
    LoginBuat, TanggalBuat)
    values
    ('$NIM', '$NIM', 120, LEFT(PASSWORD('$NIM'), 10),
    '$baru[KDPIN]', '$MhswID', '$TahunID', '".KodeID."', '$BIPOTID',
    '$baru[Autodebet]', '$baru[Nama]', '$mhsw[Foto]',
    '$baru[StatusAwalID]', '$baru[StatusMhswID]', '$ProgramID', '$ProdiID',
    '$baru[PenasehatAkademik]', '$baru[Kelamin]', '$baru[WargaNegara]', '$baru[Kebangsaan]',
    '$baru[TempatLahir]', '$baru[TanggalLahir]', '$baru[Agama]', '$baru[StatusSipil]',
    '$baru[TinggiBadan]', '$baru[BeratBadan]',
    '$baru[Alamat]', '$baru[Kota]', '$baru[RT]', '$baru[RW]', '$baru[KodePos]', '$baru[Propinsi]', '$baru[Negara]', '$baru[Telephone]', '$baru[Handphone]', '$baru[Email]',
    '$baru[AlamatAsal]', '$baru[KotaAsal]', '$baru[RTAsal]', '$baru[RWAsal]', '$baru[KodePosAsal]', '$baru[PropinsiAsal]', '$baru[NegaraAsal]', '$baru[TeleponAsal]',
    '$baru[AnakKe]', '$baru[JumlahSaudara]',
    '$baru[NamaAyah]', '$baru[AgamaAyah]', '$baru[PendidikanAyah]', '$baru[PekerjaanAyah]', '$baru[HidupAyah]',
    '$baru[NamaIbu]', '$baru[AgamaIbu]', '$baru[PendidikanIbu]', '$baru[PekerjaanIbu]', '$baru[HidupIbu]',
    '$baru[AlamatOrtu]', '$baru[KotaOrtu]', '$baru[RTOrtu]', '$baru[RWOrtu]', '$baru[KodePosOrtu]', '$baru[PropinsiOrtu]', '$baru[NegaraOrtu]', '$baru[TeleponOrtu]',
    '$baru[HandphoneOrtu]', '$baru[EmailOrtu]',
    '$baru[PendidikanTerakhir]', '$baru[AsalSekolah]', '$baru[AsalSekolah1]',
    '$baru[AlamatSekolah]', '$baru[KotaSekolah]', '$baru[JurusanSekolah]', '$baru[NilaiSekolah]',
    '$baru[TahunLulus]', '$baru[IjazahSekolah]',
    '$baru[AsalPT]', '$baru[MhswIDAsalPT]', '$baru[ProdiAsalPT]', '$baru[LulusAsalPT]', '$baru[TglLulusAsalPT]', '$baru[IPKAsalPT]',
    '$BatasStudi', 'N',
    '$baru[NamaBank]', '$baru[NomerBank]',
    '$_SESSION[_Login]', now())";
  $r = _query($s);
  // Non aktifkan data mhsw lama --> status: Keluarkan
  $sk = "update mhsw 
    set StatusMhswID = 'D', 
        Keluar = 'Y', TahunKeluar = '$TahunID',
        CatatanKeluar = 'Pindah Prodi ke: $ProdiID, Program: $ProgramID'
    where MhswID = '$MhswID' and KodeID = '".KodeID."' ";
  $rk = _query($sk);
  // Kembali
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=&crNamaMhsw=$baru[Nama]", 1);
}
function fnKonversi() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, '*');
  
  TampilkanHeaderMhsw($mhsw);
}
function TampilkanHeaderMhsw($mhsw) {
  $Prodi = GetaField('prodi', "ProdiID='$mhsw[ProdiID]' and KodeID", KodeID, 'Nama');
  $Program = GetaField('program', "ProgramID='$mhsw[ProgramID]' and KodeID", KodeID, 'Nama');
  $sta = GetaField('statusmhsw', 'StatusMhswID', $mhsw['StatusMhswID'], 'Nama');
  $stawal = GetaField('statusawal', 'StatusAwalID', $mhsw['StatusAwalID'], 'Nama');
  $h = 500;
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=800>
  <tr><td class=inp width=80>NIM/NPM:</td>
      <td class=ul>$mhsw[MhswID]</td>
      <td class=inp width=80>Nama Mhsw:</td>
      <td class=ul>$mhsw[Nama]</td>
      <td class=inp width=80>Status:</td>
      <td class=ul>$sta <sup>$stawal</sup>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul>$Prodi</td>
      <td class=inp>Program:</td>
      <td class=ul>$Program</td>
      <td class=ul colspan=2>
      <input type=button name='btnKembali' value='Kembali'
        onClick="location='?mnux=$_SESSION[mnux]&gos='" />
      <input type=button name='btnRefresh' value='Refresh'
        onClick="location='?mnux=$_SESSION[mnux]&gos=fnKonversi&MhswID=$mhsw[MhswID]'" />
      </td></tr>
  </table>
  <table class=box cellspacing=1 align=center width=800>
  <tr><td class=ul width=50%>
      <iframe id='FRAMEDETAIL1' src="$_SESSION[mnux].krs.php?MhswID=$mhsw[MhswID]" frameborder=0 width=100% height=$h>
      </iframe>
      </td>
      <td class=ul width=50%>
      <iframe id='FRAMEDETAIL2' src="$_SESSION[mnux].oldkrs.php?MhswID=$mhsw[MhswID]" frameborder=0 width=100% height=$h>
      </iframe>
      </td>
      </tr>
  </table>
ESD;
}
?>
