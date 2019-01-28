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
    if ($route == NULL && $uuid == NULL) {
      echo json_encode(['error' => 'Invalid URL']);
    }
    // data kuis
    elseif ($route == $dataRoute[0] && $uuid == "tabelKuis") {
      // code...
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
      // code...
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
