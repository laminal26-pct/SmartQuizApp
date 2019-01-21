<?php include_once 'master/header.php'; ?>
  <div class="login-box-body">
  <?php
    if (isset($_GET['t']) != NULL && isset($_GET['e']) != NULL && isset($_GET['_token']) != NULL) {
      $tipe = base64_decode($_GET['t']);
      $email = base64_decode($_GET['e']);
      $token = $_GET['_token'];
      $sql = "SELECT UNIX_TIMESTAMP(tb_token.expried_in) as exp, tb_users.email, tb_users.id_user,
              tb_users.name, tb_token.id_user, tb_token.verify_token FROM tb_users
              INNER JOIN tb_token ON tb_token.id_user = tb_users.id_user
              WHERE tb_users.email='$email' AND tb_token.verify_token='$token' LIMIT 1";
      $dateNow = date('Y-m-d H:i:s',strtotime('now'));
      if ($tipe == "verification-account") {
        ?>
        <div id="message">
        <?php
          $result = mysqli_query($link,$sql);
          $r = mysqli_fetch_assoc($result);
          $old = $r['exp'];
          $id = $r['id_user'];
          if ($old > time()) {
            if (mysqli_num_rows($result) == 1 && $r['verify_token'] == $token) {
              $nm = $r['name'];
              mysqli_query($link,"UPDATE tb_token SET access_token=NULL, verify_token=NULL, forget_token=NULL,expried_in=NULL, updated_at='$dateNow' WHERE id_user='$id'");
              mysqli_query($link,"UPDATE tb_users SET status='2', updated_at='$dateNow' WHERE id_user='$id'");
              mysqli_query($link,"INSERT INTO tb_profile VALUES(NULL,'$id','$nm',NULL,'O',NULL,NULL,'0','0',NULL,NULL,NULL,'$dateNow','$dateNow')");
              ?>
                <div class="alert alert-success">
                  <label class="control-label">Verification Success</label>
                  Login to <a href="<?= BASE_URL; ?>">Click Here !</a>
                </div>
              <?php
            }
            elseif (mysqli_num_rows($result) == 0 && $r['verify_token'] == NULL) {
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
                  <label class="control-label">Verification Failed</label>
                </div>
              <?php
            }
          }
          else {
            $id != null ? mysqli_query($link,"DELETE FROM tb_users WHERE id_user='$id'") : '';
            ?>
              <div class="alert alert-danger">
                <label class="control-label">Expried time for Verification</label>
              </div>
            <?php
          }
        ?>
        </div>
        <?php
      }
      elseif ($tipe == "re-verification-email") {
        ?>
        <div id="message">
        <?php
          $result = mysqli_query($link,$sql);
          $r = mysqli_fetch_assoc($result);
          $old = $r['exp'];
          $id = $r['id_user'];
          if ($old > time()) {
            if (mysqli_num_rows($result) == 1 && $r['verify_token'] == $token) {
              $nm = $r['name'];
              mysqli_query($link,"UPDATE tb_token SET access_token=NULL, verify_token=NULL, forget_token=NULL,expried_in=NULL, updated_at='$dateNow' WHERE id_user='$id'");
              mysqli_query($link,"UPDATE tb_users SET status='2', updated_at='$dateNow' WHERE id_user='$id'");
              //mysqli_query($link,"INSERT INTO tb_profile VALUES(NULL,'$id','$nm',NULL,'O',NULL,NULL,'0','0',NULL,NULL,NULL,'$dateNow','$dateNow')");
              ?>
                <div class="alert alert-success">
                  <label class="control-label">Verification Success</label>
                  Login to <a href="<?= BASE_URL; ?>">Click Here !</a>
                </div>
              <?php
            }
            elseif (mysqli_num_rows($result) == 0 && $r['verify_token'] == NULL) {
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
                  <label class="control-label">Verification Failed</label>
                </div>
              <?php
            }
          }
          else {
            $id != null ? mysqli_query($link,"DELETE FROM tb_users WHERE id_user='$id'") : '';
            ?>
              <div class="alert alert-danger">
                <label class="control-label">Expried time for Verification</label>
              </div>
            <?php
          }
        ?>
        </div>
        <?php
      }
      else {
        header('location: 404');
      }
    }
    else {
      ?>
        <div class="alert alert-danger">
          <label class="control-label">Access Forbidden</label>
        </div>
      <?php
    }
  ?>
  </div>
<?php include_once 'master/footer.php'; ?>
