<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../path.php';
require_once (ABSPATH . 'config/config.php');
require_once (ABSPATH . 'config/database.php');
require_once (ABSPATH . 'vendor/autoload.php');
$url = BASE_URL;
header("Access-Control-Allow-Origin: $url");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Connection: Close");
$dataRoute = array(
  sha1('dataKuis'), sha1('dataSoal'), sha1('dataWithDraw'), sha1('dataVoucher'), sha1('dataUser')
);
if (isset($_SESSION['is_logged'])) {
  if (isset($_GET['f']) && isset($_GET['d'])) {
    $route = base64_decode($_GET['f']);
    $uuid = base64_decode($_GET['d']);
    $requestData = $_REQUEST;
    $data = array();
    if ($route == NULL && $uuid == NULL) {
      echo json_encode(['error' => 'Invalid URL']);
    }
    // data kuis
    elseif ($route == $dataRoute[0] && $uuid == "tabelKuis") {
      $columns = array(
        '0' => 'tb_kuis.id_kuis',
        '1' => 'tb_kuis.judul',
        '2' => 'tb_users.username',
        '3' => 'tb_kuis.jumlah_soal',
        '4' => 'tb_kuis.durasi',
        '5' => 'tb_kuis.status'
      );
      // getting total number records without any search
      $sql = "SELECT tb_kuis.*, tb_users.id_user, tb_users.username FROM tb_kuis INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user";
      $query=mysqli_query($link, $sql) or die("error");
      $totalData = mysqli_num_rows($query);
      $totalFiltered = $totalData;
      if( !empty($requestData['search']['value']) ) {
        // if there is a search parameter
        $sql = "SELECT tb_kuis.*, tb_users.id_user, tb_users.username FROM tb_kuis INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user";
        $sql.=" WHERE tb_kuis.judul LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR tb_users.username LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR tb_kuis.jumlah_soal LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR tb_kuis.status LIKE '%".$requestData['search']['value']."%' ";
        $query=mysqli_query($link, $sql) or die("error");
        $totalFiltered = mysqli_num_rows($query);

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        $query=mysqli_query($link, $sql) or die("error"); // again run query with limit

      }
      else {

        $sql = "SELECT tb_kuis.*, tb_users.id_user, tb_users.username FROM tb_kuis INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user";
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        $query=mysqli_query($link, $sql) or die("employee-grid-data.php: get employees");

      }
      $i = 1;
      $data = array();
      while( $row=mysqli_fetch_array($query) ) {
        $status = base64_encode('aktivasiKuis');
        $detail = base64_encode('detailKuis');
        $edit = base64_encode('editKuis');
        $delete = base64_encode('hapusKuis');
        $nestedData=array();
        $btnStatus = "";
        if ($row['status'] == 0) {
          $btnStatus .= '<a id="aktivasi" class="btn btn-xs btn-warning" title="Ubah Status Kuis" data-kuis="'.$row['id_kuis'].'" data-status="'.$row['status'].'" data-dest="'.$status.'">
                          <i class="fa fa-refresh"></i>
                          <span>Aktifkan Kuis</span>
                        </a>';
        } else {
          $btnStatus .= '<a id="aktivasi" class="btn btn-xs btn-primary" title="Ubah Status Kuis" data-kuis="'.$row['id_kuis'].'" data-status="'.$row['status'].'" data-dest="'.$status.'">
                          <i class="fa fa-refresh"></i>
                          <span>Nonaktifkan Kuis</span>
                        </a>';
        }
        $nestedData[] = $i++.".";
        $nestedData[] = $row["judul"];
        $nestedData[] = $row["username"];
        $nestedData[] = $row["jumlah_soal"];
        $nestedData[] = $row["durasi"];
        $nestedData[] = $btnStatus;
        $nestedData[] =
          '<a id="edit" name="edit" class="btn btn-xs btn-warning" title="Edit Data" data-kuis="'.$row['id_kuis'].'" data-target="'.$edit.'">
            <i class="fa fa-edit"></i>
            <span>Edit</span>
          </a>'."&nbsp".
          '<a id="detail" name="detail" class="btn btn-xs btn-info" title="Detail Data" data-kuis="'.$row['id_kuis'].'" data-target="'.$detail.'">
            <i class="fa fa-list"></i>
            <span>Detail</span>
          </a>'."&nbsp".
          '<a id="hapus" name="hapus" class="btn btn-xs btn-danger" title="Hapus Data" title-kuis="'.$row['judul'].'" data-kuis="'.$row['id_kuis'].'" data-target="'.$delete.'">
            <i class="fa fa-trash"></i>
            <span>Hapus</span>
          </a>';
        $data[] = $nestedData;
      }

      $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $data
            );
      echo json_encode($json_data);
    }
    elseif ($route == $dataRoute[0] && $uuid == "aktivasiKuis" && isset($_GET['idKuis']) && isset($_GET['s'])) {
      $id = $_GET['idKuis'];
      $stts = $_GET['s'];
      $sqlKuis = "SELECT * FROM tb_kuis WHERE id_kuis='$id' LIMIT 1";
      $exec = mysqli_query($link,$sqlKuis);
      if ($exec) {
        $k = mysqli_fetch_assoc($exec);
        if ($stts == 0) {
          // check jumlah soal
          $sqlSoal = mysqli_query($link,"SELECT id_kuis, judul_soal FROM tb_soal WHERE id_kuis='$id'");
          if (mysqli_num_rows($sqlSoal) >= $k['jumlah_soal']) {
            $sqlToken = "SELECT tb_token.id_user, tb_token.firebase_token, tb_users.id_user, tb_users.name, tb_level.* FROM tb_token
                         INNER JOIN tb_users ON tb_users.id_user = tb_token.id_user
                         INNER JOIN tb_level ON tb_level.id_level = tb_users.id_level
                         WHERE tb_level.nama_level='user'";
            $exec = mysqli_query($link,$sqlToken);
            while ($r = mysqli_fetch_assoc($exec)) {
              if ($r['firebase_token'] != NULL) {
                $message['data'] = array(
                  'tipe' => 'kuisTerbaru',
                  'subtitle' => 'Kuis Terbaru',
                  'title' => 'Hi,' . $r['name'],
                  'message' => 'Ada kuis baru nih ' . $k['judul'],
                  'image' => $url . '/assets/img/kuis/' . $k['cover'],
                  'namaKuis' => $k['judul'],
                  'slug' => $k['slug']
                );
                $devicetoken = $r['firebase_token'];
                $fields = array(
                  'to' => $devicetoken,
                  'data' => $message
                );
                $headers = array(
                	'Authorization: key='.FIREBASE_API_KEY,
                	'Content-Type: application/json'
                );
                $urlFcm = 'https://fcm.googleapis.com/fcm/send';
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL,$urlFcm);
                curl_setopt( $ch,CURLOPT_POST,true);
                curl_setopt( $ch,CURLOPT_HTTPHEADER,$headers);
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER,true);
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER,false);
                curl_setopt( $ch,CURLOPT_POSTFIELDS,json_encode($fields));
                curl_exec($ch);
                if (curl_error($ch)) {
                  $error_msg = curl_error($ch);
                }
                else {
                  $data['kuis'] = array(
                    'kode' => '1',
                    'message' => 'Kuis telah diaktifkan'
                  );
                }
                curl_close($ch);
                if (isset($error_msg)) {
                  $data['kuis'] = array(
                    'kode' => '0',
                    'message' => $error_msg
                  );
                }
              }
              else {
                $data['kuis'] = array(
                  'kode' => '1',
                  'message' => 'Kuis telah diaktifkan tanpa notifikasi'
                );
              }
            }
            mysqli_query($link,"UPDATE tb_kuis SET status='1' WHERE id_kuis='$id'");
          }
          else {
            $data['kuis'] = array(
              'kode' => '0',
              'message' => 'Jumlah soal kurang !'
            );
          }
        }
        else {
          mysqli_query($link,"UPDATE tb_kuis SET status='0' WHERE id_kuis='$id'");
          $data['kuis'] = array(
            'kode' => '1',
            'message' => 'Kuis berhasil dinonaktifkan !'
          );
        }
      }
      else {
        $data['kuis'] = array(
          'kode' => '0',
          'message' => mysqli_error($link)
        );
      }
      echo json_encode($data);
    }
    // data soal
    elseif ($route == $dataRoute[1] && $uuid == "tabelSoal") {
      // code...
    }
    // data with draw
    elseif ($route == $dataRoute[2] && $uuid == "tabelWithDraw") {
      // code...
    }
    // data voucher
    elseif ($route == $dataRoute[3] && $uuid == "tabelVoucher") {
      $columns = array(
        '0' => 'id_voucher',
        '1' => 'kode_voucher',
        '2' => 'jumlah',
        '3' => 'tb_users.status'
      );
      // getting total number records without any search
      $sql = "SELECT * FROM tb_voucher";
      $query=mysqli_query($link, $sql) or die("error");
      $totalData = mysqli_num_rows($query);
      $totalFiltered = $totalData;
      if( !empty($requestData['search']['value']) ) {
        // if there is a search parameter
        $sql = "SELECT * FROM tb_users";
        $sql.=" WHERE kode_voucher LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR jumlah LIKE '%".$requestData['search']['value']."%' ";
        $query=mysqli_query($link, $sql) or die("error");
        $totalFiltered = mysqli_num_rows($query);

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        $query=mysqli_query($link, $sql) or die("error"); // again run query with limit

      }
      else {

        $sql = "SELECT * FROM tb_voucher";
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        $query=mysqli_query($link, $sql) or die("employee-grid-data.php: get employees");

      }
      $i = 1;
      $data = array();
      while( $row=mysqli_fetch_array($query) ) {

        $nestedData=array();
        $nestedData[] = $i++.".";
        $nestedData[] = $row["kode_voucher"];
        $nestedData[] = $row["jumlah"];
        $nestedData[] = $row["status"] == "1" ? '<label class="label label-success">Sudah digunakan</label>' : '<label class="label label-warning">Belum digunakan</label>';
        $data[] = $nestedData;
      }

      $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $data
            );
      echo json_encode($json_data);
    }
    // data user
    elseif ($route == $dataRoute[4] && $uuid == "tabelUser") {
      $columns = array(
        '0' => 'tb_users.id_user',
        '1' => 'tb_level.nama_level',
        '2' => 'tb_users.name',
        '3' => 'tb_users.email',
        '4' => 'tb_users.status'
      );
      // getting total number records without any search
      $sql = "SELECT tb_users.*, tb_level.* FROM tb_users";
      $sql.= " INNER JOIN tb_level ON tb_level.id_level = tb_users.id_level";
      $query=mysqli_query($link, $sql) or die("error");
      $totalData = mysqli_num_rows($query);
      $totalFiltered = $totalData;
      if( !empty($requestData['search']['value']) ) {
        // if there is a search parameter
        $sql = "SELECT tb_users.*, tb_level.* FROM tb_users";
        $sql.= " INNER JOIN tb_level ON tb_level.id_level = tb_users.id_level";
        $sql.=" WHERE tb_users.name LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR tb_users.status LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR tb_users.email LIKE '%".$requestData['search']['value']."%' ";
        $query=mysqli_query($link, $sql) or die("error");
        $totalFiltered = mysqli_num_rows($query);

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        $query=mysqli_query($link, $sql) or die("error"); // again run query with limit

      }
      else {

        $sql = "SELECT tb_users.*, tb_level.* FROM tb_users";
        $sql.= " INNER JOIN tb_level ON tb_level.id_level = tb_users.id_level";
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        $query=mysqli_query($link, $sql) or die("employee-grid-data.php: get employees");

      }
      $i = 1;
      $data = array();
      while( $row=mysqli_fetch_array($query) ) {  // preparing an array
        $detail = base64_encode('detailUser');
        $edit = base64_encode('editUser');
        $delete = base64_encode('hapusUser');
        $reset = base64_encode('resetUser');

        $nestedData=array();
        $nestedData[] = $i++.".";
        $nestedData[] = ucwords($row["nama_level"]);
        $nestedData[] = $row["name"];
        $nestedData[] = $row["email"];
        $nestedData[] = $row["ip_addr"];
        $nestedData[] = $row["status"] == "2" ? '<label class="label label-success">Aktif</label>' : ($row["status"] == "1" ? '<label class="label label-warning">Tidak Aktif</label>' : '<label class="label label-danger">Suspend</label>');
        $nestedData[] =
          '<a id="edit" name="edit" class="btn btn-xs btn-warning" title="Edit Data" data-user="'.$row['id_user'].'" data-target="'.$edit.'">
            <i class="fa fa-edit"></i>
            <span>Edit</span>
          </a>'."&nbsp".
          '<a id="detail" name="detail" class="btn btn-xs btn-info" title="Detail Data" data-user="'.$row['id_user'].'" data-target="'.$detail.'">
            <i class="fa fa-list"></i>
            <span>Detail</span>
          </a>'."&nbsp".
          '<a id="reset" name="reset" class="btn btn-xs btn-default" title="Reset Password" title-user="'.$row['name'].'" data-user="'.$row['id_user'].'" data-target="'.$reset.'">
            <i class="fa fa-refresh"></i>
            <span>Reset</span>
          </a>'."&nbsp".
          '<a id="hapus" name="hapus" class="btn btn-xs btn-danger" title="Hapus Data" title-user="'.$row['name'].'" data-user="'.$row['id_user'].'" data-target="'.$delete.'">
            <i class="fa fa-trash"></i>
            <span>Hapus</span>
          </a>';
        $data[] = $nestedData;
      }

      $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $data
            );
      echo json_encode($json_data);
    }
  }
  else {
    echo json_encode(['error' => 'Access Forbidden']);
  }
}
else {
  $url = BASE_URL;
  echo "<script>var url = '$url'; window.location.href= url;</script>";
  exit();
}
