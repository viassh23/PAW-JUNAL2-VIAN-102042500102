<?php
require 'koneksi.php';
$error = '';
$kode = $nama = $dosen = $semester = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!cek_token($_POST['token'] ?? '')) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $kode     = strtoupper(trim($_POST['kode'] ?? ''));
        $nama     = trim($_POST['nama'] ?? '');
        $dosen    = trim($_POST['dosen'] ?? '');
        $semester = trim($_POST['semester'] ?? '');

        if (!preg_match('/^[A-Z0-9\-]{3,20}$/', $kode)) {
            $error = 'Kode mata kuliah wajib 3-20 karakter (hanya huruf kapital, angka, atau tanda -).';
        } elseif (mb_strlen($nama) < 3 || mb_strlen($nama) > 100) {
            $error = 'Nama mata kuliah wajib berisi 3 sampai 100 karakter.';
        } elseif (mb_strlen($dosen) < 3 || mb_strlen($dosen) > 100) {
            $error = 'Nama dosen pengampu wajib berisi 3 sampai 100 karakter.';
        } elseif (filter_var($semester, FILTER_VALIDATE_INT) === false || (int)$semester < 1 || (int)$semester > 14) {
            $error = 'Semester harus berupa bilangan bulat antara 1 sampai 14.';
        } else {
            $stmt = $conn->prepare('SELECT id FROM mata_kuliah WHERE kode = ?');
            $stmt->bind_param('s', $kode);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Kode mata kuliah sudah digunakan.';
            } else {
                $sem_int = (int)$semester;
                $stmt = $conn->prepare('INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('sssi', $kode, $nama, $dosen, $sem_int);
                $stmt->execute();
                $_SESSION['pesan'] = 'Mata kuliah baru berhasil ditambahkan.';
                header('Location: daftar_mata_kuliah.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mata Kuliah - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <p class="label-kecil">PRAKTIKUM PAW / JURNAL 2</p>
    <h1>Learning Management System</h1>
    <p class="subjudul">Pengelolaan mata kuliah, materi pembelajaran, dan tugas.</p>

    <nav class="nav-bar">
        <a href="index.php">Beranda</a>
        <a href="daftar_mata_kuliah.php" class="aktif">Mata kuliah</a>
        <a href="daftar_materi.php">Materi</a>
        <a href="daftar_tugas.php">Tugas</a>
    </nav>

    <h2>Tambah Mata Kuliah</h2>
    <?php if ($error !== ''): ?>
        <div class="kesalahan"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="token" value="<?= h($_SESSION['token']) ?>">

        <label for="kode">Kode mata kuliah (3-20 karakter: huruf kapital, angka, atau -)</label>
        <input id="kode" name="kode" maxlength="20" value="<?= h($kode) ?>">

        <label for="nama">Nama mata kuliah (3-100 karakter)</label>
        <input id="nama" name="nama" maxlength="100" value="<?= h($nama) ?>">

        <label for="dosen">Dosen pengampu (3-100 karakter)</label>
        <input id="dosen" name="dosen" maxlength="100" value="<?= h($dosen) ?>">

        <label for="semester">Semester (1-14)</label>
        <input id="semester" type="number" name="semester" min="1" max="14" value="<?= h($semester) ?>">

        <button type="submit">Simpan data</button>
        <a class="tombol tombol-sekunder" href="daftar_mata_kuliah.php">Batal</a>
    </form>
</main>
</body>
</html>