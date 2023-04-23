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
        <div class="row" id="rowForm" style="display:none;">
          <div class="col-12">
            <form id="formVendor">
              <input type="hidden" id="act" name="act" value="add">
              <input type="hidden" id="menu_id" name="menu_id">
              <div class="row form-group">
                <label class="col-md-3 control-label">Parent</label>
                <div class="col-md-3">
                  <select name="parent_menu_id" id="parent_menu_id" class="form-control">
                    <option value="0">-- No Parent --</option>
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Kode</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="menu_kode" name="menu_kode">
                </div>
                <input type="hidden" id="old_menu_kode">
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Nama</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="menu_nama" name="menu_nama">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Tipe</label>
                <div class="col-md-2">
                  <select class="form-control" id="menu_type" name="menu_type">
                    <option value="1">Link</option>
                    <option value="2">Text</option>
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Link</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="menu_link" name="menu_link">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Ikon</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="menu_ikon" name="menu_ikon">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Status</label>
                <div class="col-md-2">
                  <select name="menu_status" id="menu_status" class="form-control">
                    <option value="1">Aktif</option>
                    <option value="0">Non Aktif</option>
                  </select>
                </div>
              </div>
              <div class="row text-center">
                <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-secondary" onclick="btnBatal.click();">Batal</button>
                  <button type="button" class="btn btn-sm btn-primary" id="btnSimpan">Simpan</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="row" id="rowData">
          <div class="col-12">
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor"
              style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Kode</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Tipe</th>
                  <th class="text-center">Link</th>
                  <th class="text-center">Ikon</th>
                  <th class="text-center">Parent</th>
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
  const baseDir = baseUrl + '/ms-menus',
    rowForm = $("#rowForm"),
    rowData = $("#rowData");

  // form
  const formVendor = $("#formVendor"),
    act = $("#act"),
    menuId = $("#menu_id"),
    menuKode = $("#menu_kode"),
    oldMenuKode = $("#old_menu_kode"),
    menuNama = $("#menu_nama"),
    menuType = $("#menu_type"),
    menuLink = $("#menu_link"),
    menuIkon = $("#menu_ikon"),
    menuStatus = $("#menu_status"),
    parentMenuId = $("#parent_menu_id"),
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
          menu_nama: {
            required: true,
          },
          menu_kode: {
            required: true,
            remote: {
              url: baseDir + '/check-duplicate',
              cache: false,
              data: {
                act: function() {
                  return act.val();
                },
                key: "menu_kode",
                val: function() {
                  return menuKode.val();
                },
                old: function() {
                  return act.val() == 'edit' ? oldMenuKode.val() : "";
                }
              }
            },
          },
        },
        messages: {
          menu_kode: {
            remote: "Kode menu sudah digunakan. Gunakan yang lain",
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
      fnGetParent();
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
    menuId.val('');
    oldMenuKode.val('');
    parentMenuId.select2("val", "0");
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
          menuId.val(dt.menu_id);
          menuKode.val(dt.menu_kode);
          oldMenuKode.val(dt.menu_kode);
          menuNama.val(dt.menu_nama);
          menuType.val(dt.menu_type);
          menuLink.val(dt.menu_link);
          menuIkon.val(dt.menu_ikon);
          menuStatus.val(dt.menu_status);
          parentMenuId.select2("val", dt.parent_menu_id.toString());

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

  function fnGetParent() {
    parentMenuId.find("option[value!=0]").remove();
    parentMenuId.select2("val", "0");
    $.ajax({
      url: baseDir + "/get-parent",
      cache: false,
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          let opt = "";
          if (res.data.length > 0) {
            $.each(res.data, function(index, i) {
              opt += `<option value="${i.menu_id}">${i.menu_kode} - ${i.menu_nama}</option>`;
            });
            parentMenuId.append(opt);
          } else {
            parentMenuId.append(opt);
          }
        } else {
          alert(res.msg);
        }
      }
    });
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

    parentMenuId.select2({
      theme: 'bootstrap4'
    });

  });
</script>
