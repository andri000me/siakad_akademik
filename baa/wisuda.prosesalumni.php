<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 17 Desember 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Proses Alumni");

// *** Parameters ***
$thn = sqling($_REQUEST['thn']);

// *** Main ***
$gos = (empty($_REQUEST['gos']))? "fnKonfirmasi" : $_REQUEST['gos'];
$gos($thn);

// *** Functions ***
function fnKonfirmasi($thn) {
  TampilkanJudul("Proses Kelulusan & Alumni");
  $rowspan = 10;
  echo <<<ESD
  <p><table class=box cellspacing=1 align=center width=100%>
  <form action='../$_SESSION[mnux].prosesalumni.php' method=POST>
  <input type=hidden name='thn' value='$thn' />
  <input type=hidden name='gos' value='fnProses' />
  
  <tr>
      <td class=wrn width=1 rowspan=$rowspan></td>
      <td class=ul align=center>
      <font size=+1>Warning!</font>
      </td>
      <td class=wrn width=1 rowspan=$rowspan></td>
      </tr>
  <tr>
      <td class=ul>
      Anda akan melakukan proses berikut ini:
      <ol>
        <li>Mengeset status mhsw menjadi Lulus &raquo; Keluar.</li>
        <li>Menyalin data mhsw ke tabel alumni.</li>
      </ol>
      Data mahasiswa/wisudawan yang sudah diset sebagai Lulus &raquo; Keluar tidak dapat diedit lagi.<br />
      Pastikan data mahasiswa/wisudawan telah diisi dengan benar.
      </td>
      </tr>
  <tr>
      <td class=ul align=center>
      <input type=submit name='btnProses' value='Mulai Proses' />
      <input type=button name='btnBatal' value='Batalkan'
        onClick="window.close()" />
      </td>
      </tr>
  
  </form>
  </table>
ESD;
}
function fnProses($thn) {
  TampilkanJudul("Proses Kelulusan & Alumni");
  $_SESSION['_max'] = 250;
  $_SESSION['_grp'] = 0;
  $_SESSION['_cnt'] = 0;
  $_SESSION['_tot'] = GetaField('mhsw', "ProdiID='21' and KodeID", KodeID, "count(MhswID)")+0;
  
  echo <<<ESD
  <form name='frmWsd' />
  <p align=center>NIM/NPM:<br />
  <input type=text name='MhswID' value='' width=30 style='text-align:center' />
  </p>
  
  <p align=center>Nama Wisudawan:<br />
  <input type=text name='NamaMhsw' value='' width=30 style='text-align:center' />
  </p>
  
  <p align=center>Progress:<br />
  <input type=text name='Progress' value='' width=30 style='text-align:center' />
  </form>
  
  <script>
  function fnProgress(MhswID, Nama, Prgs) {
    frmWsd.MhswID.value = MhswID;
    frmWsd.NamaMhsw.value = Nama;
    frmWsd.Progress.value = Prgs + ' %';
  }
  function scriptSelesai(jml) {
    window.location = "../$_SESSION[mnux].prosesalumni.php?gos=fnSelesai&jml="+jml;
  }
  </script>
  
  <iframe name='frmProsesDetail' src="../$_SESSION[mnux].prosesalumni.php?thn=$thn&_max=$_max&_grp=$_grp&gos=fnProsesnya" width=100% height=200 frameborder=0>
  </iframe>
ESD;
}
function fnSelesai($thn) {
  $jml = $_REQUEST['jml'];
  $_jml = number_format($jml);
  echo "<script>opener.location=\"../index.php?mnux=$_SESSION[mnux]&gos=\";</script>";
  echo Konfirmasi("Proses Selesai",
    "Proses kelulusan & alumni sudah selesai.<br />
    Data yang telah diproses: <font size=+1>$_jml</font><br />
    <hr size=1 color=silver />
    <input type=button name='btnTutup' value='Tutup' onClick=\"window.close()\" />"); 
}
function fnProsesnya($thn) {
  $_tot = $_SESSION['_tot']+0;
  $_tot = ($_tot == 0)? 1 : $_tot;
  $_max = $_SESSION['_max'];
  $_max = (empty($_max))? 10 : $_max;
  $_grp = $_SESSION['_grp'];
  $_mulai = $_grp * $_max;
  
  $s = "select w.WisudawanID, w.MhswID, m.Nama, m.ProdiID,
    m.Alamat, m.Kota, m.KodePos, m.RT, m.RW,
    m.Propinsi, m.Negara, m.Telephone, m.Telepon, m.Handphone, m.Email
    from wisudawan w
      left outer join mhsw m on m.MhswID = w.MhswID and m.KodeID='".KodeID."'
    where w.KodeID='".KodeID."'
      and w.TahunID = '$thn'
    order by w.MhswID
    limit $_mulai, $_max";
  $r = _query($s);
  $_jml = _num_rows($r);
  if ($_jml > 0) {
    while ($w = _fetch_array($r)) {
      $_SESSION['_cnt']++;
      $persen = $_SESSION['_cnt'] * 100 / $_tot;
      $_persen = number_format($persen);
      echo "<script>
      parent.fnProgress('$w[MhswID]', '$w[Nama]', '$_persen');
      </script>";
      // Lakukan proses
      // 1. Export data ke alumni
      $ada = GetFields('alumni', "MhswID='$w[MhswID]' and KodeID", KodeID, "*");
      if (empty($ada)) {
        $Gelar = GetaField('prodi', "ProdiID='$w[ProdiID]' and KodeID", KodeID, 'Gelar');
        $s1 = "insert into alumni
          (MhswID, KodeID, Gelar,
          Alamat, Kota, KodePos, RT, RW,
          Propinsi, Negara, Telepon, Handphone, Email,
          TanggalBuat, LoginBuat)
          values
          ('$w[MhswID]', '".KodeID."', '$Gelar',
          '$w[Alamat]', '$w[Negara]', '$w[Telepon]', '$w[Handphone]', '$w[Email]',
          '$w[Propinsi]', '$w[Negara]', '$w[Telepon]', '$w[Handphone]', '$w[Email]',
          now(), '$_SESSION[_Login]')";
        $r1 = _query($s1);
      }
      
      // 2. Set data mhsw, StatusMhswID = 'L', Keluar = 'Y'
      $maxSesi = GetaField('khs', "MhswID='$w[MhswID]' and KodeID", KodeID, 'max(Sesi)');
      $IPK = GetaField('khs', "MhswID='$w[MhswID]' and KodeID", KodeID, 'IP');
      $s2 = "update mhsw
        set StatusMhswID = 'L',
            Keluar = 'Y',
            LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
        where MhswID = '$w[MhswID]' and KodeID='".KodeID."'";
      $r2 = _query($s2);
    }
    
    $tmr = 10;
    $_SESSION['_grp']++;
    echo <<<ESD
    <!--<p align=center><font size=+2>$_persen</font>%</p>-->
    <script>
    window.onload=setTimeout("window.location='../$_SESSION[mnux].prosesalumni.php?thn=$thn&_max=$_max&_grp=$_grp&gos=fnProsesnya'", $tmr);
    </script>
ESD;
  }
  else {
    $_cnt = $_SESSION['_cnt']+0;
    echo "<script>parent.scriptSelesai($_cnt)</script>";
  }
}
?>

</BODY>
</HTML>
