<?php
session_start();
include_once "../sisfokampus1.php";
include_once "../$_SESSION[mnux].lib.php";

HeaderSisfoKampus("Edit Jadwal Tambahan", 1);

// *** infrastruktur **
echo <<<SCR
  <script src="../$_SESSION[mnux].edit.script.js"></script>
SCR;

// *** Parameters ***

// *** Special Parameters ***
$md = $_REQUEST['md']+0;
$jid = $_REQUEST['jid']+0; // JadwalID yang merupakan jadwal utama dari seluruh jadwal tambahan
$krsid = $_REQUEST['krsid']+0; // KRSID dari krs yang menjadi krs pokok
$krsresid = $_REQUEST['krsresid']+0; // KRSID dari krs tambahan yang telah diambil 
$jenis = $_REQUEST['jenis'];

echo "MD: $md, JID: $jid, KRS: $krsid, KRSRES: $krsresid, JENIS: $jenis";

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $jid, $krsid, $krsresid, $jenis);

// *** Functions ***
function Edit($md, $jid, $krsid, $krsresid, $jenis) {
  if ($md == 0) {
	// Mode Edit
	$jdl = "Edit Jadwal Tambahan";
  }
  elseif ($md == 1) {
	// Mode Tambah
	$jdl = "Tambah Jadwal Tambahan";
	
  }
  else {
	die(ErrorMsg("Error", "Mode tidak dikenali")); 
  }
  
  $w = GetFields('jadwal', "JadwalID='$jid' and KodeID", KodeID, "*");
  $w['Dosen'] = GetaField('dosen', "KodeID='".KodeID."' and Login", $w['DosenID'], 'Nama');

  // Parameters
  $NamaHari = GetaField('hari', 'HariID', $w['HariID'], 'Nama');
  $NamaJenisJadwal = GetaField('jenisjadwal', "JenisJadwalID", $jenis, 'Nama');
  // Tampilkan
  TampilkanJudul($jdl);
  echo <<<END
  <table class=bsc cellspacing=1 width=100%>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul1>$w[Nama] <sup>$w[MKKode]</sup></td>
      <td class=inp>Jenis Jadwal:</td>
	  <td class=ul1>$jenis - $NamaJenisJadwal</td>
	  </tr>
  <tr><td class=inp>Dosen Pengajar:</td>
      <td class=ul1>$w[Dosen] <sup>$w[DosenID]</sup></td>
      <td class=inp>Kelas:</td>
      <td class=ul1>$w[NamaKelas]</td>
      </tr>
  <tr><td colspan=4><hr color=silver size=3></td></tr>
  </table>
END;
  echo "<table class=bsc cellspacing=1 width=100%>
  <form name='frmJadwalTambahan' action='../$_SESSION[mnux].resedit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='jid' value='$jid' />
  <input type=hidden name='krsid' value='$krsid' />
  <input type=hidden name='krsresid' value='$krsresid' />
  <input type=hidden name='jenis' value='$jenis' />";
  
	echo "<tr>
    <th class=ttl width=10>Ambil</th>
    <th class=ttl>Keterangan</th>
	<th class=ttl width=15>Hari</th>
    <th class=ttl width=70>Jam<br />Kuliah</th>
	<th class=ttl width=80>Ruang</th>
	<th class=ttl width=50>Jmlh.<br>Siswa</th>
    <th class=ttl width=50>Kap.</th>
	</tr>";
	   $s1 = "select jr.JadwalID, jr.JadwalRefID, LEFT(jr.JamMulai, 5) as JM, LEFT(jr.JamSelesai, 5) as JS, 
					jr.RuangID, jr.JumlahMhsw, jr.Kapasitas, h.Nama as _NamaHari, jr.JenisJadwalID, jj.Nama as _NamaJenisJadwal
				from jadwal jr left outer join hari h on jr.HariID=h.HariID
								left outer join jenisjadwal jj on jj.JenisJadwalID=jr.JenisJadwalID
				where jr.JadwalRefID='$jid' and jr.KodeID='".KodeID."' and jr.JenisJadwalID='$jenis'
				order by jr.HariID, jr.JamMulai, jr.JamSelesai";
		$r1 = _query($s1);
		$n1 = 0; 
		while($w1 = _fetch_array($r1))
		{	$n1++;
			$class='ul1';
			$ada = GetaField('krs', "JadwalID='$w1[JadwalID]' and KodeID", KodeID, "KRSID");
			$checked = (empty($ada))? "": "checked";
			echo "<tr>
			  <td class=$class align=right>
				<input type=checkbox id='JdwlRes$w[JadwalID]of$n1' name='jresid[]' value='$w1[JadwalID]' onChange=\"ChooseLab('$w[JadwalID]', '$n1')\" $checked/>
				</td>
			  <td class=$class align=left width=50><b>$w1[_NamaJenisJadwal] #$n1</b></td>
			  <td class=$class align=center>$w1[_NamaHari]</td>
			  <td class=$class><sup>$w1[JM]</sup>&minus;<sub>$w1[JS]</sub></td>
			  <td class=$class align=center>$w1[RuangID]&nbsp;</td>
			  <td class=$class align=right>$w1[JumlahMhsw]&nbsp;</td>
			  <td class=$class align=right>$w1[Kapasitas]&nbsp;</td>
			  </tr>";
		}
	echo "<tr><td colspan=4><input type=submit name='Simpan' value='Simpan'></td></tr>";
	echo "<input type=hidden id='JdwlResCount$w[JadwalID]' name='JdwlResCount$w[JadwalID]' value='$n1'>";
	
	PilihLabKRSScript();
}

