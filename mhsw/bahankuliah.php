<?php ob_start(); ob_flush(); ?>
<!-- 	
Author	: Arisal Yanuarafi
Start	: 24 Oktober 2012 -->

<?php
$FileID = $_GET['Fn'];
TampilkanJudul("Download Bahan Kuliah");
$MhswID = $_SESSION['_Login'];
CekBolehAksesModul();
if (!empty($FileID)) { cekSudahPernahAkses($FileID,$MhswID); }
else TampilkanDownload($MhswID);

// *** here is the Function ***
function cekSudahPernahAkses($id,$MhswID) {
$cek = GetaField('log',"Script like '%Fn=$id' and Login",$MhswID,'count(WaktuAkses)');
if ($cek > 1) {
	echo "<blink><strong><font color=darkred>File sudah pernah Anda download sebelumnya.</font></strong></blink><br>
		";
	}
TampilkanDownload($MhswID); 
}

function DownloadFile($id,$MhswID) {

}

function TampilkanDownload ($MhswID) {
//$ProdiID = GetaField('mhsw',"MhswID", $MhswID,'ProdiID');
$prodi = $_SESSION['_ProdiID'];
$prodi = TRIM($prodi, ',');
  $p = explode(',', $prodi);
  $_p = array();
  foreach ($p as $prd) {
    $_p[] = "'".$prd."'";
  }
  if (empty($_p)) $_p[] = "'xqyalajdlflkajshdf'";
  $prodi = implode(',', $_p);
$TahunID = GetaField('tahun', "NA", N, "TahunID");
	echo "<form method='post' action='?'>Silakan ketik nama dosen <input type='text' name='NMD_BA' value='$_POST[NMD_BA]'> <input type=submit value='Cari'></form>";
  if (empty($_POST['NMD_BA'])) die('');
$s = "Select ds.NIDN as NIDN, (concat(d.Gelar1, ' ',d.Nama,', ',Gelar)) as Nama from dosenbahanajar ds left outer join mk m on m.MKID=ds.MKID, dosen d where d.NIDN=ds.NIDN
								AND Hide='N' AND d.Nama like '%$_POST[NMD_BA]%' group by d.NIDN order by d.Nama";
$r = _query($s);
while ($w = _fetch_array($r)) {
//$data = GetFields('jadwal', "JadwalID", $w[JadwalID], "DosenID,MKKode,Nama");
$Login = GetaField('dosen',"NA='N' AND NIDN",$w['NIDN'],"Login");
// *** Cari File yang berhubungan dengan Jadwal Ini
	$s1 = "Select * from dosenbahanajar where NIDN='$w[NIDN]' AND Hide='N' order by NIDN, Matakuliah, Nama";
	$r1 = _query($s1);
	if ($r1) {
	?><p>&nbsp;</p>
    <table class="bsc" width="800" cellpadding="0" cellspacing="0">
    	<tr>
        <td align="center">
	<h4><?php echo $w['Nama'];?></h4>
    	</td>
     </tr>
     <tr>   
     <td>
    <table class="bsc" width="800" cellpadding="0" cellspacing="0">
    	<tr>
    		<th class="ttl">No</td>
            <th class="ttl">Matakuliah</td>
            <th class="ttl">Bahan Kuliah</td>
            <th class="ttl" width=80>Besar</td>
            <th class="ttl"  width=100>Download</td>
        </tr>
    <?php
	$n=0;
	while ($w1 = _fetch_array($r1)) {
	$n++;
	?>
    	<tr>
        	<td class="inp"><?php echo $n; ?></td>
            <td class="ul1"><?php echo $w1['Matakuliah']; ?></td>
            <td class="ul1"><?php echo $w1['Nama']; ?></td>
            <td class="ul1" align="right"><?php echo $w1['Ukuran']; ?> KB</td>
            <td class="ul1" align="center"><form action="mhsw/download.php" method="post">
			<?php echo "<input type=hidden name=mnux value='$_SESSION[mnux]'>";
				 echo "<input type=hidden name=fn value='$w1[MD5Code]'>"; 
                 echo "<input type=hidden name=MhswID value='$_SESSION[_Login]'>"; ?>
                 <input type="submit" value="Download" /> <sub><?php echo $w1['TotalDownload']; ?>&times;</form></sub></td>
        </tr>
     <?php
	 } // / end while $w1
	 echo "</table>";
	} // end if
	echo "</td></tr></table>";
   } // / end while $w
            
}

function CekBolehAksesModul() {
$arrAkses = array(1, 120, 20, 100, 440, 66, 50);
  $key = array_search($_SESSION['_LevelID'], $arrAkses);
  if ($key === false)
    die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut."));
}
?>
