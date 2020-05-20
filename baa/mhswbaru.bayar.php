<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 14 Agustus 2008

session_start();
include_once "../sisfokampus1.php";
include_once "../$_SESSION[mnux].lib.php";

HeaderSisfoKampus("Edit BIPOT");

// *** Parameters ***
$pmbid = sqling($_REQUEST['pmbid']);
$md = $_REQUEST['md']+0;
$bayar = $_REQUEST['bayar']+0;
$bipotmhsw = $_REQUEST['bipotmhsw']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($pmbid, $md, $bayar, $bipotmhsw);

// *** Functions ***
function Edit($pmbid, $md, $bayar, $bipotmhsw) {
  $pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $pmbid, '*');
  if ($md == 0) {
    $jdl = "Edit Pembayaran";
    $w = GetFields('bayarmhsw', 'BayarMhswID', $bayar, '*');
  }
  elseif ($md == 1) {
    $jdl = "Tambah Pembayaran";
    $w = array();
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Tampilkan
  BayarScript();
  CheckFormScript('RekeningID,Bank');
  $optrek = GetOption2('rekening', "concat(RekeningID, ' - ', Nama)", 'RekeningID', $w['RekeningID'],
    "KodeID='".KodeID."'", 'RekeningID');
  echo "<table class=box cellspacing=1 width=100%>
  <form name='frmBayar' action='../$_SESSION[mnux].bayar.php' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='pmbid' value='$pmbid' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='bayar' value='$bayar' />
  <input type=hidden name='bipotmhsw' value='$bipotmhsw' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Dibayar oleh:</td>
      <td class=ul1><sup>$pmbid</sup> <font size=+1>$pmb[Nama]</font></td>
      </tr>
  <tr><td class=inp>Ke Rekening:</td>
      <td class=ul1><select name='RekeningID'>$optrek</select></td>
      </tr>
  <tr><td class=inp>Dibayar Dari Bank:</td>
      <td class=ul1><input type=text name='Bank' value='$w[Bank]' size=30 maxlength=50 /> <a href='#' onClick=\"javascript:SetCash()\">Set CASH</a><br />
        Isi dengan `CASH` bila dibayarkan dengan Tunai.
      </td>
      </tr>
  <tr><td class=inp>No. Bukti Setor:</td>
      <td class=ul1><input type=text name='BuktiSetoran' value='$w[BuktiSetoran]'
        size=30 maxlength=50 /><br />
        Kosongkan jika pembayaran cash.
      </td>
      </tr>
  <tr><td class=inp>Catatan:</td>
      <td class=ul1><input type=text name='Keterangan' value='$w[Keterangan]'
        size=30 maxlength=50 />
      </td>
      </tr>
  </table>";
  TampilkanDetailBiaya($pmbid, $pmb, $md);
}
function TampilkanDetailBiaya($pmbid, $pmb, $md) {
  $s = "select bm.*, s.Nama as _saat,
      format(bm.Jumlah, 0) as JML,
      format(bm.Besar, 0) as BSR,
      (bm.Jumlah * bm.Besar) as SubTTL,
      ((bm.Jumlah * bm.Besar) - bm.Dibayar) as SISA,
	  bn.DipotongBeasiswa
    from bipotmhsw bm
      left outer join bipot2 b2 on b2.BIPOT2ID = bm.BIPOT2ID
      left outer join saat s on b2.SaatID = s.SaatID
	  left outer join bipotnama bn on bn.BipotNamaID = bm.BipotNamaID
	where bm.PMBID = '$pmbid'
      and bm.PMBMhswID = 0
      and bm.TahunID = '$pmb[PMBPeriodID]'
      and bm.KodeID = '".KodeID."'
    order by bm.TrxID, bm.BIPOTMhswID";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $n = 0;
  $sisa = 0;
  $potongan = 0;
  echo "<table class=box cellspacing=1 width=100%>";
  echo "<tr><th class=ttl colspan=7>Detail Biaya</th></tr>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>Nama Biaya<hr size=1 color=silver />Saat Bayar</th>
    <th class=ttl width=100>Jumlah
      <hr size=1 color=silver />
      Besar Biaya</th>
    <th class=ttl width=100>Sub Total</th>
    <th class=ttl width=100>Sdh Dibayar</th>
    <th class=ttl width=100>Dibayarkan</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    if($w['TrxID'] < 1)
	{	$potongan += $w['Jumlah'] * $w['Besar'];
	}
	
	else
	{	$n++;
		
		if($potongan > 0 and $w['DipotongBeasiswa'] == 'Y')
		{	if($potongan >= $w['SISA'])
			{	$Dibayar = $w['SubTTL'];
				$sisa += 0;
				$potongan -= $w['SISA'];
			}
			else
			{	$selisih = $w['SISA'] - $potongan;
				$Dibayar = $w['Dibayar'] + $selisih;
				$sisa += $selisih;
				$potongan = 0;
			}
		}
		else
		{	$Dibayar = $w['Dibayar'];
			$selisih = $w['SISA'];
			$sisa += $selisih;
		}
		
		if ($Dibayar >= $w['Jumlah'] * $w['Besar'] ) {
		  $ro = 'readonly=TRUE';
		  $c = "class=nac";
		}
		else {
		  $ro = '';
		  $c = "class=ul";
		}
		$Dibayar = number_format($Dibayar, 2, ',', '.');  
		$SubTTL = number_format($w['SubTTL'], 2, ',', '.');
		
		echo "<tr>
		  <td class=inp>$n</td>
		  <td $c><b>$w[Nama]</b>
			<br />
			<div align=right><sub>$w[_saat]</sub></div></td>
		  <td $c><sup>$w[JML] &times;</sup><br />
			<div align=right>$w[BSR]</div>
			</td>
		  <td $c align=right><a href='#' onClick=\"javascript:SetBayar($n, $selisih); HitungUlang();\">$SubTTL<a></td>
		  <td $c align=right>$Dibayar</td>
		  <td $c>
			<input type=hidden name='BYRID_$n' value='$w[BIPOTMhswID]' />
			<input type=text name='BYR_$n' value='0' size=10 maxlength=20 style='text-align:right' onChange='HitungUlang()' $ro />
			</td>
		  </tr>";
	  }
  }
  $_sisa = number_format($sisa);
  echo "<input type=hidden name='CNT' value='$n' />";
  echo "<tr><td bgcolor=silver colspan=10 height=1></td></tr>";
  echo "<tr>
    <td class=ul1 colspan=4 align=right>Total Yg Harus Dibayar:</td>
    <td class=ul1 align=right><font size=+1>$_sisa</font></td>
    <td class=ul1><input type=text name='TTLBYR' size=10 maxlength=50 readonly=TRUE style='text-align:right' /></td>
    </tr>";
  echo "<tr>
    <td class=ul1 colspan=10 align=center>
    <input type=submit  name='Simpan' value='Simpan' />
    <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
    </td></tr>";
  echo "</form>";
  echo "</table></p>";
   echo <<<ESD
	  <script>
	  function SetBayar(n, jml) {
		//alert("frmBayar.BYR_" + n + ".value = 'test';");
		eval("frmBayar.BYR_" + n + ".value = '" + jml +"';");
	  }
	  </script>
