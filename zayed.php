<?php
$rn = (empty($_REQUEST['rn']))? 'berhasil' : $_REQUEST['rn'];
$rn();
 
 
function berhasil() {
    if ($_SESSION['_TabelUser']  == 'karyawan') {
        include 'dasboard/kariawan.php';
    }elseif($_SESSION['_TabelUser'] == 'dosen'){
        include 'dasboard/dosen.php';
    }elseif($_SESSION['_TabelUser'] == 'mhsw'){
        include 'dasboard/mhs.php';
    }


} 

function lout() { 
  session_start(); 
  session_destroy();
  echo "<script>window.location='auth/login?log=true';</script>";
}

 

?>
