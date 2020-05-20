<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 14 Agustus 2008

session_start();
include_once "../sisfokampus1.php";
// *** infrastruktur **
echo <<<SCR
  <script src="../$_SESSION[mnux].pindah.script.js"></script>
SCR;

HeaderSisfoKampus("Pindahkan Peserta Kuliah", 1);
// *** Parameters ***
$JadwalID = $_REQUEST['JadwalID'];
$jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
    left outer join mk mk on mk.MKID = j.MKID
    left outer join hari h on h.HariID = j.HariID
    left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
    ",
    "j.JadwalID", $JadwalID,
    "j.*, d.Nama as DSN, d.Gelar,
    prd.Nama as _PRD, prg.Nama as _PRG,
    mk.Sesi,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
    date_format(j.UASTanggal, '%w') as _UASHari,
    huas.Nama as HRUAS, h.Nama as HR,
    LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai,
    LEFT(j.JamMulai, 5) as _JM, LEFT(j.JamSelesai, 5) as _JS
    ");

if (empty($jdwl))
  die(ErrorMsg("Fatal Error",
    "Data jadwal kuliah tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));

// *** Main ***
TampilkanJudul("Pindahkan Peserta Kuliah");
$gos = (empty($_REQUEST['gos']))? 'Pindahkan' : $_REQUEST['gos'];
$gos($JadwalID, $jdwl);

// *** Functions ***
function Pindahkan($JadwalID, $jdwl) {
  $tengah = "<td class=ul><img src='../img/kanan.gif' /></td>";
  $ro = 'readonly=TRUE';
  echo <<<ESD
  <table class=box cellspacing=1 width=100%>
  <form name='frmPindah' action='../$_SESSION[mnux].pindah.php' method=POST>
  <input type=hidden name='JadwalID' value='$JadwalID' />
  <input type=hidden name='gos' value='Simpan' />
  <tr><th class=ttl colspan=2 width=50%>Jadwal Asal:</th>
      $tengah
      <th class=ttl colspan=2 width=50%>Pindah Ke:</th>
      </tr>
  
  <tr><td class=inp width=50>Matakuliah:</td>
      <td class=ul>$jdwl[Nama] <sup>$jdwl[MKKode]</sup></td>
      $tengah
      <td class=inp width=50>Matakuliah:</td>
      <td class=ul>
      <input type=hidden name='_JadwalID' />
      <input type=text name='_Nama' value='' $ro size=25 maxlength=50 />
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariJadwal('$jdwl[ProdiID]', 'frmPindah')" />Cari...</a> |
      <a href='#' onClick="javascript:frmPindah._Nama.value='';frmPindah._Dosen.value='';frmPindah._Jadwal.value='';frmPindah._Peserta.value=''">Reset</a>
      </div>
      </td>
      </tr>
  <tr><td class=inp>Dosen:</td>
      <td class=ul>$jdwl[DSN] <sup>$jdwl[Gelar]</sup></td>
      $tengah
      <td class=inp>Dosen:</td>
      <td class=ul><input type=text name='_Dosen' value='' $ro size=25 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Jadwal:</td>
      <td class=ul>$jdwl[HR], <sup>$jdwl[_JM]</sup>~<sub>$jdwl[_JS]</sub></td>
      $tengah
      <td class=inp>Jadwal:</td>
      <td class=ul><input type=text name='_Jadwal' value='' $ro size=25 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Peserta:</td>
      <td class=ul>$jdwl[JumlahMhsw] <sup>~$jdwl[Kapasitas]</sup></td>
      $tengah
      <td class=inp>Peserta:</td>
      <td class=ul><input type=text name='_Peserta' value='' $ro size=25 maxlength=50 /></td>
      </tr>
  <tr><td class=ul colspan=5 align=center>
      <input type=submit name='Pindahkan' value='Pindahkan Mahasiswa' />
      <input type=button name='Batal' value='Batal Pindah' onClick='window.close()' />
      </td>
      </tr>
  </table>
  <div class='box1' id='carijadwal'></div>
  
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
  function CariJadwal(ProdiID, frm) {
      showJadwal(ProdiID, frm, $JadwalID, 'carijadwal');
      toggleBox('carijadwal', 1);
  }
  </script>
ESD;
  PindahkanDetail($JadwalID, $jdwl);
}
function PindahkanDetail($JadwalID, $jdwl) {
  $s = "select k.MhswID, m.Nama, k.KRSID
    from krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
    where k.JadwalID = '$JadwalID'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  $all = "<a href='#' onClick=\"javascript:CheckAll()\" title='Beri centang semuanya'>&#8616;</a>
    <a href='#' onClick=\"javascript:CheckNone()\" title='Hilangkan centang semuanya'>o</a>";
  echo "<table class=bsc width=100%>
    <tr><th class=ttl colspan=5>Mahasiswa yang akan dipindahkan:</th>
    </tr>";
  echo "<tr>
    <th class=ttl width=30>#</th>
    <td class=ul align=center width=20>$all</td>
    <th class=ttl>NIM</th>
    <th class=ttl>Nama Mahasiswa</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
    <td class=inp width=30>$n</td>
    <td class=ul width=30 align=center><input type=checkbox name='KRSID_$n' value='$w[KRSID]' /></td>
    <td class=ul width=90>$w[MhswID]</td>
    <td class=ul>$w[Nama]&nbsp;</td>
    </tr>";
  }
  echo <<<ESD
  <tr><td class=ttl>&nbsp;</td><td class=ul align=center>$all</td><td class=ul colspan=5>&nbsp;</td></tr>
  <input type=hidden name='KRS' value='$n' />
  </table>
  </form>
  
ESD;
  PilihSemuaScript($n);
}
function PilihSemuaScript($n) {
  echo "<script>
  <!--
  function CheckAll() {
  ";
  for ($i = 1; $i <= $n; $i++) {
    echo "frmPindah.KRSID_$i.checked = true;";
  }
  echo "
  }
  function CheckNone() {
  ";
  for ($i = 1; $i <= $n; $i++) {
    echo "frmPindah.KRSID_$i.checked = false;";
  }
  echo "
  }
  //-->
  </script>";
}
function Simpan($JadwalID, $jdwl) {
  $_JadwalID = $_REQUEST['_JadwalID'];
  if (empty($_JadwalID))
    die(ErrorMsg('Error',
      "Anda belum memilih jadwal tujuan pemindahan.<br />
      Pilihlah salah satu jadwal.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Kembali' value='Kembali'
        onClick=\"location='../$_SESSION[mnux].pindah.php?JadwalID=$JadwalID'\" />
        <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  $KRS = $_REQUEST['KRS']+0;
  if ($KRS == 0)
    die(ErrorMsg('Error',
      "Matakuliah ini tidak ada peserta kuliahnya.<br />
      Anda tidak bisa memindahkan apa<sup>2</sup> ke kuliah lain.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  // Params
  $_j = GetFields('jadwal', 'JadwalID', $_JadwalID, '*');
  // Jika ada mhsw-nya
  $psn = array();
  for ($i = 1; $i <= $KRS; $i++) {
    $KRSID = $_REQUEST['KRSID_'.$i]+0;
    if ($KRSID > 0) {
      $_krs = GetFields('krs', 'KRSID', $KRSID, "MhswID, JadwalID, MKID, TahunID");
      // Cek dulu
      $ada = GetaField('krs', "KodeID='".KodeID."' and TahunID='$_krs[TahunID]' and JadwalID=$_j[JadwalID] and MhswID",
        $_krs['MhswID'], 'KRSID')+0;
      if ($ada > 0) {
        $psn[] = "Gagal dipindah: NIM $_krs[MhswID] karena telah mengambil MK ini";
      }
      else {
        $s = "update krs
          set JadwalID = '$_j[JadwalID]',
              MKID = '$_j[MKID]',
              MKKode = '$_j[MKKode]',
              Nama = '$_j[Nama]',
              SKS = '$_j[SKS]',
              Catatan = 'Pindahan dari kelas $jdwl[MKKode] - $jdwl[Nama] - $jdwl[HR] - $jdwl[_JM]-$jdwl[_JS] - $jdwl[DSN]',
              LoginEdit = '$_SESSION[_Login]',
              TanggalEdit = now()
          where KRSID = $KRSID";
        $r = _query($s);
      }
    }
  }
  // Refresh dulu
  HitungPeserta($JadwalID);
  HitungPeserta($_JadwalID);
  echo "<script>opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';</script>";
  // Tampilkan pesan kesalahan
  if (!empty($psn)) {
    $pesan = "<ol>";
    foreach ($psn as $p) {
      $pesan .= "<li>$p</li>";
    }
    $pesan .= "</ol>";
    echo ErrorMsg('Error',
      "Ada kegagalan pemindahan peserta kuliah. Berikut adalah pesan kesalahannya:
      $pesan
      <hr size=1 color=silver />
      Opsi: <input type=button name='Kembali' value='Kembali'
        onClick=\"location='../$_SESSION[mnux].pindah.php?JadwalID=$JadwalID'\" />
        <input type=button name='Tutup' value='Tutup' onClick='window.close()' />");
  }
  else {
    TutupScript();
  }
}
function HitungPeserta($id) {
  $jml = GetaField('krs', "NA='N' and JadwalID", $id, 'count(KRSID)')+0;
  $s = "update jadwal set JumlahMhsw = $jml where JadwalID=$id";
  $r = _query($s);
  return $jml;
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
