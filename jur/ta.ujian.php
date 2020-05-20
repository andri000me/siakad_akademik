<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Setup Ujian Seminar Hasil", 1);
CekBolehAksesModul();
echo <<<SCR
  <script src="../$_SESSION[mnux].ujian.script.js"></script>
SCR;

// *** Parameters ***
$TAID = GetSetVar('TAID');

// *** Main ***
TampilkanJudul("Setup Ujian Seminar Hasil");
$gos = (empty($_REQUEST['gos']))? 'TampilkanSetupUjianTA' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanSetupUjianTA() {
  $ta = GetFields("ta t
    left outer join mhsw m on t.MhswID = m.MhswID and m.KodeID='".KodeID."'
    left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID='".KodeID."'
    left outer join dosen d1 on d1.Login = t.Pembimbing and d1.KodeID='".KodeID."'
    ", 
    "t.TAID", $_SESSION['TAID'], 
    "t.*, m.Nama as NamaMhsw,
    date_format(t.TglMulai, '%d-%m-%Y') as _TglMulai,JamUjian,
    date_format(t.TglSelesai, '%d-%m-%Y') as _TglSelesai,
    d.Nama as NamaPA, d.Gelar as GelarPA,
    d1.Nama as NamaPembimbing, d1.Gelar as GelarPembimbing");
  TampilkanHeaderTA($ta);
  TampilkanDosenPenguji($ta);
}
function TampilkanHeaderTA($ta) {
  $PA = (empty($ta['NamaPA']))? 'Belum diset' : "$ta[NamaPA] <sup>$ta[GelarPA]</sup>";
  $_TglUjian = ($ta['TglUjian'] == '0000-00-00')? date('Y-m-d') : $ta['TglUjian'];
  $TglUjian = GetDateOption($_TglUjian, 'TglUjian');
  $JamUjian = GetTimeOption($ta['JamUjian'], 'JamUjian');
  $NamaPenguji = GetaField('dosen', "KodeID='".KodeID."' and Login", $ta['Penguji'], 'Nama');
  echo <<<SCR
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmTA' action='../$_SESSION[mnux].ujian.php' method=POST />
  <input type=hidden name='TAID' value='$ta[TAID]' />
  <input type=hidden name='gos' value='Simpan' />
  <tr><td class=inp width=160>NPM:</td>
      <td class=ul>$ta[MhswID]</td>
      <td class=inp>Mahasiswa:</td>
      <td class=ul>$ta[NamaMhsw]</td>
      </tr>
  <tr><td class=inp>Penasehat Akademik:</td>
      <td class=ul>$PA</td>
      <td class=inp>Pembimbing:</td>
      <td class=ul>$ta[NamaPembimbing] <sup>$ta[GelarPembimbing]</sup></td>
      </tr>
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul>$ta[TahunID]</td>
      <td class=inp>Batas Waktu:</td>
      <td class=ul><sup>$ta[_TglMulai]</sup> &#8883; <sub>$ta[_TglSelesai]</sub></td>
      </tr>
  <tr><td class=inp>Judul:</td>
      <td class=ul colspan=3>$ta[Judul]</td>
      </tr>
  <tr><td class=inp>Deskripsi/Abstrak:</td>
      <td class=ul colspan=3>$ta[Deskripsi]</td>
      </tr>
  <tr><td class=inp>Tgl Ujian Seminar Hasil:</td>
      <td class=ul colspan=3>
      $TglUjian Jam $JamUjian
      </td></tr>
  <tr><td class=inp>Pemimpin Penguji:</td>
      <td class=ul colspan=3>
      <input type=text name='DosenID' value='$ta[Penguji]' size=10 maxlength=50 />
      <input type=text name='Dosen' value='$NamaPenguji' size=30 maxlength=50 onKeyUp="javascript:CariDosen('$_SESSION[ProdiID]', 'frmTA')" />
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[ProdiID]', 'frmTA')" />Cari...</a> |
      <a href='#' onClick="javascript:frmTA.DosenID.value='';frmTA.Dosen.value=''">Reset</a>
      </td></tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Refresh' value='Refresh'
        onClick="location='../$_SESSION[mnux].ujian.php?TAID=$ta[TAID]'" />
      <input type=button name='Tutup' value='Tutup' onClick='javascript:TutupDong()' />
      </td></tr>
  </form>
  </table>
  
  <div class='box0' id='caridosen'></div>
  
  <script>
  <!--
  function TutupDong() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
    self.close();
    return false;
  }
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
  function CariDosen(ProdiID, frm) {
    if (eval(frm + ".Dosen.value != ''")) {
      eval(frm + ".Dosen.focus()");
      showDosen(ProdiID, frm, eval(frm +".Dosen.value"), 'caridosen');
      toggleBox('caridosen', 1);
    }
  }
  //-->
  </script>

SCR;
}
function TampilkanDosenPenguji($ta) {


  DivTambahDosenPenguji($ta);
  $s = "select td.*, d.Nama, d.Gelar
    from tadosen td
      left outer join dosen d on d.Login = td.DosenID and d.KodeID = '".KodeID."'
    where td.TAID = '$ta[TAID]'
      and td.Tipe = 1
    order by d.Nama";
  $r = _query($s); $n = 0;
  
  echo "<table class=bsc cellspacing=1 width=100%>
    <tr><th class=ttl>#</th>
        <th class=ttl>Nama Dosen Penguji</th>
        <th class=ttl>Hapus</th>
        </tr>";
  
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp width=20>$n</td>
      <td class=ul>$w[Nama] <sup>$w[Gelar]</sup>&nbsp;</td>
      <td class=ul width=20 align=center>
        <a href='#' onClick='javascript:HapusDosenPenguji($w[TADosenID])' title='Hapus Dosen ini'><img src='../img/del.gif' /></a>
        </td>
      </tr>";
  }
  echo <<<SCR
  </table>
  <script>
  function HapusDosenPenguji(id) {
    if (confirm("Anda yakin akan menghapus dosen penguji ini?")) {
      window.location = "../$_SESSION[mnux].ujian.php?gos=HapusDosenPenguji&TAID=$ta[TAID]&id="+id;
    }
  }
  </script>
