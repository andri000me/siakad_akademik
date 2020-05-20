<?php 
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 17 Agustus 2014  */
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
$md = $_REQUEST['md'];
$val = $_REQUEST['v'];

if ($md=1){
	$result = GetOption3('app_pustaka1.mst_publisher', "publisher_name", 
    '', $val, "", 'publisher_id');
}
 

if ($md=1){
// Pengarang
$s1 = "SELECT a.*, b.author_id as AI from app_pustaka1.mst_author a left outer join app_pustaka1.biblio_author b on b.author_id=a.author_id and b.biblio_id='$val'";
	$r1 = _query($s1);
	while ($w1 = _fetch_array($r1)) {
		$result .= "<option value='$w1[author_id]' ".($w1['AI']==$w1['author_id']? "Selected='selected'":"").">$w1[author_name]</option>";
	}
}
echo $result;
?>