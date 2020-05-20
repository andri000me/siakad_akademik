<!doctype html><html><head><style>html{font-family:'Trebuchet MS';}</style><title>Tagihan Semester Mahasiswa</title></head><body>
<?php
session_start();error_reporting(E_ALL);
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  
$MhswID = ($_SESSION['_LevelID']=='120')? $_SESSION['_Login']: GetSetVar('MhswID');

TampilkanTagihanMhsw($MhswID);
?>
<script>window.print()</script>
</body>
</html>