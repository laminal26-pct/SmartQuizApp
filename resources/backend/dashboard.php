<?php include_once 'master/header.php'; ?>
  <div class="content-wrapper" id="content-field">
    <?php
      if (@$_REQUEST['menu'] == "") {
        ?>
        <!-- Main content -->
        <section class="content">
          <div class="callout callout-warning">
            <h4>Beta Testing</h4>
            <p>Masih dalam pengembangan</p>
          </div>
        </section>
        <!-- /.content -->
        <?php
      }
      elseif (@$_REQUEST['menu'] == "kuis") {
        include_once 'kuis.php';
      }
      elseif (@$_REQUEST['menu'] == "with-draw") {
        include_once 'withdraw.php';
      }
      elseif (@$_REQUEST['menu'] == "voucher") {
        include_once 'voucher.php';
      }
      elseif (@$_REQUEST['menu'] == "user") {
        include_once 'user.php';
      }
      elseif (@$_REQUEST['menu'] == "profile") {
        include_once 'profile.php';
      }
      elseif (@$_REQUEST['menu'] == "password") {
        include_once 'password.php';
      }
      else {
        include_once '404.php';
      }
    ?>
  </div>
<?php include_once 'master/footer.php'; ?>
