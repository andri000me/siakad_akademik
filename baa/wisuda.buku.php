<?php 
session_start();
 	  include_once "../dwo.lib.php";
	  include_once "../db.mysql.php";
	  include_once "../connectdb.php";
	  include_once "../parameter.php";
	  include_once "../cekparam.php";

	$TahunID = GetSetVar('TahunWisuda'); 
	$s = "select DISTINCT(TahunID),Nama from wisuda where KodeID='".KodeID."' order by TahunID DESC";
	  $r = _query($s);
	  $opttahun = "<option value=''></option>";
	  while($w = _fetch_array($r))
		{  $ck = ($w['TahunID'] == $_SESSION['TahunWisuda'])? "selected" : '';
		   $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID] - $w[Nama]</option>";
		}
	  $_inputTahun = "<select name='TahunWisuda' onChange='this.form.submit()'>$opttahun</select>";
echo '
<style>
td .fac{ font-family:Tahoma; font-size:12px;  }
header{ font-family:Tahoma;}
body( backround-color:#fff; color:#000; }
</style>
<link href="baa/cetak.css" rel="stylesheet" type="text/css">';
 if (empty($_REQUEST['gos'])) { ?>
<form method="post" action="?"><table class="box"><tr><td class="inp">Tahun Wisuda:</td><td><?php echo $_inputTahun?></td></tr></table></form>
<?php
}
 $s1="select ProdiID,Nama,Gelar from prodi";
 $r1=_query($s1);
 while ($w1=_fetch_array($r1)) {
	$s="select m.Telepon,m.Handphone,Alamat,w.TanggalLahirFinal,m.NamaAyah,m.NamaIbu,m.IPK,m.TahunID, m.MhswID,m.Nama,m.ProdiID,m.TempatLahir,m.Foto,m.FotoWisuda,m.TanggalLahir from mhsw m, wisudawan w where w.MhswID=m.MhswID and m.ProdiID='$w1[ProdiID]' and w.TahunID='$TahunID' order by m.TahunID,m.MhswID";
	//echo $w1['ProdiID'];
				$r=_query($s);
				$n=_num_rows($r);
				$_n += $n;
if ($n != 0) {
$jenjang= GetaField('prodi',"ProdiID",$w1['ProdiID'],"JenjangID");
$_jenjang= GetaField('jenjang',"JenjangID",$jenjang,"Nama");
}
$nj=0;
				while ($w=_fetch_array($r)) {
				//if($w['FotoWisuda']!='') {
				$nj++;
  	 				
	//jurusan dan fakultas
	//$judul = GetFields('wisudawan', "MhswID", $w['MhswID'], 'Judul, Pembimbing,Pembimbing2,Predikat');
	//$prog = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $w[ProdiID], 'j.Nama as NMProg');
	//$jur = GetFields('prodi', "ProdiID", $w['ProdiID'], 'Nama as Nama');
	//$IPK = ($w['ProdiID'] == 'PGSD' ? HitungIPKPGSD($w['MhswID']) : HitungIPK($w['MhswID']));
	//imagejpeg($dst_image, 'new-image.jpg', 100);
	//if (!copy('../foto/wisudawan/sedang/'.$w['FotoWisuda'], '../foto/wisudawan67/'.$w['FotoWisuda'])){
?><table width=820><tr valign=top><td><table width=195 align=left height=300><tr valign=top><td><font size=1> <img src='../foto/wisudawan/<?php echo 'sedang/'.$w['FotoWisuda']; ?>' width=180></font></td></tr></table></td><td>
					<?php  echo "<table width=615 align=right>
					<tr><td>
					<font class=fac>Program Studi</font></td><td class=fac>:</td><td class=fac>$jur[Nama] ";/*$prog[NMProg]*/ echo"</td></tr>
					<tr><td>
					<font class=fac>Nama</font></td><td class=fac>:</td><td class=fac>".ucwords(strtolower($w['Nama'])).", $w1[Gelar]</td></tr>
					<tr valign=top><td width=170>
					<font class=fac>NPM</font></td><td class=fac width=3>:</td><td class=fac>$w[MhswID]</td></tr>	
					<tr><td>";
					
	
					echo"<font class=fac>Tempat/Tgl Lahir</td><td class=fac>:</td><td class=fac>$w[TempatLahir], ".((empty($w['TanggalLahirFinal']))? TanggalFormat($w['TanggalLahir']):$w['TanggalLahirFinal'])."</td></tr>
					<tr><td>
					<font class=fac>IPK</font></td><td class=fac>:</td><td class=fac>$IPK</td></tr>
					<tr valign=top><td>
					<font class=fac>Yudisium</font></td><td class=fac>:</td><td class=fac>$judul[Predikat]</td></tr>
					<tr valign=top><td>
					<font class=fac>Alamat</font></td><td class=fac>:</td><td class=fac>".ucfirst($w['Alamat'])."</td></tr>
					<tr valign=top><td>
					<font class=fac>Nomor Telepon/HP</font></td><td class=fac>:</td><td class=fac>$w[Telepon] $w[Handphone]</td></tr>
					<tr valign=top><td>
					<font class=fac>Nama Ayah</font></td><td class=fac>:</td><td class=fac>".ucfirst($w['NamaAyah'])."</td></tr>
					<tr valign=top><td>
					<font class=fac>Nama Ibu</font></td><td class=fac>:</td><td class=fac>".ucfirst($w['NamaIbu'])."</td></tr>
					</table></td></tr></table><br>
					";
				//}else{
			//echo '../foto/wisudawan67/'.$w['FotoWisuda'].'</br>';
		//}
				}
				//}
			}
		
?><title>Cetak Buku Wisuda</title>

