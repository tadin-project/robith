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
        </div>
      </div>
      <div class="card-body">
        <div class="row" id="rowForm" style="display:none;">
          <div class="col-12">
            <div class="row mb-3 text-right">
              <div class="col-md-12">
                <button class="btn btn-primary" id="btnFinal">Finalisasi</button>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label class="control-label col-md-3">Nama Tenant</label>
                  <div class="col-md-5">
                    <input class="form-control" type="text" readonly="" value="Tadin" id="tenant_nama"
                      name="tenant_nama">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="control-label col-md-3">Deskripsi Tenant</label>
                  <div class="col-md-5">
                    <textarea class="form-control" readonly="" id="tenant_desc" name="tenant_desc"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="card card-primary card-outline card-outline-tabs">
              <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                  @php
                    $aktifKriteria = false;
                  @endphp
                  @foreach ($dtKriteria as $k => $v)
                    @php
                      $aktifKriteria = $k == 0 ? 'active' : '';
                    @endphp
                    <li class="nav-item">
                      <a class="nav-link {{ $aktifKriteria }}" id="kriteria-{{ $v['mk_id'] }}-tab" data-toggle="pill"
                        href="#kriteria-{{ $v['mk_id'] }}" role="tab" aria-controls="kriteria-{{ $v['mk_id'] }}"
                        aria-selected="true">{{ $v['mk_nama'] }}</a>
                    </li>
                  @endforeach
                </ul>
              </div>
              <div class="card-body">
                <form id="formVendor">
                  <input type="hidden" name="as_id" id="as_id">
                  <div class="tab-content" id="custom-tabs-four-tabContent">
                    @foreach ($dtKriteria as $k => $v)
                      @php
                        $aktifKriteria = $k == 0 ? 'show active' : '';
                      @endphp
                      <div class="tab-pane fade {{ $aktifKriteria }}" id="kriteria-{{ $v['mk_id'] }}" role="tabpanel"
                        aria-labelledby="kriteria-{{ $v['mk_id'] }}-tab">
                        <table class="table table-sm table-striped table-hover table-bordered">
                          <thead>
                            <tr>
                              <th class="text-center">No</th>
                              <th class="text-center">Sub Kriteria</th>
                              <th class="text-center">Nilai</th>
                              <th class="text-center">Aksi</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($v['msk'] as $k1 => $v1)
                              <tr>
                                <th class="text-center">{{ $k1 + 1 }}</th>
                                <th colspan="3">{{ $v1['msk_nama'] }} @if ($v1['msk_is_submission'])
                                    &nbsp;(&nbsp;<span class="buktiMsk"
                                      id="buktiMsk{{ $v1['msk_id'] }}">-</span>&nbsp;)
                                  @endif
                                </th>
                              </tr>
                              @foreach ($v1['radar'] as $k2 => $v2)
                                <tr>
                                  <td class="text-center">{{ $k2 + 1 }}</td>
                                  <td>{{ $v2['mr_nama'] }}</td>
                                  <td class="text-center nilaiMr" id="nilaiMr{{ $v2['sskr_id'] }}">
                                    @if ($v1['msk_is_submission'])
                                      <select class="form-control optNilai" id="optNilai{{ $v2['sskr_id'] }}">
                                        @foreach ($dtConvertionValue as $a)
                                          <option value="{{ $a->cval_nilai }}">{{ $a->cval_nama }}</option>
                                        @endforeach
                                      </select>
                                    @else
                                      0
                                    @endif
                                  </td>
                                  <td class="text-center aksi" id="aksi{{ $v2['sskr_id'] }}"></td>
                                </tr>
                              @endforeach
                            @endforeach;
                          </tbody>
                        </table>
                      </div>
                    @endforeach
                  </div>
                </form>
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>
        <div class="row" id="rowData">
          <div class="col-12">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Status</label>
                  <select name="fil_as_status" id="fil_as_status" class="form-control">
                    <option value="1">Belum Divalidasi</option>
                    <option value="2">Sudah Divalidasi</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Kategori Usaha</label>
                  <select name="fil_mku_id" id="fil_mku_id" class="form-control">
                    @foreach ($opt_ku as $v)
                      <option value="{{ $v->mku_id }}">{{ $v->mku_nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor"
              style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Dibuat</th>
                  <th class="text-center">Tenant</th>
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
  const baseDir = baseUrl + '/validasi-asesmen',
    rowForm = $("#rowForm"),
    rowData = $("#rowData");

  // form
  const formVendor = $("#formVendor"),
    asId = $("#as_id"),
    tenantNama = $("#tenant_nama"),
    tenantDesc = $("#tenant_desc"),
    btnBatal = $("#btnBatal"),
    btnFinal = $("#btnFinal");

  // datatable
  const tableVendor = $("#tableVendor"),
    filMkuId = $("#fil_mku_id"),
    filAsStatus = $("#fil_as_status");

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
          data: function(d) {
            d.fil_mku_id = filMkuId.val();
            d.fil_as_status = filAsStatus.val();
          },
        },
        columnDefs: [{
          targets: [0, -1],
          orderable: false,
        }, {
          targets: [0, 1, -1],
          className: "text-center"
        }, {
          targets: [0],
          width: "5%"
        }, {
          targets: [1],
          width: "18%"
        }, {
          targets: [-1],
          width: "12%"
        }, ],
        language: {
          lengthMenu: "Show _MENU_",
        },
      });

      table = dt.$;
    }

    // var initForm = function() {
    //   formVendor.validate({
    //     errorClass: 'help-block',
    //     errorElement: 'span',
    //     ignore: 'input[type=hidden]',
    //     rules: {
    //       md_nama: {
    //         required: true,
    //       },
    //       md_kode: {
    //         required: true,
    //         remote: {
    //           url: baseDir + '/check-duplicate',
    //           cache: false,
    //           data: {
    //             act: function() {
    //               return act.val();
    //             },
    //             key: "md_kode",
    //             val: function() {
    //               return mdKode.val();
    //             },
    //             old: function() {
    //               return act.val() == 'edit' ? oldMdKode.val() : "";
    //             }
    //           }
    //         },
    //       },
    //     },
    //     messages: {
    //       md_kode: {
    //         remote: "Kode sudah digunakan. Gunakan yang lain",
    //       },
    //     },
    //     highlight: function(el, errorClass) {
    //       $(el).parents('.form-group').first().addClass('has-error');
    //     },
    //     unhighlight: function(el, errorClass) {
    //       var $parent = $(el).parents('.form-group').first();
    //       $parent.removeClass('has-error');
    //       $parent.find('.help-block').hide();
    //     },
    //     errorPlacement: function(error, el) {
    //       error.appendTo(el.parents('.form-group').find('div:first'));
    //     },
    //     submitHandler: function(form) {
    //       btnSimpan.attr('disabled', 'disabled').text('Loading...')
    //       var $data = $(form).serialize();
    //       $.ajax({
    //         type: 'POST',
    //         url: baseDir,
    //         data: $data,
    //         error: function() {
    //           btnSimpan.removeAttr('disabled', 'disabled').text('Simpan');
    //         },
    //         complete: function() {
    //           btnSimpan.removeAttr('disabled', 'disabled').text('Simpan');
    //         },
    //         success: function(res) {
    //           if (res.status) {
    //             Swal.fire({
    //               icon: 'success',
    //               title: 'Data berhasil disimpan!',
    //               showConfirmButton: false,
    //               timer: 1500
    //             });

    //             btnBatal.click();
    //           } else {
    //             if (typeof res.msg == 'object') {
    //               res.msg = JSON.stringify(res.msg);
    //             }
    //             Swal.fire('Error', res.msg, 'error');
    //           }
    //         }
    //       });
    //       return false;
    //     }
    //   });
    // }

    // Public methods
    return {
      init: function() {
        initDatatable();
        // initForm();
      }
    }
  }();

  function fnLoadTbl() {
    tableVendor.DataTable().draw()
  }

  async function fnCek(id) {
    let res = await $.ajax({
      url: baseDir + "/edit/" + id,
      dataType: 'json',
      cache: false,
      success: function(res) {
        if (res.status) {
          const dt = res.data;
          asId.val(dt.as_id);
          tenantNama.val(dt.tenant_nama);
          tenantDesc.val(dt.tenant_desc);
          if (dt.as_status == 2) {
            btnFinal.hide();
          } else {
            btnFinal.show();
          }

          $.each(dt.detail, function(index, i) {
            if (i.msk_is_submission == true) {
              $("#optNilai" + i.sskr_id).val(i.asd_value);
            } else {
              $("#nilaiMr" + i.sskr_id).text(i.cval_nama);
            }

            let aksi = "";
            if (i.msk_is_submission == true) {
              if (dt.as_status == 1) {
                if (i.asd_status == 0) {
                  aksi = `<button type="button" class="btn btn-success btn-sm" onclick="fnValidasi(${i.asd_id},1,this)" title="Setujui">
                            <i class="fas fa-check"></i>
                          </button>
                          <button type="button" class="btn btn-danger btn-sm" onclick="fnValidasi(${i.asd_id},2, this)" title="Tolak">
                            <i class="fas fa-times"></i>
                          </button>`;
                  $("#optNilai" + i.sskr_id).removeAttr("disabled", "disabled");
                } else {
                  aksi = `<button type="button" class="btn btn-info btn-sm" onclick="fnInfo(${i.asd_status},'${i.user_fullname}')" title="Info">
                            <i class="fas fa-info"></i>
                          </button>
                          <button type="button" class="btn btn-danger btn-sm" onclick="fnValidasi(${i.asd_id},0, this)" title="Batal Validasi">
                            <i class="fas fa-times"></i>
                          </button>`;
                  $("#optNilai" + i.sskr_id).attr("disabled", "disabled");
                }
              } else {
                aksi = `<button type="button" class="btn btn-info btn-sm" onclick="fnInfo(${i.asd_status},'${i.user_fullname}')" title="Info">
                          <i class="fas fa-info"></i>
                        </button>`;
                $("#optNilai" + i.sskr_id).attr("disabled", "disabled");
              }
            }

            $("#aksi" + i.sskr_id).html(aksi);
          });

          $.each(dt.file, function(index, i) {
            $("#buktiMsk" + i.msk_id).html(i.asf_file ?
              `<a href="{{ url($dirUploads) }}/${i.asf_file}" target="_blank">${i.asf_file}</a>` :
              "Bukti tidak diupload");
          });
        } else {
          Swal.fire("Error", res.msg, "error");
        }
      }
    });

    return res;
  }

  async function fnEdit(id) {
    let res = await fnCek(id);
    if (res.status) {
      rowForm.slideDown(500);
      rowData.slideUp(500);
      btnBatal.show();
    }
  }

  function fnResetForm() {
    formVendor[0].reset();
    $(".aksi").text("");
    $(".buktiMsk").text("-");
    $('.nilaiMr').find('select').prop('selectedIndex', 0);
    $('.nilaiMr').not($('.nilaiMr').find('select').parent()).text("");
  }

  function fnValidasi(id, val, e) {
    $.ajax({
      url: baseDir + "/validasi",
      cache: false,
      data: {
        id: id,
        val: val,
        new_val: val == 1 ? $(e).closest('tr').find('.optNilai').val() : null,
      },
      dataType: 'json',
      type: "post",
      success: function(res) {
        if (res.status) {
          Swal.fire({
            icon: 'success',
            title: 'Data berhasil diperbarui!',
            showConfirmButton: false,
            timer: 1500,
          });
          fnCek(asId.val());
        } else {
          Swal.fire("Error", res.msg, "error");
        }
      }
    });
  }

  function fnInfo(status, user) {
    let msg = "Data sudah ";
    if (status == 1) {
      msg += "<span class='text-success'><strong>DISETUJUI</strong></span> ";
    } else if (status == 2) {
      msg += "<span class='text-danger'><strong>DITOLAK</strong></span> ";
    }

    msg += "oleh " + user;

    Swal.fire({
      icon: 'info',
      title: 'Info',
      html: msg,
    });
  }

  function fnFinal() {
    Swal.fire({
      title: `Apakah Anda yakin?`,
      text: "Data yang sudah difinalisasi tidak dapat dikembalikan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Iya',
      cancelButtonText: 'Tidak',
    }).then((result) => {
      if (result.isConfirmed) {
        btnFinal.attr("disabled", "disabled").text("Loading...");
        $.ajax({
          url: baseDir + '/final/' + asId.val(),
          method: 'post',
          dataType: 'json',
          cache: false,
          error: function(err) {
            Swal.fire('Error', err, 'error');
            btnFinal.removeAttr("disabled", "disabled").text("Finalisasi");
          },
          complete: function() {
            btnFinal.removeAttr("disabled", "disabled").text("Finalisasi");
          },
          success: function(res) {
            if (res.status) {
              Swal.fire({
                icon: 'success',
                title: 'Data berhasil difinalisasi!',
                showConfirmButton: false,
                timer: 1500
              });
              btnBatal.click();
            } else {
              Swal.fire('Error', res.msg, 'error');
            }
          },
        });
      }
    });
  }

  $(document).ready(function() {
    PageAdvanced.init();

    filMkuId.change(function() {
      fnLoadTbl();
    });

    filAsStatus.change(function() {
      fnLoadTbl();
    });

    btnBatal.click(function() {
      fnResetForm();
      fnLoadTbl();
      rowForm.slideUp(500);
      rowData.slideDown(500);
      btnBatal.hide();
    });

    btnFinal.click(function() {
      fnFinal();
    });
  })
</script>
