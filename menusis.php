<?php 

$sid = session_id();
$agent = $_SERVER['HTTP_USER_AGENT'];

function menu() {
  $_snm = session_name(); $_sid = session_id();
  $_Login = $_SESSION['_Login'];
  $_LevelID = $_SESSION['_LevelID'];
  $_LoginID = isset($_SESSION['_LoginID']) ? $_SESSION['_LoginID']:'';
  
  $_ggl = "<p>Gagal menginisialisasi menu</p><p>Failed to initialised menus</p>";
  $strLevelID = '.'.$_LevelID.'.';
  $strLevel = '.'.$_LevelID.'.';

  /*bagian query*/
  $_res = _query("SELECT mg.MdlGrpID as GM ,mg.Nama as Nama_menu
    from mdl m
    left outer join mdlgrp mg on m.MdlGrpID=mg.MdlGrpID
    where m.Web='Y' and LOCATE('$strLevelID', m.LevelID)>0 and m.NA='N'
    group by mg.Urutan");

  /*query pertaman sistem*/  
  
  while ($menu_s=mysql_fetch_array($_res)) {
    $menu=$menu_s['Nama_menu'];
    $gm=$menu_s['GM'];
 
    $_rs1 = _query("SELECT m.*
    from mdl m
    where LOCATE('$strLevel', m.LevelID)>0 and m.Web='Y' and m.MdlGrpID='$gm' and m.NA='N'
    order by m.Nama") or die($_ggl . mysqli_error());
    $cek=mysql_num_rows($_rs1);    
     if ($cek > 0) {
  
    echo "<li class='dropdown'>
                <a href='javascript:void(0)' class='dropdown-toggle' data-toggle='dropdown'>
                 <i class='fa fa-graduation-cap'></i><span>$menu</span>
                 <i class='fa fa-angle-left pull-right'></i></a>
                  <ul class='dropdown-menu' role='menu'>";
    while ($_w1 = mysql_fetch_array($_rs1)) {
    echo "<li><a href=\"?mnux=$_w1[Script]&mdlid=$_w1[MdlID]&$_snm=$_sid\"><i class='fa fa-list'></i><span>$_w1[Nama]</span></a></li>";
    }
  }else{
    while ($_w1 = mysql_fetch_array($_rs1)) {
    echo "<li><a href=\"?mnux=$_w1[Script]&mdlid=$_w1[MdlID]&$_snm=$_sid\"><span>$menu_s[Nama]</span></a></li>";
    } 
  }
    echo"</ul>
   </li>";
}
}

menu();
?>