ESD;
  HitungUlang($n);
}
function Simpan($pmbid, $md, $bayar, $bipotmhsw) {
  $RekeningID = sqling($_REQUEST['RekeningID']);
  $Bank = sqling($_REQUEST['Bank']);
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $CNT = $_REQUEST['CNT']+0;
  // Cek jika tidak ada detailnya
  if ($CNT == 0)
    die(ErrorMsg('Error',
      "Tidak ada detail biaya.<br />
      Data tidak bisa disimpan.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  // Cek dulu jumlah yg dibayarkan
  $jml = 0;
  for ($i = 1; $i <= $CNT; $i++) {
    $jml += $_REQUEST['BYR_'.$i]+0;
  }
  if ($jml <= 0) {
    die(ErrorMsg('Error',
      "Jumlah yang Anda bayarkan: <font size=+1>$jml</font>.<br />
      Tidak ada yang perlu dibayarkan.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" /"));
  }
  $pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $pmbid, '*');
  // Oke, mulai simpan datanya
  // 1. Ambil nomer terakhir yang ada
  $BayarMhswID = GetNextBPM();
  // 2. Buat header bukti pembayaran
  $s = "insert into bayarmhsw
    (BayarMhswID, TahunID, KodeID,
    RekeningID, PMBID, TrxID, PMBMhswID,
    Bank, BuktiSetoran, Tanggal,
    Jumlah, Keterangan,
    LoginBuat, TanggalBuat)
    values
    ('$BayarMhswID', '$pmb[PMBPeriodID]', '".KodeID."',
    '$RekeningID', '$pmb[PMBID]', 1, 0,
    '$Bank', '$BuktiSetoran', now(),
    $jml, '$Keterangan',
    '$_SESSION[_LoginBuat]', now())";
  $r = _query($s);
  // 3. Simpan detailnya
  for ($i = 1; $i <= $CNT; $i++) {
    $_j = $_REQUEST['BYR_'.$i]+0;
    if ($_j > 0) { // Simpan
      $id = $_REQUEST['BYRID_'.$i]+0;
      $byrmhsw = GetFields('bipotmhsw', "BIPOTMhswID", $id, '*');
      // Simpan dulu detail pembayarannya
      $s = "insert into bayarmhsw2
        (BayarMhswID, BIPOTMhswID, BIPOTNamaID, Jumlah,
        LoginBuat, TanggalBuat)
        values
        ('$BayarMhswID', $id, '$byrmhsw[BIPOTNamaID]', $_j,
        '$_SESSION[_Login]', now())";
      $r = _query($s);
      // Update detail biayanya
      $s = "update bipotmhsw
        set Dibayar = Dibayar + $_j
        where BIPOTMhswID = $id";
      $r = _query($s);
    }
  }
  HitungUlangBIPOTPMB($pmbid);
  TutupScript($pmbid);
}
function BayarScript() {
  echo <<<SCR
  <script>
  function SetCash() {
    frmBayar.Bank.value = 'CASH';
    frmBayar.BuktiSetoran.value = 'CASH';
  }
  </script>
SCR;
}
function HitungUlang($n) {
  echo <<<SCR
  <script>
  function HitungUlang() {
    var i = 0;
    var ttl = 0;
SCR;
  for ($i = 1; $i <= $n; $i++) {
    echo "
    ttl = ttl + Number(frmBayar.BYR_" . $i . ".value);\n";
  }
  echo <<<SCR
    frmBayar.TTLBYR.value = ttl;
  }
  </script>

SCR;
}
function TutupScript($pmbid) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=MhswBaruEdt&PMBID=$pmbid';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