SCR;
}
function HapusDosenPenguji() {
  $TAID = $_REQUEST['TAID'];
  $id = $_REQUEST['id'];
  $s = "delete from tadosen where TADosenID = '$id' ";
  $r = _query($s);
  echo <<<ESD
    <script>
    opener.location = "../index.php?mnux=$_SESSION[mnux]&gos=";
    </script>
ESD;
  BerhasilSimpan("../$_SESSION[mnux].ujian.php?TAID=$TAID", 1);
}
function DivTambahDosenPenguji($ta) {

  CheckFormScript('DosenID');
  echo <<<SCR
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmDosen' action='../$_SESSION[mnux].ujian.php' method=POST
    onSubmit="return CheckForm(this)">
  <input type=hidden name='TAID' value='$ta[TAID]' />
  <input type=hidden name='gos' value='TambahDosenPenguji' />
  
  <tr><th class=ttl colspan=2>Para Dosen Penguji</th></tr>
  <tr><td class=inp width=160>Tambah Dosen Penguji:</td>
      <td class=ul1 nowrap>
      <input type=text name='DosenID' size=10 maxlength=50 />
      <input type=text name='Dosen' size=30 maxlength=50 
        onKeyUp="javascript:CariDosen('$_SESSION[ProdiID]', 'frmDosen')" />
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$_SESSION[ProdiID]', 'frmDosen')" />Cari...</a> |
      <a href='#' onClick="javascript:frmDosen.DosenID.value='';frmDosen.Dosen.value='';">Reset</a>
      <br />
      <input type=submit name='TambahkanDosen' value='Tambahkan Dosen Penguji Ini' />
      </td></tr>
  
  </form>
  </table>

SCR;
}
function TambahDosenPenguji() {
  $TAID = $_REQUEST['TAID'];
  $DosenID = sqling($_REQUEST['DosenID']);
  $cek = GetFields('ta', "TAID='$TAID' and Penguji",$DosenID,'*');
  $cek1 = GetFields('tadosen', "TAID='$TAID' and Tipe=1 and DosenID",$DosenID,'*');
  $ada = GetaField('tadosen', "TAID='$TAID' and Tipe=1 and DosenID", $DosenID, 'TADosenID')+0;
  if(!empty($cek)){
    die(ErrorMsg('Error', 
      "Dosen ini telah didaftarkan sebagai Penguji.<br/>
       Pilih dosen yang lain.  
      <hr size=1 color=silver />
       Opsi: <input type=button name='Kembali' value='Kembali'
        onClick=\"location='../$_SESSION[mnux].ujian.php?TAID=$TAID'\" />
        <input type=button name='Batal' value='Batal'
        onClick='window.close()' />"));
  }elseif(!empty($cek1)){
    die(ErrorMsg('Error', 
      "Dosen ini telah didaftarkan sebagai Penguji.<br/>
       Pilih dosen yang lain.  
      <hr size=1 color=silver />       
	  Opsi: <input type=button name='Kembali' value='Kembali'
        onClick=\"location='../$_SESSION[mnux].ujian.php?TAID=$TAID'\" />
        <input type=button name='Batal' value='Batal'
        onClick='window.close()' />
      "));
  
  }
  
 
    $MhswID = GetaField('ta', 'TAID', $TAID, 'MhswID');
    $s = "insert into tadosen
      (TAID, MhswID, DosenID, Tipe,
      LoginBuat, TanggalBuat, NA)
      values
      ('$TAID', '$MhswID', '$DosenID', 1,
      '$_SESSION[_Login]', now(), 'N')";
    $r = _query($s);
    echo <<<ESD
    <script>
    opener.location = "../index.php?mnux=$_SESSION[mnux]&gos=";
    </script>
ESD;
    BerhasilSimpan("../$_SESSION[mnux].ujian.php?TAID=$TAID", 1);
  
}
function Simpan() {
  $TAID = $_REQUEST['TAID'];
  $DosenID = sqling($_REQUEST['DosenID']);
  $TglUjian = "$_REQUEST[TglUjian_y]-$_REQUEST[TglUjian_m]-$_REQUEST[TglUjian_d]";
  $JamUjian = "$_REQUEST[JamUjian_h]:$_REQUEST[JamUjian_n]";
  $s = "update ta
    set Penguji = '$DosenID', TglUjian = '$TglUjian', JamUjian='$JamUjian',
        LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
    where TAID = '$TAID' ";
  $r = _query($s);
  echo <<<ESD
  <script>
  opener.location = "../index.php?mnux=$_SESSION[mnux]&gos=";
  </script>
ESD;
  BerhasilSimpan("../$_SESSION[mnux].ujian.php?TAID=$TAID", 1);
}
function CekBolehAksesModul() {
  $arrAkses = array(1, 20, 40, 42, 43, 56, 66, 440, 51);
  $key = array_search($_SESSION['_LevelID'], $arrAkses);
  if ($key === false)
    die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi SysAdmin untuk informasi lebih lanjut."));
}
?>

</BODY>
</HTML>
