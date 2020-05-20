<?php  
error_reporting(0);
include_once "dwo.lib.php"; 
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "parameter.php";
  $un = _fetch_array(_query("SELECT Nama from identitas order by Kode"));
  $universitas=isset($un['Nama']) ? $un['Nama'] : 'Undefined';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>
  LOGIN <?= strtoupper($universitas) ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/bootstrap/css/bootstrap.min.css">
    <link rel="shortcut icon" href="https://cdn.sstatic.net/Sites/stackoverflow/img/favicon.ico?v=4f32ecc8f43d">
    <script src="<?= base_url() ?>tpl/nahyan/ckeditor/ckeditor.js"></script>
    <script src="<?= base_url() ?>tpl/nahyan/ckeditor/styles.js"></script>
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/bootstrap/font-awesome/css/font-awesome.min.css">
    <script type="text/javascript" language="javascript" src="include/js/dropdowntabs.js"></script>
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>tpl/nahyan/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css" /> 
    <link rel="stylesheet" href="<?= base_url() ?>/tpl/nahyan/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/tpl/ddcolortabs.css">
    <link rel="stylesheet" href="<?= base_url() ?>/tpl/styles_menu.css">
    <link rel="stylesheet" href="<?= base_url() ?>/tpl/app_stl.css">
    <script src="<?= base_url() ?>tpl/nahyan/dist/jquery-1.11.2.min.js"></script> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  </head>
  <body style="background: #ddd; overflow: hidden;">
  <?php include 'login_cek.php'; ?>
  <div class="login-box">
    <div class="login-logo">
   </div>

   
   <div class="login-box-body">
    <p class="login-box-msg">
  <img src="https://icge.unespadang.ac.id//asset/home/img/unes.png" class="user-image" style="width:70px;height:70px" alt=""><br>
  <div style="text-align:center;font-size:20px"><b>PORTAL AKADEMIK</b></div></p><p style="color:#f48f42;text-align:center"></p>

    <form action="" method="POST">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Username .." name="username" required="">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password .." name="password" required="">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>


    <div align="center" style="width:100%;">Question : 
    What is ? <b><?= $_SESSION['val1'].'+'.$_SESSION['val2'] ?></b> </div>
      <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="Your Answer" name='kode_keamanan' required=""/>
            <span class="fa fa-eye form-control-feedback"></span>
          </div>

        <!-- /.col -->
        <div style="padding-left:60%">
          <button type="submit" name="login" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
     <br />
     <?= $notif=isset($_SESSION['pesan']) ? $_SESSION['pesan'] : ''?>

  </div>
</div>

<script src="<?= base_url() ?>tpl/nahyan/dist/js/jquery.min.js"></script>
<script src="<?= base_url() ?>tpl/nahyan/dist/js/bootstrap.min.js"></script>
</body>
</html>
