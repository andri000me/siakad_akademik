<?php
// *** Parameters ***
session_start();
$tabalumni = GetSetVar('tabalumni', 0);
$mhswid = GetSetVar('mhswid');
$mhswbck = ($_SESSION['_LevelID']==121)? "":GetSetVar('mhswbck');
if ($_SESSION['_LevelID']==121) {$_SESSION['mhswid']=$_SESSION['_Login'];$mhswid=$_SESSION['_Login'];}
// *** Main ***
TampilkanJudul(($_SESSION['_LevelID']==121)? "Profil Alumni":"Administrasi Alumni");
$gos = (empty($_REQUEST['gos']))? 'fnEdit' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function fnEdit() {
  $mhsw = GetFields("mhsw m",
    "m.MhswID='$_SESSION[mhswid]' and m.KodeID", KodeID,
    "m.*");
  $alumni = GetFields("alumni a",
    "a.MhswID='$_SESSION[mhswid]' and a.KodeID", KodeID,
    "a.*");
  if (empty($mhsw) || empty($alumni))
    echo ErrorMsg('Error',
      "Data alumni dengan NPM: $mhswid tidak ditemukan.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mhswbck]&gos='\" />");
  else {
    if (empty($alumni['MhswID'])){
      $s = "INSERT into alumni(MhswID) value('$mhsw[MhswID]')";
      $r = _query($s);
      $alumni = GetFields("alumni a",
                          "a.MhswID='$_SESSION[mhswid]' and a.KodeID", KodeID,
                          "a.*");
    }
    TampilkanHeader($mhsw, $alumni);
    TampilkanTab($mhsw, $alumni, $_SESSION['tabalumni']);
  }
}
function TampilkanHeader($mhsw, $alumni) {
  if (empty($alumni['MhswID'])){
    $s = "INSERT into alumni(MhswID) value('$mhsw[MhswID]')";
    $r = _query($s);
  }
  $prodi = GetaField('prodi', "ProdiID='$mhsw[ProdiID]' and KodeID", KodeID, "Nama");
  $kembali = $_SESSION['_LevelID']!=121? "<input type=button name='btnBack' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mhswbck]&gos='\" />":'';
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <tr><td class=inp width=60>Nama:</td>
      <td class=ul>$mhsw[Nama] <sup>$alumni[Gelar]</sup></td>
      <td class=inp width=60>NPM:</td>
      <td class=ul>$mhsw[MhswID]</td>
      </tr>
  <tr><td class=inp>Prodi/Prg:</td>
      <td class=ul>$prodi <sup>$mhsw[ProgramID]</sup></td>
      <td class=inp>E-mail:</td>
      <td class=ul>$alumni[Email]&nbsp;</td>
      </tr>
  <tr><td class=ul colspan=4 align=center>
      $kembali
      <input type=button name='btnRefresh' value='Refresh'
        onClick="location='?mnux=$_SESSION[mnux]&gos='" />
      </td></tr>
  
  </table>
  <p>
ESD;
}
function TampilkanTab($mhsw, $alumni, $tab) {
  $arrtab = array(
    "Biodata",
    "Pekerjaan"
  );
  $tab += 0;
  // parsing tab
  $i = 0;
  echo "<table class=bsc cellspacing=1 align=center width=600>
    <tr>";
  foreach ($arrtab as $a) {
    $sel = ($i == $tab)? "class=menuaktif" : "class=menuitem";
    echo "<td $sel width=100 align=center>
      <a href='?mnux=$_SESSION[mnux]&tabalumni=$i'>$a</a>
      </td>";
    $i++;
  }
  echo "<td style='border-bottom: 1px solid silver' width=*>&nbsp;</td>
    </tr></table>";
  $fn = 'fn_'.$tab;
  $fn($mhsw, $alumni, $tab);
}
function fn_0($mhsw, $alumni, $tab) { 
  // *** Data Alamat Alumni
  	$s = "SELECT * from negara where NA='N' group by KodeBenua order by KodeBenua";
	$r = _query($s); $benua='<option value=""></option>';
	while ($w = _fetch_array($r)) {
		$negara 	= GetOption2('negara', "NamaNegara", 'NamaNegara', $alumni['Negara'],
    				"NA='N' AND KodeBenua='$w[KodeBenua]'", 'IDNegara');
		$benua 	.= '<optgroup label="'.$w['NamaBenua'].'">';
		$benua 	.= $negara;
		$benua 	.= '</optgroup>';
	}
	$GolonganDarah 	= GetOption2('golongandarah', "GolonganDarah", 'GolonganDarah', $alumni['GolonganDarah'],
    				"", 'GolonganDarah');
	$nKerja = ($alumni['SudahBekerja']=="Y" ? " selected ":"");
	$MasaTunggu = GetNumberOption(0,48,$alumni['MasaTungguKerja'],'1',1);
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmAlamat' action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='fn_0_save' />
  <input type=hidden name='mhswid' value='$mhsw[MhswID]' />
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><td class=inp width=100>Alamat:</td>
      <td class=ul><textarea name='Alamat' cols=50 rows=3>$alumni[Alamat]</textarea>
      </td></tr>
  <tr><td class=inp>Kota:</td>
      <td class=ul>
      <input type=text name='Kota' value='$alumni[Kota]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Kode Pos:</td>
      <td class=ul>
      <input type=text name='KodePos' value='$alumni[KodePos]' size=20 maxlength=50 />
      </td></tr>
  <tr><td class=inp>RT/RW:</td>
      <td class=ul>
      <input type=text name='RT' value='$alumni[RT]' size=10 maxlength=10 />/
      <input type=text name='RW' value='$alumni[RW]' size=10 maxlength=10 />
      </td></tr>
  <tr><td class=inp>Propinsi:</td>
      <td class=ul>
      <input type=text name='Propinsi' value='$alumni[Propinsi]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Negara:</td>
      <td class=ul>
      <select name='Negara'>$benua</select>
      </td></tr>
  <tr><td class=inp>Telepon/Ponsel:</td>
      <td class=ul>
      <input type=text name='Telepon' value='$alumni[Telepon]' size=20 maxlength=50 />/
      <input type=text name='Handphone' value='$alumni[Handphone]' size=20 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Email:</td>
      <td class=ul>
      <input type=text name='Email' value='$alumni[Email]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Golongan Darah:</td>
      <td class=ul>
      <select name='GolonganDarah' class='nones'>
	  $GolonganDarah 
	  </select>
      </td></tr>
  <tr><td class=inp>Sudah Bekerja:</td>
      <td class=ul>
      <select name='SudahBekerja'>
	  <option value=''></option>
	  <option value='Y' $nKerja>Sudah</option>
	  <option value='N'>Belum</option>
	  </select>
      </td></tr>
  <tr><td class=inp>Masa Tunggu:</td>
      <td class=ul>
      <select name='MasaTunggu' class='nones'>$MasaTunggu</select> bulan <sup>&raquo; Masa tunggu mendapatkan pekerjaan pertama setelah wisuda</sup>
      </td></tr>
  <tr><td class=inp>Gaji Pertama:</td>
      <td class=ul>
      <input type=text name='GajiPertama' value='$alumni[GajiPertama]' size=15 maxlength=30 /> isi dengan angka saja.
      </td></tr>
  <tr><td class=inp>Deskripsi singkat:</td>
      <td class=ul>
      <textarea name='DeskripsiSingkat' placeholder='Deskripsikan singkat tentang diri anda...' cols=50></textarea>
      <br>
      </td></tr>
  <tr><td class=inp>Penghargaan:</td>
      <td class=ul>
      <textarea name='Penghargaan' placeholder='Penghargaan yang pernah diraih...' cols=50></textarea>
      <br>
      </td></tr>
  <tr><td class=inp>Upload Berkas:</td>
      <td class=ul>
      <input type='file' name='Berkas'>
      <br>Upload berkas ijazah/transkrip/sertifikat/dan data pendukung lainnya.
      </td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='btnSimpan' value='Simpan' />
      </td></tr>
  
  </form>
  </table>
ESD;
}
function fn_0_save() {
  $mhswid = sqling($_REQUEST['mhswid']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $RT = sqling($_REQUEST['RT']);
  $RW = sqling($_REQUEST['RW']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Handphone = sqling($_REQUEST['Handphone']);
  $Email = sqling($_REQUEST['Email']);
  $MasaTunggu = sqling($_REQUEST['MasaTunggu']);
  $GajiPertama = sqling($_REQUEST['GajiPertama']);
  $SudahBekerja = sqling($_REQUEST['SudahBekerja']);
  $GolonganDarah = sqling($_REQUEST['GolonganDarah']);
  // Simpan
  $s = "update alumni
    set Alamat = '$Alamat', Kota = '$Kota', KodePos = '$KodePos',
        RT = '$RT', RW = '$RW', Propinsi = '$Propinsi', Negara = '$Negara',
        Telepon = '$Telepon', Handphone = '$Handphone', Email = '$Email',GajiPertama='$GajiPertama', SudahBekerja='$SudahBekerja',
        TanggalEdit = now(), LoginEdit = '$_SESSION[_Login]', MasaTungguKerja='$MasaTunggu', GolonganDarah='$GolonganDarah'
    where MhswID = '$mhswid' and KodeID = '".KodeID."' ";
  $r = _query($s);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}
function fn_1($mhsw, $alumni, $tab) {
  // *** Data pekerjaan ***
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['alumnikerjapage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&alumnikerjapage==PAGE='>=PAGE=</a>";

  $lst->tables = "alumnikerja
    where MhswID='$_SESSION[mhswid]' and KodeID = '".KodeID."'
    order by MulaiKerja Desc";
  $lst->fields = "*,
    date_format(MulaiKerja, '%d-%m-%Y') as _MulaiKerja";
  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=600>
    <tr><td class=ul colspan=6>
        <input type=button name='btnTambah' value='+ Tambah'
          onClick=\"javascript:editPekerjaan('$mhsw[MhswID]', 1, 0)\" />
        </td></tr>
    <tr><th class=ttl width=20>No.</th>
        <th class=ttl width=90>Mulai Kerja</th>
        <th class=ttl>Perusahaan</th>
        <th class=ttl width=100>Jabatan</th>
        <th class=ttl width=100>Kota</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp width=20>=NOMER=</td>
    <td class=ul align=center>
      <a href='#' onClick=\"javascript:editPekerjaan('$mhsw[MhswID]', 0, =AlumniKerjaID=)\"><img src='img/edit.png' /></a>
      =_MulaiKerja=
      </td>
    <td class=ul>=Nama=</td>
    <td class=ul>=Jabatan=</td>
    <td class=ul>=Kota=</td>
    </tr>
    <tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
  
  RandomStringScript();
  echo <<<ESD
  <div class='box0' id='divPekerjaan' align=center>
  <!--<a href="#" onClick="javascript:toggleBox('divPekerjaan', 0)"><img src='img/kali.png' align=right /></a>-->
  <iframe name='framePekerjaan' id="framePekerjaan" src="" width=100% height=100% frameborder=0>
  </iframe>
  </div>
  
  <script>
  function editPekerjaan(MhswID, md, akid) {
    toggleBox('divPekerjaan', 1);
    _rnd = randomString();
    lnk = "$_SESSION[mnux].pekerjaan.php?mhswid="+MhswID+"&md="+md+"&akid="+akid+"&_rnd="+_rnd;
    document.getElementById('framePekerjaan').src = lnk;
    //alert(lnk);
  }

  </script>
ESD;
}
?>

  <script>
  function toggleBox(szDivID, iState) // 1 visible, 0 hidden
  {
    if(document.layers)	   //NN4+
    {
       document.layers[szDivID].visibility = iState ? "show" : "hide";
    }
    else if(document.getElementById)	  //gecko(NN6) + IE 5+
    {
        var obj = document.getElementById(szDivID);
        obj.style.visibility = iState ? "visible" : "hidden";
    }
    else if(document.all)	// IE 4
    {
        document.all[szDivID].style.visibility = iState ? "visible" : "hidden";
    }
  }
  </script>
