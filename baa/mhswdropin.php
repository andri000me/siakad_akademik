<?php


// *** Parameters ***
$crMhswID = GetSetVar('crMhswID');
$crNamaMhsw = GetSetVar('crNamaMhsw');
$_mhswdropinPage = GetSetVar('_mhswdropinPage');

// *** Main ***
TampilkanJudul("Mahasiswa Pindah Prodi / Program");
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
  if ($_SESSION['crMhswID'] != '') $whr[] = "and m.MhswID like '$_SESSION[crMhswID]%'";
  if ($_SESSION['crNamaMhsw'] != '') $whr[] = "and m.Nama like '%$_SESSION[crNamaMhsw]%'";
  $strwhr = implode("\n", $whr);
  
  // Tampilkan
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['_mhswdropinPage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&_mhswdropinPage==PAGE='>=PAGE=</a>";

  $lst->tables = "mhsw m
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join statusawal sa on m.StatusAwalID=sa.StatusAwalID
    where m.KodeID='".KodeID."'
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
    <td class=cna=Keluar= width=100 align=center>
      =MhswID=
	  <hr size=1 color=silver />
	  <a href='?mnux=$_SESSION[mnux]&gos=fnDropin&MhswID==MhswID='>Prodi</a> &bull;
	  <a href='?mnux=$_SESSION[mnux]&gos=fnDropinProgram&MhswID==MhswID='>Program</a>
      </td>
    <td class=cna=Keluar= nowrap>
      <b>=Nama=</b></br>
	  <img src='img/=Kelamin=.bmp' align=left />
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
        Anda akan memindahkan mahasiswa berikut ini ke <b>prodi</b> lain.<br />
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
  $Tahun = substr($TahunID, 0, 4);
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
  // Edit: Ilham
  $NIM = (GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], 'GunakanNIMSementara')=='Y')? 
	  GetNextNIMSementara($TahunID, $baru) : GetNextNIM($TahunID, $baru);
  $BIPOTID = GetaField('bipot', "ProdiID='$ProdiID' and ProgramID='$ProgramID' and Def='Y' and KodeID",
    KodeID, 'BIPOTID');
  $BatasStudi = HitungBatasStudi($TahunID, $ProdiID);
  // Salin data mhsw
  $s = "insert into mhsw
    (MhswID, Login, LevelID, `Password`,
    KDPIN, PMBID, TahunID, KodeID, BIPOTID, SemesterAwal,
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
    ('$NIM', '$NIM', 120, md5('$NIM'),
    '$baru[KDPIN]', '$MhswID', '$Tahun', '".KodeID."', '$BIPOTID', '$TahunID',
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
  <tr><td class=inp width=80>NPM:</td>
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

function fnDropinProgram() {
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
  
  $buttons = '';
  $programs = "select * from program where KodeID='".KodeID."' and ProgramID!='$mhsw[ProgramID]' and NA='N' order by Nama";
  $programr = _query($programs);
  while($programw = _fetch_array($programr))
	$buttons .= "<input type=button name='$programw[ProgramID]' value='Pindahkan ke Program $programw[Nama]' onClick=\"location='?mnux=$_SESSION[mnux]&gos=fnProsesProgram&MhswID=$MhswID&ProgramID=$programw[ProgramID]'\" />";
  
  CheckFormScript('ProdiID, ProgramID');
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=700>
  <tr><td class=wrn width=1 rowspan=$rowspan></td>
      <td class=ul align=center colspan=4>
        Anda akan memindahkan mahasiswa berikut ini ke <b>program</b> lain.<br />
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
  
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='fnProses' />
  <input type=hidden name='MhswID' value='$MhswID' />
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=ul colspan=4 align=center>
      $buttons
      <input type=button name='btnBatal' value='Batal Pindah' 
        onClick="location='?mnux=$_SESSION[mnux]&gos='" />
      </td></tr>
  </form>
  </table>
  <p>
ESD;
}
function fnProsesProgram() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $ProgramID = sqling($_REQUEST['ProgramID']);
  
  $mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, '*');
  // Cek apakah prodi-nya sama?
  if ($ProgramID == $mhsw['ProgramID'])
    die(ErrorMsg('Error',
      "Anda tidak bisa memindahkan Mhsw: <b>$mhsw[nama]</b> <sup>($MhswID)</sup><br />
      ke Program yang sama ($ProgramID &raquo; $mhsw[ProgramID]).<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=fnDropinProgram&MhswID=$MhswID'\" />
      <input type=button name='btnBatal' value='Batal'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
  // Konfirmasi sekali lagi
  $sta = GetaField('statusmhsw', 'StatusMhswID', $mhsw['StatusMhswID'], 'Nama');
  $stawal = GetaField('statusawal', 'StatusAwalID', $mhsw['StatusAwalID'], 'Nama');
  $TahunID = GetaField('khs', "MhswID='$MhswID' and Sesi=(select max(Sesi) from khs where MhswID='$MhswID' and KodeID='".KodeID."') and KodeID",
    KodeID, 'TahunID');
  
  CheckFormScript('TahunID');
  echo 
    "<table class=bsc cellspacing=1 width=500>
    <form action='?' method=POST onSubmit='return CheckForm(this)'>
    <input type=hidden name='mnux' value='$_SESSION[mnux]' />
    <input type=hidden name='gos' value='fnProsesnyaProgram' />
    <input type=hidden name='MhswID' value='$MhswID' />
	<input type=hidden name='TahunID' value='$TahunID' />
    <input type=hidden name='ProgramID' value='$ProgramID' />
    <input type=hidden name='OldProgramID' value='$mhsw[ProgramID]' />
    
	<tr><th class=ttl colspan=4>Konfirmasi</th></tr>
    <tr><td class=ul1 colspan=4 align=center>
        Anda akan memproses pemindahan program mhsw berikut:
        </td></tr>
    <tr><td class=inp width=100>NIM/NPM:</td>
        <td class=ul1>$MhswID</td>
        <td class=inp width=100>Nama:</td>
        <td class=ul1>$mhsw[Nama]</td>
        </tr>
    <tr><td class=inp>Status:</td>
        <td class=ul1>$sta <sup>$stawal</sup></td>
		<td class=inp>Tahun Akademik:</td>
        <td class=ul1>$TahunID
        <input type=hidden name='TahunID' value='$TahunID'/>
        </td></tr>
    <tr><td class=inp>Perpindahan:</td>
        <td class=ul1>
        $mhsw[ProdiID] <sup>$mhsw[ProgramID] &raquo; $ProgramID</sup>
        </td></tr>
	<tr size=1 bgcolor=silver><td colspan=5></td></tr>
	";

	$s = "select k.KRSID, k.MKID, k.MKKode, k.Nama, k.TahunID, j.HariID, left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS 
			from krs k left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
						left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
			where k.MhswID='$MhswID' and k.Final='N' and jj.Tambahan='N' and k.KodeID='".KodeID."'
			order by k.MKKode";
	$r = _query($s);
	$n = _num_rows($r);
	
	if($n == 0)
	{	echo "<tr><td colspan=5 align=center>Mahasiswa tidak memiliki mata kuliah yang dapat dijadwalkan untuk Program $ProgramID.<br>
				Mahasiswa dapat dipindahkan dan anda dapat menset mata kuliah di penjadwalan kuliah seperti biasa</td></tr>";
	}
	else
	{	$count = 0; 
		echo "<tr><td colspan=5 align=center><font color=red>Catatan: Untuk dapat memproses pemindahan mahasiswa ini, SETIAP penjadwalan kuliah yang BELUM di-finalisasi harus memiliki jadwal di program yang baru.</td></tr>";
		while($w = _fetch_array($r))
		{	$count++; 
			echo "<tr><td colspan=2>$w[Nama]<font size=1 color=teal>($w[MKKode])</font></br>
									<div align=right><font size=1 color=gray>".UbahKeHariIndonesia($w[HariID])." $w[_JM]&rarr;$w[_JS]</font></div>
									<input type=hidden name='KRS[]' value='$w[KRSID]'</td>";
			
			$s1 = "select j.JadwalID, j.MKID, j.MKKode, j.Nama, j.HariID, j.RuangID, left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS, 
							j.Kapasitas, j.JumlahMhsw 
						from jadwal j
						where j.ProdiID='$mhsw[ProdiID]' and j.ProgramID='$ProgramID' and j.MKID=$w[MKID] and j.TahunID='$TahunID' and j.KodeID='".KodeID."'
						order by j.HariID, j.JamMulai, j.JamSelesai";
			$r1 = _query($s1);
			$JCount = _num_rows($r1);
			$countkrs = 0;
			$w1 = _fetch_array($r1);
			$da = ($w1[JumlahMhsw] >= $w1[Kapasitas])? 'disabled=true' : '';
			if(!empty($w1))
				echo "<td colspan=2>&raquo;<input type=checkbox id='$w1[MKKode]$countkrs' name='Pilihan[]' value='$w1[JadwalID]' $da onClick=\"EmptyOthers('$w1[MKKode]', '$countkrs', '$JCount')\">
								$w1[Nama]<font size=1 color=teal>($w1[MKKode])</font></br>
									<div align=right><font size=1 color=gray>$w1[RuangID], ".UbahKeHariIndonesia($w1[HariID])." $w1[_JM]&rarr;$w1[_JS]</font> 
													 <font size=1 color=teal>Quota: $w1[JumlahMhsw] / $w1[Kapasitas]</font> </div>";
			else echo "<td colspan=2 align=center><b>- Tidak ada penjadwalan pada Program $ProgramID -</b></td>";
			
			while($w1 = _fetch_array($r1))
			{	$countkrs++; 
				$da = ($w1[JumlahMhsw] >= $w1[Kapasitas])? 'disabled=true' : '';
				echo "<hr size=1 color=silver>
						  &raquo;<input type=checkbox id='$w1[MKKode]$countkrs' name='Pilihan[]' value='$w1[JadwalID]' $da onClick=\"EmptyOthers('$w1[MKKode]', '$countkrs', '$JCount')\">
								$w1[Nama]<font size=1 color=teal>($w1[MKKode])</font></br>
									<div align=right><font size=1 color=gray>$w1[RuangID], ".UbahKeHariIndonesia($w1[HariID])." $w1[_JM]&rarr;$w1[_JS]</font> 
													 <font size=1 color=teal>Quota: $w1[JumlahMhsw] / $w1[Kapasitas]</font></div>";
			
			}
			echo "</td></tr>";
			echo "<tr><td colspan=4><hr size=1 color=silver></td></tr>";
		}
	}
	
	echo "<tr size=1 bgcolor=silver><td colspan=4></td></tr>
    <tr><td class=ul1 colspan=4 align=center>
        <input type=submit name='btnProses' value='Proses' />
        <input type=button name='btnBatal' value='Batal'
          onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />
        </td></tr>
    </form>
    </table>";
	PilihKRSBaruScript();
}
function fnProsesnyaProgram()
{	$MhswID = sqling($_REQUEST['MhswID']);
	$ProgramID = sqling($_REQUEST['ProgramID']);
	$TahunID = sqling($_REQUEST['TahunID']);
	$KRS = $_REQUEST['KRS'];
	$Pilihan = $_REQUEST['Pilihan'];
	
	// Cek Apa Semua KRS Baru ada pasangannya pada Program yang baru
	if(!empty($KRS))
	{
		$matched = array();
		
		foreach($KRS as $perkrs)
		{	$mkkrs = GetaField('krs', 'KRSID', $perkrs, 'MKID');
			$matched[$mkkrs] = $mkkrs;
			
			if(!empty($Pilihan))
			{
				foreach($Pilihan as $perjadwal)
				{	$mkjadwal = GetaField('jadwal', 'JadwalID', $perjadwal, 'MKID');
					if($mkkrs == $mkjadwal)
					{	$matched[$mkkrs] = ''; 
						break;
					}
				}
			}
		}
		$errMsg = array(); 
		foreach($matched as $isMatched)
		{	if(empty($Pilihan))
			{	$mk = GetFields('mk', 'MKID', $isMatched, 'MKKode, Nama');
				$errMsg[] = "Mata Kuliah $mk[Nama] ($mk[MKKode])";
			}
			else
			{	if(!empty($isMatched)) 
				{	$mk = GetFields('mk', 'MKID', $isMatched, 'MKKode, Nama');
					$errMsg[] = "Mata Kuliah $mk[Nama] ($mk[MKKode])";
				}
			}
		}
		
		if (!empty($errMsg))
		{	foreach($errMsg as $error) $errorstring .= "<br>&bull; <b>$error</b>";
			die(ErrorMsg("Gagal",
			"Mata Kuliah di bawah ini tidak mendapat pilihan KRS Baru untuk dipindahkan ke Program $ProgramID.<br>".$errorstring."
			<br>
			<br>Siswa tidak dapat dipindahkan ke Program $ProgramID.
			<hr size=1 color=silver />
			<input type=button name='btnKembali' value='Kembali'
			  onClick=\"location='?mnux=$_SESSION[mnux]&gos=fnProsesProgram&MhswID=$MhswID&ProgramID=$ProgramID'\" />
			<input type=button name='btnBatal' value='Batal'
			  onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />")); 
		}
		
		foreach($KRS as $perkrs)
		{
			$krs = GetFields('krs', "KRSID='$perkrs' and KodeID", KodeID, '*');
			$baru = $krs;
			$baru['ProgramID'] = $ProgramID;
			if(!empty($Pilihan))
			{	foreach($Pilihan as $perjadwal)
				{	$mkjadwal = GetFields('jadwal', 'JadwalID', $perjadwal, 'MKID, DosenID');
					if($krs[MKID] == $mkjadwal[MKID])
					{	$baru['JadwalID'] = $perjadwal; 
						$baru['DosenID'] = $mkjadwal['DosenID'];
						break;
					}
				}
			}
			// Salin data mhsw
			$s = "update krs
				set JadwalID='$baru[JadwalID]', DosenID='$baru[DosenID]', 
					LoginEdit='$_SESSION[_Login]', TanggalEdit=now() 
				where KRSID='$perkrs'";
			$r = _query($s);
		}
	}
	
	// Ubah semua Status Program Mhsw di sini
	$sm = "update mhsw 
		set ProgramID = '$ProgramID'
		where MhswID = '$MhswID' and KodeID = '".KodeID."' ";
	$rm = _query($sm);
	$sk = "update khs
		set ProgramID = '$ProgramID'
		where MhswID = '$MhswID' and KodeID = '".KodeID."' and TahunID='$TahunID'";
	$rk = _query($sk);
	// Kembali
	BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=&crMhswID=$MhswID", 1);
	
}

function PilihKRSBaruScript()
{	echo <<< SCR
		<script>
			function EmptyOthers(mkkode, target, count)
			{	
				for(i = 0; i < count; i++)
				{	document.getElementById(mkkode+i).checked = false;
				}
				document.getElementById(mkkode+target).checked = true;
			}
		</script>
SCR;
}
function UbahKeHariIndonesia($integer)
{	$arrHari = array('Minggu', 'Senin', 'Selasa', 'Rabu','Kamis', 'Jumat', 'Sabtu');
	return $arrHari[$integer+0];
}
?>
