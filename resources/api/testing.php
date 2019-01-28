<?php
require_once '../../path.php';
require_once (ABSPATH . 'config/config.php');
require_once (ABSPATH . 'config/database.php');
require_once 'firebase.php';
require_once 'push.php';

if(isset($_POST['title']) and isset($_POST['message']) and isset($_POST['email'])){
  $email = $_POST['email'];
   //creating a new push
   $push = null;
   //first check if the push has an image with it
   if(isset($_POST['image'])){
   $message['data'] = array(
  	'title'     => $_POST['title'],
  	'message'      => $_POST['message'],
    'image' => $_POST['image'],
  );
   }else{
   //if the push don't have an image give null in place of image
   $message['data'] = array(
  	'title'     => $_POST['title'],
  	'message'      => $_POST['message'],
    );
   }

 //getting the push from push object
 //$mPushNotification = $push->getPush();

 //getting the token from database object
 $token = mysqli_fetch_assoc(mysqli_query($link,"SELECT tb_token.id_user, tb_token.firebase_token, tb_users.id_user, tb_users.email FROM tb_users
 INNER JOIN tb_token ON tb_token.id_user = tb_users.id_user WHERE tb_users.email='$email' LIMIT 1"));

 //$devicetoken = $db->getTokenByEmail($_POST['email']);
 $devicetoken = $token['firebase_token'];
 //creating firebase class object
 $firebase = new Firebase();
   $fields = array(
  	'to' => $devicetoken,
  	'data'             => $message
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
  curl_close($ch);
  echo $result;
 //sending push notification and displaying result
 //echo $firebase->send($devicetoken, $mPushNotification);
 }else{
 $response['error']=true;
 $response['message']='Parameters missing';
 }
