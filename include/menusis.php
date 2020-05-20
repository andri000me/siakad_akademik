<?php
$sid = session_id();
$agent = $_SERVER['HTTP_USER_AGENT'];

include_once "dwo.mnu.php";

$modul = array();
$modul = GetUserModule();

//var_dump($modul); exit;
// Buat Menu
StartMenu($modul);

foreach ($modul as $submenu=>$key) {
    GetModule($submenu);
}

RunMenu();
?>