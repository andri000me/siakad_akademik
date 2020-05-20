<?php

include_once "$_SESSION[mnux].lib.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$MhswID = GetSetVar('MhswID');
BayarMhswScript();

// *** Main ***
TampilkanJudul("Her-Registrasi & Pembayaran Mhsw");
TampilkanHeaderBayar();

$gos = (empty($_REQUEST['gos']))? 'DetailMhsw' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeaderBayar() {
  $print = (empty($_SESSION['TahunID']))? "" : "<a href='#' onClick=\"javascript:CetakTagihan('$_SESSION[TahunID]', '$_SESSION[MhswID]')\" title='Tagihan Administrasi'><img src='img/printer2.gif' /></a>";
  echo "
	<script>
	  function CetakTagihan(thn, mhsw) {
		lnk = '$_SESSION[mnux].tagihan.bank.php?TahunID='+thn+'&MhswID='+mhsw;
		win2 = window.open(lnk, '', 'width=800, height=600, scrollbars, status');
		if (win2.opener == null) childWindow.opener = self;
	  }
    </script>
    <table class=box cellspacing=0 align=center width=800 />
    <form action='?' method=POST>
    <input type=hidden name='gos' value='DetailMhsw' />
    <tr><td class=wrn width=2></td>
        <td class=inp width=90>Thn Akd:</td>
        <td class=ul1 width=100>
          <input type=text name='TahunID' value='$_SESSION[TahunID]'
            size=5 maxlength=10 />
          </td>
        <td class=inp width=80>NPM:</td>
        <td class=ul1>
          <input type=text name='MhswID' value='$_SESSION[MhswID]'
            size=12 maxlength=50 />
          <input type=submit name='CariMhsw' value='Ambil Data' />
          </td>
		<td class=ul1 width=30>$print</td>
        </tr>
    
    </form>
    </table>";
}
function DetailMhsw() {
  if (!empty($_SESSION['MhswID']) && !empty($_SESSION['TahunID'])) {
    $mhsw = CekDataMhsw($_SESSION['MhswID']);
    if (!empty($mhsw)) {
      $khs = CekSemesterMhsw($_SESSION['TahunID'], $_SESSION['MhswID']);
      if (!empty($khs)) {
        TampilkanDataMhsw($mhsw, $khs);
        TampilkanBIPOTMhsw($mhsw, $khs);
        TampilkanBayarMhsw($mhsw, $khs);
      }
    }
  }
}
function CekDataMhsw($mhswid) {
  $mhsw = GetFields("mhsw m
      left outer join prodi prd on prd.ProdiID = m.ProdiID and prd.KodeID = '".KodeID."'
      left outer join program prg on prg.ProgramID = m.ProgramID and prg.KodeID = '".KodeID."' 
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."' 
      left outer join statusmhsw sm on sm.StatusMhswID = m.StatusMhswID",
    "m.KodeID = '".KodeID."' and m.MhswID", $mhswid,
    "m.*,
    prd.Nama as _PRD, prg.Nama as _PRG,
    sm.Nama as _STT, sm.Keluar,
    if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Nama, ' <sup>', d.Gelar, '</sup>')) as _DSN");
  if (empty($mhsw))
    echo ErrorMsg('Error',
      "Mahasiswa dengan NPM <b>$mhswid</b> tidak ditemukan.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.");
  return $mhsw;
}
function CekSemesterMhsw($tahunid, $mhswid) {
  $khs = GetFields("khs k",
    "k.KodeID = '".KodeID."' and k.TahunID = '$tahunid' and k.MhswID",
    $mhswid, "k.*");
  if (empty($khs))
    echo Konfirmasi("Data Semester",
      "Data Tahun Akademik <b>$tahunid</b> untuk mahasiswa ini belum dibuat.<br />
      Apakah mahasiswa akan didaftarkan untuk Tahun Akademik ini?
      <hr size=1 color=silver />
        <input type=button name='BuatData' value='Daftarkan Mhsw'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos=DaftarkanSemesterMhsw&MhswID=$mhswid&TahunID=$tahunid&BypassMenu=1'\" />
        <input type=button name='Batalkan' value='Jangan Daftarkan'
        onClick=\"location='?mnux=$_SESSION[mnux]&MhswID='\" />
        &raquo;
        <input type=button name='LihatSemester' value='Lihat Sejarah Semester Mhsw'
        onClick=\"javascript:InquirySemesterMhsw('$mhswid')\" />");
  return $khs;
}
function TampilkanDataMhsw($mhsw, $khs) {
  TampilkanHeaderMhsw($mhsw, $khs);
}
function BuatSummaryKeu($mhsw, $khs) {
  $_Biaya = number_format($khs['Biaya']);
  $_Potongan = number_format($khs['Potongan']);
  $_Bayar = number_format($khs['Bayar']);
  $_Tarik = number_format($khs['Tarik']);
  $Sisa = $khs['Biaya'] - $khs['Potongan'] + $khs['Tarik'] - $khs['Bayar'];
  $_Sisa = number_format($Sisa);
  $color = ($Sisa > 0)? 'color=red' : '';
  $NamaBipot = GetaField('bipot', 'BIPOTID', $mhsw['BIPOTID'], 'Tahun');
  $NamaBipot = (empty($NamaBipot))? 'Blm diset' : $NamaBipot;
  return <<<ESD
  <table class=box cellspacing=1 width=100%>
  <tr><td class=inp width=15%>Bipot</td>
      <td class=inp width=15%>Total Biaya</td>
      <td class=inp width=15%>Total Potongan</td>
      <td class=inp width=15%>Total Bayar</td>
      <td class=inp width=15%>Total Penarikan</td>
      <td class=inp>SISA</td>
      </tr>
  <tr><td class=ul align=right>$NamaBipot
      <a href='#' onClick="javascript:EditBipot('$mhsw[MhswID]')"><img src="img/edit.png" /></a>
      </td>
      <td class=ul align=right>$_Biaya</td>
      <td class=ul align=right>$_Potongan</td>
      <td class=ul align=right>$_Bayar</td>
      <td class=ul align=right>$_Tarik</td>
      <td class=ul align=right><font size=+1 $color>$_Sisa</font></td>
  </table>
  
  <script>
  function EditBipot(mhswid) {
    lnk = "$_SESSION[mnux].bipotmhsw.php?MhswID="+mhswid;
    win2 = window.open(lnk, "", "width=400, height=300, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
ESD;
}
function TampilkanHeaderMhsw($mhsw, $khs) {
  $summary = BuatSummaryKeu($mhsw, $khs);
  $tombol = <<<ESD
    <i>Lakukan proses BIPOT terlebih dahulu untuk memastikan biaya<sup>2</sup> mahasiswa</i><br />
    <input type=button name='Proses' value='Proses BIPOT'
      onClick="location='?mnux=$_SESSION[mnux]&BypassMenu=1&gos=ProsesBIPOT&MhswID=$mhsw[MhswID]&TahunID=$khs[TahunID]'" />
    <input type=button name='HapusSemua' value='Hapus Semua BIPOT' 
      onClick="javascript:BIPOTDELALLCONF('$mhsw[MhswID]','$khs[TahunID]')" />
    <input type=button name='TambahBipot' value='Tambah Bipot'
      onClick="javascript:BIPOTEdit('$mhsw[MhswID]', '$khs[TahunID]', 1, 0)" />
    <input type=button name='TambahBayar' value='Tambah Pembayaran'
      onClick="javascript:ByrEdit('$mhsw[MhswID]', $khs[KHSID], 1, '')" />
    <input type=button name='btnTarikan' value='Penarikan'
      onClick="javascript:fnTarikan('$mhsw[MhswID]', $khs[KHSID], 1, '')" />
	<input type=button name='btnHistoryBeasiswa' value='Sejarah Beasiswa'
	  onClick="javascript:fnHistoryBeasiswa('$mhsw[MhswID]', $khs[KHSID], 1)" />
    <input type=button name='LihatSemester' value='Lihat Sejarah Semester Mhsw'
        onClick="javascript:InquirySemesterMhsw('$mhsw[MhswID]')" /><br>
  <input type=button name='TagihanBank' value='Buat Tagihan Bank'
        onClick="javascript:BuatTagihanBank('$mhsw[MhswID]','$khs[TahunID]')" />
ESD;
//function ByrEdit(mhswid, khsid, md, bayarid) {
  $Stt = GetaField('statusmhsw', 'StatusMhswID', $khs['StatusMhswID'], 'Nama');
  	$totBayar= GetaField ("khs","MhswID",$mhsw['MhswID'],'sum(Bayar)');
	$totBiaya= GetaField ("khs","MhswID",$mhsw['MhswID'],'sum(Biaya)');
	$totTarik= GetaField ("khs","MhswID",$mhsw['MhswID'],'sum(Tarik)');
	$JenjangID=GetaField ("prodi","ProdiID",$mhsw['ProdiID'],'JenjangID');
	$maxSesi= GetaField ("biayamhswref","JenjangID='$JenjangID' and ProgramID='$mhsw[ProgramID]' and TahunID",$mhsw['TahunID'],'max(Sesi)');
	$tanggungan= GetaField ("biayamhswref","JenjangID='$JenjangID' and ProgramID='$mhsw[ProgramID]' and TahunID",$mhsw['TahunID'],'sum(Biaya)');
	$totPot= GetaField ("khs","MhswID",$mhsw['MhswID'],'sum(Potongan)');
	$_totBiaya = number_format($tanggungan, 0, ',', '.');
	$totBayPot = $totBayar-$totPot;
	$_totBayPot = number_format($totBayPot, 0, ',', '.');
	$totTunggakan = $totBiaya - $totBayar - $totPot + $totTarik;
	$_totTunggakan = number_format($totTunggakan, 0, ',', '.');
  $khslalustring = '';
  if ($mhsw[ProgramID]=='N') {
	$bayarmhs_ref="<td class=inp>Tanggungan s/d Sesi $maxSesi:</td>
     					<td class=ul1 valign='middle'><font size=+1>$_totBiaya</font><font size=+1> / $_totBayPot</font> 
						<sup>(Pembayaran s/d Sesi ini)</sup></td>";
						}
	else
	{ 	$bayarmhs_ref="<td colspan=2 class=ul1>&nbsp;</td>";
						}
  if($khs[Sesi] > 1)
  {
  	$sesilalu = $khs[Sesi]-1;
	if ($sesilalu>0) {
  	$khslalu = GetFields('khs', "Sesi='$sesilalu' and MhswID='$mhsw[MhswID]' and KodeID", KodeID, 'IP, IPS, TahunID, SKS'); 
	
      $khslalustring = "<tr><td class=inp>SKS Tahun $khslalu[TahunID] / Minimum:</td>
     					<td class=ul1>$khslalu[SKS]</td>
						<td class=inp>IP $khslalu[TahunID] / IPK :</td>
						<td class=ul1>$khslalu[IPS] / $khslalu[IP]</td>
						<tr>$bayarmhs_ref
						<td class=inp>Tunggakan :</td>
						<td class=ul1 valign='middle'><font size=+1 color=red>$_totTunggakan</font></td>
      				</tr>";
	}
	else {
	$sesilalu = $khs[Sesi]-1;
	$khslalu = GetFields('khs', "Sesi='$sesilalu' and MhswID='$mhsw[MhswID]' and KodeID", KodeID, 'IP, IPS, TahunID, SKS'); 
      $khslalustring = "<tr><td class=inp>SKS Tahun $khslalu[TahunID] / Minimum:</td>
     					<td class=ul1>$khslalu[SKS]</td>
						<td class=inp>IPS / IP Tahun $khslalu[TahunID]:</td>
						<td class=ul1>$khslalu[IPS] / $khslalu[IP]</td>
      				</tr>
					<tr>$bayarmhs_ref
						<td class=inp>Tunggakan :</td>
						<td class=ul1 valign='middle'><font size=+1 color=red>$_totTunggakan</font></td>
      				</tr>";
	}
	
  }
  else
  {	$NilaiSekolah = GetaField('pmb', "MhswID='$mhsw[MhswID]' and KodeID", KodeID, 'NilaiSekolah');
      $NilaiSekolah = (empty($NilaiSekolah))? "<b><tidak ada data></b>" : $NilaiSekolah;
      $khslalustring = "<tr><td class=inp>Nilai Sekolah:</td>
      				<td class=ul1>$NilaiSekolah</td></tr>
					<tr>$bayarmhs_ref
						<td class=inp>Tunggakan :</td>
						<td class=ul1 valign='middle'><font size=+1 color=red>$_totTunggakan</font></td>
      				</tr>";

  }
  echo "
  <table class=box cellspacing=1 align=center width=800>
  <tr><td class=inp width=150>Mahasiswa:</td>
      <td class=ul1><b>$mhsw[Nama]</b> <sup>($mhsw[_STT])</sup></td>
      <td class=inp width=100>NPM:</td>
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
  <tr><td class=inp>Jumlah SKS:</td>
      <td class=ul1>$khs[SKS] <sup>~$khs[MaxSKS]</sup></td>
      <td class=inp>Status Smt:</td>
      <td class=ul1>$Stt <sup>($khs[StatusMhswID])</sup>
      </td></tr>
  $khslalustring
  <tr><td colspan=4>
      $summary
      </td></tr>
  <tr><td class=ul1 colspan=4 align=center>
      $tombol
      </td></tr>
  </table>";
  // Cek apakah mhsw sudah keluar?
  if ($mhsw['Keluar'] == 'Y')
    die(ErrorMsg('Error',
      "Mahasiswa sudah keluar.<br />
      Status: <b>$mhsw[_STT]</b> <sup>($mhsw[StatusMhswID])</sup>.<br />
      Data sudah tidak bisa diakses lagi.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut."));
}
function DaftarkanSemesterMhsw() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $TahunID = sqling($_REQUEST['TahunID']);
  $ada = GetaField('khs', "KodeID='".KodeID."' and TahunID='$TahunID' and MhswID",
    $MhswID, 'KHSID')+0;
  if ($ada > 0) {
    echo ErrorMsg("Error",
      "Mahasiswa <b>$MhswID</b> sudah terdaftar utk Tahun <b>$TahunID</b>.<br />
      Silakan mengecek data mahasiswa, mungkin ada kesalahan.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
  }
  else {
    $mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID,
      "Nama, ProgramID, ProdiID, BIPOTID, StatusMhswID");
    // Ambil semester terakhir mhsw
    $_sesiakhir = GetaField('khs', "KodeID='".KodeID."' and MhswID", $MhswID,
      "max(Sesi)");
	  
    if ($_sesiakhir > 0 and $_sesiakhir <= 1) {
			//mulai
			$bentrok = GetaField('khs',"MhswID='$MhswID' AND Sesi",$_sesiakhir,'COUNT(Sesi)');
			if ($bentrok > 1) {
				$s = "select KHSID,Sesi From khs where MhswID='$MhswID' AND Sesi='$_sesiakhir' order by KHSID DESC limit 1";
				$r = _query($s);
				while ($palingBaru = _fetch_array($r)) {
				$sesibaru = $palingBaru['Sesi']+1;
				$upd = "UPDATE khs set Sesi='$sesibaru' where KHSID='$palingBaru[KHSID]'";
				$update = _query($upd);
				$_sesiakhir = GetaField('khs', "KodeID='".KodeID."' and MhswID", $MhswID,
      									"max(Sesi)");
				}
			}
	// end;
	      $Sesi = $_sesiakhir+1;
     $MaxSKS = GetaField('prodi', "KodeID='".KodeID."' and ProdiID",
        $mhsw['ProdiID'], 'DefSKS');
    }
	if ($_sesiakhir > 1) {
		//mulai
			$bentrok = GetaField('khs',"MhswID='$MhswID' AND Sesi",$_sesiakhir,'COUNT(Sesi)');
			if ($bentrok > 1) {
				$s = "select KHSID,Sesi From khs where MhswID='$MhswID' AND Sesi='$_sesiakhir' order by KHSID DESC limit 1";
				$r = _query($s);
				while ($palingBaru = _fetch_array($r)) {
				$sesibaru = $palingBaru['Sesi']+1;
				$upd = "UPDATE khs set Sesi='$sesibaru' where KHSID='$palingBaru[KHSID]'";
				$update = _query($upd);
				$_sesiakhir = GetaField('khs', "KodeID='".KodeID."' and MhswID", $MhswID,
      									"max(Sesi)");
				}
			}
	// end;
			$_genapganjil=$_sesiakhir-1;
	      $_khs = GetFields('khs', "KodeID='".KodeID."' and MhswID='$MhswID' and Sesi", 
        $_genapganjil, '*');
      $Sesi = $_sesiakhir+1;
      $MaxSKS = GetaField('maxsks', "KodeID='".KodeID."' 
        and DariIP <= $_khs[IPS] and $_khs[IPS] <= SampaiIP
        and ProdiID", $mhsw['ProdiID'], 'SKS')+0;
    }
	else {
      $Sesi = 1;
      $MaxSKS = GetaField('prodi', "KodeID='".KodeID."' and ProdiID",
        $mhsw['ProdiID'], 'DefSKS');
    }
    //$StatusMhswID = GetaField('statusmhsw', 'Def', 'Y', 'StatusMhswID');
    //$StatusMhswID = (empty($StatusMhswID))? 'A' : $StatusMhswID;
	$StatusMhswID = $mhsw['StatusMhswID'];
	
    // Simpan
    $s = "insert into khs
      (TahunID, KodeID, ProgramID, ProdiID, 
      MhswID, StatusMhswID,
      Sesi, IP, MaxSKS,
      LoginBuat, TanggalBuat, NA)
      values
      ('$TahunID', '".KodeID."', '$mhsw[ProgramID]', '$mhsw[ProdiID]',
      '$MhswID', '$StatusMhswID',
      '$Sesi', 0, $MaxSKS,
      '$_SESSION[_Login]', now(), 'N')";
    $r = _query($s);
    BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 100);
  }
}
function TampilkanBIPOTMhsw($mhsw, $khs) {
  BIPOTScript();
  $s = "select bm.*, s.Nama as _saat,
    format(bm.Jumlah, 0) as JML,
	Left(bm.TambahanNama,250) as TambahanNama,
    format(bm.TrxID*bm.Besar, 0) as BSR,
    format(bm.Dibayar, 0) as BYR,
	date_format(bm.TanggalBuat, '%d-%m-%Y %H:%i') as TGLBuat,date_format(bm.TanggalEdit, '%d-%m-%Y %H:%i') as TGLEdit,
	bm.LoginBuat as LGNBuat, bm.LoginEdit as LGNEdit
    from bipotmhsw bm
	  left outer join bipot2 b2 on b2.BIPOT2ID = bm.BIPOT2ID
      left outer join saat s on b2.SaatID = s.SaatID
    where bm.PMBMhswID = 1
      and bm.NA = 'N'
	  and bm.BIPOT2ID != 0
      and bm.KodeID = '".KodeID."'
      and bm.MhswID = '$mhsw[MhswID]'
      and bm.TahunID = '$khs[TahunID]'
    order by b2.Prioritas, bm.TrxID DESC, bm.BIPOTMhswID ";
  $r = _query($s); $n = 0;
echo "
  <table class=box cellspacing=1 align=center width=800>";
  echo "<tr>
    <th class=ttl colspan=10>Daftar Biaya & Potongan (BIPOT)</th>
    </tr>";
  echo "<tr>
    <th class=ttl colspan=2>#</th>
    <th class=ttl>Nama Biaya/Potongan</th>
    <th class=ttl>Jumlah &times;<br />Besar</th>
    <th class=ttl>Total</th>
    <th class=ttl>Dibayar</th>
    <th class=ttl width=80>Catatan</th>
    <th class=ttl>&times;</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $sub = $w['Jumlah'] * $w['Besar'] * $w['TrxID'];
    $_sub = number_format($sub);
    $ttl += $sub;
    $byr += $w['Dibayar'];
    
      $del = "<a href='#' onClick=\"BIPOTDELCONF($w[BIPOTMhswID], '$mhsw[MhswID]', '$khs[TahunID]', '$w[TambahanNama]','$w[Dibayar]')\"><img src='img/del.gif' /></a>";
    
    $ctt = TRIM($w['Catatan']);
    $ctt = str_replace("\r", "<br />", $ctt);
	$Edit = GetaField('karyawan',"Login",$w[LGNEdit],'Nama');
	$Buat1 = GetaField('karyawan',"Login",$w[LGNBuat],'Nama');
	$log1="Dibuat: <font color=#00CCCC><b>$Buat1</b></font> ($w[TGLBuat])";
	if (!empty($w[LGNEdit])) { $log2="Diedit: <font color=#00CCCC><b>$Edit</b></font> $w[TGLEdit]"; }
    echo "<tr>
      <td class=inp width=15>$n</td>
      <td class=ul width=10>
        <a href='#' onClick=\"javascript:BIPOTEdit('$mhsw[MhswID]', '$khs[TahunID]', 0, $w[BIPOTMhswID])\"><img src='img/edit.png' /></a>
        </td>
      <td class=ul>
        $w[Nama] <br />
        <sup>$w[TambahanNama]</sup>
		<div align=right><sub>$w[_saat]</sub></div>
        </td>
      <td class=ul norwap>
        <sup>$w[JML] &times;</sup><br />
        <div align=right>$w[BSR]</div>
        </td>
      <td class=ul align=right nowrap>$_sub</td>
      <td class=ul align=right nowrap>
        $w[BYR] <!--
        <a href='#' onClick=\"javascript:ByrEdit('$pmb[PMBID]', 1, 0, $w[PMBMhswID])\"><img src='img/edit.png' /></a>
        -->
        </td>
      <td class=ul1 align=center>$ctt&nbsp;<br /><a data-rel='tooltip' style='cursor:pointer' title='$log1 $log2'><img src=themes/default/img/tt.png /></a>
	  </td>
      <td class=ul1 align=center width=10>
        $del
        </td>
      </tr>";
  }
  
  $s = "select bm.*, s.Nama as _saat,
    format(bm.Jumlah, 0) as JML,
	Left(bm.TambahanNama,7) as TambahanNama,
    format(bm.TrxID*bm.Besar, 0) as BSR,
    format(bm.Dibayar, 0) as BYR,
	date_format(bm.TanggalBuat, '%d-%m-%Y %H:%i') as TGLBuat,date_format(bm.TanggalEdit, '%d-%m-%Y %H:%i') as TGLEdit,
	bm.LoginBuat as LGNBuat, bm.LoginEdit as LGNEdit
    from bipotmhsw bm
	  left outer join bipot2 b2 on b2.BIPOT2ID = bm.BIPOT2ID
      left outer join saat s on b2.SaatID = s.SaatID
    where bm.PMBMhswID = 1
      and bm.NA = 'N'
	  and bm.BIPOT2ID = 0
      and bm.KodeID = '".KodeID."'
      and bm.MhswID = '$mhsw[MhswID]'
      and bm.TahunID = '$khs[TahunID]'
    order by b2.Prioritas, bm.TrxID DESC, bm.BIPOTMhswID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $n++;
    $sub = $w['Jumlah'] * $w['Besar'] * $w['TrxID'];
    $_sub = number_format($sub);
    $ttl += $sub;
    $byr += $w['Dibayar'];
    if ($_SESSION['_LevelID'] == 1) {
      $del = "<a href='#' onClick=\"BIPOTDELCONF($w[BIPOTMhswID], '$mhsw[MhswID]', '$khs[TahunID]', '$w[TambahanNama]','$w[Dibayar]')\"><img src='img/del.gif' /></a>";
    }
    $ctt = TRIM($w['Catatan']);
    $ctt = str_replace("\r", "<br />", $ctt);
	$Edit = GetaField('karyawan',"Login",$w[LGNEdit],'Nama');
	$Buat1 = GetaField('karyawan',"Login",$w[LGNBuat],'Nama');
	$log1="Dibuat: <font color=#00CCCC><b>$Buat1</b></font> ($w[TGLBuat])";
	if (!empty($w[LGNEdit])) { $log2="Diedit: <font color=#00CCCC><b>$Edit</b></font> $w[TGLEdit]"; }
    echo "<tr>
      <td class=inp width=15>$n</td>
      <td class=ul width=10>
        <a href='#' onClick=\"javascript:BIPOTEdit('$mhsw[MhswID]', '$khs[TahunID]', 0, $w[BIPOTMhswID])\"><img src='img/edit.png' /></a>
        </td>
      <td class=ul>
        $w[Nama] <br />
        <sup>$w[TambahanNama]</sup>
		<div align=right><sub>$w[_saat]</sub></div>
        </td>
      <td class=ul norwap>
        <sup>$w[JML] &times;</sup><br />
        <div align=right>$w[BSR]</div>
        </td>
      <td class=ul align=right nowrap>$_sub</td>
      <td class=ul align=right nowrap>
        $w[BYR] <!--
        <a href='#' onClick=\"javascript:ByrEdit('$pmb[PMBID]', 1, 0, $w[PMBMhswID])\"><img src='img/edit.png' /></a>
        -->
        </td>
      <td class=ul1 align=center>$ctt&nbsp;<br /><a data-rel='tooltip' style='cursor:pointer' title='$log1 $log2'><img src=themes/default/img/tt.png /></a>
	  </td>
      <td class=ul1 align=center width=10>
        $del
        </td>
      </tr>";
  }
  
  $TTL = number_format($ttl);
  $BYR = number_format($byr);
  $SS = number_format($ttl - $byr);
  echo "<tr><td bgcolor=silver colspan=10 height=1></td></tr>";
  echo "<tr>
    <td class=ul1 colspan=4 align=right><b>Total:</td>
    <td class=ul1 align=right><b>$TTL</b></td>
    <td class=ul1 align=right><b>$BYR</b></td>
    <td class=ul1 colspan=2>Sisa: <font size=+1>$SS</font></td>
    </tr>";
  echo "</table>";
}
function HapusSemuaBIPOT() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $TahunID = sqling($_REQUEST['TahunID']);

  $s = "update bipotmhsw set NA='Y'
        where PMBMhswID = 1
      and MhswID = '$MhswID'
      and TahunID = '$TahunID'
      and KodeID = '".KodeID."' ";
	  $r = _query($s);
  HitungUlangBIPOTMhsw($MhswID, $TahunID);
  echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=&MhswID=$MhswID&TahunID=$TahunID'</script>";
}
function HapusBIPOT() {
  $_BIPOTMhswID = $_REQUEST['_BIPOTMhswID']+0;
  $MhswID = sqling($_REQUEST['MhswID']);
  $TahunID = sqling($_REQUEST['TahunID']);
  $MKKode = sqling($_REQUEST['MKKode']);
  /*
  $s = "delete from bipotmhsw where BIPOTMhswID = '$_BIPOTMhswID' ";
  $r = _query($s); 
  */
  $s = "update bipotmhsw set NA = 'Y' where BIPOTMhswID = '$_BIPOTMhswID' ";
  $r = _query($s);

  HitungUlangBIPOTMhsw($MhswID, $TahunID);
  echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=&MhswID=$MhswID&TahunID=$TahunID'</script>";
}
function ProsesBIPOT() {
  $MhswID = sqling($_REQUEST['MhswID']);
  $TahunID = sqling($_REQUEST['TahunID']);
  // Ambil data
  $mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID, "*");
  $khs = GetFields('khs', "KodeID = '".KodeID."' and TahunID = '$TahunID' and MhswID", $MhswID, "*");
  $Prodi = GetFields('prodi', "ProdiID", $mhsw['ProdiID'], "Nama, FakultasID");
  $Fakultas = GetaField('fakultas', "FakultasID", $Prodi['FakultasID'], "Nama");
  $NamaMhs = GetaField('mhsw', "MhswID", $MhswID, 'Nama');
  $Semester = GetaField('khs', "TahunID = '$_SESSION[TahunID]' AND MhswID", $MhswID, "Sesi");

  $khslalu = array();
  if($khs[Sesi] > 1)
  {
	  $sesilalu = $khs[Sesi]-1;
	  $khslalu = GetFields('khs', "KodeID = '".KodeID."' and Sesi = '$sesilalu' and MhswID", $MhswID, "*");
	  /*while(!empty($khslalu))
	  {	if($khslalu['StatusMhswID'] != 'A')
		{	$sesilalu = $sesilalu-1;
			$khslalu = GetFields('khs', "KodeID = '".KodeID."' and Sesi = '$sesilalu' and MhswID", $MhswID, "*");
		}
		else
		{	break;
		}
	  }*/
  }
  
  // Ambil BIPOT-nya
  $s = "select * 
    from bipot2 
    where BIPOTID = '$mhsw[BIPOTID]'
      and Otomatis = 'Y'
      and PerMataKuliah = 'N'
	  and PerLab = 'N'
	  and Remedial = 'N'
	  and PraktekKerja = 'N'
    and PerSkripsi = 'N'
	  and NA = 'N'
    order by TrxID, Prioritas";
  $r = _query($s);
  $MsgList = array();
  while ($w = _fetch_array($r)) {
    $MsgList[] = '';
	$MsgList[] = "Memproses $w[BIPOT2ID], Rp. $w[Jumlah]";
	
	$oke = true;
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);
	$MsgList[] =  "Sesuai dengan status awalnya ($w[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
	
	// Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w['StatusMhswID'], ".".$khs['StatusMhswID'].".");
    $oke = $oke && !($pos === false);
	$MsgList[] =  "Sesuai dengan status mahasiswanya ($w[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
	
    // Apakah grade-nya?
    if ($oke) {
      if ($w['GunakanGradeNilai'] == 'Y') {
        $pos = strpos($w['GradeNilai'], ".".$mhsw['GradeNilai'].".");
        $oke = $oke && !($pos === false);
		$MsgList[] = "Gunakan Grade Nilai? $oke";
	  }
    }
	
	// Apakah Jumlah SKS Tahun lalu mencukupi?
	if ($oke) {
	  if ($w['GunakanGradeIPK'] == 'Y') {
		$_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
		if($_SKS > $khslalu[SKS]) $oke = false;
		else $oke = true;
		
		$MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
	  }
	}
	
	// Apakah Grade IPK-nya OK?
	if ($oke) {
      if ($w['GunakanGradeIPK'] == 'Y') {
		if(!empty($khslalu))
		{   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
			$pos = strpos($w['GradeIPK'], ".".$_GradeIPK.".");
			$oke = $oke && !($pos === false);
			$MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w[GradeIPK])? $oke";
		}
		else
		{	$oke = false;
		}
		
      }
    }
	
    // Apakah dimulai pada sesi ini?
    if ($oke) {
      if ($w['MulaiSesi'] <= $khs['Sesi'] or $w['MulaiSesi'] == 0) $oke = true;
	  else $oke = false;
	  $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w[MulaiSesi])? $oke";
    }
	
	// Apakah ada setup berapa kali ambil?
    if ($oke && $w['KaliSesi'] > 0) {
      $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and KodeID",
        KodeID, "count(BIPOTMhswID)")+0;
      $oke = $_kali < $w['KaliSesi'];
	  $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w[KaliSesi])? $oke";
    }
	
	if($oke) $MsgList[] = "ALL OK! GO FOR IT!";
  
    // Simpan data
    if ($oke) {
      // Cek, sudah ada atau belum? Kalau sudah, ambil ID-nya
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
        and NA = 'N'
        and PMBMhswID = 1
        and TahunID='$khs[TahunID]' and BIPOT2ID",
        $w['BIPOT2ID'], "BIPOTMhswID") +0;
      // Cek apakah memakai script atau tidak?
      if ($w['GunakanScript'] == 'Y') BipotGunakanScript($mhsw, $khs, $w, $ada, 1);
      // Jika tidak perlu pakai script
      else {
        // Jika tidak ada duplikasi, maka akan di-insert. Tapi jika sudah ada, maka dicuekin aja.
        if ($ada == 0) {
          // Simpan
          $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w['BIPOTNamaID'], 'Nama');
          $s1 = "insert into bipotmhsw
            (KodeID, COAID, PMBMhswID, MhswID, TahunID,
            BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID,
            Jumlah, Besar, Dibayar,
            Catatan, NA,
            LoginBuat, TanggalBuat, Prodi, Fakultas, NamaMhs, Sesi)
            values
            ('".KodeID."', '$w[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
            '$w[BIPOT2ID]', '$w[BIPOTNamaID]', '$w[TambahanNama]', '$Nama', '$w[TrxID]',
            1, '$w[Jumlah]', 0,
            'Auto', 'N',
            '$_SESSION[_Login]', now(), '$Prodi[Nama]', '$Fakultas', '$NamaMhs', '$Semester')";
          $r1 = _query($s1);
        }// end $ada=0
      } // end if $ada
    }   // end if $oke
  }     // end while
  
  // Ambil BIPOT Biaya Per Mata Kuliah dan Bukan Biaya per SKS
  $s = "select k.MKKode, k.Nama, (mk.SKS-mk.SKSPraktikum) as SKSTeori, mk.SKSPraktikum, j.BiayaKhusus, j.Biaya, j.NamaBiaya, j.AdaResponsi
			from krs k 
				left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
				left outer join mk mk on mk.MKID=k.MKID and mk.KodeID='".KodeID."'
			where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and mk.PraktekKerja='N' and mk.PerSKS='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {	  $s1 = "select * 
	   from bipot2 
		where BIPOTID = '$mhsw[BIPOTID]'
		  and Otomatis = 'Y'
		  and (PerMataKuliah = 'Y' or PerLab = 'Y')
		  and NA = 'N'
		order by TrxID, Prioritas";
	  $r1 = _query($s1);
	  while ($w1 = _fetch_array($r1)) 
	  {	
		$MsgList[] = '-----------------------------------------------------------------';
		$MsgList[] = "Memproses $w1[BIPOT2ID], Rp. $w1[Jumlah]";
	    
		$oke = true;
		// Cek apakah mata kuliah ini dapat dikenakan biaya Lab
		if($w1['PerLab'] == 'Y') 
		{	if($w['AdaResponsi'] == 'Y') $oke = true;
			else $oke = false;
		}
		else $oke = true;
		
		// Apakah sesuai dengan status awalnya?
		$pos = strpos($w1['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
		$oke = $oke && !($pos === false);
		$MsgList[] =  "Sesuai dengan status awalnya ($w1[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
		
		// Apakah sesuai dengan status mahasiswanya?
		$pos = strpos($w1['StatusMhswID'], ".".$khs['StatusMhswID'].".");
		$oke = $oke && !($pos === false);
		$MsgList[] =  "Sesuai dengan status mahasiswanya ($w1[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
		
		// Apakah grade-nya?
		if ($oke) {
		  if ($w1['GunakanGradeNilai'] == 'Y') {
			$pos = strpos($w1['GradeNilai'], ".".$mhsw['GradeNilai'].".");
			$oke = $oke && !($pos === false);
			$MsgList[] = "Gunakan Grade Nilai? $oke";
		  }
		}
		
		// Apakah Jumlah SKS Tahun lalu mencukupi?
		if ($oke) {
		  if ($w1['GunakanGradeIPK'] == 'Y') {
			$_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
			if($_SKS > $khslalu[SKS]) $oke = false;
			else $oke = true;
			
			$MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
		  }
		}
		
		// Apakah Grade IPK-nya OK?
		if ($oke) {
		  if ($w1['GunakanGradeIPK'] == 'Y') {
			if(!empty($khslalu))
			{   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
				$pos = strpos($w1['GradeIPK'], ".".$_GradeIPK.".");
				$oke = $oke && !($pos === false);
				$MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w1[GradeIPK])? $oke";
			}
			else
			{	$oke = false;
			}		
		  }
		}
		
		// Apakah dimulai pada sesi ini?
		if ($oke) {
		  if ($w1['MulaiSesi'] <= $khs['Sesi'] or $w1['MulaiSesi'] == 0) $oke = true;
		  else $oke = false;
		  $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w1[MulaiSesi])? $oke";
		}
		
		// Apakah ada setup berapa kali ambil?
		if ($oke && $w1['KaliSesi'] > 0) {
		  $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and BIPOTNamaID='$w1[BIPOTNamaID]' and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS' and KodeID",
			KodeID, "count(BIPOTMhswID)")+0;
		  $oke = $_kali < $w1['KaliSesi'];
		  $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w1[KaliSesi])? $oke";
		}
		
		if($oke) $MsgList[] = "ALL OK! GO FOR IT!";
	
		// Simpan data
		if ($oke) {
		  		if($w1['PerSKS'] == 'Y') $SKS = 'Teori:'.$w['SKSTeori'];
              	if($w1['PerSKSPraktek'] == 'Y') $SKS = 'Praktek:'.$w['SKSPraktikum'];
			$ada = GetaField('bipotmhsw',
				"KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
				and NA = 'N'
				and PMBMhswID = 1
				and TahunID='$khs[TahunID]'
				and BIPOTNamaID = '$w1[BIPOTNamaID]'
				and TambahanNama='$w[MKKode] - $w[Nama] - $SKS SKS'
				and BIPOT2ID",
				$w1['BIPOT2ID'], "BIPOTMhswID") +0;
			
			if ($ada == 0) {
			  // Simpan
			  $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w1['BIPOTNamaID'], 'Nama');
              $Jumlah = 0;
              
               if($w1['PerSKS'] == 'Y') $Jumlah = $w['SKSTeori'];
               else $Jumlah = 1;
               
               if($w1['PerSKSPraktek'] == 'Y' && $w['SKSPraktikum'] > 0) $Jumlah = $w['SKSPraktikum'];

			  $Besar = $w1['Jumlah'];
              // jika bipot untuk mk teori pakai query ini
			  if ($Jumlah > 0 && $w1['PerSKS'] == 'Y'){
                  $s2 = "insert into bipotmhsw
                    (KodeID, COAID, PMBMhswID, MhswID, TahunID,
                    BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
                    Jumlah, Besar, Dibayar,
                    Catatan, NA,
                    LoginBuat, TanggalBuat, Prodi, Fakultas, NamaMhs, Sesi)
                    values
                    ('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
                    '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '".$w['MKKode']." - ".$w['Nama']." - ".$SKS." SKS', '$Nama', '$w1[TrxID]', 
                    '$Jumlah', '$Besar', 0,
                    'Auto', 'N',
                    '$_SESSION[_Login]', now(), '$Prodi[Nama]', '$Fakultas', '$NamaMhs', '$Semester')";
                  $r2 = _query($s2);
              }
              // jika bipot untuk mk praktek pakai query ini
              if ($Jumlah > 0 && $w1['PerSKSPraktek'] == 'Y' && $w['SKSPraktikum'] > 0){
                 $s2 = "insert into bipotmhsw
                    (KodeID, COAID, PMBMhswID, MhswID, TahunID,
                    BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
                    Jumlah, Besar, Dibayar,
                    Catatan, NA,
                    LoginBuat, TanggalBuat, Prodi, Fakultas, NamaMhs, Sesi)
                    values
                    ('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
                    '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '".$w['MKKode']." - ".$w['Nama']." - ".$SKS." SKS', '$Nama', '$w1[TrxID]', 
                    '$Jumlah', '$Besar', 0,
                    'Auto', 'N',
                    '$_SESSION[_Login]', now(), '$Prodi[Nama]', '$Fakultas', '$NamaMhs', '$Semester')";
                  $r2 = _query($s2);
              }
		    }
	     }
	  }
  }
  
  
  // Ambil BIPOT Biaya Praktek Kerja
  $s = "select k.MKKode, k.Nama, k.SKS, j.BiayaKhusus, j.Biaya, j.NamaBiaya, j.AdaResponsi
			from krs k 
				left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
				left outer join mk mk on mk.MKID=k.MKID and mk.KodeID='".KodeID."'
			where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and mk.PraktekKerja='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {	  $s1 = "select * 
	   from bipot2 
		where BIPOTID = '$mhsw[BIPOTID]'
		  and Otomatis = 'Y'
		  and (PraktekKerja = 'Y')
		  and NA = 'N'
		order by TrxID, Prioritas";
	  $r1 = _query($s1);
	  while ($w1 = _fetch_array($r1)) 
	  {	
		$MsgList[] = '-----------------------------------------------------------------';
		$MsgList[] = "Memproses $w1[BIPOT2ID], Rp. $w1[Jumlah]";
	    
		$oke = true;
		
		// Apakah sesuai dengan status awalnya?
		$pos = strpos($w1['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
		$oke = $oke && !($pos === false);
		$MsgList[] =  "Sesuai dengan status awalnya ($w1[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
		
		// Apakah sesuai dengan status mahasiswanya?
		$pos = strpos($w1['StatusMhswID'], ".".$khs['StatusMhswID'].".");
		$oke = $oke && !($pos === false);
		$MsgList[] =  "Sesuai dengan status mahasiswanya ($w1[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
		
		// Apakah grade-nya?
		if ($oke) {
		  if ($w1['GunakanGradeNilai'] == 'Y') {
			$pos = strpos($w1['GradeNilai'], ".".$mhsw['GradeNilai'].".");
			$oke = $oke && !($pos === false);
			$MsgList[] = "Gunakan Grade Nilai? $oke";
		  }
		}
		
		// Apakah Jumlah SKS Tahun lalu mencukupi?
		if ($oke) {
		  if ($w1['GunakanGradeIPK'] == 'Y') {
			$_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
			if($_SKS > $khslalu[SKS]) $oke = false;
			else $oke = true;
			
			$MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
		  }
		}
		
		// Apakah Grade IPK-nya OK?
		if ($oke) {
		  if ($w1['GunakanGradeIPK'] == 'Y') {
			if(!empty($khslalu))
			{   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
				$pos = strpos($w1['GradeIPK'], ".".$_GradeIPK.".");
				$oke = $oke && !($pos === false);
				$MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w1[GradeIPK])? $oke";
			}
			else
			{	$oke = false;
			}		
		  }
		}
		
		// Apakah dimulai pada sesi ini?
		if ($oke) {
		  if ($w1['MulaiSesi'] <= $khs['Sesi'] or $w1['MulaiSesi'] == 0) $oke = true;
		  else $oke = false;
		  $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w1[MulaiSesi])? $oke";
		}
		
		// Apakah ada setup berapa kali ambil?
		if ($oke && $w1['KaliSesi'] > 0) {
		  $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and BIPOTNamaID='$w1[BIPOTNamaID]' and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS' and KodeID",
			KodeID, "count(BIPOTMhswID)")+0;
		  $oke = $_kali < $w1['KaliSesi'];
		  $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w1[KaliSesi])? $oke";
		}
		
		if($oke) $MsgList[] = "ALL OK! GO FOR IT!";
		
		// Simpan data
		if ($oke) {
		 
			$ada = GetaField('bipotmhsw',
				"KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
				and NA = 'N'
				and PMBMhswID = 1
				and TahunID='$khs[TahunID]'
				and BIPOTNamaID = '$w1[BIPOTNamaID]'
				and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS'
				and BIPOT2ID",
				$w1['BIPOT2ID'], "BIPOTMhswID") +0;
			
			if ($ada == 0) {
			  // Simpan
			  $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w1['BIPOTNamaID'], 'Nama');
			  if($w1['PerSKS'] == 'Y') $Jumlah = $w['SKS'];
			  else $Jumlah = 1;
			  $Besar = $w1['Jumlah'];
			  
			  $s2 = "insert into bipotmhsw
				(KodeID, COAID, PMBMhswID, MhswID, TahunID,
				BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
				Jumlah, Besar, Dibayar,
				Catatan, NA,
				LoginBuat, TanggalBuat)
				values
				('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
				'$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '".$w[MKKode]." - ".$w['Nama']." - ".$w['SKS']." SKS', '$Nama', '$w1[TrxID]', 
				'$Jumlah', '$Besar', 0,
				'Auto', 'N',
				'$_SESSION[_Login]', now())";
			  $r2 = _query($s2);
		    }
	     }
	  }
  }

  // Ambil BIPOT Biaya Skripsi
  $s = "select k.MKKode, k.Nama, k.SKS, j.BiayaKhusus, j.Biaya, j.NamaBiaya, j.AdaResponsi
      from krs k 
        left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
        left outer join mk mk on mk.MKID=k.MKID and mk.KodeID='".KodeID."'
      where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and mk.TugasAkhir='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {   $s1 = "select * 
     from bipot2 
    where BIPOTID = '$mhsw[BIPOTID]'
      and Otomatis = 'Y'
      and (PerSkripsi = 'Y')
      and NA = 'N'
    order by TrxID, Prioritas";
    $r1 = _query($s1);
    while ($w1 = _fetch_array($r1)) 
    { 
    $MsgList[] = '-----------------------------------------------------------------';
    $MsgList[] = "Memproses $w1[BIPOT2ID], Rp. $w1[Jumlah]";
      
    $oke = true;
    
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w1['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status awalnya ($w1[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
    
    // Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w1['StatusMhswID'], ".".$khs['StatusMhswID'].".");
    $oke = $oke && !($pos === false);
    $MsgList[] =  "Sesuai dengan status mahasiswanya ($w1[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
    
    // Apakah grade-nya?
    if ($oke) {
      if ($w1['GunakanGradeNilai'] == 'Y') {
      $pos = strpos($w1['GradeNilai'], ".".$mhsw['GradeNilai'].".");
      $oke = $oke && !($pos === false);
      $MsgList[] = "Gunakan Grade Nilai? $oke";
      }
    }
    
    // Apakah Jumlah SKS Tahun lalu mencukupi?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      $_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
      if($_SKS > $khslalu[SKS]) $oke = false;
      else $oke = true;
      
      $MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
      }
    }

    // Apakah Sudah Pernah Membayar Semester Sebelumnya
    if ($oke) {
      
      $_Dibayar = GetaField('bipotmhsw', "MhswID = '$MhswID' and TahunID!='$khs[TahunID]' and BIPOTNamaID='$w1[BIPOTNamaID]' and KodeID", KodeID, 'Dibayar');
      if($_Dibayar > 0) $oke = false;
      else $oke = oke;
      
      $MsgList[] = "Apakah sudah pernah melakukan pembayaran Uang TA/Skripsi ($_Dibayar)? $oke";
    }
    
    // Apakah Grade IPK-nya OK?
    if ($oke) {
      if ($w1['GunakanGradeIPK'] == 'Y') {
      if(!empty($khslalu))
      {   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
        $pos = strpos($w1['GradeIPK'], ".".$_GradeIPK.".");
        $oke = $oke && !($pos === false);
        $MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w1[GradeIPK])? $oke";
      }
      else
      { $oke = false;
      }   
      }
    }
    
    // Apakah dimulai pada sesi ini?
    if ($oke) {
      if ($w1['MulaiSesi'] <= $khs['Sesi'] or $w1['MulaiSesi'] == 0) $oke = true;
      else $oke = false;
      $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w1[MulaiSesi])? $oke";
    }
    
    // Apakah ada setup berapa kali ambil?
    if ($oke && $w1['KaliSesi'] > 0) {
      $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and BIPOTNamaID='$w1[BIPOTNamaID]' and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS' and KodeID",
      KodeID, "count(BIPOTMhswID)")+0;
      $oke = $_kali < $w1['KaliSesi'];
      $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w1[KaliSesi])? $oke";
    }
    
    if($oke) $MsgList[] = "ALL OK! GO FOR IT!";
    
    // Simpan data
    if ($oke) {
     
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
        and NA = 'N'
        and PMBMhswID = 1
        and TahunID='$khs[TahunID]'
        and BIPOTNamaID = '$w1[BIPOTNamaID]'
        and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS'
        and BIPOT2ID",
        $w1['BIPOT2ID'], "BIPOTMhswID") +0;
      
      if ($ada == 0) {
        // Simpan
        $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w1['BIPOTNamaID'], 'Nama');
        if($w1['PerSKS'] == 'Y') $Jumlah = $w['SKS'];
        else $Jumlah = 1;
        $Besar = $w1['Jumlah'];
        
        $s2 = "insert into bipotmhsw
        (KodeID, COAID, PMBMhswID, MhswID, TahunID,
        BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
        Jumlah, Besar, Dibayar,
        Catatan, NA,
        LoginBuat, TanggalBuat)
        values
        ('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
        '$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', '".$w['MKKode']." - ".$w['Nama']." - ".$w['SKS']." SKS', '$Nama', '$w1[TrxID]', 
        '$Jumlah', '$Besar', 0,
        'Auto', 'N',
        '$_SESSION[_Login]', now())";
        $r2 = _query($s2);
        }
       }
    }
  }
  
  // Masukkan Biaya Khusus dari tiap mata kuliah (termasuk biaya khusus mata kuliah praktek kerja - bila ada)
  $s = "select k.MKKode, k.Nama, k.SKS, j.BiayaKhusus, j.Biaya, j.NamaBiaya from krs k left outer join jadwal j on k.JadwalID=j.JadwalID and j.KodeID='".KodeID."'
			where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and j.BiayaKhusus='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))			  
  {	$ada = GetaField('bipotmhsw',
	"KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
	and NA = 'N'
	and PMBMhswID = 1
	and TahunID='$khs[TahunID]' 
	and Nama='$w[NamaBiaya]'
	and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS'
	and BIPOT2ID",
	0, "BIPOTMhswID") +0;
	
	if ($ada == 0) {
	  // Simpan
	  
	  $s2 = "insert into bipotmhsw
		(KodeID, COAID, PMBMhswID, MhswID, TahunID,
		BIPOT2ID, BIPOTNamaID, Nama, TambahanNama, TrxID, 
		Jumlah, Besar, Dibayar,
		Catatan, NA,
		LoginBuat, TanggalBuat)
		values
		('".KodeID."', '', 1, '$mhsw[MhswID]', '$khs[TahunID]',
		0, 0, '$w[NamaBiaya]', '$w[MKKode] - $w[Nama] - $w[SKS] SKS', 1, 
		1, '$w[Biaya]', 0,
		'Biaya Khusus', 'N',
		'$_SESSION[_Login]', now())";
	  $r2 = _query($s2);
	}
  }
  
  // Ambil BIPOT Remedial
  $s = "select k.MKKode, k.Nama, k.SKS
			from krsremedial k 
			where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))
  {	  $MsgList[] = '-----------------------------------------------------------------';
	  $MsgList[] = '---------------------------REMEDIAL---------------------------';
	  $s1 = "select * 
	   from bipot2 
		where BIPOTID = '$mhsw[BIPOTID]'
		  and Otomatis = 'Y'
		  and Remedial = 'Y'
		  and NA = 'N'
		order by TrxID, Prioritas";
	  $r1 = _query($s1);
	  while ($w1 = _fetch_array($r1)) 
	  {	
		$MsgList[] = '-----------------------------------------------------------------';
		$MsgList[] = "Memproses $w1[BIPOT2ID] - $w[MKKode] - $w[Nama], Rp. $w1[Jumlah]";
	    
		$oke = true;
		
		// Apakah sesuai dengan status awalnya?
		$pos = strpos($w1['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
		$oke = $oke && !($pos === false);
		$MsgList[] =  "Sesuai dengan status awalnya ($w1[StatusAwalID] ~ $mhsw[StatusAwalID])? $oke";
		
		// Apakah sesuai dengan status mahasiswanya?
		$pos = strpos($w1['StatusMhswID'], ".".$khs['StatusMhswID'].".");
		$oke = $oke && !($pos === false);
		$MsgList[] =  "Sesuai dengan status mahasiswanya ($w1[StatusMhswID] ~ $khs[StatusMhswID])? $oke";
		
		// Apakah grade-nya?
		if ($oke) {
		  if ($w1['GunakanGradeNilai'] == 'Y') {
			$pos = strpos($w1['GradeNilai'], ".".$mhsw['GradeNilai'].".");
			$oke = $oke && !($pos === false);
			$MsgList[] = "Gunakan Grade Nilai? $oke";
		  }
		}
		
		// Apakah Jumlah SKS Tahun lalu mencukupi?
		if ($oke) {
		  if ($w1['GunakanGradeIPK'] == 'Y') {
			$_SKS = GetaField('gradeipk', "IPKMin <= '$khslalu[IPS]' and '$khslalu[IPS]' <= IPKMax and KodeID", KodeID, 'SKSMin');
			if($_SKS > $khslalu[SKS]) $oke = false;
			else $oke = true;
			
			$MsgList[] = "Jumlah SKS Tahun Mencukupi($_SKS ~ $khslalu[SKS])? $oke";
		  }
		}
		
		// Apakah Grade IPK-nya OK?
		if ($oke) {
		  if ($w1['GunakanGradeIPK'] == 'Y') {
			if(!empty($khslalu))
			{   $_GradeIPK = GetaField('gradeipk', "IPKMin <= $khslalu[IPS] and $khslalu[IPS] <= IPKMax and KodeID", KodeID, 'GradeIPK');
				$pos = strpos($w1['GradeIPK'], ".".$_GradeIPK.".");
				$oke = $oke && !($pos === false);
				$MsgList[] = "Grade IPK OK ($_GradeIPK ~ $w1[GradeIPK])? $oke";
			}
			else
			{	$oke = false;
			}		
		  }
		}
		
		// Apakah dimulai pada sesi ini?
		if ($oke) {
		  if ($w1['MulaiSesi'] <= $khs['Sesi'] or $w1['MulaiSesi'] == 0) $oke = true;
		  else $oke = false;
		  $MsgList[] = "Mulai pada sesi ini ($khs[Sesi] ~ $w1[MulaiSesi])? $oke";
		}
		
		// Apakah ada setup berapa kali ambil?
		if ($oke && $w1['KaliSesi'] > 0) {
		  $_kali = GetaField('bipotmhsw', "MhswID='$MhswID' and NA='N' and PMBMhswID=1 and BIPOTNamaID='$w1[BIPOTNamaID]' and TambahanNama='$w[MKKode] - $w[Nama] - $w[SKS] SKS' and KodeID",
			KodeID, "count(BIPOTMhswID)")+0;
		  $oke = $_kali < $w1['KaliSesi'];
		  $MsgList[] = "Berapa Kali Ambil - ($_kali ~ $w1[KaliSesi])? $oke";
		}
		
		if($oke) $MsgList[] = "ALL OK! GO FOR IT!";

		// Simpan data
		if ($oke) {
		 
			$ada = GetaField('bipotmhsw',
				"KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
				and NA = 'N'
				and PMBMhswID = 1
				and TahunID='$khs[TahunID]'
				and BIPOTNamaID = '$w1[BIPOTNamaID]'
				and TambahanNama='Remedial: $w[MKKode] - $w[Nama] - $w[SKS] SKS'
				and BIPOT2ID",
				$w1['BIPOT2ID'], "BIPOTMhswID") +0;
			
			if ($ada == 0) {
			  // Simpan
			  $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w1['BIPOTNamaID'], 'Nama');
			  if($w1['PerSKS'] == 'Y') $Jumlah = $w['SKS'];
			  else $Jumlah = 1;
			  $Besar = $w1['Jumlah'];
			  
			  $s2 = "insert into bipotmhsw
				(KodeID, COAID, PMBMhswID, MhswID, TahunID,
				BIPOT2ID, BIPOTNamaID, TambahanNama, Nama, TrxID, 
				Jumlah, Besar, Dibayar,
				Catatan, NA,
				LoginBuat, TanggalBuat)
				values
				('".KodeID."', '$w1[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
				'$w1[BIPOT2ID]', '$w1[BIPOTNamaID]', 'Remedial: ".$w['MKKode']." - ".$w['Nama']." - ".$w['SKS']." SKS', '$Nama', '$w1[TrxID]', 
				'$Jumlah', '$Besar', 0,
				'Auto', 'N',
				'$_SESSION[_Login]', now())";
			  $r2 = _query($s2);
		    }
	     }
	  }
  }
  
  // Masukkan Biaya Khusus dari tiap mata kuliah remedial
  $s = "select k.MKKode, k.Nama, k.SKS, j.BiayaKhusus, j.Biaya, j.NamaBiaya 
			from krsremedial k left outer join jadwalremedial j on k.JadwalRemedialID=j.JadwalRemedialID and j.KodeID='".KodeID."'
			where k.MhswID='$MhswID' and k.TahunID='$_SESSION[TahunID]' and j.BiayaKhusus='Y' and k.KodeID='".KodeID."'";
  $r = _query($s);
  while($w = _fetch_array($r))			  
  {	$ada = GetaField('bipotmhsw',
	"KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
	and NA = 'N'
	and PMBMhswID = 1
	and TahunID='$khs[TahunID]' 
	and Nama='$w[NamaBiaya]'
	and TambahanNama='Remedial: $w[MKKode] - $w[Nama] - $w[SKS] SKS'
	and BIPOT2ID",
	0, "BIPOTMhswID") +0;
	
	if ($ada == 0) {
	  // Simpan
	  
	  $s2 = "insert into bipotmhsw
		(KodeID, COAID, PMBMhswID, MhswID, TahunID,
		BIPOT2ID, BIPOTNamaID, Nama, TambahanNama, TrxID, 
		Jumlah, Besar, Dibayar,
		Catatan, NA,
		LoginBuat, TanggalBuat)
		values
		('".KodeID."', '', 1, '$mhsw[MhswID]', '$khs[TahunID]',
		0, 0, '$w[NamaBiaya]', 'Remedial: $w[MKKode] - $w[Nama] - $w[SKS] SKS', 1, 
		1, '$w[Biaya]', 0,
		'Biaya Khusus', 'N',
		'$_SESSION[_Login]', now())";
	  $r2 = _query($s2);
	}
  }
  // Uncomment lines below to print debugging messages
  /*echo "COUNT: ".count($MsgList);
  if(!empty($MsgList))
	{	foreach($MsgList as $Msg)
		{	echo "$Msg<br>";
		}
	}*/
	

  
  HitungUlangBIPOTMhsw($MhswID, $TahunID);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=&MhswID=$MhswID&TahunID=$TahunID", 10);
  
}
function TampilkanBayarMhsw($mhsw, $khs) {
  $s = "select bm.*, format(bm.TrxID * bm.Jumlah, 0) as JML,
    date_format(bm.Tanggal, '%d-%m-%Y') as TGL,date_format(bm.TanggalBuat, '%d-%m-%Y %H:%i') as TGLBuat,date_format(bm.TanggalEdit, '%d-%m-%Y %H:%i') as TGLEdit
    from bayarmhsw bm
    where bm.KodeID = '".KodeID."'
      and bm.NA = 'N'
      and bm.TahunID = '$khs[TahunID]'
      and bm.MhswID = '$mhsw[MhswID]'
      and bm.PMBMhswID = 1
    order by bm.Tanggal";
  $r = _query($s); $n = 0;
  //echo "<pre>$s</pre>";
  echo "<table class=box cellspacing=1 width=800 align=center>";
  echo "<tr><th class=ttl colspan=8>Daftar Pembayaran & Penarikan</th></tr>";
  echo "<tr>
    <th class=ttl width=20>#</th>
    <th class=ttl width=80>Tanggal</th>
    <th class=ttl width=120>Nomer Bukti</th>
    <th class=ttl width=100>Jumlah</th>
	<th class=ttl>Keterangan</th>
	<th class=ttl width=50>Log</th>
    <th class=ttl>Cetak<br />BPM</th>
    </tr>";
    $TOTAL = 0;
  while ($w = _fetch_array($r)) {
    $n++;
	$tglBuat=date('d F Y',$w[TanggalBuat]);
	$tglEdit=date('d F Y',$w[TanggalEdit]);
	$Edit = GetaField('karyawan',"Login",$w[LoginEdit],'Nama');
	$Buat1 = GetaField('karyawan',"Login",$w[LoginBuat],'Nama');
    
	$log1="Dibuat: <font color=#00CCCC><b>$Buat1</b></font> ($w[TGLBuat])";
	if (!empty($w[TanggalEdit])) { $log2="Diedit: <font color=#00CCCC><b>$Edit</b></font> $w[TGLEdit]"; }
   // -----------------------------------------------------------------------
   // Tambahan fitur Hapus Pembayaran oleh Arisal Yanuarafi 19 September 2012
   // -----------------------------------------------------------------------
   echo "<tr>
      <th class=ttl>$n</td>
      <td class=ul align=center>$w[TGL]</td>
      <td class=ul align=center>$w[BayarMhswID]</td>
      <td class=ul align=right>&nbsp;</td>
      <td class=ul>&nbsp;</td>
	  <th class=ul><a data-rel='tooltip' style='cursor:pointer' data-original-title='$log1 <br>$log2'><img src=themes/default/img/tt.png /></a></th>
      <td class=ul align=center width=10>
        <a href='#' onClick=\"javascript:CetakBPM('$w[BayarMhswID]', $w[TrxID])\"><img src='img/printer2.gif' /></a>
        </td>
      </tr>";
   $o = "Select *,Format(Jumlah,0) as JML from bayarmhsw2 where BayarMhswID='$w[BayarMhswID]' and NA='N'";
   $p = _query($o);
   while ($q = _fetch_array($p)) {
   $nBM++;
   $NamaBipot = GetaField('bipotnama',"BIPOTNamaID",$q[BIPOTNamaID],"Nama");
   $NamaBipot2 = GetaField('bipotmhsw',"BIPOTMhswID",$q[BIPOTMhswID],"left(TambahanNama,200)");
   if (($_SESSION['_LevelID'] == 1) || ($_SESSION['_LevelID'] == 60) || ($_SESSION['_LevelID'] == 20)) {
   $del = "<a href='#' onClick=\"javascript:ByrDel('$w[BayarMhswID]','$q[BayarMhsw2ID]', '$mhsw[MhswID]', '$khs[TahunID]', $q[Jumlah])\"><img src='img/del.gif' /></a>";
   }
   else $del = '';
   $NA = ($q['NA'] == 'N')? "<tr>" : '<tr bgcolor=#CCCCCC>';
   $Pesan = ($q['NA'] == 'N')? "" : '<sup>sudah dihapus</sup>';
   echo "$NA
   		<td colspan='3' class=ul align=right>$del</td>
        <td align=right class=ul>$q[JML]</td>
        <td colspan='3' class=ul>&raquo; $NamaBipot $NamaBipot2  $Pesan</td>
        </tr>";
   } 
    echo "<tr>
      <td class=ul align=right colspan=5><b>SUBTOTAL</b></td>
      <td class=ul align=right colspan=2><b>$w[JML]</b></td>
      </tr>";
   $TOTAL += $w[Jumlah];
 
  }
      echo "<tr>
      <td class=ul align=right colspan=5><b>TOTAL</b></td>
      <td class=ul align=right colspan=2><b>".number_format($TOTAL)."</b></td>
      </tr>";
  echo "</table></p>";
}
function HapusBayar() {
  $BayarMhswID = sqling($_REQUEST['BayarMhswID']);
  $BayarMhsw2ID = sqling($_REQUEST['BayarMhsw2ID']);
  $MhswID = sqling($_REQUEST['MhswID']);
  $TahunID = sqling($_REQUEST['TahunID']);
  $Jml = sqling($_REQUEST['Jml'])+0;
  
  // Hapus detail
  $s = "update bayarmhsw2
    set NA = 'Y'
    where BayarMhswID = '$BayarMhswID'
      and BayarMhsw2ID = '$BayarMhsw2ID'";
  $r = _query($s);
  
  $UpdateJumlah = GetaField('bayarmhsw',"BayarMhswID",$BayarMhswID,"((TrxID * Jumlah) - $Jml)");
  // Update BayarMhsw
    $s = "update bayarmhsw
    set Jumlah = '$UpdateJumlah'
    where BayarMhswID = '$BayarMhswID'
      and KodeID = '".KodeID."' ";
  $r = _query($s);

  HitungUlangBIPOTMhsw($MhswID, $TahunID);
	BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 100);
}
function BayarMhswScript() {
  echo <<<SCR
  <script>
  function InquirySemesterMhsw(mhswid) {
    lnk = "inq/mhsw_semester.php?mhswid=" + mhswid;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
function BIPOTScript() {
  RandomStringScript();
  echo <<<SCR
  <script>
  $(document).ready(function() {

	//Select all anchor tag with rel set to tooltip
	$('a[rel=tooltip]').mouseover(function(e) {
		
		//Grab the title attribute's value and assign it to a variable
		var tip = $(this).attr('alt');	
		
		//Remove the title attribute's to avoid the native tooltip from the browser
		$(this).attr('title','');
		
		//Append the tooltip template and its value
		$(this).append('<div id="tooltip"><div class="tipHeader"></div><div class="tipBody">' + tip + '</div><div class="tipFooter"></div></div>');		
				
		//Show the tooltip with faceIn effect
		$('#tooltip').fadeIn('1000');
		$('#tooltip').fadeTo('10',0.9);
		
	}).mousemove(function(e) {
	
		//Keep changing the X and Y axis for the tooltip, thus, the tooltip move along with the mouse
		$('#tooltip').css('top', e.pageY - 65 );
		$('#tooltip').css('left', e.pageX - 130 );
		
	}).mouseout(function() {
	
		//Put back the title attribute's value
		$(this).attr('title',$('.tipBody').html());
	
		//Remove the appended tooltip template
		$(this).children('div#tooltip').remove();
		
	});

});

  function BIPOTDELCONF(id, mhswid, tahunid,mkkode,bayar) {
    if (confirm("Benar Anda akan menghapus BIPOT ini?")) {
      window.location="?mnux=$_SESSION[mnux]&gos=HapusBIPOT&BypassMenu=1&_BIPOTMhswID="+id+"&MhswID="+mhswid+"&TahunID="+tahunid+"&MKKode="+mkkode;
    	}
	}
  function BIPOTDELALLCONF(mhswid, tahunid) {
    if (confirm("Benar Anda akan menghapus semua biaya di bawah ini? Biaya yang sudah terbayar tidak akan dihapus.")) {
      window.location="?mnux=$_SESSION[mnux]&gos=HapusSemuaBIPOT&BypassMenu=1&MhswID="+mhswid+"&TahunID="+tahunid;
    }
  }
  function BIPOTEdit(mhswid, tahunid, md, id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].bipotedit.php?MhswID="+mhswid+"&TahunID="+tahunid+"&md="+md+"&id="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=400, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function ByrEdit(mhswid, khsid, md, bayarid) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].bayar.php?MhswID="+mhswid+"&KHSID="+khsid+"&md="+md+"&BayarID="+bayarid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=750, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnTarikan(mhswid, khsid, md, bayarid) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].tarik.php?MhswID="+mhswid+"&KHSID="+khsid+"&md="+md+"&BayarID="+bayarid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnHistoryBeasiswa(mhswid, khsid, md) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].historybeasiswa.php?MhswID="+mhswid+"&KHSID="+khsid+"&md="+md+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakBPM(id, trx) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].bpm.php?id="+id+"&_rnd="+_rnd+"&trx="+trx;
    win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function ByrDel(BayarMhswID, BayarMhsw2ID, MhswID, TahunID,J) {
    if (confirm("Benar Anda akan menghapus pembayaran ini? Mungkin daftar BIPOT di atas menjadi tidak balance lagi.")) {
      window.location="?mnux=$_SESSION[mnux]&gos=HapusBayar&BayarMhswID="+BayarMhswID+"&BayarMhsw2ID="+BayarMhsw2ID+"&MhswID="+MhswID+"&TahunID="+TahunID+"&Jml="+J;
    }
  }
  function BuatTagihanBank(mhswid, tahunid) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].buat.tagihan.bank.php?MhswID="+mhswid+"&__TahunID="+tahunid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=400, height=100, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>
