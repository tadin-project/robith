<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0">{{ $__title }}</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content pb-2">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="ion ion-clipboard mr-1"></i> {{ $__title }}
        </h3>
        <div class="card-tools">
          <button class="btn btn-sm btn-info" title="Cetak Detail" id="btnCetak"><i class="fas fa-print"></i>
            Print</button>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12">
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor" style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Kriteria</th>
                  <th class="text-center">Rata-rata nilai</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($kriteria as $k => $v)
                  <tr>
                    <td class="text-center">{{ $k }}</td>
                    <td>{{ $v['mk_nama'] }}</td>
                    <td class="text-right">{{ $v['rata2'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
  $("#btnCetak").click(function() {
    window.open("{{ route('laporan-penilaian-user.cetak') }}", "_blank");
  });
</script>
