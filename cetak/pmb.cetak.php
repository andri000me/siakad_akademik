<?php
// Author: Emanuel Setio Dewo
// 06 Feb 2006

// *** Functions ***
include "../sisfokampus.php";
function PilihProdiPMB($lnk='pmb.cetak') {
  global $pmbaktif;
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)",
    'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  if (empty($_SESSION['prodi'])) {
    $strjml = '';
  }
  else {
    $jml = GetaField('pmb', "PMBPeriodID='$_SESSION[tahunpmb]' and ProdiID", $_SESSION['prodi'], "count(PMBID)");
    $strjml = "<tr><td class=inp1>Jumlah Peserta Ujian:</td><td class=ul><b>$jml</b></td></tr>";
  }
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='$lnk'>
  <tr><td class=inp1>Periode :</td><td class=ul><input type=text name=tahunpmb value='$_SESSION[tahunpmb]' size=10></td></tr>
  <tr><td class=inp1>Program Studi:</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprodi</select></td></tr>
  $strjml
  </form></table></p>";
}
function DaftarProdiUSM() {
  global $pmbaktif;
  $s = "select pu.*, pmu.Nama, date_format(pu.TanggalUjian, '%d/%m/%Y %H:%i') as TGL
    from prodiusm pu
    left outer join pmbusm pmu on pu.PMBUSMID=pmu.PMBUSMID
    where pu.ProdiID='$_SESSION[prodi]' and pu.PMBPeriodID='$_SESSION[tahunpmb]'
    order by pu.Urutan";
  $r = _query($s);
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl>Urutan</th><th class=ttl>Kode</th>
  <th class=ttl>Matakuliah Ujian</th>
  <th class=ttl>Soal</th>
  <th class=ttl>Ruang</th>
  <th class=ttl>Tanggal</th>
  <th class=ttl>Label</th>
  <th class=ttl title='Daftar Hadir Ujian'>DHU</th>
  </tr>";
  while ($w = _fetch_array($r)) {
    $ctklbl = "<a href='cetak/pmb.cetak.label.php?prodi=$_SESSION[prodi]&prodiusmid=$w[ProdiUSMID]&pmbusmid=$w[PMBUSMID]&pmbaktif=$_SESSION[tahunpmb]'><img src='img/printer.gif' border=0></a>";
    $ctkdhu = "<a href='cetak/pmb.cetak.dhu.php?prodi=$_SESSION[prodi]&prodiusmid=$w[ProdiUSMID]&pmbusmid=$w[PMBUSMID]&pmbaktif=$_SESSION[tahunpmb]'><img src='img/printer.gif' border=0></a>";
    echo "<tr><td class=inp1>$w[Urutan]</td>
    <td class=ul>$w[PMBUSMID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul align=right>$w[JumlahSoal]</td>
    <td class=ul>$w[RuangID]</td>
    <td class=ul>$w[TGL]</td>
    <td class=ul align=center title='Cetak Label Meja'>$ctklbl</td>
    <td class=ul align=center title='Cetak Daftar Hadir Ujian'>$ctkdhu</td>
    </tr>";
  }
  echo "</table></p>";
}
function TampilkanCetakLabel($pmbid='') {
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='pmb.cetak.label.php' method=POST>
  <tr><td class=inp1>Cetak Label utk No. PMB :</td>
    <td class=ul><input type=text name='pmbid' value='$pmbid'>
    <input type=submit name='Cetak' value='Cetak'></td></tr>
  </table></p>";
}

// *** Parameters ***
$pmbaktif = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
$pmbaktif = GetSetVar('pmbaktif', $pmbaktif);
$tahunpmb = GetSetVar('tahunpmb', $pmbaktif);
$prodi = GetSetVar('prodi');
$pmbid = GetSetVar('pmbid');
$gos = (empty($_REQUEST['gos']))? 'DaftarProdiUSM' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul ("Cetak PMB");
PilihProdiPMB();
TampilkanCetakLabel($pmbid);
if (!empty($prodi)) $gos();
?>