<?php
require_once '../../path.php';
require_once (ABSPATH . 'config/config.php');
require_once (ABSPATH . 'config/database.php');

if (isset($_GET['f']) && isset($_GET['d'])) {
  $route = base64_decode($_GET['f']);
  $uuid = base64_decode($_GET['d']);

  // modal kuis
  if ($route == sha1('modal') && $uuid == "kuis") {
    # code...
  }
  // modal withdraw
  elseif ($route == sha1('modal') && $uuid == "withdraw") {
    # code...
  }
  // modal user
  elseif ($route == sha1('modal') && $uuid == "user") {
    ?>
    <div class="modal fade" id="addModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title text-center">Tambah Data User</h3>
          </div>
          <form class="form-horizontal" method="post" role="form" id="formtambah" data-remote="<?= base64_encode(sha1('datauser')); ?>" data-target="<?= base64_encode('tambahUser') ?>">
            <div class="modal-body">
              <div class="boxModal">
                <div class="form-group">
                  <label class="col-xs-3 control-label">Tipe User</label>
                  <div class="col-xs-9">
                    <select class="form-control" name="tipe_user" required>
                      <option selected disabled value="0">Pilih Tipe User</option>
                      <?php
                        $a = mysqli_query($link,"SELECT * FROM tb_level ORDER BY nama_level ASC");
                        while ($r = mysqli_fetch_assoc($a)) {
                          ?>
                            <option value="<?= $r['id_level']; ?>"><?= ucwords($r["nama_level"]); ?></option>
                          <?
                        }
                      ?>
                    </select>
                    <p id="tipe"></p>
                  </div>
                </div>
                <div class="form-group has-feedback">
                  <label class="col-xs-3 control-label">Nama</label>
                  <div class="col-xs-9">
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                    <span class="fa fa-user form-control-feedback"></span>
                    <p id="name"></p>
                  </div>
                </div>
                <div class="form-group has-feedback">
                  <label class="col-xs-3 control-label">Email</label>
                  <div class="col-xs-9">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <span class="fa fa-envelope form-control-feedback"></span>
                    <p id="username"></p>
                  </div>
                </div>
                <div class="form-group has-feedback">
                  <label class="col-xs-3 control-label">Password</label>
                  <div class="col-xs-9">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <p id="password"></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="simpanUser" class="btn btn-primary">
                <span class="fa fa-save"></span> &nbsp;Tambah User
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title text-center">Edit Data User</h3>
          </div>
          <form class="form-horizontal" method="post" role="form" id="formedit" data-remote="<?= base64_encode(sha1('datauser')); ?>" data-target="<?= base64_encode('ubahUser') ?>" enctype=multipart/form-data>
            <input type="hidden" name="id" value="">
            <div class="modal-body">
              <div class="boxModal">
                <div class="form-group">
                  <label class="col-xs-3 control-label">Username</label>
                  <div class="col-xs-9">
                    <p></p>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-xs-3 control-label">Nama</label>
                  <div class="col-xs-9">
                    <input type="text" name="nama" class="form-control " placeholder="Nama" required>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-xs-3 control-label">Status</label>
                  <div class="col-xs-9">
                    <select class="form-control" name="status" required title="Pilih Status">
                      <option selected disabled value="status">Pilih Status</option>
                      <option value="1">Aktif</option>
                      <option value="0">Tidak Aktif</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="ubahBerita" class="btn btn-primary">
                <span class="fa fa-save"></span> &nbsp;Ubah User
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="detailModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title text-center">Detail User</h4>
          </div>
          <div class="modal-body">
            <table class="table table-striped table-bordered" id="modalDetailTabel">
              <tbody>
                <tr>
                  <td>Nama</td>
                  <td><p></p></td>
                </tr>
                <tr>
                  <td>Username</td>
                  <td><p></p></td>
                </tr>
                <tr>
                  <td>Status</td>
                  <td><p></p></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">

          </div>
        </div>
      </div>
    </div>
    <?php
  }
}
