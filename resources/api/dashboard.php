<?php
  require_once '../../path.php';
  require_once (ABSPATH . 'config/config.php');
  require_once (ABSPATH . 'config/database.php');
  require_once (ABSPATH . 'vendor/autoload.php');
  $app = APPDEBUG;
  $appName = APPNAME;
  $url = BASE_URL;
  header("Access-Control-Allow-Origin: $url");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  $data = array();

  if (isset($_GET['_token']) != NULL) {
    $_token = $_GET['_token'];
    $token = mysqli_query($link,"SELECT UNIX_TIMESTAMP(expried_in) as exp, access_token, id_user FROM tb_token WHERE access_token='$_token' LIMIT 1");
    $r = mysqli_fetch_assoc($token);
    $old = $r['exp'];
    $id = $r['id_user'];
    if ($old > time()) {
      if (mysqli_num_rows($token) == 1) {
        if (isset($_GET['f']) && isset($_GET['d'])) {
          $route = $_GET['f'];
          $uuid  = $_GET['d'];
          if ($route == NULL && $uuid == NULL) {
            $data['home'] = array(
              'kode' => '0',
              'message' => 'URL Can\'t Empty !!!',
            );
          }
          // buat kuis
          elseif ($route == "dashboard" && $uuid == "buatKuis") {
            # code...
          }
          // tambah kuis
          elseif ($route == "dashboard" && $uuid == "simpanKuis" && isset($_GET['email'])) {
            # code...
          }
          // buat soal
          elseif ($route == "dashboard" && $uuid == "buatSoal" && isset($_GET['idKuis'])) {
            # code...
          }
          // simpan soal
          elseif ($route == "dashboard" && $uuid == "simpanSoal" && isset($_GET['idKuis'])) {
            # code...
          }
          // list kuis
          elseif ($route == "dashboard" && $uuid == "listKuis" && isset($_GET['email'])) {
            # code...
          }
          // detail & edit kuis
          elseif ($route == "dashboard" && ($uuid == "detailKuis" || $uuid == "editKuis") && isset($_GET['idKuis'])) {
            # code...
          }
          // hapus kuis
          elseif ($route == "dashboard" && $uuid == "hapusKuis" && isset($_GET['idKuis'])) {
            # code...
          }
          // list soal
          elseif ($route == "dashboard" && $uuid == "listSoal" && isset($_GET['idKuis'])) {
            # code...
          }
          // detail & edit soal
          elseif ($route == "dashboard" && ($uuid == "detailSoal" || $uuid == "editSoal") && isset($_GET['idSoal'])) {
            # code...
          }
          // hapus soal
          elseif ($route == "dashboard" && $uuid == "hapusSoal" && isset($_GET['idSoal'])) {
            # code...
          }
          // withdraw
          elseif ($route == "dashboard" && $uuid == "withDraw" && isset($_GET['email'])) {
            # code...
          }
          
        }
      }
      else {
        $data['dashboard'] = array(
          'kode' => '2',
          'message' => 'Expried session. Login Again !',
          'setLogin' => true,
        );
      }
    }
    else {
      $data['dashboard'] = array(
        'kode' => '2',
        'message' => 'Expried session. Login Again !',
        'setLogin' => true,
      );
    }
  }
  else {
    $data['dashboard'] = array(
      'kode' => '2',
      'message' => 'Invalid Token',
      'setLogin' => true,
    );
  }

  echo json_encode($data);
