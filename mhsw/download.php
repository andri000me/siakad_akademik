<?php ob_start(); session_start();
// Author 	: Arisal Yanuarafi
// Start 	: 02 November 2012
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
$Filenya = GetaField('dosenbahanajar', "MD5Code",$_REQUEST['fn'],"concat(REPLACE(LEFT(TanggalBuat,10),'-',''),REPLACE(RIGHT(TanggalBuat,8),':',''),NIDN,'_',MD5Code,'.',TipeFile)");
//$cek = GetaField('log',"Script like '%Fn=$_POST[fn]' and Login",$_REQUEST[MhswID],'count(WaktuAkses)');
$File = GetaField('dosenbahanajar', "MD5Code",$_REQUEST['fn'],"Nama");
$ukuran =GetaField('dosenbahanajar', "MD5Code",$_REQUEST['fn'],"Ukuran");
$file_extension = GetaField('dosenbahanajar', "MD5Code",sqling($_REQUEST['fn']),"TipeFile");
	switch($file_extension){
	  case "pdf": $ctype="application/pdf"; break;
	  case "exe": $ctype="application/octet-stream"; break;
	  case "zip": $ctype="application/zip"; break;
	  case "rar": $ctype="application/rar"; break;
	  case "doc": $ctype="application/msword"; break;
	  case "xls": $ctype="application/vnd.ms-excel"; break;
	  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
	  case "gif": $ctype="image/gif"; break;
	  case "png": $ctype="image/png"; break;
	  case "jpeg":
	  case "jpg": $ctype="image/jpg"; break;
	  default: $ctype="application/proses";
	}
//if ($cek == 0) {
	if ($file_extension=='php'){
	  echo "<h1>Access forbidden!</h1>
			<p>Maaf, file yang Anda download sudah tidak tersedia atau filenya (direktorinya) telah diproteksi. <br />
			Silahkan hubungi SysAdmin.</p>";
	  exit;
	}
	else {
			$direktori = '../file/bahanajar/';
			$s = "update dosenbahanajar set TotalDownload=TotalDownload+1 where MD5Code='$_REQUEST[fn]'";
			$r = _query($s);
			//readfile("$direktori$Filenya");
		//die("$direktori$Filenya");
			header("Content-Type: octet/stream");
		  header("Pragma: private"); 
		  header("Expires: 0");
		  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		  header("Cache-Control: private",false); 
		  header("Content-Type: $ctype");
		  header("Content-Disposition: attachment; filename=\"".$File."\";" );
		  header("Content-Transfer-Encoding: binary");
		  header("Content-Length: ".filesize($direktori.$Filenya));

		  readfile("$direktori$Filenya");
 			exit();
		  }
//}
?>?