<!-- Main content -->
<section class="content">
  <!--Default box -->
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Data <?= ucwords($_REQUEST['menu']); ?></h3>
        <div class="pull-right">
          <a id="tambahdata" name="tambahdata" class="btn btn-primary btn-xs">
            <i class="fa fa-plus"></i>
            <span>Tambah Data</span>
          </a>
        </div>
      </div>
      <div class="box-body">
        <div class="col-md-12 col-xs-12">
          <div class="table-responsive">
            <table id="tabeluser" class="table table-striped table-bordered" data-remote="<?= base64_encode(sha1('dataUser')); ?>" data-target="<?= base64_encode('tabelUser'); ?>">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Level</th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>IP address</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- /.content -->
<style>
  #tabeluser thead tr th:nth-child(1) {
    width: 3% !important;
  }
  #tabeluser thead tr th:nth-child(2) {
    width: 7% !important;
  }
  #tabeluser tbody tr td:nth-child(2) {
    text-align: center;
  }
  #tabeluser thead tr th:nth-child(4) {
    width: 20% !important;
    text-align: center;
  }
  #tabeluser tbody tr td:nth-child(4) {
    text-align: center;
  }
  #tabeluser thead tr th:nth-child(5) {
    width: 10% !important;
    text-align: center;
  }
  #tabeluser tbody tr td:nth-child(5) {
    text-align: center;
  }
  #tabeluser thead tr th:nth-child(6) {
    width: 10% !important;
    text-align: center;
  }
  #tabeluser tbody tr td:nth-child(6) {
    text-align: center;
  }
  #tabeluser thead tr th:nth-child(7) {
    width: 24% !important;
    text-align: center;
  }
  #tabeluser tbody tr td:nth-child(7) {
    text-align: center;
  }
  #modalDetailTabel tbody tr td:nth-child(1){
    width: 10%;
  }
