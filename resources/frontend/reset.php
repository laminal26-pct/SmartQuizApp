<?php include_once 'master/header.php'; ?>
  <div class="login-box-body">
  <?php
    if (isset($_GET['t']) && isset($_GET['e']) && isset($_GET['_token'])) {
      $tipe = base64_decode($_GET['t']);
      $email = base64_decode($_GET['e']);
      $token = $_GET['_token'];
      $sql = "SELECT UNIX_TIMESTAMP(tb_token.expried_in) as exp, tb_users.email,
              tb_users.id_user, tb_token.id_user, tb_token.forget_token FROM tb_users
              INNER JOIN tb_token ON tb_token.id_user = tb_users.id_user
              WHERE tb_users.email='$email' AND tb_token.forget_token='$token' LIMIT 1";
      $result = mysqli_query($link,$sql); $r = mysqli_fetch_assoc($result);
      $old = $r['exp'];
      if ($tipe == "reset-password") {
        if ($old > time()) {
          if (mysqli_num_rows($result) == 1 && $r['forget_token'] == $token) {
            ?>
              <p class="login-box-msg">Please Input Password</p>
              <div id="message"></div>
              <form method="POST" id="reset-password">
                <input type="hidden" name="email" value="<?= $email; ?>">
                <div class="form-group has-feedback">
                  <input type="password" name="password" class="form-control" placeholder="Password" required>
                  <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                  <p id="password"></p>
                </div>
                <div class="row">
                  <div class="col-md-5 col-xs-7 pull-right">
                    <input type="submit" class="btn btn-info btn-block" value="Reset Password">
                  </div>
                </div>
              </form>
            <?php
          }
          elseif (mysqli_num_rows($result) == 0 && $r['forget_token'] == NULL) {
            ?>
              <div class="alert alert-warning">
                <label class="control-label">Token was used to verification !</label>
                Login to <a href="<?= BASE_URL; ?>">Click Here !</a>
              </div>
            <?php
          }
          else {
            ?>
              <div class="alert alert-danger">
                <label class="control-label">Reset Password Failed</label>
              </div>
            <?php
          }
        }
        else {
          ?>
            <div id="message">
              <div class="alert alert-danger">
                <label class="control-label">Expried time for reset password</label>
              </div>
            </div>
          <?php
        }
      }
      else {
        header('location: 404');
      }
    }
    else {
      ?>
        <div id="message">
          <div class="alert alert-danger">
            <label class="control-label">Access Forbidden</label>
          </div>
        </div>
      <?php
    }
  ?>
  </div>
<?php include_once 'master/footer.php'; ?>
