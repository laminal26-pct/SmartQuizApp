<?php
  include_once 'resources/frontend/master/header.php';

  if (@$_REQUEST['menu']) {
    if (@$_REQUEST['menu'] == "login") {
      include_once 'resources/frontend/login.php';
    }
    elseif (@$_REQUEST['menu'] == "register") {
      include_once 'resources/frontend/register.php';
    }
    elseif (@$_REQUEST['menu'] == "forget-password") {
      include_once 'resources/frontend/forget.php';
    }
    elseif (@$_REQUEST['menu'] == "reset-password") {
      include_once 'resources/frontend/reset.php';
    }
    elseif (@$_REQUEST['menu'] == "verification") {
      include_once 'resources/frontend/verify.php';
    }
    else {
      include_once 'resources/frontend/404.php';
    }
  }
  else {
    include_once 'resources/frontend/login.php';
  }

  include_once 'resources/frontend/master/footer.php';
?>
