<style>
td .fac{ font-family:Trebuchet MS; font-size:11px; font-weight:bold; }
header{ font-family:Tahoma;}
font,h4,h5{ font-family:Tahoma;}
table { font-family:Tahoma; font-size:10px}
.foto { -moz-border-radius: 4px; border:1px solid #cccccc; }
.belakang { background:url(../img/Alumni.png) center no-repeat; }
</style>
  <script>
  function TampilkanFoto(MhswID, Nama, Foto) {
    jQuery.facebox("<font size=+1>"+Nama+"</font> <sup>(" + MhswID + ")</sup><hr size=1 color=silver /><img src='"+Foto+"' />");
  }
  </script>
<?php error_reporting(0);
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb2.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
 	$jid = array();
  	$jid = $_REQUEST['mhswid'];
  	$tahunid = $_REQUEST['tahunid'];

				foreach($jid as $j) {
				$MhswID=$_REQUEST['Mhsw_'.$j];
				$hapus=$_REQUEST['hapus'.$j];
				$KHSID=$_REQUEST['KHSID_'.$j];
				$tahunid=$_REQUEST['TahunID_'.$j];
				if (!empty($hapus)) {
					if ($hapus=='x' || $hapus=='X') {   
							$s4="Select * from krs k where k.MhswID='$MhswID' and k.TahunID='$tahunid'";
							$r4=_query($s4);
							$nr=_num_rows($r4);
					echo "<h4>--No.BP: $MhswID --Tahun Akademik: $tahunid</h4>";
		if (!empty($nr)) {
					echo "<table class=box><tr><td align=center class=ul width=50><u>Kode MK</td><td align=center class=ul><u>Nama Matakuliah</td><td align=center class=ul><u>SKS</td><td align=center class=ul width=80><u>Grade Nilai</td><td align=center class=ul><u>KRSDumpID</td></tr>$s4---$j";
					while ($w4 = _fetch_array($r4)) {
//Masukan nilai ke KRS Dump		
$s8="insert into krsdump(KRSDumpID, KHSID,KRSID, MhswID, TahunID, JadwalID, MKID, MKKode, Nama, SKS, DosenID, UTS, UAS, NilaiAkhir,GradeNilai,BobotNilai,Tinggi,Final,RuangID,LoginBuat,TanggalBuat) values ('','$w4[KHSID]','$w4[KRSID]', '$w4[MhswID]', '$w4[TahunID]','$w4[JadwalID]','$w4[MKID]','$w4[MKKode]','$w4[Nama]','$w4[SKS]','$w4[DosenID]', '$w4[UTS]', '$w4[UAS]', '$w4[NilaiAkhir]','$w4[GradeNilai]','$w4[BobotNilai]','$w4[Tinggi]','$w4[Final]','$w4[RuangID]','$_SESSION[_Login]',now())";
// proses query insert
$r8=_query($s8);
// simpan KRSDUMPID
$id=mysql_insert_id();
					echo "<tr><td align=center class=ul>$w4[MKKode]</td><td class=ul>$w4[Nama]</td><td align=center class=ul>$w4[SKS]</td><td align=center class=ul>$w4[GradeNilai]</td><td align=center class=ul>$id</td></tr>";


				}
					echo "</table><font color=red size=2>Data KRS sudah di backup ke tabel KRSDUMP.</font>";
						$s_1="update khs set StatusMhswID='P' where MhswID='$MhswID' and TahunID='$tahunid'";
						$r8=_query($s_1);
						$s_2="update mhsw set StatusMhswID='P' where MhswID='$MhswID'";
						$r8=_query($s_2);
						$s1="delete from krs where MhswID='$MhswID' AND TahunID='$tahunid'";
						$r1=_query($s1);
					}
	else { echo "<font color=red size=2>tidak ada data KRS mahasiswa ini untuk tahun akademik $tahunid.</font>"; }
					echo "<h5>--Data KHS untuk mahasiswa dengan No.BP: $MhswID dan KHSID=$KHSID sudah diganti...</h5><hr>";
						$s_1="update khs set StatusMhswID='P' where MhswID='$MhswID' and TahunID='$tahunid'";
						$r8=_query($s_1);
						$s_2="update mhsw set StatusMhswID='P' where MhswID='$MhswID'";
						$r8=_query($s_2);
					}
										
				}
				
			}
				

?><title>Hapus Transaksi Nol Bayar</title>