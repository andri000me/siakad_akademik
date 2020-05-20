  <header class="main-header">
    <!-- Logo -->
    <a href="index.php?mnux=zayed" class="logo">
      <span class="logo-lg"><b>MAHASISWA</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a> 
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
           <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              
              <span class="hidden-xs"><?= $_SESSION['_Nama'] ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
            
                <p>
                  <?= $_SESSION['_Nama'] ?> - Mahasiswa
                  <small>Login Terakhir <?= date('Y-m-d H:i:s') ?></small>
                </p>
              </li>
              <!-- Menu Body -->
               
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?= base_url().'profile'?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                   <a href="<?= base_url().'logout'?>" onclick="return(confirm('Ada Yakin Keluar ?'))" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
   <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
       <ul class="sidebar-menu" data-widget="tree">
        <li class="header"><?= $_SESSION['_Nama'] ?></li>
        <li><a href="<?= base_url().'home' ?>"><i class="fa fa-home"></i> <span>Home</span></a></li>
        <li><a href="<?= base_url().'mhs_krs' ?>"><i class="fa fa-graduation-cap"></i> <span>KARTU RENCANAN STUDI (KRS)</span></a></li>
        <li><a href="<?= base_url().'khs' ?>"><i class="fa fa-graduation-cap"></i> <span>KARTU HASIL STUDI (KHS)</span></a></li>
        <li><a href="<?= base_url().'transkrip_nilai' ?>"><i class="fa fa-graduation-cap"></i> <span>TRANSKIP NILAI</span></a></li>
		<li><a href="<?= base_url().'edom' ?>"><i class="fa fa-list"></i> <span>EVALUASI DOSEN</span></a></li>
		<li><a href="<?= base_url().'bahan_ajar' ?>"><i class="fa fa-book"></i> <span>BAHAN AJAR</span></a></li>
      </ul>
     </section>
    <!-- /.sidebar -->
  </aside>