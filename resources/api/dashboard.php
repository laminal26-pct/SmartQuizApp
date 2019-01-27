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
             file_put_contents($file,$dataCover);
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
            $jawaban = strtolower($_POST['jawaban']);
            $bahas = $_POST['bahas'] != NULL ? $_POST['bahas'] : '';
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
            $email = $_GET['email'];
            $sqlProfile .= " WHERE tb_users.email='$email' LIMIT 1";
            $r = mysqli_fetch_assoc(mysqli_query($link,$sqlProfile));
            $id = $r['id_user'];
            $sqlKuisAuthor = "SELECT tb_kuis.id_kuis as idKuis, tb_kuis.*, tb_rating.*, tb_users.id_user, tb_users.username, tb_kategori.id_kategori, tb_kategori.nama_kategori, tb_mapel.*, tb_soal.* FROM tb_kuis
                              LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
                              INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user
                              INNER JOIN tb_kategori ON tb_kategori.id_kategori = tb_kuis.id_kategori
                              INNER JOIN tb_mapel ON tb_mapel.id_mapel = tb_kuis.id_mapel
                              LEFT JOIN (SELECT id_kuis, COUNT(*) as nomorSoal FROM tb_soal GROUP BY id_kuis) tb_soal ON tb_soal.id_kuis = tb_kuis.id_kuis
                              WHERE tb_users.id_user='$id' ORDER BY tb_kuis.created_at DESC";
            $exec = mysqli_query($link,$sqlKuisAuthor);
            if (mysqli_num_rows($exec) > 0) {
              $i = 1;
              while ($r = mysqli_fetch_assoc($exec)) {
                $data['dashboard']['kode'] = '1';
                $data['dashboard']['kuisList'][] = array(
                  'nomor' => $i++.".",
                  'id_kuis' => $r['idKuis'],
                  'judul' => $r['judul'],
                  'slug' => $r['slug'],
                  'author' => $r['username'],
                  'judul' => $r['judul'],
                  'slug' => $r['slug'],
                  'soal' => $r['jumlah_soal'],
                  'durasi' => $r['durasi'],
                  'harga' => $r['harga'] == 0 ? 'Gratis' : 'Rp ' . number_format($r['harga'],'0',',','.'),
                  'cover' => $url . '/assets/img/kuis/' . $r['cover'],
                  'deskripsi' => $r['deskripsi'],
                  'nm_kategori' => $r['nama_kategori'],
                  'nm_mapel' => $r['nama_mapel'],
                  'status' => $r['status'] == 1 ? 'Aktif' : 'Tidak Aktif',
                  'rating' => $r['rate'] != NULL ? $r['rate'] : 0,
                  'nomorSoal' => $r['nomorSoal'] == 0 ? 1 : $r['nomorSoal']+1
                );
              }
            }
            else {
              $data['dashboard'] = array(
                'kode' => '0',
                'message' => 'Belum Ada Kuis'
              );
            }
          }
          // edit kuis
          elseif ($route == "dashboard" && $uuid == "editKuis" && isset($_GET['idKuis'])) {
            $id = $_GET['idKuis'];
            $sqlKuisAuthor = "SELECT tb_kuis.id_kuis as idKuis, tb_kuis.*, tb_rating.*, tb_users.id_user, tb_users.username, tb_kategori.id_kategori, tb_kategori.nama_kategori, tb_mapel.*, tb_soal.* FROM tb_kuis
                              LEFT JOIN (SELECT *, AVG(tb_rating.rating) as rate FROM tb_rating GROUP BY id_kuis) tb_rating ON tb_rating.id_kuis = tb_kuis.id_kuis
                              INNER JOIN tb_users ON tb_users.id_user = tb_kuis.id_user
                              INNER JOIN tb_kategori ON tb_kategori.id_kategori = tb_kuis.id_kategori
                              INNER JOIN tb_mapel ON tb_mapel.id_mapel = tb_kuis.id_mapel
                              LEFT JOIN (SELECT id_kuis, COUNT(*) as nomorSoal FROM tb_soal GROUP BY id_kuis) tb_soal ON tb_soal.id_kuis = tb_kuis.id_kuis
                              WHERE tb_kuis.id_kuis='$id'";
            $exec = mysqli_query($link,$sqlKuisAuthor);
            if (mysqli_num_rows($exec) == 1) {
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
              $r = mysqli_fetch_assoc($exec);
              $data['dashboard'] = array(
                'kode' => '1',
                'kategori' => $kategori,
                'mapel' => $mapel,
                'kuis' => array(
                  'id_kuis' => $r['idKuis'],
                  'judul' => $r['judul'],
                  'slug' => $r['slug'],
                  'author' => $r['username'],
                  'judul' => $r['judul'],
                  'slug' => $r['slug'],
                  'soal' => $r['jumlah_soal'],
                  'durasi' => $r['durasi'],
                  'harga' => $r['harga'],
                  'cover' => $url . '/assets/img/kuis/' . $r['cover'],
                  'deskripsi' => $r['deskripsi'],
                  'id_kategori' => $r['id_kategori'],
                  'nm_kategori' => $r['nama_kategori'],
                  'id_mapel' => $r['id_mapel'],
                  'nm_mapel' => $r['nama_mapel'],
                  'status' => $r['status'],
                  'acak' => $r['soal_acak'],
                  'bahas' => $r['tmpl_bahas'],
                  'rating' => $r['rate'] != NULL ? $r['rate'] : 0,
                  'nomorSoal' => $r['nomorSoal'] == 0 ? 1 : $r['nomorSoal']+1
                )
              );
            }
            else {
              $data['dashboard'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
          }
          // ubah Kuis
          elseif ($route == "dashboard" && $uuid == "ubahKuis" && isset($_GET['idKuis'])) {
            $id = $_GET['idKuis'];
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
             file_put_contents($file,$dataCover);
            }
            $query = "UPDATE tb_kuis SET judul='$judul', jumlah_soal='$soal',durasi='$durasi', harga='$harga',
                      soal_acak='$acakSoal', tmpl_bahas='$tmplBahas', deskripsi='$deskripsi', cover='$fileName'
                      WHERE id_kuis='$id'";
            $exec = mysqli_query($link,$query);
            if ($exec) {
              $data['dashboard'] = array(
                'kode' => '1',
                'message' => 'Kuis berhasil diubah !'
              );
            }
            else {
              $data['dashboard'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
          }
          // hapus kuis
          elseif ($route == "dashboard" && $uuid == "hapusKuis" && isset($_GET['idKuis'])) {
            $id = $_GET['idKuis'];
            $sqlSoal = mysqli_query($link,"DELETE FROM tb_soal WHERE id_kuis='$id'");
            $sqlKuis = mysqli_query($link,"DELETE FROM tb_kuis WHERE id_kuis='$id'");
            if ($sqlSoal && $sqlKuis) {
              $data['dashboard'] = array(
                'kode' => '1',
                'message' => 'Kuis berhasil dihapus !'
              );
            }
            else {
              $data['dashboard'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
          }
          // list soal
          elseif ($route == "dashboard" && $uuid == "listSoal" && isset($_GET['slug'])) {
            $slug = $_GET['slug'];
            $sqlKuis = "SELECT tb_kuis.slug, tb_kuis.id_kuis, tb_kuis.judul, tb_soal.* FROM tb_soal INNER JOIN tb_kuis ON tb_kuis.id_kuis = tb_soal.id_kuis WHERE tb_kuis.slug='$slug'";
            $exec = mysqli_query($link,$sqlKuis);
            if (mysqli_num_rows($exec) > 0) {
              $i = 1;
              while ($r = mysqli_fetch_assoc($exec)) {
                $data['dashboard']['kode'] = '1';
                $data['dashboard']['jumlahSoal'] = mysqli_num_rows($exec);
                $data['dashboard']['title'] = $r['judul'];
                $data['dashboard']['soalList'][] = array(
                  'nomorSoal' => $i++.".",
                  'idSoal' => $r['id_soal'],
                  'judulSoal' => $r['judul_soal'],
                  'A' => $r['a'],
                  'B' => $r['b'],
                  'C' => $r['c'],
                  'D' => $r['d'],
                  'E' => $r['e'],
                  'kunciJawaban' => strtoupper($r['kunci'])
                );
              }
            }
            else {
              $r = mysqli_fetch_assoc(mysqli_query($link,"SELECT slug, judul FROM tb_kuis WHERE slug='$slug'"));
              $data['dashboard'] = array(
                'kode' => '0',
                'jumlahSoal' => mysqli_num_rows($exec),
                'title' => $r['judul'],
                'message' => 'Data Soal Kosong'
              );
            }
          }
          // edit soal
          elseif ($route == "dashboard" && $uuid == "editSoal" && isset($_GET['idSoal'])) {
            $id = $_GET['idSoal'];
            $sqlSoal = mysqli_query($link,"SELECT * FROM tb_soal WHERE id_soal='$id'");
            if ($sqlSoal) {
              $r = mysqli_fetch_assoc($sqlSoal);
              $data['dashboard'] = array(
                'kode' => '1',
                'soal' => array(
                  'idSoal' => $r['id_soal'],
                  'judulSoal' => $r['judul_soal'],
                  'A' => $r['a'],
                  'B' => $r['b'],
                  'C' => $r['c'],
                  'D' => $r['d'],
                  'E' => $r['e'],
                  'kunciJawaban' => strtolower($r['kunci'])
                )
              );
            }
            else {
              $data['dashboard'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
          }
          // ubah soal
          elseif ($route == "dashboard" && $uuid == "ubahSoal" && isset($_GET['idSoal'])) {
            $id = $_GET['idSoal'];
            $soal = $_POST['soal'];
            $pilihanA = $_POST['pilihanA'];
            $pilihanB = $_POST['pilihanB'];
            $pilihanC = $_POST['pilihanC'];
            $pilihanD = $_POST['pilihanD'];
            $pilihanE = $_POST['pilihanE'];
            $jawaban = strtolower($_POST['jawaban']);
            $bahas = $_POST['bahas'] != NULL ? $_POST['bahas'] : '';
            $sqlUpdateSoal = "UPDATE tb_soal SET judul_soal='$soal', a='$pilihanA',b='$pilihanB',c='$pilihanC',d='$pilihanD',e='$pilihanE', kunci='$jawaban', pembahasan='$bahas' WHERE id_soal='$id'";
            $exec = mysqli_query($link,$sqlUpdateSoal);
            if ($exec) {
              $data['dashboard'] = array(
                'kode' => '1',
                'message' => 'Soal berhasil diubah !'
              );
            }
            else {
              $data['dashboard'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
          }
          // hapus soal
          elseif ($route == "dashboard" && $uuid == "hapusSoal" && isset($_GET['idSoal'])) {
            $id = $_GET['idSoal'];
            $sqlSoal = mysqli_query($link,"DELETE FROM tb_soal WHERE id_soal='$id'");
            if ($sqlSoal) {
              $data['dashboard'] = array(
                'kode' => '1',
                'message' => 'Soal berhasil dihapus !'
              );
            }
            else {
              $data['dashboard'] = array(
                'kode' => '0',
                'message' => mysqli_error($link)
              );
            }
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
