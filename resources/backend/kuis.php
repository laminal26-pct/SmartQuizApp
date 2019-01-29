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
            <table id="tabeluser" class="table table-striped table-bordered" data-remote="<?= base64_encode(sha1('dataKuis')); ?>" data-target="<?= base64_encode('tabelKuis'); ?>">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Judul</th>
                  <th>Username</th>
                  <th>Jumlah Soal</th>
                  <th>Durasi</th>
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
  #tabeluser thead tr th:nth-child(4) {
    width: 10% !important;
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
      'pageLength': 100,
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
  });
</script>
