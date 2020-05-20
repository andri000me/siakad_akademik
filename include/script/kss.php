<?php
// Author: Emanuel Setio Dewo
// 17 April 2006
// Selamat Ulang Tahun Ibu

// *** Function ***
function CetakKSS() {
  $s = "select khs.*, sm.Nama as STT
    from khs khs
      left outer join statusmhsw sm on khs.StatusMhswID=sm.StatusMhswID
    where khs.MhswID='$_SESSION[crmhswid]'
    order by khs.Sesi";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<tr><th class=ttl>Sesi</th>
    <th class=ttl>Tahun Akd</th>
    <th class=ttl>SKS</th>
    <th class=ttl>MK</th>
    <th class=ttl>Status</th>
    <th class=ttl>Biaya</th>
    <th class=ttl>Bayar</th>
    <th class=ttl>Tarik</th>
    <th class=ttl>Potongan</th>
    <th class=ttl>Balance</th>
    <th class=ttl>Gagal<br />KRS</th>
    <th class=ttl>Cetak</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    if ($w['TahunID'] == $_SESSION['tahun']) {
      $c = "class=ul";
      //$ctk = "<a href='kss.cetak.php?tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]' target=_blank><img src='img/printer.gif'></a>";
      $ctk1 = "<a href='?mnux=kss&gos=cekkss&tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]'>
        <img src='img/printer.gif'></a>";
    }
    else {
      $c = "class=ul";
      $ctk = "&nbsp;";
    }
    $bia = number_format($w['Biaya']);
    $byr = number_format($w['Bayar']);
    $trk = number_format($w['Tarik']);
    $pot = number_format($w['Potongan']);
    $balance = $w['Bayar'] - $w['Biaya'] + $w['Potongan'] - $w['Tarik'];
    $bal = number_format($balance);
    $cbal = ($bal < 0)? 'class=wrn' : 'class=ul';
    
    //$ggl = GetaField('krs', "KHSID", $w['KHSID'], "count(KRSID)")+0;
    $ggl = ($w['TahunID'] == $_SESSION['tahun'])? GetaField("krstemp", "NA='Y' and KHSID", $khsid, "count(KRSID)")+0 : "&nbsp;";
    $cggl = ($ggl > 0)? 'class=wrn' : 'class=ul';
    if ($w['TahunID'] == $_SESSION['tahun']) {
      $ctk = ($ggl > 0) ? "<img src='img/check.gif' title='Tidak dapat dicetak karena ada KRS gagal.'>" : 
        "<a href='kss.cetak.php?tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]'><img src='img/printer.gif'></a>";
      $ctk1 = ($ggl > 0) ? "<img src='img/check.gif' title='Tidak dapat dicetak karena ada KRS gagal.'>" :
        "<a href='?mnux=kss&gos=cekkss&tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]'><img src='img/printer.gif'></a>";
    } else $ctk = '&nbsp;';
    echo "<tr><td class=inp>$w[Sesi]</td>
      <td $c>$w[TahunID]</td>
      <td $c align=right>$w[TotalSKS]</td>
      <td $c align=right>$w[JumlahMK]</td>
      <td $c>$w[STT]</td>
      <td $c align=right>$bia</td>
      <td $c align=right>$byr</td>
      <td $c align=right>$pot</td>
      <td $c align=right>$trk</td>
      <td $cbal align=right><b>$bal</b></td>
      <td $cggl align=right><b>$ggl</b></td>
      <td $c align=center>$ctk</td>
      </tr>";
  }
  echo "</table></p>";
}
function cekkss() {
  $mhswid = $_REQUEST['mhswid'];
  $khsid = $_REQUEST['khsid'];
  $tahun = $_REQUEST['tahun'];
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  $balance = $khs['Biaya'] - $khs['Bayar'] + $khs['Tarik'] - $khs['Potongan'];
  // cek sudah lunas atau belum
  if ($balance > 0) {
    $acc = GetFields('keusetup', 'NA', 'N', '*');
    $htg = GetaField('bipotmhsw', "MhswID='$mshwid' and TahunID='$tahun' and BIPOTNamaID", $acc['HutangNext'], "Jumlah*Besar")+0;
    if ($htg > 0) {
      $Nama = GetaField('bipotnama', 'BIPOTNamaID', $acc['HutangNext'], 'Nama');
      $_htg = number_format($htg);
      echo ErrorMsg("Belum Lunas",
        "Mahasiswa tidak dapat mencetak KSS karena masih memiliki <b>$Nama</b> sebesar <b>$_htg</b>");
      CetakKSS();
    }
    else cetakkssgo();
  }
  else {
    // cek apakah ada KRS gagal?
    $ggl = GetaField("krs", "NA='Y' and KHSID", $khsid, "count(KRSID)")+0;
    if ($ggl > 0) {
      echo ErrorMsg("Tidak Dapat Dicetak",
        "KSS tidak dapat dicetak karena masih ada <b>$ggl</b> matakuliah yang gagal KRS.<br />
        Hapus terlebih dahulu matakuliah yg gagal baru kemudian cetak KSS.");
      CetakKSS();
    }
    else {
      cetakkssgo();
    }
  }
}
function cetakkssgo() {
  echo "<script>
    new1 = window.open('kss.cetak.php?tahun=$_REQUEST[tahun]&khsid=$_REQUEST[khsid]&mhswid=$_REQUEST[mhswid]');
  </script>";
  CetakKSS();
  echo "<script>new1.close();</script>";
}

// *** Parameters ***
$crmhswid = GetSetVar('crmhswid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? "donothing" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Cetak Kartu Studi Semester (KSS)");
TampilkanPencarianMhswTahun('kss', 'CetakKSS', 1);
if (!empty($crmhswid)) {
  $w = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID", 
    "m.MhswID", $crmhswid, 
    "m.*, prd.Nama as PRD, prg.Nama as PRG, bpt.Nama as BPT");
  if (!empty($w)) {
    include_once "mhsw.hdr.php";
    TampilkanHeaderBesar($w, 'kss', '', 0);
    $gos();
  }
  else echo ErrorMsg("Mahasiswa Tidak Ditemukan",
    "Mahasiswa dengan NPM <font size=+1>$crmhswid</font> tidak ditemukan.");
}
?>
