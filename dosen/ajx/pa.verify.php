<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 10 Sept 2013  */
	
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";

if ($_SESSION['_LevelID']==100) $KHSID = GetSetVar('a'); $MhswID = GetSetVar('b');
?>
<form class='form-horizontal' id='modal-form' method=post action='verify'>
<input type=hidden name='gos' value='SAV'>
<input type='hidden' value='<?php echo $KHSID?>' name='KHSID'>
<input type='hidden' value='<?php echo $MhswID?>' name='MhswID'>

<?php $khs = GetFields('khs',"KHSID", $KHSID, "*");
$setujui = ($khs['MaxSKS'] >= $khs['SKS'] || $khs['Sesi'] <= 2) ? "<input type=radio value='Y' name='Status'> <img src='img/diterima.gif' /> Setujui": "<input type=radio value='' disabled name='Status'> <img src='img/diterima.gif' /> Setujui";
$_Pesan = ($khs['MaxSKS'] >= $khs['SKS'] || $khs['Sesi'] <= 2) ? "...": "Maksimal SKS yang bisa Anda Ambil $khs[MaxSKS] SKS."; ?>
<table class=\"table table-striped\">
<tr><td colspan='2'><b>Petunjuk Penggunaan:</b>
					<ul>
                    	<li>Jika Disetujui: Sistem akan mengunci akses mahasiswa untuk melakukan
                        			edit/hapus KRS dan memberikan informasi bahwa KRS yang diajukan sudah disetujui.</li>
                        <li>Jika Ditolak: Mahasiswa akan menerima pesan penolakan atas KRS yang diambil sebelumnya dan
                        	dapat memperbaiki kembali sesuai instruksi pada alasan penolakan.</li>   
                    </ul></td></tr>
<tr><td class='inp'>Tindakan</td><td> <?php echo $setujui; ?> <input type=radio name='Status' value='N'> <img src="img/ditolak.gif" /> Tolak</td></tr>
<tr><td class='inp'>Alasan <sup>*) Jika ditolak</sup></td><td><input type="text" name="Alasan" size="30" maxlength="250" value="<?php echo $_Pesan?>" /></td></tr>
</table>
</form>
