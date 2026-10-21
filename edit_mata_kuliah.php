<?php
require 'koneksi.php';
$id = isset($_GET['id']) && ctype_digit((string)$_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

$stmt = $conn->prepare('SELECT * FROM mata_kuliah WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();

if (!$lama) {
    die('Data mata kuliah tidak ditemukan.');
}

$kode     = $lama['kode'];
$nama     = $lama['nama'];
$dosen    = $lama['dosen'];
$semester = $lama['semester'];

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
            $sem_int = (int)$semester;
            $stmt = $conn->prepare('SELECT id FROM mata_kuliah WHERE kode = ? AND id <> ?');
            $stmt->bind_param('si', $kode, $id);
            $stmt->execute();

            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Kode mata kuliah sudah digunakan.';
            } else {
                if ($lama['kode'] === $kode && $lama['nama'] === $nama && $lama['dosen'] === $dosen && (int)$lama['semester'] === $sem_int) {
                    $_SESSION['pesan'] = 'Tidak ada perubahan data yang disimpan.';
                } else {
                    $stmt = $conn->prepare('UPDATE mata_kuliah SET kode = ?, nama = ?, dosen = ?, semester = ? WHERE id = ?');
                    $stmt->bind_param('sssii', $kode, $nama, $dosen, $sem_int, $id);
                    $stmt->execute();
                    $_SESSION['pesan'] = 'Data mata kuliah berhasil diperbarui.';
                }
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
    <title>Edit Mata Kuliah - LMS EAD</title>
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

    <h2>Edit Mata Kuliah</h2>
    <?php if ($error !== ''): ?>
        <div class="kesalahan"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="token" value="<?= h($_SESSION['token']) ?>">

        <label for="kode">Kode mata kuliah</label>
        <input id="kode" name="kode" maxlength="20" value="<?= h($kode) ?>">

        <label for="nama">Nama mata kuliah</label>
        <input id="nama" name="nama" maxlength="100" value="<?= h($nama) ?>">

        <label for="dosen">Dosen pengampu</label>
        <input id="dosen" name="dosen" maxlength="100" value="<?= h($dosen) ?>">

        <label for="semester">Semester</label>
        <input id="semester" type="number" name="semester" min="1" max="14" value="<?= h($semester) ?>">

        <button type="submit">Simpan perubahan</button>
        <a class="tombol tombol-sekunder" href="daftar_mata_kuliah.php">Batal</a>
    </form>
</main>
</body>
</html>