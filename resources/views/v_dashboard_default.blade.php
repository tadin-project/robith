<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h5>Selamat Datang</h5>
        <h1 class="m-0">{{ !empty($user['user_fullname']) ? $user['user_fullname'] : $user['user_name'] }}</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
