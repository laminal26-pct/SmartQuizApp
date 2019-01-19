<?php
  session_start();
  require_once '../../path.php';
  require_once (ABSPATH . 'config/config.php');
  require_once (ABSPATH . 'config/database.php');
  $url = BASE_URL;
  header("Access-Control-Allow-Origin: $url");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  $data = array();

  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if(isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
      $ipaddress = 'UNKNOWN';

  $token = hash('sha512',time());
  $tipeUrl = array(
    base64_encode('verification-account'),
    base64_encode('forget-password'),
    base64_encode('reset-password')
  );

  function sendMail($tipe, $email, $name, $body) {
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'mail.kukitriplan.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'no-reply@kukitriplan.com';
      $mail->Password = 'kampang26';
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->setFrom('no-reply@kukitriplan.com', APPNAME);
      $mail->addAddress($email, $name);

      $mail->isHTML(true);
      $mail->Subject = $tipe;
      $mail->Body    = $body;
    }
    catch(Exception $e) {
      $data['auth'] = array(
        'kode' => '0',
        'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
      );
    }
  }

  $mods = @$_REQUEST['tipe'] == 'user' ? 'Forbidden' : @$_REQUEST['tipe'];
  if ($mods == "Forbidden") {
    $data['auth'] = array(
      'kode' => '0',
      'message' => 'Access Forbidden !',
    );
  }
  else {
    if ($mods == "login") {
      echo $token;
      $a = mysqli_real_escape_string($link,strip_tags($_POST['email']));
      $b = mysqli_real_escape_string($link,strip_tags($_POST['password']));
      if (isset($_POST['email']) && isset($_POST['password']) &&
          !empty($_POST['email']) && !empty($_POST['password'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
          $check = mysqli_query($link,"SELECT email FROM tb_users WHERE email='$a'");
          if (mysqli_num_rows($check) == 0) {
            $data['auth'] = array(
              'kode' => '0',
              'message' => 'Email Belum terdaftar !',
            );
          }
          else {
            $pass = hash('sha512', $b);
            $sql = mysqli_query($link, "SELECT tb_users.*,tb_level.* FROM tb_users INNER JOIN tb_level ON tb_level.id_level = tb_users.id_level
                   WHERE tb_users.email='$a' AND tb_users.password='$pass'");
            $r = mysqli_fetch_assoc($sql);
            $id = $r['id_user'];
            if (mysqli_num_rows($sql) == 1 && $r['status'] == 0) {
              $data['auth'] = array(
                'kode' => '0',
                'message' => 'Akun Anda telah disuspend !',
              );
            }
            elseif (mysqli_num_rows($sql) == 1 && $r['status'] == 1) {
              $data['auth'] = array(
                'kode' => '0',
                'message' => 'Silahkan melakukan Aktivasi Email !',
              );
            }
            elseif (mysqli_num_rows($sql) == 1 && $r['status'] == 2) {
              # code...
            }
            else {
              $data['auth'] = array(
                'kode' => '0',
                'message' => 'Email / Password salah !',
              );
            }
          }
        }
        else {
          $data['auth'] = array(
            'kode' => '0',
            'message' => 'Email tidak valid !',
          );
        }
      }
      else {
        $data['auth'] = array(
          'kode' => '0',
          'message' => 'Email or Password tidak boleh kosong !',
        );
      }
    }
    elseif ($mods == "register") {
      $a = mysqli_real_escape_string($link,strip_tags($_POST['tipe']));
      $b = mysqli_real_escape_string($link,strip_tags($_POST['name']));
      $c = mysqli_real_escape_string($link,strip_tags($_POST['email']));
      $d = mysqli_real_escape_string($link,strip_tags($_POST['password']));
      if (isset($_POST['tipe']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) &&
          !empty($_POST['tipe']) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
          $getUser = explode("@",$c);
          $pass = hash('sha512',$d);
          $date = date('Y-m-d H:i:s',strtotime('now'));
          $check = mysqli_query($link,"SELECT email FROM tb_users WHERE email='$a'");
          if (mysqli_num_rows($check) == 1) {
            $data['auth'] = array(
              'kode' => '0',
              'message' => 'Email sudah terdaftar. Harap menggunakan email lain',
            );
          }
          else {
            $body = '
            Thanks for signing up!<br>
            Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.<br>

            ------------------------<br>
            Username: '.$name.'<br>
            ------------------------<br>

            Please click bottom button this link to activate your account: <br>
            <a href="'.$url.'/'.$tipeUrl.'/'.base64_encode($email).'/'.$token.'">Click here !</a><br>or copy link below<br>
            '.$url.'/account/'.$tipeUrl.'/'.base64_encode($email).'/'.$token.'<br>

            ';
          }
        }
        else {
          $data['auth'] = array(
            'kode' => '0',
            'message' => 'Email tidak valid !',
          );
        }
      }
      else {
        $data['auth'] = array(
          'kode' => '0',
          'message' => 'Tipe/Nama/Email/Password tidak boleh kosong !',
        );
      }
    }
    elseif ($mods == "forget-password") {
      $a = mysqli_real_escape_string($link,strip_tags($_POST['email']));
      if (isset($_POST['email']) && !empty($_POST['email'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

        }
        else {
          $data['auth'] = array(
            'kode' => '0',
            'message' => 'Email tidak valid !',
          );
        }
      }
      else {
        $data['auth'] = array(
          'kode' => '0',
          'message' => 'Email or Password tidak boleh kosong !',
        );
      }
    }
    elseif ($mods == "reset-password") {
      $a = mysqli_real_escape_string($link,strip_tags($_POST['email']));
      $b = mysqli_real_escape_string($link,strip_tags($_POST['passold']));
      $c = mysqli_real_escape_string($link,strip_tags($_POST['passnew']));
      if (isset($_POST['email']) && isset($_POST['passold']) && isset($_POST['passnew']) &&
          !empty($_POST['email']) && !empty($_POST['passold']) && !empty($_POST['passnew']) ) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

        }
        else {
          $data['auth'] = array(
            'kode' => '0',
            'message' => 'Email tidak valid !',
          );
        }
      }
      else {
        $data['auth'] = array(
          'kode' => '0',
          'message' => 'Password tidak boleh kosong !',
        );
      }
    }
    else {
      $data['auth'] = array(
        'kode' => '0',
        'message' => 'Access Forbidden !',
      );
    }
  }

  echo json_encode($data);
