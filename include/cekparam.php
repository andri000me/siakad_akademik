<?php
// Author: Emanuel Setio Dewo
// Oktober 2005

function ResetLogin() {
  global $_defmnux;
  $_SESSION['mnux'] = $_defmnux;
  $_SESSION['mdlid'] = 0;
  $_SESSION['_Login'] = '';
  $_SESSION['_Nama'] = '';
  $_SESSION['_TabelUser'] = '';
  $_SESSION['_LevelID'] = 0;
  $_SESSION['_Session'] = '';
  $_SESSION['_Superuser'] = 'N';
  $_SESSION['_KodeID'] = '';
  $_SESSION['_ProdiID'] = '';
  
}

$mnux = GetSetVar('mnux', $_defmnux);
if (empty($mnux)) {
  $mnux = $_defmnux;
  $_SESSION['mnux'] = $_defmnux;
}
if (empty($_SESSION['_Session'])) {
  $mnux = $_defmnux;
  $_SESSION['mnux'] = $_defmnux;
  $_SESSION['mdlid'] = 0;
}

?>