</style>
<div id="content-modal" data-remote="<?= base64_encode(sha1('modal')); ?>" data-target="<?= base64_encode('user'); ?>"></div>
<script type="text/javascript">
  $(function () {
    var http = $('meta[name="url"]').attr('content');
    $('div#content-modal').html("");
    var page = $('div#content-modal').attr('data-remote');
    var uuid = $('div#content-modal').attr('data-target');
    $.ajax({
      url: http+'/modal',
      data: 'f='+page+'&d='+uuid,
      dataType: 'html',
      async: false,
      type: 'GET',
      success: function(res) {
        $('#content-modal').append(res);
      }
    });

    var page = $('#tabeluser').attr('data-remote');
    var uuid = $('#tabeluser').attr('data-target');
    var dataTableUser = $('#tabeluser').DataTable({
      'processing': true,
      'serverSide': true,
      'ajax': {
        url: http+'/fetch?f='+page+"&d="+uuid,
        type: 'POST',
        beforeSend: function() {
          $("#tabeluser_processing").html('<i class="fa fa-spinner fa-pulse fa-fw text-blue"></i>&nbsp;Memuat Data...');
        },
        error: function(){
          $(".tabeluser-error").html("");
          $("#tabeluser").append('<tbody class="tabeluser-error"><tr><td colspan="7">No data found in the server</td></tr></tbody>');
          $("#tabeluser_processing").css("display","none");
        }
      },
      'pageLength': 25,
      columnDefs: [
        { orderable:false, targets: [4,6], searchable: false},
        { orderable:true, targets: [0,1,2,3,5]},
      ]
    });

    $('#tambahdata').on('click', function(e) {
      e.preventDefault();
      $('#addModal').modal({
        'show': true,
        'backdrop': 'static'
      });
    });
    /*$('#formtambah').on('submit', function(e) {
      e.preventDefault();
      var page = $(this).attr('data-remote');
      var uuid = $(this).attr('data-target');
      var form = new FormData($('#formtambah')[0]);
      $.ajax({
        url: 'user/fetch?f='+page+'&d='+uuid,
        data: form,
        type: 'POST',
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: function() {
          $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
        },
        success: function(res) {
          if (res.message == "OK") {
            $('#formtambah')[0].reset();
            $('#addModal .close').click();
            dataTableUser.ajax.reload();
            $('div#loading').hide();
            swal({
              title: 'Sukses',
              text: "Berhasil menambahkan user",
              type: 'success',
              allowOutsideClick: false,
              showConfirmButton: true,
            });
          } else {
            $('div#loading').hide();
            swal({
              title: 'Peringatan',
              text: res.message,
              type: 'error',
              allowOutsideClick: false,
              showConfirmButton: true,
            });
          }
        }
      })
    });
    */
    $('button[type=submit]').attr('disabled','disabled');
    function validateEmail(email) {
      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    }

    $('#formtambah').on('keyup change click', function() {
      var tipe = $('select').val();
      var name = $('input[name=name]').val();
      var username = $('input[name=email]').val();
      var password = $('input[name=password]').val();
      $label = $(this).find('p');
      $formGroup = $(this).find('.form-group');

      if (tipe == 0) {
        $formGroup.eq(0).addClass('has-error');
        $label.eq(0).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Harus Pilih Tipe User !');
      } else {
        $formGroup.eq(0).removeClass('has-error');
        $formGroup.eq(0).addClass('has-success');
        $label.eq(0).removeClass().html('');
      }
      if (name.length == 0) {
        $formGroup.eq(1).addClass('has-error');
        $label.eq(1).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Nama tidak boleh kosong !');
      } else if (name.length < 3) {
        $formGroup.eq(1).addClass('has-error');
        $label.eq(1).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Nama minimal 3 karakter !')
      } else {
        $formGroup.eq(1).removeClass('has-error');
        $formGroup.eq(1).addClass('has-success');
        $label.eq(1).removeClass().html('');
      }
      if (!validateEmail(username)) {
        $formGroup.eq(2).addClass('has-error');
        $label.eq(2).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Email tidak valid !');
      } else {
        $formGroup.eq(2).removeClass('has-error');
        $formGroup.eq(2).addClass('has-success');
        $label.eq(2).removeClass().html('');
      }
      if (password.length == 0) {
        $formGroup.eq(3).addClass('has-error');
        $label.eq(3).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Password tidak boleh kosong !');
      } else if (password.length < 8) {
        $formGroup.eq(3).addClass('has-error');
        $label.eq(3).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Password minimal 8 karakter !');
      } else {
        $formGroup.eq(3).removeClass('has-error');
        $formGroup.eq(3).addClass('has-success');
        $label.eq(3).removeClass().html('');
      }
      if ((tipe != 'undefined' || tipe != null) && name.length > 3 && validateEmail(username) && password.length > 7) {
        $('button[type=submit]').removeAttr('disabled');
      } else {
        $('button[type=submit]').attr('disabled','disabled');
      }
    });
    $('#formtambah').on('submit', function(e) {
      e.preventDefault();
      var register = $(this).serialize();
      $input = $('#formtambah').find('input[type=text], input[type=password]');
      $.ajax({
        url: http+'/api/v1/register',
        type: 'POST',
        async: false,
        data: register,
        beforeSend: function() {
          $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
          $('button[type=submit]').attr('disabled','disabled');
        },
        success: function(response) {
          if (response.auth.kode == "1") {
            $('button[type=submit]').attr('disabled','disabled');
            $('#formtambah')[0].reset();
            $('#addModal .close').click();
            dataTableUser.ajax.reload();
            $('div#loading').hide();
            swal({
              title: 'Sukses',
              text: response.auth.message,
              type: 'success',
              allowOutsideClick: false,
              showConfirmButton: true,
            });
          } else {
            $('button[type=submit]').attr('disabled','disabled');
            $formGroup.eq(0).removeClass('has-success');
            $formGroup.eq(0).addClass('has-error');
            $formGroup.eq(1).removeClass('has-success');
            $formGroup.eq(1).addClass('has-error');
            $formGroup.eq(2).removeClass('has-success');
            $formGroup.eq(2).addClass('has-error');
            $formGroup.eq(3).removeClass('has-success');
            $formGroup.eq(3).addClass('has-error');
            $input.eq(0).val('');
            $input.eq(1).val('');
            $input.eq(2).val('');
            $('div#loading').hide();
            swal({
              title: 'Peringatan',
              text: response.auth.message,
              type: 'warning',
              allowOutsideClick: false,
              showConfirmButton: true,
            });
          }
        }
      });
      return false;
    });

    $('#tabeluser').on('click','#edit', function(e) {
      e.preventDefault();
      var page = $('#tabeluser').attr('data-remote');
      var uuid = $(this).attr('data-target');
      var id = $(this).attr('data-user');
      $a = $('#formedit').find('input[type=text], select, textarea');
      $b = $('#formedit').find('p');
      $c = $('#formedit').find('input[type=hidden]');
      $.ajax({
        url: 'user/fetch?f='+page+'&d='+uuid+'&id='+id,
        type: 'GET',
        async: false,
        dataType: 'json',
        beforeSend: function() {
          $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
        },
        success: function(res) {
          if (res.message == "OK") {
            $('div#loading').hide();
            $('#editModal').modal({
              'show': true,
              'backdrop': 'static'
            });
            $c.eq(0).val(res.user.id_user);
            $b.eq(0).html(res.user.username);
            $a.eq(0).val(res.user.nama);
            $a.eq(1).val(res.user.status);
          } else {
            $('div#loading').hide();
            swal({
              title: 'Peringatan',
              text: res.message,
              type: 'error',
              allowOutsideClick: false,
              showConfirmButton: true,
            });
          }
        }
      });
    });
    $('#formedit').on('submit', function(e) {
      e.preventDefault();
      var page = $(this).attr('data-remote');
      var uuid = $(this).attr('data-target');
      var id = $(this).find('input[type=hidden]').val();
      var form = new FormData($('#formedit')[0]);
      $.ajax({
        url: 'user/fetch?f='+page+'&d='+uuid+'&id='+id,
        data: form,
        type: 'POST',
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: function() {
          $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
        },
        success: function(res) {
          if (res.message == "OK") {
            $('#formedit')[0].reset();
            $('#editModal .close').click();
            dataTableUser.ajax.reload();
            $('div#loading').hide();
            swal({
              title: 'Sukses',
              text: "Berhasil mengubah user",
              type: 'success',
              allowOutsideClick: false,
              showConfirmButton: true,
            });
          } else {
            $('div#loading').hide();
            swal({
              title: 'Peringatan',
              text: res.message,
              type: 'error',
              allowOutsideClick: false,
              showConfirmButton: true,
            });
          }
        }
      });
    });
    $('#tabeluser').on('click','#detail', function(e) {
      e.preventDefault();
      var page = $('#tabeluser').attr('data-remote');
      var uuid = $(this).attr('data-target');
      var id = $(this).attr('data-user');
      $a = $('#modalDetailTabel').find('p');

      $.ajax({
        url: 'user/fetch?f='+page+'&d='+uuid+'&id='+id,
        type: 'GET',
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        beforeSend: function() {
          $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
        },
        success: function(res) {
          if (res.message == "OK") {
            $('div#loading').hide();
            $('#detailModal').modal({
              'show': true,
              'backdrop': 'static'
            });
            $a.eq(0).html(res.user.nama);
            $a.eq(1).html(res.user.username);
            $a.eq(2).html(res.user.status == 1 ? 'Aktif' : 'Tidak Aktif');
          } else {
            $('div#loading').hide();
            swal({
              title: 'Peringatan',
              text: res.message,
              type: 'error',
              allowOutsideClick: false,
              showConfirmButton: true,
            });
          }
        }
      });
    });
    $('#tabeluser').on('click','#hapus', function(e) {
      e.preventDefault();
      var page = $('#tabeluser').attr('data-remote');
      var uuid = $(this).attr('data-target');
      var id = $(this).attr('data-user');
      var nm = $(this).attr('title-user');
      swal({
        title: 'Apa Anda Yakin?',
        html: 'Menghapus user <b>'+ nm +'</b> ?',
        type: 'warning',
        showCancelButton: true,
        allowOutsideClick: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak'
      }).then(function (isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: 'user/fetch?f='+page+'&d='+uuid+'&id='+id,
            type: 'POST',
            dataType: 'json',
            async: false,
            processData: false,
            contentType: false,
            beforeSend: function() {
              $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
            },
            success: function(res) {
              if (res.message == "OK") {
                $('div#loading').hide();
                dataTableUser.ajax.reload();
                swal({
                  title: 'Sukses',
                  text: "Data berhasil dihapus",
                  type: 'success',
                  allowOutsideClick: false,
                  showConfirmButton: true,
                });
              }
            }
          });
        }
      });
    });
    $('#tabeluser').on('click','#reset', function(e) {
      e.preventDefault();
      var page = $('#tabeluser').attr('data-remote');
      var uuid = $(this).attr('data-target');
      var id = $(this).attr('data-user');
      var nm = $(this).attr('title-user');
      swal({
        title: 'Apa Anda Yakin?',
        html: 'Mereset password user <b>'+ nm +'</b> ?',
        type: 'warning',
        showCancelButton: true,
        allowOutsideClick: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak'
      }).then(function (isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: 'user/fetch?f='+page+'&d='+uuid+'&id='+id,
            type: 'POST',
            dataType: 'json',
            async: false,
            processData: false,
            contentType: false,
            beforeSend: function() {
              $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
            },
            success: function(res) {
              if (res.message == "OK") {
                $('div#loading').hide();
                dataTableUser.ajax.reload();
                swal({
                  title: 'Sukses',
                  html: "Password berhasil direset <b>12345678</b>",
                  type: 'success',
                  allowOutsideClick: false,
                  showConfirmButton: true,
                });
              }
            }
          });
        }
      });
    });
  });
  $(document).ready(function () {
    $('div#loading').hide();


    $('#forget-password').on('keyup change click', function() {
      var username = $('input[name=email]').val();
      $label = $(this).find('p');
      $formGroup = $(this).find('.form-group');

      if (!validateEmail(username)) {
        $formGroup.eq(0).addClass('has-error');
        $label.eq(0).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Email tidak valid !');
      } else {
        $formGroup.eq(0).removeClass('has-error');
        $formGroup.eq(0).addClass('has-success');
        $label.eq(0).removeClass().html('');
      }
      if (validateEmail(username)) {
        $('input[type=submit]').removeAttr('disabled');
      } else {
        $('input[type=submit]').attr('disabled','disabled');
      }
    });
    $('#forget-password').on('submit', function(e) {
      e.preventDefault();
      var email = $('#forget-password').serialize();
      $input = $('#forget-password').find('input[type=email]');
      $.ajax({
        url: 'api/v1/forget-password',
        type: 'POST',
        async: false,
        data: email,
        beforeSend: function() {
          $('#message').fadeOut();
          $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
          $('input[type=submit]').attr('disabled','disabled');
        },
        success: function(response) {
          if (response.auth.kode == "1") {
            $('#message').fadeIn(500, function() {
              $('input[type=submit]').attr('disabled','disabled');
              $formGroup.eq(0).removeClass('has-success');
              $input.eq(0).val('');
              $('div#loading').hide();
              $('div#message').addClass('alert alert-success').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
            });
          } else {
            $('#message').fadeIn(500, function() {
              $('input[type=submit]').attr('disabled','disabled');
              $formGroup.eq(0).removeClass('has-success');
              $formGroup.eq(0).addClass('has-error');
              $input.eq(0).val('');
              $('div#loading').hide();
              $('div#message').addClass('alert alert-danger').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
            });
          }
        }
      });
      return false;
    });
  });
</script>
