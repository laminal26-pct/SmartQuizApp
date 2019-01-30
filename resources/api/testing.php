<?php
require_once '../../path.php';
require_once (ABSPATH . 'config/config.php');
require_once (ABSPATH . 'config/database.php');
require_once 'firebase.php';
require_once 'push.php';

if(isset($_POST['title']) && isset($_POST['message']) && isset($_POST['image']) && isset($_POST['email'])){
  $email = $_POST['email'];
  $token = mysqli_fetch_assoc(mysqli_query($link,"SELECT tb_token.id_user, tb_token.firebase_token, tb_users.id_user, tb_users.email FROM tb_users
  INNER JOIN tb_token ON tb_token.id_user = tb_users.id_user WHERE tb_users.email='$email' LIMIT 1"));

  $message['data'] = array(
    'tipe' => 'kuisTerbaru',
    'subtitle' => 'Kuis Terbaru',
    'title' => $_POST['title'],
    'message' => $_POST['message'],
    'image' => $_POST['image'],
    'namaKuis' => 'Soal Apo Ye',
    'slug' => '4f9f3f0040e3f5e9c1261d346162495b2e5219699ca0ed5af3a8556e1a72a01adfdc9b485304326feb2860919e2eff3dff20fdaf99f66f6f6b68c2f5e3ac637f'
  );
  $devicetoken = $token['firebase_token'];
  $fields = array(
    'to' => $devicetoken,
    'data' => $message
  );
  $headers = array(
  	'Authorization: key='.FIREBASE_API_KEY,
  	'Content-Type: application/json'
  );
  $url = 'https://fcm.googleapis.com/fcm/send';
  $ch = curl_init();
  curl_setopt( $ch,CURLOPT_URL,$url);
  curl_setopt( $ch,CURLOPT_POST,true);
  curl_setopt( $ch,CURLOPT_HTTPHEADER,$headers);
  curl_setopt( $ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER,false);
  curl_setopt( $ch,CURLOPT_POSTFIELDS,json_encode($fields));
  $result = curl_exec($ch);
  if (curl_error($ch)) {
    $error_msg = curl_error($ch);
  }
  curl_close($ch);
  if (isset($error_msg)) {
    echo $error_msg;
  }
  echo $result;
 }else{
 $response['error']=true;
 $response['message']='Parameters missing';
 }
