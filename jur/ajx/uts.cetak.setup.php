<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 1 Okt 2013  */
	
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";

if ($_SESSION['_LevelID']!=120) $JadwalID = GetSetVar('a'); 
$w = GetFields("jadwal j
		left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
		left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
		left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
		left outer join mk mk on mk.MKID = j.MKID
		left outer join kelas k on k.KelasID = j.NamaKelas
		left outer join hari hr on hr.HariID=j.HariID
		left outer join jadwaluts jut on jut.JadwalID = j.JadwalID
		left outer join jadwaluas jua on jua.JadwalID = j.JadwalID
			left outer join hari huts on huts.HariID = date_format(jut.Tanggal, '%w')
		  left outer join hari huas on huas.HariID = date_format(jua.Tanggal, '%w')
		",
		"j.JadwalID", $JadwalID,
		"k.Nama as _NamaKelas, j.*, concat(d.Gelar1, ' ', d.Nama, ', ', d.Gelar) as DSN, d.NIDN,
		prd.Nama as _PRD, prg.Nama as _PRG, mk.Sesi, mk.PerSKS,
		date_format(jua.Tanggal, '%d-%m-%Y') as _UASTanggal,
		date_format(jut.Tanggal, '%d-%m-%Y') as _UTSTanggal,
		date_format(jut.Tanggal, '%w') as _UTSHari,
		date_format(jua.Tanggal, '%w') as _UASHari,
		huts.Nama as HRUTS,
		huas.Nama as HRUAS, mk.MKKode,
		hr.Nama as HariKuliah,
		j.JamMulai, j.JamSelesai,
		LEFT(jut.JamMulai, 5) as _UTSJamMulai, LEFT(jut.JamSelesai, 5) as _UTSJamSelesai,
		LEFT(jua.JamMulai, 5) as _UASJamMulai, LEFT(jua.JamSelesai, 5) as _UASJamSelesai
		");
if ($w['ProgramID']=='M' || $w['PerSKS']=='N'){
	$s = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$JadwalID'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID";
}
else {
$s = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, bipotmhsw b, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$JadwalID'
	AND h.MhswID=k.MhswID
	AND b.MhswID=k.MhswID
	AND b.TambahanNama like (concat('%',k.MKKode,'%'))
	AND b.Dibayar=(b.Jumlah*b.Besar)
	AND b.TahunID=k.TahunID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID";
}
$r = _query($s);
$n = _num_rows($r);
?>
<form class='form-horizontal' id='modal-form' action="#" name="formuji">
<input type='hidden' value='<?=$JadwalID?>' name='JadwalID'>
<table class=\"table table-striped\">
<tr><td class='inp'>Kode MK</td><td><?=$w['MKKode']?></td></tr>
<tr><td class='inp'>Matakuliah</td><td><?=$w['Nama']?></td></tr>
<tr><td class='inp'>Jumlah Mhsw</td><td><?=$n?> orang</td></tr>
<tr><td class='inp'>Mhsw per Ruang</td><td>
	<input type="text" name="PerRuang" size="4" maxlength="3" />
	<input type="button" value="Atur" onclick="javascript:aturuji('<?=$JadwalID?>');" /></td></tr>
<tr><td class='inp'>Cetak</td><td><span id='ucetak'></span></td></tr>
</table>
</form>

