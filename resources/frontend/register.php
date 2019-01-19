        <div class="login-box-body">
          <p class="login-box-msg">Please Register</p>
          <div id="message"></div>
          <form method="POST" id="register">
            <div class="form-group has-feedback">
              <select class="form-control" name="tipe_user" required>
                <option selected disabled value="0">Pilih Tipe User</option>
                <option value="2">Author</option>
                <option value="3">User</option>
              </select>
              <p id="tipe"></p>
            </div>
            <div class="form-group has-feedback">
              <input type="text" name="name" class="form-control" placeholder="Name" required>
              <span class="fa fa-user form-control-feedback"></span>
              <p id="name"></p>
            </div>
            <div class="form-group has-feedback">
              <input type="email" name="email" class="form-control" placeholder="Email" required>
              <span class="fa fa-envelope form-control-feedback"></span>
              <p id="username"></p>
            </div>
            <div class="form-group has-feedback">
              <input type="password" name="password" class="form-control" placeholder="Password" required>
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              <p id="password"></p>
            </div>
            <div class="row">
              <div class="col-md-4 col-xs-6 pull-right">
                <input type="submit" class="btn btn-info btn-block" value="Register">
              </div>
            </div>
          </form>
          I forget my password &nbsp;<a href="<?= BASE_URL . '/forget-password'; ?>">Click here !</a><br>
          Have an account ? &nbsp;<a href="<?= BASE_URL . '/'; ?>">Login</a>
        </div>
