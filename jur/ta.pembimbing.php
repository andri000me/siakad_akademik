<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Setup Dosen Pembimbing Skripsi/TA", 1);
CekBolehAksesModul();
echo <<<SCR
  <script src="../$_SESSION[mnux].ujian.script.js"></script>
SCR;

// *** Parameters ***
$TAID = GetSetVar('TAID');

// *** Main ***
TampilkanJudul("Setup Pembimbing Skripsi/TA");
$gos = (empty($_REQUEST['gos']))? 'TampilkanHeader' : $_REQUEST['gos'];
$gos($TAID);

// *** Functions ***
function TampilkanHeader() {
  $ta = GetFields('ta', 'TAID', $_SESSION['TAID'], '*');
  $dsn = GetFields('dosen', "Login = '$ta[Pembimbing]' and KodeID",
    KodeID, "Nama, Gelar");
  $mhsw = GetFields('mhsw', "MhswID = '$ta[MhswID]' and KodeID",
    KodeID, "*");
  $TglMulai = FormatTanggal($ta['TglMulai']);
  $TglSelesai = FormatTanggal($ta['TglSelesai']);
  $b1 = (empty($dsn))? '(Belum diset)' : "$dsn[Nama] <sup>$dsn[Gelar]</sup>";
  echo <<<ESD
  <table class=box cellspacing=1 width=100%>
  <tr><td class=inp width=100>NPM:</td>
      <td class=ul>$ta[MhswID]&nbsp;</td>
      <td class=inp width=100>Nama Mahasiswa:</td>
      <td class=ul>$mhsw[Nama]&nbsp;</td>
      </tr>
  <tr><td class=inp>Tahun Akademik:</td>
      <td class=ul>$ta[TahunID]&nbsp;</td>
      <td class=inp>Batas Waktu:</td>
      <td class=ul>$TglMulai ~ $TglSelesai</td>
      </tr>
  <tr><td class=inp>Judul:</td>
      <td class=ul colspan=3>$ta[Judul]&nbsp;</td>
      </tr>
  <tr><td class=inp>Deskripsi/Abstrak:</td>
      <td class=ul colspan=3>$ta[Deskripsi]&nbsp;</td>
      </tr>
  <tr><td class=inp>Pembimbing Utama:</td>
      <td class=ul colspan=3>$b1</td>
      </tr>
  </table>
ESD;
  TampilkanDaftarPembimbing($_SESSION['TAID'], $mhsw);
}
function TampilkanDaftarPembimbing($TAID, $mhsw) {
  $s = "select td.*,
      d.Nama, d.Gelar
    from tadosen td
      left outer join dosen d on td.DosenID = d.Login and d.KodeID = '".KodeID."'
    where td.TAID = '$TAID' ";
  $r = _query($s); $n = 1;

  echo "<table class=box cellspacing=1 width=100%>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo <<<ESD
    <tr><td class=inp width=100>Pembimbing ke-$n:</td>
        <td class=ul>$w[Nama] <sup>$w[Gelar]</sup></td>
        <td class=ul width=10 align=center>
        <a href='#' onClick="javascript:HapusDosenPembimbing($TAID, $w[TADosenID])"><img src='../img/del.gif' /></a>
        </td>
        </tr>
ESD;
  }
  echo <<<ESD
  <form name='frmTA' action='../$_SESSION[mnux].pembimbing.php' method=POST>
  <input type=hidden name='gos' value='fnTambah' />
  <input type=hidden name='TAID' value='$TAID' />
    <tr><td class=inp width=100>&raquo; Tambah<br />Pembimbing:</td>
      <td class=ul colspan=3>
      <input type=text name='DosenID' value='' size=10 maxlength=50 />
      <input type=text name='Dosen' value='' size=30 maxlength=50 onKeyUp="javascript:CariDosen('$mhsw[ProdiID]', 'frmTA')" />
      &raquo;
      <a href='#'
        onClick="javascript:CariDosen('$mhsw[ProdiID]', 'frmTA')" />Cari...</a> |
      <a href='#' onClick="javascript:frmTA.DosenID.value='';frmTA.Dosen.value=''">Reset</a>
      <div align=right>
      <input type=submit name='btnSimpan' value='Simpan' />
      <input type=button name='btnTutup' value='Tutup' onClick="TutupDong()" />
      </div>
      </td></tr>
  </form>
  
  </table>
  <div class='box0' id='caridosen'></div>
  
  <script>
  <!--
  function HapusDosenPembimbing(taid, id) {
    if (confirm("Anda yakin akan menghapus dosen pembimbing ini?")) {
      window.location = "../$_SESSION[mnux].pembimbing.php?gos=fnHapusDosenPembimbing&TAID="+taid+"&id="+id;
    }
  }
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
ESD;
}
function fnHapusDosenPembimbing($TAID) {
  $id = $_REQUEST['id']+0;
  $s = "delete from tadosen where TADosenID = $id";
  $r = _query($s);
  echo <<<ESD
  <script>
  opener.location = "../index.php?mnux=$_SESSION[mnux]&gos=";
  </script>
ESD;
  BerhasilSimpan("../$_SESSION[mnux].pembimbing.php?TAID=$TAID", 10);
}
function fnTambah($TAID) {
  $ta = GetFields('ta', 'TAID', $TAID, '*');
  $DosenID = sqling($_REQUEST['DosenID']);
  $cek = GetFields('ta', "TAID='$TAID' and Pembimbing",$DosenID,'*');
  $cek1 = GetFields('tadosen', "TAID='$TAID' and Tipe='0' and DosenID",$DosenID,'*');
  if(!empty($cek)){
    die(ErrorMsg('Error', 
      "Dosen ini telah didaftarkan sebagai Pembimbing.<br/>
       Pilih dosen yang lain.  
      <hr size=1 color=silver />
      <p align=center>      
	  <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
  }elseif(!empty($cek1)){
    die(ErrorMsg('Error', 
      "Dosen ini telah didaftarkan sebagai Pembimbing.<br/>
       Pilih dosen yang lain.  
      <hr size=1 color=silver />
      <p align=center>      
	  <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
  
  }
  $s = "insert into tadosen
    (TAID, Tipe, MhswID, DosenID,
    LoginBuat, TanggalBuat)
    values
    ($TAID, 0, '$ta[MhswID]', '$DosenID',
    '$_SESSION[_Login]', now())";
  $r = _query($s);
  $s = "UPDATE ta set Pembimbing2='$DosenID' where TAID = $TAID";
  $r = _query($s);
  echo <<<ESD
  <script>
  opener.location = "../index.php?mnux=$_SESSION[mnux]&gos=";
  </script>
ESD;
  BerhasilSimpan("../$_SESSION[mnux].pembimbing.php?TAID=$TAID", 10);
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
