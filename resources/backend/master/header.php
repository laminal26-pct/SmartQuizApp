<?php
  session_start();
  require_once '../../path.php';
  require_once (ABSPATH . 'config/config.php');
  require_once (ABSPATH . 'config/database.php');
  if (!isset($_SESSION['is_logged'])) {
    $url = BASE_URL;
    echo "<script>var url = '$url'; window.location.href= url;</script>";
    exit();
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SmartQuizApp | Dashboard</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/plugins/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/plugins/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/plugins/sweetalert2/dist/sweetalert2.min.css"/>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/plugins/DataTables/datatables.min.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/AdminLTE.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <meta name="url" content="<?= BASE_URL; ?>">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="apple-touch-icon" sizes="57x57" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL; ?>/assets/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?= BASE_URL; ?>/assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL; ?>/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= BASE_URL; ?>/assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL; ?>/assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= BASE_URL; ?>/assets/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#eb2c5b">
    <meta name="msapplication-TileImage" content="<?= BASE_URL; ?>/assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#eb2c5b">
    <!--link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"-->
    <script src="<?= BASE_URL; ?>/assets/plugins/jquery/dist/jquery.min.js"></script>
    <script src="<?= BASE_URL; ?>/assets/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="<?= BASE_URL; ?>/assets/plugins/DataTables/datatables.min.js"></script>
    <script src="<?= BASE_URL; ?>/assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?= BASE_URL; ?>/assets/plugins/tinymce/jquery.tinymce.min.js"></script>
    <script src="<?= BASE_URL; ?>/assets/plugins/tinymce/tinymce.min.js"></script>
    <style>
      .skin-blue .main-header .navbar {
        background-color: #eb2c5b !important;
      }
      .skin-blue .main-header .logo {
        background-color: #eb2c5b !important;
        color: #fff;
        border-bottom: 0 solid transparent;
      }
      .main-header .sidebar-toggle {
        float: left;
        background-color: #eb2c5b !important;
        background-image: none;
        padding: 15px 15px;
        font-family: fontAwesome;
      }
    </style>
  </head>

  <body class="hold-transition skin-blue sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">

      <header class="main-header">
        <!-- Logo -->
        <a href="<?= BASE_URL; ?>/dashboard/admin" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>S</b>QA</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>S</b>QA</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>

          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="<?= BASE_URL . '/assets/img/avatar04.png'; ?>" class="user-image" alt="User Image">
                  <span class="hidden-xs"><?= $_SESSION['username']; ?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- Menu Body -->
                  <li class="user-body">
                    <a href="<?= BASE_URL.'/dashboard/admin/profile'; ?>">
                      <i class="fa fa-user text-yellow"></i>
                      <span>Profil</span>
                    </a>
                  </li>
                  <li class="user-body">
                    <a href="<?= BASE_URL.'/dashboard/admin/password'; ?>">
                      <i class="fa fa-lock text-blue"></i>
                      <span>Password</span>
                    </a>
                  </li>
                  <li class="user-body">
                    <a href="<?= BASE_URL.'/logout'; ?>">
                      <i class="fa fa-sign-out text-red"></i>
                      <span>Logout</span>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>

      <?php include_once "navigation.php"; ?>
