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
          <button class="btn btn-sm btn-danger" title="Batal Tambah Data" id="btnBatal" style="display: none"><i
              class="fas fa-times"></i></button>
          <button class="btn btn-sm btn-primary" title="Tambah Data" id="btnTambah"><i class="fas fa-plus"></i>
            Data</button>
        </div>
      </div>
      <div class="card-body">
        <div class="row" id="rowData">
          <div class="col-12">
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor" style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Kode</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Aksi</th>
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
  const baseDir = baseUrl + '/ms-dimensi',
    rowForm = $("#rowForm"),
    rowData = $("#rowData");

  // form
  const formVendor = $("#formVendor"),
    act = $("#act"),
    mdId = $("#md_id"),
    mdNama = $("#md_nama"),
    mdKode = $("#md_kode"),
    oldMdKode = $("#old_md_kode"),
    mdStatus = $("#md_status"),
    btnBatal = $("#btnBatal"),
    btnSimpan = $("#btnSimpan");

  // datatable
  const tableVendor = $("#tableVendor"),
    btnTambah = $("#btnTambah");

  // Class definition
  var PageAdvanced = function() {
    // Shared variables
    var table;
    var dt;

    // Private functions
    var initDatatable = function() {
      dt = tableVendor.DataTable({
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        order: [
          [1, 'asc']
        ],
        ajax: {
          url: baseDir + "/get-data",
        },
        columnDefs: [{
          targets: [0, -1],
          orderable: false,
        }, {
          targets: [0, -2, -1],
          className: "text-center"
        }, {
          targets: [0],
          width: "5%"
        }, {
          targets: [-1],
          width: "12%"
        }, {
          targets: [-2],
          width: "8%"
        }, ],
        language: {
          lengthMenu: "Show _MENU_",
        },
      });

      table = dt.$;
    }

    var initForm = function() {
      formVendor.validate({
        errorClass: 'help-block',
        errorElement: 'span',
        ignore: 'input[type=hidden]',
        rules: {
          md_nama: {
            required: true,
          },
          md_kode: {
            required: true,
            remote: {
              url: baseDir + '/check-duplicate',
              cache: false,
              data: {
                act: function() {
                  return act.val();
                },
                key: "md_kode",
                val: function() {
                  return mdKode.val();
                },
                old: function() {
                  return act.val() == 'edit' ? oldMdKode.val() : "";
                }
              }
            },
          },
        },
        messages: {
          md_kode: {
            remote: "Kode sudah digunakan. Gunakan yang lain",
          },
        },
        highlight: function(el, errorClass) {
          $(el).parents('.form-group').first().addClass('has-error');
        },
        unhighlight: function(el, errorClass) {
          var $parent = $(el).parents('.form-group').first();
          $parent.removeClass('has-error');
          $parent.find('.help-block').hide();
        },
        errorPlacement: function(error, el) {
          error.appendTo(el.parents('.form-group').find('div:first'));
        },
        submitHandler: function(form) {
          btnSimpan.attr('disabled', 'disabled').text('Loading...')
          var $data = $(form).serialize();
          $.ajax({
            type: 'POST',
            url: baseDir,
            data: $data,
            error: function() {
              btnSimpan.removeAttr('disabled', 'disabled').text('Simpan');
            },
            complete: function() {
              btnSimpan.removeAttr('disabled', 'disabled').text('Simpan');
            },
            success: function(res) {
              if (res.status) {
                Swal.fire({
                  icon: 'success',
                  title: 'Data berhasil disimpan!',
                  showConfirmButton: false,
                  timer: 1500
                });

                btnBatal.click();
              } else {
                if (typeof res.msg == 'object') {
                  res.msg = JSON.stringify(res.msg);
                }
                Swal.fire('Error', res.msg, 'error');
              }
            }
          });
          return false;
        }
      });
    }

    // Public methods
    return {
      init: function() {
        initDatatable();
        initForm();
      }
    }
  }();

  function fnShowForm(isShow = true) {
    fnResetForm();
    if (isShow) {
      rowForm.slideDown(500);
      rowData.slideUp(500);
      btnBatal.show();
      btnTambah.hide();
    } else {
      rowForm.slideUp(500);
      rowData.slideDown(500);
      btnBatal.hide();
      btnTambah.show();
    }
  }

  function fnResetForm() {
    formVendor[0].reset();
    formVendor.validate().resetForm();
    $('.has-error').removeClass('has-error');
    act.val('add');
    mdId.val('');
    oldMdKode.val('');
  }

  function fnLoadTbl() {
    tableVendor.DataTable().draw()
  }

  function fnEdit(id) {
    $.ajax({
      url: baseDir + '/' + id,
      dataType: 'json',
      cache: false,
      success: function(res) {
        if (res.status) {
          var dt = res.data;

          act.val('edit');
          mdId.val(id);
          mdKode.val(dt.md_kode);
          oldMdKode.val(dt.md_kode);
          mdNama.val(dt.md_nama);
          mdStatus.val(dt.md_status);

          rowForm.slideDown(500);
          rowData.slideUp(500);
          btnBatal.show();
          btnTambah.hide();
        } else {
          Swal.fire('Error', res.msg, 'error');
        }
      },
    });
  }

  function fnDel(id, nama) {
    Swal.fire({
      title: `Apakah Anda yakin menghapus ${nama}?`,
      text: "Data yang dihapus tidak dapat dikembalikan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Iya',
      cancelButtonText: 'Tidak',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: baseDir + '/' + id,
          method: 'delete',
          dataType: 'json',
          cache: false,
          success: function(res) {
            if (res.status) {
              Swal.fire({
                icon: 'success',
                title: 'Data berhasil dihapus!',
                showConfirmButton: false,
                timer: 1500
              });
              fnLoadTbl();
            } else {
              Swal.fire('Error', res.msg, 'error');
            }
          },
        });
      }
    })
  }

  $(document).ready(function() {
    PageAdvanced.init();

    btnTambah.click(function() {
      fnShowForm();
    });

    btnBatal.click(function() {
      fnShowForm(false);
      fnLoadTbl();
    });

    btnSimpan.click(function() {
      formVendor.submit();
    });

  });
</script>