function Simpan($md, $jid, $krsid, $krsresid, $jenis) {
  $jresid = $_REQUEST['jresid'];
  $jres = '';
  foreach($jresid as $j)
  {	$jres = $j;
  }
  
  $krs = GetFields('krs', "KRSID='$krsid' and KodeID", KodeID, "*");
  
  
  
  // *** Cek semuanya dulu ***
  $oke = '';
  //if (!empty($w['UTSRuangID'])) $oke .= CekRuang($w, $jutsid);
  //$/oke .= CekTanggal($w, $jutsid);
  
  // Ambil data MK
  $mk = GetFields('mk', "MKID", $w['MKID'], "Nama,MKKode,KurikulumID,SKS,Sesi");
  // Jika semuanya baik2 saja
  if (empty($oke)) {
    // Jika mode=edit
    if ($md == 0) {
      $s = "update krs
        set JadwalID='$jres'
        where KRSID = '$krsresid'";
      $r = _query($s);
	  
	  TutupScript();
    }
    elseif ($md == 1) {
      $s = "insert into krs
		(KodeID, KHSID, MhswID, TahunID, JadwalID, 
		MKID, MKKode, Nama, SKS,
		LoginEdit, TanggalEdit)
		values
		('".KodeID."', '$krs[KHSID]', '$krs[MhswID]', '$krs[TahunID]', '$jres',
		'$krs[MKID]', '$krs[MKKode]', '$krs[Nama]', '0',
		'$_SESSION[_Login]', now())";
	  $r = _query($s);
	  
	  HitungPeserta($jdwl['JadwalID']);
      TutupScript();
    }
  }
  // Jika ada yg salah
  else {
	die(ErrorMsg('Ada Kesalahan', 
      "Berikut adalah pesan kesalahannya: 
      <ol>$oke</ol>
      <hr size=1 color=silver />
      <p align=center>
      <input type=button name='Kembali' value='Kembali' onClick=\"javascript:Kembali()\" />
	  <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />
      </p>"));
  }
}

function PilihLabKRSScript()
{	echo <<< SCR
		<script>
			function ChooseLab(mkid, target)
			{	count = document.getElementById('JdwlResCount'+mkid).value;
				for(i = 1; i <= count; i++)
				{	document.getElementById('JdwlRes'+mkid+'of'+i).checked = false;
				}
				document.getElementById('JdwlRes'+mkid+'of'+target).checked = true;
			}
		</script>
SCR;
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>

</BODY>
</HTML>
