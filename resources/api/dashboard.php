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
  $sqlProfile = "SELECT tb_level.*, tb_users.id_user, tb_users.id_level, tb_users.username, tb_users.email,
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
          // buat kuis
          elseif ($route == "dashboard" && $uuid == "buatKuis") {
             $kategori = array();
             $mapel = array();
             $sqlKategori = mysqli_query($link,"SELECT * FROM tb_kategori");
             $sqlMapel = mysqli_query($link,"SELECT * FROM tb_mapel ORDER BY nama_mapel ASC");
             $kategori[] = array('id' => NULL, 'title' => "Pilih Kategori Kuis");
             $mapel[] = array('id' => NULL, 'mapel' => "Pilih Mata Pelajaran Kuis");
             while ($r = mysqli_fetch_assoc($sqlKategori)) {
               $kategori[] = array(
                 'id' => $r['id_kategori'],
                 'title' => $r['nama_kategori']
               );
             }
             while ($r = mysqli_fetch_assoc($sqlMapel)) {
               $mapel[] = array(
                 'id' => $r['id_mapel'],
                 'mapel' => $r['nama_mapel']
               );
             }
             $data['dashboard'] = array(
               'kode' => '1',
               'message' => 'successful',
               'kategori' => $kategori,
               'mapel' => $mapel
             );
          }
          // tambah kuis
          elseif ($route == "dashboard" && $uuid == "simpanKuis" && isset($_GET['email'])) {
            /* note
             * membuat folder cover kuis berdasarkan slug
             * mengupload gambar ke folder cover kuis berdasarkan slug
             * menyimpan data kuis
             * call back ke tambah soal berdasarkan idKuis
             */
            $email = $_GET['email'];
            $idKategori = $_POST['idKategori'];
            $tmplBahas = $_POST['tmplBahas'];
            $soal = $_POST['soal'];
            $harga = $_POST['harga'];
            $idMapel = $_POST['idMapel'];
            $deskripsi = $_POST['deskripsi'];
            $judul = $_POST['judul'];
            $durasi = $_POST['durasi'];
            $acakSoal = $_POST['acakSoal'];
            $slug = hash('sha512', time());
            $date = date('Y-m-d H:i:s',strtotime('now'));
            $cover = "";
            $file = "../../assets/img/kuis/";
            if ($_POST['cover'] == "default.png") {
             $cover = "default.png";
             $fileName = "default.png";
             $file .= $fileName;
            } else {
             $cover = htmlspecialchars($_POST['cover']);
             $cover = str_replace('data:image/png;base64,', '', $cover);
             $cover = str_replace(' ','+', $cover);
             $dataCover = base64_decode($cover);
             $fileName = uniqid() . '.png';
             $file .= $fileName;
             file_put_contents($file,$fileName);
            }
            $sqlProfile .= " WHERE tb_users.email='$email'";
            $r = mysqli_fetch_assoc(mysqli_query($link,$sqlProfile));
            $id = $r['id_user'];
            $query = "INSERT INTO tb_kuis VALUES(NULL,'$id','$idKategori','$idMapel','$judul','$slug','$soal','$durasi','$harga','$acakSoal','$tmplBahas','$deskripsi','$fileName','0','$date','$date')";
            $sqlTmbhKuis = mysqli_query($link,$query);
            if ($sqlTmbhKuis) {
              $sqlSoalKuis = mysqli_query($link,"SELECT tb_kuis.id_kuis, tb_kuis.slug, tb_soal.* FROM tb_soal INNER JOIN tb_kuis ON tb_kuis.id_kuis = tb_soal.id_kuis WHERE tb_kuis.slug='$slug'");
              $nomorSoal = 0;
              if (mysqli_num_rows($sqlSoalKuis) == 0) {
                $nomorSoal = 1;
              }
              $data['dashboard'] = array(
               'kode' => '1',
               'message' => 'Kuis Berhasil dibuat !',
               'slugKuis' => $slug,
               'judulKuis' => $judul,
               'nomorSoal' => $nomorSoal,
              );
            } else {
              $data['dashboard'] = array(
               'kode' => '0',
               'message' => mysqli_error($link)
              );
            }
          }
          // simpan soal
          elseif ($route == "dashboard" && $uuid == "simpanSoal" && isset($_GET['slug'])) {
            $slug = $_GET['slug'];
            $r = mysqli_fetch_assoc(mysqli_query($link,"SELECT id_kuis,slug,judul,jumlah_soal FROM tb_kuis WHERE slug='$slug' LIMIT 1"));
            $idKuis = $r['id_kuis'];
            $judul = $r['judul'];
            $jmlhSoal = $r['jumlah_soal'];
            $soal = $_POST['soal'];
            $pilihanA = $_POST['pilihanA'];
            $pilihanB = $_POST['pilihanB'];
            $pilihanC = $_POST['pilihanC'];
            $pilihanD = $_POST['pilihanD'];
            $pilihanE = $_POST['pilihanE'];
            $jawaban = $_POST['jawaban'];
            $bahas = $_POST['bahas'] != NULL ? $_POST['bahas'] : NULL;
            $sqlSoal = mysqli_query($link,"INSERT INTO tb_soal VALUES(NULL,'$idKuis','$soal','$pilihanA','$pilihanB','$pilihanC','$pilihanD','$pilihanE','$jawaban','$bahas')");
            if ($sqlSoal) {
              $nomorSoal = mysqli_num_rows(mysqli_query($link,"SELECT * FROM tb_soal WHERE id_kuis='$idKuis'"));
              $data['dashboard'] = array(
               'kode' => '1',
               'message' => $nomorSoal < $jmlhSoal ? 'Soal Berhasil ditambahkan !' : 'Input Soal Selesai, Tim kami akan melakukan verifikasi',
               'slugKuis' => $slug,
               'judulKuis' => $judul,
               'nomorSoal' => $nomorSoal == 0 ? 1 : $nomorSoal+1,
              );
            }
            else {
              $data['dashboard'] = array(
               'kode' => '0',
               'message' => mysqli_error($link)
              );
            }
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
