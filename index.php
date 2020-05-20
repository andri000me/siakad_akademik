<?php
ini_set('display_errors', 0);
date_default_timezone_set("Asia/Bangkok");
session_start();

$waktu_login = $_SESSION['waktu_login']; 
$batas = time()-$waktu_login;

if ($_SESSION['timeout'] <= $batas) {
echo "<script>window.location='auth/login?log=true';</script> ";
session_destroy();
exit();  
};  
if(!empty($_SESSION['mnux']) AND  !empty($_SESSION['_LevelID'])){  
  include_once "dwo.lib.php";
  include_once "db.mysql.php";
  include_once "connectdb.php";
  include_once "parameter.php";
  include_once "cekparam.php";
  $mdlid = GetSetVar('mdlid'); 
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SISTEM MANAJEMEN KAMPUS</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/bootstrap/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/dist/css/AdminLTE.min.css"> 
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/dist/css/AdminLTE.min.css">
    <!-- thirparty -->
 
      <link rel="stylesheet" href="<?= base_url() ?>themes/unes/css/unes_padang.css"> 
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/dist/css/global.css"> 
        <link href="themes/unes/css/jquery-ui-1.8.21.custom.css" rel="stylesheet">
    <link href='themes/unes/css/chosen.css' rel='stylesheet'>
    <link href='themes/unes/css/uniform.default.css' rel='stylesheet'>
    <link href='themes/unes/css/colorbox.css' rel='stylesheet'>
    <link href='themes/unes/css/jquery.cleditor.css' rel='stylesheet'>
    <link href='themes/unes/css/jquery.noty.css' rel='stylesheet'>
    <link href='themes/unes/css/noty_theme_default.css' rel='stylesheet'>
    <link href='themes/unes/css/opa-icons.css' rel='stylesheet'>
    <script src="themes/unes/js/jquery-1.7.2.min.js"></script>
    <link type="text/css" rel="stylesheet" media="all" href="chat/css/chat.css" />
    <link type="text/css" rel="stylesheet" media="all" href="chat/css/screen.css" />
    <script type="text/javascript" src="chat/js/chat.js"></script>
    <script src="themes/unes/js/bootstrap-dropdown.js"></script>
    <link href="fb/facebox.css" media="screen" rel="stylesheet" type="text/css" />
    <script src="fb/facebox.js" type="text/javascript"></script>
    <script src='themes/unes/js/jquery.dataTables.min.js'></script>


  </head>
  <style type="text/css">
    .sidebar-menu .treeview-menu{
      background: #0e839f !important;
      border-left: 4px solid #f8ff00; 
    }
    .box-header .well{
      background: #fea724;
        border-radius: 0px 0px 0px;
        color:#000;
    }

    h2{
      font-size: 17px;
    }
  </style>

  <?php if($_SESSION['_TabelUser'] != 'mhsw'):  
    $body= 'hold-transition skin-blue layout-top-nav';
  else:
    $body= 'hold-transition skin-blue sidebar-mini';
  endif;
  ?> 
  <body class="<?= $body ?>">
    <div class="wrapper" style="height: auto;min-height: 100%;background: #fff;">
      <?php if($_SESSION['_TabelUser'] != 'mhsw'):  ?> 
        <header class="main-header">
          <nav class="navbar navbar-static-top">

            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
              <ul class="nav navbar-nav"> 
                <?php
                if (!empty($_SESSION['_Session'])) { ?> 
                  <li><a href='?mnux=zayed'><i class="fa fa-home"></i><span>Home</span></a></li>
                  <?php $NamaLevel = GetaField('level', 'LevelID', $_SESSION['_LevelID'], 'Nama');
                  include "menusis.php"; 
                } ?> 
              </ul>

            </div>
            <!-- /.navbar-collapse -->
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
              <ul class="nav navbar-nav">
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                  <!-- Menu Toggle Button -->
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                    <span class="hidden-xs"><?= ucfirst($_SESSION['_Nama']) ?></span>
                  </a>
                  <ul class="dropdown-menu">
                    <!-- The user image in the menu -->
                    <li class="user-header">
                      <p>
                        <?= $_SESSION['_Nama'] ?> - <?= $_SESSION['Level_akses'] ?>
                        <small>Login Terakhir <?= date('Y-m-d H:i:s') ?></small>
                      </p>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body">
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                      <div class="pull-left">
                        <a href="<?= base_url().'profile' ?>" class="btn btn-default btn-flat">Profile</a>
                      </div>
                      <div class="pull-right">
                        <a href="<?= base_url().'logout' ?>" onclick="return(confirm('Ada Yakin Keluar ?'))" class="btn btn-default btn-flat">Sign out</a>
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <!-- /.navbar-custom-menu -->

            <!-- /.container-fluid -->
          </nav>
        </header>
        <?php else: include 'menu_mhs.php'; endif; ?> 

        <div class="content-wrapper">

          <div id='onlineUser' onClick='javascript:openUser()'></div><div class='box-header well' data-original-title style="background: #fff">
            <section class="content" style="background: #fff"> 

              <?php 
              $rn=isset($_GET['rn']) ? $_GET['rn'] :'';
              $wp= isset($_GET['wp']) ? $_GET['wp'] : '';

              if ($rn != '' AND $rn = 'berhasil') {
                echo '<div class="callout callout-info"><i class="fa fa-info"></i>Selamat Datang Di Halaman Administrasi Akademik Level Akses '.ucfirst($_SESSION['NamaL']).'</div>';
              }
              if ($wp == 1) { 
                echo "<div class='callout callout-danger'>Karakter Inputan Tidak Di izinkan , pastikan Karakter Inputan Sudah Benar .</div>";
              }

              ?>

              <br />  
              <?php


              $out=isset($_GET['out']) ? $_GET['out'] : '';
              if ($out == 'success') {
                echo "<div class='callout callout-info'><b>Data Berhasil Di Simpan</b></div>";   
              }
              if (file_exists($_SESSION['mnux'].'.php')) {
                if ($_SESSION['mnux'] == 'login') {
                  echo "<script>window.location='index.php?mnux=zayed';</script>";
                  exit();
                }

                $sboleh = "SELECT * from mdl where Script='$_SESSION[mnux]'";
                $rboleh = _query($sboleh); $ktm = -1;
                if (_num_rows($rboleh) > 0) {
                  while ($wboleh = _fetch_array($rboleh)) {
                    $level=isset($_SESSION['_LevelID']) ? $_SESSION['_LevelID'] : '';
                    $pos = strpos($wboleh['LevelID'], ".$level.");
                    if ($pos === false) {
                    }
                    else $ktm = 1;
                  }
                  if ($ktm <= 0) {
                    echo ErrorMsg('Akses Denied', "Menu Tidak Di Perkenan Kan diakses ");
                  }
                  else include_once $_SESSION['mnux'].'.php';
                } else include_once $_SESSION['mnux'].'.php';

              }
              else echo ErrorMsg('Fatal Error', "Modul tidak ditemukan. Hubungi Administrator!!!<hr size=1 color=silver>
                Pilihan: <a href='?mnux=&KodeID=$_SESSION[KodeID]'>Kembali</a>");

                ?>
              </section>
            </div>

          </div>
          <script src="tpl/nahyan/dist/js/adminlte.min.js"></script>
          <script src="themes/unes/js/jquery-ui-1.8.21.custom.min.js"></script>
          <!-- alert enhancer library -->
          <script src="themes/unes/js/bootstrap-alert.js"></script>
          <!-- modal / dialog library -->

          <script src="themes/unes/js/bootstrap-tooltip.js"></script>

          <script src="themes/unes/js/jquery.cookie.js"></script>
          <!-- select or dropdown enhancer -->
          <script src="themes/unes/js/jquery.chosen.min.js"></script>
          <!-- checkbox, radio, and file input styler -->
          <script src="themes/unes/js/jquery.uniform.min.js"></script>
          <!-- rich text editor library -->
          <script src="themes/unes/js/jquery.cleditor.min.js"></script>
          <!-- notification plugin -->
         
          <!-- autogrowing textarea plugin -->
          <script src="themes/unes/js/jquery.autogrow-textarea.js"></script>
          <script src="themes/unes/js/jquery.history.js"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.6/jquery.slimscroll.min.js"></script>
          <script src="themes/unes/js/unes_pdg.js"></script>

        </body>
        </html>
      <?php }else{ ?>
        <script>window.location='auth/login?log=true';</script> 
        <?php

      } ?> 