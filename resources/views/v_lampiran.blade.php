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
      </div>
      <div class="card-body">
        <div class="row" id="rowData">
          <div class="col-12">
            <ol>
              @foreach ($data as $v)
                <li>
                  @if ($v->lampiran_jenis == 1)
                    <a target="_blank"
                      href="{{ url($dir_uploads) }}/{{ $v->lampiran_field }}">{{ $v->lampiran_nama }}</a>
                  @else
                    <a target="_blank" href="{!! $v->lampiran_field !!}">{{ $v->lampiran_nama }}</a>
                  @endif
                </li>
              @endforeach
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
