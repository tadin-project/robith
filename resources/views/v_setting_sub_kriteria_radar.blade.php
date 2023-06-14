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
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Dimensi</label>
              <select id="md_id" class="form-control">
                @foreach ($dimensi as $item)
                  <option value="{{ $item->md_id }}">{{ $item->md_kode }} - {{ $item->md_nama }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Kriteria</label>
              <select id="mk_id" class="form-control"></select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Sub Kriteria</label>
              <select id="msk_id" class="form-control"></select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5">
            <table class="table table-sm table-striped table-bordered table-hover" id="tableKiri" style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center"><input type="checkbox" id="allKiri"></th>
                  <th class="text-center">Kode</th>
                  <th class="text-center">Nama</th>
                </tr>
              </thead>
            </table>
          </div>
          <div class="col-md-2 d-flex flex-column align-items-center justify-content-center">
            <div class="row m-1">
              <div class="col-md-12">
                <button class="btn btn-primary" id="btnKanan"><i class="fa fa-arrow-right"></i></button>
              </div>
            </div>
            <div class="row m-1">
              <div class="col-md-12">
                <button class="btn btn-primary" id="btnKiri"><i class="fa fa-arrow-left"></i></button>
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <table class="table table-sm table-striped table-bordered table-hover" id="tableKanan" style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center"><input type="checkbox" id="allKanan"></th>
                  <th class="text-center">Kode</th>
                  <th class="text-center">Nama</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
  // init component
  // global
  const baseDir = baseUrl + '/setting-sub-kriteria-radar';

  // datatable
  const tableKiri = $("#tableKiri"),
    allKiri = $("#allKiri"),
    btnKiri = $("#btnKiri"),
    tableKanan = $("#tableKanan"),
    allKanan = $("#allKanan"),
    btnKanan = $("#btnKanan");

  // Class definition
  var PageAdvanced = function() {
    // Private functions
    var initDatatable = function() {
      tableKiri.DataTable({
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        order: [
          [2, 'asc']
        ],
        ajax: {
          url: baseDir + "/get-data/before",
          data: function(d) {
            d.msk_id = $("#msk_id").val();
          }
        },
        columnDefs: [{
          targets: [0, 1],
          orderable: false,
          className: "text-center",
        }, {
          targets: [0],
          width: "5%"
        }, {
          targets: [1],
          width: "10%"
        }, ],
        language: {
          lengthMenu: "Show _MENU_",
        },
        drawCallback: function(setting) {
          allKiri.prop("checked", false);
        }
      });

      tableKanan.DataTable({
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        order: [
          [2, 'asc']
        ],
        ajax: {
          url: baseDir + "/get-data/after",
          data: function(d) {
            d.msk_id = $("#msk_id").val();
          }
        },
        columnDefs: [{
          targets: [0, 1],
          orderable: false,
          className: "text-center",
        }, {
          targets: [0],
          width: "5%"
        }, {
          targets: [1],
          width: "10%"
        }, ],
        language: {
          lengthMenu: "Show _MENU_",
        },
        drawCallback: function(setting) {
          allKanan.prop("checked", false);
        }
      });
    }

    // Public methods
    return {
      init: function() {
        initDatatable();
      }
    }
  }();

  function fnResetForm() {}

  function fnLoadTbl() {
    tableKiri.DataTable().draw();
    tableKanan.DataTable().draw();
  }

  function fnSetData(data = [], ) {
    $.ajax({
      url: baseDir,
      dataType: "json",
      type: "post",
      data: data,
      error: function(xhr) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: xhr.statusText,
        });
      },
      success: function(res) {
        if (!res.status) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: res.message,
          });
          return;
        }

        Swal.fire({
          icon: 'success',
          title: 'Data berhasil dirubah!',
          showConfirmButton: false,
          timer: 1500
        });
        fnLoadTbl();
      },
    });
  }

  function fnGetOpt(jenisParent = "kriteria", parent = "", selectedId = "") {
    let url = baseDir + "/get-parent";

    $.ajax({
      url: url,
      dataType: "json",
      data: {
        jenis: jenisParent,
        parent: parent,
      },
      error: function(xhr) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: xhr.statusText,
        });
      },
      success: function(res) {
        if (!res.status) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: res.message,
          });
          return;
        }

        let opt = "";
        $.each(res.data, function(i, v) {
          opt +=
            `<option value="${v.id}" ${selectedId == v.id ? "selected" : ""}>${v.kode} - ${v.nama}</option>`;
        });

        if (jenisParent == "kriteria") {
          $("#mk_id").html(opt);
          fnGetOpt("sub-kriteria", $("#mk_id").val(), selectedId);
        } else if (jenisParent == "sub-kriteria") {
          $("#msk_id").html(opt);
          fnLoadTbl();
        }
      },
    });
  }

  $(document).ready(function() {
    PageAdvanced.init();
    fnGetOpt("kriteria", $("#md_id").val());

    btnKanan.click(async function() {
      let data = [];
      let rows = tableKiri.find('tbody input:checkbox:checked');

      $.each(rows, function(i, v) {
        data.push(v.value);
      });

      if (data.length == 0) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Pilih data yang akan dipindahkan",
        });
        return;
      }

      fnSetData({
        mr_id: data,
        msk_id: $("#msk_id").val(),
        jenis: "kanan",
      });
    });

    btnKiri.click(async function() {
      let data = [];
      let rows = tableKanan.find('tbody input:checkbox:checked');

      $.each(rows, function(i, v) {
        data.push(v.value);
      });

      if (data.length == 0) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Pilih data yang akan dipindahkan",
        });
        return;
      }

      fnSetData({
        mr_id: data,
        msk_id: $("#msk_id").val(),
        jenis: "kiri",
      });
    });

    allKanan.click(function() {
      const isChecked = $(this).prop("checked");

      if (isChecked) {
        tableKanan.find('tbody input:checkbox').prop("checked", true);
      } else {
        tableKanan.find('tbody input:checkbox').prop("checked", false);
      }
    });

    allKiri.click(function() {
      const isChecked = $(this).prop("checked");

      if (isChecked) {
        tableKiri.find('tbody input:checkbox').prop("checked", true);
      } else {
        tableKiri.find('tbody input:checkbox').prop("checked", false);
      }
    });

    $("#md_id").change(function() {
      fnGetOpt("kriteria", $(this).val());
    });

    $("#mk_id").change(function() {
      fnGetOpt("sub-kriteria", $(this).val());
    });

    $("#msk_id").change(function() {
      fnLoadTbl();
    });
  });
</script>
