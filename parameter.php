<?php
// Parameter
$_ProductName = "Sisfo Kampus";
$_Institution = "Sistem Akademik ";
$_Identitas = "CL1";
$_Version = "Sumbar";
$_Author = "Ehsan Zayed Annahyan";
$_AuthorEmail = "kotokareh@gmail.com";
$_URL = "";

$thems = "tpl/nahyan";
function base_url(){
 $config = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
 $config .= "://".$_SERVER['HTTP_HOST'];
 $config.= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
return $config;
}

if (!defined('KodeID')) define('KodeID', $_Identitas);
$arrID = GetFields('identitas', 'Kode', 'KodeID', '*');
$_lf = "\r\n";
$_defmnux = 'login';
$_maxbaris = 10;
$_PMBDigit = 4;
$arrBulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
  'Agustus', 'September', 'Oktober', 'November', 'Desember');
$arrHari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');

function update_chat($sessionId,$username,$_LevelID,$address,$sessionTime,$sessionStart){
     mysql_query("INSERT INTO session set sessionId = '$sessionId',user = '$username', LevelID = '$_LevelID',address ='$address',sessionTime='$sessionTime',sessionStart='$sessionStart'"); 
}

/*session expired*/

function isLoginSessionExpired() {
	$login_session_duration = 10; 
	$current_time = time(); 
	if(isset($_SESSION['loggedin_time']) and isset($_SESSION["user_id"])){  
		if(((time() - $_SESSION['loggedin_time']) > $login_session_duration)){ 
			return true; 
		} 
	}
	return false;
}

/*end session*/



?>
