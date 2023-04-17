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
      <div class="overlay dark flex-column" id="spinnerForm" style="display: none;">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2 text-white">Loading...</div>
      </div>
      <div class="card-header">
        <h3 class="card-title">
          <i class="ion ion-clipboard mr-1"></i> {{ $__title }}
        </h3>
        <div class="card-tools">
          <button class="btn btn-sm btn-warning" title="Simpan Sementara" id="btnSimpanSementara">
            <i class="fas fa-save"></i> Simpan Sementara
          </button>
          <button class="btn btn-sm btn-primary" title="Simpan Final" id="btnSimpanFinal">
            <i class="fas fa-save"></i> Simpan Final
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="row" id="rowForm">
          <div class="col-12">
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
                  <div class="tab-content" id="custom-tabs-four-tabContent">
                    @foreach ($dtKriteria as $k => $v)
                      @php
                        $aktifKriteria = $k == 0 ? 'show active' : '';
                      @endphp
                      <div class="tab-pane fade {{ $aktifKriteria }}" id="kriteria-{{ $v['mk_id'] }}" role="tabpanel"
                        aria-labelledby="kriteria-{{ $v['mk_id'] }}-tab">
                        @foreach ($v['msk'] as $k1 => $v1)
                          <div class="form-group row">
                            <div class="col-md-12">
                              <div class="row">
                                <label class="control-label col-md-6"
                                  for="formControlRange{{ $v1['msk_id'] }}">{{ $v1['msk_nama'] }}</label>
                                <div class="col-md-6">
                                  <div class="row align-items-center">
                                    <div class="col">
                                      <input type="hidden" id="idDetail{{ $v1['msk_id'] }}"
                                        name="id_detail[{{ $k }}{{ $k1 }}]" value="" />
                                      <input type="hidden" id="idInput{{ $v1['msk_id'] }}"
                                        name="msk_id[{{ $k }}{{ $k1 }}]"
                                        value="{{ $v1['msk_id'] }}" />
                                      <input type="range" class="form-control-range"
                                        id="formControlRange{{ $v1['msk_id'] }}" oninput="fnChangeRange(this)"
                                        value="0" data-target="rangeval{{ $v1['msk_id'] }}"
                                        name="msk_value[{{ $k }}{{ $k1 }}]">
                                    </div>
                                    <div class="col-md-2 col-3"><span class="col-md-2"
                                        id="rangeval{{ $v1['msk_id'] }}">0</span></div>
                                  </div>
                                </div>
                              </div>
                              @if ($v1['msk_is_submission'] == 1)
                                <div class="row">
                                  <label class="control-label col-md-6 font-weight-normal">Upload file bukti<span
                                      id="fileName{{ $v1['msk_id'] }}"></span></label>
                                  <div class="col-md-6">
                                    <input type="file" name="lampiran[{{ $k }}{{ $k1 }}]">
                                  </div>
                                </div>
                              @endif
                            </div>
                          </div>
                        @endforeach
                      </div>
                    @endforeach
                  </div>
                </form>
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
  // form
  const spinnerForm = $("#spinnerForm"),
    formVendor = $("#formVendor"),
    btnSimpanSementara = $("#btnSimpanSementara"),
    btnSimpanFinal = $("#btnSimpanFinal");

  let listHapusLampiran = [];

  function fnChangeRange(e) {
    $("#" + $(e).data("target")).html($(e).val())
  }

  function fnCek() {
    spinnerForm.show()
    $.ajax({
      url: "{{ route('asesmen.cek-data') }}",
      dataType: 'json',
      cache: false,
      error: function(err) {
        spinnerForm.hide();
        Swal.fire("Error", err, "error")
      },
      complete: function(err) {
        spinnerForm.hide();
      },
      success: function(res) {
        if (res.status) {
          if (res.data == 0) {
            return;
          }

          const dt = res.data;
          if (dt.as_status > 0) {
            btnSimpanFinal.hide();
            btnSimpanSementara.hide();
            formVendor.find("input[type=file]").hide();
            formVendor.find("input[type=range]").attr("disabled", "disabled");
          } else {
            btnSimpanFinal.show();
            btnSimpanSementara.show();
            formVendor.find("input[type=file]").show();
            formVendor.find("input[type=range]").removeAttr("disabled", "disabled");
          }

          let btnHapus = "";
          $.each(dt.detail, function(index, i) {
            $("#formControlRange" + i.msk_id).val(i.asd_value)
            $("#idDetail" + i.msk_id).val(i.id_detail)
            $("#rangeval" + i.msk_id).text(i.asd_value)
            if (i.asd_file) {
              btnHapus = dt.as_status > 0 ? "" :
                ` <button class="btn btn-sm btn-danger" type="button" onclick="fnHapusLampiran(this,${i.id_detail})"><i class="fa fa-times"></i></button>`;
              $("#fileName" + i.msk_id).html(
                ` (<a target="_blank" href="{{ url($dirUploads) }}/${i.asd_file}">${i.asd_file}</a>)${btnHapus}`
              )
            }
          });
        } else {
          Swal.fire("Error", res.msg, "error")
        }
      },
    })
  }

  function fnResetForm() {
    formVendor[0].reset();
    listHapusLampiran = [];
  }

  function fnSimpanSementara() {
    fnSetBtnOnSubmit("tmp", "disabled");
    let formData = new FormData(formVendor[0]);
    formData.append("list_hapus_lampiran", listHapusLampiran);

    $.ajax({
      url: "{{ route('asesmen.save-tmp') }}",
      cache: false,
      data: formData,
      contentType: false,
      processData: false,
      type: "post",
      dataType: "json",
      error: function(err) {
        Swal.fire("Error", err, "error");
        fnSetBtnOnSubmit();
      },
      complete: function() {
        fnSetBtnOnSubmit();
      },
      success: function(res) {
        if (res.status) {
          Swal.fire({
            icon: 'success',
            title: 'Data berhasil disimpan!',
            showConfirmButton: false,
            timer: 1500
          });
          fnResetForm();
          fnCek();
        } else {
          Swal.fire("Error", res.msg, "error")
        }
      }
    })
  }

  function fnSetBtnOnSubmit(target, status) {
    if (status == "disabled") {
      btnSimpanSementara.attr("disabled", "disabled");
      btnSimpanFinal.attr("disabled", "disabled");
      if (target == "tmp") {
        btnSimpanSementara.text("Loading...");
      } else {
        btnSimpanFinal.text("Loading...");
      }
      spinnerForm.show();
    } else {
      spinnerForm.hide();
      btnSimpanSementara.removeAttr("disabled", "disabled").text("Simpan Sementara");
      btnSimpanFinal.removeAttr("disabled", "disabled").text("Simpan Final");
    }
  }

  function fnHapusLampiran(e, id) {
    Swal.fire({
      text: "Apakah Anda yakin ingin menghapus file bukti?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Iya',
      cancelButtonText: 'Tidak',
    }).then((result) => {
      if (result.isConfirmed) {
        listHapusLampiran.push(id);
        $(e).closest("span").text("");
      }
    })
  }

  function fnSimpanFinal() {
    Swal.fire({
      title: "Apakah Anda yakin ingin memfinalisasi ini?",
      text: "Data yang sudah difinalisasi, tidak dapat dirubah",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Iya',
      cancelButtonText: 'Tidak',
    }).then((result) => {
      if (result.isConfirmed) {
        fnSetBtnOnSubmit("final", "disabled");

        $.ajax({
          url: "{{ route('asesmen.save') }}",
          cache: false,
          type: "post",
          dataType: "json",
          error: function(err) {
            Swal.fire("Error", err, "error");
            fnSetBtnOnSubmit();
          },
          complete: function() {
            fnSetBtnOnSubmit();
          },
          success: function(res) {
            if (res.status) {
              Swal.fire({
                icon: 'success',
                title: 'Data difinalisasi. Silahkan menunggu hasil finalisasi!',
                showConfirmButton: false,
                timer: 1500
              });
              fnResetForm();
              fnCek();
            } else {
              Swal.fire("Error", res.msg, "error")
            }
          }
        })
      }
    })
  }

  $(document).ready(function() {
    fnCek();
    btnSimpanSementara.click(function() {
      fnSimpanSementara();
    });
    btnSimpanFinal.click(function() {
      fnSimpanFinal();
    });
  })
</script>
