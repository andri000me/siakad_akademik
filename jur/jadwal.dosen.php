<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Jadwal Kuliah", 1);

// *** Parameters ***
$id = GetSetVar('id');
$jdwl = GetFields("jadwal j
  left outer join dosen d on d.Login = j.DosenID and d.KodeID='".KodeID."'
  left outer join program prg on prg.ProgramID = j.ProgramID
  left outer join prodi prd on prd.ProdiID = j.ProdiID
  left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID
  left outer join hari h on h.HariID = j.HariID",
  "j.KodeID='".KodeID."' and j.JadwalID", $id,
  "j.JadwalID, j.MKKode, j.Nama, j.MKID, j.NamaKelas, j.JenisJadwalID,
  left(j.JamMulai, 5) as JM, left(j.JamSelesai, 5) as JS,
  h.Nama as HR, jj.Nama as JEN, j.SKS,
  j.RuangID,
  concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
  prg.Nama as _PRG, prd.Nama as _PRD");
if (empty($jdwl))
  die(ErrorMsg('Error',
    "Jadwal tidak ditemukan.<br />
    Mungkin jadwal sudah dihapus atau mungkin Anda tidak berhak mengakses modul ini.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));

// *** Main ***
TampilkanJudul("Edit Dosen &minus; Jadwal");
TampilkanHeaderDosenJadwal($jdwl);
$gos = (empty($_REQUEST['gos']))? 'DftrDosen' : $_REQUEST['gos'];
$gos($jdwl);

// *** Functions ***
function TampilkanHeaderDosenJadwal($jdwl) {
  echo "<table class=bsc cellspacing=1 width=100%>
  <tr><td class=inp width=100>Matakuliah:</td>
      <td class=ul1>$jdwl[Nama] <sup>($jdwl[MKKode])</sup></td>
      <td class=inp>Waktu Kuliah:</td>
      <td class=ul1>$jdwl[HR] <sup>$jdwl[JM]</sup>&minus;<sub>$jdwl[JS]</sup></td>
      </tr>
  <tr><td class=inp>Dosen Utama:</td>
      <td class=ul1>$jdwl[DSN]</td>
      <td class=inp>SKS:</td>
      <td class=ul1>$jdwl[SKS]</td>
      </tr>
  <tr><td class=inp>Kelas:</td>
      <td class=ul1>$jdwl[NamaKelas] <sup>($jdwl[JEN])</sup></td>
      <td class=inp>Ruang:</td>
      <td class=ul1>$jdwl[RuangID]</sup></td>
      </tr>
  </table>";
}
function DftrDosen($jdwl) {
  $s = "select jd.*, d.Nama, d.Gelar
    from jadwaldosen jd
      left outer join dosen d on jd.DosenID = d.Login
    where jd.JadwalID = '$jdwl[JadwalID]'
    order by d.Nama";
  $r = _query($s);
  $n = 0;
  
  // Tampilkan
  FormDosenJadwal($jdwl);
  HapusDosenScript($jdwl);
  echo "<p><table class=bsc cellspacing=1 width=100%>";
  echo "<tr
    <th class=ttl width=20>#</th>
    <th class=ttl width=100>NIP</th>
    <th class=ttl>Nama Dosen</th>
    <th class=ttl width=100>Jenis</th>
    <th class=ttl>&times;</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul1>$w[DosenID]</td>
      <td class=ul1>$w[Nama] <sup>($w[Gelar])</sup>
      <td class=ul1>$w[JenisDosenID]</td>
      <td class=ul1 width=10 align=center><a href='#' onClick='javascript:HapusDosen($w[JadwalDosenID])'><img src='../img/del.gif' /></a></td>
      </tr>";
  }
  echo "</table></p>";
}
function FormDosenJadwal($jdwl) {
  JadwalDosenScript($jdwl);
  TutupScript();
  CheckFormScript('DosenID,JenisDosenID');
  $optjenisdosen = GetOption2('jenisdosen', 'Nama', 'JenisDosenID', 'DSN', '', 'JenisDosenID');
  echo <<<SCR
    <table class=bsc width=100%>

    <form name='frmJadwal' action='../$_SESSION[mnux].dosen.php' method=POST
      onSubmit="return CheckForm(this)">
    <input type=hidden name='gos' value='SimpanDosen' />
    <input type=hidden name='id' value='$jdwl[JadwalID]' />

    <tr><td class=inp width=100>Tambah:</td>
        <td class=ul1 width=50>Kode:<br />
          <input type=text name='DosenID' size=8 maxlength=50 /><br />&nbsp;</td>
        <td class=ul1 width=50>Nama Dosen:<br />
          <input type=text name='Dosen' size=20 maxlength=50 
            onKeyUp="javascript:CariDosen('$_SESSION[_jdwlProdi]', 'frmJadwal')" /><br />
          <a href='#'
            onClick="javascript:CariDosen('$jdwl[ProdiID]', 'frmJadwal')">Cari...</a> |
          <a href='#' onClick="javascript:frmJadwal.DosenID.value='';frmJadwal.Dosen.value=''">Reset</a>
          </td>
        <td class=ul1>Jenis:<br />
          <select name='JenisDosenID'>$optjenisdosen</select>
          <br />&nbsp;
          </td>
        <td class=ul1>
          <input type=submit name='Simpan' value='Simpan' />
          <input type=button name='Kembali' value='Kembali' onClick="ttutup()" />
          </td>
        </tr>
    </form>
    </table>
    <div class='box0' id='caridosen'>...</div>
SCR;
}
function SimpanDosen($jdwl) {
  $DosenID = sqling($_REQUEST['DosenID']);
  $JenisDosenID = sqling($_REQUEST['JenisDosenID']);
  // Cek dulu
  $ada = GetaField('jadwaldosen', "JadwalID='$_SESSION[id]' and DosenID", $DosenID, 'JadwalDosenID');
  $ada1 = GetFields('jadwal', "JadwalID='$_SESSION[id]' and DosenID", $DosenID, '*');
  if(!empty($ada1)){
     echo ErrorMsg("Error",
      "Dosen <b>$DosenID</b> telah didaftarkan di jadwal ini.<br />
      Anda tidak bisa mendaftarkannya dua kali.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup'
        onClick=\"window.close()\" />");
  }
  elseif (!empty($ada)) {
     echo ErrorMsg("Error",
      "Dosen <b>$DosenID</b> telah didaftarkan di jadwal ini.<br />
      Anda tidak bisa mendaftarkannya dua kali.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup'
        onClick=\"window.close()\" />");
  }else{
    // Simpan
    $s = "insert into jadwaldosen
      (JadwalID, DosenID, JenisDosenID,
      TglBuat, LoginBuat)
      values
      ('$jdwl[JadwalID]', '$DosenID', '$JenisDosenID',
      now(), '$_SESSION[_Login]')";
    $r = _query($s);
    BerhasilSimpan("../$_SESSION[mnux].dosen.php?id=$jdwl[JadwalID]", 1);  
  }
  
}
function HapusDosenScript($jdwl) {
  echo <<<SCR
  <script>
  function HapusDosen(jdid) {
    if (confirm("Benar Anda akan menghapus dosen ini?")) {
      window.location = "../$_SESSION[mnux].dosen.php?gos=HapusDosen&id=$jdwl[JadwalID]&jdid="+jdid;
    }
  }
  </script>
SCR;
}
function HapusDosen($jdwl) {
  $jdid = $_REQUEST['jdid']+0;
  $s = "delete from jadwaldosen where JadwalDosenID = '$jdid' ";
  $r = _query($s);
  BerhasilHapus("../$_SESSION[mnux].dosen.php?id=$jdwl[JadwalID]&gos=", 1);
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}
function JadwalDosenScript($jdwl) {
  echo <<<SCR
  <script src="../$_SESSION[mnux].edit.script.js"></script>
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
  function CariDosen(ProdiID, frm) {
    if (eval(frm + ".Dosen.value != ''")) {
      showDosen(ProdiID, frm, eval(frm +".Dosen.value"), 'caridosen');
      toggleBox('caridosen', 1);
    }
  }
  </script>
SCR;
}
?>

</BODY>
</HEAD>
