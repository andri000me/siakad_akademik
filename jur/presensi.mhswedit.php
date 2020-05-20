<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Presensi Mahasiswa", 1);

// *** Parameters ***
$pid = $_REQUEST['pid'];

// *** Main ***
TampilkanJudul("Presensi Mahasiswa");
$gos = (empty($_REQUEST['gos']))? 'DftrSiswa' : $_REQUEST['gos'];
$gos($pid);

// *** Functions ***
function DftrSiswa($pid) {
  $p = GetFields("presensi p
    left outer join jadwal j on p.JadwalID = j.JadwalID
    left outer join dosen d on d.Login = j.DosenID and d.KodeID='".KodeID."'
    left outer join hari h on h.HariID = date_format(p.Tanggal, '%w')
	left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID",
    "p.PresensiID", $pid,
    "p.*, j.MKKode, j.Nama, h.Nama as _HR,j.ProgramID as PRG,
    concat(d.Gelar1, ' ',d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
    date_format(p.Tanggal, '%d-%m-%Y') as _Tanggal,
    left(p.JamMulai, 5) as _JM, left(p.JamSelesai, 5) as _JS,
	jj.Nama as _NamaJenisJadwal, jj.Tambahan");
  TampilkanHeader($p);
  CekKRSMhsw($p);
  TampilkanPresensiMhsw($p);
}
function TampilkanHeader($p) {
  $TagTambahan = ($p['Tambahan'] == 'Y')? "<b>( $p[_NamaJenisJadwal] )</b>" : "";
  echo "<h4 align=center><font color=red>Nama-nama berikut adalah mahasiswa yang berhak mengikuti perkuliahan pada matakuliah ini.</font></h4>";
  echo "<table class=box cellspacing=1 width=100%>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul>$p[Nama] $TagTambahan<sup>$p[MKKode]</sup></td>
      <td class=inp>Dosen:</td>
      <td class=ul>$p[DSN]</td>
      </tr>
  <tr>
      <td class=inp>Pertemuan:</td>
      <td class=ul>#$p[Pertemuan] &#8594; $p[_HR] $p[_Tanggal]
        </td>
      <td class=inp>Jam:</td>
      <td class=ul><sup>$p[_JM]</sup> &#8594; <sub>$p[_JS]</sub></td>
      </tr>
  </table>";
}
function CekKRSMhsw($p) {
  $def = GetFields('jenispresensi', 'Def', 'Y', '*');
  $s = "select k.KRSID, k.MhswID, k.JadwalID,m.StatusAwalID,k.MKKode,k.TahunID,h.Sesi,m.ProgramID, p.FakultasID
    from krs k
    left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
    left outer join prodi p on p.ProdiID = m.ProdiID
    left outer join khs h on h.KHSID=k.KHSID
    where k.JadwalID = '$p[JadwalID]'
    order by k.MhswID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $ada = GetFields('presensimhsw', "PresensiID = '$p[PresensiID]' and NA='N' and KRSID", $w['KRSID'], '*');
    //$totByrMhs = (($w['StatusAwalID']=='B' || $w['StatusAwalID']=='D' || $w['StatusAwalID']=='P') && $w['Sesi'] > 2 && $w['ProgramID'] !='M' && $w['ProgramID'] !='P' && $w['FakultasID']!='08')? GetaField("bipotmhsw", "TambahanNama like '$p[MKKode]%' and TahunID='$w[TahunID]' and Dibayar>0 And MhswID", $w['MhswID'],'Dibayar')+0 : 1;
    //echo "$w[MKKode] = $ada[KRSID] - $totByrMhs<br>";
    //die(var_dump($w));
    //if (empty($ada) && $totByrMhs > 0) {
    if (empty($ada)) {
      $sp = "insert into presensimhsw
        (JadwalID, KRSID, PresensiID, 
        MhswID, JenisPresensiID, Nilai, NA)
        values
        ($p[JadwalID], $w[KRSID], $p[PresensiID],
        '$w[MhswID]', '$def[JenisPresensiID]', '$def[Nilai]', 'N')";
      $rp = _query($sp);
      // Hitung KRS
      $jml = GetaField('presensimhsw', 'KRSID', $w['KRSID'], "sum(Nilai)")+0;
      $sk = "update krs
        set _Presensi = $jml
        where KRSID = $w[KRSID]";
      $rk = _query($sk);
    }
  }
}
function TampilkanPresensiMhsw($p) {
echo <<<SCR
  </table>
  <script>
  function createRequestObject()
    {
      var ro;
      var browser = navigator.appName;
      if(browser == 'Microsoft Internet Explorer')
      {
        ro = new ActiveXObject('Microsoft.XMLHTTP');
      }
      else
      {
        ro = new XMLHttpRequest();
      }
      return ro;
    }
    var xmlhttp = createRequestObject();
  function SetPresensiMhsw(id) {
    var status = document.getElementById("PresensiMhsw_"+id).value;
    lnk = "../$_SESSION[mnux].mhswedit.save.php?id="+id+"&st="+status;
    if (!lnk) return;
      xmlhttp.open('get', lnk, true);
      xmlhttp.send(null);
      xmlhttp.onreadystatechange = function()
      {
      if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
      return false;
      }
  }
  </script>
SCR;
  $s = "select pm.*, mhsw.Nama,m.MKKode as KDMK, mhsw.ProgramID, k.TahunID, mhsw.TahunID as ThnMasuk
    from presensimhsw pm
      left outer join mhsw on mhsw.MhswID = pm.MhswID and mhsw.KodeID = '".KodeID."'
	  left outer join krs k on k.MhswID = pm.MhswID and k.JadwalID='$p[JadwalID]'
	  left outer join mk m on m.MKID=k.MKID
    where pm.PresensiID = '$p[PresensiID]'
	group by mhsw.MhswID
    order by pm.MhswID";
  $r = _query($s);
  $def = GetFields('jenispresensi', 'Def', 'Y', '*');
  $opt0 = GetOption2('jenispresensi', "Nama", 'JenisPresensiID', $def['JenisPresensiID'], '', 'JenisPresensiID');
  
  echo "<table class=box cellspacing=1 width=100%>";
  echo "<script>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=Edit&JadwalID=$p[JadwalID]';
    self.close();
    return false;
  }
  </script>";
  echo "<tr>
    <form action='../$_SESSION[mnux].mhswedit.php' method=POST>
    <input type=hidden name='gos' value='SimpanSemua' />
    <input type=hidden name='pid' value='$p[PresensiID]' />
    
    <td class=ul colspan=5>Set semua ke:
    <select name='Stt'>$opt0</select>
    <input type=submit name='SetStt' value='Set Status' />
    <input type=button name='Refresh' value='Refresh' 
      onClick=\"location='../$_SESSION[mnux].mhswedit.php?pid=$p[PresensiID]'\" />
    <input type=button name='Tutup' value='Tutup' onClick=\"ttutup()\" />
    </td>
    
    </form>
    </tr>";
  $n = 0;
  $arr = GetArrPre();
  while ($w = _fetch_array($r)) {
				$optpre = GetOptPre($arr, $w['JenisPresensiID']);
					$n++;
					echo "
					  <tr><td class=inp width=10>$n</td>
						  <td class=inp1 width=94><b>$w[MhswID]</b></td>
						  <td class=ul1 width=260>$w[Nama]</td>
						  <td class=ul><select id='PresensiMhsw_$w[PresensiMhswID]'
							onChange='javascript:SetPresensiMhsw($w[PresensiMhswID])'>$optpre</select></td>
					  </tr>";
  }
}
function SimpanSemua($pid) {
  $Stt = sqling($_REQUEST['Stt']);
  $Nilai = GetaField('jenispresensi', 'JenisPresensiID', $Stt, 'Nilai');
  $s = "select *
    from presensimhsw
    where PresensiID = '$pid' ";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    // update
    $s0 = "update presensimhsw set JenisPresensiID = '$Stt', Nilai = '$Nilai'
      where PresensiMhswID = $w[PresensiMhswID]";
    $r0 = _query($s0);
    // Hitung & update ke KRS
    $jml = GetaField('presensimhsw', 'KRSID', $w['KRSID'], "sum(Nilai)")+0;
    // Update KRS
    $s1 = "update krs
      set _Presensi = $jml
      where KRSID = $w[KRSID]";
    $r1 = _query($s1);
  }
  BerhasilSimpan("../$_SESSION[mnux].mhswedit.php?pid=$pid", 1);
}
function GetOptPre($arr, $id) {
  $opt = '';
  foreach($arr as $a) {
    $_a = explode('~', $a);
    $sel = ($id == $_a[0])? 'selected' : '';
    $opt .= "<option value='$_a[0]' $sel>$_a[1]</option>";
  }
  return $opt;
}
function GetArrPre() {
  $s = "select * from jenispresensi where NA='N' order by JenisPresensiID";
  $r = _query($s);
  $arr = array();
  $arr[] = '';
  while ($w = _fetch_array($r)) {
    $arr[] = "$w[JenisPresensiID]~$w[Nama]";
  }
  return $arr;
}
?>
