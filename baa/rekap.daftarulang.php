<?php session_start();
// Rekap data pendaftaran ulang mahasiswa
// Author	: Arisal Yanuarafi
// Start	: 2 Februari 2014

// *** Parameter ***
$ProdiID = GetSetVar('ProdiID');
$TahunID = GetSetVar('WTahunID');

// *** GO! ***
TampilkanJudul('Rekapitulasi Data Pendaftaran Ulang');
$opttahun = GetOption2('tahun', "concat(TahunID)", 'TahunID', $_SESSION['WTahunID'],
    "KodeID='".KodeID."' group by TahunID", "TahunID"); //bila sudah final, hapus comment ini
$optprodi = GetProdiUser($_SESSION['_Login'],$ProdiID);

// Jumlah mahasiswa mendaftar ulang ke Portal

// Jumlah mahasiswa yang mengisi KRS

// Jumlah KRS yang belum disetujui

// Jumlah KRS yang sudah disetujui

// Jumlah mahasiswa yang sudah membayar

// Jumlah mahasiswa yang belum bayar

// Jumlah mahasiswa yang Aktif kembali

// Jumlah mahasiswa aktif kembali yang sudah bayar

// Jumlah mahasiswa aktif kembali yang belum bayar

?><form action="?" method="post">
<table class=bsc cellpadding="0" cellspacing="0">
	<tr>
    	<td class='inp'>Tahun Akademik:</td>
        <td class='ul1'><select name='WTahunID' onChange="this.form.submit()"><?php echo $opttahun?></select></td>
        <td class='inp'>Prodi:</td>
        <td class='ul1'><select name='ProdiID' onChange="this.form.submit()"><?php echo $optprodi?></select></td>
    </tr>
</table></form>
<?php $Printer = (!empty($ProdiID))? "<p align=center><a href='baa/wisuda.daftar.xls.php?ProdiID=$ProdiID&WTahunID=$TahunID'><img src='../img/excel.png'> Download</a></p>" : ''; 

//echo $Printer;?>

<table class=bsc cellpadding="0" cellspacing="0">
	<tr>
    	<th class="ttl">#</th>
        <th class="ttl">NPM</th>
        <th class="ttl">Nama</th>
        <th class="ttl">Tempat Lahir</th>
        <th class="ttl">Tanggal Lahir</th>
        <th class="ttl">SKS</th>
        <th class="ttl">IPK</th>
        <th class="ttl">Yudisium</th>
        <th class="ttl">Tgl Yudisium</th>
        <th class="ttl">Judul</th>
     </tr>
<?php
$s = "SELECT w.MhswID, m.Nama, m.TempatLahir, m.TanggalLahir, w.TanggalLahirFinal, w.TglSidang as TglSidang, m.Predikat, w.Judul, m.IPK, m.TotalSKS, w.Pembimbing, w.Pembimbing2
		from wisudawan w left outer join mhsw m on m.MhswID=w.MhswID
		where
				w.TahunID='$TahunID'
			AND m.ProdiID='$ProdiID' 
		order by m.MhswID";
$r = _query($s);
$n = 0;
while ($w = _fetch_array($r)) {
	$n++;
	?>
    <tr>
    	<td align="center" valign="middle"><?php echo $n?></td>
        <td align="center" valign="middle"><?php echo $w['MhswID']?></td>
        <td align="left" valign="top"><?php echo $w['Nama']?></td>
        <td align="left" valign="middle"><?php echo ucfirst($w['TempatLahir'])?></td>
        <td align="left" valign="middle"><?php echo (!empty($w['TanggalLahirFinal']))? $w['TanggalLahirFinal'] : TanggalFormat($w['TanggalLahir'])?></td>
        <td align="center" valign="middle"><?php echo $w['TotalSKS']?></td>
        <td align="center" valign="middle"><?php echo $w['IPK']?></td>
        <td align="center" valign="middle"><?php echo $w['Predikat']?></td>
        <td align="left" valign="middle"><?php echo TanggalFormat($w['TglSidang'])?></td>
        <td align="justify" valign="top"><?php echo FixStr($w['Judul'])?><br>
        								Pembimbing I: <?php echo $w['Pembimbing']?></td>
     </tr>
<?php
}
?>
</table>