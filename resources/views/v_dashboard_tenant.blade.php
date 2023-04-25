<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h5>Selamat Datang</h5>
        <h1 class="m-0">{{ !empty($user['user_fullname']) ? $user['user_fullname'] : $user['user_email'] }}</h1>
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
          <i class="ion ion-clipboard mr-1"></i> Ringkasan Penilaian
        </h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div style="max-height: 260px;"><canvas id="container"></canvas></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  const baseDir = baseUrl + "/dashboard";

  const config = {
    type: 'bar',
    data: {
      datasets: [{
        data: JSON.parse(
          '{!! count($data['data']) > 0 ? addslashes(json_encode($data['data'])) : '[]' !!}'),
        backgroundColor: JSON.parse(
          '{!! count($data['backgroundColor']) > 0 ? addslashes(json_encode($data['backgroundColor'])) : '[]' !!}'
        ),
      }]
    },
    options: {
      indexAxis: 'y',
      parsing: {
        yAxisKey: 'nama',
        xAxisKey: 'nested.value'
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  };

  let myChart = new Chart(
    document.getElementById('container'), config
  );

  let intervalGetData;

  function fnGetData() {
    $.ajax({
      url: baseDir + "/get-data-tenant",
      dataType: 'json',
      cache: false,
      error: function(err) {
        Swal.fire("Error", err, "error");
        clearInterval(intervalGetData);
      },
      success: function(res) {
        if (res.status) {
          const dt = res.data;
          $.each(myChart.data.datasets[0].data, function(index, i) {
            i.nested.value = dt[i.id];
          });
          myChart.update();
        } else {
          Swal.fire("Error", res.msg, "error");
          clearInterval(intervalGetData);
        }
      }
    })
  }

  $(document).ready(function() {
    fnGetData();
    intervalGetData = setInterval(() => {
      fnGetData();
    }, 7500);
  });
</script>
