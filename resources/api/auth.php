<?php
  session_start();
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  require_once '../../path.php';
  require_once (ABSPATH . 'config/config.php');
  require_once (ABSPATH . 'config/database.php');
  require_once (ABSPATH . 'vendor/autoload.php');
  $url = BASE_URL;
  $app = APPNAME;
  header("Access-Control-Allow-Origin: $url");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Connection: Close");

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
    base64_encode('reset-password')
  );

  $mods = @$_REQUEST['tipe'] ?? @$_REQUEST['tipe'];
  if ($mods == "Forbidden") {
    $data['auth'] = array(
      'kode' => '0',
      'message' => 'Access Forbidden!',
    );
  }
  else {
    if ($mods == "login") {
      if (isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['email']) && !empty($_POST['password']) && isset($_POST['firebase_token'])) {
        $a = mysqli_real_escape_string($link,strip_tags($_POST['email']));
        $b = mysqli_real_escape_string($link,strip_tags($_POST['password']));
        $tokenFirebase = $_POST['firebase_token'];
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
              $exp = date('Y-m-d H:i:s',strtotime('+7 day'));
              $loginDate = date('Y-m-d H:i:s',strtotime('now'));
              $updateLogin = mysqli_query($link,"UPDATE tb_users SET first_login='$loginDate', ip_addr='$ipaddress' WHERE id_user='$id' LIMIT 1");
              if ($tokenFirebase != NULL) {
                $updateToken = mysqli_query($link,"UPDATE tb_token SET firebase_token='$tokenFirebase', access_token='$token', forget_token=NULL, expried_in='$exp', updated_at='$loginDate' WHERE id_user='$id' LIMIT 1");
              } else {
                $updateToken = mysqli_query($link,"UPDATE tb_token SET access_token='$token', forget_token=NULL, expried_in='$exp', updated_at='$loginDate' WHERE id_user='$id' LIMIT 1");
              }
              $idUser = $r['id_user'];
              $lengkap = '1';
              $sqlUser = mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM tb_profile WHERE id_user='$idUser'"));
              if ($sqlUser['tgl_lahir'] == "" || $sqlUser['jk'] == "" || $sqlUser['no_hp'] == "" || $sqlUser['almt'] == "" ||
                  $sqlUser['nama_bank'] == "" || $sqlUser['no_rek'] == "" || $sqlUser['atas_nama'] == "") {
                $lengkap = '0';
              }
              else {
                $lengkap = '1';
              }
              $notifAkun = array();
              if ($lengkap == 0) {
                $notifAkun =
                $data['auth'] = array(
                  'kode' => '1',
                  'message' => 'Login Sukses !',
                  'user' => array(
                    'nama' => $r['name'],
                    'email' => $r['email'],
                    'level' => $r['nama_level'],
                    'saldo' => number_format($sqlUser['saldo']),
                    'token' => $token,
                  ),
                  'exp' => $exp,
                  'notif' => array(
                                'title' => 'Pemberitahuan',
                                'message' => 'Lengkapi Profil Anda !',
                                'setNotif' => true
                              ),
                );
              }
              else {
                $data['auth'] = array(
                  'kode' => '1',
                  'message' => 'Login Sukses !',
                  'user' => array(
                    'nama' => $r['name'],
                    'email' => $r['email'],
                    'level' => $r['nama_level'],
                    'saldo' => number_format($sqlUser['saldo']),
                    'token' => $token,
                  ),
                  'exp' => $exp,
                  'notif' => array('setNotif' => false),
                );
              }
              $_SESSION['is_logged'] = true;
              $_SESSION['username'] = $r['name'];
              $_SESSION['email'] = $r['email'];
              $_SESSION['level'] = $r['nama_level'];
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
      if (isset($_POST['tipe_user']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) &&
          !empty($_POST['tipe_user']) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $a = mysqli_real_escape_string($link,strip_tags($_POST['tipe_user']));
        $b = mysqli_real_escape_string($link,strip_tags($_POST['name']));
        $c = mysqli_real_escape_string($link,strip_tags($_POST['email']));
        $d = mysqli_real_escape_string($link,strip_tags($_POST['password']));
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
          $getUser = explode("@",$c);
          $pass = hash('sha512',$d);
          $date = date('Y-m-d H:i:s',strtotime('now'));
          $check = mysqli_query($link,"SELECT email FROM tb_users WHERE email='$c'");
          if (mysqli_num_rows($check) == 1) {
            $data['auth'] = array(
              'kode' => '0',
              'message' => 'Email sudah terdaftar. Harap menggunakan email lain',
            );
          }
          else {
            $exp = date('Y-m-d H:i:s',strtotime('+1 day'));
            $body = '
            Thanks for signing up!<br>
            Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.<br>

            ------------------------<br>
            Username: '.$b.'<br>
            ------------------------<br>

            Please click bottom button this link to activate your account: <br>
            <a href="'.$url.'/verification?t='.$tipeUrl[0].'&e='.base64_encode($c).'&_token='.$token.'">Click here !</a><br>or copy link below<br>
            '.$url.'/verification?t='.$tipeUrl[0].'&e='.base64_encode($c).'&_token='.$token.'<br>
            the link expried next day.
            ';
            $mail = new PHPMailer(true);
            try {
              $mail->isSMTP();
              $mail->Host = MAIL_HOST;
              $mail->SMTPAuth = true;
              $mail->Username = MAIL_USERNAME;
              $mail->Password = MAIL_PASSWORD;
              $mail->SMTPSecure = MAIL_ENCRYPTION;
              $mail->Port = MAIL_PORT;

              $mail->setFrom(MAIL_USERNAME, APPNAME);
              $mail->addAddress($c, $b);

              $mail->isHTML(true);
              $mail->Subject = "Verification Account";
              $mail->Body    = $body;
              //$mail->send();
              if ($mail->send()) {
                $sqlUsers = mysqli_query($link,"INSERT INTO tb_users VALUES(NULL,'$a','$b','$getUser[0]','$c','$pass','1','$ipaddress',NULL,NULL,'$date','$date')");
                $result = mysqli_fetch_assoc(mysqli_query($link,"SELECT id_user,email FROM tb_users WHERE email='$c' LIMIT 1"));
                $idUser = $result['id_user'];
                $generateToken = mysqli_query($link,"INSERT INTO tb_token VALUES(NULL,'$idUser',NULL,NULL,'$token',NULL,'$exp','$date','$date')");
                if ($sqlUsers && $generateToken) {
                  $data['auth'] = array(
                    'kode' => '1',
                    'message' => 'Lakukan Aktivasi Akun melalui email. Jika belum mendapatkan email, harap cek di spam !'
                  );
                }
                else {
                  $data['auth'] = array(
                    'kode' => '0',
                    'message' => mysqli_error($link),
                  );
                }
              }
              else {
                $data['auth'] = array(
                  'kode' => '0',
                  'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
                );
              }
            }
            catch(Exception $e) {
              $data['auth'] = array(
                'kode' => '0',
                'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
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
          'message' => 'Tipe/Nama/Email/Password tidak boleh kosong !',
        );
      }
    }
    elseif ($mods == "forget-password") {
      $a = mysqli_real_escape_string($link,strip_tags($_POST['email']));
      if (isset($_POST['email']) && !empty($_POST['email'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
          $date = date('Y-m-d H:i:s',strtotime('now'));
          $checkEmail = mysqli_query($link,"SELECT id_user,email,username FROM tb_users WHERE email='$a' LIMIT 1");
          if (mysqli_num_rows($checkEmail) == 0) {
            $data['auth'] = array(
              'kode' => '0',
              'message' => 'Email tidak terdaftar !'
            );
          }
          else {
            $r = mysqli_fetch_assoc($checkEmail);
            $id = $r['id_user'];
            $name = $r['username'];
            $exp = date('Y-m-d H:i:s',strtotime('+1 day'));
            $body = '
            Hi, '.$name.' !<br>
            You have requested your MAP password to be reset. Please click the following link to change your password:<br>

            Please click bottom button this link to reset password your account:<br>
            <a href="'.$url.'/reset-password?t='.$tipeUrl[1].'&e='.base64_encode($a).'&_token='.$token.'">Click here !</a><br>or copy link below<br>
            '.$url.'/reset-password?t='.$tipeUrl[1].'&e='.base64_encode($a).'&_token='.$token.'<br>
            This link will expire in 1 hour.<br>

            thanks, '.$app.'</br>
            ';
            $mail = new PHPMailer(true);
            try {
              $mail->isSMTP();
              $mail->Host = MAIL_HOST;
              $mail->SMTPAuth = true;
              $mail->Username = MAIL_USERNAME;
              $mail->Password = MAIL_PASSWORD;
              $mail->SMTPSecure = MAIL_ENCRYPTION;
              $mail->Port = MAIL_PORT;

              $mail->setFrom(MAIL_USERNAME, APPNAME);
              $mail->addAddress($a, $name);

              $mail->isHTML(true);
              $mail->Subject = "Reset Password";
              $mail->Body    = $body;
              //$mail->send();
              if ($mail->send()) {
                $sql = mysqli_query($link,"UPDATE tb_token SET access_token=NULL, forget_token='$token', expried_in='$exp', updated_at='$date' WHERE id_user='$id'");
                if ($sql) {
                  $data['auth'] = array(
                    'kode' => '1',
                    'message' => 'Lakukan reset password melalui email yang dikirim !'
                  );
                }
                else {
                  $data['auth'] = array(
                    'kode' => '0',
                    'message' => mysqli_error($link),
                  );
                }
              }
              else {
                $data['auth'] = array(
                  'kode' => '0',
                  'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
                );
              }
            }
            catch(Exception $e) {
              $data['auth'] = array(
                'kode' => '0',
                'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
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
    elseif ($mods == "reset-password") {
      $a = mysqli_real_escape_string($link,strip_tags($_POST['email']));
      $b = mysqli_real_escape_string($link,strip_tags($_POST['password']));
      if (isset($_POST['email']) && isset($_POST['password']) &&
          !empty($_POST['email']) && !empty($_POST['password'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
          $date = date('Y-m-d H:i:s',strtotime('now'));
          $pass = hash('sha512', $b);
          $sql = mysqli_query($link,"UPDATE tb_users SET password='$pass', updated_at='$date' WHERE email='$a'");
          if ($sql) {
            $sqlUser = mysqli_fetch_assoc(mysqli_query($link,"SELECT id_user,email FROM tb_users WHERE email='$a'"));
            $id = $sqlUser['id_user'];
            $sqlUpdate = mysqli_query($link,"UPDATE tb_token SET forget_token=NULL, expried_in=NULL WHERE id_user='$id'");
            $data['auth'] = array(
              'kode' => '1',
              'message' => 'Berhasil melakukan Reset password !'
            );
          }
          else {
            $data['auth'] = array(
              'kode' => '0',
              'message' => 'Gagal dalam melakukan reset password !'
            );
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
          'message' => 'Password tidak boleh kosong !',
        );
      }
    }
    elseif ($mods == "logout") {
      $token = $_POST['_token'];
      $date = date('Y-m-d H:i:s',strtotime('now'));
      $r = mysqli_fetch_assoc(mysqli_query($link,"SELECT id_user,access_token FROM tb_token WHERE access_token='$token'"));
      $id = $r['id_user'];
      mysqli_query($link,"UPDATE tb_users SET last_login='$date' WHERE id_user='$id'");
      $sql = mysqli_query($link,"UPDATE tb_token SET updated_at='$date', firebase_token=NULL, access_token=NULL, expried_in=NULL WHERE access_token='$token'");
      $data['auth'] = array(
        'kode' => '1',
        'message' => 'Logout',
        'setLogin' => true,
      );
    }
    else {
      $data['auth'] = array(
        'kode' => '0',
        'message' => 'Access Forbidden !',
      );
    }
  }

  echo json_encode($data);
