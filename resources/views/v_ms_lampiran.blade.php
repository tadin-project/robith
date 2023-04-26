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
              <input type="hidden" id="lampiran_id" name="lampiran_id">
              <div class="row form-group">
                <label class="col-md-3 control-label">Kode</label>
                <div class="col-md-2">
                  <input type="text" class="form-control" id="lampiran_kode" name="lampiran_kode">
                </div>
                <input type="hidden" id="old_lampiran_kode">
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Nama</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="lampiran_nama" name="lampiran_nama">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Jenis Lampiran</label>
                <div class="col-md-2">
                  <select name="lampiran_jenis" id="lampiran_jenis" class="form-control">
                    <option value="1">File</option>
                    <option value="2">Link</option>
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">File</label>
                <div class="col-md-5">
                  <input type="hidden" id="has_file" name="has_file">
                  <input type="file" id="lampiran_file" name="lampiran_file">
                </div>
              </div>
              <div class="row form-group" style="display: none">
                <label class="col-md-3 control-label">Link</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="lampiran_link" name="lampiran_link">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Status</label>
                <div class="col-md-2">
                  <select name="lampiran_status" id="lampiran_status" class="form-control">
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
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor" style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Kode</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Jenis Lampiran</th>
                  <th class="text-center">Lampiran</th>
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
  const baseDir = baseUrl + '/ms-lampiran',
    rowForm = $("#rowForm"),
    rowData = $("#rowData");

  // form
  const formVendor = $("#formVendor"),
    act = $("#act"),
    lampiranId = $("#lampiran_id"),
    lampiranNama = $("#lampiran_nama"),
    lampiranKode = $("#lampiran_kode"),
    oldLampiranKode = $("#old_lampiran_kode"),
    lampiranJenis = $("#lampiran_jenis"),
    hasFile = $("#has_file"),
    lampiranFile = $("#lampiran_file"),
    lampiranLink = $("#lampiran_link"),
    lampiranStatus = $("#lampiran_status"),
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
          targets: [0, 3, -2, -1],
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
          lampiran_nama: {
            required: true,
          },
          lampiran_kode: {
            required: true,
            remote: {
              url: baseDir + '/check-duplicate',
              cache: false,
              data: {
                act: function() {
                  return act.val();
                },
                key: "lampiran_kode",
                val: function() {
                  return lampiranKode.val();
                },
                old: function() {
                  return act.val() == 'edit' ? oldLampiranKode.val() : "";
                }
              }
            },
          },
        },
        messages: {
          lampiran_kode: {
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
          if (lampiranJenis.val() == 1) {
            if (lampiranFile[0].files.length <= 0 && hasFile.val() == 0) {
              Swal.fire("Error", "File tidak boleh kosong!", "error");
              return;
            }
          } else {
            if (lampiranLink.val().trim() == "") {
              Swal.fire("Error", "Link tidak boleh kosong!", "error");
              return;
            }
          }

          let formData = new FormData();
          formData.append("act", act.val());
          formData.append("lampiran_id", lampiranId.val());
          formData.append("lampiran_kode", lampiranKode.val());
          formData.append("lampiran_nama", lampiranNama.val());
          formData.append("lampiran_status", lampiranStatus.val());
          formData.append("lampiran_jenis", lampiranJenis.val());
          formData.append("has_file", hasFile.val());

          if (lampiranJenis.val() == 1) {
            formData.append("lampiran_field", lampiranFile[0].files[0])
          } else {
            formData.append("lampiran_field", lampiranLink.val())
          }


          $.ajax({
            type: 'POST',
            url: baseDir,
            contentType: false,
            processData: false,
            cache: false,
            data: formData,
            beforeSend: function() {
              btnSimpan.attr('disabled', 'disabled').text('Loading...');
            },
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
    lampiranId.val('');
    oldLampiranKode.val('');
    lampiranLink.closest(".form-group").hide();
    hasFile.val(0);
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
          lampiranId.val(id);
          lampiranKode.val(dt.lampiran_kode);
          oldLampiranKode.val(dt.lampiran_kode);
          lampiranNama.val(dt.lampiran_nama);
          lampiranStatus.val(dt.lampiran_status);
          lampiranJenis.val(dt.lampiran_jenis);
          if (lampiranJenis.val() == 1) {
            lampiranFile.closest(".form-group").show();
            lampiranLink.closest(".form-group").hide();
            console.log(dt.lampiran_field);
            if (dt.lampiran_field != null && dt.lampiran_field.trim() != '') {
              hasFile.val(1);
            } else {
              hasFile.val(0);
            }
          } else {
            lampiranFile.closest(".form-group").hide();
            lampiranLink.closest(".form-group").show();
            lampiranLink.val(dt.lampiran_field);
          }

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

    lampiranJenis.change(function() {
      if ($(this).val() == 1) {
        lampiranFile.closest(".form-group").show();
        lampiranLink.closest(".form-group").hide();
      } else {
        lampiranFile.closest(".form-group").hide();
        lampiranLink.closest(".form-group").show();
      }
    })

  });
</script>
