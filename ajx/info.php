<?php 

session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 10 Sept 2013  */
	
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
$Jumlah = GetSetVar('a');  ?>
<form id='modal-form' action="evaluasidosen" method="post">Anda belum mengisi <?php echo $Jumlah; ?> Formulir Evaluasi Dosen. 
Untuk bisa melihat Nilai matakuliah yang Anda ambil pada semester ini, Anda diharapkan mengisi formulir evaluasi terhadap masing-masing dosen
yang bersangkutan terlebih dahulu.</form>