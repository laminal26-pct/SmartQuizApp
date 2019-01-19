<?php
  require_once '../../path.php';
  require_once (ABSPATH . 'config/config.php');
  require_once (ABSPATH . 'config/database.php');
  $url = BASE_URL;
  header("Access-Control-Allow-Origin: $url");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  $data = [];
  $_token = $_GET['_token'];
  $token = mysqli_query($link,"SELECT UNIX_TIMESTAMP(expried_in) as exp, access_token FROM tb_token WHERE access_token='$_token' LIMIT 1");
  $r = mysqli_fetch_assoc($token);
  $old = $r['exp'];
  if ($old > time()) {
    if (mysqli_num_rows($token) == 1) {
      # code...
    }
    else {
      $data['home'] = array(
        'kode' => '0',
        'message' => 'Access Forbidden',
        'setLogin' => true
      );
    }
  }
  else {
    $data['home'] = array(
      'kode' => '0',
      'message' => 'Expried session. Login Again !',
      'setLogin' => true
    );
  }

  echo json_encode($data);
