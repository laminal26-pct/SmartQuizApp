<!-- Left side column. contains the sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?= BASE_URL;?>/assets/img/avatar04.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info" id="account">
              <p><?= $_SESSION['username']; ?></p>
              <a href="<?= BASE_URL.'/dashboard/admin/profile'; ?>"><i class="fa fa-user text-yellow"></i></a>
              <a href="<?= BASE_URL.'/dashboard/admin/password'; ?>"><i class="fa fa-lock text-blue"></i></a>
              <a href="<?= BASE_URL.'/logout'; ?>"><i class="fa fa-sign-out text-red"></i></a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu" data-widget="tree" id="navigation">
            <li class="header">MAIN NAVIGATION</li>
            <li class="<?= (SECOND_PART == "dashboard" && THIRD_PART == '' && FOURTH_PART == '') ? 'active' : ''; ?>">
              <a href="<?= BASE_URL.'/dashboard/'; ?>" class="menu">
                <i class="fa fa-dashboard"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li class="<?= (SECOND_PART == "dashboard" && THIRD_PART == 'admin' && FOURTH_PART == 'berita') ? 'active' : ''; ?>">
              <a href="<?= BASE_URL.'/dashboard/kuis'; ?>" class="menu">
                <i class="fa fa-newspaper-o"></i>
                <span>Kuis</span>
              </a>
            </li>
            <li class="<?= (SECOND_PART == "dashboard" && THIRD_PART == 'admin' && FOURTH_PART == 'galeri') ? 'active' : ''; ?>">
              <a href="<?= BASE_URL.'/dashboard/with-draw'; ?>" class="menu">
                <i class="fa fa-bank"></i>
                <span>Request Withdraw</span>
              </a>
            </li>
            <li class="<?= (SECOND_PART == "dashboard" && THIRD_PART == 'admin' && FOURTH_PART == 'content') ? 'active' : ''; ?>">
              <a href="<?= BASE_URL.'/dashboard/voucher'; ?>" class="menu">
                <i class="fa fa-feed"></i>
                <span>Voucher</span>
              </a>
            </li>
            <li class="<?= (SECOND_PART == "dashboard" && THIRD_PART == 'admin' && FOURTH_PART == 'user') ? 'active' : ''; ?>">
              <a href="<?= BASE_URL.'/dashboard/user'; ?>" class="menu">
                <i class="fa fa-users"></i>
                <span>User</span>
              </a>
            </li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
