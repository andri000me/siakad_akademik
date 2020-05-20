 <?php 
 if($_SESSION['_TabelUser'] == 'mhsw'): 
    if (isset($_POST['kirim'])) {
    	if ($_POST['passw1'] != $_POST['passw2']) {
          echo '<div class="callout callout-danger">Password yang anda Masukan tidak sama silahkan ulangi kembali.</div>
              <hr />
            <a href="'.base_url().'profile" class="btn btn-primary">kembali</a>
           	';
       	}else{

           echo "FITUR SAAT INI DI NON AKTIFKAN";
           exit();
           // $password = barasiah($_POST['passw2']);   
           // $pwd = "Password=PASSWORD('$password')";
           // $cek= mysql_query("UPDATE mhsw set $pwd where MhswID='". $_SESSION['mhswid']."'"); 
           // if ($cek) {  
           // 	echo '<div class="callout callout-danger">pasword berhasil di ubah ke'.$_POST['passw2'].'harap di inga password .</div>';
           // }
         }  
       }else{
       echo '
       ** ) pastikan password login yang di ubah di ingat sebelumnya dan silahkan cocokan kembali password yang di ubah pada perulangan password yang di bawah
       <form action="" method="POST">
       <table class="table table-striped">
             <tr><td>Password Login</td><td><input type="password" name="passw1" class="form-control"></td></tr>
             <tr><td>Password Login</td><td><input type="password" name="passw2"  class="form-control"></td></tr>
             <tr><td></td><td><button class="btn btn-primary" name="kirim">Ubah Password</button></td></tr>
         </table>
         </form>';
      
    }
 elseif($_SESSION['_TabelUser']  == 'karyawan'):
       if (isset($_POST['kirim'])) {
       	if ($_POST['passw1'] != $_POST['passw2']) {
          echo '<div class="callout callout-danger">Password yang anda Masukan tidak sama silahkan ulangi kembali.</div>
              <hr />
            <a href="'.base_url().'profile" class="btn btn-primary">kembali</a>
           	';
       	}else{ 
             echo "FITUR SAAT INI DI NON AKTIFKAN";
           exit();
           //  $password = barasiah($_POST['passw2']);   
           //  $pwd = "Password=PASSWORD('$password')";
           // $cek= mysql_query("UPDATE karyawan set $pwd where Login='". $_SESSION['_Login']."'"); 
           // if ($cek) {  
           // 	echo '<div class="callout callout-success"><i class="fa fa-check"></i>pasword berhasil di ubah ke '.$_POST['passw2'].' harap di ingat password .</div>
           //    <hr />
           //  <a href="'.base_url().'profile" class="btn btn-primary">kembali</a>
           // 	';
           // }
         }
       }else{
       echo '
       ** ) pastikan password login yang di ubah di ingat sebelumnya dan silahkan cocokan kembali password yang di ubah pada perulangan password yang di bawah.
       <form action="" method="POST">
       <table class="table table-striped">
             <tr><td>Password Pertama</td><td><input type="password" name="passw1" class="form-control"></td></tr>
             <tr><td>Ulangi Password</td><td><input type="password" name="passw2"  class="form-control"></td></tr>
             <tr><td></td><td><button class="btn btn-primary" name="kirim">Ubah Password</button></td></tr>
         </table>
         </form>';
      
    }
 elseif($_SESSION['_TabelUser']  == 'dosen'):
      if (isset($_POST['kirim'])) {
      	if ($_POST['passw1'] != $_POST['passw2']) {
          echo '<div class="callout callout-danger">Password yang anda Masukan tidak sama silahkan ulangi kembali.</div>
              <hr />
            <a href="'.base_url().'profile" class="btn btn-primary">kembali</a>
           	';
       	}else{
            echo "FITUR SAAT INI DI NON AKTIFKAN";
           exit();
           // $password = barasiah($_POST['passw2']); 
           // $pwd = "Password=PASSWORD('$password')";  
           // $cek= mysql_query("UPDATE dosen set $pwd where Login='". $_SESSION['mhswid']."'"); 
           // if ($cek) {  
           // 	echo '<div class="callout callout-danger">pasword berhasil di ubah ke'.$_POST['passw2'].'harap di inga password .</div>';
           // }else{
           //   	echo '<div class="callout callout-danger">perubahan password gagal silahkan periksa kembali .</div>';
           // }
         }
       }else{
       echo '
       ** ) pastikan password login yang di ubah di ingat sebelumnya dan silahkan cocokan kembali password yang di ubah pada perulangan password yang di bawah.
       <form action="" method="POST">
       <table class="table table-striped">
             <tr><td>Password Pertama</td><td><input type="password" name="passw1" class="form-control"></td></tr>
             <tr><td>Ulangi Password</td><td><input type="password" name="passw1"  class="form-control"></td></tr>
             <tr><td></td><td><button class="btn btn-primary" name="kirim">Ubah Password</button></td></tr>
         </table>
         </form>';
      
    }

 endif;