<?php
error_reporting(0);
if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

if(!empty($_SESSION['mnux']) AND  !empty($_SESSION['_LevelID'])){  
function get_hitung(){
if (count($_GET) > 0) {
    foreach ($_GET as $k=>$v) {
     $cocokan="/^union/";
     $hasget=preg_match($cocokan, $_GET[$k]);
     if ($hasget) {
        header("location:index.php?mnux=".$_SESSION['mnux']);
         exit();
     }else{
      
     }
   }
 }
}

$mnux = GetSetVar('mnux', $_defmnux);
if (empty($mnux)) {
  $mnux = $_defmnux;
  $_SESSION['mnux'] = $_defmnux;
}

if (empty($_SESSION['_Session']) && empty($mnux)) {
  $mnux = $_defmnux;
  $_SESSION['mnux'] = $_defmnux;
  $_SESSION['mdlid'] = 0;
}
}else{
   echo "Direcktori Access Forbiden.";
   exit();

}


?>
