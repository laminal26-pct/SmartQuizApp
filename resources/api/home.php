<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
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
  $sqlKuis = "SELECT tb_kuis.id_kuis as idKuis, tb_kuis.*, tb_rating.*, tb_users.id_user, tb_users.username, tb_kategori.id_kategori, tb_kategori.nama_kategori, tb_mapel.* FROM tb_kuis
              LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
              INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user
              INNER JOIN tb_kategori ON tb_kategori.id_kategori = tb_kuis.id_kategori
              INNER JOIN tb_mapel ON tb_mapel.id_mapel = tb_kuis.id_mapel
              WHERE tb_kuis.status='1'";
  $sqlProfile = "SELECT tb_level.*, tb_users.id_user, tb_users.id_level, tb_users.username, tb_users.email, tb_users.password,
                 tb_profile.id_profil, tb_profile.id_user, tb_profile.nama, tb_profile.tgl_lahir, tb_profile.jk, tb_profile.no_hp,
                 tb_profile.almt, tb_profile.saldo, tb_profile.poin, tb_profile.nama_bank, tb_profile.no_rek, tb_profile.atas_nama
                 FROM tb_users
                 INNER JOIN tb_level ON tb_level.id_level = tb_users.id_level
                 INNER JOIN tb_profile ON tb_profile.id_user = tb_users.id_user";

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
          // home
          elseif ($route == "home" && $uuid == "fetchAll") {
            $sqlProfile .= " WHERE tb_users.id_user='$id' LIMIT 1";
            $saldo = mysqli_fetch_assoc(mysqli_query($link,$sqlProfile));
            $kategori = mysqli_query($link,"SELECT * FROM tb_kategori");
            if (mysqli_num_rows($kategori) > 0) {
              $data['home'] = array(
                'kode' => '1',
                'message' => 'Fetch All Data',
                'saldo' => number_format($saldo['saldo']),
              );
              while ($k = mysqli_fetch_assoc($kategori)) {
                $data['home']['kategori'][] = array(
                  'id' => $k['id_kategori'],
                  'title' => $k['nama_kategori'],
                  'icon' => BASE_URL . '/assets/img/icon/' . $k['img']
                );
              }
              $jumlahKuis = mysqli_query($link,"SELECT * FROM tb_kuis WHERE status='1'");

              if (mysqli_num_rows($jumlahKuis) > 25) {
                $home = array('Event Terkini','Kuis Terbaru','Pilihan Editor','Kuis Terlaris','Kuis Populer');

                foreach ($home as $key => $value) {
                  $tampilKuis = rand(5,10);
                  $jmlh = mysqli_num_rows(mysqli_query($link,"SELECT * FROM tb_kuis WHERE status='1'"));
                  //echo $jmlh;
                  if ($key == 0) {
                    $acakKuis = $jmlh > 400 ? rand(1,400) : rand(1,$jmlh);
                    $sqlKuis = "SELECT tb_kuis.*, tb_rating.*, tb_users.id_user, tb_users.username, tb_kategori.id_kategori, tb_kategori.nama_kategori, tb_mapel.* FROM tb_kuis
                                LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
                                INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user
                                INNER JOIN tb_kategori ON tb_kategori.id_kategori = tb_kuis.id_kategori
                                INNER JOIN tb_mapel ON tb_mapel.id_mapel = tb_kuis.id_mapel
                                WHERE tb_kuis.status='1' OR tb_kuis.judul LIKE '%$acakKuis%' ORDER BY RAND() limit 1,$tampilKuis";
                  }
                  elseif ($key == 1) {
                    $acakKuis = $jmlh > 400 && $jmlh < 800 ? rand(401,800) : rand(1,$jmlh);
                    $sqlKuis = "SELECT tb_kuis.*, tb_rating.*, tb_users.id_user, tb_users.username, tb_kategori.id_kategori, tb_kategori.nama_kategori, tb_mapel.* FROM tb_kuis
                                LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
                                INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user
                                INNER JOIN tb_kategori ON tb_kategori.id_kategori = tb_kuis.id_kategori
                                INNER JOIN tb_mapel ON tb_mapel.id_mapel = tb_kuis.id_mapel
                                WHERE tb_kuis.status='1' OR tb_kuis.judul LIKE '%$acakKuis%' ORDER BY RAND() limit 1,$tampilKuis";
                  }
                  elseif ($key == 2) {
                    $acakKuis = $jmlh > 800 && $jmlh < 1200 ? rand(801,1200) : rand(1,$jmlh);
                    $sqlKuis = "SELECT tb_kuis.*, tb_rating.*, tb_users.id_user, tb_users.username, tb_kategori.id_kategori, tb_kategori.nama_kategori, tb_mapel.* FROM tb_kuis
                                LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
                                INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user
                                INNER JOIN tb_kategori ON tb_kategori.id_kategori = tb_kuis.id_kategori
                                INNER JOIN tb_mapel ON tb_mapel.id_mapel = tb_kuis.id_mapel
                                WHERE tb_kuis.status='1' OR tb_kuis.judul LIKE '%$acakKuis%' ORDER BY RAND() limit 1,$tampilKuis";
                  }
                  elseif ($key == 3) {
                    $acakKuis = $jmlh > 1201 && $jmlh < 1600 ? rand(1201,1600) : rand(1,$jmlh);
                    $sqlKuis = "SELECT tb_kuis.*, tb_rating.*, tb_users.id_user, tb_users.username, tb_kategori.id_kategori, tb_kategori.nama_kategori, tb_mapel.* FROM tb_kuis
                                LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
                                INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user
                                INNER JOIN tb_kategori ON tb_kategori.id_kategori = tb_kuis.id_kategori
                                INNER JOIN tb_mapel ON tb_mapel.id_mapel = tb_kuis.id_mapel
                                WHERE tb_kuis.status='1' OR tb_kuis.judul LIKE '%$acakKuis%' ORDER BY RAND() limit 1,$tampilKuis";
                  }
                  elseif ($key == 4) {
                    $acakKuis = $jmlh > 1600 ? rand(1601,$jmlh) : rand(1,$jmlh);
                    $sqlKuis = "SELECT tb_kuis.*, tb_rating.*, tb_users.id_user, tb_users.username, tb_kategori.id_kategori, tb_kategori.nama_kategori, tb_mapel.* FROM tb_kuis
                                LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
                                INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user
                                INNER JOIN tb_kategori ON tb_kategori.id_kategori = tb_kuis.id_kategori
                                INNER JOIN tb_mapel ON tb_mapel.id_mapel = tb_kuis.id_mapel
                                WHERE tb_kuis.status='1' OR tb_kuis.judul LIKE '%$acakKuis%' ORDER BY RAND() limit 1,$tampilKuis";
                  }

                  $kuis = mysqli_query($link,$sqlKuis);
                  if (mysqli_num_rows($kuis) > 0) {
                    $fetchKuis = array();
                    while ($r = mysqli_fetch_assoc($kuis)) {
                      $fetchKuis[] = array(
                        'author' => $r['username'],
                        'judul' => $r['judul'],
                        'slug' => $r['slug'],
                        'kategori' => $r['nama_kategori'],
                        'mapel' => $r['nama_mapel'],
                        'soal' => $r['jumlah_soal'],
                        'durasi' => $r['durasi'],
                        'harga' => $r['harga'] == 0 ? 'Gratis' : 'Rp ' . number_format($r['harga']),
                        'deskripsi' => $r['deskripsi'],
                        'cover' => $url . '/assets/img/kuis/' . $r['cover'],
                        'status' => $r['status'],
                        'rating' => $r['rate'] != NULL ? $r['rate'] : '0'
                      );
                    }
                    $data['home']['pilihan'][] = array(
                      'title' => $value,
                      'jumlah' => mysqli_num_rows($kuis),
                      'kuis' => $fetchKuis
                    );
                  }
                  else {
                    $data['home']['pilihan'] = NULL;
                  }
                  $data['home']['listKuis'] = array();
                }
              }
              elseif (mysqli_num_rows($jumlahKuis) < 25) {
                $jmlh = $jmlh = mysqli_num_rows(mysqli_query($link,"SELECT * FROM tb_kuis WHERE status='1'")) < 0 ? '0' : mysqli_num_rows(mysqli_query($link,"SELECT * FROM tb_kuis WHERE status='1'"));
                $sqlKuis .= " OR tb_kuis.judul ORDER BY RAND() LIMIT 0,$jmlh";
                $kuis = mysqli_query($link,$sqlKuis);
                $listKuis = array();
                while ($r = mysqli_fetch_assoc($kuis)) {
                  $listKuis[] = array(
                    'author' => $r['username'],
                    'judul' => $r['judul'],
                    'slug' => $r['slug'],
                    'kategori' => $r['nama_kategori'],
                    'mapel' => $r['nama_mapel'],
                    'soal' => $r['jumlah_soal'],
                    'durasi' => $r['durasi'],
                    'harga' => $r['harga'] == 0 ? 'Gratis' : 'Rp ' . number_format($r['harga'],'0',',','.'),
                    'deskripsi' => $r['deskripsi'],
                    'cover' => $url . '/assets/img/kuis/' . $r['cover'],
                    'status' => $r['status'],
                    'rating' => $r['rate'] != NULL ? $r['rate'] : '0',
                  );
                }
                $data['home']['pilihan'] = array();
                $data['home']['listKuis'] = $listKuis;
              }
              else {
                $data['home']['pilihan'] = array();
              }
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Data is empty !'
              );
            }
          }
          // kategori
          elseif ($route == "home" && $uuid == "kategori" && isset($_GET['id'])) {
            $id = $_GET['id'];
            $sqlKuis .= " AND tb_kuis.id_kategori='$id' ORDER BY RAND()";
            $kategori = mysqli_query($link,$sqlKuis);
            if (mysqli_num_rows($kategori) > 0) {
              $data['home'] = array(
                'kode' => '1',
                'message' => mysqli_num_rows($kategori) . " Data"
              );
              while ($r = mysqli_fetch_assoc($kategori)) {
                $data['home']['title'] = $r['nama_kategori'];
                $data['home']['kuisKategori'][] = array(
                  'author' => $r['username'],
                  'judul' => $r['judul'],
                  'slug' => $r['slug'],
                  'id_kategori' => $r['id_kategori'],
                  'nm_kategori' => $r['nama_kategori'],
                  'mapel' => $r['nama_mapel'],
                  'soal' => $r['jumlah_soal'],
                  'durasi' => $r['durasi'],
                  'harga' => $r['harga'] == 0 ? 'Gratis' : 'Rp ' . number_format($r['harga'],0,',','.'),
                  'deskripsi' => $r['deskripsi'],
                  'cover' => $url . '/assets/img/kuis/' . $r['cover'],
                  'status' => $r['status'],
                  'rating' => $r['rate'] != NULL ? $r['rate'] : '0',
                );
              }
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Data not found'
              );
            }
          }
          // detail kuis
          elseif ($route == "home" && ($uuid == "detailKuis" || $uuid == "scanBarcode") && isset($_GET['slug'])) {
            $slug = $_GET['slug'];
            $sqlKuis .= "AND tb_kuis.slug='$slug'";
            $detail = mysqli_query($link,$sqlKuis);
            if (mysqli_num_rows($detail) == 1) {
              $r = mysqli_fetch_assoc($detail);
              $data['home']['kode'] = '1';
              $data['home']['kuis'] = array(
                'author' => $r['username'],
                'judul' => $r['judul'],
                'slug' => $r['slug'],
                'soal' => $r['jumlah_soal'],
                'durasi' => $r['durasi'],
                'harga' => $r['harga'] == 0 ? 'Gratis' : 'Rp ' . number_format($r['harga'],'0',',','.'),
                'deskripsi' => $r['deskripsi'],
                'rating' => $r['rate'] != NULL ? $r['rate'] : '0',
                'cover' => $url . '/assets/img/kuis/' . $r['cover']
              );
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Quis not found'
              );
            }
          }
          // cari kuis
          elseif ($route == "home" && $uuid == "cariKuis" && isset($_GET['q'])) {
            $q = $_GET['q'];
            $sqlKuis .= " AND tb_kuis.judul LIKE '%$q%'";
            $cari = mysqli_query($link,$sqlKuis);
            if (mysqli_num_rows($cari) > 0) {
              while ($r = mysqli_fetch_assoc($cari)) {
                $data['home']['kode'] = '1';
                $data['home']['kuisCari'][] = array(
                  'author' => $r['username'],
                  'judul' => $r['judul'],
                  'slug' => $r['slug'],
                  'soal' => $r['jumlah_soal'],
                  'durasi' => $r['durasi'],
                  'harga' => $r['harga'] == 0 ? 'Gratis' : 'Rp ' . number_format($r['harga'],'0',',','.'),
                  'deskripsi' => $r['deskripsi'],
                  'rating' => $r['rate'] != NULL ? $r['rate'] : '0',
                  'cover' => $url . '/assets/img/kuis/' . $r['cover']
                );
              }
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Quis not found'
              );
            }
          }
          // main kuis belum
          elseif ($route == "home" && $uuid == "mainKuis" && isset($_GET['email']) && isset($_GET['slug'])) {
            $email = $_GET['email'];
            $slug = $_GET['slug'];

            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $execProfile = mysqli_query($link,$sqlProfile);
            $p = mysqli_fetch_assoc($execProfile);

            $sqlKuis .= " AND tb_kuis.slug='$slug'";
            $execKuis = mysqli_query($link,$sqlKuis);
            $k = mysqli_fetch_assoc($execKuis);
            $idUserNilai = $p['id_user'];
            $idKuisNilai = $k['id_kuis'];
            $checkSdhMain = mysqli_query($link,"SELECT id_user, id_kuis FROM tb_nilai WHERE id_user='$idUserNilai' AND id_kuis='$idKuisNilai'");
            if (mysqli_num_rows($checkSdhMain) > 0) {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Maaf, Anda sudah ikut kuis ini !'
              );
            }
            else {
              if (mysqli_num_rows($execKuis) == 1) {
                if ($k['harga'] <= $p['saldo']) {
                  $sqlProfile = "SELECT tb_users.id_user, tb_users.id_level, tb_profile.*, tb_level.* FROM tb_users
                  LEFT JOIN tb_profile ON tb_profile.id_user = tb_users.id_user
                  INNER JOIN tb_level ON tb_level.id_level = tb_users.id_level
                  WHERE tb_level.nama_level='admin'";
                  $execAdmin = mysqli_query($link,$sqlProfile);
                  while ($r = mysqli_fetch_assoc($execAdmin)) {
                    $id = $r['id_user'];
                    $price = ($k['harga'] * 0.8) / mysqli_num_rows($execAdmin);
                    mysqli_query($link,"UPDATE tb_profile SET saldo=saldo+$price WHERE id_user='$id'");
                  }
                  $soal = array();
                  $idKuis = $k['idKuis'];
                  $idAuthor = $k['id_user'];
                  $acak = $k['soal_acak'];
                  $jmlh = $k['jumlah_soal'];
                  $sqlSoal = "";
                  if ($acak == 1) {
                    $sqlSoal .= "SELECT * FROM tb_soal WHERE id_kuis='$idKuis' ORDER BY RAND() LIMIT 0,$jmlh";
                  }
                  else {
                    $sqlSoal .= "SELECT * FROM tb_soal WHERE id_kuis='$idKuis' LIMIT 0,$jmlh";
                  }
                  $execSoal = mysqli_query($link,$sqlSoal);
                  $price = $k['harga'] * 0.2;
                  mysqli_query($link,"UPDATE tb_profile SET saldo=saldo+$price WHERE id_user='$idAuthor'");
                  $idUser = $p['id_user'];
                  $price = $k['harga'];
                  mysqli_query($link,"UPDATE tb_profile SET saldo=saldo-$price WHERE id_user='$idUser'");
                  $data['home'] = array(
                    'kode' => '1',
                    'title' => $k['judul'],
                    'message' => 'Saldo Cukup',
                    'saldo' => $p['saldo'] - $k['harga'],
                    'kuis' => array('id_kuis' => $k['idKuis']),
                  );
                }
                else {
                  $data['home'] = array(
                    'kode' => '0',
                    'message' => 'Saldo anda tidak cukup !',
                    'saldo anda' => $p['saldo'],
                    'harga kuis' => $k['harga']
                  );
                }
              }
              else {
                $data['home'] = array(
                  'kode' => '0',
                  'message' => 'Kuis tidak ditemukan !'
                );
              }
            }
          }
          // tampil soal
          elseif ($route == "home" && $uuid == "tampilSoal" && isset($_GET['email']) && isset($_GET['idKuis'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $execProfile = mysqli_query($link,$sqlProfile);
            $p = mysqli_fetch_assoc($execProfile);
            $idUser = $p['id_user'];
            $id = $_GET['idKuis'];
            $sqlKuis .= " AND tb_kuis.id_kuis='$id'";
            $execKuis = mysqli_query($link,$sqlKuis);
            $k = mysqli_fetch_assoc($execKuis);
            $acak = $k['soal_acak'];
            $jmlh = $k['jumlah_soal'];
            $durasi = $k['durasi'];
            $sqlSoal = "";
            if ($acak == 1) {
              $sqlSoal .= "SELECT * FROM tb_soal WHERE id_kuis='$id' ORDER BY RAND() LIMIT 0,$jmlh";
            }
            else {
              $sqlSoal .= "SELECT * FROM tb_soal WHERE id_kuis='$id' LIMIT 0,$jmlh";
            }
            $execSoal = mysqli_query($link,$sqlSoal);
            if (mysqli_num_rows($execSoal) > 0) {
              $soal = array();
              $i = 1;
              while ($s = mysqli_fetch_assoc($execSoal)) {
                $idSoal = $s['id_soal'];
                mysqli_query($link,"INSERT INTO tb_result VALUES(NULL,'$idUser','$id','$idSoal',NULL,'0')");
                $soal[] = array(
                  'idKuis' => $s['id_kuis'],
                  'idSoal' => $s['id_soal'],
                  'judulSoal' => $i++ . ". " . $s['judul_soal'],
                  'A' => $s['a'],
                  'B' => $s['b'],
                  'C' => $s['c'],
                  'D' => $s['d'],
                  'E' => $s['e'],
                );
              }
              $data['home'] = array(
                'kode' => '1',
                'kuis' => array('id_kuis' => $id),
                'durasi' => $durasi * 60 * 1000,
                'soal' => $soal
              );
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
          }
          // submit jawaban
          elseif ($route == "home" && $uuid == "submitJawaban" && isset($_GET['email'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $execProfile = mysqli_query($link,$sqlProfile);
            $p = mysqli_fetch_assoc($execProfile);
            $idUser = $p['id_user'];
            $idKuis = $_POST['idKuis'];
            $idSoal = $_POST['idSoal'];
            $pilihan = strtolower($_POST['pilihan']);
            $ket = "0";
            $checkJawaban = mysqli_query($link,"SELECT * FROM tb_soal WHERE id_soal='$idSoal' LIMIT 1");
            $cj = mysqli_fetch_assoc($checkJawaban);
            if ($pilihan == $cj['kunci']) {
              $ket = "1";
            } else {
              $ket = "0";
            }
            $checkResult = mysqli_query($link,"SELECT * FROM tb_result WHERE id_user='$idUser' AND id_kuis='$idKuis' AND id_soal='$idSoal'");
            if (mysqli_num_rows($checkResult) == 1) {
              $updateJawaban = mysqli_query($link,"UPDATE tb_result SET pilihan='$pilihan', ket='$ket' WHERE id_soal='$idSoal'");
              if ($updateJawaban) {
                $data['home'] = array(
                  'kode' => '1'
                );
              }
              else {
                $data['home'] = array(
                  'kode' => '0',
                  'message' => mysqli_error($link)
                );
              }
            }
          }
          // selesai kuis
          elseif ($route == "home" && $uuid == "selesaiKuis" && isset($_GET['email']) && isset($_GET['idKuis'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $execProfile = mysqli_query($link,$sqlProfile);
            $p = mysqli_fetch_assoc($execProfile);
            $idUser = $p['id_user'];
            $idKuis = $_GET['idKuis'];
            $checkResultNotNull = mysqli_query($link,"SELECT * FROM tb_result WHERE id_user='$idUser' AND id_kuis='$idKuis'");
            if (mysqli_num_rows($checkResultNotNull) > 0) {
              $sqlResultBenar = "SELECT COUNT(ket) as BENAR FROM tb_result WHERE id_user='$idUser' AND id_kuis='$idKuis' AND ket='1'";
              $benar = mysqli_query($link,$sqlResultBenar);
              $resultB = mysqli_fetch_assoc($benar);
              $b = $resultB['BENAR'];
              $sqlResultSalah = "SELECT COUNT(ket) as SALAH FROM tb_result WHERE id_user='$idUser' AND id_kuis='$idKuis' AND ket='0'";
              $salah = mysqli_query($link,$sqlResultSalah);
              $resultS = mysqli_fetch_assoc($salah);
              $s = $resultS['SALAH'];
              if ($benar && $salah) {
                $nilai = @($b * 100) / ($b + $s);
                $n = doubleval($nilai);
                $date = date('Y-m-d H:i:s', strtotime('now'));
                $sqlNilai = mysqli_query($link,"INSERT INTO tb_nilai VALUES(NULL,'$idUser','$idKuis','$b','$s','$n','$date')");
                if ($sqlNilai) {
                  mysqli_query($link,"DELETE FROM tb_result WHERE id_user='$idUser' AND id_kuis='$idKuis'");
                  $data['home'] = array(
                    'kode' => '1',
                    'kuis' => array('id_kuis' => $idKuis),
                    'nilai' => doubleval($nilai)
                  );
                }
                else {
                  $data['home'] = array(
                    'kode' => '0',
                    'message' => mysqli_error($link)
                  );
                }
              }
            }
          }
          // beri bintang
          elseif ($route == "home" && $uuid == "beriBintang" && isset($_GET['email']) && isset($_GET['idKuis'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $execProfile = mysqli_query($link,$sqlProfile);
            $p = mysqli_fetch_assoc($execProfile);
            $idUser = $p['id_user'];
            $idKuis = $_GET['idKuis'];
            $rating = $_POST['rating'];
            $exec = mysqli_query($link,"INSERT INTO tb_rating VALUES(NULL,'$idKuis','$idUser','$rating')");
            if ($exec) {
              $data['home'] = array(
                'kode' => '1',
                'message' => 'Terima kasih atas rating yang anda berikan !'
              );
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
          }
          // Top Up Saldo Voucher
          elseif ($route == "home" && $uuid == "topUp" && isset($_GET['email'])) {
            $email = strip_tags($_GET['email']);
            $kode = strip_tags($_POST['kode']);
            if (strlen($kode) < 16 || strlen($kode) > 16) {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Kode Voucher Harus 16 Angka !'
              );
            }
            elseif (is_numeric($kode)) {
              $sqlKode = mysqli_query($link,"SELECT * FROM tb_voucher WHERE kode_voucher='$kode' LIMIT 1");
              if (mysqli_num_rows($sqlKode) == 1) {
                $checkKode = mysqli_fetch_assoc($sqlKode);
                $a = $checkKode['status'] == 0 ? 'Aktif' : 'Tidak Aktif';
                if ($a == "Aktif") {
                  $jmlh = $checkKode['jumlah'];
                  $idKode = $checkKode['id_voucher'];
                  $date = date('Y-m-d H:i:s',strtotime('now'));
                  $user = mysqli_fetch_assoc(mysqli_query($link,"SELECT tb_users.id_user,tb_users.email,tb_users.name, tb_token.id_user, tb_token.firebase_token FROM tb_users
                    INNER JOIN tb_token ON tb_token.id_user = tb_users.id_user
                    WHERE tb_users.email='$email'"));
                  $id = $user['id_user'];

                  if ($app == "DEVELOPMENT") {
                    mysqli_query($link,"UPDATE tb_profile SET saldo=saldo+'$jmlh' WHERE id_user='$id'");
                    mysqli_query($link,"UPDATE tb_voucher SET status='1' WHERE id_voucher='$idKode'");
                    mysqli_query($link,"INSERT INTO tb_voucher_user VALUES(NULL,'$idKode',NULL,'$id','voucher','$date')");
                    $data['home'] = array(
                      'kode' => '1',
                      'title' => 'Top Up Saldo',
                      'message' => 'Top Up Saldo Berhasil Sejumlah ' . $checkKode['jumlah']
                    );
                  }
                  else {
                    if (substr($kode,0,15) == "000000000000000") {
                      $data['home'] = array(
                        'kode' => '0',
                        'message' => 'Kode Voucher Salah !'
                      );
                    }
                    else {
                      mysqli_query($link,"UPDATE tb_profile SET saldo=saldo+'$jmlh' WHERE id_user='$id'");
                      mysqli_query($link,"UPDATE tb_voucher SET status='1' WHERE id_voucher='$idKode'");
                      mysqli_query($link,"INSERT INTO tb_voucher_user VALUES(NULL,'$idKode',NULL,'$id','voucher','$date')");
                      $message['data'] = array(
                        'tipe' => 'topUpSaldo',
                        'subtitle' => 'Top up saldo',
                        'title' => 'Hi, ' . $user['name'],
                        'message' => 'Terima kasih telah melakukan top up saldo voucher sebesar Rp ' . number_format($jmlh)
                      );
                      $devicetoken = $user['firebase_token'];
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
                      curl_close($ch);
                      $data['home'] = array(
                        'kode' => '1',
                        'title' => 'Top Up Saldo',
                        'message' => 'Top Up Saldo Berhasil Sejumlah ' . $checkKode['jumlah']
                      );
                    }
                  }
                }
                else {
                  $data['home'] = array(
                    'kode' => '0',
                    'message' => 'Kode Voucher Sudah digunakan !'
                  );
                }
              }
              else {
                $data['home'] = array(
                  'kode' => '0',
                  'message' => 'Kode Voucher Salah !'
                );
              }
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Kode Voucher Harus Angka !'
              );
            }
          }
          // Top Up Saldo INAPP
          elseif ($route == "home" && $uuid == "inapp" && isset($_GET['email'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email'";
            $exc = mysqli_fetch_assoc(mysqli_query($link,$sqlProfile));
            $id = $exc['id_user'];

            $orderId = $_POST['orderId'];
            $package = $_POST['packageName'];
            $purchaseTime = $_POST['purchaseTime'];
            $purchaseToken = $_POST['purchaseToken'];
            $productId = $_POST['productId'];
            $date = date('Y-m-d H:i:s',strtotime('now'));
            $sqlBiling = mysqli_query($link,"INSERT INTO tb_billing VALUES(NULL,'$orderId','$package','$purchaseTime','$purchaseToken','$productId')");
            if ($sqlBiling) {
              $idOrder = mysqli_fetch_assoc(mysqli_query($link,"SELECT id_billing, orderId FROM tb_billing WHERE orderId='$orderId'"));
              $idKode = $idOrder['id_billing'];
              mysqli_query($link,"UPDATE tb_profile SET saldo=saldo+'$productId' WHERE id_user='$id'");
              mysqli_query($link,"INSERT INTO tb_voucher_user VALUES(NULL,NULL,'$idKode','$id','inapp','$date')");
              $c = mysqli_fetch_assoc(mysqli_query($link,"SELECT saldo FROM tb_profile WHERE id_user='$id'"));
              $t = mysqli_fetch_assoc(mysqli_query($link,"SELECT firebase_token FROM tb_token WHERE id_user='$id'"));
              $message['data'] = array(
                'tipe' => 'topUpSaldo',
                'subtitle' => 'Top up saldo',
                'title' => 'Hi, ' . $exc['nama'],
                'message' => 'Terima kasih telah melakukan top up saldo via G-pay sebesar Rp ' . number_format($productId)
              );
              $devicetoken = $t['firebase_token'];
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
              curl_close($ch);
              $data['home'] = array(
                'kode' => '1',
                'message' => 'Berhasil melakukan top up saldo inapp',
                'saldo' => number_format($c['saldo'])
              );
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => mysqli_query($link)
              );
            }
          }
          // History Top Up
          elseif ($route == "home" && $uuid == "historyTopUp" && isset($_GET['email'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email'";
            $exc = mysqli_fetch_assoc(mysqli_query($link,$sqlProfile));
            $id = $exc['id_user'];
            $query = "SELECT tb_voucher_user.*, tb_voucher.*, tb_billing.* FROM tb_voucher_user
                      LEFT JOIN tb_voucher ON tb_voucher.id_voucher = tb_voucher_user.id_voucher
                      LEFT JOIN tb_billing ON tb_billing.id_biling = tb_voucher_user.id_billing
                      WHERE tb_voucher_user.id_user='$id' ORDER BY tb_voucher_user.created_at DESC";
            $sqlHistory = mysqli_query($link,$query);
            if (mysqli_num_rows($sqlHistory) > 0) {
              $i = 1;
              while ($r = mysqli_fetch_assoc($sqlHistory)) {
                $data['home']['kode'] = '1';
                $data['home']['saldo'] = number_format($exc['saldo']);
                $data['home']['historyTopUp'][] = array(
                  'nomor' => $i++.".",
                  'jumlah' => $r['tipe'] == "voucher" ? "Top Up Saldo voucher sebesar Rp. " . number_format($r['jumlah']) : "Top Up Saldo via G-pay sebesar Rp. " .number_format($r['productId']),
                  'tanggal' => date('d-m-Y', strtotime($r['created_at']))
                );
              }
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Belum Pernah Top Up Saldo'
              );
            }
          }
          // Submit Feedback
          elseif ($route == "home" && $uuid == "feedback" && isset($_GET['email'])) {
            $info = $_POST['info'];
            $rate = $_POST['rating'];
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $exec = mysqli_fetch_assoc(mysqli_query($link,$sqlProfile));
            $id = $exec['id_user'];
            $date = date('Y-m-d H:i:s',strtotime('now'));
            $sqlFeedback = mysqli_query($link,"INSERT INTO tb_feedback VALUES(NULL,'$id','$info','$rate','$date')");
            if ($sqlFeedback) {
              $data['home'] = array(
                'kode' => '0',
                'title' => 'Success',
                'message' => 'Terima Kasih telah mengirimkan feedback anda '
              );
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'title' => 'Error',
                'message' => mysqli_error($link)
              );
            }
          }
          // Detail Profile
          elseif ($route == "home" && $uuid == "profileUser" && isset($_GET['email'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $exec = mysqli_query($link,$sqlProfile);
            if ($exec) {
              $r = mysqli_fetch_assoc($exec);
              $data['home']['kode'] = '1';
              $data['home']['user'] = array(
                'nama' => $r['nama'],
                'username' => $r['username'],
                'email' => $r['email'],
                'level' => ucwords($r['nama_level']),
                'lahir' => $r['tgl_lahir'] != NULL ? date('d-m-Y', strtotime($r['tgl_lahir'])) : '-',
                'kelamin' => $r['jk'],
                'no' => $r['no_hp'] ?? $r['no_hp'],
                'almt' => $r['almt'] ?? $r['almt'],
                'saldo' => "Rp. " . number_format($r['saldo']),
                'bank' => $r['nama_bank'] ?? $r['nama_bank'],
                'rek' => $r['no_rek'] ?? $r['no_rek'],
                'atasNama' => $r['atas_nama'] ?? $r['atas_nama']
              );
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
          }
          // Update Profile
          elseif ($route == "home" && $uuid == "ubahProfile" && isset($_GET['email'])) {
            $email = $_GET['email'];

            $mail = $_POST['mail'];
            $nama = $_POST['nama'];
            $tgl = date('Y-m-d', strtotime($_POST['tgl']));
            $klm = $_POST['jk'];
            $no = $_POST['no'];
            $almt = $_POST['almt'];
            $nmBank = $_POST['nama_bank'];
            $noRek = $_POST['no_rek'];
            $anBank = $_POST['atas_nama'];
            $date = date('Y-m-d H:i:s', strtotime('now'));

            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $exec = mysqli_query($link,$sqlProfile);
            $r = mysqli_fetch_assoc($exec);
            $id = $r['id_user'];

            if ($email !== $mail) {
              $checkEmail = mysqli_query($link,"SELECT email FROM tb_users WHERE email='$mail' LIMIT 1");
              if (mysqli_num_rows($checkEmail) == 1) {
                $data['home'] = array(
                  'kode' => '0',
                  'message' => 'Email telah digunakan. Harap menggunakan Email lain'
                );
              }
              else {
                $token = hash('sha512',time());
                $tipeUrl = base64_encode('re-verification-email');
                $exp = date('Y-m-d H:i:s',strtotime('+1 hour'));
                $body = '
                Hi, '.$nama.' !<br>
                You have requested your MAP email to be changed. Please click the following link to verification email :<br>

                Please click bottom button this link to changed email your account:<br>
                <a href="'.$url.'/verification?t='.$tipeUrl.'&e='.base64_encode($mail).'&_token='.$token.'">Click here !</a><br>or copy link below<br>
                '.$url.'/verification?t='.$tipeUrl.'&e='.base64_encode($mail).'&_token='.$token.'<br>
                This link will expire in 1 hour.<br>

                thanks, '.$appName.'</br>
                ';
                $mail1 = new PHPMailer(true);

                try {
                  $mail1->isSMTP();
                  $mail1->Host = MAIL_HOST;
                  $mail1->SMTPAuth = true;
                  $mail1->Username = MAIL_USERNAME;
                  $mail1->Password = MAIL_PASSWORD;
                  $mail1->SMTPSecure = MAIL_ENCRYPTION;
                  $mail1->Port = MAIL_PORT;

                  $mail1->setFrom(MAIL_USERNAME, APPNAME);
                  $mail1->addAddress($mail, $nama);

                  $mail1->isHTML(true);
                  $mail1->Subject = "Change Email Account";
                  $mail1->Body    = $body;
                  //$mail->send();
                  if ($mail1->send()) {
                    $sql = mysqli_query($link,"UPDATE tb_token SET access_token=NULL, verify_token='$token', expried_in='$exp', updated_at='$date' WHERE id_user='$id'");
                    mysqli_query($link,"UPDATE tb_users SET email='$mail', status='1', updated_at='$date' WHERE email='$email'");
                    mysqli_query($link,"UPDATE tb_profile SET nama='$nama', tgl_lahir='$tgl', jk='$klm', no_hp='$no', almt='$almt', nama_bank='$nmBank', no_rek='$noRek', atas_nama='$anBank', updated_at='$date' WHERE id_user='$id'");
                    if ($sql) {
                      $data['home'] = array(
                        'kode' => '1',
                        'setLogin' => true,
                        'message' => 'Lakukan Verifikasi email akun melalui email yang dikirim ! Jika tidak ada masuk, silahkan cek spam !'
                      );
                    }
                    else {
                      $data['home'] = array(
                        'kode' => '0',
                        'message' => mysqli_error($link),
                      );
                    }
                  }
                  else {
                    $data['home'] = array(
                      'kode' => '0',
                      'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
                    );
                  }
                }
                catch(Exception $e) {
                  $data['home'] = array(
                    'kode' => '0',
                    'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
                  );
                }
              }
            }
            else {
              $update = mysqli_query($link,"UPDATE tb_profile SET nama='$nama', tgl_lahir='$tgl', jk='$klm', no_hp='$no', almt='$almt', nama_bank='$nmBank', no_rek='$noRek', atas_nama='$anBank', updated_at='$date' WHERE id_user='$id'");
              if ($update) {
                $data['home'] = array(
                  'kode' => '1',
                  'message' => 'Data berhasil diperbarui !'
                );
              }
              else {
                $data['home'] = array(
                  'kode' => '0',
                  'message' => mysqli_query($link)
                );
              }
            }

          }
          // Update password
          elseif ($route == "home" && $uuid == "ubahPassword" && isset($_GET['email'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $execProfile = mysqli_query($link,$sqlProfile);
            $p = mysqli_fetch_assoc($execProfile);
            $idUser = $p['id_user'];

            $passold = $_POST['passold'];
            $passnew = $_POST['passnew'];
            if ($passold == NULL || $passnew == NULL) {
              $data['home'] = array(
                'kode' => '1',
                'message' => 'Kata sandi tidak boleh kosong !'
              );
            }
            else {
              if (hash('sha512', $passold) == $p['password']) {
                $pass = hash('sha512', $passnew);
                $updatePass = mysqli_query($link,"UPDATE tb_users SET password='$pass' WHERE id_user='$idUser'");
                if ($updatePass) {
                  $data['home'] = array(
                    'kode' => '1',
                    'message' => 'Kata sandi berhasil diperbarui !'
                  );
                }
                else {
                  $data['home'] = array(
                    'kode' => '0',
                    'message' => mysqli_query($link)
                  );
                }
              }
              else {
                $data['home'] = array(
                  'kode' => '0',
                  'message' => 'Kata sandi lama tidak sesuai dengan database'
                );
              }
            }
          }
          // history ikut kuis
          elseif ($route == "home" && $uuid == "historyKuis" && isset($_GET['email'])) {
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $exec = mysqli_fetch_assoc(mysqli_query($link,$sqlProfile));
            $id = $exec['id_user'];
            $sqlHistoryKuis = "SELECT tb_kuis.id_kuis, tb_kuis.judul, tb_kuis.harga, tb_kuis.jumlah_soal, tb_kuis.durasi,
                               tb_nilai.*, tb_users.id_user, tb_profile.id_user, tb_profile.nama, tb_rating.* FROM tb_nilai
                               INNER JOIN tb_kuis ON tb_nilai.id_kuis = tb_kuis.id_kuis
                               LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
                               INNER JOIN tb_users ON tb_nilai.id_user = tb_users.id_user
                               INNER JOIN tb_profile ON tb_profile.id_user = tb_users.id_user
                               WHERE tb_nilai.id_user='$id' ORDER BY tb_nilai.created_at DESC";
            $exec = mysqli_query($link,$sqlHistoryKuis);
            if (mysqli_num_rows($exec) > 0) {
              $i = 1;
              while ($r = mysqli_fetch_assoc($exec)) {
                $data['home']['kode'] = '1';
                $data['home']['historyKuis'][] = array(
                  'idNilai' => $r['id_nilai'],
                  'nomor' => $i++.".",
                  'namaKuis' => $r['judul'],
                  'nilai' => $r['nilai'],
                  'harga' => $r['harga'] != 0 ? 'Rp. ' . number_format($r['harga']) : 'Gratis',
                  'soal' => $r['jumlah_soal'],
                  'durasi' => $r['durasi'],
                  'benar' => $r['jumlah_benar'],
                  'salah' => $r['jumlah_salah'],
                  'tanggal' => date('d-m-Y, H:i:s',strtotime($r['created_at'])),
                  'rating' => $r['rating'] != NULL ? $r['rating'] : 0
                );
              }
            }
            else {
              $data['home'] = array(
                'kode' => '0',
                'message' => 'Belum pernah ikut kuis'
              );
            }
          }
        }
        else {
          $data['home'] = array(
            'kode' => '0',
            'message' => 'Invalid URL',
          );
        }
      }
      else {
        $data['home'] = array(
          'kode' => '2',
          'message' => 'Expried session. Login Again !',
          'setLogin' => true
        );
      }
    }
    else {
      $data['home'] = array(
        'kode' => '2',
        'message' => 'Expried session. Login Again !',
        'setLogin' => true
      );
    }
  }
  else {
    $data['home'] = array(
      'kode' => '2',
      'message' => 'Invalid Token !',
      'setLogin' => true
    );
  }

  echo json_encode($data);
