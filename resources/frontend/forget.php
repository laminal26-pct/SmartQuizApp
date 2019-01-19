        <div class="login-box-body">
          <p class="login-box-msg">Forget Password, input your email</p>
          <div id="message"></div>
          <form method="POST" id="forget-password">
            <div class="form-group has-feedback">
              <input type="email" name="email" class="form-control" placeholder="Email" required>
              <span class="fa fa-envelope form-control-feedback"></span>
              <p id="username"></p>
            </div>
            <div class="row">
              <div class="col-md-5 col-xs-7 pull-right">
                <input type="submit" class="btn btn-info btn-block" value="Forget Password">
              </div>
            </div>
          </form>
          Have an account ? &nbsp;<a href="<?= BASE_URL . '/'; ?>">Login</a> <br>
          Haven't an account ? &nbsp;<a href="<?= BASE_URL . '/'; ?>">Register</a>
        </div>
