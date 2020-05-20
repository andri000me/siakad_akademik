<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Sejarah Semester Mahasiswa");

// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$mhsw = GetFields("mhsw m
      left outer join prodi prd on prd.ProdiID = m.ProdiID and prd.KodeID = '".KodeID."'
      left outer join program prg on prg.ProgramID = m.ProgramID and prg.KodeID = '".KodeID."' 
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."' ",
    "m.KodeID = '".KodeID."' and m.MhswID", $mhswid,
    "m.*,
    prd.Nama as _PRD, prg.Nama as _PRG,
    if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Nama, ' <sup>', d.Gelar, '</sup>')) as _DSN");
if (empty($mhsw))
  die(ErrorMsg("Error",
    "Mahasiswa dengan NPM <b>$mhswid</b> tidak ditemukan.<br />
    Masukkan NPM dengan benar.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));

// *** Main ***
TampilkanJudul("Sejarah Semester Mahasiswa");
$gos = (empty($_REQUEST['gos']))? "DftrSmt" : $_REQUEST['gos'];
$gos($mhswid, $mhsw);

// *** Functions ***
function DftrSmt($mhswid, $mhsw) {
  TampilkanHeaderMhsw($mhsw);
  
  $s = "select k.*,
    sm.Nama as STT
    from khs k
      left outer join statusmhsw sm on sm.StatusMhswID = k.StatusMhswID
    where k.KodeID = '".KodeID."'
      and k.MhswID = '$mhswid'
    order by k.TahunID";
  $r = _query($s);
  $n = 0;
  echo "<table class=box cellspacing=1 width=100%>";
  echo "<tr>
    <th class=ttl>Sesi</th>
    <th class=ttl>Thn Akd</th>
    <th class=ttl>Status</th>
    <th class=ttl>SKS</th>
    <th class=ttl>IPS</th>
    <th class=ttl>IPK</th>
    <th class=ttl>Biaya</th>
    <th class=ttl>Potongan</th>
    <th class=ttl>Pembayaran</th>
    <th class=ttl>Penarikan</th>
    <th class=ttl>Balance</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
      <td class=inp>$w[Sesi]</td>
      <td class=ul1>$w[TahunID]</td>
      <td class=ul1>$w[STT]<sup>$w[StatusMhswID]</sup></td>
      <td class=ul1 align=right>$w[SKS]</td>
      <td class=ul1 align=right>$w[IPS]</td>
      <td class=ul1 align=right>$w[IPK]</td>
      <td class=ul1 align=right>$w[Biaya]</td>
      <td class=ul1 align=right>$w[Potongan]</td>
      <td class=ul1 align=right>$w[Bayar]</td>
      <td class=ul1 align=right>$w[Tarik]</td>
      <td class=ul1 align=right>$w[Balance]</td>
      </tr>";
  }
  echo "</table></p>";
}
function TampilkanHeaderMhsw($mhsw) {
  echo "
  <table class=box cellspacing=0 align=center width=100%>
  <tr><td class=inp>Mahasiswa:</td>
      <td class=ul1><b>$mhsw[Nama]</b></td>
      <td class=inp>NIM:</td>
      <td class=ul1><b>$mhsw[MhswID]</b></td>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul1>$mhsw[_PRD] <sup>($mhsw[ProdiID])</sup></td>
      <td class=inp>Prg. Pendidikan:</td>
      <td class=ul1>$mhsw[_PRG] <sup>($mhsw[ProgramID])</sup></td>
      </tr>
  <tr><td class=inp>Dosen P.A.:</td>
      <td class=ul1>$mhsw[_DSN]</td>
      <td class=inp>Masa Studi:</td>
      <td class=ul1>
        $mhsw[TahunID] &minus; $mhsw[BatasStudi]
        </td>
        </tr>
  <tr>
      <td class=ul1 colspan=4 align=center>
      <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </td>
      </tr>
  </table>
  </p>";
}
?>
