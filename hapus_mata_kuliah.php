<?php
require 'koneksi.php';
$id = isset($_GET['id']) && ctype_digit((string)$_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

$stmt = $conn->prepare('SELECT * FROM mata_kuliah WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die('Data mata kuliah tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!cek_token($_POST['token'] ?? '')) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $stmt1 = $conn->prepare('SELECT COUNT(*) AS jml FROM materi WHERE mata_kuliah_id = ?');
        $stmt1->bind_param('i', $id);
        $stmt1->execute();
        $jml_materi = (int)$stmt1->get_result()->fetch_assoc()['jml'];

        $stmt2 = $conn->prepare('SELECT COUNT(*) AS jml FROM tugas WHERE mata_kuliah_id = ?');
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $jml_tugas = (int)$stmt2->get_result()->fetch_assoc()['jml'];

        if ($jml_materi > 0 || $jml_tugas > 0) {
            $error = 'Data belum tersimpan. Mata kuliah masih memiliki materi atau tugas. Hapus data terkait terlebih dahulu.';
        } else {
            $stmt = $conn->prepare('DELETE FROM mata_kuliah WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $_SESSION['pesan'] = 'Mata kuliah berhasil dihapus.';
            header('Location: daftar_mata_kuliah.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Mata Kuliah - LMS EAD</title>
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

    <h2>Mata kuliah</h2>
    <?php if ($error !== ''): ?>
        <div class="kesalahan"><?= h($error) ?></div>
    <?php endif; ?>

    <p>Hapus <strong><?= h($data['nama']) ?> (<?= h($data['kode']) ?>)</strong>?</p>
    <p>Data baru dihapus setelah tombol konfirmasi dikirim.</p>

    <form method="post">
        <input type="hidden" name="token" value="<?= h($_SESSION['token']) ?>">
        <button type="submit" class="bahaya">Ya, hapus data</button>
        <a class="tombol tombol-sekunder" href="daftar_mata_kuliah.php">Batal</a>
    </form>
</main>
</body>
</html>