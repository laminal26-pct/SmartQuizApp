<?php include_once 'master/header.php'; ?>
    <div class="login-box">
      <div class="login-logo">
        <b><?= APPNAME; ?></b>
      </div>
      <div class="login-box-body">
        <p class="login-box-msg">Please Login to Dashboard</p>
        <div id="message"></div>
        <form method="POST" id="login">
          <div class="form-group has-feedback">
            <input type="hidden" name="firebase_token" value="">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <span class="fa fa-user form-control-feedback"></span>
            <p id="username"></p>
          </div>
          <div class="form-group has-feedback">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <p id="password"></p>
          </div>
          <div class="row">
            <div class="col-md-4 col-xs-6 pull-right">
              <input type="submit" class="btn btn-info btn-block" value="Login">
            </div>
          </div>
        </form>
        I forget my password &nbsp;<a href="<?= BASE_URL . '/forget-password'; ?>">Click here !</a><br>
        Haven't an account ? &nbsp;<a href="<?= BASE_URL . '/register'; ?>">Register</a>
      </div>
    </div>
<?php include_once 'master/footer.php'; ?>
