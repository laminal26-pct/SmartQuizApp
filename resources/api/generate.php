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

  $data = array();
  /*
  $nip = "123456789098765432";
  $nama = array(
    'Adi','Andi','Yuyun','Karmila','Nobita','Pasha Ungu','Ariel Tatum','M. Pur','Ariel Tatum'
  );
  $database = array(
    'ariel_peterpan','ariel_tatum','arieltatum_32','karmila'
  );
  for ($i=0; $i < count($nama); $i++) {
    $username = str_replace(array(' ','.'),array('_',''),strtolower($nama[4]));
    if (strlen($username) == 3) {
      $las = substr($nip,strlen($nip)-4,4);
      $urname = $username."_".$las;
    }
    else if (strlen($username) == 4) {
      $las = substr($nip,strlen($nip)-3,3);
      $urname = $username."_".$las;
    }
    else if (strlen($username) == 5) {
      $las = substr($nip,strlen($nip)-2,2);
      $urname = $username."_".$las;
    }
    elseif (strlen($username) == 6) {
      $las = substr($nip,strlen($nip)-2,2);
      $urname = $username."".$las;
    }
    elseif (strlen($username) == 7) {
      $las = substr($nip,strlen($nip)-1,1);
      $urname = $username."".$las;
    }
    else {

      $a = array();
      $b = "";
      $las = "";
      $urname = "";
      foreach ($database as $key => $value) {
        $a[] = $value;
        if ($username == $value) {
          $b = "Username telah digunakan";
          $userbaru = str_replace(' ','',strtolower($nama[4]));
          if (strlen($userbaru) == 3) {
            $las = substr($nip,strlen($nip)-4,4);
            $urname = $userbaru."_".$las;
          }
          else if (strlen($userbaru) == 4) {
            $las = substr($nip,strlen($nip)-3,3);
            $urname = $userbaru."_".$las;
          }
          else if (strlen($userbaru) == 5) {
            $las = substr($nip,strlen($nip)-2,2);
            $urname = $userbaru."_".$las;
          }
          elseif (strlen($userbaru) == 6) {
            $las = substr($nip,strlen($nip)-2,2);
            $urname = $userbaru."".$las;
          }
          elseif (strlen($userbaru) == 7) {
            $las = substr($nip,strlen($nip)-1,1);
            $urname = $userbaru."".$las;
          }
          elseif (strlen($userbaru) == 8) {
            $las = substr($nip,strlen($nip)-1,1);
            $urname = $userbaru."".$las;
          }
          else {
            $las = substr($nip,strlen($nip)-8,2);
            $urname = $userbaru."_".$las;
          }
          //$las = "";
          //$urname = $username;
        }
      }
    //}
    $data['testing'][] = array(
      'check' => $b,
      'nama' => $nama[4],
      'username' => $urname,
      'las' => $las,
      'database' => $a,
    );
  }*/

  function acak_kode($panjang) {
    $k = "1234567890";
    $string = "";
    for ($i=0; $i < $panjang; $i++) {
      $pos = rand(0,strlen($k) - 1);
      $string .= $k{$pos};
    }
    return $string;
  }

  function acak_jawaban($panjang) {
    $k = "ABCDE";
    $string = "";
    for ($i=0; $i < $panjang; $i++) {
      $pos = rand(0,strlen($k) - 1);
      $string .= $k{$pos};
    }
    return $string;
  }

  if (isset($_GET['username']) && isset($_GET['password']) && isset($_GET['tipe'])) {
    $namo  = $_GET['username'];
    $sandi = hash('sha256',$_GET['password']);
    if ($namo == "root" && $sandi == hash('sha256', 'toor')) {
      if ($_GET['tipe'] == "generateKuis" && isset($_GET['awal']) && isset($_GET['akhir'])) {
        $awal = $_GET['awal'];
        $akhir = $_GET['akhir'];
        $soal1 = array();

        $namasoal = array(
          'UTS','UAS','LATIHAN','SBMPTN','SNMPTN'
        );

        $jmlhSoal = array(
          '5','10'
        );

        $durasi = array(
          '5','10','30','45','60','90'
        );

        $harga = array(
          '0','2500','5000','10000','20000','25000','50000','100000'
        );

        $idRating = array(
          '6', '7'
        );

        $rating = array(
          '1','2','3','4','5'
        );
        $idUser = array(
          '4','5'
        );
        for ($i=$awal; $i <= $akhir; $i++) {
          $ax = array_rand($namasoal);
          $bx = array_rand($jmlhSoal);
          $cx = array_rand($durasi);
          $dx = array_rand($harga);
          $ex = array_rand($idRating);
          $fx = array_rand($rating);
          $gx = array_rand($idUser);
          $idU = $idUser[$gx];
          $judul = "Soal " . $namasoal[$ax] . " Fake " . $i;
          $slug = hash('sha256', $judul . time());
          $idKategori = rand(1,12);
          $idMapel = rand(1,17);
          $soal = $jmlhSoal[$bx];
          $wktu = $durasi[$cx];
          $hrg = $harga[$dx];
          $ack = rand(0,1);
          $bhs = rand(0,1);
          $dsc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris nec laoreet nulla, vel scelerisque mauris. Etiam facilisis venenatis est sit amet tempus. Fusce imperdiet vestibulum mauris, at euismod mi blandit id. Phasellus tempus luctus nibh id venenatis. Nunc finibus, erat vitae blandit consectetur, ipsum mauris egestas arcu, ultrices lacinia nunc ante nec lacus. In hac habitasse platea dictumst. Nulla suscipit, ipsum sed varius convallis, dolor ante malesuada ligula, at feugiat magna quam accumsan ligula. Pellentesque ut rutrum libero. Praesent laoreet fringilla nisi in semper.";
          $foto = 'default.png';
          $stts = rand(0,1);
          $date = date('Y-m-d H:i:s',strtotime('now'));
          $sqlKuis = mysqli_query($link,"INSERT INTO tb_kuis VALUES(NULL,'$idU','$idKategori','$idMapel','$judul','$slug','$soal','$wktu','$hrg','$ack','$bhs','$dsc','$foto','$stts','$date','$date')");
          $sqlRating = mysqli_query($link,"INSERT INTO tb_rating VALUES(NULL,'$i','$idRating[$ex]','$rating[$fx]')");
          for ($j=1; $j <= $soal; $j++) {
            $judulSoal = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris nec laoreet nulla, vel scelerisque mauris. Etiam facilisis venenatis est sit amet tempus. Fusce imperdiet vestibulum mauris, at euismod mi blandit id. Phasellus tempus luctus nibh id venenatis. Nunc finibus, erat vitae blandit consectetur, ipsum mauris egestas arcu, ultrices lacinia nunc ante nec lacus. In hac habitasse platea dictumst. Nulla suscipit, ipsum sed varius convallis, dolor ante malesuada ligula, at feugiat magna quam accumsan ligula. Pellentesque ut rutrum libero. Praesent laoreet fringilla nisi in semper.";
            $pilihan = array('Pilihan A','Pilihan B','Pilihan C','Pilihan D','Pilihan E');
            $pilihan1 = $pilihan[0];
            $pilihan2 = $pilihan[1];
            $pilihan3 = $pilihan[2];
            $pilihan4 = $pilihan[3];
            $pilihan5 = $pilihan[4];
            $jawaban = acak_jawaban(1);
            $pmbhs = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris nec laoreet nulla, vel scelerisque mauris. Etiam facilisis venenatis est sit amet tempus. Fusce imperdiet vestibulum mauris, at euismod mi blandit id. Phasellus tempus luctus nibh id venenatis. Nunc finibus, erat vitae blandit consectetur, ipsum mauris egestas arcu, ultrices lacinia nunc ante nec lacus. In hac habitasse platea dictumst. Nulla suscipit, ipsum sed varius convallis, dolor ante malesuada ligula, at feugiat magna quam accumsan ligula. Pellentesque ut rutrum libero. Praesent laoreet fringilla nisi in semper.";
            $sqlSoal = mysqli_query($link,"INSERT INTO tb_soal VALUES(NULL,'$i','$judulSoal','$pilihan1','$pilihan2','$pilihan3','$pilihan4','$pilihan5','$jawaban','$pmbhs')");
          }
        }
      }
      else if ($_GET['tipe'] == "generateVoucher") {
        $saldo = array(
          '1000','2000','5000','10000','20000','25000','50000','100000'
        );
        for ($i=9; $i <= 1008; $i++) {
          $ax = array_rand($saldo);
          //$ax = $saldo[$i];
          $kode = acak_kode(16);
          //$kode = "000000000000000".$i;
          //mysqli_query($link,"INSERT INTO tb_voucher VALUES(NULL,'$kode','$saldo[$ax]','0')");
        }
      }
      else if ($_GET['tipe'] == "kategori") {
        $kategori = array(
          'SD Kelas 1', 'SD Kelas 2', 'SD Kelas 3', 'SD Kelas 4', 'SD Kelas 5', 'SD Kelas 6',
          'SMP Kelas 7', 'SMP Kelas 8', 'SMP Kelas 9', 'SMA Kelas 10', 'SMA Kelas 11', 'SMA Kelas 12',
        );
        $i = 1;
        foreach ($kategori as $key => $value) {
          $img = "kls-".$i++.".png";
          //mysqli_query($link,"INSERT INTO tb_kategori VALUES(NULL,'$value','$img')");
        }
      }
      else if ($_GET['tipe'] == "listkuis") {
        
      }
      else {
        $data = "Jangan Galak Baseng Yee ?";
      }
    }
    else {
      $data = "Nak Ngapo Yee ?";
    }
  }
  else {
    $data = "Etts...";
  }

  echo json_encode($data);
